<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Alpine.js CDN --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'mz': {
                            '50': '#fef7f0',
                            '100': '#fceee0',
                            '200': '#f9d5b3',
                            '300': '#f4b885',
                            '400': '#ed9254',
                            '500': '#e87533',
                            '600': '#d95d24',
                            '700': '#b4471f',
                            '800': '#903a21',
                            '900': '#74321e',
                        },
                        'dark': {
                            '900': '#0a0a0b',
                            '800': '#111113',
                            '700': '#18181b',
                            '600': '#1f1f23',
                            '500': '#27272a',
                            '400': '#3f3f46',
                            '300': '#52525b',
                            '200': '#71717a',
                            '100': '#a1a1aa',
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #e87533 0%, #f4b885 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glow effect */
        .glow {
            box-shadow: 0 0 40px rgba(232, 117, 51, 0.15);
        }

        /* Input focus glow */
        input:focus, select:focus {
            box-shadow: 0 0 0 3px rgba(232, 117, 51, 0.1);
        }

        /* Background pattern */
        .bg-pattern {
            background-image:
                radial-gradient(circle at 20% 50%, rgba(232, 117, 51, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(232, 117, 51, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 40% 80%, rgba(232, 117, 51, 0.03) 0%, transparent 40%);
        }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-dark-900 bg-pattern font-sans antialiased" x-data="authApp()" x-cloak>

    <!-- Ambient Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-mz-500/5 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-mz-600/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="relative min-h-screen flex">

        <!-- Left Side - Branding (Hidden on mobile) -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 flex-col justify-between p-12 relative">
            <!-- Logo -->
            <a href="/" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 bg-gradient-to-br from-mz-500 to-mz-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <i class="fas fa-music text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-white">Muzibu</span>
            </a>

            <!-- Center Content -->
            <div class="space-y-8 animate-fade-in">
                <div>
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight mb-4">
                        Isletmeniz Icin<br>
                        <span class="gradient-text">Profesyonel Muzik</span>
                    </h1>
                    <p class="text-dark-100 text-lg leading-relaxed max-w-md">
                        Lisansli, telifsiz muzik koleksiyonuyla isletmenize prestij katin.
                        Yasal guvenlik, sinirsiz erisim.
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-4">
                    <div class="flex items-center gap-4 text-dark-100">
                        <div class="w-10 h-10 rounded-lg bg-dark-700 flex items-center justify-center">
                            <i class="fas fa-shield-check text-mz-500"></i>
                        </div>
                        <span>%100 Lisansli ve Yasal</span>
                    </div>
                    <div class="flex items-center gap-4 text-dark-100">
                        <div class="w-10 h-10 rounded-lg bg-dark-700 flex items-center justify-center">
                            <i class="fas fa-infinity text-mz-500"></i>
                        </div>
                        <span>Sinirsiz Muzik Erisimi</span>
                    </div>
                    <div class="flex items-center gap-4 text-dark-100">
                        <div class="w-10 h-10 rounded-lg bg-dark-700 flex items-center justify-center">
                            <i class="fas fa-headphones text-mz-500"></i>
                        </div>
                        <span>Profesyonel Playlist'ler</span>
                    </div>
                </div>
            </div>

            <!-- Bottom -->
            <div class="text-dark-300 text-sm">
                &copy; {{ date('Y') }} Muzibu. Tum haklar saklidir.
            </div>
        </div>

        <!-- Right Side - Auth Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">

                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-mz-500 to-mz-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-music text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold text-white">Muzibu</span>
                    </a>
                </div>

                <!-- Auth Card -->
                <div class="bg-dark-800/80 backdrop-blur-xl rounded-2xl border border-dark-600/50 p-8 glow">
                    @yield('content')
                </div>

                <!-- Footer Links -->
                <div class="mt-6 text-center space-y-4">
                    @yield('footer-links')

                    <div class="lg:hidden">
                        <a href="/" class="text-dark-300 hover:text-white text-sm transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Ana Sayfaya Don
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        function authApp() {
            return {
                loading: false,
                showPassword: false,
                showPasswordConfirm: false,
                passwordStrength: 0,

                checkPasswordStrength(password) {
                    let strength = 0;
                    if (password.length >= 8) strength++;
                    if (password.match(/[a-z]/)) strength++;
                    if (password.match(/[A-Z]/)) strength++;
                    if (password.match(/[0-9]/)) strength++;
                    if (password.match(/[^a-zA-Z0-9]/)) strength++;
                    this.passwordStrength = strength;
                },

                getStrengthColor() {
                    if (this.passwordStrength <= 1) return 'bg-red-500';
                    if (this.passwordStrength <= 2) return 'bg-orange-500';
                    if (this.passwordStrength <= 3) return 'bg-yellow-500';
                    if (this.passwordStrength <= 4) return 'bg-green-500';
                    return 'bg-emerald-500';
                },

                getStrengthText() {
                    if (this.passwordStrength <= 1) return 'Cok Zayif';
                    if (this.passwordStrength <= 2) return 'Zayif';
                    if (this.passwordStrength <= 3) return 'Orta';
                    if (this.passwordStrength <= 4) return 'Guclu';
                    return 'Cok Guclu';
                }
            }
        }
    </script>
</body>
</html>
