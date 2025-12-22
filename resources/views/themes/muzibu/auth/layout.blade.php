<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Muzibu')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    }
                }
            }
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #52525b; }
    </style>

    @livewireStyles
</head>
<body class="min-h-screen bg-gradient-to-br from-dark-900 via-dark-800 to-dark-900 font-sans antialiased" x-data="authApp()" x-cloak data-active-tab="@yield('active-tab', 'login')">

    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-mz-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-mz-600/10 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Content -->
    <div class="relative min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            @yield('content')

            <!-- Footer -->
            <div class="mt-8 text-center">
                <a href="/" class="inline-flex items-center gap-2 text-dark-300 hover:text-white text-sm transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    Ana Sayfaya Don
                </a>
            </div>
        </div>
    </div>

    @livewireScripts

    <script>
        function authApp() {
            return {
                activeTab: document.body.dataset.activeTab || 'login',
                loading: false,
                showPassword: false,
                showPasswordConfirm: false,
                passwordStrength: 0,

                // Device Selection Modal
                showDeviceModal: false,
                deviceLimit: 1,
                otherDevices: [],
                selectedDevices: [],
                intendedUrl: '/',
                terminatingDevices: false,
                terminateError: null,

                init() {
                    // Session flash'tan device limit exceeded kontrolu
                    const deviceData = window.deviceLimitData || null;
                    if (deviceData && deviceData.exceeded) {
                        this.showDeviceModal = true;
                        this.deviceLimit = deviceData.limit || 1;
                        this.otherDevices = deviceData.devices || [];
                        this.intendedUrl = deviceData.intendedUrl || '/';
                    }
                },

                getDeviceIcon(deviceType) {
                    switch(deviceType) {
                        case 'mobile': return 'fas fa-mobile-alt';
                        case 'tablet': return 'fas fa-tablet-alt';
                        default: return 'fas fa-desktop';
                    }
                },

                toggleDeviceSelection(sessionId) {
                    const index = this.selectedDevices.indexOf(sessionId);
                    if (index === -1) {
                        this.selectedDevices.push(sessionId);
                    } else {
                        this.selectedDevices.splice(index, 1);
                    }
                },

                isDeviceSelected(sessionId) {
                    return this.selectedDevices.includes(sessionId);
                },

                async terminateSelectedDevices() {
                    if (this.selectedDevices.length === 0) {
                        this.terminateError = 'Lutfen en az bir cihaz secin.';
                        return;
                    }

                    this.terminatingDevices = true;
                    this.terminateError = null;

                    try {
                        const response = await fetch('/api/auth/terminate-devices', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                session_ids: this.selectedDevices
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Basarili - hedef URL'e yonlendir
                            window.location.href = this.intendedUrl;
                        } else {
                            this.terminateError = data.message || 'Cihazlar cikis yaptirilirken hata olustu.';
                        }
                    } catch (error) {
                        console.error('Terminate devices error:', error);
                        this.terminateError = 'Baglanti hatasi. Lutfen tekrar deneyin.';
                    } finally {
                        this.terminatingDevices = false;
                    }
                },

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
