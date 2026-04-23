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
                <form action="{{ route('dashboard') }}" method="GET" class="relative flex flex-wrap items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="filterFakultas" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[200px]">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Tahun Lulus</label>
                        <select name="tahun" id="filterTahun" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[150px]">
                            <option value="">Semua Tahun</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="mt-4 px-8 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-100">
                        Terapkan Filter
                    </button>

                    <div class="absolute right-5 top-1/2 -translate-y-1/2 flex items-center">
                        <a href="{{ route('dashboard.export', request()->all()) }}"
                            class="{{ !(request('fakultas') || request('tahun')) ? 'opacity-20 cursor-not-allowed pointer-events-none' : '' }} p-3 bg-red-50 text-red-600 rounded-xl border border-red-100 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni</p>
                    <h3 class="text-3xl font-black mt-1">{{ number_format($total) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tepat Waktu</p>
                    @php $persenTepat = ($total > 0) ? ($tepat / $total) * 100 : 0; @endphp
                    <h3 class="text-3xl font-black text-blue-900 mt-1">{{ round($persenTepat, 1) }}%</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Terlambat</p>
                    <h3 class="text-3xl font-black text-red-500 mt-1">{{ round(100 - $persenTepat, 1) }}%</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata Studi</p>
                    <h3 class="text-3xl font-black mt-1">{{ $rataStudi }} <small class="text-xs uppercase">Sem</small></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-black">Tren Kelulusan & Performa</h4>
                    <div class="h-[320px]"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Proporsi Ketepatan Lulus</h4>
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
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Statistik Per Fakultas</h4>
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
            data-labels='{{ $tren->pluck("tahun_lulus")->toJson() }}'
            data-total='{{ $tren->pluck("total")->toJson() }}'
            data-ipk='{{ $tren->pluck("avg_ipk")->map(fn($v) => round($v, 2))->toJson() }}'
            data-studi='{{ $tren->pluck("avg_lama_studi")->map(fn($v) => round($v, 1))->toJson() }}'
            data-tepat="{{ $tepat }}"
            data-all="{{ $total }}">
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataEl = document.getElementById('chart-data');
            const labels = JSON.parse(dataEl.getAttribute('data-labels'));
            const totalData = JSON.parse(dataEl.getAttribute('data-total'));
            const ipkData = JSON.parse(dataEl.getAttribute('data-ipk'));
            const studiData = JSON.parse(dataEl.getAttribute('data-studi'));
            const tepat = parseInt(dataEl.getAttribute('data-tepat'));
            const all = parseInt(dataEl.getAttribute('data-all'));

            // LINE CHART (3 GARIS)
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total Lulusan',
                            data: totalData,
                            borderColor: '#000000', // Hitam Legam
                            backgroundColor: 'rgba(0, 0, 0, 0.05)',
                            borderWidth: 4,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Avg IPK',
                            data: ipkData,
                            borderColor: '#10b981', // Hijau
                            borderWidth: 3,
                            tension: 0.4,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Avg Studi',
                            data: studiData,
                            borderColor: '#f59e0b', // Oranye
                            borderWidth: 3,
                            borderDash: [5, 5],
                            tension: 0.4,
                            yAxisID: 'y1'
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
                                boxWidth: 10,
                                font: {
                                    weight: 'bold',
                                    size: 10
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Jumlah Alumni'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: 'IPK / Semester'
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
                        backgroundColor: ['#1e3a8a', '#f87171'],
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

            // ROLLING LOGIC (Simplified)
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