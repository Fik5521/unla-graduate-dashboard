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
        // 1. Input Filter & Search
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi;
        $search = $request->search;

        // 2. Tahun Aktif (Default & Range)
        $minYearDb = Lulusan::where('tahun_lulus', '>', 2000)->min('tahun_lulus') ?? 2016;
        $maxYearDb = Lulusan::where('tahun_lulus', '>', 2000)->max('tahun_lulus') ?? date('Y');
        $tahunMulai = $request->tahun_mulai ?? $minYearDb;
        $tahunSelesai = $request->tahun_selesai ?? $maxYearDb;

        // 3. Base Query untuk Grafik & Card (Makro)
        $baseQuery = Lulusan::where('tahun_lulus', '>', 2000);
        if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
        if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);

        // Data Sekarang (Sesuai Range Tahun)
        $queryNow = (clone $baseQuery)->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai]);

        $total = $queryNow->count();
        $tepat = (clone $queryNow)->where('lama_studi', '<=', 8)->count();
        $rataStudi = round((clone $queryNow)->avg('lama_studi'), 1) ?? 0;

        $persenTepatNow = ($total > 0) ? ($tepat / $total) * 100 : 0;
        $persenTerlambatNow = ($total > 0) ? 100 - $persenTepatNow : 0;

        // 4. Logika Trend (Bandingkan dengan 1 Tahun Sebelumnya)
        $tahunLalu = $tahunMulai - 1;
        $queryPrev = (clone $baseQuery)->where('tahun_lulus', $tahunLalu);
        $totalPrev = $queryPrev->count();
        $tepatPrev = (clone $queryPrev)->where('lama_studi', '<=', 8)->count();
        $persenTepatPrev = ($totalPrev > 0) ? ($tepatPrev / $totalPrev) * 100 : 0;
        $trendTepat = $persenTepatNow - $persenTepatPrev;
        $trendTerlambat = $persenTerlambatNow - (100 - $persenTepatPrev);

        // 5. Query Data Mahasiswa (Tabel)
        $queryTable = (clone $queryNow);
        if ($search) {
            $queryTable->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
            });
        }
        $mahasiswas = $queryTable->orderBy('ipk', 'desc')->paginate(10)->appends($request->all());

        // 6. Data Grafik & Pendukung
        $tren = (clone $queryNow)->select(
            'tahun_lulus',
            DB::raw('count(*) as total'),
            DB::raw('count(CASE WHEN lama_studi <= 9 THEN 1 END) as tepat_waktu'),
            DB::raw('count(CASE WHEN lama_studi > 9 THEN 1 END) as terlambat')
        )
            ->groupBy('tahun_lulus')->orderBy('tahun_lulus', 'asc')->get();

        $listFakultas = Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')->distinct()->where('tahun_lulus', '>', 2000)->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');
        $dataFakultas = Lulusan::select('fakultas', DB::raw('round((count(CASE WHEN lama_studi <= 8 THEN 1 END) / count(*)) * 100) as persentase'))
            ->where('tahun_lulus', '>', 2000)->groupBy('fakultas')->get();
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
            'tahunMulai',
            'tahunSelesai',
            'prodiPerFakultas',
            'mahasiswas'
        ));
    }

    public function exportPdf(Request $request)
    {
        // Proteksi: Wajib pilih Fakultas
        if (!$request->fakultas) {
            return back()->with('error', 'Silakan pilih Fakultas terlebih dahulu.');
        }

        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $filterFakultas = $request->fakultas;
            $prodiSelected = $request->prodi;
            $search = $request->search;

            $tahunMulai = $request->tahun_mulai;
            $tahunSelesai = $request->tahun_selesai;

            $query = Lulusan::where('tahun_lulus', '>', 2000)
                ->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai])
                ->where('fakultas', $filterFakultas);

            if ($prodiSelected) $query->where('prodi', $prodiSelected);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
                });
            }

            $data = $query->orderBy('ipk', 'desc')->get();

            // Siapkan metadata (Pastikan key 'periode' ada di sini)
            $metadata = [
                'fakultas' => $filterFakultas,
                'prodi'    => $prodiSelected ?? 'Semua Prodi',
                'periode'  => $tahunMulai . ' - ' . $tahunSelesai, // Kita pakai periode
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
}
