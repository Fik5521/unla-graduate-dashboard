<div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/50 z-40 hidden transition-opacity md:hidden"></div>

<aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r flex flex-col shadow-sm transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0">
    <div class="p-6 border-b flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('unla.png') }}" class="w-full h-full object-contain" alt="Logo UNLA">
            </div>
            <div>
                <span class="font-bold text-xs text-black uppercase leading-tight tracking-widest">
                    UNLA<br>GRADUATE
                </span>
            </div>
        </div>
        <button onclick="toggleSidebar()" class="md:hidden text-gray-400 hover:text-red-500 transition-colors p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 p-4 mt-4 overflow-y-auto hide-scrollbar">
        <ul class="space-y-2">

            <li>
                <a href="{{ route('dashboard') }}" id="menu-dashboard" class="flex items-center gap-3 p-3 rounded-lg text-[11px] uppercase tracking-wider transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-gray-400 font-medium hover:bg-gray-50' }}">
                    <img src="{{ asset('home.png') }}" id="icon-dashboard" class="w-4 h-4 object-contain transition-all {{ request()->routeIs('dashboard') ? 'brightness-0' : '' }}" alt="">
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('dashboard') }}#tabel-mahasiswa" id="menu-mahasiswa" class="flex items-center gap-3 p-3 rounded-lg text-[11px] uppercase tracking-wider transition-all text-gray-400 font-medium hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" id="icon-mahasiswa" class="h-4 w-4 transition-all text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Data Mahasiswa</span>
                </a>
            </li>

            <li>
                <a href="{{ route('kinerja.prodi') }}" class="flex items-center gap-3 p-3 rounded-lg text-[11px] uppercase tracking-wider transition-all {{ request()->routeIs('kinerja.prodi') ? 'bg-blue-50 font-bold text-black border-l-4 border-black' : 'text-gray-400 font-medium hover:bg-gray-50' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-all {{ request()->routeIs('kinerja.prodi') ? 'text-black' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Kinerja Prodi</span>
                </a>
            </li>

            @auth
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

    @auth
    <div class="p-4 border-t mt-auto">
        <a href="{{ route('settings') }}" class="flex items-center gap-3 p-4 rounded-2xl transition-all group {{ request()->routeIs('settings') ? 'bg-blue-900 text-white shadow-lg shadow-blue-100' : 'bg-gray-50 text-gray-400 hover:bg-gray-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ request()->routeIs('settings') ? 'text-white' : 'text-gray-400 group-hover:text-blue-900' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <div class="flex-1">
                <p class="text-[10px] font-black uppercase tracking-widest">Pengaturan</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full {{ request()->routeIs('settings') ? '' : 'animate-pulse' }}"></div>
                    <span class="text-[8px] font-bold {{ request()->routeIs('settings') ? 'text-blue-100' : 'text-gray-400' }}">Sistem Aktif</span>
                </div>
            </div>
        </a>
    </div>
    @endauth
</aside>

<script>
    // FUNGSI UNTUK TOGGLE SIDEBAR DI MOBILE
    function toggleSidebar() {
        const sidebar = document.getElementById('main-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Script Active State yang lama (biarkan saja)
    document.addEventListener('DOMContentLoaded', function() {
        const menuDashboard = document.getElementById('menu-dashboard');
        const menuMahasiswa = document.getElementById('menu-mahasiswa');
        const iconDashboard = document.getElementById('icon-dashboard');

        const activeClasses = ['bg-blue-50', 'font-bold', 'text-black', 'border-l-4', 'border-black'];
        const inactiveClasses = ['text-gray-400', 'font-medium', 'hover:bg-gray-50'];

        function setActive(element, iconImg, isSvg) {
            if (!element) return;
            element.classList.remove(...inactiveClasses);
            element.classList.add(...activeClasses);
            if (iconImg) iconImg.classList.add('brightness-0');
            if (isSvg) {
                element.querySelector('svg').classList.add('text-black');
                element.querySelector('svg').classList.remove('text-gray-400');
            }
        }

        function setInactive(element, iconImg, isSvg) {
            if (!element) return;
            element.classList.remove(...activeClasses);
            element.classList.add(...inactiveClasses);
            if (iconImg) iconImg.classList.remove('brightness-0');
            if (isSvg) {
                element.querySelector('svg').classList.remove('text-black');
                element.querySelector('svg').classList.add('text-gray-400');
            }
        }

        const isRootPath = window.location.pathname === '/' || window.location.pathname === '/dashboard';
        if (!isRootPath) return;

        const tabelSection = document.getElementById('tabel-mahasiswa');

        if (tabelSection) {
            const observerOptions = {
                root: null,
                threshold: 0.2
            };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setActive(menuMahasiswa, null, true);
                        setInactive(menuDashboard, iconDashboard, false);
                        history.replaceState(null, null, '#tabel-mahasiswa');
                    } else {
                        setActive(menuDashboard, iconDashboard, false);
                        setInactive(menuMahasiswa, null, true);
                        history.replaceState(null, null, ' ');
                    }
                });
            }, observerOptions);

            observer.observe(tabelSection);
        }
    });
</script>