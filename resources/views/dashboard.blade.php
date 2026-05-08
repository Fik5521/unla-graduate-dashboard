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

<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto hide-scrollbar">
        @include('partials.header')

        <div class="p-8">
            <div class="mb-8">
                <form action="{{ route('dashboard') }}" method="GET" class="flex flex-nowrap items-end gap-4 bg-white p-5 rounded-2xl border shadow-sm overflow-x-auto hide-scrollbar">
                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 min-w-[180px]">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="prodi" disabled class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 min-w-[180px] disabled:opacity-50">
                            <option value="">Pilih Fakultas Dulu</option>
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Dari</label>
                        <select name="tahun_mulai" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5">
                            @foreach($listTahun->sort() as $t)
                            <option value="{{ $t }}" {{ $tahunMulai == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Sampai</label>
                        <select name="tahun_selesai" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5">
                            @foreach($listTahun->sortDesc() as $t)
                            <option value="{{ $t }}" {{ $tahunSelesai == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-8 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-lg">Terapkan</button>

                    <button id="btn-export" type="button" disabled
                        onclick="handleExport()"
                        class="flex items-center gap-2 px-6 py-2.5 bg-gray-200 text-gray-400 rounded-xl font-black uppercase tracking-widest text-[10px] cursor-not-allowed transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni</p>
                    <h3 class="text-3xl font-black text-blue-900 mt-1">{{ number_format($total) }}</h3>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-blue-900">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-blue-900">Tepat Waktu</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-3xl font-black text-blue-900 mt-1">{{ round($persenTepatNow, 1) }}%</h3>
                        <span class="text-[10px] font-black mb-1.5 px-1.5 py-0.5 rounded-lg {{ $trendTepat >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                            {{ $trendTepat >= 0 ? '↑' : '↓' }} {{ abs(round($trendTepat, 1)) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-red-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-red-500">Terlambat</p>
                    <div class="flex items-end gap-2">
                        <h3 class="text-3xl font-black text-red-500 mt-1">{{ round($persenTerlambatNow, 1) }}%</h3>
                        <span class="text-[10px] font-black mb-1.5 px-1.5 py-0.5 rounded-lg {{ $trendTerlambat <= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                            {{ $trendTerlambat <= 0 ? '↓' : '↑' }} {{ abs(round($trendTerlambat, 1)) }}%
                        </span>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-orange-500">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rata-rata Studi</p>
                    <h3 class="text-3xl font-black text-orange-500 mt-1">{{ $rataStudi }} <small class="text-[10px] uppercase font-bold tracking-tighter">Sem</small></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Tren Kelulusan</h4>
                    <div class="h-[320px]"><canvas id="lineChart"></canvas></div>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col items-center justify-center">
                    <h4 class="font-bold text-gray-400 uppercase text-[10px] mb-8 tracking-[0.3em] italic">Proporsi</h4>
                    <div class="w-full max-w-[220px]"><canvas id="pieChart"></canvas></div>
                </div>
            </div>

            <div id="tabel-mahasiswa" class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8 scroll-mt-10">
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                    <div>
                        <h2 class="text-sm font-black text-blue-900 uppercase tracking-wider">Daftar Mahasiswa</h2>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-white px-3 py-1 rounded-full border border-gray-100 mt-2 inline-block">
                            {{ $mahasiswas->total() }} Record
                        </span>
                    </div>

                    <form action="{{ route('dashboard') }}#tabel-mahasiswa" method="GET" class="flex items-center gap-2">
                        @if(request('fakultas')) <input type="hidden" name="fakultas" value="{{ request('fakultas') }}"> @endif
                        @if(request('prodi')) <input type="hidden" name="prodi" value="{{ request('prodi') }}"> @endif
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIM..." class="text-xs font-bold bg-white border border-gray-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-blue-900 w-[200px]">
                        <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-xl text-[10px] font-black uppercase">Cari</button>
                    </form>
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">No</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">Mahasiswa</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">NIM</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap text-center">Lama Studi</th>
                            <th class="p-5 text-[10px] font-black text-gray-400 uppercase whitespace-nowrap text-center">IPK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $index => $mhs)
                        <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                            <td class="p-5 text-xs font-bold text-gray-400">
                                {{ $mahasiswas->firstItem() + $index }}
                            </td>

                            <td class="p-5">
                                <p class="text-xs font-black text-blue-900 dark:text-blue-400 uppercase">{{ $mhs->nama }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $mhs->prodi }} - {{ $mhs->tahun_lulus }}</p>
                            </td>

                            <td class="p-5 text-xs font-bold text-gray-600 dark:text-gray-300">
                                {{ $mhs->nim }}
                            </td>

                            <td class="p-5 text-center">
                                <span class="text-xs font-black {{ $mhs->lama_studi <= 9 ? 'text-blue-600 dark:text-blue-400' : 'text-red-500 dark:text-red-400' }}">
                                    {{ $mhs->lama_studi }} <span class="text-[9px] font-bold text-gray-400 uppercase">Sem</span>
                                </span>
                            </td>

                            <td class="p-5 text-center">
                                <span class="px-3 py-1.5 bg-blue-50 dark:bg-gray-800 text-blue-900 dark:text-blue-300 rounded-xl text-[10px] font-black shadow-sm">
                                    {{ number_format($mhs->ipk, 2) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">
                                Data Mahasiswa Tidak Ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-5 bg-gray-50/50 border-t">{{ $mahasiswas->links() }}</div>
            </div>
        </div>

        <div id="chart-data" data-labels='{!! $tren->pluck("tahun_lulus")->toJson() !!}' data-total='{!! $tren->pluck("total")->toJson() !!}' data-tepat-tren='{!! $tren->pluck("tepat_waktu")->toJson() !!}' data-lambat-tren='{!! $tren->pluck("terlambat")->toJson() !!}' data-tepat="{{ $tepat }}" data-all="{{ $total }}"></div>
    </main>

    <script>
        tailwind.config = {
            darkMode: 'class',
        }
        document.addEventListener('DOMContentLoaded', function() {
            // DEPENDENT DROPDOWN
            const prodiData = @json($prodiPerFakultas);
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const selectedProdi = "{{ request('prodi') }}";

            function updateProdi() {
                const f = fakultasSelect.value;
                prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
                if (f && prodiData[f]) {
                    prodiSelect.disabled = false;
                    prodiData[f].forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.prodi;
                        opt.text = p.prodi;
                        if (p.prodi === selectedProdi) opt.selected = true;
                        prodiSelect.appendChild(opt);
                    });
                } else {
                    prodiSelect.disabled = true;
                }
            }
            fakultasSelect.addEventListener('change', updateProdi);
            updateProdi();

            // CHARTS LOGIC
            const el = document.getElementById('chart-data');
            const labels = JSON.parse(el.getAttribute('data-labels'));
            const totalData = JSON.parse(el.getAttribute('data-total'));
            const tepatData = JSON.parse(el.getAttribute('data-tepat-tren'));
            const lambatData = JSON.parse(el.getAttribute('data-lambat-tren')); // Ambil data tidak tepat (terlambat)

            // LINE CHART
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Total Lulusan',
                            data: totalData,
                            borderColor: '#1e3a8a',
                            backgroundColor: 'rgba(30, 58, 138, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 3,
                            pointStyle: 'circle'
                        },
                        {
                            label: 'Tepat Waktu',
                            data: tepatData,
                            borderColor: '#10b981', // Warna Hijau
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 3,
                            pointStyle: 'circle'
                        },
                        {
                            label: 'Tidak Tepat',
                            data: lambatData,
                            borderColor: '#ef4444', // Warna Merah
                            borderWidth: 3,
                            borderDash: [5, 5], // Efek garis putus-putus biar gampang dibedakan
                            tension: 0.4,
                            pointRadius: 3,
                            pointStyle: 'circle'
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

            new Chart(document.getElementById('pieChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Tepat', 'Terlambat'],
                    datasets: [{
                        data: [parseInt(el.getAttribute('data-tepat')), parseInt(el.getAttribute('data-all')) - parseInt(el.getAttribute('data-tepat'))],
                        backgroundColor: ['#1e3a8a', '#ef4444']
                    }]
                },
                options: {
                    cutout: '80%'
                }
            });
        });

        const fakultasSelect = document.getElementById('fakultas');
        const btnExport = document.getElementById('btn-export');

        function toggleExportButton() {
            if (fakultasSelect.value !== "") {
                // Aktifkan tombol
                btnExport.disabled = false;
                btnExport.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                btnExport.classList.add('bg-blue-900', 'text-white', 'hover:bg-blue-800', 'shadow-lg', 'active:scale-95');
            } else {
                // Matikan tombol
                btnExport.disabled = true;
                btnExport.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                btnExport.classList.remove('bg-blue-900', 'text-white', 'hover:bg-blue-800', 'shadow-lg', 'active:scale-95');
            }
        }

        // Jalankan saat fakultas diganti
        fakultasSelect.addEventListener('change', toggleExportButton);

        // Jalankan saat halaman pertama muat (buat jaga-jaga kalau filter sudah terisi)
        toggleExportButton();

        // Fungsi buat kirim perintah export dengan filter yang ada
        function handleExport() {
            const params = new URLSearchParams(new FormData(fakultasSelect.form)).toString();
            window.location.href = "{{ route('dashboard.export') }}?" + params;
        }
    </script>
</body>

</html>