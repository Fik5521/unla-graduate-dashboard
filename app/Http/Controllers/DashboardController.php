<?php

namespace App\Http\Controllers;

use App\Models\Lulusan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filterFakultas = $request->fakultas;

        // --- PERBAIKAN DI SINI: Filter tahun sampah saat ambil Min/Max ---
        $minYearDb = Lulusan::where('tahun_lulus', '>', 2000)->min('tahun_lulus') ?? 2016;
        $maxYearDb = Lulusan::where('tahun_lulus', '>', 2000)->max('tahun_lulus') ?? date('Y');

        // Ambil input dari user atau gunakan default yang sudah bersih
        $tahunMulai = $request->tahun_mulai ?? $minYearDb;
        $tahunSelesai = $request->tahun_selesai ?? $maxYearDb;

        // --- 1. DATA UTAMA (Card) ---
        $query = Lulusan::query();

        // Pastikan query utama juga membuang tahun sampah agar kalkulasi IPK/Total akurat
        $query->where('tahun_lulus', '>', 2000);

        if ($filterFakultas) {
            $query->where('fakultas', $filterFakultas);
        }

        // Gunakan whereBetween untuk rentang tahun
        $query->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai]);

        $total = $query->count();
        $tepat = (clone $query)->where('lama_studi', '<=', 8)->count();
        $rataStudi = round((clone $query)->avg('lama_studi'), 1) ?? 0;

        // --- 2. DATA TREN (Grafik Line) ---
        $tren = Lulusan::select(
            'tahun_lulus',
            DB::raw('count(*) as total'),
            DB::raw('count(CASE WHEN lama_studi <= 8 THEN 1 END) as tepat_waktu'),
            DB::raw('count(CASE WHEN lama_studi > 8 THEN 1 END) as terlambat')
        )
            ->where('tahun_lulus', '>', 2000) // Buang tahun sampah
            ->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai]);

        if ($filterFakultas) {
            $tren->where('fakultas', $filterFakultas);
        }

        $tren = $tren->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'asc')
            ->get();

        // Data dropdown
        $listFakultas = Lulusan::select('fakultas')->distinct()->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000)
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        // Data Statistik Fakultas
        $dataFakultas = Lulusan::select(
            'fakultas',
            DB::raw('round((count(CASE WHEN lama_studi <= 8 THEN 1 END) / count(*)) * 100) as persentase')
        )
            ->where('tahun_lulus', '>', 2000) // Buang tahun sampah
            ->groupBy('fakultas')
            ->get();

        $topCumlaude = (clone $query)->orderBy('ipk', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'tren',
            'total',
            'tepat',
            'rataStudi',
            'listFakultas',
            'listTahun',
            'dataFakultas',
            'topCumlaude',
            'tahunMulai',
            'tahunSelesai'
        ));
    }

    /**
     * Menampilkan Halaman List Mahasiswa dengan Dependent Dropdown Filter
     */
    public function mahasiswa(Request $request)
    {
        $search = $request->search;
        $filterFakultas = $request->fakultas;
        $filterProdi = $request->prodi;
        $filterTahun = $request->tahun;

        $query = Lulusan::query();

        // 1. Logika Pencarian Nama atau NIM
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        });

        // 2. Filter Berdasarkan Fakultas
        $query->when($filterFakultas, function ($q) use ($filterFakultas) {
            $q->where('fakultas', $filterFakultas);
        });

        // 3. Filter Berdasarkan Program Studi
        $query->when($filterProdi, function ($q) use ($filterProdi) {
            $q->where('prodi', $filterProdi);
        });

        // 4. Filter Berdasarkan Tahun Lulus
        $query->when($filterTahun, function ($q) use ($filterTahun) {
            $q->where('tahun_lulus', $filterTahun);
        });

        // Eksekusi data dengan paginasi dan mempertahankan query string di URL
        $mahasiswas = $query->orderBy('tahun_lulus', 'desc')
            ->orderBy('nama', 'asc')
            ->paginate(15)
            ->appends($request->all());

        // Menyiapkan data untuk Dropdown Filter
        $listFakultas = Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000) // Memastikan hanya tahun 2000 ke atas yang muncul
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');
        // Mengelompokkan Prodi berdasarkan Fakultas untuk keperluan Dependent Dropdown di View
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')
            ->distinct()
            ->orderBy('prodi')
            ->get()
            ->groupBy('fakultas');

        return view('mahasiswa', compact('mahasiswas', 'listFakultas', 'listTahun', 'prodiPerFakultas'));
    }

    /**
     * API untuk Slider Top Cumlaude di Dashboard (Rotasi Otomatis)
     */
    public function getTopCumlaude(Request $request)
    {
        $allFakultas = Lulusan::select('fakultas')->distinct()->pluck('fakultas')->toArray();
        if (empty($allFakultas)) return response()->json(['data' => []]);

        $currentIndex = $request->get('index', 0) % count($allFakultas);
        $targetFakultas = $allFakultas[$currentIndex];

        $top = Lulusan::where('fakultas', $targetFakultas)
            ->orderBy('ipk', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'fakultas' => $targetFakultas,
            'data' => $top
        ]);
    }

    /**
     * Fitur Export ke PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $query = Lulusan::query();
            if ($request->fakultas) $query->where('fakultas', $request->fakultas);
            if ($request->tahun) $query->where('tahun_lulus', $request->tahun);

            // Limit data untuk mencegah overload memori saat render PDF
            $data = $query->orderBy('nama', 'asc')->limit(100)->get();

            if ($data->isEmpty()) {
                return "Data tidak ditemukan untuk kriteria filter tersebut.";
            }

            $metadata = [
                'fakultas' => $request->fakultas ?? 'Semua Fakultas',
                'tahun' => $request->tahun ?? 'Semua Tahun',
                'total' => $data->count(),
                'tanggal' => now()->format('d F Y')
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.lulusan_pdf', compact('data', 'metadata'));
            return $pdf->setPaper('a4', 'portrait')->download('Laporan_Lulusan_UNLA.pdf');
        } catch (\Exception $e) {
            return "Terjadi Kesalahan saat Export: " . $e->getMessage();
        }
    }
    public function analisisProdi(Request $request)
    {
        $fakultasSelected = $request->fakultas;
        $prodiSelected = $request->prodi;
        $tahunSelected = $request->tahun;

        // --- 1. Query Utama (Untuk Stats Card & Tabel) ---
        $query = Lulusan::query();
        if ($fakultasSelected) $query->where('fakultas', $fakultasSelected);
        if ($prodiSelected) $query->where('prodi', $prodiSelected);

        // Simpan query dasar tanpa filter tahun untuk digunakan di Tren
        $queryBaseForTren = clone $query;

        // Filter tahun hanya berlaku untuk angka di Card & Tabel
        if ($tahunSelected) $query->where('tahun_lulus', $tahunSelected);

        // --- 2. Statistik Card ---
        $stats = [
            'total' => $query->count(),
            'avg_ipk' => round($query->avg('ipk') ?? 0, 2),
            'avg_lama_studi' => round($query->avg('lama_studi') ?? 0, 1),
            'tepat_waktu' => $query->where('lama_studi', '<=', 8)->count(),
        ];

        // --- 3. Perbaikan Query Tren (Grafik Line) ---
        // Kita gunakan $queryBaseForTren supaya grafik tetap panjang walau tahun dipilih
        // Ambil input range tahun
        $mulai = $request->tahun_mulai ?? Lulusan::min('tahun_lulus');
        $selesai = $request->tahun_selesai ?? Lulusan::max('tahun_lulus');

        // Query Tren harus pakai whereBetween
        $tren = Lulusan::query()
            ->when($prodiSelected, fn($q) => $q->where('prodi', $prodiSelected))
            ->whereBetween('tahun_lulus', [$mulai, $selesai]) // <-- Kunci sinkronisasi grafik
            ->select(
                'tahun_lulus',
                DB::raw('count(*) as total'),
                DB::raw('avg(ipk) as avg_ipk'),
                DB::raw('avg(lama_studi) as avg_lama_studi')
            )
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'asc')
            ->get();

        // --- 4. Data Filter & Pendukung ---
        $listFakultas = Lulusan::select('fakultas')->distinct()->orderBy('fakultas')->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000) // Ini akan membuang tahun 202 dan 1970
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');
        $prodiPerFakultas = Lulusan::select('fakultas', 'prodi')->distinct()->orderBy('prodi')->get()->groupBy('fakultas');

        $ipkDist = [
            'cumlaude' => (clone $query)->where('ipk', '>=', 3.5)->count(),
            'sangat_memuaskan' => (clone $query)->where('ipk', '>=', 3.0)->where('ipk', '<', 3.5)->count(),
            'memuaskan' => (clone $query)->where('ipk', '<', 3.0)->count(),
        ];

        $topLulusan = (clone $query)->orderBy('ipk', 'desc')->take(5)->get();

        return view('analisis_prodi', compact(
            'stats',
            'tren',
            'ipkDist',
            'listFakultas',
            'listTahun',
            'fakultasSelected',
            'prodiSelected',
            'tahunSelected',
            'prodiPerFakultas',
            'topLulusan'
        ));
    }

    public function perbandinganProdi(Request $request)
    {
        // 1. Ambil input rentang tahun atau set default jika kosong
        $minYear = Lulusan::min('tahun_lulus') ?? 2016;
        $maxYear = Lulusan::max('tahun_lulus') ?? date('Y');

        $tahunMulai = $request->tahun_mulai ?? $minYear;
        $tahunSelesai = $request->tahun_selesai ?? $maxYear;

        // 2. Default prodi untuk dibandingkan
        $prodiA = $request->prodi_a ?? 'Informatika';
        $prodiB = $request->prodi_b ?? 'Sistem Informasi';

        // 3. Query Data Prodi A dengan FILTER RENTANG TAHUN
        $dataA = Lulusan::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('ROUND(AVG(ipk), 2) as ipk'),
            DB::raw('ROUND(AVG(lama_studi), 1) as studi')
        )
            ->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai]) // <--- KUNCI FILTER
            ->where('prodi', $prodiA)
            ->first();

        // 4. Query Data Prodi B dengan FILTER RENTANG TAHUN
        $dataB = Lulusan::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('ROUND(AVG(ipk), 2) as ipk'),
            DB::raw('ROUND(AVG(lama_studi), 1) as studi')
        )
            ->whereBetween('tahun_lulus', [$tahunMulai, $tahunSelesai]) // <--- KUNCI FILTER
            ->where('prodi', $prodiB)
            ->first();

        // 5. Data untuk Dropdown
        $listProdi = Lulusan::select('prodi')->distinct()->orderBy('prodi')->pluck('prodi');
        $listTahun = Lulusan::select('tahun_lulus')
            ->distinct()
            ->where('tahun_lulus', '>', 2000) // Ini akan membuang tahun 202 dan 1970
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');
        return view('perbandingan_prodi', compact(
            'dataA',
            'dataB',
            'prodiA',
            'prodiB',
            'listProdi',
            'listTahun',
            'tahunMulai',
            'tahunSelesai'
        ));
    }
}
