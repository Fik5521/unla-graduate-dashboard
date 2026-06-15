<?php

namespace App\Http\Controllers;

use App\Models\Lulusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Default langsung ke Fakultas Hukum
        $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');
        $prodiSelected = $request->prodi;
        $filterAngkatan = $request->angkatan;

        $maxAngkatanDb = \App\Models\Lulusan::max('angkatan') ?? date('Y');

        $baseQuery = \App\Models\Lulusan::query();

        if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
        if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);

        $queryNow = (clone $baseQuery);
        if ($filterAngkatan) $queryNow->where('angkatan', $filterAngkatan);

        // --- METRIK UTAMA ---
        $totalMahasiswa = (clone $queryNow)->count();

        // Lulus Tepat: (S2 <= 5 Sem) ATAU (S1 <= 9 Sem)
        $lulusTepat = (clone $queryNow)->where('status', 'Lulus')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('fakultas', 'Pascasarjana')->where('lama_studi', '<=', 5);
                })->orWhere(function ($sub) {
                    $sub->where('fakultas', '!=', 'Pascasarjana')->where('lama_studi', '<=', 9);
                });
            })->count();

        // Lulus Terlambat: (S2 > 5 Sem) ATAU (S1 > 9 Sem)
        $berhasilLulus = (clone $queryNow)->where('status', 'Lulus')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('fakultas', 'Pascasarjana')->where('lama_studi', '>', 5);
                })->orWhere(function ($sub) {
                    $sub->where('fakultas', '!=', 'Pascasarjana')->where('lama_studi', '>', 9);
                });
            })->count();

        $tidakLulus = (clone $queryNow)->where('status', '!=', 'Lulus')->count();
        $rataStudi = round((clone $queryNow)->where('status', 'Lulus')->avg('lama_studi'), 1) ?? 0;

        $persenTepatNow = ($totalMahasiswa > 0) ? round(($lulusTepat / $totalMahasiswa) * 100, 1) : 0;
        $persenTerlambatNow = ($totalMahasiswa > 0) ? round(($berhasilLulus / $totalMahasiswa) * 100, 1) : 0;
        $persenGagalNow = ($totalMahasiswa > 0) ? round(($tidakLulus / $totalMahasiswa) * 100, 1) : 0;

        // --- KINERJA PRODI ---
        $kinerjaProdi = (clone $baseQuery)->select(
            'prodi',
            DB::raw('COUNT(id) as total_mhs'),
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi > 5) OR (fakultas != "Pascasarjana" AND lama_studi > 9)) THEN 1 END) as tidak_tepat_waktu'),
            DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus')
        )->groupBy('prodi')->get();

        foreach ($kinerjaProdi as $kp) {
            $total = $kp->total_mhs ?: 1;
            $kp->persen_tepat = round(($kp->tepat_waktu / $total) * 100);
            $kp->persen_lambat = round(($kp->tidak_tepat_waktu / $total) * 100);
            $kp->persen_gagal = round(($kp->tidak_lulus / $total) * 100);
        }

        // --- TREN KELULUSAN ---
        $tren = (clone $baseQuery)->select(
            'angkatan',
            DB::raw('count(*) as total'),
            DB::raw('count(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
            DB::raw('count(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi > 5) OR (fakultas != "Pascasarjana" AND lama_studi > 9)) THEN 1 END) as terlambat'),
            DB::raw('count(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus')
        )->whereNotNull('angkatan')->groupBy('angkatan')->orderBy('angkatan', 'asc')->get();

        // --- TREND PANAH KARTU METRIK ---
        $angkatanAcuan = $filterAngkatan ?: $maxAngkatanDb;
        $angkatanLalu = $angkatanAcuan - 1;

        $queryTrendNow = (clone $baseQuery)->where('angkatan', $angkatanAcuan)->where('status', 'Lulus');
        $totalTrendNow = $queryTrendNow->count();
        $tepatTrendNow = (clone $queryTrendNow)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('fakultas', 'Pascasarjana')->where('lama_studi', '<=', 5);
            })
                ->orWhere(function ($sub) {
                    $sub->where('fakultas', '!=', 'Pascasarjana')->where('lama_studi', '<=', 9);
                });
        })->count();
        $persenTepatTrendNow = ($totalTrendNow > 0) ? ($tepatTrendNow / $totalTrendNow) * 100 : 0;

        $queryPrev = (clone $baseQuery)->where('angkatan', $angkatanLalu)->where('status', 'Lulus');
        $totalPrev = $queryPrev->count();
        $tepatPrev = (clone $queryPrev)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('fakultas', 'Pascasarjana')->where('lama_studi', '<=', 5);
            })
                ->orWhere(function ($sub) {
                    $sub->where('fakultas', '!=', 'Pascasarjana')->where('lama_studi', '<=', 9);
                });
        })->count();
        $persenTepatPrev = ($totalPrev > 0) ? ($tepatPrev / $totalPrev) * 100 : 0;

        $trendTepat = $persenTepatTrendNow - $persenTepatPrev;
        $trendTerlambat = ($persenTepatTrendNow > 0) ? (100 - $persenTepatTrendNow) - (100 - $persenTepatPrev) : 0;

        // Data Dropdown
        $listFakultas = Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listAngkatan = Lulusan::select('angkatan')->whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');

        return view('dashboard', compact(
            'totalMahasiswa',
            'berhasilLulus',
            'lulusTepat',
            'tidakLulus',
            'rataStudi',
            'persenTepatNow',
            'persenTerlambatNow',
            'persenGagalNow',
            'trendTepat',
            'trendTerlambat',
            'tren',
            'kinerjaProdi',
            'listFakultas',
            'listAngkatan',
            'prodiPerFakultas',
            'filterFakultas'
        ));
    }

    public function mahasiswas(Request $request)
    {
        $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');
        $prodiSelected = $request->prodi;
        $filterAngkatan = $request->angkatan;
        $search = $request->search;

        $query = Lulusan::query();

        if ($filterFakultas) $query->where('fakultas', $filterFakultas);
        if ($prodiSelected) $query->where('prodi', $prodiSelected);
        if ($filterAngkatan) $query->where('angkatan', $filterAngkatan);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->orderBy('nim', 'asc')->paginate(15)->appends($request->all());

        $listFakultas = Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listAngkatan = Lulusan::select('angkatan')->whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');

        return view('mahasiswas', compact('mahasiswas', 'listFakultas', 'listAngkatan', 'prodiPerFakultas'));
    }

    public function kinerjaProdi(Request $request)
    {
        $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');
        $prodiSelected = $request->prodi;
        $filterAngkatan = $request->angkatan;

        $baseQuery = Lulusan::query();

        if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
        if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);
        if ($filterAngkatan) $baseQuery->where('angkatan', $filterAngkatan);

        $kinerjaProdi = (clone $baseQuery)->select(
            'fakultas',
            'prodi',
            DB::raw('COUNT(id) as total_mhs'),
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi > 5) OR (fakultas != "Pascasarjana" AND lama_studi > 9)) THEN 1 END) as berhasil_lulus'),
            DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
            DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
        )->groupBy('fakultas', 'prodi')->paginate(10)->withQueryString();

        foreach ($kinerjaProdi as $kp) {
            $totalLulus = $kp->tepat_waktu + $kp->berhasil_lulus;
            $kp->persen_tepat = $totalLulus > 0 ? round(($kp->tepat_waktu / $totalLulus) * 100, 1) : 0;
        }

        $kinerjaProdiChart = (clone $baseQuery)->select(
            'prodi',
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
            DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi > 5) OR (fakultas != "Pascasarjana" AND lama_studi > 9)) THEN 1 END) as berhasil_lulus'),
            DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
            DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN ipk END), 2) as rata_ipk')
        )->groupBy('prodi')->get();

        $distribusiIpk = (clone $baseQuery)->where('status', 'Lulus')->select(
            DB::raw('COUNT(CASE WHEN ipk >= 3.51 AND (jenis_daftar NOT LIKE "%pindahan%" OR jenis_daftar IS NULL) THEN 1 END) as cumlaude'),
            DB::raw('COUNT(CASE WHEN (ipk >= 3.00 AND ipk < 3.51) OR (ipk >= 3.51 AND jenis_daftar LIKE "%pindahan%") THEN 1 END) as sangat_memuaskan'),
            DB::raw('COUNT(CASE WHEN ipk >= 2.76 AND ipk < 3.00 THEN 1 END) as memuaskan'),
            DB::raw('COUNT(CASE WHEN ipk > 0 AND ipk < 2.76 THEN 1 END) as cukup')
        )->first();

        $totalAlumni = $kinerjaProdiChart->sum('tepat_waktu') + $kinerjaProdiChart->sum('berhasil_lulus') + $kinerjaProdiChart->sum('tidak_lulus');
        $prodiTerbaik = $kinerjaProdiChart->sortByDesc('tepat_waktu')->first()->prodi ?? '-';
        $prodiPerhatian = $kinerjaProdiChart->sortByDesc('tidak_lulus')->first()->prodi ?? '-';

        $listFakultas = Lulusan::distinct()->orderBy('fakultas')->pluck('fakultas');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->get()->groupBy('fakultas');
        $listAngkatan = Lulusan::select('angkatan')->whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan');

        return view('kinerja_prodi', compact(
            'kinerjaProdi',
            'kinerjaProdiChart',
            'distribusiIpk',
            'listFakultas',
            'listAngkatan',
            'filterAngkatan',
            'filterFakultas',
            'prodiSelected',
            'prodiPerFakultas',
            'totalAlumni',
            'prodiTerbaik',
            'prodiPerhatian'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');

        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $prodiSelected = $request->prodi;
            $search = $request->search;
            $filterTahun = $request->tahun_lulus;

            $query = Lulusan::where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025)->where('fakultas', $filterFakultas);

            if ($filterTahun) $query->where('tahun_lulus', $filterTahun);
            if ($prodiSelected) $query->where('prodi', $prodiSelected);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")->orWhere('nim', 'like', "%{$search}%");
                });
            }

            // METODE PAKSA LOG
            $log = new AuditLog();
            $log->user_id = Auth::id();
            $log->ip_address = request()->ip();
            $log->aksi = 'Export Data';
            $log->keterangan = "Pengguna telah melakukan export data lulusan ke file PDF.";
            $log->save();

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

    public function exportKinerjaExcel(Request $request)
    {
        try {
            $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');
            $prodiSelected = $request->prodi;
            $filterTahun = $request->tahun_lulus;

            $baseQuery = Lulusan::where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025);

            if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
            if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);
            if ($filterTahun) $baseQuery->where('tahun_lulus', $filterTahun);

            $kinerjaProdi = $baseQuery->select(
                'fakultas',
                'prodi',
                DB::raw('COUNT(id) as total_mhs'),
                DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
                DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
                DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
                DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
            )->groupBy('fakultas', 'prodi')->get();

            // METODE PAKSA LOG
            $log = new AuditLog();
            $log->user_id = Auth::id();
            $log->ip_address = request()->ip();
            $log->aksi = 'Export Data';
            $log->keterangan = "Pengguna telah melakukan export data kinerja prodi ke file Excel (CSV).";
            $log->save();

            $fileName = 'Laporan_Kinerja_Prodi_' . date('Y-m-d_H-i') . '.csv';
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $columns = ['Fakultas', 'Program Studi', 'Total Mahasiswa', 'Berhasil Lulus', 'Tepat Waktu', 'Tidak Lulus', 'Avg Studi (Semester)'];

            $callback = function () use ($kinerjaProdi, $columns) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBBF));
                fputcsv($file, $columns, ';');
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

    public function exportKinerjaPdf(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $filterFakultas = $request->input('fakultas', 'Fakultas Hukum');
            $prodiSelected = $request->prodi;
            $filterTahun = $request->tahun_lulus;

            $baseQuery = Lulusan::where('tahun_lulus', '>', 2000)->where('tahun_lulus', '<=', 2025);

            if ($filterFakultas) $baseQuery->where('fakultas', $filterFakultas);
            if ($prodiSelected) $baseQuery->where('prodi', $prodiSelected);
            if ($filterTahun) $baseQuery->where('tahun_lulus', $filterTahun);

            $kinerjaProdi = $baseQuery->select(
                'fakultas',
                'prodi',
                DB::raw('COUNT(id) as total_mhs'),
                DB::raw('COUNT(CASE WHEN status = "Lulus" THEN 1 END) as berhasil_lulus'),
                DB::raw('COUNT(CASE WHEN status = "Lulus" AND ((fakultas = "Pascasarjana" AND lama_studi <= 5) OR (fakultas != "Pascasarjana" AND lama_studi <= 9)) THEN 1 END) as tepat_waktu'),
                DB::raw('COUNT(CASE WHEN status != "Lulus" THEN 1 END) as tidak_lulus'),
                DB::raw('ROUND(AVG(CASE WHEN status = "Lulus" THEN lama_studi END), 1) as rata_studi')
            )->groupBy('fakultas', 'prodi')->get();

            // METODE PAKSA LOG
            $log = new AuditLog();
            $log->user_id = Auth::id();
            $log->ip_address = request()->ip();
            $log->aksi = 'Export Data';
            $log->keterangan = "Pengguna telah melakukan export data kinerja prodi ke file PDF.";
            $log->save();

            $metadata = [
                'fakultas' => $filterFakultas ?? 'Semua Fakultas',
                'prodi'    => $prodiSelected ?? 'Semua Prodi',
                'periode'  => $filterTahun ? 'Angkatan ' . $filterTahun : 'Semua Angkatan',
                'tanggal'  => now()->format('d F Y')
            ];

            $pdf = Pdf::loadView('exports.kinerja_pdf', compact('kinerjaProdi', 'metadata'));
            return $pdf->setPaper('a4', 'landscape')->download('Laporan_Kinerja_Prodi_UNLA.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Export PDF: ' . $e->getMessage());
        }
    }

    public function settings(Request $request)
    {
        $search = $request->search;
        $query = AuditLog::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('aksi', 'like', "%{$search}%")->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $total = Lulusan::where('tahun_lulus', '>', 2000)->count();

        return view('settings', compact('total', 'logs'));
    }

    public function importData(Request $request)
    {
        $request->validate(['file_import' => 'required|file|max:51200']);

        $file = $request->file('file_import');
        $extension = strtolower($file->getClientOriginalExtension());
        $data = [];

        // --- 1. JIKA FORMAT JSON ---
        if ($extension === 'json') {
            $json = file_get_contents($file);
            $dataMentah = json_decode($json, true);

            if (!$dataMentah) return back()->with('error', 'Format JSON tidak valid atau rusak!');
            $data = isset($dataMentah['data']) ? $dataMentah['data'] : $dataMentah;

            // --- 2. JIKA FORMAT CSV ---
        } elseif ($extension === 'csv') {
            $fileHandle = fopen($file->getRealPath(), 'r');

            $firstLine = fgets($fileHandle);
            $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
            rewind($fileHandle);

            $header = fgetcsv($fileHandle, 0, $delimiter);
            $header[0] = preg_replace('/^[\xef\xbb\xbf]+/', '', $header[0]);
            $header = array_map('trim', $header);

            while (($row = fgetcsv($fileHandle, 0, $delimiter)) !== false) {
                if (count($header) === count($row)) {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($fileHandle);

            // --- 3. JIKA FORMAT XLSX / EXCEL ---
        } elseif ($extension === 'xlsx' || $extension === 'xls') {
            try {
                // Load file Excel menggunakan PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(); // Ubah ke array mentah

                if (count($rows) > 0) {
                    $header = array_map('trim', $rows[0]); // Baris pertama dianggap sebagai header kolom

                    // Looping mulai dari baris kedua (index 1) sampai akhir
                    for ($i = 1; $i < count($rows); $i++) {
                        // Pastikan jumlah kolom data sama dengan jumlah kolom header
                        if (count($header) === count($rows[$i])) {
                            $data[] = array_combine($header, $rows[$i]);
                        }
                    }
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
            }
        } else {
            return back()->with('error', 'Format tidak didukung! Harap upload file .json, .csv, atau .xlsx');
        }

        // --- CEK APAKAH DATA KOSONG ---
        if (!is_array($data) || count($data) === 0) {
            return back()->with('error', 'Struktur data tidak dikenali atau file kosong.');
        }

        $jumlahUpdate = 0;
        $jumlahBaru = 0;
        $jumlahDitolak = 0;

        $mapFakultas = [
            'Informatika' => 'Fakultas Teknik',
            'Sistem Informasi' => 'Fakultas Teknik',
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
            'Pendidikan' => 'Fakultas Keguruan dan Ilmu Pendidikan',
            'PGSD' => 'Fakultas Keguruan dan Ilmu Pendidikan'
        ];

        foreach ($data as $row) {
            $nim = $row['nim'] ?? null;
            if (empty($nim)) continue;

            $namaProdi = $row['nama_program_studi'] ?? $row['prodi'] ?? '-';
            $namaMhs = $row['nama_mahasiswa'] ?? $row['nama'] ?? 'Tanpa Nama';

            // FILTER D3 KEPOLISIAN
            if (stripos($namaProdi, 'Kepolisian') !== false) {
                $log = new \App\Models\AuditLog();
                $log->user_id = \Illuminate\Support\Facades\Auth::id();
                $log->ip_address = request()->ip();
                $log->aksi = 'Data Ditolak';
                $log->keterangan = "Import diabaikan: {$namaMhs} (NIM: {$nim}) karena Prodi D3 Kepolisian.";
                $log->save();

                $jumlahDitolak++;
                continue;
            }

            $tahunLulus = $row['tahun_lulus'] ?? null;
            if (!$tahunLulus && !empty($row['tanggal_keluar'])) {
                $tahunLulus = (int) substr($row['tanggal_keluar'], -4);
            }

            $angkatan = $row['angkatan'] ?? null;
            $tglMasuk = $row['tgl_masuk_sp'] ?? null;
            if (!$angkatan && !empty($tglMasuk)) {
                $angkatan = (int) substr($tglMasuk, -4);
            }

            $lamaStudi = 0;
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
                    if ($bulanKeluar >= 7) $lamaStudi += 1;
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

            $fakultas = 'Belum Diatur';
            foreach ($mapFakultas as $kataKunci => $namaFakultas) {
                if (stripos($namaProdi, $kataKunci) !== false) {
                    $fakultas = $namaFakultas;
                    break;
                }
            }

            if (stripos($nim, 'L') !== false) {
                $fakultas = 'Pascasarjana';
            }

            // BERSINIKAN FORMAT IPK DARI KOMA MENJADI TITIK
            $ipkMentah = $row['ipk'] ?? 0;
            if (is_string($ipkMentah)) {
                $ipkMentah = str_replace(',', '.', $ipkMentah);
            }

            // SIMPAN KE DATABASE LULUSAN
            $lulusan = \App\Models\Lulusan::updateOrCreate(
                ['nim' => $nim],
                [
                    'nama' => $namaMhs,
                    'fakultas' => $fakultas,
                    'prodi' => $namaProdi,
                    'angkatan' => $angkatan,
                    'tahun_lulus' => $tahunLulus,
                    'lama_studi' => $lamaStudi,
                    'ipk' => (float) $ipkMentah,
                    'status' => $status,
                    'jenis_daftar' => $row['nm_jns_daftar'] ?? 'Peserta didik baru',
                    'created_by' => \Illuminate\Support\Facades\Auth::id(),
                ]
            );

            // CATAT LOG JIKA ADA DATA BARU
            if ($lulusan->wasRecentlyCreated) {
                $jumlahBaru++;

                $log = new \App\Models\AuditLog();
                $log->user_id = \Illuminate\Support\Facades\Auth::id();
                $log->ip_address = request()->ip();
                $log->aksi = 'Data Baru';
                $log->keterangan = "Import: {$lulusan->nama} (NIM: {$nim})";
                $log->save();
            } else if ($lulusan->wasChanged()) {
                $jumlahUpdate++;
            }
        }

        $pesanTolak = $jumlahDitolak > 0 ? " dan $jumlahDitolak data Kepolisian (D3) diabaikan." : ".";
        return back()->with('success', "Import Selesai! $jumlahUpdate diperbarui, $jumlahBaru data baru masuk{$pesanTolak}");
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}
