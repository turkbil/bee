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
                            '50': '#fff8f5',
                            '100': '#ffefe8',
                            '200': '#ffddd0',
                            '300': '#ffc4ad',
                            '400': '#ff9966',
                            '500': '#ff7f50',
                            '600': '#ff6633',
                            '700': '#e55528',
                            '800': '#c44520',
                            '900': '#a33a1c',
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
        <div class="w-full max-w-lg">
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

    {{-- Page Content Modal - İletişim Sayfası Stili --}}
    <div x-show="showPageModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         @click.self="closePageModal()"
         style="display: none;"
         x-cloak>

        {{-- Modal Content --}}
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-4xl max-h-[90vh] bg-dark-800 rounded-2xl shadow-2xl border border-dark-600 overflow-hidden"
             @click.stop>

            {{-- Modal Header --}}
            <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-6 bg-gradient-to-r from-mz-500 to-mz-600">
                <h3 class="text-2xl font-black text-white pr-8" x-text="pageModalTitle"></h3>
                <button @click="closePageModal()"
                        class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/30 text-white transition-all duration-200 hover:scale-110">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-8 py-6">
                <div x-show="loadingPageContent" class="flex items-center justify-center py-12">
                    <i class="fa-solid fa-spinner fa-spin text-4xl text-mz-500"></i>
                </div>
                <div x-show="!loadingPageContent"
                     class="prose prose-lg max-w-none dark:prose-invert
                          prose-headings:text-white
                          prose-p:text-gray-300
                          prose-a:text-mz-400 hover:prose-a:text-mz-300
                          prose-strong:text-white
                          prose-ul:text-gray-300
                          prose-ol:text-gray-300"
                     x-html="pageModalContent">
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="sticky bottom-0 z-10 flex items-center justify-end px-8 py-4 bg-gray-900/50 border-t border-gray-700">
                <button @click="closePageModal()"
                        class="px-6 py-3 bg-gradient-to-r from-mz-500 to-mz-600 rounded-xl font-bold text-white hover:shadow-lg hover:shadow-mz-500/30 transition-all duration-200 hover:scale-105">
                    <i class="fa-solid fa-check mr-2"></i>
                    Anladım
                </button>
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

                // Page Content Modal
                showPageModal: false,
                pageModalTitle: '',
                pageModalContent: '',
                loadingPageContent: false,

                init() {
                    // Session flash'tan device limit exceeded kontrolu
                    const deviceData = window.deviceLimitData || null;
                    if (deviceData && deviceData.exceeded) {
                        this.showDeviceModal = true;
                        this.deviceLimit = deviceData.limit || 1;
                        this.otherDevices = deviceData.devices || [];
                        this.intendedUrl = deviceData.intendedUrl || '/';
                    }

                    // Register validation hatası varsa otomatik register tab'ına geç
                    @if($errors->register->any())
                        this.activeTab = 'register';
                    @endif
                },

                async openPageModal(pageId) {
                    this.showPageModal = true;
                    this.pageModalTitle = 'Yükleniyor...';
                    this.pageModalContent = '';
                    this.loadingPageContent = true;

                    try {
                        const response = await fetch(`/api/page-content/${pageId}`);
                        const data = await response.json();

                        if (data.success && data.data) {
                            this.pageModalTitle = data.data.title || 'Sayfa';
                            this.pageModalContent = data.data.body || '<p class="text-gray-300">İçerik bulunamadı.</p>';
                        } else {
                            this.pageModalTitle = 'Hata';
                            this.pageModalContent = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                        }
                    } catch (error) {
                        console.error('Page content load error:', error);
                        this.pageModalTitle = 'Hata';
                        this.pageModalContent = '<p class="text-red-400">İçerik yüklenirken hata oluştu.</p>';
                    } finally {
                        this.loadingPageContent = false;
                    }
                },

                closePageModal() {
                    this.showPageModal = false;
                    this.pageModalTitle = '';
                    this.pageModalContent = '';
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
