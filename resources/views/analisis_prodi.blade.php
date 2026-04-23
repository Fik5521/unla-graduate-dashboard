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
                <form action="{{ route('analisis.prodi') }}" method="GET" class="relative flex items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="filterProdi" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[220px]" onchange="this.form.submit()">
                            @foreach($listProdi as $p)
                            <option value="{{ $p }}" {{ $prodiSelected == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Tahun Lulus</label>
                        <select name="tahun" id="filterTahun" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[120px]" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="absolute right-5 top-1/2 -translate-y-1/2">
                        <a href="{{ route('analisis.prodi') }}" class="flex items-center gap-2 p-3 bg-red-50 text-red-600 rounded-xl transition-all border border-red-100 shadow-sm hover:bg-red-100" title="Reset Filters">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="text-[10px] font-bold uppercase">Reset</span>
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni</p>
                    <h3 class="text-3xl font-black text-blue-900 mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata IPK</p>
                    <h3 class="text-3xl font-black text-green-600 mt-1">{{ number_format($stats['avg_ipk'], 2) }}</h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata Studi</p>
                    <h3 class="text-3xl font-black text-orange-500 mt-1">{{ number_format($stats['avg_lama_studi'], 1) }} <small class="text-xs uppercase">Sem</small></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tepat Waktu</p>
                    <h3 class="text-3xl font-black text-purple-600 mt-1">{{ $stats['tepat_waktu'] }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Tren Performa & Kelulusan</h4>
                    <div class="h-[350px]"><canvas id="lineChart"></canvas></div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Distribusi Predikat</h4>
                    <div class="flex-1 flex items-center justify-center">
                        <div class="w-full max-w-[240px]"><canvas id="pieChart"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <div>
                        <h4 class="font-bold text-gray-400 uppercase text-[10px] tracking-[0.3em] italic">Top 5 Best Graduates</h4>
                        <p class="text-[11px] text-blue-900 font-bold mt-1">Lulusan Terbaik Prodi {{ $prodiSelected }}</p>
                    </div>
                    <span class="px-4 py-1 bg-blue-900 text-white text-[9px] font-black uppercase rounded-full">Academic Excellence</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[9px] uppercase font-black text-gray-400 tracking-widest border-b border-gray-50">
                                <th class="py-4 px-8">Rank</th>
                                <th class="py-4 px-6">Nama Mahasiswa</th>
                                <th class="py-4 px-6 text-center">Tahun Lulus</th>
                                <th class="py-4 px-6 text-center">Lama Studi</th>
                                <th class="py-4 px-8 text-right">IPK</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($topLulusan as $index => $top)
                            <tr class="hover:bg-blue-50/30 transition-all group">
                                <td class="py-4 px-8">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center font-black text-[10px] 
                                        {{ $index == 0 ? 'bg-yellow-100 text-yellow-700' : ($index == 1 ? 'bg-gray-100 text-gray-600' : ($index == 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-50 text-blue-900')) }}">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-black text-gray-800 uppercase tracking-tight group-hover:text-blue-900">{{ $top->nama }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $top->nim }}</p>
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-gray-500">{{ $top->tahun_lulus }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[10px] font-bold text-gray-600 bg-gray-100 px-2 py-1 rounded-md">{{ $top->lama_studi }} Sem</span>
                                </td>
                                <td class="py-4 px-8 text-right">
                                    <span class="font-black text-blue-900 bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100">
                                        ⭐ {{ number_format($top->ipk, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="chart-data"
            data-labels='{{ $tren->pluck("tahun_lulus")->toJson() }}'
            data-total='{{ $tren->pluck("total")->toJson() }}'
            data-ipk-line='{{ $tren->pluck("avg_ipk")->map(fn($v) => round($v, 2))->toJson() }}'
            data-studi-line='{{ $tren->pluck("avg_lama_studi")->map(fn($v) => round($v, 1))->toJson() }}'
            data-ipk-dist='{{ json_encode($ipkDist) }}'>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataEl = document.getElementById('chart-data');
            if (!dataEl) return;

            const labels = JSON.parse(dataEl.getAttribute('data-labels'));
            const totalData = JSON.parse(dataEl.getAttribute('data-total'));
            const ipkLineData = JSON.parse(dataEl.getAttribute('data-ipk-line'));
            const studiLineData = JSON.parse(dataEl.getAttribute('data-studi-line'));
            const ipkDist = JSON.parse(dataEl.getAttribute('data-ipk-dist'));

            // --- 1. LINE CHART (3 DATASETS) ---
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total Lulusan',
                            data: totalData,
                            borderColor: '#1e3a8a',
                            backgroundColor: 'rgba(30, 58, 138, 0.1)',
                            borderWidth: 3,
                            tension: 0.3,
                            fill: true,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Avg IPK',
                            data: ipkLineData,
                            borderColor: '#10b981',
                            borderWidth: 3,
                            tension: 0.3,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Avg Studi',
                            data: studiLineData,
                            borderColor: '#f59e0b',
                            borderWidth: 3,
                            borderDash: [5, 5],
                            tension: 0.3,
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
                                    size: 10,
                                    weight: 'bold'
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
                                text: 'Jumlah Alumni',
                                font: {
                                    weight: 'bold'
                                }
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
                                text: 'IPK / Semester',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });

            // --- 2. PIE CHART ---
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Cumlaude', 'Sangat Memuaskan', 'Memuaskan'],
                    datasets: [{
                        data: [ipkDist.cumlaude || 0, ipkDist.sangat_memuaskan || 0, ipkDist.memuaskan || 0],
                        backgroundColor: ['#1e3a8a', '#3b82f6', '#93c5fd'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    weight: 'bold',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>