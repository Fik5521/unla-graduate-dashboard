<header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm transition-colors duration-300">
    
    <div class="flex items-center gap-2 md:gap-3">
        <button onclick="toggleSidebar()" class="md:hidden p-2 -ml-2 text-gray-500 hover:text-blue-900 focus:outline-none rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-all active:scale-95 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <h2 class="font-extrabold text-gray-700 dark:text-gray-200 uppercase tracking-widest text-[10px] md:text-xs italic truncate">Analytics Overview</h2>
    </div>
    
    @auth
    <div class="flex items-center gap-3">
        <div class="text-right hidden sm:block">
            <p class="text-[10px] font-bold text-blue-900 dark:text-blue-400 leading-none">{{ Auth::user()->name }}</p>
            <p class="text-[9px] text-gray-400 uppercase">Admin</p>
        </div>
        <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-900 dark:bg-blue-800 rounded-full border-2 md:border-4 border-blue-50 dark:border-gray-700 flex items-center justify-center text-white text-xs md:text-sm font-bold shadow-md uppercase">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
    </div>
    @endauth
    
</header>