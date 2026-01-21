<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- FOUC Prevention - Dark Mode Default --}}
    <script>
        if (localStorage.getItem('varilsan-dark-mode') !== 'false') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
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

    {{-- DNS Prefetch & Preconnect for Performance --}}
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://unpkg.com">

    {{-- Fonts: Outfit (Modern Headings) + DM Sans (Clean Body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Preload Critical Assets --}}
    <link rel="preload" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('js/instantpage.js') }}" as="script" crossorigin>

    {{-- Tailwind CSS CDN with Typography Plugin --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Outfit', 'sans-serif'],
                        'body': ['DM Sans', 'sans-serif'],
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

    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    {{-- GLightbox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css">

    {{-- Varilsan Custom Styles --}}
    <style>
        [x-cloak] { display: none !important; }

        /* ===== DESIGN TOKENS ===== */
        :root {
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --primary-light: #e0f2fe;
            --accent: #fac312;
            --accent-hover: #d4a410;
            --dark-bg: #0f172a;
            --dark-surface: #1e293b;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            line-height: 1.7;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }
        /* Hero büyük başlıklar - üst (Ç,Ü,Ö) ve alt (g,y,p) karakterler için */
        .hero-title {
            line-height: 1.35;
        }
        .gradient-text-hero {
            line-height: 1.35;
            padding-bottom: 0.08em;
        }
        p { line-height: 1.75; }
        html { scroll-behavior: smooth; }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #1e293b; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #0284c7, #0ea5e9); border-radius: 10px; }

        /* Gradient Shift Button */
        .gradient-shift {
            background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #0284c7 100%);
            background-size: 200% 200%;
            background-position: 0% 50%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .gradient-shift:hover {
            background-position: 100% 50%;
        }

        /* Card Border Hover */
        .card-shadow {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }
        .card-shadow:hover {
            border-color: #0ea5e9;
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.2);
        }

        /* Link Hover Effect */
        .link-hover {
            position: relative;
            transition: all 0.3s ease;
        }
        .link-hover::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #0ea5e9, #38bdf8, #06b6d4);
            transition: width 0.3s ease;
        }
        .link-hover:hover::after {
            width: 100%;
        }
        .link-hover:hover {
            color: #0ea5e9;
        }

        /* Icon Border Hover */
        .icon-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }
        .icon-hover:hover {
            border-color: #0ea5e9;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.25);
        }

        /* Button Hover */
        .btn-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-hover::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }
        .btn-hover:hover::before {
            left: 100%;
        }

        /* Hero Swiper */
        .hero-swiper .swiper-slide {
            transition: opacity 0.8s ease-in-out !important;
        }
        .hero-swiper .swiper-pagination-bullet { width: 40px; height: 4px; border-radius: 2px; background: rgba(255,255,255,0.3); transition: all 0.3s ease; }
        .hero-swiper .swiper-pagination-bullet-active { background: #0ea5e9; width: 60px; }

        /* ===== MEGA MENU CARDS ===== */
        .mega-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }
        .mega-card:hover {
            border-color: #0ea5e9;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.15);
        }

        /* Animated Background */
        .animated-mesh { position: absolute; inset: 0; overflow: hidden; }
        .animated-mesh::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background:
                radial-gradient(circle at 20% 30%, rgba(2, 132, 199, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(14, 165, 233, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(250, 195, 18, 0.1) 0%, transparent 40%);
            animation: meshMove 20s ease-in-out infinite;
        }
        @keyframes meshMove {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-5%, 5%) rotate(2deg); }
            50% { transform: translate(5%, -5%) rotate(-2deg); }
            75% { transform: translate(-3%, -3%) rotate(1deg); }
        }

        /* Gradient Text - Kontrastlı & Yavaş */
        @keyframes gradientSlide {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Light Mode - Geniş kontrastlı aralık */
        .gradient-text,
        .gradient-text-hero {
            background: linear-gradient(90deg,
                #075985,      /* sky-800 */
                #0284c7,      /* sky-600 */
                #38bdf8,      /* sky-400 (açık) */
                #06b6d4,      /* cyan-500 (vurgu) */
                #38bdf8,      /* sky-400 */
                #0284c7,      /* sky-600 */
                #075985       /* sky-800 */
            );
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientSlide 8s linear infinite;
        }

        /* Dark Mode - Açık renkler + beyaz */
        .dark .gradient-text,
        .dark .gradient-text-hero {
            background: linear-gradient(90deg,
                #0369a1,      /* sky-700 */
                #0ea5e9,      /* sky-500 */
                #7dd3fc,      /* sky-300 */
                #ffffff,      /* beyaz (parlak) */
                #7dd3fc,      /* sky-300 */
                #0ea5e9,      /* sky-500 */
                #0369a1       /* sky-700 */
            );
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientSlide 8s linear infinite;
        }

        /* CTA Gradient Text - Dark background için (beyazlı gradient) */
        .cta-gradient-text {
            background: linear-gradient(90deg,
                #0369a1,
                #0ea5e9,
                #7dd3fc,
                #ffffff,
                #7dd3fc,
                #0ea5e9,
                #0369a1
            );
            background-size: 300% 100%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientSlide 8s linear infinite;
        }

        /* CTA Animated Background */
        .cta-animated-bg {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, transparent 0%, rgba(14, 165, 233, 0.05) 50%, transparent 100%);
            background-size: 400% 400%;
            animation: ctaGradientMove 8s ease infinite;
        }
        .cta-animated-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 80%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
            animation: ctaFloat 6s ease-in-out infinite;
        }
        @keyframes ctaGradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        @keyframes ctaFloat {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.8; }
            50% { transform: translate(20px, -20px) scale(1.1); opacity: 1; }
        }

        /* Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Float Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        /* Pulse Ring */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }
        .pulse-ring {
            position: relative;
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid #0ea5e9;
            animation: pulse-ring 2s ease-out infinite;
        }

        /* Glow Effects */
        .glow-sky {
            box-shadow: 0 0 40px rgba(14, 165, 233, 0.4);
        }
        .glow-cyan {
            box-shadow: 0 0 40px rgba(6, 182, 212, 0.4);
        }
        .glow-emerald {
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.4);
        }

        /* ===== KINETIC MAXIMALIST ANIMATIONS ===== */

        /* Organic Float - Doğal hareket */
        @keyframes organicFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(10px, -15px) rotate(2deg); }
            50% { transform: translate(-5px, 10px) rotate(-1deg); }
            75% { transform: translate(15px, 5px) rotate(1deg); }
        }
        .organic-float {
            animation: organicFloat 12s ease-in-out infinite;
        }
        @keyframes organicFloatReverse {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(-15px, 10px) rotate(-2deg); }
            50% { transform: translate(10px, -5px) rotate(1deg); }
            75% { transform: translate(-5px, -15px) rotate(-1deg); }
        }
        .organic-float-reverse {
            animation: organicFloatReverse 15s ease-in-out infinite;
        }

        /* Kinetic Particles */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(14, 165, 233, 0.6);
            border-radius: 50%;
            filter: blur(1px);
        }
        @keyframes particleFloat1 {
            0%, 100% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translate(100px, -200px); opacity: 0; }
        }
        @keyframes particleFloat2 {
            0%, 100% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translate(-150px, -180px); opacity: 0; }
        }
        @keyframes particleFloat3 {
            0%, 100% { transform: translate(0, 0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translate(80px, 150px); opacity: 0; }
        }
        .particle-1 {
            top: 70%; left: 20%;
            animation: particleFloat1 8s ease-in-out infinite;
        }
        .particle-2 {
            top: 80%; left: 60%;
            animation: particleFloat2 10s ease-in-out infinite;
            animation-delay: -2s;
            background: rgba(6, 182, 212, 0.6);
        }
        .particle-3 {
            top: 30%; left: 80%;
            animation: particleFloat3 12s ease-in-out infinite;
            animation-delay: -4s;
            background: rgba(16, 185, 129, 0.6);
        }
        .particle-4 {
            top: 50%; left: 10%;
            animation: particleFloat1 9s ease-in-out infinite;
            animation-delay: -3s;
            background: rgba(20, 184, 166, 0.6);
        }
        .particle-5 {
            top: 20%; left: 40%;
            animation: particleFloat2 11s ease-in-out infinite;
            animation-delay: -5s;
        }

        /* Orbit Ring Animation */
        @keyframes orbitPulse {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 0.8; }
        }
        .orbit-ring {
            animation: orbitPulse 3s ease-in-out infinite, spin 30s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Orbit Container - Smooth rotation */
        .orbit-container {
            transition: transform 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Orbit Icon - Counter-rotation to stay upright */
        .orbit-icon {
            transition: transform 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Kinetic Text Effect */
        @keyframes kineticShimmer {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
        .kinetic-text {
            background: linear-gradient(
                90deg,
                #38bdf8 0%,
                #0ea5e9 25%,
                #06b6d4 50%,
                #0ea5e9 75%,
                #38bdf8 100%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: kineticShimmer 4s linear infinite;
        }

        /* Active Card Border Colors */
        .border-sky-500\/50 { border-color: rgba(14, 165, 233, 0.5); }
        .border-cyan-500\/50 { border-color: rgba(6, 182, 212, 0.5); }
        .border-emerald-500\/50 { border-color: rgba(16, 185, 129, 0.5); }
        .border-teal-500\/50 { border-color: rgba(20, 184, 166, 0.5); }

        /* Blog Card Border Hover */
        .blog-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }
        .blog-card:hover {
            border-color: #0ea5e9;
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.2);
        }
        .blog-image img { transition: all 0.4s ease; }

        /* Container - Unimad/t-6 Referans */
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
<body x-data="themeData()" :class="{ 'dark': darkMode }"
      class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-body antialiased">

    {{-- Header --}}
    @include('themes.t-7.layouts.header')

    {{-- Main Content --}}
    <main class="flex-1 min-h-[60vh]">
        @yield('content')
        @yield('module_content')
    </main>

    {{-- Footer --}}
    @include('themes.t-7.layouts.footer')

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>

    <script>
        // Theme Data - Dark/Light Mode
        function themeData() {
            return {
                darkMode: localStorage.getItem('varilsan-dark-mode') !== 'false',
                mobileMenu: false,
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('varilsan-dark-mode', this.darkMode);
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },
                init() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            }
        }

        // AOS Animation
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });

        // GLightbox Init
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            closeButton: true
        });
    </script>

    @stack('scripts')

    {{-- instant.page v5.2.0 - Intelligent Preloading --}}
    <script src="{{ asset('js/instantpage.js') }}" type="module"></script>

    {{-- PWA Service Worker Registration --}}
    <x-pwa-registration />
</body>
</html>
