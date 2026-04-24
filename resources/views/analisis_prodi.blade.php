<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNLA Graduate - Analisis Prodi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        select { max-width: 170px; }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-8">
            <div class="mb-8">
                <form action="{{ route('analisis.prodi') }}" method="GET" id="filterForm" class="flex flex-nowrap items-end gap-3 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm overflow-x-auto hide-scrollbar">
                    
                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="filterFakultas" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900" onchange="updateProdiOptions()">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($listFakultas as $f)
                                <option value="{{ $f }}" {{ $fakultasSelected == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Prodi</label>
                        <select name="prodi" id="filterProdi" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900 disabled:opacity-50 disabled:cursor-not-allowed" {{ !$fakultasSelected ? 'disabled' : '' }}>
                            <option value="">-- Pilih Prodi --</option>
                        </select>
                    </div>

                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Dari</label>
                        <select name="tahun_mulai" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sort() as $t)
                                <option value="{{ $t }}" {{ request('tahun_mulai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-shrink-0 flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Sampai</label>
                        <select name="tahun_selesai" class="text-[11px] font-bold bg-gray-50 border-none rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-blue-900">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sortDesc() as $t)
                                <option value="{{ $t }}" {{ request('tahun_selesai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                        <button type="submit" class="px-6 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-100">
                            Analisis
                        </button>

                        @if(request('fakultas') || request('tahun_mulai'))
                        <a href="{{ route('analisis.prodi') }}" class="px-4 py-2.5 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-xl border border-red-100 hover:bg-red-100 transition-all flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                            Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni</p>
                    <h3 class="text-3xl font-black text-blue-900 mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-green-600">Avg IPK</p>
                    <h3 class="text-3xl font-black mt-1">{{ number_format($stats['avg_ipk'], 2) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-orange-500">Avg Studi</p>
                    <h3 class="text-3xl font-black mt-1">{{ number_format($stats['avg_lama_studi'], 1) }} <small class="text-xs uppercase">Sem</small></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-purple-600">Tepat Waktu</p>
                    <h3 class="text-3xl font-black mt-1">{{ $stats['tepat_waktu'] }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">
                        Tren Performa Prodi ({{ request('tahun_mulai') ?? 'Semua' }} - {{ request('tahun_selesai') ?? 'Semua' }})
                    </h4>
                    <div class="h-[350px]"><canvas id="lineChart"></canvas></div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Distribusi Predikat</h4>
                    <div class="w-full max-w-[240px] mt-10"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                        <h4 class="font-bold text-gray-400 uppercase text-[10px] tracking-[0.3em] italic">Best Graduates: {{ $prodiSelected ?? '-' }}</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="text-[9px] uppercase font-black text-gray-400 tracking-widest border-b">
                                    <th class="py-4 px-8">Rank</th>
                                    <th class="py-4 px-6">Nama</th>
                                    <th class="py-4 px-6 text-center">Tahun</th>
                                    <th class="py-4 px-8 text-right">IPK</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topLulusan as $index => $top)
                                <tr class="hover:bg-blue-50/30 transition-all group">
                                    <td class="py-4 px-8"><div class="w-6 h-6 rounded-lg flex items-center justify-center font-black text-[10px] bg-blue-50 text-blue-900">{{ $index + 1 }}</div></td>
                                    <td class="py-4 px-6 font-black uppercase">{{ $top->nama }}</td>
                                    <td class="py-4 px-6 text-center font-bold text-gray-500">{{ $top->tahun_lulus }}</td>
                                    <td class="py-4 px-8 text-right"><span class="font-black text-blue-900 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">⭐ {{ number_format($top->ipk, 2) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Efisiensi Kelulusan</h4>
                    <div class="flex-1 min-h-[300px]"><canvas id="barEfficiencyChart"></canvas></div>
                </div>
            </div>
        </div>

        <div id="chart-data"
            data-labels='{!! $tren->pluck("tahun_lulus")->toJson() !!}'
            data-total='{!! $tren->pluck("total")->toJson() !!}'
            data-ipk-line='{!! $tren->pluck("avg_ipk")->map(fn($v) => round($v, 2))->toJson() !!}'
            data-studi-line='{!! $tren->pluck("avg_lama_studi")->map(fn($v) => round($v, 1))->toJson() !!}'
            data-ipk-dist='{!! json_encode($ipkDist) !!}'
            data-tepat="{{ $stats['tepat_waktu'] }}"
            data-total-stats="{{ $stats['total'] }}">
        </div>
    </main>

    <script>
        const prodiPerFakultas = @json($prodiPerFakultas);
        const selectedProdi = "{{ $prodiSelected }}";

        function updateProdiOptions() {
            const fSelect = document.getElementById('filterFakultas');
            const pSelect = document.getElementById('filterProdi');
            const selectedF = fSelect.value;
            pSelect.innerHTML = '<option value="">-- Pilih Prodi --</option>';

            if (selectedF && prodiPerFakultas[selectedF]) {
                pSelect.disabled = false;
                prodiPerFakultas[selectedF].forEach(i => {
                    const o = document.createElement('option');
                    o.value = i.prodi; o.text = i.prodi;
                    if (i.prodi === selectedProdi) o.selected = true;
                    pSelect.appendChild(o);
                });
            } else { pSelect.disabled = true; }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateProdiOptions();
            const dataEl = document.getElementById('chart-data');
            const labels = JSON.parse(dataEl.getAttribute('data-labels'));
            const totalData = JSON.parse(dataEl.getAttribute('data-total'));
            const ipkLineData = JSON.parse(dataEl.getAttribute('data-ipk-line'));
            const studiLineData = JSON.parse(dataEl.getAttribute('data-studi-line'));
            const ipkDist = JSON.parse(dataEl.getAttribute('data-ipk-dist'));
            const tepat = parseInt(dataEl.getAttribute('data-tepat'));
            const totalAlumni = parseInt(dataEl.getAttribute('data-total-stats'));

            // LINE CHART
            new Chart(document.getElementById('lineChart'), {
                type: 'line', data: {
                    labels: labels,
                    datasets: [
                        { label: 'Total', data: totalData, borderColor: '#1e3a8a', backgroundColor: 'rgba(30, 58, 138, 0.1)', tension: 0.3, fill: true, yAxisID: 'y', pointStyle: 'circle', pointRadius: 5 },
                        { label: 'Avg IPK', data: ipkLineData, borderColor: '#10b981', tension: 0.3, yAxisID: 'y1', pointStyle: 'circle', pointRadius: 5 },
                        { label: 'Avg Studi', data: studiLineData, borderColor: '#f59e0b', tension: 0.3, yAxisID: 'y1', borderDash: [5, 5], pointStyle: 'circle', pointRadius: 5 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top', labels: { usePointStyle: true, font: { weight: 'bold' } } } }, scales: { y: { position: 'left' }, y1: { position: 'right', grid: { display: false } } } }
            });

            // PIE CHART
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut', data: {
                    labels: ['Cumlaude', 'Sangat Memuaskan', 'Memuaskan'],
                    datasets: [{ data: [ipkDist.cumlaude || 0, ipkDist.sangat_memuaskan || 0, ipkDist.memuaskan || 0], backgroundColor: ['#1e3a8a', '#3b82f6', '#93c5fd'], borderWidth: 0 }]
                },
                options: { cutout: '75%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } } }
            });

            // BAR CHART
            new Chart(document.getElementById('barEfficiencyChart'), {
                type: 'bar', data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{ data: [tepat, totalAlumni - tepat], backgroundColor: ['#10b981', '#ef4444'], borderRadius: 8, barThickness: 40 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        });
    </script>
</body>
</html>