<?php

namespace App\Http\Controllers;

use App\Models\Lulusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Daftar nama Fakultas yang dikecualikan (Blacklist)
        $pengecualianFakultas = ['teknik', 'hukum', 'ekonomi dan bisnis'];

        // 2. Input Filter & Search
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi;
        $search = $request->search;
        $filterTahun = $request->tahun_lulus; // Ganti jadi 1 filter tahun

        $maxYearDb = Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->whereNotIn('fakultas', $pengecualianFakultas)
            ->max('tahun_lulus') ?? 2025;

        // 3. Base Query (Proteksi Fakultas & Batas 2025)
        $baseQuery = Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->whereNotIn('fakultas', $pengecualianFakultas);

        if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
        if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);

        // 4. Data Utama (Terapkan Filter Tahun di sini)
        $queryNow = (clone $baseQuery);
        if ($filterTahun) {
            $queryNow->where('tahun_lulus', $filterTahun);
        }

        $total = $queryNow->count();
        $tepat = (clone $queryNow)->where('lama_studi', '<=', 8)->count();
        $rataStudi = round((clone $queryNow)->avg('lama_studi'), 1) ?? 0;

        $persenTepatNow = ($total > 0) ? ($tepat / $total) * 100 : 0;
        $persenTerlambatNow = ($total > 0) ? 100 - $persenTepatNow : 0;

        // 5. Logika Trend 
        // Bandingkan tahun yang dipilih vs tahun sebelumnya (atau tahun terbaru vs sebelumnya jika tidak ada filter)
        $tahunAcuan = $filterTahun ? $filterTahun : $maxYearDb;
        $tahunLalu = $tahunAcuan - 1;

        $queryTrendNow = (clone $baseQuery)->where('tahun_lulus', $tahunAcuan);
        $totalTrendNow = $queryTrendNow->count();
        $tepatTrendNow = (clone $queryTrendNow)->where('lama_studi', '<=', 8)->count();
        $persenTepatTrendNow = ($totalTrendNow > 0) ? ($tepatTrendNow / $totalTrendNow) * 100 : 0;
        $persenTerlambatTrendNow = ($totalTrendNow > 0) ? 100 - $persenTepatTrendNow : 0;

        $queryPrev = (clone $baseQuery)->where('tahun_lulus', $tahunLalu);
        $totalPrev = $queryPrev->count();
        $tepatPrev = (clone $queryPrev)->where('lama_studi', '<=', 8)->count();
        $persenTepatPrev = ($totalPrev > 0) ? ($tepatPrev / $totalPrev) * 100 : 0;

        $trendTepat = $persenTepatTrendNow - $persenTepatPrev;
        $trendTerlambat = $persenTerlambatTrendNow - (100 - $persenTepatPrev);

        // 6. Query Data Mahasiswa (Tabel)
        $queryTable = (clone $queryNow);
        if ($search) {
            $queryTable->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
            });
        }
        $mahasiswas = $queryTable->orderBy('ipk', 'desc')->paginate(10)->appends($request->all());

        // 7. Grafik Garis Tren (Sengaja pakai $baseQuery agar grafiknya tetap nampil semua tahun untuk prodi/fakultas terkait, tidak jadi cuma 1 titik)
        $tren = (clone $baseQuery)->select(
            'tahun_lulus',
            DB::raw('count(*) as total'),
            DB::raw('count(CASE WHEN lama_studi <= 9 THEN 1 END) as tepat_waktu'),
            DB::raw('count(CASE WHEN lama_studi > 9 THEN 1 END) as terlambat')
        )
            ->groupBy('tahun_lulus')->orderBy('tahun_lulus', 'asc')->get();

        $listFakultas = Lulusan::select('fakultas')->distinct()->whereNotIn('fakultas', $pengecualianFakultas)->orderBy('fakultas')->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')->distinct()->where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025)->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->whereNotIn('fakultas', $pengecualianFakultas)->get()->groupBy('fakultas');

        $dataFakultas = Lulusan::select('fakultas', DB::raw('round((count(CASE WHEN lama_studi <= 8 THEN 1 END) / count(*)) * 100) as persentase'))
            ->where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->whereNotIn('fakultas', $pengecualianFakultas)
            ->groupBy('fakultas')->get();

        $topCumlaude = (clone $queryNow)->orderBy('ipk', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'total',
            'tepat',
            'rataStudi',
            'persenTepatNow',
            'persenTerlambatNow',
            'trendTepat',
            'trendTerlambat',
            'tren',
            'listFakultas',
            'listTahun',
            'dataFakultas',
            'topCumlaude',
            'filterTahun',
            'prodiPerFakultas',
            'mahasiswas'
        ));
    }

    public function exportPdf(Request $request)
    {
        if (!$request->fakultas) {
            return back()->with('error', 'Silakan pilih Fakultas terlebih dahulu.');
        }

        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $filterFakultas = $request->fakultas;
            $prodiSelected = $request->prodi;
            $search = $request->search;
            $filterTahun = $request->tahun_lulus; // Ambil filter tahun lulus

            $pengecualianFakultas = ['teknik', 'hukum', 'ekonomi dan bisnis'];

            $query = Lulusan::where('tahun_lulus', '>', 2000)
                ->where('tahun_lulus', '<=', 2025)
                ->whereNotIn('fakultas', $pengecualianFakultas)
                ->where('fakultas', $filterFakultas);

            // Terapkan filter tahun lulus ke PDF
            if ($filterTahun) {
                $query->where('tahun_lulus', $filterTahun);
            }

            if ($prodiSelected) $query->where('prodi', $prodiSelected);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('ipk', 'desc')->get();

            $metadata = [
                'fakultas' => $filterFakultas,
                'prodi'    => $prodiSelected ?? 'Semua Prodi',
                'periode'  => $filterTahun ? 'Angkatan ' . $filterTahun : 'Semua Angkatan (s/d 2025)',
                'total'    => $data->count(),
                'tanggal'  => now()->format('d F Y')
            ];

            $pdf = Pdf::loadView('exports.lulusan_pdf', compact('data', 'metadata'));
            return $pdf->setPaper('a4', 'portrait')->download('Laporan_Lulusan_UNLA.pdf');
        } catch (\Exception $e) {
            return "Gagal Export: " . $e->getMessage();
        }
    }

    public function settings()
    {
        // Ambil total data lulusan
        $total = Lulusan::where('tahun_lulus', '>', 2000)->count();

        // Ambil 50 riwayat log terbaru
        $logs = \App\Models\AuditLog::latest()->take(50)->get();

        return view('settings', compact('total', 'logs'));
    }

    public function importJson(Request $request)
    {
        // 1. Validasi file harus JSON
        $request->validate([
            'file_json' => 'required|mimes:json|max:2048' // Maksimal 2MB
        ]);

        // 2. Baca isi file
        $file = $request->file('file_json');
        $json = file_get_contents($file);
        $data = json_decode($json, true);

        if (!$data) {
            return back()->with('error', 'Format JSON tidak valid!');
        }

        $jumlahUpdate = 0;
        $jumlahBaru = 0;

        foreach ($data as $row) {
            // Pastikan NIM ada di file JSON
            if (empty($row['nim'])) {
                continue; // Skip kalau nggak ada NIM biar nggak error
            }

            // MAGIC LARAVEL: updateOrCreate
            // Array pertama: Kunci pencarian (NIM)
            // Array kedua: Data yang mau diisi/diupdate
            $lulusan = Lulusan::updateOrCreate(
                ['nim' => $row['nim']],
                [
                    'nama' => $row['nama'],
                    'fakultas' => $row['fakultas'],
                    'prodi' => $row['prodi'],
                    'tahun_lulus' => $row['tahun_lulus'],
                    'lama_studi' => $row['lama_studi'] ?? 0,
                    'ipk' => $row['ipk'] ?? 0,
                ]
            );

            // Cek apakah sistem baru saja membuat data baru?
            if ($lulusan->wasRecentlyCreated) {
                $jumlahBaru++;

                // Catat log khusus buat data yang benar-benar BARU masuk
                \App\Models\AuditLog::create([
                    'aksi' => 'Data Baru',
                    'keterangan' => "Menambahkan: {$row['nama']} (NIM: {$row['nim']}) - {$row['prodi']} Lulusan {$row['tahun_lulus']}"
                ]);
            }
            // Atau apakah sistem cuma mengubah/update data yang udah ada?
            else if ($lulusan->wasChanged()) {
                $jumlahUpdate++;
                \App\Models\AuditLog::create([
                    'aksi' => 'Data Diperbarui',
                    'keterangan' => "Memperbarui data: {$row['nama']} (NIM: {$row['nim']})"
                ]);
            }

            // NOTE: Bagian 'else' Lulusan::create yang lama sudah DIHAPUS.
            // Karena updateOrCreate sudah otomatis menghandle pembuatan data baru.
        }

        // 4. Balik ke halaman pengaturan dengan pesan sukses
        return back()->with('success', "Import Selesai! $jumlahUpdate data diperbarui, dan $jumlahBaru data baru ditambahkan ke sistem.");
    }

    public function kinerjaProdi(Request $request)
    {
        // Tangkap input filter
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi; // Menangkap filter prodi
        $filterTahun = $request->tahun_lulus;

        $pengecualianFakultas = ['teknik', 'hukum', 'ekonomi dan bisnis'];

        // Base Query
        $query = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->whereNotIn('fakultas', $pengecualianFakultas);

        // Terapkan filter Fakultas jika dipilih
        if ($filterFakultas) {
            $query->where('fakultas', $filterFakultas);
        }

        // Terapkan filter Prodi jika dipilih
        if ($prodiSelected) {
            $query->where('prodi', $prodiSelected);
        }

        // Terapkan filter Angkatan jika dipilih
        if ($filterTahun) {
            $query->where('tahun_lulus', $filterTahun);
        }

        // Mengelompokkan (Group By) data berdasarkan Fakultas & Prodi
        $kinerjaProdi = $query->select(
            'fakultas',
            'prodi',
            \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_lulusan'),
            \Illuminate\Support\Facades\DB::raw('ROUND(AVG(lama_studi), 1) as rata_studi'),
            \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN lama_studi <= 8 THEN 1 END) as tepat_waktu')
        )
            ->groupBy('fakultas', 'prodi')
            ->orderBy('fakultas')
            ->orderBy('prodi')
            ->get();

        // Kalkulasi Persentase Tepat Waktu per Prodi
        foreach ($kinerjaProdi as $kp) {
            $kp->persen_tepat = $kp->total_lulusan > 0
                ? round(($kp->tepat_waktu / $kp->total_lulusan) * 100, 1)
                : 0;
        }

        // --- DATA UNTUK SUMMARY CARDS PADA UI ---
        $totalAlumni = $kinerjaProdi->sum('total_lulusan');
        $prodiTercepat = $kinerjaProdi->where('total_lulusan', '>', 0)->sortBy('rata_studi')->first();
        $prodiTepatTertinggi = $kinerjaProdi->where('total_lulusan', '>', 0)->sortByDesc('persen_tepat')->first();

        // --- DATA UNTUK DROPDOWN FILTER ---
        $listFakultas = \App\Models\Lulusan::select('fakultas')
            ->distinct()
            ->whereNotIn('fakultas', $pengecualianFakultas)
            ->orderBy('fakultas')
            ->pluck('fakultas');

        // Ambil data prodi per fakultas untuk logika JavaScript
        $prodiPerFakultas = \App\Models\Lulusan::select('fakultas', 'prodi')
            ->distinct()
            ->whereNotIn('fakultas', $pengecualianFakultas)
            ->get()
            ->groupBy('fakultas');

        $listTahun = \App\Models\Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        // Kirim semua variabel ke View
        return view('kinerja_prodi', compact(
            'kinerjaProdi',
            'listFakultas',
            'listTahun',
            'filterTahun',
            'filterFakultas',
            'prodiSelected',
            'prodiPerFakultas',
            'totalAlumni',
            'prodiTercepat',
            'prodiTepatTertinggi'
        ));
    }
}
