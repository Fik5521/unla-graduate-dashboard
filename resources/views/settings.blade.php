@extends('layouts.app')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('content')
<div class="p-4 md:p-8 transition-colors duration-300">

    <div class="mb-8">
        <h1 class="text-2xl font-black text-blue-900 dark:text-blue-400 uppercase tracking-tighter">Pengaturan Sistem</h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em]">Manajemen Akun & Konfigurasi Dashboard</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-2xl text-blue-900 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-blue-900 dark:text-blue-400 uppercase tracking-wider">Informasi Profil</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Update data diri dan alamat email anda</p>
                    </div>
                </div>
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Nama Lengkap</label>
                            <input type="text" value="{{ Auth::user()->name }}" class="w-full px-6 py-4 mt-1 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Alamat Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="w-full px-6 py-4 mt-1 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-8 py-4 bg-blue-900 dark:bg-blue-700 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all shadow-lg">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-2xl text-red-600 dark:text-red-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-blue-900 dark:text-blue-400 uppercase tracking-wider">Keamanan Akun</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Pastikan anda menggunakan password yang kuat</p>
                    </div>
                </div>
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <input type="password" placeholder="Password Saat Ini" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs">
                        <input type="password" placeholder="Password Baru" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs">
                    </div>
                    <div class="flex justify-end">
                        <button class="px-8 py-4 bg-gray-50 dark:bg-gray-700 text-gray-400 dark:text-gray-300 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-gray-100 dark:hover:bg-gray-600 transition-all">Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-r-xl text-green-700 dark:text-green-400 text-xs font-bold">{{ session('success') }}</div>
                @endif
                <h2 class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest mb-4">Import Data JSON</h2>
                <form action="{{ route('import.json') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file_json" accept=".json" required class="w-full text-xs text-gray-500 dark:text-gray-400 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-900 dark:file:text-blue-300 file:rounded-xl file:border-0 file:py-2 file:px-4 mb-4 file:cursor-pointer file:font-bold">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-900 dark:bg-blue-700 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all active:scale-95">Proses Import</button>
                </form>
            </div>

            <div class="bg-red-50/50 dark:bg-red-900/10 p-8 rounded-[2rem] border border-red-100 dark:border-red-900/50 shadow-sm transition-colors">
                <h2 class="text-lg font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-4">Keluar Sistem</h2>
                <button type="button" onclick="confirmLogout()" class="w-full px-6 py-3 bg-red-600 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-red-700 transition-all active:scale-95">
                    Logout Sekarang
                </button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm mt-8 transition-colors">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <h2 class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest">Riwayat Aktivitas</h2>
            <form action="{{ route('settings') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..." class="flex-1 md:w-64 px-4 py-2.5 text-xs border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-900 outline-none transition-all">
                <button type="submit" class="px-5 py-2.5 bg-blue-900 text-white text-xs font-bold rounded-xl hover:bg-blue-800 transition-all active:scale-95">Cari</button>
                @if(request('search'))
                <a href="{{ route('settings') }}" class="px-5 py-2.5 bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-xl hover:bg-gray-300 transition-all">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Waktu</th>
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Aksi</th>
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="p-4 text-[11px] font-bold text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="p-4"><span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-900 dark:text-blue-300 rounded-xl text-[10px] font-black uppercase">{{ $log->aksi }}</span></td>
                        <td class="p-4 text-xs font-medium text-gray-600 dark:text-gray-300">{{ $log->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-xs font-bold text-gray-400 italic">Belum ada riwayat aktivitas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">{{ $logs->links() }}</div>
    </div>
</div>

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Yakin mau keluar?',
            text: "Sesi Anda akan berakhir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Logout!',
            customClass: {
                title: 'text-sm font-black text-blue-900 uppercase'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
@endsection