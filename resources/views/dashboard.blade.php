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

    <aside class="w-64 bg-white border-r flex flex-col shadow-sm">
        <div class="p-6 border-b flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-900 rounded-lg flex items-center justify-center text-white text-[10px] font-bold shadow-lg shadow-blue-200">U</div>
            <span class="font-bold text-xs text-blue-900 uppercase leading-tight tracking-widest">UNLA<br>GRADUATE</span>
        </div>
        <nav class="flex-1 p-4 mt-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('dashboard') ? 'bg-blue-50 font-bold text-blue-900 border-l-4 border-blue-900' : 'text-gray-400' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                        <span>🏠</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('mahasiswa.index') ? 'bg-blue-50 font-bold text-blue-900 border-l-4 border-blue-900' : 'text-gray-400' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                        <span>👤</span> Data Mahasiswa
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 border-t">
            <div class="bg-gray-50 p-4 rounded-xl">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Status Server</p>
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-[10px] font-bold text-gray-600">Terhubung ke DB</span>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        <header class="h-16 bg-white border-b px-8 flex items-center justify-between sticky top-0 z-50 shadow-sm">
            <h2 class="font-extrabold text-gray-700 uppercase tracking-widest text-xs italic">Analytics Overview</h2>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-[10px] font-bold text-blue-900 leading-none">Fiki</p>
                    <p class="text-[9px] text-gray-400 uppercase">Informatics Admin</p>
                </div>
                <div class="w-10 h-10 bg-blue-900 rounded-full border-4 border-blue-50 flex items-center justify-center text-white font-bold shadow-md">F</div>
            </div>
        </header>

        <div class="p-8">
            <div class="mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="relative flex flex-wrap items-center gap-4 bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="filterFakultas" class="filter-input text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[200px]">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Tahun Lulus</label>
                        <select name="tahun" id="filterTahun" class="filter-input text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 min-w-[150px]">
                            <option value="">Semua Tahun</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="mt-4 px-8 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-100">
                        Terapkan Filter
                    </button>

                    @if(request('fakultas') || request('tahun'))
                    <a href="{{ route('dashboard') }}" class="mt-4 text-[9px] font-bold text-red-500 uppercase underline ml-2">Reset</a>
                    @endif

                    <div class="absolute right-5 top-1/2 -translate-y-1/2 flex items-center">
                        <a href="{{ route('dashboard.export', request()->all()) }}"
                            id="btnExport"
                            class="{{ !(request('fakultas') || request('tahun')) ? 'opacity-20 cursor-not-allowed pointer-events-none' : 'hover:scale-110 active:scale-95' }} p-3 bg-red-50 text-red-600 rounded-xl transition-all border border-red-100 shadow-sm"
                            title="Export ke PDF (Hanya aktif jika filter dipilih)">
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
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="text-3xl font-black">{{ number_format($total) }}</h3>
                        <span class="text-[10px] font-bold {{ $trendTotal >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $trendTotal >= 0 ? '▲' : '▼' }} {{ abs(round($trendTotal, 1)) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tepat Waktu</p>
                    @php $persenTepat = ($total > 0) ? ($tepat / $total) * 100 : 0; @endphp
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="text-3xl font-black text-blue-900">{{ round($persenTepat, 1) }}%</h3>
                        <span class="text-[10px] font-bold {{ $trendTepat >= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $trendTepat >= 0 ? '▲' : '▼' }} {{ abs(round($trendTepat, 1)) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Tidak Tepat Waktu</p>
                    <div class="flex items-center gap-2 mt-1">
                        <h3 class="text-3xl font-black text-red-500">{{ round(100 - $persenTepat, 1) }}%</h3>
                        <span class="text-[10px] font-bold {{ $trendTepat <= 0 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $trendTepat <= 0 ? '▼' : '▲' }} {{ abs(round($trendTepat, 1)) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata Studi</p>
                    <h3 class="text-3xl font-black mt-1">{{ $rataStudi }} <small class="text-xs text-gray-400 uppercase">Sem</small></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Tren Kelulusan Per Tahun</h4>
                    <div class="h-[300px]"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Proporsi Ketepatan Lulus</h4>
                    <div class="w-full max-w-[220px]"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <div id="slider-data" data-top='{!! json_encode($topCumlaude) !!}'></div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-blue-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="font-bold text-blue-200 uppercase text-[10px] tracking-widest">
                                🏆 Top 5 Cumlaude: <span id="current-fakultas-label" class="text-white">Fakultas Teknik</span>
                            </h4>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></div>
                                <span class="text-[9px] text-blue-400 font-mono uppercase italic">Rolling Update</span>
                            </div>
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
                                <span class="text-white font-black text-lg">{{ $alumni['ipk'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Statistik Per Fakultas</h4>
                    <div class="space-y-8">
                        @foreach($dataFakultas as $f)
                        <div>
                            <div class="flex justify-between text-[11px] mb-2 font-black uppercase text-gray-600 tracking-tighter">
                                <span>{{ $f->fakultas }}</span>
                                <span class="text-blue-900">{{ $f->persentase }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden shadow-inner">
                                <div class="h-full bg-blue-900 rounded-full" style="width: {{ $f->persentase }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="chart-data"
            data-labels="{{ $tren->pluck('tahun_lulus')->toJson() }}"
            data-values="{{ $tren->pluck('total')->toJson() }}"
            data-tepat="{{ $tepat }}"
            data-total="{{ $total }}">
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. DATA PARSING
            const chartDataEl = document.getElementById('chart-data');
            const labels = JSON.parse(chartDataEl.getAttribute('data-labels'));
            const values = JSON.parse(chartDataEl.getAttribute('data-values'));
            const tepat = parseInt(chartDataEl.getAttribute('data-tepat'));
            const total = parseInt(chartDataEl.getAttribute('data-total'));
            const topData = JSON.parse(document.getElementById('slider-data').getAttribute('data-top'));

            // 2. LINE CHART (Trend)
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderColor: '#1e3a8a',
                        borderWidth: 5,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#1e3a8a',
                        pointBorderWidth: 3,
                        pointRadius: 6,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(30, 58, 138, 0.15)');
                            gradient.addColorStop(1, 'rgba(30, 58, 138, 0)');
                            return gradient;
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false
                            }
                        },
                        x: {
                            // Ini memastikan hanya label yang ada di data yang muncul
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: false,
                                maxRotation: 0
                            }
                        }
                    }
                }
            });

            // 3. PIE CHART (Proporsi)
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat'],
                    datasets: [{
                        data: [tepat, total - tepat],
                        backgroundColor: ['#1e3a8a', '#f87171'],
                        hoverOffset: 15,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 25,
                                usePointStyle: true,
                                font: {
                                    weight: 'bold',
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            // 4. AUTO-SLIDER LOGIC
            let currentIndex = 0;
            const content = document.getElementById('slider-content');
            const progressBar = document.getElementById('slider-progress');

            function rotateSlider() {
                if (topData.length === 0) return;

                // Animate Out
                content.style.opacity = '0';
                content.style.transform = 'translateX(-20px)';

                setTimeout(() => {
                    const current = topData[currentIndex];
                    document.getElementById('slide-fakultas').innerText = current.fakultas;
                    document.getElementById('slide-nama').innerText = current.nama;
                    document.getElementById('slide-prodi').innerText = current.prodi;
                    document.getElementById('slide-ipk').innerText = current.ipk;

                    // Animate In
                    content.style.opacity = '1';
                    content.style.transform = 'translateX(0)';

                    // Reset Progress Bar
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';

                    setTimeout(() => {
                        progressBar.style.transition = 'width 10000ms linear';
                        progressBar.style.width = '100%';
                    }, 100);

                    currentIndex = (currentIndex + 1) % topData.length;
                }, 700);
            }

            if (topData.length > 0) {
                rotateSlider();
                setInterval(rotateSlider, 10000);
            }
        });

        let fakultasIndex = 1; // Mulai dari index ke-1 karena index 0 sudah tampil saat page load

        function rollingTopCumlaude() {
            const listContainer = document.getElementById('top-cumlaude-list');
            const labelFakultas = document.getElementById('current-fakultas-label');

            // Efek transisi halus
            listContainer.style.opacity = '0';
            listContainer.style.transform = 'translateY(10px)';

            setTimeout(() => {
                fetch(`/api/top-cumlaude?index=${fakultasIndex}`)
                    .then(response => response.json())
                    .then(res => {
                        // Update Judul Fakultas
                        labelFakultas.innerText = res.fakultas;

                        let html = '';
                        if (res.data.length > 0) {
                            res.data.forEach((alumni, index) => {
                                html += `
                                <div class="flex items-center justify-between bg-blue-800/40 p-4 rounded-2xl border border-blue-700/50 transition-all">
                                    <div class="flex items-center gap-4">
                                        <span class="w-6 h-6 flex items-center justify-center bg-white text-blue-900 rounded-full font-black text-[10px]">${index + 1}</span>
                                        <div>
                                            <h5 class="text-white font-bold text-xs uppercase">${alumni.nama}</h5>
                                            <p class="text-blue-300 text-[9px]">${alumni.prodi}</p>
                                        </div>
                                    </div>
                                    <span class="text-white font-black text-lg">${parseFloat(alumni.ipk).toFixed(2)}</span>
                                </div>
                            `;
                            });
                        } else {
                            html = '<p class="text-blue-300 italic text-center py-10">Belum ada data untuk fakultas ini.</p>';
                        }

                        listContainer.innerHTML = html;

                        // Kembalikan Opacity
                        listContainer.style.opacity = '1';
                        listContainer.style.transform = 'translateY(0)';

                        // Naikkan index untuk fakultas berikutnya
                        fakultasIndex++;
                    })
                    .catch(err => console.error("Gagal mengambil data rolling:", err));
            }, 500);
        }

        // Ganti fakultas setiap 10 detik
        setInterval(rollingTopCumlaude, 10000);
    </script>
</body>

</html>