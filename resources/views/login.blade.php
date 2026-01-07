<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InventariSIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-bg {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-30%, -30%) scale(1.1); }
        }

        .animate-slideUp {
            animation: slideUp 0.6s ease;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse-bg 15s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-5 transition-colors duration-300 bg-gradient-to-br from-[#14a2ba] to-[#0d7a8f] dark:from-slate-900 dark:to-slate-950">
    <!-- Dark Mode Toggle -->
    <div class="fixed top-5 right-5 z-50">
        <button 
            onclick="toggleDarkMode()" 
            class="w-12 h-12 rounded-full border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 relative transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center group"
            aria-label="Toggle dark mode"
        >
            <x-heroicon-o-sun class="w-6 h-6 text-[#14a2ba] dark:hidden transition-transform group-hover:rotate-45" />
            <x-heroicon-o-moon class="w-6 h-6 text-[#14a2ba] hidden dark:block transition-transform group-hover:-rotate-12" />
        </button>
    </div>
    
    <!-- Login Container -->
    <div class="w-full max-w-[950px] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[550px] animate-slideUp transition-all duration-300">
        <!-- Left Side - Logo & Branding -->
        <div class="flex-1 bg-gradient-to-b from-[#14a2ba] to-[#0d7a8f] dark:from-[#0d7a8f] dark:to-slate-900 p-10 md:p-15 flex flex-col justify-center items-center text-white relative overflow-hidden">
            <div class="absolute inset-0 login-left"></div>
            
            <div class="relative z-10 bg-white p-9 rounded-2xl mb-9 shadow-2xl transition-transform duration-300 hover:scale-105 hover:rotate-2">
                <img src="{{ asset('images/pln-logo.png') }}" alt="PLN Logo" class="w-30 h-30 object-contain">
            </div>
            
            <div class="relative z-10 flex items-center gap-3 mb-2">
                <x-heroicon-o-cube class="w-10 h-10 text-white drop-shadow-lg" />
                <h1 class="text-5xl font-extrabold text-white drop-shadow-lg text-center tracking-wide">
                    InventariSIS
                </h1>
            </div>
            <p class="relative z-10 text-base text-white/90 text-center mt-2.5 flex items-center gap-2 justify-center">
                Sistem Inventaris Internal
            </p>
        </div>

        <!-- Right Side - Login Form -->
        <div class="flex-1 p-10 md:p-12 flex flex-col justify-center bg-white dark:bg-gray-900 transition-colors duration-300">
            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2.5 flex items-center gap-3">
                    <x-heroicon-o-hand-raised class="w-8 h-8 text-[#14a2ba]" />
                    Selamat Datang
                </h2>
                <p class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                    Silakan masuk dengan akun Anda
                </p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border-l-4 border-red-500 p-4 rounded-lg mb-6 animate-slideIn flex items-start gap-3">
                    <x-heroicon-o-exclamation-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-400 border-l-4 border-red-500 p-4 rounded-lg mb-6 animate-slideIn">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                        <div>
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="transition-transform duration-200">
                    <label for="email" class="flex items-center gap-2 mb-2.5 text-gray-800 dark:text-gray-200 font-semibold text-sm">
                        <x-heroicon-o-envelope class="w-4 h-4" />
                        Email / Username
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required 
                            autofocus
                            placeholder="Masukkan email atau username"
                            class="w-full px-4.5 py-4 pl-11 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-base transition-all duration-300 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 placeholder:text-gray-500/60 dark:placeholder:text-gray-500 focus:outline-none focus:border-[#14a2ba] focus:bg-white dark:focus:bg-gray-900 focus:shadow-[0_0_0_4px_rgba(20,162,186,0.15)] focus:-translate-y-0.5"
                        >
                        <x-heroicon-o-user class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" />
                    </div>
                </div>

                <div class="transition-transform duration-200">
                    <label for="password" class="flex items-center gap-2 mb-2.5 text-gray-800 dark:text-gray-200 font-semibold text-sm">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Masukkan password"
                            class="w-full px-4.5 py-4 pl-11 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-base transition-all duration-300 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 placeholder:text-gray-500/60 dark:placeholder:text-gray-500 focus:outline-none focus:border-[#14a2ba] focus:bg-white dark:focus:bg-gray-900 focus:shadow-[0_0_0_4px_rgba(20,162,186,0.15)] focus:-translate-y-0.5"
                        >
                        <x-heroicon-o-key class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500" />
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember"
                            class="w-4.5 h-4.5 cursor-pointer accent-[#14a2ba]"
                        >
                        <label for="remember" class="text-gray-600 dark:text-gray-400 text-sm cursor-pointer select-none flex items-center gap-1.5">
                            <x-heroicon-o-bookmark class="w-4 h-4" />
                            Ingat Saya
                        </label>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-[#14a2ba] to-[#0d7a8f] text-white rounded-lg text-lg font-bold cursor-pointer transition-all duration-300 shadow-lg shadow-[#14a2ba]/30 tracking-wide hover:from-[#0d7a8f] hover:to-[#0a5f6f] hover:shadow-xl hover:shadow-[#14a2ba]/40 hover:-translate-y-1 active:-translate-y-0 flex items-center justify-center gap-2"
                >
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    MASUK
                </button>
            </form>
        </div>
    </div>

    <script>
        // Check for saved dark mode preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        }

        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);
        }
    </script>
</body>
</html>
