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
        /* Style tambahan untuk ikon sorting tabel */
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
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Komparasi performa kelulusan dan masa studi</p>
            </div>

            <div class="mb-8">
                <form action="{{ route('kinerja.prodi') }}" method="GET" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-3 md:gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                    
                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 md:min-w-[180px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ $filterFakultas == $f ? 'selected' : '' }}>{{ $f }}</option>
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
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Angkatan Lulus</label>
                        <select name="tahun_lulus" class="w-full text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 md:min-w-[130px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Angkatan</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-blue-900 dark:bg-blue-700 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-md active:scale-95">Terapkan</button>
                        <a href="{{ route('kinerja.prodi') }}" class="flex-1 md:flex-none px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-center flex items-center justify-center">Reset</a>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto md:ml-auto">
                        <button type="button" onclick="handleExport('excel')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl font-black uppercase tracking-widest text-[10px] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> Excel
                        </button>
                        <button type="button" onclick="handleExport('pdf')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl font-black uppercase tracking-widest text-[10px] transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg> PDF
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8">
                <div class="bg-gradient-to-br from-green-400 to-green-600 p-6 rounded-3xl shadow-md text-white flex items-center gap-5">
                    <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-green-100">Prodi Tepat Waktu Tertinggi</p>
                        <h3 class="text-xl font-black mt-1">{{ $prodiTerbaik ?? 'Informatika' }}</h3>
                        <p class="text-xs font-medium text-green-100 mt-1">Lulus tepat waktu mencapai angka yang sangat memuaskan.</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-400 to-red-500 p-6 rounded-3xl shadow-md text-white flex items-center gap-5">
                    <div class="p-4 bg-white/20 rounded-2xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-orange-100">Butuh Perhatian Khusus</p>
                        <h3 class="text-xl font-black mt-1">{{ $prodiPerhatian ?? 'Manajemen' }}</h3>
                        <p class="text-xs font-medium text-orange-100 mt-1">Memiliki rasio mahasiswa tidak lulus / terlambat paling tinggi.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors mb-8">
                <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic text-center">Komposisi Kelulusan Per Prodi</h4>
                <div class="h-[400px] w-full">
                    <canvas id="prodiGroupedChart"></canvas>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-8 transition-colors">
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
                                <th onclick="sortTable(2)" class="sortable p-5 text-[10px] font-black text-gray-400 uppercase text-center transition-colors">Lulus ⇕</th>
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
                            <tr><td colspan="6" class="p-12 text-center text-xs font-bold text-gray-400 italic">Data Tidak Ditemukan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. DEPENDENT DROPDOWN LOGIC
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

            // 2. CHART LOGIC
            const ctx = document.getElementById('prodiGroupedChart').getContext('2d');
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($kinerjaProdi->pluck('prodi')) !!},
                    datasets: [
                        { label: 'Berhasil Lulus', data: {!! json_encode($kinerjaProdi->pluck('berhasil_lulus')) !!}, backgroundColor: '#3b82f6', borderRadius: 5 },
                        { label: 'Tepat Waktu', data: {!! json_encode($kinerjaProdi->pluck('tepat_waktu')) !!}, backgroundColor: '#22c55e', borderRadius: 5 },
                        { label: 'Gagal', data: {!! json_encode($kinerjaProdi->pluck('tidak_lulus')) !!}, backgroundColor: '#ef4444', borderRadius: 5 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' }, ticks: { color: '#9ca3af', font: { weight: 'bold' } } },
                        x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { weight: 'bold' } } }
                    },
                    plugins: {
                        legend: { labels: { color: '#9ca3af', usePointStyle: true, font: { weight: 'bold' } }, position: 'bottom' }
                    }
                }
            });
        });

        // 3. FITUR SORTING TABEL
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("kinerjaTable");
            switching = true;
            dir = "desc"; // Set awal urutan ke menurun (terbesar ke terkecil)

            while (switching) {
                switching = false;
                rows = table.rows;
                
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    
                    // Ekstrak angka murni (hilangkan "Sem", koma, dll)
                    let valX = x.innerText.replace(/[^0-9.-]+/g,"");
                    let valY = y.innerText.replace(/[^0-9.-]+/g,"");
                    
                    // Kalau kolom pertama (Nama Prodi), bandingkan sebagai huruf
                    if (n === 0) {
                        valX = x.innerText.toLowerCase();
                        valY = y.innerText.toLowerCase();
                    } else {
                        // Bandingkan sebagai angka
                        valX = parseFloat(valX) || 0;
                        valY = parseFloat(valY) || 0;
                    }

                    if (dir == "asc") {
                        if (valX > valY) { shouldSwitch = true; break; }
                    } else if (dir == "desc") {
                        if (valX < valY) { shouldSwitch = true; break; }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount ++;
                } else {
                    if (switchcount == 0 && dir == "desc") {
                        dir = "asc";
                        switching = true;
                    }
                }
            }
        }

        // 4. FUNGSI EXPORT
        function handleExport(tipe) {
            const form = document.querySelector('form');
            const params = new URLSearchParams(new FormData(form)).toString();
            // Nanti arahkan route ini di web.php ke fungsi Controller yang sesuai
            const url = tipe === 'excel' ? "{{ route('kinerja.export.excel') }}" : "{{ route('kinerja.export.pdf') }}";
            window.location.href = url + "?" + params;
        }
    </script>
</body>
</html>