<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNLA - Data Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    <aside class="w-64 bg-white border-r flex flex-col shadow-sm">
        <div class="p-6 border-b flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-900 rounded-lg flex items-center justify-center text-white text-[10px] font-bold">U</div>
            <span class="font-bold text-xs text-blue-900 uppercase leading-tight tracking-widest">UNLA<br>GRADUATE</span>
        </div>
        <nav class="flex-1 p-4 mt-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 p-3 {{ request()->routeIs('dashboard') ? 'bg-blue-50 font-bold text-blue-900 border-l-4 border-blue-900' : 'text-gray-400' }} rounded-lg text-[11px] uppercase tracking-wider">
                        <span>🏠</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('mahasiswa.index') }}"
                        class="flex items-center gap-3 p-3 {{ request()->routeIs('mahasiswa.index') ? 'bg-blue-50 font-bold text-blue-900 border-l-4 border-blue-900' : 'text-gray-400' }} rounded-lg text-[11px] uppercase tracking-wider">
                        <span>👤</span> Mahasiswa
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 bg-white border-b px-8 flex items-center justify-between shadow-sm sticky top-0 z-10">
            <h2 class="font-black text-gray-700 uppercase tracking-widest text-xs italic">Database Alumni</h2>
            <div class="w-10 h-10 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold">F</div>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <form action="{{ route('mahasiswa.index') }}" method="GET" class="relative w-full md:w-1/3">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIM..."
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border border-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-transparent text-sm transition-all shadow-sm">
                    </form>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-4 py-2 rounded-full">
                        Total Data: <span class="text-blue-900">{{ $mahasiswas->total() }}</span> Mahasiswa
                    </div>
                </div>

                <div class="overflow-hidden border border-gray-50 rounded-2xl shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr class="text-[10px] uppercase font-black text-gray-400 tracking-[0.2em]">
                                <th class="py-5 px-6">NIM Mahasiswa</th>
                                <th class="py-5 px-6">Nama Lengkap</th>
                                <th class="py-5 px-6">Program Studi</th>
                                <th class="py-5 px-6 text-center">Tahun</th>
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
                                <td class="py-4 px-6 text-right font-black text-gray-800">
                                    <span class="px-3 py-1 bg-gray-100 rounded-lg group-hover:bg-white transition-colors">{{ number_format($mhs->ipk, 2) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center text-gray-400 italic">Data tidak ditemukan dalam sistem...</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $mahasiswas->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </main>

</body>

</html>