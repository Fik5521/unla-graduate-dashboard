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
        // Kita buat query khusus untuk membandingkan YoY (Year on Year)
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

        // --- 3. DATA LAINNYA (Slider, Chart, dll tetap sama) ---
        $listFakultas = Lulusan::select('fakultas')->distinct()->pluck('fakultas');
        $listTahun = Lulusan::select('tahun_lulus')->distinct()->orderBy('tahun_lulus', 'desc')->pluck('tahun_lulus');
        $tren = Lulusan::select('tahun_lulus', DB::raw('count(*) as total'))
            ->where('tahun_lulus', '>=', 2016) // TAMBAHKAN INI
            ->groupBy('tahun_lulus')
            ->orderBy('tahun_lulus')
            ->get();
        $dataFakultas = Lulusan::select('fakultas', DB::raw('round((count(CASE WHEN lama_studi <= 8 THEN 1 END) / count(*)) * 100) as persentase'))->groupBy('fakultas')->get();

        $topPerFakultas = [];
        foreach ($listFakultas as $fak) {
            $best = Lulusan::where('fakultas', $fak)->orderBy('ipk', 'desc')->first();
            if ($best) {
                $topPerFakultas[] = ['fakultas' => $fak, 'nama' => $best->nama, 'prodi' => $best->prodi, 'ipk' => number_format($best->ipk, 2)];
            }
        }

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

        return view('dashboard', compact(
            'total',
            'tepat',
            'rataStudi',
            'tren',
            'dataFakultas',
            'listFakultas',
            'listTahun',
            'topCumlaude',
            'trendTotal',
            'trendTepat'
        ));
    }

    public function mahasiswa(Request $request)
    {
        $search = $request->search;

        // Ambil data dengan pencarian NIM atau Nama, lalu bagi 15 data per halaman
        $mahasiswas = Lulusan::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nim', 'like', "%{$search}%");
        })->paginate(15);

        return view('mahasiswa', compact('mahasiswas'));
    }
    public function getTopCumlaude(Request $request)
    {
        // Ambil semua daftar fakultas yang ada di UNLA
        $allFakultas = Lulusan::select('fakultas')->distinct()->pluck('fakultas')->toArray();

        // Ambil index fakultas yang sedang diminta (dikirim dari JS)
        $currentIndex = $request->get('index', 0) % count($allFakultas);
        $targetFakultas = $allFakultas[$currentIndex];

        // Ambil Top 5 untuk fakultas spesifik tersebut
        $top = Lulusan::where('fakultas', $targetFakultas)
            ->orderBy('ipk', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'fakultas' => $targetFakultas,
            'data' => $top
        ]);
    }

    public function exportPdf(Request $request)
    {
        try {
            // Tingkatkan kekuatan server sementara
            ini_set('memory_limit', '1024M');
            set_time_limit(600);

            $query = Lulusan::query();
            if ($request->fakultas) {
                $query->where('fakultas', $request->fakultas);
            }
            if ($request->tahun) {
                $query->where('tahun_lulus', $request->tahun);
            }

            // COBA LIMIT 100 DATA DULU untuk testing
            $data = $query->orderBy('nama', 'asc')->limit(100)->get();

            if ($data->isEmpty()) {
                return "Data kosong, tidak ada yang bisa di-export.";
            }

            $metadata = [
                'fakultas' => $request->fakultas ?? 'Semua Fakultas',
                'tahun' => $request->tahun ?? 'Semua Tahun',
                'total' => $data->count(),
                'tanggal' => now()->format('d F Y')
            ];

            // Pakai full path class untuk menghindari 'Class not found'
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.lulusan_pdf', compact('data', 'metadata'));

            return $pdf->setPaper('a4', 'portrait')->download('Laporan_Lulusan_UNLA.pdf');
        } catch (\Exception $e) {
            // Jika error, tampilkan pesan errornya di layar daripada Error 500
            return "Gagal Export: " . $e->getMessage();
        }
    }
}
