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

        <div class="p-4 md:p-8">
            <div class="mb-6 md:mb-8">
                <h1 class="text-xl md:text-2xl font-black text-blue-900">Kinerja Program Studi</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Komparasi performa kelulusan dan masa studi</p>
            </div>

            <div class="mb-6 md:mb-8">
                <form action="{{ route('kinerja.prodi') }}" method="GET" class="flex flex-col md:flex-row flex-wrap items-start md:items-end gap-3 md:gap-4 bg-white p-4 md:p-5 rounded-2xl border shadow-sm">
                    
                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="w-full text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[200px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ $filterFakultas == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="prodi" disabled class="w-full text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[200px] focus:ring-2 focus:ring-blue-900 disabled:opacity-50 cursor-not-allowed">
                            <option value="">Semua Program Studi</option>
                        </select>
                    </div>

                    <div class="flex flex-col w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Angkatan Lulus</label>
                        <select name="tahun_lulus" class="w-full text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-3 md:py-2.5 md:min-w-[150px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Angkatan</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ $filterTahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto mt-2 md:mt-0">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-3 md:py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-lg active:scale-95 text-center">Terapkan</button>
                        
                        <a href="{{ route('kinerja.prodi') }}" class="flex-1 md:flex-none px-5 py-3 md:py-2.5 bg-gray-100 text-gray-500 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 transition-all active:scale-95 text-center flex items-center justify-center">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
                <div class="bg-white p-5 md:p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-gray-400">
                    <p class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Alumni (Filter)</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h3 class="text-2xl md:text-3xl font-black text-gray-800">{{ number_format($totalAlumni) }}</h3>
                    </div>
                </div>

                <div class="bg-white p-5 md:p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-green-500">
                    <p class="text-[9px] md:text-[10px] text-green-500 font-bold uppercase tracking-widest">Rata-Rata Tercepat</p>
                    @if($prodiTercepat)
                        <h3 class="text-xl md:text-2xl font-black text-gray-800 mt-1 leading-tight">{{ $prodiTercepat->prodi }}</h3>
                        <p class="text-[9px] md:text-[10px] font-black bg-green-50 text-green-600 px-2 py-0.5 rounded-lg inline-block mt-1">{{ $prodiTercepat->rata_studi }} Semester</p>
                    @else
                        <h3 class="text-xl md:text-2xl font-black text-gray-400 mt-1">-</h3>
                    @endif
                </div>

                <div class="bg-white p-5 md:p-6 rounded-3xl border border-gray-100 shadow-sm border-l-4 border-l-blue-900 sm:col-span-2 md:col-span-1">
                    <p class="text-[9px] md:text-[10px] text-blue-900 font-bold uppercase tracking-widest">Tepat Waktu Tertinggi</p>
                    @if($prodiTepatTertinggi)
                        <h3 class="text-xl md:text-2xl font-black text-gray-800 mt-1 leading-tight">{{ $prodiTepatTertinggi->prodi }}</h3>
                        <p class="text-[9px] md:text-[10px] font-black bg-blue-50 text-blue-900 px-2 py-0.5 rounded-lg inline-block mt-1">{{ $prodiTepatTertinggi->persen_tepat }}% Tepat</p>
                    @else
                        <h3 class="text-xl md:text-2xl font-black text-gray-400 mt-1">-</h3>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
                <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[9px] md:text-[10px] mb-4 md:mb-8 tracking-[0.3em] italic text-center md:text-left">Persentase Tepat Waktu</h4>
                    <div class="h-[250px] md:h-[280px] w-full"><canvas id="chartTepatWaktu"></canvas></div>
                </div>

                <div class="bg-white p-4 md:p-8 rounded-2xl md:rounded-[2rem] border border-gray-100 shadow-sm">
                    <h4 class="font-bold text-gray-400 uppercase text-[9px] md:text-[10px] mb-4 md:mb-8 tracking-[0.3em] italic text-center md:text-left">Rata-Rata Masa Studi</h4>
                    <div class="h-[250px] md:h-[280px] w-full"><canvas id="chartRataStudi"></canvas></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl md:rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
                <div class="p-4 md:p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50">
                    <div class="w-full text-center md:text-left">
                        <h2 class="text-xs md:text-sm font-black text-blue-900 uppercase tracking-wider">Rincian Data Program Studi</h2>
                    </div>
                </div>

                <div class="overflow-x-auto w-full hide-scrollbar">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="p-4 md:p-5 text-[9px] md:text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">Program Studi</th>
                                <th class="p-4 md:p-5 text-[9px] md:text-[10px] font-black text-gray-400 uppercase whitespace-nowrap">Fakultas</th>
                                <th class="p-4 md:p-5 text-[9px] md:text-[10px] font-black text-gray-400 uppercase whitespace-nowrap text-center">Total Alumni</th>
                                <th class="p-4 md:p-5 text-[9px] md:text-[10px] font-black text-gray-400 uppercase whitespace-nowrap text-center">Rata-Rata Studi</th>
                                <th class="p-4 md:p-5 text-[9px] md:text-[10px] font-black text-gray-400 uppercase whitespace-nowrap text-center">Lulus Tepat Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kinerjaProdi as $kp)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-all">
                                <td class="p-4 md:p-5">
                                    <p class="text-[11px] md:text-xs font-black text-blue-900 uppercase">{{ $kp->prodi }}</p>
                                </td>
                                <td class="p-4 md:p-5">
                                    <p class="text-[9px] md:text-[10px] font-bold text-gray-500 uppercase">{{ $kp->fakultas }}</p>
                                </td>
                                <td class="p-4 md:p-5 text-center">
                                    <span class="text-[11px] md:text-xs font-bold text-gray-600">{{ number_format($kp->total_lulusan) }}</span>
                                </td>
                                <td class="p-4 md:p-5 text-center">
                                    <span class="text-[11px] md:text-xs font-black {{ $kp->rata_studi <= 9 ? 'text-blue-600' : 'text-red-500' }}">
                                        {{ $kp->rata_studi }} <span class="text-[8px] md:text-[9px] font-bold text-gray-400 uppercase">Sem</span>
                                    </span>
                                </td>
                                <td class="p-4 md:p-5 text-center">
                                    <span class="px-2 md:px-3 py-1 md:py-1.5 rounded-xl text-[9px] md:text-[10px] font-black shadow-sm {{ $kp->persen_tepat >= 50 ? 'bg-blue-50 text-blue-900' : 'bg-red-50 text-red-600' }}">
                                        {{ $kp->persen_tepat }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-[10px] md:text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    Tidak ada data untuk filter tersebut
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="chart-data" class="hidden"
            data-labels='{!! $kinerjaProdi->pluck("prodi")->toJson() !!}' 
            data-persentase='{!! $kinerjaProdi->pluck("persen_tepat")->toJson() !!}'
            data-ratastudi='{!! $kinerjaProdi->pluck("rata_studi")->toJson() !!}'>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA DEPENDENT DROPDOWN FAKULTAS -> PRODI ---
            const prodiData = @json($prodiPerFakultas);
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const selectedProdi = "{{ $prodiSelected }}";

            function updateProdi() {
                const f = fakultasSelect.value;
                prodiSelect.innerHTML = '<option value="">Semua Prodi</option>';
                
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
                    prodiSelect.innerHTML = '<option value="">Semua Program Studi</option>';
                    prodiSelect.disabled = true;
                    prodiSelect.classList.add('cursor-not-allowed');
                }
            }
            // Jalankan fungsi saat Fakultas diubah
            fakultasSelect.addEventListener('change', updateProdi);
            
            // Jalankan sekali saat halaman dimuat (untuk mempertahankan pilihan setelah tombol Terapkan ditekan)
            updateProdi();
            // ----------------------------------------------------

            // --- LOGIKA CHART.JS ---
            const el = document.getElementById('chart-data');
            const labels = JSON.parse(el.getAttribute('data-labels'));
            const persentaseData = JSON.parse(el.getAttribute('data-persentase'));
            const rataStudiData = JSON.parse(el.getAttribute('data-ratastudi'));

            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

            // Deteksi lebar layar untuk penyesuaian font chart di HP
            const isMobile = window.innerWidth < 768;
            const tickFontSize = isMobile ? 8 : 9;

            // GRAFIK 1: Persentase Tepat Waktu
            new Chart(document.getElementById('chartTepatWaktu'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Lulus Tepat Waktu (%)',
                        data: persentaseData,
                        backgroundColor: '#1e3a8a',
                        borderRadius: 4,
                        barPercentage: isMobile ? 0.8 : 0.6,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { tooltip: { padding: 10, titleFont: { size: 11 }, bodyFont: { size: 11 }, cornerRadius: 8 }, legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, max: 100,
                            grid: { color: '#f3f4f6' },
                            border: { display: false },
                            ticks: { font: {size: tickFontSize, weight: 'bold'}, color: '#9ca3af', callback: val => val + '%' }
                        },
                        x: { 
                            grid: { display: false }, border: { display: false },
                            ticks: { font: {size: tickFontSize, weight: 'bold'}, color: '#6b7280' }
                        }
                    }
                }
            });

            // GRAFIK 2: Rata-Rata Studi
            new Chart(document.getElementById('chartRataStudi'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Rata-rata Semester',
                        data: rataStudiData,
                        backgroundColor: '#ef4444', 
                        borderRadius: 4,
                        barPercentage: isMobile ? 0.8 : 0.6,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { tooltip: { padding: 10, titleFont: { size: 11 }, bodyFont: { size: 11 }, cornerRadius: 8 }, legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            suggestedMax: 10, 
                            grid: { color: '#f3f4f6' },
                            border: { display: false },
                            ticks: { font: {size: tickFontSize, weight: 'bold'}, color: '#9ca3af', callback: val => val + (isMobile ? '' : ' Sem') }
                        },
                        x: { 
                            grid: { display: false }, border: { display: false },
                            ticks: { font: {size: tickFontSize, weight: 'bold'}, color: '#6b7280' }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>