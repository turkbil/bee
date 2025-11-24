<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="authApp()" x-init="init()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'İxtif - Giriş')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'primary': '#3b82f6',
                        'primary-dark': '#2563eb',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .slide-in { animation: slideIn 0.4s ease-out; }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-300">
    <!-- Dark/Light Mode Toggle -->
    <div class="fixed top-6 right-6 z-50">
        <button @click="toggleDarkMode()" class="w-12 h-12 rounded-full bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:scale-110 transition-all">
            <i :class="darkMode ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-blue-600'" class="text-lg"></i>
        </button>
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo & Back to Home -->
            <div class="text-center mb-8 slide-in">
                <a href="/" class="inline-flex items-center justify-center gap-3 mb-6 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-all">
                        <i class="fas fa-industry text-white text-2xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-gray-900 dark:text-white">İxtif</span>
                </a>
                <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">@yield('subtitle', 'Endüstriyel Ekipman Çözümleri')</p>
            </div>

            <!-- Auth Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 slide-in border border-gray-100 dark:border-gray-700">
                @yield('content')
            </div>

            <!-- Footer Links -->
            <div class="text-center mt-6 space-y-3">
                @yield('footer-links')

                <div class="text-gray-600 dark:text-gray-400 text-sm">
                    <a href="/" class="hover:text-gray-900 dark:hover:text-white transition-colors">← Ana Sayfaya Dön</a>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        function authApp() {
            return {
                darkMode: localStorage.getItem('darkMode') === 'true',
                showPassword: false,

                init() {
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
