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
        $latestYearInDb = Lulusan::max('tahun_lulus');
        $filterTahun = $request->tahun ?? $latestYearInDb;
        $prevYear = $filterTahun - 1;

        // --- 1. DATA UTAMA (Sesuai Filter) ---
        $query = Lulusan::query();
        if ($filterFakultas) {
            $query->where('fakultas', $filterFakultas);
        }
        if ($request->tahun) {
            $query->where('tahun_lulus', $filterTahun);
        }

        $total = $query->count();
        $tepat = (clone $query)->where('lama_studi', '<=', 8)->count();
        $rataStudi = round((clone $query)->avg('lama_studi'), 2) ?? 0;

        // --- 2. LOGIKA TREN (Perbandingan Tahun Terpilih vs Tahun Sebelumnya) ---
        $qCurrent = Lulusan::where('tahun_lulus', $filterTahun);
        $qPrev = Lulusan::where('tahun_lulus', $prevYear);

        if ($filterFakultas) {
            $qCurrent->where('fakultas', $filterFakultas);
            $qPrev->where('fakultas', $filterFakultas);
        }

        $totalCur = $qCurrent->count();
        $totalPrev = $qPrev->count();

        // Persentase Kenaikan/Penurunan Total Lulusan
        $trendTotal = ($totalPrev > 0) ? (($totalCur - $totalPrev) / $totalPrev) * 100 : 0;

        // Persentase Ketepatan Waktu (Sekarang vs Lalu)
        $persenTepatCur = ($totalCur > 0) ? ($qCurrent->where('lama_studi', '<=', 8)->count() / $totalCur) * 100 : 0;
        $persenTepatPrev = ($totalPrev > 0) ? ($qPrev->where('lama_studi', '<=', 8)->count() / $totalPrev) * 100 : 0;
        $trendTepat = $persenTepatCur - $persenTepatPrev;

        // --- 3. DATA LAINNYA ---
        $listFakultas = Lulusan::select('fakultas')->distinct()->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')->distinct()->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');

        $tren = Lulusan::select(
            'tahun_lulus',
            DB::raw('count(*) as total'),
            DB::raw('avg(ipk) as avg_ipk'),
            DB::raw('avg(lama_studi) as avg_lama_studi')
        )
            ->where('tahun_lulus', '>=', 2016) // <--- Ubah bagian ini jadi 2016
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'asc')
            ->get();

        $dataFakultas = Lulusan::select('fakultas', DB::raw('round((count(CASE WHEN lama_studi <= 8 THEN 1 END) / count(*)) * 100) as persentase'))
            ->groupBy('fakultas')
            ->get();

        $queryTop = Lulusan::query();
        if ($request->fakultas) {
            $queryTop->where('fakultas', $request->fakultas);
        }
        if ($request->tahun) {
            $queryTop->where('tahun_lulus', $request->tahun);
        }

        $topCumlaude = $queryTop->orderBy('ipk', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'nama' => $item->nama,
                    'fakultas' => $item->fakultas,
                    'prodi' => $item->prodi,
                    'ipk' => number_format($item->ipk, 2)
                ];
            });

        return view('dashboard', compact('tren', 'total', 'tepat', 'rataStudi', 'listFakultas', 'listTahun', 'dataFakultas', 'topCumlaude', 'trendTotal', 'trendTepat'));
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
        $listTahun = Lulusan::select('tahun_lulus')->distinct()->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');

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
        $prodiSelected = $request->prodi ?? 'Informatika';
        $tahunSelected = $request->tahun;

        // Base Query
        $query = Lulusan::where('prodi', $prodiSelected);

        // Tambahkan filter tahun jika dipilih
        if ($tahunSelected) {
            $query->where('tahun_lulus', $tahunSelected);
        }

        // 1. Statistik
        $stats = [
            'total' => (clone $query)->count(),
            'avg_ipk' => (clone $query)->avg('ipk') ?? 0,
            'avg_lama_studi' => (clone $query)->avg('lama_studi') ?? 0,
            'tepat_waktu' => (clone $query)->where('lama_studi', '<=', 8)->count(),
        ];

        // 2. List untuk Dropdown
        $listProdi = Lulusan::select('prodi')->distinct()->orderBy('prodi', 'asc')->pluck('prodi');
        $listTahun = Lulusan::select('tahun_lulus')
            ->where('tahun_lulus', '>', 2000)
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        // 3. Tren (Tetap ambil per prodi tanpa filter tahun agar grafiknya tetap panjang)
        // Di dalam DashboardController.php bagian analisisProdi
        $tren = Lulusan::select(
            'tahun_lulus',
            DB::raw('count(*) as total'),
            DB::raw('avg(ipk) as avg_ipk'),
            DB::raw('avg(lama_studi) as avg_lama_studi')
        )
            ->where('prodi', $prodiSelected)
            ->where('tahun_lulus', '>', 2000)
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus', 'asc')
            ->get();

        // 4. Distribusi IPK (Ikut filter tahun)
        $ipkDist = [
            'cumlaude' => (clone $query)->where('ipk', '>=', 3.5)->count(),
            'sangat_memuaskan' => (clone $query)->where('ipk', '>=', 3.0)->where('ipk', '<', 3.5)->count(),
            'memuaskan' => (clone $query)->where('ipk', '<', 3.0)->count(),
        ];
        $topLulusan = (clone $query)
            ->orderBy('ipk', 'desc')
            ->orderBy('lama_studi', 'asc') // Jika IPK sama, yang lebih cepat lulus di atas
            ->take(5)
            ->get();

        return view('analisis_prodi', compact('stats', 'tren', 'ipkDist', 'listProdi', 'listTahun', 'prodiSelected', 'topLulusan'));
    }
}
