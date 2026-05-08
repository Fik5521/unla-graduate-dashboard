@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Pengaturan Sistem</h1>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em]">Manajemen Akun & Konfigurasi Dashboard</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-blue-900 uppercase tracking-wider">Informasi Profil</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Update data diri dan alamat email anda</p>
                    </div>
                </div>

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Nama Lengkap</label>
                            <input type="text" value="{{ Auth::user()->name }}" class="w-full px-6 py-4 mt-1 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs">
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Alamat Email</label>
                            <input type="email" value="{{ Auth::user()->email }}" class="w-full px-6 py-4 mt-1 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-8 py-4 bg-blue-900 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all shadow-lg shadow-blue-100">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-blue-900 uppercase tracking-wider">Keamanan Akun</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Pastikan anda menggunakan password yang kuat</p>
                    </div>
                </div>

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <input type="password" placeholder="Password Saat Ini" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs">
                        <input type="password" placeholder="Password Baru" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs">
                    </div>
                    <div class="flex justify-end">
                        <button class="px-8 py-4 bg-gray-50 text-gray-400 rounded-2xl font-black uppercase tracking-widest text-[10px]">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="space-y-8">
            <div class="bg-blue-900 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100 relative overflow-hidden">
                <div class="absolute -bottom-10 -right-10 w-32 h-32 bg-white opacity-10 rounded-full"></div>

                <h2 class="text-sm font-black uppercase tracking-wider mb-4">Statistik Data</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest">Filter Tahun Terlama</span>
                        <span class="text-xs font-black">2001</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest">Total Record</span>
                        <span class="text-xs font-black">{{ number_format($total) }}</span>
                    </div>
                </div>

                <hr class="my-6 border-white/10">

                <button class="w-full py-4 bg-white text-blue-900 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-50 transition-all">
                    Optimasi Database
                </button>
            </div>
            <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm max-w-xl">
                <h2 class="text-lg font-black text-blue-900 uppercase tracking-widest mb-4">Import Data JSON</h2>

                @if(session('success'))
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-4 font-bold text-xs">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('import.json') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Pilih File JSON</label>
                        <input type="file" name="file_json" accept=".json" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-900 hover:file:bg-blue-100 transition-all">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-blue-900 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all active:scale-95">
                        Proses Import
                    </button>
                </form>
            </div>
        </div>

    </div>
    <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm mt-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-black text-blue-900 uppercase tracking-widest">Riwayat Aktivitas (Audit Log)</h2>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                50 Aktivitas Terakhir
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50/50">
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap rounded-tl-xl">Waktu</th>
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Aksi</th>
                        <th class="p-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap rounded-tr-xl">Keterangan Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition-all">
                        <td class="p-4 text-[11px] font-bold text-gray-500 whitespace-nowrap">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 bg-blue-50 text-blue-900 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="p-4 text-xs font-medium text-gray-600">
                            {{ $log->keterangan }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-xs font-bold text-gray-400 uppercase tracking-widest bg-gray-50/30 rounded-b-xl">
                            Belum ada riwayat aktivitas di sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection