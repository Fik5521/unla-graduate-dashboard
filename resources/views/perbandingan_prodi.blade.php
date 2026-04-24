<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNLA - Perbandingan 2 Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        select { max-width: 160px; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">
    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-8">
            <div class="mb-8">
                <h2 class="text-xl font-black text-blue-900 uppercase italic mb-4">Head-to-Head Comparison</h2>
                
                <form action="{{ route('perbandingan.prodi') }}" method="GET" class="flex flex-nowrap items-end gap-3 bg-white p-5 rounded-2xl border shadow-sm overflow-x-auto hide-scrollbar">
                    
                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Dari Tahun</label>
                        <select name="tahun_mulai" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900 cursor-pointer">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sort() as $t)
                                <option value="{{ $t }}" {{ request('tahun_mulai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Sampai Tahun</label>
                        <select name="tahun_selesai" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900 cursor-pointer">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sortDesc() as $t)
                                <option value="{{ $t }}" {{ request('tahun_selesai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Prodi A</label>
                        <select name="prodi_a" class="text-[11px] font-bold bg-blue-50 text-blue-900 border-none rounded-xl px-3 py-2.5">
                            @foreach($listProdi as $p)
                                <option value="{{ $p }}" {{ $prodiA == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 pb-3">
                        <span class="text-xs font-black text-gray-300 italic px-1">VS</span>
                    </div>

                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-black text-gray-400 uppercase mb-1 ml-1 tracking-widest">Prodi B</label>
                        <select name="prodi_b" class="text-[11px] font-bold bg-red-50 text-red-900 border-none rounded-xl px-3 py-2.5">
                            @foreach($listProdi as $p)
                                <option value="{{ $p }}" {{ $prodiB == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                        <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-800 transition-all shadow-lg">
                            Bandingkan
                        </button>

                        @if(request()->anyFilled(['tahun_mulai', 'tahun_selesai', 'prodi_a']))
                        <a href="{{ route('perbandingan.prodi') }}" class="px-4 py-2.5 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-xl border border-red-100 hover:bg-red-100 transition-all flex items-center gap-1.5" title="Reset">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-[2.5rem] border shadow-sm flex flex-col items-center">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase mb-6 tracking-widest text-center">Total Alumni<br><span class="text-blue-900 italic">({{ request('tahun_mulai') ?? 'Semua' }} - {{ request('tahun_selesai') ?? 'Semua' }})</span></h4>
                    <div class="h-[250px] w-full"><canvas id="chartTotal"></canvas></div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border shadow-sm flex flex-col items-center">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase mb-6 tracking-widest text-center">Rata-rata IPK<br><span class="text-blue-900 italic">({{ request('tahun_mulai') ?? 'Semua' }} - {{ request('tahun_selesai') ?? 'Semua' }})</span></h4>
                    <div class="h-[250px] w-full"><canvas id="chartIpk"></canvas></div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border shadow-sm flex flex-col items-center">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase mb-6 tracking-widest text-center">Lama Studi (Sem)<br><span class="text-blue-900 italic">({{ request('tahun_mulai') ?? 'Semua' }} - {{ request('tahun_selesai') ?? 'Semua' }})</span></h4>
                    <div class="h-[250px] w-full"><canvas id="chartStudi"></canvas></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = ['{{ $prodiA }}', '{{ $prodiB }}'];
            const colors = ['#1e3a8a', '#ef4444']; 

            const config = (data) => ({
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderRadius: 12,
                        barThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                        x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' } } }
                    }
                }
            });

            new Chart(document.getElementById('chartTotal'), config([{{ $dataA->total ?? 0 }}, {{ $dataB->total ?? 0 }}]));
            new Chart(document.getElementById('chartIpk'), config([{{ $dataA->ipk ?? 0 }}, {{ $dataB->ipk ?? 0 }}]));
            new Chart(document.getElementById('chartStudi'), config([{{ $dataA->studi ?? 0 }}, {{ $dataB->studi ?? 0 }}]));
        });
    </script>
</body>
</html>