<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNLA Graduate Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-8">
            <div class="mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-nowrap items-end gap-4 bg-white p-5 rounded-2xl border shadow-sm overflow-x-auto hide-scrollbar">
                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 min-w-[180px]">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Dari</label>
                        <select name="tahun_mulai" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sort() as $t)
                            <option value="{{ $t }}" {{ request('tahun_mulai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Sampai</label>
                        <select name="tahun_selesai" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5">
                            <option value="">Pilih Tahun</option>
                            @foreach($listTahun->sortDesc() as $t)
                            <option value="{{ $t }}" {{ request('tahun_selesai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-8 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-800 transition-all shadow-lg">
                        Terapkan
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni</p>
                    <h3 class="text-3xl font-black mt-1">{{ number_format($total) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-blue-900">Tepat Waktu</p>
                    @php $persenTepat = ($total > 0) ? ($tepat / $total) * 100 : 0; @endphp
                    <h3 class="text-3xl font-black text-blue-900 mt-1">{{ round($persenTepat, 1) }}%</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-red-500">Terlambat</p>
                    <h3 class="text-3xl font-black text-red-500 mt-1">{{ round(100 - $persenTepat, 1) }}%</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata Studi</p>
                    <h3 class="text-3xl font-black mt-1 text-orange-500">{{ $rataStudi }} <small class="text-xs uppercase">Sem</small></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-black italic">Tren Kelulusan ({{ $tahunMulai }} - {{ $tahunSelesai }})</h4>
                    <div class="h-[320px]"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Proporsi Kelulusan</h4>
                    <div class="w-full max-w-[220px]"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-blue-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-bold text-blue-200 uppercase text-[10px] tracking-widest">
                                🏆 Top 5 Cumlaude: <span id="current-fakultas-label" class="text-white">Fakultas Teknik</span>
                            </h4>
                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></div>
                        </div>
                        <div id="top-cumlaude-list" class="space-y-3 transition-all duration-500">
                            @foreach($topCumlaude as $index => $alumni)
                            <div class="flex items-center justify-between bg-blue-800/40 p-4 rounded-2xl border border-blue-700/50">
                                <div class="flex items-center gap-4">
                                    <span class="w-6 h-6 flex items-center justify-center bg-white text-blue-900 rounded-full font-black text-[10px]">{{ $index + 1 }}</span>
                                    <div>
                                        <h5 class="text-white font-bold text-xs uppercase">{{ $alumni['nama'] }}</h5>
                                        <p class="text-blue-300 text-[9px]">{{ $alumni['prodi'] }}</p>
                                    </div>
                                </div>
                                <span class="text-white font-black text-lg">{{ number_format($alumni['ipk'], 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Persentase Tepat Waktu</h4>
                    <div class="space-y-6">
                        @foreach($dataFakultas as $f)
                        <div>
                            <div class="flex justify-between text-[11px] mb-2 font-black uppercase text-gray-600">
                                <span>{{ $f->fakultas }}</span>
                                <span class="text-blue-900">{{ $f->persentase }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-900" style="width: {{ $f->persentase }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="chart-data"
            data-labels='{!! $tren->pluck("tahun_lulus")->toJson() !!}'
            data-total='{!! $tren->pluck("total")->map(fn($v) => (int)$v)->toJson() !!}'
            data-tepat-tren='{!! $tren->pluck("tepat_waktu")->map(fn($v) => (int)$v)->toJson() !!}'
            data-lambat-tren='{!! $tren->pluck("terlambat")->map(fn($v) => (int)$v)->toJson() !!}'
            data-tepat="{{ $tepat }}"
            data-all="{{ $total }}">
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataEl = document.getElementById('chart-data');
            const labels = JSON.parse(dataEl.getAttribute('data-labels'));
            const totalData = JSON.parse(dataEl.getAttribute('data-total'));
            const tepatData = JSON.parse(dataEl.getAttribute('data-tepat-tren'));
            const lambatData = JSON.parse(dataEl.getAttribute('data-lambat-tren'));
            const tepat = parseInt(dataEl.getAttribute('data-tepat'));
            const all = parseInt(dataEl.getAttribute('data-all'));

            // LINE CHART
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total',
                            data: totalData,
                            borderColor: '#1e3a8a',
                            backgroundColor: 'rgba(30, 58, 138, 0.05)',
                            borderWidth: 4,
                            tension: 0.4,
                            fill: true,
                            pointStyle: 'circle',
                            pointRadius: 4
                        },
                        {
                            label: 'Tepat',
                            data: tepatData,
                            borderColor: '#10b981',
                            borderWidth: 3,
                            tension: 0.4,
                            pointStyle: 'circle',
                            pointRadius: 2
                        },
                        {
                            label: 'Terlambat',
                            data: lambatData,
                            borderColor: '#ef4444',
                            borderWidth: 3,
                            borderDash: [5, 5],
                            tension: 0.4,
                            pointStyle: 'circle',
                            pointRadius: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // PIE CHART
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{
                        data: [tepat, all - tepat],
                        backgroundColor: ['#1e3a8a', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '80%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // ROLLING TOP CUMLAUDE
            let fIndex = 1;
            setInterval(() => {
                const list = document.getElementById('top-cumlaude-list');
                const label = document.getElementById('current-fakultas-label');
                list.style.opacity = '0';
                setTimeout(() => {
                    fetch(`/api/top-cumlaude?index=${fIndex}`)
                        .then(r => r.json())
                        .then(res => {
                            label.innerText = res.fakultas;
                            let html = '';
                            res.data.forEach((a, i) => {
                                html += `<div class="flex items-center justify-between bg-blue-800/40 p-4 rounded-2xl border border-blue-700/50">
                                            <div class="flex items-center gap-4">
                                                <span class="w-6 h-6 flex items-center justify-center bg-white text-blue-900 rounded-full font-black text-[10px]">${i+1}</span>
                                                <div><h5 class="text-white font-bold text-xs uppercase">${a.nama}</h5><p class="text-blue-300 text-[9px]">${a.prodi}</p></div>
                                            </div>
                                            <span class="text-white font-black text-lg">${parseFloat(a.ipk).toFixed(2)}</span>
                                        </div>`;
                            });
                            list.innerHTML = html;
                            list.style.opacity = '1';
                            fIndex++;
                        });
                }, 500);
            }, 10000);
        });
    </script>
</body>

</html>