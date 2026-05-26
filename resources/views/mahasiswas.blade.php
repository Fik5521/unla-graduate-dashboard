<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - UNLA Graduate</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <form action="{{ route('mahasiswas') }}" method="GET" class="flex flex-col md:flex-row flex-wrap items-end gap-4 bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">

                    <div class="flex flex-col flex-shrink-0 w-full md:w-auto">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Cari Mahasiswa</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIM..." class="text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 w-full md:w-64 focus:ring-2 focus:ring-blue-900 outline-none">
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select name="fakultas" id="fakultas" class="text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 min-w-[180px] focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Program Studi</label>
                        <select name="prodi" id="prodi" disabled class="text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 min-w-[180px] focus:ring-2 focus:ring-blue-900 disabled:opacity-50 cursor-not-allowed">
                            <option value="">Pilih Fakultas Dulu</option>
                        </select>
                    </div>

                    <div class="flex flex-col flex-shrink-0">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Angkatan Lulus</label>
                        <select name="tahun_lulus" class="text-xs font-bold bg-gray-50 dark:bg-gray-700 dark:text-white border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Angkatan</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun_lulus') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2 w-full md:w-auto mt-2">
                        <button type="submit" class="flex-1 md:flex-none px-6 py-2.5 bg-blue-900 dark:bg-blue-800 text-white text-[10px] font-black uppercase rounded-xl hover:bg-blue-800 transition-all shadow-lg active:scale-95">Filter</button>
                        <a href="{{ route('mahasiswas') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 text-[10px] font-black uppercase rounded-xl hover:bg-gray-200 transition-all active:scale-95 flex items-center justify-center">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden mb-8 transition-colors">
                <div class="p-6 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                    <div>
                        <h2 class="text-sm font-black text-blue-900 dark:text-blue-400 uppercase tracking-wider">Daftar Mahasiswa</h2>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-white dark:bg-gray-700 px-3 py-1 rounded-full border border-gray-100 dark:border-gray-600 mt-2 inline-block">
                            {{ $mahasiswas->total() }} Mahasiswa Ditemukan
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase">No</th>
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase">Nama Mahasiswa</th>
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase">NIM</th>
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase text-center">Status</th>
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase text-center">Lama Studi</th>
                                <th class="p-5 text-[10px] font-black text-gray-400 uppercase text-center">IPK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mahasiswas as $index => $mhs)
                            @php
                            $isLulus = ($mhs->status === 'Lulus');
                            $isLambat = ($mhs->lama_studi > 9);

                            // LOGIKA WARNA: Merah jika TIDAK LULUS atau LAMA STUDI > 9
                            // Hijau jika LULUS dan MASA STUDI 7-9
                            $isWarning = (!$isLulus || $isLambat);
                            $rowClass = $isWarning ? 'bg-red-50/20 dark:bg-red-900/5' : 'hover:bg-gray-50 dark:hover:bg-gray-700/30';
                            @endphp

                            <tr class="border-b border-gray-50 dark:border-gray-700/50 transition-all {{ $rowClass }}">
                                <td class="p-5 text-xs font-bold text-gray-400">{{ $mahasiswas->firstItem() + $index }}</td>
                                <td class="p-5">
                                    <p class="text-xs font-black text-blue-900 dark:text-blue-400 uppercase">{{ $mhs->nama }}</p>
                                    <p class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">{{ $mhs->prodi }} - {{ $mhs->tahun_lulus ?? 'N/A' }}</p>
                                </td>
                                <td class="p-5 text-xs font-bold text-gray-600 dark:text-gray-300">{{ $mhs->nim }}</td>
                                <td class="p-5 text-center">
                                    <span class="px-3 py-1.5 rounded-xl text-[9px] font-black uppercase {{ $isLulus 
                                        ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400' 
                                        : 'bg-red-50 dark:bg-red-900/30 text-red-500 dark:text-red-400' }}">
                                        {{ $mhs->status }}
                                    </span>
                                </td>
                                <td class="p-5 text-center text-xs font-bold {{ $isWarning ? 'text-red-500' : 'text-green-600' }}">
                                    {{ $mhs->lama_studi > 0 ? $mhs->lama_studi . ' Sem' : '-' }}
                                </td>
                                <td class="p-5 text-center text-xs font-black text-blue-600 dark:text-blue-300">
                                    {{ number_format($mhs->ipk, 2) }}
                                </td>
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
                    {{ $mahasiswas->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const prodiData = @json($prodiPerFakultas);
            const fakultasSelect = document.getElementById('fakultas');
            const prodiSelect = document.getElementById('prodi');
            const selectedProdi = "{{ request('prodi') }}";

            function updateProdi() {
                const f = fakultasSelect.value;
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
            fakultasSelect.addEventListener('change', updateProdi);
            updateProdi();
        });
    </script>
</body>

</html>