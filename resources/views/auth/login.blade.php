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
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-[400px]">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-10 relative overflow-hidden">
            
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
            
            <div class="text-center mb-10 relative">
                <div class="inline-block p-4 bg-blue-900 rounded-2xl mb-6 shadow-xl shadow-blue-100">
                    <img src="{{ asset('unla.png') }}" class="w-12 h-12 object-contain" alt="Logo">
                </div>
                <h1 class="text-2xl font-black text-blue-900 tracking-tighter uppercase leading-none">
                    UNLA<br>GRADUATE
                </h1>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.3em] mt-4">Internal Access Only</p>
            </div>

            @if($errors->has('loginError'))
                <div class="mb-6 p-4 bg-red-50 rounded-2xl border border-red-100 text-red-600 text-[11px] font-bold text-center">
                    {{ $errors->first('loginError') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="admin@unla.ac.id"
                        class="w-full px-6 py-4 mt-1 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs transition-all shadow-sm">
                </div>

                <div>
                    <label class="text-[9px] font-black text-gray-400 uppercase ml-2 tracking-widest">Password</label>
                    <input type="password" name="password" required
                        placeholder="••••••••"
                        class="w-full px-6 py-4 mt-1 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-900 font-bold text-xs transition-all shadow-sm">
                </div>

                <button type="submit" 
                    class="w-full py-4 bg-blue-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-blue-800 transition-all shadow-lg shadow-blue-100 mt-6 active:scale-95 text-[11px]">
                    Masuk Dashboard
                </button>
            </form>
        </div>

        <div class="text-center mt-10">
            <p class="text-[9px] text-gray-300 font-bold uppercase tracking-widest">
                Universitas Langlangbuana
            </p>
        </div>
    </div>

</body>
</html>