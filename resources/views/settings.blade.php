<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - UNLA Graduate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <div class="p-4 md:p-8 transition-colors duration-300">
            <div class="mb-8">
                <h1 class="text-2xl font-black text-blue-900 dark:text-blue-400 uppercase tracking-tighter">Pengaturan Sistem</h1>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.3em] mt-1">Manajemen Akun & Konfigurasi Dashboard</p>
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

                        <form action="{{ route('settings.update.profile') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Nama Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="w-full px-6 py-4 mt-1 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs transition-all">
                                    @error('name')
                                    <p class="text-red-500 text-[10px] mt-2 ml-2 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Alamat Email</label>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="w-full px-6 py-4 mt-1 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs transition-all">
                                    @error('email')
                                    <p class="text-red-500 text-[10px] mt-2 ml-2 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-8 py-4 bg-blue-900 dark:bg-blue-700 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all shadow-lg active:scale-95">Simpan Perubahan</button>
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

                        <form action="{{ route('settings.update.password') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="space-y-4">
                                <div>
                                    <input type="password" name="current_password" placeholder="Password Saat Ini" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs transition-all">
                                    @error('current_password')
                                    <p class="text-red-500 text-[10px] mt-2 ml-2 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <input type="password" name="password" placeholder="Password Baru (Min. 8 Karakter)" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs transition-all">
                                        @error('password')
                                        <p class="text-red-500 text-[10px] mt-2 ml-2 font-bold">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <input type="password" name="password_confirmation" placeholder="Ulangi Password Baru" class="w-full px-6 py-4 bg-gray-50 dark:bg-gray-700 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 dark:text-white font-bold text-xs transition-all">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button type="submit" class="px-8 py-4 bg-blue-50 dark:bg-gray-700 text-blue-900 dark:text-gray-300 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-100 dark:hover:bg-gray-600 transition-all active:scale-95">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-[2rem] border border-gray-100 dark:border-gray-700 shadow-sm transition-colors">
                        @if (session('success'))
                        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-r-xl text-green-700 dark:text-green-400 text-xs font-bold">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r-xl text-red-700 dark:text-red-400 text-xs font-bold">{{ session('error') }}</div>
                        @endif

                        <h2 class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest mb-4">Import Data Lulusan</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mb-4">Format didukung: .JSON, .CSV, dan .XLSX (Excel)</p>

                        <form action="{{ route('import.data') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file_import" accept=".json,.csv,.xlsx,.xls" required class="w-full text-xs text-gray-500 dark:text-gray-400 file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-900 dark:file:text-blue-300 file:rounded-xl file:border-0 file:py-2 file:px-4 mb-4 file:cursor-pointer file:font-bold">
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

                    <div class="bg-blue-50/50 dark:bg-blue-900/10 p-8 rounded-[2rem] border border-blue-100 dark:border-blue-900/50 shadow-sm transition-colors">
                        <h2 class="text-lg font-black text-blue-900 dark:text-blue-400 uppercase tracking-widest mb-4">Pusat Bantuan</h2>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase mb-6">Panduan fitur sistem UNLA Graduate</p>
                        <button type="button" onclick="openHelpModal()" class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-900 dark:bg-blue-700 text-white rounded-xl font-black uppercase tracking-widest text-[10px] hover:bg-blue-800 transition-all active:scale-95 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Lihat Panduan
                        </button>
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
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Pengguna</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">IP Address</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Aksi</th>
                                <th class="p-4 text-[10px] font-black text-gray-400 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr class="border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="p-4 text-[11px] font-bold text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>

                                <td class="p-4 text-[11px] font-black text-blue-900 dark:text-blue-400 whitespace-nowrap">
                                    {{ $log->user->name ?? 'Sistem' }}
                                </td>

                                <td class="p-4 text-[11px] font-mono font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $log->ip_address ?? '-' }}
                                </td>

                                <td class="p-4"><span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-900 dark:text-blue-300 rounded-xl text-[10px] font-black uppercase">{{ $log->aksi }}</span></td>
                                <td class="p-4 text-xs font-medium text-gray-600 dark:text-gray-300">{{ $log->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-xs font-bold text-gray-400 italic">Belum ada riwayat aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">{{ $logs->links() }}</div>
            </div>
        </div>
    </main>
    <div id="helpModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeHelpModal()"></div>

        <div id="helpCard" class="relative bg-white dark:bg-gray-800 w-full max-w-2xl max-h-[90vh] overflow-y-auto hide-scrollbar rounded-[2rem] shadow-2xl p-6 md:p-8 transform scale-95 opacity-0 transition-all duration-300 ease-out border border-gray-100 dark:border-gray-700">

            <button onclick="closeHelpModal()" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-red-500 bg-gray-50 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-colors active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="mb-8 pr-12">
                <h2 class="text-2xl font-black text-blue-900 dark:text-blue-400 uppercase tracking-tighter">Panduan Sistem</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Penjelasan Fitur Utama UNLA Graduate</p>
            </div>

            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 hover:bg-white dark:hover:bg-gray-800 transition-colors shadow-sm hover:shadow-md group">
                    <div class="flex-shrink-0 p-4 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-xl h-fit w-fit group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest mb-1.5">Dashboard</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Pusat ringkasan metrik kelulusan secara keseluruhan. Anda dapat melihat total mahasiswa, jumlah yang lulus tepat waktu, lulus lambat, angka *drop out*, serta grafik tren kelulusan dari waktu ke waktu berdasarkan filter yang dipilih.</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 hover:bg-white dark:hover:bg-gray-800 transition-colors shadow-sm hover:shadow-md group">
                    <div class="flex-shrink-0 p-4 bg-green-100 dark:bg-green-900/40 text-green-600 dark:text-green-400 rounded-xl h-fit w-fit group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest mb-1.5">Data Mahasiswa</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Tabel rincian setiap individu mahasiswa. Di sini Anda bisa mencari mahasiswa berdasarkan Nama atau NIM, serta melihat detail lama studi, IPK, dan status spesifik kelulusan mereka (contoh: Pindahan / Mengundurkan Diri).</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 hover:bg-white dark:hover:bg-gray-800 transition-colors shadow-sm hover:shadow-md group">
                    <div class="flex-shrink-0 p-4 bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 rounded-xl h-fit w-fit group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest mb-1.5">Kinerja Prodi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Analisis mendalam mengenai performa masing-masing Program Studi. Anda dapat melihat komparasi rasio kelulusan, mengetahui rata-rata waktu studi per prodi, distribusi predikat IPK (Cumlaude, Memuaskan), serta mengekspor data ke dalam Excel atau PDF.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function openHelpModal() {
            const modal = document.getElementById('helpModal');
            const card = document.getElementById('helpCard');

            // Tampilkan container utama
            modal.classList.remove('hidden');

            // Sedikit delay agar transisi scale/opacity terlihat
            setTimeout(() => {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeHelpModal() {
            const modal = document.getElementById('helpModal');
            const card = document.getElementById('helpCard');

            // Animasi mengecil dan memudar
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');

            // Tunggu animasi beres baru disembunyikan total
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
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
</body>

</html>