<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinerja Per Prodi - UNLA Graduate Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        th.sortable:hover { color: #3b82f6; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 flex h-screen overflow-hidden text-gray-800 dark:text-gray-200 transition-colors duration-300">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-4 md:p-8">
            <div class="mb-6">
                <h1 class="text-xl md:text-2xl font-black text-blue-900 dark:text-blue-400">Kinerja Program Studi</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Komparasi performa kelulusan dan Kualitas Akademik</p>
            </div>

            <div class="mb-8">
                <form action="{{ route('kinerja.prodi') }}" id="filterForm" method="GET" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-3 md:gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                    
                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 md:min-w-[180px] focus:ring-2 focus:ring-blue-900">
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ (request('fakultas') == $f) || (!request('fakultas') && stripos($f, 'Hukum') !== false) ? 'selected' : '' }}>
                                {{ $f }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="prodi" disabled class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 md:min-w-[180px] focus:ring-2 focus:ring-blue-900 disabled:opacity-50 cursor-not-allowed">
                            <option value="">Pilih Fakultas Dulu</option>
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Angkatan</label>
                        <select name="angkatan" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 md:min-w-[130px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Angkatan</option>
                            @foreach($listAngkatan as $a)
                            <option value="{{ $a }}" {{ request('angkatan') == $a ? 'selected' : '' }}>{{ $a }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-blue-900 dark:bg-blue-700 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-md active:scale-95">Terapkan</button>
                        @if(request('fakultas') || request('prodi') || request('angkatan'))
                        <a href="{{ route('kinerja.prodi') }}" class="flex-1 md:flex-none px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-center flex items-center justify-center">Reset</a>
                        @endif
                    </div>

                    <div class="flex gap-2 w-full md:w-auto md:ml-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t border-gray-100 dark:border-gray-700 md:border-none">
                        
                        <button type="button" onclick="exportData('pdf')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-red-500 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-red-600 transition-all active:scale-95 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </button>
                        
                        <button type="button" onclick="exportData('excel')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-green-500 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-green-600 transition-all active:scale-95 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel
                        </button>

                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- CHART KOMPOSISI LULUSAN -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-6 tracking-[0.3em] italic text-center">Komposisi Kelulusan Per Prodi</h4>
                    <div class="h-[300px] w-full"><canvas id="prodiGroupedChart"></canvas></div>
                </div>

                <!-- CHART DISTRIBUSI IPK -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors flex flex-col items-center">
                    
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <h4 class="font-bold text-gray-400 uppercase text-[10px] tracking-[0.3em] italic text-center">Distribusi Kualitas Akademik (IPK)</h4>
                        
                        <!-- Ikon Info & Tooltip Penjelasan Predikat -->
                        <div class="relative flex items-center justify-center group/tooltip z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-hover/tooltip:text-blue-500 cursor-help transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Tooltip Box -->
                            <div class="absolute bottom-full right-1/2 translate-x-1/2 mb-2 w-64 p-3 bg-gray-900 text-white text-[9px] font-medium rounded-xl opacity-0 invisible group-hover/tooltip:opacity-100 group-hover/tooltip:visible transition-all duration-200 shadow-xl leading-relaxed text-left">
                                <p class="font-bold text-blue-400 mb-1 border-b border-gray-700 pb-1">Aturan Predikat Kelulusan:</p>
                                <ul class="list-disc pl-3 space-y-1 mt-1 text-gray-300">
                                    <li><strong class="text-white">Cumlaude:</strong> IPK > 3.50. <br><span class="text-red-400 text-[8px]">*Catatan: Mahasiswa Pindahan dan Mahasiswa yang lulus terlambat tidak berhak mendapatkan predikat Cumlaude.</span></li>
                                    <li><strong class="text-white">Sangat Memuaskan:</strong> IPK 3.00 - 3.50.</li>
                                    <li><strong class="text-white">Memuaskan:</strong> IPK 2.76 - 2.99.</li>
                                    <li><strong class="text-white">Cukup:</strong> IPK < 2.76.</li>
                                </ul>
                                <!-- Panah Tooltip -->
                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    </div>

                    <div class="h-[250px] w-full max-w-[250px] relative z-0"><canvas id="ipkChart"></canvas></div>
                </div>
            </div>

            <!-- CHART RATA-RATA IPK -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors mb-8">
                <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-6 tracking-[0.3em] italic text-center">Rata-Rata IPK Per Program Studi</h4>
                <div class="h-[300px] w-full"><canvas id="ipkBarChart"></canvas></div>
            </div>

            <!-- TABEL KINERJA -->
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-8 transition-colors">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h2 class="text-xs font-black text-blue-900 dark:text-blue-300 uppercase">Rincian Data Program Studi</h2>
                    <span class="text-[9px] text-gray-400 font-bold bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">Klik judul tabel untuk mengurutkan</span>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left min-w-[800px]" id="kinerjaTable">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cursor-pointer select-none">
                                <th onclick="sortTable(0)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase transition-colors">Program Studi ⇕</th>
                                <th onclick="sortTable(1)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Total Mhs ⇕</th>
                                <th onclick="sortTable(2)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Berhasil Lulus ⇕</th>
                                <th onclick="sortTable(3)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Tepat Waktu ⇕</th>
                                <th onclick="sortTable(4)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Gagal ⇕</th>
                                <th onclick="sortTable(5)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Avg Studi ⇕</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @forelse($kinerjaProdi as $kp)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all">
                                <td class="p-5"><p class="text-xs font-black text-blue-900 dark:text-blue-400 uppercase">{{ $kp->prodi }}</p></td>
                                <td class="p-5 text-center font-black dark:text-gray-200">{{ $kp->total_mhs }}</td>
                                <td class="p-5 text-center font-black text-blue-600 dark:text-blue-400">{{ $kp->berhasil_lulus }}</td>
                                <td class="p-5 text-center font-black text-green-600 dark:text-green-400">{{ $kp->tepat_waktu }}</td>
                                <td class="p-5 text-center font-black text-red-500 dark:text-red-400">{{ $kp->tidak_lulus }}</td>
                                <td class="p-5 text-center font-black text-orange-500 dark:text-orange-400">{{ $kp->rata_studi ?? 0 }} Sem</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-xs font-bold text-gray-400 italic">Data Tidak Ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700">
                    {{ $kinerjaProdi->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- HIDDEN DATA UNTUK CHART -->
    <div id="chartDataContainer" class="hidden"
        data-prodi='{!! json_encode($kinerjaProdiChart->pluck("prodi")) !!}'
        data-tepat='{!! json_encode($kinerjaProdiChart->pluck("tepat_waktu")) !!}'
        data-lambat='{!! json_encode($kinerjaProdiChart->pluck("berhasil_lulus")) !!}'
        data-gagal='{!! json_encode($kinerjaProdiChart->pluck("tidak_lulus")) !!}'
        data-rataipk='{!! json_encode($kinerjaProdiChart->pluck("rata_ipk")) !!}'
        
        data-cumlaude="{{ $distribusiIpk->cumlaude ?? 0 }}"
        data-sangat="{{ $distribusiIpk->sangat_memuaskan ?? 0 }}"
        data-memuaskan="{{ $distribusiIpk->memuaskan ?? 0 }}"
        data-cukup="{{ $distribusiIpk->cukup ?? 0 }}">
    </div>

    <!-- SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DEPENDENT DROPDOWN
            const prodiData = {!! json_encode($prodiPerFakultas) !!};
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const prodiSelected = "{{ $prodiSelected }}";

            function updateProdiDropdown() {
                const fakultas = fakultasSelect.value;
                prodiSelect.innerHTML = '<option value="">Semua Program Studi</option>';
                if (fakultas && prodiData[fakultas]) {
                    prodiSelect.disabled = false;
                    prodiSelect.classList.remove('cursor-not-allowed', 'opacity-50');
                    prodiData[fakultas].forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.prodi;
                        opt.text = p.prodi;
                        if (p.prodi === prodiSelected) opt.selected = true;
                        prodiSelect.appendChild(opt);
                    });
                } else {
                    prodiSelect.disabled = true;
                    prodiSelect.classList.add('cursor-not-allowed', 'opacity-50');
                }
            }
            fakultasSelect.addEventListener('change', updateProdiDropdown);
            updateProdiDropdown();

            // AMBIL DATA DARI HIDDEN DIV
            const container = document.getElementById('chartDataContainer');
            const labelsProdi = JSON.parse(container.getAttribute('data-prodi'));
            const dataTepat = JSON.parse(container.getAttribute('data-tepat'));
            const dataLambat = JSON.parse(container.getAttribute('data-lambat'));
            const dataGagal = JSON.parse(container.getAttribute('data-gagal'));
            const dataRataIpk = JSON.parse(container.getAttribute('data-rataipk'));

            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            const commonOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: '#9ca3af', usePointStyle: true, font: { weight: 'bold' } } } } };

            // 1. CHART KOMPOSISI LULUSAN
            new Chart(document.getElementById('prodiGroupedChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labelsProdi,
                    datasets: [
                        { label: 'Tepat Waktu', data: dataTepat, backgroundColor: '#22c55e', borderRadius: 4 },
                        { label: 'Berhasil Lulus', data: dataLambat, backgroundColor: '#f97316', borderRadius: 4 },
                        { label: 'Gagal / DO', data: dataGagal, backgroundColor: '#ef4444', borderRadius: 4 }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' }, ticks: { color: '#9ca3af' } },
                        x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 } } }
                    }
                }
            });

            // 2. CHART RATA-RATA IPK
            new Chart(document.getElementById('ipkBarChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labelsProdi,
                    datasets: [{ label: 'Rata-rata IPK', data: dataRataIpk, backgroundColor: '#8b5cf6', borderRadius: 6 }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: { min: 2.0, max: 4.0, grid: { color: 'rgba(156, 163, 175, 0.1)' }, ticks: { color: '#9ca3af' } },
                        x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 10 } } }
                    }
                }
            });

            // 3. CHART DISTRIBUSI KUALITAS AKADEMIK (DOUGHNUT)
            new Chart(document.getElementById('ipkChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Cumlaude (>3.50)', 'Sangat Memuaskan (3.00-3.50)', 'Memuaskan (2.76-2.99)', 'Cukup (<2.76)'],
                    datasets: [{
                        data: [
                            parseInt(container.getAttribute('data-cumlaude')),
                            parseInt(container.getAttribute('data-sangat')),
                            parseInt(container.getAttribute('data-memuaskan')),
                            parseInt(container.getAttribute('data-cukup'))
                        ],
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: { cutout: '65%', plugins: { legend: { position: 'bottom', labels: { color: '#9ca3af', usePointStyle: true, font: { size: 10 } } } } }
            });
        });

        // FUNGSI SORTING TABEL
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("kinerjaTable");
            switching = true; dir = "desc";
            while (switching) {
                switching = false; rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n]; y = rows[i + 1].getElementsByTagName("TD")[n];
                    let valX = x.innerText.replace(/[^0-9.-]+/g, ""); let valY = y.innerText.replace(/[^0-9.-]+/g, "");
                    if (n === 0) { valX = x.innerText.toLowerCase(); valY = y.innerText.toLowerCase(); } 
                    else { valX = parseFloat(valX) || 0; valY = parseFloat(valY) || 0; }
                    if (dir == "asc") { if (valX > valY) { shouldSwitch = true; break; } } 
                    else if (dir == "desc") { if (valX < valY) { shouldSwitch = true; break; } }
                }
                if (shouldSwitch) { rows[i].parentNode.insertBefore(rows[i + 1], rows[i]); switching = true; switchcount++; } 
                else { if (switchcount == 0 && dir == "desc") { dir = "asc"; switching = true; } }
            }
        }
    </script>
</body>
</html>