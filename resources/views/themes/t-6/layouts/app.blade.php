<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- FOUC Prevention - Dark Mode (Premium feel default) --}}
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    {{-- SEO Meta Tags --}}
    <x-seo-meta />

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon (iOS Safari) --}}
    @php
        $logoService = app(\App\Services\LogoService::class);
        $appleTouchIcon = $logoService->getSchemaLogoUrl();
    @endphp
    @if($appleTouchIcon)
        <link rel="apple-touch-icon" href="{{ $appleTouchIcon }}">
    @endif

    {{-- Fonts: Cinzel (Art Deco Headings) + Lora (Readable Body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800;900&family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN with Typography Plugin --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Cinzel', 'serif'],
                        'body': ['Lora', 'serif'],
                    },
                    colors: {
                        'gold': {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js with Collapse Plugin --}}
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FontAwesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}">

    {{-- AOS Animation --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    {{-- GLightbox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css">

    {{-- Art Deco Custom Styles --}}
    <style>
        [x-cloak] { display: none !important; }

        /* Art Deco Geometric Pattern */
        .art-deco-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f59e0b' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Animated Gradient Text */
        .gradient-text-animate {
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #d97706, #f59e0b);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientFlow 6s ease infinite;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Static Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #d97706 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Gradient Border Card */
        .gradient-border {
            position: relative;
            background: linear-gradient(135deg, #f59e0b, #fbbf24, #d97706);
            padding: 1px;
            border-radius: 0.75rem;
        }

        .gradient-border-inner {
            background: #ffffff;
            border-radius: calc(0.75rem - 1px);
            height: 100%;
        }

        .dark .gradient-border-inner {
            background: #0f172a;
        }

        /* Animated Border */
        .animated-border {
            position: relative;
            background: linear-gradient(90deg, #f59e0b, #fbbf24, #d97706, #f59e0b);
            background-size: 300% 100%;
            padding: 2px;
            border-radius: 0.75rem;
            animation: borderFlow 4s ease infinite;
        }

        @keyframes borderFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animated-border-inner {
            background: #ffffff;
            border-radius: calc(0.75rem - 2px);
            height: 100%;
        }

        .dark .animated-border-inner {
            background: #0f172a;
        }

        /* Art Deco Line */
        .art-deco-line {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #f59e0b 20%, #fbbf24 50%, #f59e0b 80%, transparent 100%);
        }

        /* Art Deco Diamond */
        .art-deco-diamond {
            width: 12px;
            height: 12px;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            transform: rotate(45deg);
        }

        /* Service Card Hover */
        .service-card {
            border: 1px solid transparent;
            transition: all 0.4s ease;
        }

        .service-card:hover {
            border-color: #f59e0b;
            transform: translateY(-4px);
        }

        /* Value Card */
        .value-card {
            position: relative;
            overflow: hidden;
        }

        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 0;
            background: linear-gradient(180deg, #f59e0b, #fbbf24);
            transition: height 0.4s ease;
        }

        .value-card:hover::before {
            height: 100%;
        }

        /* Icon Hover Effect */
        .icon-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover .icon-hover {
            transform: scale(1.1);
        }

        /* Underline Animation */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Button Shine Effect */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-shine:hover::before {
            left: 100%;
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        /* Art Deco Corner */
        .art-deco-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 2px solid #f59e0b;
        }

        .art-deco-corner-tl {
            top: 0;
            left: 0;
            border-right: none;
            border-bottom: none;
        }

        .art-deco-corner-tr {
            top: 0;
            right: 0;
            border-left: none;
            border-bottom: none;
        }

        .art-deco-corner-bl {
            bottom: 0;
            left: 0;
            border-right: none;
            border-top: none;
        }

        .art-deco-corner-br {
            bottom: 0;
            right: 0;
            border-left: none;
            border-top: none;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .dark ::-webkit-scrollbar-track {
            background: #0f172a;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #f59e0b, #d97706);
            border-radius: 4px;
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Mobile Menu Animation */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Container - Unimad Referans (DOKUNMA!) */
        .container {
            max-width: 100% !important;
            padding-left: 1.25rem !important;
            padding-right: 1.25rem !important;
        }
        @media (min-width: 768px) {
            .container {
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }
        @media (min-width: 1024px) {
            .container {
                padding-left: 3rem !important;
                padding-right: 3rem !important;
            }
        }
        @media (min-width: 1280px) {
            .container {
                max-width: 1280px !important;
                padding-left: 2rem !important;
                padding-right: 2rem !important;
            }
        }
        @media (min-width: 1536px) {
            .container {
                max-width: 1536px !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ mobileMenu: false }"
      class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-body antialiased transition-colors duration-300">

    {{-- Header --}}
    @include('themes.t-6.layouts.header')

    {{-- Main Content --}}
    <main class="flex-1 min-h-[60vh]">
        @yield('content')
        @yield('module_content')
    </main>

    {{-- Footer --}}
    @include('themes.t-6.layouts.footer')

    {{-- AOS Init --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>

    {{-- GLightbox Init --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            closeButton: true
        });
    </script>

    {{-- Theme Toggle Script --}}
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        }

        // Icon hover effect (thin to solid)
        document.querySelectorAll('.service-card, .value-card, .gradient-border').forEach(card => {
            const icon = card.querySelector('.icon-hover');
            if (icon && icon.classList.contains('fat')) {
                card.addEventListener('mouseenter', () => {
                    icon.classList.remove('fat');
                    icon.classList.add('fas');
                });
                card.addEventListener('mouseleave', () => {
                    icon.classList.remove('fas');
                    icon.classList.add('fat');
                });
            }
        });
    </script>

    @stack('scripts')

    {{-- instant.page v5.2.0 - Intelligent Preloading --}}
    <script src="{{ asset('js/instantpage.js') }}" type="module"></script>

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />
</body>
</html>
