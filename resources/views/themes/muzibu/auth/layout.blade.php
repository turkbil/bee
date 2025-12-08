<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title', 'Muzibu - Giriş')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Alpine.js: Livewire already includes Alpine, no need for CDN --}}

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'muzibu-black': '#121212',
                        'muzibu-dark': '#181818',
                        'muzibu-coral': '#ff7f50',
                        'muzibu-coral-light': '#ff9770',
                        'muzibu-coral-dark': '#ff6a3d',
                        'muzibu-gray': '#282828',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slideIn 0.4s ease-out; }
        [x-cloak] { display: none !important; }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-muzibu-coral via-orange-500 to-red-500 dark:from-muzibu-black dark:via-muzibu-dark dark:to-black transition-colors duration-300" x-data="authApp()" x-init="init()" :class="darkMode ? 'dark' : ''" x-cloak>
    <!-- Dark/Light Mode Toggle -->
    <div class="fixed top-6 right-6 z-50">
        <button @click="toggleDarkMode()" class="w-12 h-12 rounded-full bg-white/30 dark:bg-white/10 backdrop-blur-lg border border-white/50 dark:border-white/30 flex items-center justify-center hover:scale-110 transition-all shadow-xl">
            <i :class="darkMode ? 'fas fa-sun text-yellow-300' : 'fas fa-moon text-white'" class="text-lg"></i>
        </button>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo & Back to Home -->
            <div class="text-center mb-8 slide-in">
                <a href="/" class="inline-flex items-center justify-center gap-3 mb-6 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-muzibu-coral to-muzibu-coral-dark dark:from-muzibu-coral-light dark:to-muzibu-coral rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-all">
                        <i class="fas fa-music text-white text-2xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-white drop-shadow-lg">Muzibu</span>
                </a>
                <p class="text-white/90 dark:text-gray-300 text-lg font-medium">@yield('subtitle', 'İşletmenize Yasal ve Telifsiz Müzik')</p>
            </div>

            <!-- Auth Card -->
            <div class="bg-white dark:bg-muzibu-dark rounded-3xl shadow-2xl p-8 slide-in backdrop-blur-xl border border-white/20 dark:border-white/10">
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

    {{-- Device Limit Modal - NOT NEEDED in auth pages (only in main app) --}}

    @livewireScripts

    <script>
        function authApp() {
            return {
                darkMode: false,
                showPassword: false,

                init() {
                    // Try to get dark mode from localStorage (with error handling)
                    try {
                        this.darkMode = localStorage.getItem('darkMode') === 'true';
                        if (this.darkMode) {
                            document.documentElement.classList.add('dark');
                        }
                    } catch (e) {
                        // localStorage not available (iframe, private mode, etc.)
                        console.warn('localStorage not available:', e.message);
                    }
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;

                    try {
                        localStorage.setItem('darkMode', this.darkMode);
                    } catch (e) {
                        // localStorage not available, just toggle visually
                        console.warn('Cannot save dark mode preference:', e.message);
                    }

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
