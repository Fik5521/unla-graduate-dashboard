<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

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

<body class="bg-gray-50 dark:bg-gray-900 flex h-screen overflow-hidden text-gray-800 dark:text-gray-200 transition-colors duration-300">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-4 md:p-8">
            <div class="mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-3 md:gap-4 bg-white dark:bg-gray-800 p-4 md:p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[180px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="prodi" disabled class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[180px] focus:ring-2 focus:ring-blue-900 disabled:opacity-50 cursor-not-allowed">
                            <option value="">Pilih Fakultas Dulu</option>
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Angkatan Lulus</label>
                        <select name="tahun_lulus" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[150px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Angkatan</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun_lulus') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-3 md:py-2.5 bg-blue-900 dark:bg-blue-800 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-lg active:scale-95 text-center">Terapkan</button>

                        <a href="{{ route('dashboard') }}" class="flex-1 md:flex-none px-5 py-3 md:py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all active:scale-95 text-center flex items-center justify-center">Reset</a>
                    </div>

                    <div class="w-full md:w-auto md:ml-auto mt-2 md:mt-0">
                        <button id="btn-export" type="button" disabled onclick="handleExport()" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-3 md:py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-400 rounded-xl font-black uppercase tracking-widest text-[10px] cursor-not-allowed transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                
                <div onclick="toggleDetail('total')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all border-l-4 border-l-gray-400 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Mahasiswa</p>
                    <h3 class="text-3xl font-black text-gray-800 dark:text-white mt-1">{{ number_format($totalMahasiswa) }}</h3>
                    <p class="text-[8px] text-gray-400 mt-2 font-bold italic">Klik untuk melihat detail →</p>
                </div>

                <div onclick="toggleDetail('lulus')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all border-l-4 border-l-blue-500 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:bg-blue-50/50 dark:hover:bg-blue-900/10">
                    <p class="text-[10px] text-blue-500 dark:text-blue-400 font-bold uppercase tracking-widest">Berhasil Lulus</p>
                    <div class="flex items-end justify-between mt-1">
                        <h3 class="text-3xl font-black text-blue-500 dark:text-blue-400">{{ number_format($berhasilLulus) }}</h3>
                        <span class="text-[9px] font-black bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 px-2 py-1 rounded-lg">Rata-rata: {{ $rataStudi }} Sem</span>
                    </div>
                    <p class="text-[8px] text-blue-400/70 mt-2 font-bold italic">Klik untuk melihat detail →</p>
                </div>

                <div onclick="toggleDetail('tepat')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all border-l-4 border-l-green-500 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:bg-green-50/50 dark:hover:bg-green-900/10">
                    <p class="text-[10px] text-green-500 font-bold uppercase tracking-widest">Lulus Tepat Waktu</p>
                    <div class="flex items-end justify-between mt-1">
                        <div class="flex items-end gap-2">
                            <h3 class="text-3xl font-black text-green-500">{{ number_format($lulusTepat) }}</h3>
                            <span class="text-[10px] font-black text-green-600 dark:text-green-400 mb-1.5">{{ round($persenTepatNow, 1) }}%</span>
                        </div>
                        <span class="text-[8px] font-black px-1.5 py-0.5 rounded-md mb-1.5 {{ $trendTepat >= 0 ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
                            {{ $trendTepat >= 0 ? '↑' : '↓' }} {{ abs(round($trendTepat, 1)) }}%
                        </span>
                    </div>
                    <p class="text-[8px] text-green-400/70 mt-2 font-bold italic">Klik untuk melihat detail →</p>
                </div>

                <div onclick="toggleDetail('gagal')" class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm transition-all border-l-4 border-l-red-500 cursor-pointer hover:shadow-md hover:scale-[1.02] hover:bg-red-50/50 dark:hover:bg-red-900/10">
                    <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest">Tidak Lulus</p>
                    <div class="flex items-end justify-between mt-1">
                        <h3 class="text-3xl font-black text-red-500">{{ number_format($tidakLulus) }}</h3>
                        <div class="flex flex-col items-end mb-1">
                            <span class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">Yg Lulus Terlambat:</span>
                            <span class="text-[10px] font-black text-orange-500">{{ round($persenTerlambatNow, 1) }}%</span>
                        </div>
                    </div>
                    <p class="text-[8px] text-red-400/70 mt-2 font-bold italic">Klik untuk melihat detail →</p>
                </div>
            </div>

            <div id="detailPanel" class="hidden bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[2rem] border border-blue-100 dark:border-blue-900/50 shadow-lg mb-8 transition-all duration-500 relative scroll-mt-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-700 pb-4">
                    <div>
                        <h3 id="detailTitle" class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest">Detail Rincian</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em]">Berdasarkan Program Studi</p>
                    </div>
                    <button onclick="closeDetail()" class="p-2 bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/30 text-gray-400 hover:text-red-500 rounded-xl transition-colors group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="overflow-x-auto max-h-[300px] overflow-y-auto hide-scrollbar">
                    <table class="w-full text-left">
                        <thead class="sticky top-0 bg-white dark:bg-gray-800 z-10">
                            <tr class="bg-gray-50 dark:bg-gray-900/50 text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                <th class="p-4 rounded-tl-xl">Program Studi</th>
                                <th class="p-4 text-right rounded-tr-xl">Jumlah Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                            </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-4 md:p-8 rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                    <h4 class="font-bold text-gray-400 uppercase text-[9px] md:text-[10px] mb-4 md:mb-8 tracking-[0.3em] italic text-center md:text-left">Tren Mahasiswa Lulus</h4>
                    <div class="h-[250px] md:h-[320px] w-full"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 md:p-8 rounded-2xl md:rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center justify-center transition-colors">
                    <h4 class="font-bold text-gray-400 uppercase text-[9px] md:text-[10px] mb-4 md:mb-8 tracking-[0.3em] italic text-center">Proporsi Kelulusan</h4>
                    <div class="w-full max-w-[200px] md:max-w-[220px]"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 md:p-8 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-all">
                <h2 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 italic">Performa Kelulusan Per Prodi</h2>

                <div class="h-[350px] overflow-y-auto pr-2 hide-scrollbar">
                    <div class="space-y-6">
                        @foreach($kinerjaProdi as $kp)
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <p class="text-[11px] font-black text-blue-900 dark:text-blue-400 uppercase truncate">{{ $kp->prodi }}</p>
                                <span class="text-[9px] font-bold text-gray-500">{{ $kp->total_mhs }} Mhs</span>
                            </div>

                            <div class="flex w-full h-4 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden shadow-inner">
                                <div style="width: {{ $kp->persen_tepat }}%" class="h-full bg-green-500" title="Tepat Waktu: {{ $kp->persen_tepat }}%"></div>
                                <div style="width: {{ $kp->persen_lambat }}%" class="h-full bg-orange-400" title="Lulus Lambat: {{ $kp->persen_lambat }}%"></div>
                                <div style="width: {{ $kp->persen_gagal }}%" class="h-full bg-red-500" title="Tidak Lulus: {{ $kp->persen_gagal }}%"></div>
                            </div>

                            <div class="flex gap-4 text-[8px] font-bold text-gray-400 uppercase">
                                <span>Lulus: {{ $kp->berhasil_lulus }}</span>
                                <span>Tepat: {{ $kp->tepat_waktu }}</span>
                                <span>Gagal: {{ $kp->tidak_lulus }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="chart-data" class="hidden"
            data-labels='{!! $tren->pluck("tahun_lulus")->toJson() !!}'
            data-total='{!! $tren->pluck("total")->toJson() !!}'
            data-tepat-tren='{!! $tren->pluck("tepat_waktu")->toJson() !!}'
            data-lambat-tren='{!! $tren->pluck("terlambat")->toJson() !!}'
            data-gagal-tren='{!! $tren->pluck("tidak_lulus")->toJson() !!}'
            data-tepat="{{ $lulusTepat }}"
            data-lambat="{{ $berhasilLulus - $lulusTepat }}"
            data-gagal="{{ $tidakLulus }}">
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DEPENDENT DROPDOWN
            const prodiData = @json($prodiPerFakultas);
            const fakultasSelectBtn = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const selectedProdi = "{{ request('prodi') }}";

            function updateProdi() {
                const f = fakultasSelectBtn.value;
                prodiSelect.innerHTML = '<option value="">Semua Program Studi</option>';
                if (f && prodiData[f]) {
                    prodiSelect.disabled = false;
                    prodiSelect.classList.remove('cursor-not-allowed');
                    prodiData[f].forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.prodi;
                        opt.text = p.prodi;
                        if (p.prodi === selectedProdi) opt.selected = true;
                        prodiSelect.appendChild(opt);
                    });
                } else {
                    prodiSelect.disabled = true;
                    prodiSelect.classList.add('cursor-not-allowed');
                }
            }
            fakultasSelectBtn.addEventListener('change', updateProdi);
            updateProdi();

            // CHART LOGIC
            const el = document.getElementById('chart-data');
            const labels = JSON.parse(el.getAttribute('data-labels'));
            const totalData = JSON.parse(el.getAttribute('data-total'));
            const tepatData = JSON.parse(el.getAttribute('data-tepat-tren'));
            const lambatData = JSON.parse(el.getAttribute('data-lambat-tren'));
            const gagalData = JSON.parse(el.getAttribute('data-gagal-tren'));

            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            const isMobile = window.innerWidth < 768;
            const tickFontSize = isMobile ? 8 : 10;

            // 1. UPDATE LINE CHART (Dengan perbaikan transparansi titik & hover)
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total Mahasiswa',
                            data: totalData,
                            borderColor: '#6b7280',
                            backgroundColor: 'rgba(107, 114, 128, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointBackgroundColor: 'transparent',
                            pointBorderColor: '#6b7280',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#6b7280',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverRadius: 7
                        },
                        {
                            label: 'Tepat Waktu',
                            data: tepatData,
                            borderColor: '#10b981',
                            borderWidth: 3,
                            tension: 0.4,
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointBackgroundColor: 'transparent',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#10b981',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverRadius: 7
                        },
                        {
                            label: 'Berhasil Lulus (Terlambat)',
                            data: lambatData,
                            borderColor: '#f97316',
                            borderWidth: 3,
                            borderDash: [5, 5],
                            tension: 0.4,
                            pointStyle: 'circle',
                            pointRadius: 4,
                            pointBackgroundColor: 'transparent',
                            pointBorderColor: '#f97316',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#f97316',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverRadius: 7
                        },
                        {
                            label: 'Tidak Lulus',
                            data: gagalData,
                            borderColor: '#ef4444',
                            borderWidth: 3,
                            borderDash: [2, 2],
                            tension: 0.4,
                            pointStyle: 'triangle',
                            pointRadius: 5,
                            pointBackgroundColor: 'transparent',
                            pointBorderColor: '#ef4444',
                            pointBorderWidth: 2,
                            pointHoverBackgroundColor: '#ef4444',
                            pointHoverBorderColor: '#ffffff',
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: {
                                    weight: 'bold',
                                    size: tickFontSize
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(156, 163, 175, 0.1)' },
                            border: { display: false },
                            ticks: { font: { size: tickFontSize, weight: 'bold' }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { font: { size: tickFontSize, weight: 'bold' }, color: '#6b7280' }
                        }
                    }
                }
            });

            // 2. UPDATE PIE CHART
            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat Waktu', 'Terlambat', 'Tidak Lulus'],
                    datasets: [{
                        data: [
                            parseInt(el.getAttribute('data-tepat')),
                            parseInt(el.getAttribute('data-lambat')),
                            parseInt(el.getAttribute('data-gagal'))
                        ],
                        backgroundColor: ['#10b981', '#f97316', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                font: { weight: 'bold', size: tickFontSize }
                            }
                        }
                    }
                }
            });

            // EXPORT BUTTON LOGIC
            const btnExport = document.getElementById('btn-export');
            function toggleExportButton() {
                if (fakultasSelectBtn.value !== "") {
                    btnExport.disabled = false;
                    btnExport.classList.remove('bg-gray-200', 'text-gray-400', 'dark:bg-gray-700', 'cursor-not-allowed');
                    btnExport.classList.add('bg-blue-900', 'text-white', 'hover:bg-blue-800', 'shadow-lg', 'active:scale-95', 'dark:bg-blue-800', 'dark:hover:bg-blue-700');
                } else {
                    btnExport.disabled = true;
                    btnExport.classList.add('bg-gray-200', 'text-gray-400', 'dark:bg-gray-700', 'cursor-not-allowed');
                    btnExport.classList.remove('bg-blue-900', 'text-white', 'hover:bg-blue-800', 'shadow-lg', 'active:scale-95', 'dark:bg-blue-800', 'dark:hover:bg-blue-700');
                }
            }
            fakultasSelectBtn.addEventListener('change', toggleExportButton);
            toggleExportButton();
        });

        // ============================================
        // LOGIKA PANEL DETAIL (AMBIL DATA DARI BLADE)
        // ============================================
        const detailKinerja = @json($kinerjaProdi);
        const panel = document.getElementById('detailPanel');
        const title = document.getElementById('detailTitle');
        const tbody = document.getElementById('detailTableBody');

        function toggleDetail(kategori) {
            panel.classList.remove('hidden');

            const mappingJudul = {
                'total': { judul: 'Detail: Total Mahasiswa', keyJumlah: 'total_mhs', warnaTeks: 'text-gray-700 dark:text-gray-300' },
                'lulus': { judul: 'Detail: Berhasil Lulus', keyJumlah: 'berhasil_lulus', warnaTeks: 'text-blue-600 dark:text-blue-400' },
                'tepat': { judul: 'Detail: Lulus Tepat Waktu', keyJumlah: 'tepat_waktu', warnaTeks: 'text-green-600 dark:text-green-400' },
                'gagal': { judul: 'Detail: Tidak Lulus / Drop Out', keyJumlah: 'tidak_lulus', warnaTeks: 'text-red-500' }
            };

            const config = mappingJudul[kategori];
            title.innerText = config.judul;
            tbody.innerHTML = '';

            // Render isi tabel secara dinamis berdasarkan Prodi
            detailKinerja.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
                
                tr.innerHTML = `
                    <td class="p-4 text-xs font-bold text-gray-800 dark:text-gray-200">${item.prodi}</td>
                    <td class="p-4 text-xs font-black text-right ${config.warnaTeks}">${item.jumlah = item[config.keyJumlah]} Mhs</td>
                `;
                tbody.appendChild(tr);
            });

            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function closeDetail() {
            panel.classList.add('hidden');
        }

        function handleExport() {
            const fakultasSelectBtn = document.getElementById('fakultas');
            const params = new URLSearchParams(new FormData(fakultasSelectBtn.form)).toString();
            window.location.href = "{{ route('dashboard.export') }}?" + params;
        }
    </script>
</body>

</html>