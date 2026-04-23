<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNLA - Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    @include('partials.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        @include('partials.header')

        <div class="p-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 mb-6">
                <form action="{{ route('mahasiswa.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Cari Mahasiswa</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="NIM / Nama..."
                            class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900">
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Fakultas</label>
                        <select id="select-fakultas" name="fakultas" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Fakultas</option>
                            @foreach($listFakultas as $f)
                            <option value="{{ $f }}" {{ request('fakultas') == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Prodi</label>
                        <select id="select-prodi" name="prodi"
                            class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            disabled>
                            <option value="">Semua Prodi</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-[9px] font-bold text-gray-400 uppercase mb-1 ml-1 tracking-widest">Tahun Lulus</label>
                        <select name="tahun" class="text-xs font-bold bg-gray-50 border-none rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-900">
                            <option value="">Semua Tahun</option>
                            @foreach($listTahun as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-4 flex items-center justify-between mt-2 pt-4 border-t border-gray-50">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            Total: <span class="text-blue-900">{{ $mahasiswas->total() }}</span> Data
                        </div>
                        <div class="flex gap-3">
                            @if(request()->anyFilled(['search', 'fakultas', 'prodi', 'tahun']))
                            <a href="{{ route('mahasiswa.index') }}" class="px-6 py-2.5 text-[10px] font-bold text-red-500 uppercase border border-red-100 rounded-xl hover:bg-red-50 transition-all">Reset Filter</a>
                            @endif
                            <button type="submit" class="px-8 py-2.5 bg-blue-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-100">Terapkan Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <div class="overflow-hidden border border-gray-50 rounded-2xl shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em]">
                                <th class="py-5 px-6">NIM</th>
                                <th class="py-5 px-6">Nama</th>
                                <th class="py-5 px-6">Prodi</th>
                                <th class="py-5 px-6 text-center">Tahun Lulus</th>
                                <th class="py-5 px-6 text-center">Lama Studi</th>
                                <th class="py-5 px-6 text-right">IPK</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">
                            @forelse($mahasiswas as $mhs)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="py-4 px-6 font-mono text-xs text-blue-600 font-bold">{{ $mhs->nim }}</td>
                                <td class="py-4 px-6 font-black text-gray-700 uppercase">{{ $mhs->nama }}</td>
                                <td class="py-4 px-6 text-gray-400 italic font-medium">{{ $mhs->prodi }}</td>
                                <td class="py-4 px-6 text-center font-bold text-gray-500">{{ $mhs->tahun_lulus }}</td>

                                <td class="py-4 px-6 text-center">
                                    <span class="px-2 py-1 {{ $mhs->lama_studi <= 8 ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600' }} rounded-md font-bold text-[11px]">
                                        {{ $mhs->lama_studi }} Sem
                                    </span>
                                </td>

                                <td class="py-4 px-6 text-right font-black text-gray-800">
                                    <span class="px-3 py-1 bg-gray-100 rounded-lg group-hover:bg-white transition-colors">
                                        {{ number_format($mhs->ipk, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-8">{{ $mahasiswas->links() }}</div>
            </div>
        </div>
    </main>

    <script>
        const dataProdi = @json($prodiPerFakultas);
        const selectedFakultas = "{{ request('fakultas') }}";
        const selectedProdi = "{{ request('prodi') }}";

        const elFakultas = document.getElementById('select-fakultas');
        const elProdi = document.getElementById('select-prodi');

        function updateProdi(fakultas, currentSelectedProdi = "") {
            elProdi.innerHTML = '<option value="">Semua Prodi</option>';

            if (fakultas && dataProdi[fakultas]) {
                // Aktifkan dropdown jika fakultas ada
                elProdi.disabled = false;

                dataProdi[fakultas].forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.prodi;
                    option.textContent = item.prodi;
                    if (item.prodi === currentSelectedProdi) option.selected = true;
                    elProdi.appendChild(option);
                });
            } else {
                // Kunci kembali jika fakultas dikosongkan
                elProdi.disabled = true;
                elProdi.value = "";
            }
        }

        elFakultas.addEventListener('change', function() {
            updateProdi(this.value);
        });

        window.addEventListener('DOMContentLoaded', () => {
            if (selectedFakultas) {
                updateProdi(selectedFakultas, selectedProdi);
            }
        });
    </script>
</body>

</html>