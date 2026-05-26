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
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi;
        $filterTahun = $request->tahun_lulus;

        $maxYearDb = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->max('tahun_lulus') ?? 2025;

        $baseQuery = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025);

        if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
        if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);

        $queryNow = (clone $baseQuery);
        if ($filterTahun) $queryNow->where('tahun_lulus', $filterTahun);

        // Metrik Utama
        $totalMahasiswa = (clone $queryNow)->count();
        $berhasilLulus   = (clone $queryNow)->where('status', 'Lulus')->count();
        $lulusTepat      = (clone $queryNow)->where('status', 'Lulus')->where('lama_studi', '<=', 9)->count();
        $tidakLulus      = (clone $queryNow)->where('status', '!=', 'Lulus')->count();
        $rataStudi       = round((clone $queryNow)->where('status', 'Lulus')->avg('lama_studi'), 1) ?? 0;

        $persenTepatNow = ($berhasilLulus > 0) ? ($lulusTepat / $berhasilLulus) * 100 : 0;
        $persenTerlambatNow = ($berhasilLulus > 0) ? 100 - $persenTepatNow : 0;

        // Kinerja Prodi (Stacked Bar)
        $kinerjaProdi = (clone $baseQuery)->select(
            'prodi',
            \DB::raw('COUNT(id) as total_mhs'),
            \DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
            \DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi <= 9 THEN 1 END) as tepat_waktu'),
            \DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi > 9 THEN 1 END) as tidak_tepat_waktu'),
            \DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus')
        )->groupBy('prodi')->get();

        foreach ($kinerjaProdi as $kp) {
            $total = $kp->total_mhs ?: 1;
            $kp->persen_tepat = round(($kp->tepat_waktu / $total) * 100);
            $kp->persen_lambat = round(($kp->tidak_tepat_waktu / $total) * 100);
            $kp->persen_gagal = round(($kp->tidak_lulus / $total) * 100);
        }

        // Tren (Line Chart)
        $tren = (clone $baseQuery)->select(
            'tahun_lulus',
            \DB::raw('count(*) as total'),
            \DB::raw('count(CASE WHEN status = "Lulus" AND lama_studi <= 9 THEN 1 END) as tepat_waktu'),
            \DB::raw('count(CASE WHEN status = "Lulus" AND lama_studi > 9 THEN 1 END) as terlambat'),
            \DB::raw('count(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus')
        )->groupBy('tahun_lulus')->orderBy('tahun_lulus', 'asc')->get();

        // Data Trend Calculation (untuk panah naik/turun)
        $tahunAcuan = $filterTahun ?: $maxYearDb;
        $tahunLalu = $tahunAcuan - 1;
        $queryTrendNow = (clone $baseQuery)->where('tahun_lulus', $tahunAcuan)->where('status', 'Lulus');
        $totalTrendNow = $queryTrendNow->count();
        $tepatTrendNow = (clone $queryTrendNow)->where('lama_studi', '<=', 9)->count();
        $persenTepatTrendNow = ($totalTrendNow > 0) ? ($tepatTrendNow / $totalTrendNow) * 100 : 0;

        $queryPrev = (clone $baseQuery)->where('tahun_lulus', $tahunLalu)->where('status', 'Lulus');
        $totalPrev = $queryPrev->count();
        $tepatPrev = (clone $queryPrev)->where('lama_studi', '<=', 9)->count();
        $persenTepatPrev = ($totalPrev > 0) ? ($tepatPrev / $totalPrev) * 100 : 0;

        $trendTepat = $persenTepatTrendNow - $persenTepatPrev;
        $trendTerlambat = ($persenTepatTrendNow > 0) ? (100 - $persenTepatTrendNow) - (100 - $persenTepatPrev) : 0;

        $listFakultas = \App\Models\Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listTahun = \App\Models\Lulusan::select('tahun_lulus')->distinct()->where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025)->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');
        $prodiPerFakultas = \App\Models\Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');

        return view('dashboard', compact(
            'totalMahasiswa',
            'berhasilLulus',
            'lulusTepat',
            'tidakLulus',
            'rataStudi',
            'persenTepatNow',
            'persenTerlambatNow',
            'trendTepat',
            'trendTerlambat',
            'tren',
            'kinerjaProdi',
            'listFakultas',
            'listTahun',
            'prodiPerFakultas'
        ));
    }

    // FUNGSI BARU KHUSUS HALAMAN DATA MAHASISWA
    public function mahasiswas(Request $request)
    {
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi;
        $filterTahun = $request->tahun_lulus;
        $search = $request->search;

        $query = Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025);

        // Filter Fakultas
        if ($filterFakultas) {
            $query->where('fakultas', $filterFakultas);
        }

        // Filter Prodi
        if ($prodiSelected) {
            $query->where('prodi', $prodiSelected);
        }

        // Filter Tahun
        if ($filterTahun) {
            $query->where('tahun_lulus', $filterTahun);
        }

        // Fitur Pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // URUTAN DATA: Sekarang diurutkan berdasarkan NIM (Ascending)
        // Jadi daftar tetap rapi dan tidak berubah-ubah berdasarkan IPK
        $mahasiswas = $query->orderBy('nim', 'asc')
            ->paginate(15)
            ->appends($request->all());

        // Data untuk dropdown filter
        $listFakultas = Lulusan::select('fakultas')
            ->distinct()
            ->orderBy('fakultas')
            ->pluck('fakultas');

        $listTahun = Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025)
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')
            ->distinct()
            ->get()
            ->groupBy('fakultas');

        return view('mahasiswas', compact('mahasiswas', 'listFakultas', 'listTahun', 'prodiPerFakultas'));
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

            $query = Lulusan::where('tahun_lulus', '>', 2000)
                ->where('tahun_lulus', '<=', 2025)
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
            \App\Models\AuditLog::create([
                'aksi' => 'Export Data',
                'keterangan' => "Pengguna telah melakukan export data lulusan ke file Excel/PDF."
            ]);
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

    public function settings(Request $request)
    {
        $search = $request->search; // Ambil input pencarian

        // Query dasar
        $query = \App\Models\AuditLog::latest();

        // Jika ada kata kunci pencarian, filter berdasarkan aksi atau keterangan
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('aksi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString(); // withQueryString biar keyword search tidak hilang saat pindah halaman

        $total = Lulusan::where('tahun_lulus', '>', 2000)->count();

        return view('settings', compact('total', 'logs'));
    }

    public function importJson(Request $request)
    {
        $request->validate([
            'file_json' => 'required|file|max:51200'
        ]);

        $file = $request->file('file_json');
        $json = file_get_contents($file);
        $dataMentah = json_decode($json, true);

        if (!$dataMentah) {
            return back()->with('error', 'Format JSON tidak valid atau rusak!');
        }

        $data = isset($dataMentah['data']) ? $dataMentah['data'] : $dataMentah;

        if (!is_array($data)) {
            return back()->with('error', 'Struktur data di dalam JSON tidak dikenali.');
        }

        $jumlahUpdate = 0;
        $jumlahBaru = 0;

        // =========================================================
        // KAMUS PINTAR FAKULTAS (Silakan tambah/ubah sesuai prodi)
        // =========================================================
        $mapFakultas = [
            'Informatika' => 'Fakultas Teknik',
            'Teknik Industri' => 'Fakultas Teknik',
            'Teknik Sipil' => 'Fakultas Teknik',
            'Teknik Elektro' => 'Fakultas Teknik',
            'Arsitektur' => 'Fakultas Teknik',
            'Hukum' => 'Fakultas Hukum',
            'Manajemen' => 'Fakultas Ekonomi dan Bisnis',
            'Akuntansi' => 'Fakultas Ekonomi dan Bisnis',
            'Ilmu Komunikasi' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
            'Ilmu Pemerintahan' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
            'Kesejahteraan Sosial' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
            'Kepolisian' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
            'Pendidikan' => 'Fakultas Keguruan dan Ilmu Pendidikan',
            'PGSD' => 'Fakultas Keguruan dan Ilmu Pendidikan'
        ];

        foreach ($data as $row) {
            $nim = $row['nim'] ?? null;
            if (empty($nim)) {
                continue;
            }

            $tahunLulus = $row['tahun_lulus'] ?? null;
            if (!$tahunLulus && !empty($row['tanggal_keluar'])) {
                $tahunLulus = substr($row['tanggal_keluar'], -4);
            }

            $lamaStudi = 0;
            $angkatan = $row['angkatan'] ?? null;
            $idPeriodeKeluar = $row['id_periode_keluar'] ?? null;

            if ($angkatan && $idPeriodeKeluar && strlen($idPeriodeKeluar) >= 5) {
                $tahunKeluar = (int) substr($idPeriodeKeluar, 0, 4);
                $smtKeluar = (int) substr($idPeriodeKeluar, 4, 1);
                $selisihTahun = $tahunKeluar - (int) $angkatan;
                $lamaStudi = ($selisihTahun * 2) + $smtKeluar;
            } elseif ($angkatan && $tahunLulus) {
                $lamaStudi = ($tahunLulus - $angkatan) * 2;
                if (!empty($row['tanggal_keluar'])) {
                    $bulanKeluar = (int) substr($row['tanggal_keluar'], 3, 2);
                    if ($bulanKeluar >= 7) {
                        $lamaStudi += 1;
                    }
                }
            }

            $status = 'Lulus';
            if (isset($row['nama_jenis_keluar'])) {
                $status = $row['nama_jenis_keluar'];
            } elseif (isset($row['id_jns_keluar'])) {
                if ($row['id_jns_keluar'] == 1) $status = 'Lulus';
                elseif ($row['id_jns_keluar'] == 4) $status = 'Mengundurkan Diri';
                elseif ($row['id_jns_keluar'] == 5) $status = 'Putus Studi';
                else $status = 'Tidak Lulus';
            }

            // --- Logika Pendeteksi Fakultas Otomatis ---
            $namaProdi = $row['nama_program_studi'] ?? $row['prodi'] ?? '-';
            $fakultas = 'Belum Diatur'; // Fallback default

            foreach ($mapFakultas as $kataKunci => $namaFakultas) {
                if (stripos($namaProdi, $kataKunci) !== false) {
                    $fakultas = $namaFakultas;
                    break;
                }
            }

            // Insert / Update
            $lulusan = \App\Models\Lulusan::updateOrCreate(
                ['nim' => $nim],
                [
                    'nama' => $row['nama_mahasiswa'] ?? $row['nama'] ?? 'Tanpa Nama',
                    'fakultas' => $fakultas, // Gunakan fakultas hasil tebakan
                    'prodi' => $namaProdi,
                    'tahun_lulus' => $tahunLulus,
                    'lama_studi' => $lamaStudi,
                    'ipk' => $row['ipk'] ?? 0,
                    'status' => $status,
                ]
            );

            if ($lulusan->wasRecentlyCreated) {
                $jumlahBaru++;
                \App\Models\AuditLog::create([
                    'aksi' => 'Data Baru',
                    'keterangan' => "Import: {$lulusan->nama} (NIM: {$nim})"
                ]);
            } else if ($lulusan->wasChanged()) {
                $jumlahUpdate++;
            }
        }

        return back()->with('success', "Import Selesai! $jumlahUpdate diperbarui, dan $jumlahBaru data baru masuk.");
    }

    public function kinerjaProdi(Request $request)
    {
        // 1. Tangkap input filter
        $filterFakultas = $request->fakultas;
        $prodiSelected = $request->prodi;
        $filterTahun = $request->tahun_lulus;

        // 2. Base Query (Gunakan clone agar tidak perlu menulis ulang where)
        $baseQuery = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
            ->where('tahun_lulus', '<=', 2025);

        // Terapkan filter ke Base Query
        if ($filterFakultas) {
            $baseQuery->where('fakultas', $filterFakultas);
        }
        if ($prodiSelected) {
            $baseQuery->where('prodi', $prodiSelected);
        }
        if ($filterTahun) {
            $baseQuery->where('tahun_lulus', $filterTahun);
        }

        // 3. Query Kinerja Prodi (Sesuai dengan alias yang dipakai di View)
        $kinerjaProdi = (clone $baseQuery)->select(
            'fakultas',
            'prodi',
            \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_mhs'),
            \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
            \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi <= 9 THEN 1 END) as tepat_waktu'),
            \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi > 9 THEN 1 END) as tidak_tepat_waktu'),
            \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
            \Illuminate\Support\Facades\DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
        )
            ->groupBy('fakultas', 'prodi')
            ->paginate(10) // <-- PAGINATION DI SINI (10 baris per halaman)
            ->withQueryString(); // <-- Biar filter tidak hilang saat pindah halaman

        // 4. Kalkulasi Persentase (Wajib menggunakan nama alias yang sama)
        foreach ($kinerjaProdi as $kp) {
            $total = $kp->total_mhs ?: 1;
            // Menggunakan alias 'berhasil_lulus' karena itu hasil count Lulus
            $kp->persen_tepat = $kp->berhasil_lulus > 0
                ? round(($kp->tepat_waktu / $kp->berhasil_lulus) * 100, 1)
                : 0;
        }

        // 5. Data untuk Summary Cards
        $totalAlumni = $kinerjaProdi->sum('total_mhs');
        $prodiTercepat = $kinerjaProdi->where('berhasil_lulus', '>', 0)->sortBy('rata_studi')->first();
        $prodiTepatTertinggi = $kinerjaProdi->where('berhasil_lulus', '>', 0)->sortByDesc('persen_tepat')->first();

        // Mencari prodi dengan lulusan tepat waktu paling banyak
        $prodiTerbaik = $kinerjaProdi->sortByDesc('tepat_waktu')->first()->prodi ?? '-';

        // Mencari prodi dengan gagal/tidak lulus paling banyak
        $prodiPerhatian = $kinerjaProdi->sortByDesc('tidak_lulus')->first()->prodi ?? '-';

        // 6. Data Dropdown
        $listFakultas = \App\Models\Lulusan::distinct()->orderBy('fakultas')->pluck('fakultas');
        $prodiPerFakultas = \App\Models\Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');
        $listTahun = \App\Models\Lulusan::distinct()->where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025)->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');

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
            'prodiTepatTertinggi',
            'prodiTerbaik',
            'prodiPerhatian'
        ));
    }

    /**
     * FUNGSI EXPORT EXCEL KHUSUS KINERJA PRODI
     */
    public function exportKinerjaExcel(Request $request)
    {
        try {
            // 1. TANGKAP INPUT FILTER
            $filterFakultas = $request->fakultas;
            $prodiSelected = $request->prodi;
            $filterTahun = $request->tahun_lulus;

            // 2. BASE QUERY
            $baseQuery = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
                ->where('tahun_lulus', '<=', 2025);

            if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
            if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);
            if ($filterTahun) $baseQuery->where('tahun_lulus', $filterTahun);

            // 3. QUERY KINERJA PRODI
            $kinerjaProdi = $baseQuery->select(
                'fakultas',
                'prodi',
                \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_mhs'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi <= 9 THEN 1 END) as tepat_waktu'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
                \Illuminate\Support\Facades\DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
            )
                ->groupBy('fakultas', 'prodi')
                ->get();

            \App\Models\AuditLog::create([
                'aksi' => 'Export Data',
                'keterangan' => "Pengguna telah melakukan export data lulusan ke file Excel/PDF."
            ]);
            // 4. ATUR RESPONSE EXCEL (CSV)
            $fileName = 'Laporan_Kinerja_Prodi_' . date('Y-m-d_H-i') . '.csv';
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = ['Fakultas', 'Program Studi', 'Total Mahasiswa', 'Berhasil Lulus', 'Tepat Waktu', 'Tidak Lulus', 'Avg Studi (Semester)'];

            $callback = function () use ($kinerjaProdi, $columns) {
                $file = fopen('php://output', 'w');

                // Tambahkan tanda biner BOM agar Excel mengenali karakter UTF-8 dengan baik
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBBF));

                // Masukkan nama kolom dengan separator TITIK KOMA (;)
                fputcsv($file, $columns, ';');

                // Masukkan baris data dengan separator TITIK KOMA (;)
                foreach ($kinerjaProdi as $kp) {
                    fputcsv($file, [
                        $kp->fakultas,
                        $kp->prodi,
                        $kp->total_mhs,
                        $kp->berhasil_lulus,
                        $kp->tepat_waktu,
                        $kp->tidak_lulus,
                        $kp->rata_studi ?? 0
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Export Excel: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI EXPORT PDF KHUSUS KINERJA PRODI
     */
    public function exportKinerjaPdf(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            // 1. TANGKAP INPUT FILTER
            $filterFakultas = $request->fakultas;
            $prodiSelected = $request->prodi;
            $filterTahun = $request->tahun_lulus;

            // 2. BASE QUERY
            $baseQuery = \App\Models\Lulusan::where('tahun_lulus', '>', 2000)
                ->where('tahun_lulus', '<=', 2025);

            if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
            if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);
            if ($filterTahun) $baseQuery->where('tahun_lulus', $filterTahun);

            // 3. QUERY KINERJA PRODI
            $kinerjaProdi = $baseQuery->select(
                'fakultas',
                'prodi',
                \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_mhs'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status = "Lulus" AND lama_studi <= 9 THEN 1 END) as tepat_waktu'),
                \Illuminate\Support\Facades\DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
                \Illuminate\Support\Facades\DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
            )
                ->groupBy('fakultas', 'prodi')
                ->get();

            \App\Models\AuditLog::create([
                'aksi' => 'Export Data',
                'keterangan' => "Pengguna telah melakukan export data lulusan ke file Excel/PDF."
            ]);
            // 4. SIAPKAN METADATA UNTUK PDF
            $metadata = [
                'fakultas' => $filterFakultas ?? 'Semua Fakultas',
                'prodi'    => $prodiSelected ?? 'Semua Prodi',
                'periode'  => $filterTahun ? 'Angkatan ' . $filterTahun : 'Semua Angkatan',
                'tanggal'  => now()->format('d F Y')
            ];

            // 5. LOAD VIEW DAN DOWNLOAD
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.kinerja_pdf', compact('kinerjaProdi', 'metadata'));
            return $pdf->setPaper('a4', 'landscape')->download('Laporan_Kinerja_Prodi_UNLA.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Export PDF: ' . $e->getMessage());
        }
    }
}
