<aside class="w-64 bg-white border-r flex flex-col shadow-sm">
    <div class="p-6 border-b flex items-center gap-3">
        <div class="w-10 h-10 flex items-center justify-center overflow-hidden">
            <img src="{{ asset('unla.png') }}"
                class="w-full h-full object-contain"
                alt="Logo UNLA">
        </div>

        <div>
            <span class="font-bold text-xs text-black uppercase leading-tight tracking-widest">
                UNLA<br>GRADUATE
            </span>
        </div>
    </div>
    <nav class="flex-1 p-4 mt-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('dashboard') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-black' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                    <img src="{{ asset('home.png') }}" class="w-4 h-4 object-contain brightness-0" alt="">
                    Dashboard
                </a>
            </li>

            @auth
            <li>
                <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('mahasiswa.index') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-black' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                    <img src="{{ asset('users-alt.png') }}" class="w-4 h-4 object-contain brightness-0" alt="">
                    Data Mahasiswa
                </a>
            </li>

            <li>
                <a href="{{ route('analisis.prodi') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('analisis.prodi') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-black' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                    <img src="{{ asset('chart-histogram.png') }}" class="w-4 h-4 object-contain brightness-0" alt="">
                    Analisis Prodi
                </a>
            </li>

            <li>
                <a href="{{ route('perbandingan.prodi') }}" class="flex items-center gap-3 p-3 {{ request()->routeIs('perbandingan.prodi') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-black' }} rounded-lg text-[11px] uppercase tracking-wider transition-all">
                    <img src="{{ asset('compare.png') }}" class="w-4 h-4 object-contain brightness-0" alt="">
                    Perbandingan Prodi
                </a>
            </li>

            <li class="pt-10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 rounded-lg text-[11px] font-bold uppercase tracking-widest transition-all">
                        Logout
                    </button>
                </form>
            </li>
            @endauth
        </ul>
    </nav>
    <div class="p-4 border-t">
        <div class="bg-gray-50 p-4 rounded-xl">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">Status Server</p>
            <div class="flex items-center gap-2 mt-1">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold text-gray-600">Terhubung ke DB</span>
            </div>
        </div>
    </div>
</aside>