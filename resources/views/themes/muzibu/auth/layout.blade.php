<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="authApp()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu - Giriş')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'spotify-black': '#121212',
                        'spotify-dark': '#181818',
                        'spotify-green': '#1DB954',
                        'spotify-green-light': '#1ed760',
                        'spotify-gray': '#282828',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slideIn 0.4s ease-out; }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 dark:from-spotify-black dark:via-spotify-dark dark:to-black transition-colors duration-300">
    <!-- Dark/Light Mode Toggle -->
    <div class="fixed top-6 right-6 z-50">
        <button @click="toggleDarkMode()" class="w-12 h-12 rounded-full bg-white/20 dark:bg-white/10 backdrop-blur-lg border border-white/30 flex items-center justify-center hover:scale-110 transition-all shadow-xl">
            <i :class="darkMode ? 'fas fa-sun text-yellow-300' : 'fas fa-moon text-blue-900'" class="text-lg"></i>
        </button>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo & Back to Home -->
            <div class="text-center mb-8 slide-in">
                <a href="/" class="inline-flex items-center justify-center gap-3 mb-6 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-spotify-green to-green-600 dark:from-spotify-green-light dark:to-green-500 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-all">
                        <i class="fas fa-music text-white text-2xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-white drop-shadow-lg">Muzibu</span>
                </a>
                <p class="text-white/90 dark:text-gray-300 text-lg font-medium">@yield('subtitle', 'İşletmenize Yasal ve Telifsiz Müzik')</p>
            </div>

            <!-- Auth Card -->
            <div class="bg-white dark:bg-spotify-dark rounded-3xl shadow-2xl p-8 slide-in backdrop-blur-xl border border-white/20 dark:border-white/10">
                @yield('content')
            </div>

            <!-- Footer Links -->
            <div class="text-center mt-6 space-y-3">
                @yield('footer-links')

                <div class="text-white/80 dark:text-gray-400 text-sm">
                    <a href="/" class="hover:text-white dark:hover:text-white transition-colors">← Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Device Limit Modal (Tenant 1001 only) --}}
    @include('themes.muzibu.components.device-limit-modal')

    @livewireScripts

    <script>
        function authApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                showPassword: false,

                init() {
                    // Apply saved theme
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    }
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);

                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },

                togglePasswordVisibility() {
                    this.showPassword = !this.showPassword;
                }
            }
        }
    </script>
</body>
</html>
