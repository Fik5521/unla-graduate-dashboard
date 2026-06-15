<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - UNLA Graduate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300 flex items-center justify-center min-h-screen p-6 relative overflow-hidden">

    <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-[url('{{ asset('gedung-unla.jpg') }}')] bg-cover bg-center bg-no-repeat opacity-80 dark:opacity-40"></div>
        
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 to-gray-50/50 dark:from-gray-900/70 dark:to-black/80 backdrop-blur-[2px]"></div>
    </div>

    <div class="w-full max-w-[400px] relative z-10">
        <div class="bg-white/30 dark:bg-gray-800/30 backdrop-blur-xl rounded-[2.5rem] border border-white/50 dark:border-white/10 shadow-2xl dark:shadow-black/50 p-10 relative overflow-hidden transition-colors duration-300">
            
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-100/50 dark:bg-blue-500/20 rounded-full blur-2xl transition-colors"></div>
            
            <div class="text-center mb-10 relative">
                <div class="inline-block p-4 bg-white/50 dark:bg-gray-900/50 backdrop-blur-md rounded-2xl mb-6 shadow-lg border border-white/50 dark:border-gray-700/50 transition-colors">
                    <img src="{{ asset('unla.png') }}" class="w-12 h-12 object-contain" alt="Logo UNLA">
                </div>
                <h1 class="text-2xl font-black text-blue-900 dark:text-blue-300 tracking-tighter uppercase leading-none transition-colors drop-shadow-sm">
                    UNLA<br>GRADUATE
                </h1>
                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.3em] mt-4 transition-colors">Internal Access Only</p>
            </div>

            @if($errors->has('loginError'))
                <div class="mb-6 p-4 bg-red-500/20 dark:bg-red-900/40 backdrop-blur-md rounded-2xl border border-red-500/30 text-red-700 dark:text-red-300 text-[11px] font-bold text-center transition-colors">
                    {{ $errors->first('loginError') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase ml-2 tracking-widest transition-colors drop-shadow-sm">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="admin@unla.ac.id"
                        class="w-full px-6 py-4 mt-1 bg-white/50 dark:bg-black/30 backdrop-blur-sm text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-500 border border-white/50 dark:border-gray-700/50 rounded-2xl focus:ring-2 focus:ring-blue-900 dark:focus:ring-blue-500 outline-none font-bold text-xs transition-all shadow-inner">
                </div>

                <div>
                    <label class="text-[9px] font-black text-gray-600 dark:text-gray-400 uppercase ml-2 tracking-widest transition-colors drop-shadow-sm">Password</label>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full px-6 py-4 mt-1 bg-white/50 dark:bg-black/30 backdrop-blur-sm text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-500 border border-white/50 dark:border-gray-700/50 rounded-2xl focus:ring-2 focus:ring-blue-900 dark:focus:ring-blue-500 outline-none font-bold text-xs transition-all shadow-inner">
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-blue-900/90 dark:bg-blue-600/90 backdrop-blur-md text-white rounded-2xl font-black uppercase tracking-widest hover:bg-blue-900 dark:hover:bg-blue-500 border border-blue-800/50 dark:border-blue-500/50 transition-all shadow-lg mt-6 active:scale-95 text-[11px]">
                    Masuk Dashboard
                </button>
            </form>
        </div>

        <div class="text-center mt-10 relative z-10">
            <p class="text-[9px] text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest transition-colors drop-shadow-sm">
                Universitas Langlangbuana
            </p>
        </div>
    </div>

</body>
</html>