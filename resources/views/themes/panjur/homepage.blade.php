<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_title') ?? setting('site_name') }} | {{ setting('site_slogan') ?? 'Profesyonel Hizmet' }}</title>
    <meta name="description" content="{{ setting('site_description') ?? 'Profesyonel panjur tamiri ve montaj hizmetleri.' }}">

    {{-- Favicon --}}
    @php $favicon = setting('site_favicon'); @endphp
    @if($favicon && $favicon !== 'Favicon yok')
    <link rel="icon" type="image/x-icon" href="{{ cdn($favicon) }}">
    @else
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- FontAwesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // Dark mode FOUC prevention
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Inter', 'sans-serif'],
                        'body': ['Roboto', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Roboto', sans-serif; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Inter', sans-serif; }

        .animated-gradient-bg {
            background: linear-gradient(135deg, #1f2937 0%, #111827 25%, #0f172a 50%, #111827 75%, #1f2937 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .gradient-text-animated {
            background: linear-gradient(90deg, #f97316, #ea580c, #fb923c, #f97316);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: textGradientFlow 4s ease infinite;
        }
        @keyframes textGradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-border {
            position: relative;
            background: linear-gradient(135deg, #f97316, #3b82f6);
            padding: 2px;
            border-radius: 1rem;
        }

        [x-cloak] { display: none !important; }

        .icon-hover { transition: all 0.3s ease; }
        .card-hover:hover .icon-hover { transform: scale(1.15); }
        .text-slide { transition: transform 0.3s ease; }
        .card-hover:hover .text-slide { transform: translateX(8px); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .floating { animation: float 3s ease-in-out infinite; }

        .pulse-ring { position: relative; }
        .pulse-ring::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid #f97316;
            animation: pulseRing 2s ease-out infinite;
        }
        @keyframes pulseRing {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        .pattern-overlay {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-gray-800 dark:text-gray-100 transition-colors duration-300"
    x-data="{
        mobileMenu: false,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)
    }"
    x-init="
        $watch('darkMode', val => {
            localStorage.setItem('darkMode', val);
            document.documentElement.classList.toggle('dark', val);
        });
    ">

    @php
        $siteName = setting('site_name') ?? 'Yildirim Panjur';
        $siteSlogan = setting('site_slogan') ?? '30 Yillik Tecrube';
        $siteDescription = setting('site_description') ?? 'Profesyonel panjur tamiri ve montaj hizmetleri.';
        $sitePhone = setting('site_phone') ?? '0212 596 72 30';
        $siteMobile = setting('site_mobile') ?? '0533 687 73 11';
        $siteEmail = setting('site_email') ?? 'info@example.com';
        $siteWhatsapp = setting('site_whatsapp') ?? preg_replace('/[^0-9]/', '', $siteMobile);

        // Logo Service - ixtif pattern
        $logoService = app(\App\Services\LogoService::class);
        $logos = $logoService->getLogos();
        $hasLogo = $logos['has_light'] || $logos['has_dark'];
    @endphp

    {{-- Top Bar --}}
    <div class="bg-gray-900 dark:bg-black text-white py-2">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            <div class="flex flex-wrap items-center justify-between text-sm">
                <div class="flex items-center gap-4 md:gap-6">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center gap-2 hover:text-primary-400 transition-colors">
                        <i class="fat fa-phone"></i>
                        <span class="hidden sm:inline">{{ $sitePhone }}</span>
                    </a>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-center gap-2 hover:text-primary-400 transition-colors">
                        <i class="fat fa-mobile"></i>
                        <span class="hidden sm:inline">{{ $siteMobile }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="hidden md:flex items-center gap-2 text-primary-400">
                        <i class="fat fa-clock"></i>
                        7/24 Hizmet
                    </span>
                    <span class="hidden lg:flex items-center gap-2">
                        <i class="fat fa-location-dot"></i>
                        Istanbul Geneli
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 transition-all duration-300">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            <div class="flex items-center justify-between py-4">
                {{-- Logo - Gercek logo varsa SADECE logo, yoksa icon + yazi --}}
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    @if($hasLogo)
                        {{-- Gercek logo var - sadece logo goster, yazi YOK --}}
                        @if($logos['has_light'] && $logos['has_dark'])
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="dark:hidden h-10 w-auto">
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="hidden dark:block h-10 w-auto">
                        @elseif($logos['has_light'])
                            <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @elseif($logos['has_dark'])
                            <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                        @endif
                    @else
                        {{-- Logo yok - icon + yazi goster --}}
                        <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-primary-500/30 transition-all duration-300">
                            <i class="fat fa-blinds text-white text-2xl group-hover:scale-110 transition-transform"></i>
                        </div>
                        <div>
                            <span class="text-xl font-bold font-heading text-gray-900 dark:text-white">{{ $siteName }}</span>
                            @if($siteSlogan)
                                <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">{{ $siteSlogan }}</p>
                            @endif
                        </div>
                    @endif
                </a>

                {{-- Desktop Navigation --}}
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="#" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-colors">Ana Sayfa</a>
                    <a href="#hizmetler" class="font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Hizmetlerimiz</a>
                    <a href="#hakkimizda" class="font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Hakkimizda</a>
                    <a href="#iletisim" class="font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Iletisim</a>
                </nav>

                {{-- CTA Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                        <i :class="darkMode ? 'fat fa-sun' : 'fat fa-moon'" class="text-lg"></i>
                    </button>

                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium rounded-lg hover:from-primary-700 hover:to-primary-600 transition-all duration-300 shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40">
                        <i class="fat fa-phone"></i>
                        <span>Hemen Ara</span>
                    </a>
                    <a href="https://wa.me/{{ $siteWhatsapp }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium rounded-lg hover:from-green-700 hover:to-green-600 transition-all duration-300 shadow-lg shadow-green-500/25 hover:shadow-green-500/40">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp</span>
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-lg bg-gray-100 dark:bg-slate-800">
                    <i :class="mobileMenu ? 'fat fa-xmark' : 'fat fa-bars'" class="text-xl text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="lg:hidden bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800">
            <div class="container mx-auto py-4">
                <nav class="flex flex-col gap-3">
                    <a href="#" class="py-2 px-4 font-medium text-primary-600 bg-primary-50 dark:bg-primary-900/20 rounded-lg">Ana Sayfa</a>
                    <a href="#hizmetler" @click="mobileMenu = false" class="py-2 px-4 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800 rounded-lg">Hizmetlerimiz</a>
                    <a href="#hakkimizda" @click="mobileMenu = false" class="py-2 px-4 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800 rounded-lg">Hakkimizda</a>
                    <a href="#iletisim" @click="mobileMenu = false" class="py-2 px-4 font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800 rounded-lg">Iletisim</a>
                </nav>
                <div class="flex flex-col gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-slate-800">
                    {{-- Mobile Dark Mode --}}
                    <button @click="darkMode = !darkMode" class="flex items-center justify-center gap-2 py-3 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-gray-300 font-medium rounded-lg">
                        <i :class="darkMode ? 'fat fa-sun' : 'fat fa-moon'"></i>
                        <span x-text="darkMode ? 'Aydinlik Mod' : 'Karanlik Mod'"></span>
                    </button>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium rounded-lg">
                        <i class="fat fa-phone"></i>
                        <span>{{ $sitePhone }}</span>
                    </a>
                    <a href="https://wa.me/{{ $siteWhatsapp }}" target="_blank" class="flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium rounded-lg">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp ile Ulasin</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative min-h-[90vh] flex items-center overflow-hidden bg-gradient-to-br from-gray-50 via-orange-50 to-gray-100 dark:from-gray-900 dark:via-slate-900 dark:to-gray-900">
        {{-- Floating Elements --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-64 h-64 bg-primary-500/20 dark:bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/20 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-100 dark:bg-white/10 rounded-full mb-8" data-aos="fade-down">
                    <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                    <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">{{ $siteSlogan }}</span>
                </div>

                {{-- Main Heading --}}
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold font-heading mb-6 leading-tight" data-aos="fade-up" data-aos-delay="100">
                    <span class="bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">Panjur Tamiri</span><br>
                    <span class="text-gray-900 dark:text-white">ve Montaj Hizmetleri</span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mb-10 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    Istanbul'un tum bolgelerinde <strong class="text-gray-900 dark:text-white">7/24</strong> panjur tamiri, motorlu panjur sistemleri, sineklik ve garaj kapisi hizmetleri.
                    <span class="text-primary-600 dark:text-primary-400 font-medium">Ucretsiz kesif</span> ve <span class="text-primary-600 dark:text-primary-400 font-medium">5 yil garanti</span>.
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12" data-aos="fade-up" data-aos-delay="300">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="group flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold text-lg rounded-xl hover:from-primary-700 hover:to-primary-600 transition-all duration-300 shadow-xl shadow-primary-500/30 hover:shadow-primary-500/50 hover:scale-105">
                        <i class="fat fa-phone text-xl group-hover:rotate-12 transition-transform"></i>
                        <span>Acil Servis</span>
                    </a>
                    <a href="#hizmetler" class="flex items-center gap-3 px-8 py-4 bg-white dark:bg-white/10 text-gray-900 dark:text-white font-semibold text-lg rounded-xl hover:bg-gray-100 dark:hover:bg-white/20 transition-all duration-300 border border-gray-200 dark:border-white/20 shadow-lg">
                        <i class="fat fa-grid-2 text-xl"></i>
                        <span>Hizmetlerimiz</span>
                    </a>
                </div>

                {{-- Trust Badges --}}
                <div class="flex flex-wrap items-center justify-center gap-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center gap-2 bg-white dark:bg-white/10 px-4 py-2 rounded-lg shadow-md dark:shadow-none border border-gray-100 dark:border-white/10">
                        <i class="fat fa-shield-check text-primary-500 text-xl"></i>
                        <span class="text-gray-700 dark:text-white text-sm font-medium">5 Yil Garanti</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white dark:bg-white/10 px-4 py-2 rounded-lg shadow-md dark:shadow-none border border-gray-100 dark:border-white/10">
                        <i class="fat fa-clock text-primary-500 text-xl"></i>
                        <span class="text-gray-700 dark:text-white text-sm font-medium">7/24 Hizmet</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white dark:bg-white/10 px-4 py-2 rounded-lg shadow-md dark:shadow-none border border-gray-100 dark:border-white/10">
                        <i class="fat fa-hand-holding-dollar text-primary-500 text-xl"></i>
                        <span class="text-gray-700 dark:text-white text-sm font-medium">Ucretsiz Kesif</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
            <span class="text-gray-400 dark:text-white/50 text-xs uppercase tracking-widest">Kaydir</span>
            <i class="fat fa-chevron-down text-gray-400 dark:text-white/50"></i>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-8 bg-gradient-to-r from-primary-600 via-primary-500 to-primary-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div class="text-center" data-aos="zoom-in" data-aos-delay="0">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">30</div>
                    <div class="text-white/80 text-sm md:text-base">Yil Tecrube</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">5</div>
                    <div class="text-white/80 text-sm md:text-base">Yil Garanti</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">10.000+</div>
                    <div class="text-white/80 text-sm md:text-base">Mutlu Musteri</div>
                </div>
                <div class="text-center" data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-1">7/24</div>
                    <div class="text-white/80 text-sm md:text-base">Kesintisiz Destek</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section id="hizmetler" class="py-16 md:py-24 bg-gray-50 dark:bg-slate-900">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Hizmetlerimiz</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Profesyonel <span class="bg-gradient-to-r from-primary-600 to-primary-500 bg-clip-text text-transparent">Panjur Cozumleri</span>
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400" data-aos="fade-up" data-aos-delay="200">
                    Istanbul genelinde panjur tamiri, motorlu sistemler ve daha fazlasi
                </p>
            </div>

            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                {{-- Service 1: Panjur Tamiri --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="0">
                    <div class="gradient-border h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-primary-500/30 icon-hover">
                                <i class="fat fa-screwdriver-wrench text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Panjur Tamiri</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Ayni gun tamir garantisi ile tum panjur arizalariniza hizli ve kalici cozumler sunuyoruz.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-primary-500"></i>
                                    <span>Ayni gun tamir</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-primary-500"></i>
                                    <span>Ucretsiz kesif</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-primary-500"></i>
                                    <span>Orijinal parca</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Service 2: Motorlu Panjur --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-[2px] bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30 icon-hover">
                                <i class="fat fa-gears text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Motorlu Panjur</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Avrupa standartlarinda, uzaktan kumandali motorlu panjur sistemleri kurulumu.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-blue-500"></i>
                                    <span>Kaliteli motor</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-blue-500"></i>
                                    <span>Uzaktan kumanda</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-blue-500"></i>
                                    <span>5 yil garanti</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Service 3: Sineklik --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-[2px] bg-gradient-to-br from-green-500 to-green-600 rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-green-500/30 icon-hover">
                                <i class="fat fa-shield-virus text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Sineklik Sistemleri</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Plise, surgulu ve sabit sineklik sistemleri ile evinizi koruyun.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-green-500"></i>
                                    <span>Ozel olcum</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-green-500"></i>
                                    <span>Dayanikli malzeme</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-green-500"></i>
                                    <span>Hizli montaj</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Service 4: Garaj Kapisi --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="0">
                    <div class="p-[2px] bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-purple-500/30 icon-hover">
                                <i class="fat fa-warehouse text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Garaj Kapilari</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Otomatik garaj kapisi montaj ve tamir hizmetleri.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-purple-500"></i>
                                    <span>Motorlu sistemler</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-purple-500"></i>
                                    <span>Guvenlik sensorleri</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-purple-500"></i>
                                    <span>Profesyonel montaj</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Service 5: Guvenli Panjur --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-[2px] bg-gradient-to-br from-red-500 to-red-600 rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-red-500/30 icon-hover">
                                <i class="fat fa-lock text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Guvenli Panjur</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Takviyeli guvenlik sistemleri ile evinizi ve isyerinizi koruyun.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-red-500"></i>
                                    <span>Takviyeli malzeme</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-red-500"></i>
                                    <span>Alarm entegrasyonu</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-red-500"></i>
                                    <span>Yuksek guvenlik</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-red-600 dark:text-red-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Service 6: Manuel Panjur --}}
                <div class="card-hover group" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-[2px] bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl h-full">
                        <div class="bg-white dark:bg-slate-800 rounded-[14px] p-6 md:p-8 h-full">
                            <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-500/30 icon-hover">
                                <i class="fat fa-hand text-white text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold font-heading text-gray-900 dark:text-white mb-3 text-slide">Manuel Ip Panjur</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">Klasik sistem uretim ve montaj ile ekonomik cozumler.</p>
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-amber-500"></i>
                                    <span>Uygun fiyat</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-amber-500"></i>
                                    <span>Kolay kullanim</span>
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fat fa-check text-amber-500"></i>
                                    <span>Dayanikli yapi</span>
                                </li>
                            </ul>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="inline-flex items-center gap-2 text-amber-600 dark:text-amber-400 font-medium hover:gap-3 transition-all">
                                <span>Hemen Arayin</span>
                                <i class="fat fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Why Us Section --}}
    <section id="hakkimizda" class="py-16 md:py-24 bg-white dark:bg-slate-800">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Neden Biz?</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Neden <span class="bg-gradient-to-r from-primary-600 to-blue-600 bg-clip-text text-transparent">{{ $siteName }}?</span>
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400" data-aos="fade-up" data-aos-delay="200">
                    30 yillik tecrubemizle Istanbul'un guvenilir panjur ustasi
                </p>
            </div>

            {{-- Features Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-primary-500/30 icon-hover">
                        <i class="fat fa-medal text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">30 Yil Tecrube</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Sektorde uzun sureli deneyim ve guvenilirlik</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-blue-500/30 icon-hover">
                        <i class="fat fa-shield-halved text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">5 Yil Garanti</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Tum urun ve hizmetlerde guvence</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-green-500/30 icon-hover">
                        <i class="fat fa-hand-holding-dollar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Ucretsiz Kesif</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Olcum ve fiyat teklifi tamamen bedava</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="0">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-purple-500/30 icon-hover">
                        <i class="fat fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Hizli Servis</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Ayni gun tamir garantisi</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-red-300 dark:hover:border-red-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-red-500/30 icon-hover">
                        <i class="fat fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Uzman Ekip</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Deneyimli ve profesyonel teknisyenler</p>
                </div>

                <div class="card-hover group text-center p-6 md:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 hover:border-amber-300 dark:hover:border-amber-700 transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 mx-auto bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl flex items-center justify-center mb-5 shadow-lg shadow-amber-500/30 icon-hover">
                        <i class="fat fa-map-location-dot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2 text-slide">Istanbul Geneli</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Tum ilcelere hizli ulasim</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section class="py-16 md:py-24 bg-gray-50 dark:bg-slate-900">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16">
                <span class="inline-block px-4 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-sm font-medium rounded-full mb-4" data-aos="fade-up">Surec</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-gray-900 dark:text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Nasil <span class="bg-gradient-to-r from-primary-600 to-blue-600 bg-clip-text text-transparent">Calisir?</span>
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400" data-aos="fade-up" data-aos-delay="200">
                    4 kolay adimda panjur sorununuzu cozuyoruz
                </p>
            </div>

            {{-- Steps --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center" data-aos="fade-up" data-aos-delay="0">
                    <div class="relative inline-block mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-primary-500/30 rotate-3 hover:rotate-0 transition-transform">
                            1
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-primary-300 dark:text-primary-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Bizi Arayin</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">7/24 iletisim hattimizdan ulasin</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative inline-block mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-blue-500/30 -rotate-3 hover:rotate-0 transition-transform">
                            2
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-blue-300 dark:text-blue-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Ucretsiz Kesif</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Ekibimiz adresinize gelir</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="relative inline-block mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-green-500/30 rotate-3 hover:rotate-0 transition-transform">
                            3
                        </div>
                        <div class="hidden lg:flex absolute -right-12 top-1/2 -translate-y-1/2 text-green-300 dark:text-green-600">
                            <i class="fat fa-arrow-right text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Fiyat Teklifi</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Net ve uygun fiyat alin</p>
                </div>

                <div class="text-center" data-aos="fade-up" data-aos-delay="300">
                    <div class="relative inline-block mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold shadow-xl shadow-purple-500/30 -rotate-3 hover:rotate-0 transition-transform">
                            4
                        </div>
                    </div>
                    <h3 class="text-lg font-bold font-heading text-gray-900 dark:text-white mb-2">Hizli Cozum</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Ayni gun tamir garantisi</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact CTA Section --}}
    <section id="iletisim" class="py-16 md:py-24 bg-gradient-to-r from-primary-600 via-primary-500 to-primary-600 relative overflow-hidden">
        <div class="absolute inset-0 pattern-overlay opacity-30"></div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-10 right-10 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mb-8" data-aos="zoom-in">
                    <i class="fat fa-phone-volume text-white text-4xl floating"></i>
                </div>

                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold font-heading text-white mb-4" data-aos="fade-up" data-aos-delay="100">
                    Hemen Arayin, Hizli Cozum Alin!
                </h2>
                <p class="text-lg md:text-xl text-white/90 mb-10 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    7/24 acil servis hattimizdan bize ulasin. Ucretsiz kesif ve uygun fiyat garantisi.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="group flex items-center gap-4 px-8 py-5 bg-white text-primary-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fat fa-phone-rotary text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-gray-500 block">Sabit Hat</span>
                            <span>{{ $sitePhone }}</span>
                        </div>
                    </a>

                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="group flex items-center gap-4 px-8 py-5 bg-white text-primary-600 font-bold text-lg rounded-xl hover:bg-gray-100 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fat fa-mobile text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-gray-500 block">Mobil Hat</span>
                            <span>{{ $siteMobile }}</span>
                        </div>
                    </a>

                    <a href="https://wa.me/{{ $siteWhatsapp }}" target="_blank" class="group flex items-center gap-4 px-8 py-5 bg-green-600 text-white font-bold text-lg rounded-xl hover:bg-green-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <i class="fab fa-whatsapp text-2xl group-hover:animate-pulse"></i>
                        <div class="text-left">
                            <span class="text-xs text-white/80 block">WhatsApp</span>
                            <span>Mesaj Gonder</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 dark:bg-black text-white pt-16 pb-8">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-12">
                {{-- Column 1: About --}}
                <div>
                    {{-- Logo - Gercek logo varsa SADECE logo, yoksa icon + yazi --}}
                    <div class="flex items-center gap-3 mb-6">
                        @if($hasLogo)
                            {{-- Gercek logo var - sadece logo goster --}}
                            @if($logos['has_light'])
                                <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto brightness-0 invert">
                            @elseif($logos['has_dark'])
                                <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                            @endif
                        @else
                            {{-- Logo yok - icon + yazi goster --}}
                            <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center">
                                <i class="fat fa-blinds text-white text-2xl"></i>
                            </div>
                            @if($siteName)
                            <div>
                                <span class="text-xl font-bold font-heading">{{ $siteName }}</span>
                            </div>
                            @endif
                        @endif
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        {{ $siteDescription }}
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600/20 border border-primary-600/30 rounded-lg">
                        <i class="fat fa-shield-check text-primary-400"></i>
                        <span class="text-sm font-medium text-primary-400">5 Yil Garanti</span>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div>
                    <h4 class="text-lg font-bold font-heading mb-6">Hizli Erisim</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Ana Sayfa</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Hizmetlerimiz</a></li>
                        <li><a href="#hakkimizda" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Hakkimizda</a></li>
                        <li><a href="#iletisim" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Iletisim</a></li>
                    </ul>
                </div>

                {{-- Column 3: Services --}}
                <div>
                    <h4 class="text-lg font-bold font-heading mb-6">Hizmetlerimiz</h4>
                    <ul class="space-y-3">
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Panjur Tamiri</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Motorlu Panjur</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Sineklik Sistemleri</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Garaj Kapilari</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Guvenli Panjur</a></li>
                        <li><a href="#hizmetler" class="text-gray-400 hover:text-primary-400 transition-colors flex items-center gap-2"><i class="fat fa-chevron-right text-xs text-primary-500"></i> Manuel Ip Panjur</a></li>
                    </ul>
                </div>

                {{-- Column 4: Contact --}}
                <div>
                    <h4 class="text-lg font-bold font-heading mb-6">Iletisim</h4>
                    <ul class="space-y-4">
                        <li>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                                <i class="fat fa-phone text-primary-400 mt-1"></i>
                                <div>
                                    <span class="text-xs text-gray-500 block">Sabit Hat</span>
                                    <span>{{ $sitePhone }}</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                                <i class="fat fa-mobile text-primary-400 mt-1"></i>
                                <div>
                                    <span class="text-xs text-gray-500 block">Mobil Hat</span>
                                    <span>{{ $siteMobile }}</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="mailto:{{ $siteEmail }}" class="flex items-start gap-3 text-gray-400 hover:text-primary-400 transition-colors">
                                <i class="fat fa-envelope text-primary-400 mt-1"></i>
                                <div>
                                    <span class="text-xs text-gray-500 block">E-posta</span>
                                    <span>{{ $siteEmail }}</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="flex items-start gap-3 text-gray-400">
                                <i class="fat fa-location-dot text-primary-400 mt-1"></i>
                                <div>
                                    <span class="text-xs text-gray-500 block">Hizmet Bolgesi</span>
                                    <span>Istanbul - Tum Ilceler</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Footer Bottom --}}
            <div class="pt-8 border-t border-gray-800">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-gray-500 text-sm text-center md:text-left">
                        &copy; {{ date('Y') }} {{ $siteName }}. Tum haklari saklidir.
                    </p>
                    <p class="text-gray-500 text-sm text-center md:text-right">
                        <span class="text-primary-400">30 yillik tecrube</span> |
                        <span class="text-primary-400">5 yil garanti</span> |
                        <span class="text-primary-400">Ucretsiz kesif</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    {{-- Floating WhatsApp Button --}}
    <a href="https://wa.me/{{ $siteWhatsapp }}" target="_blank" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3 bg-green-600 text-white rounded-full shadow-xl hover:bg-green-700 hover:scale-110 transition-all duration-300 group">
        <i class="fab fa-whatsapp text-2xl"></i>
        <span class="hidden sm:inline font-medium">WhatsApp</span>
    </a>

    {{-- Initialize AOS --}}
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    </script>
</body>
</html>
