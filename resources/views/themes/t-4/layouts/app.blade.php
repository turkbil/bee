<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- FOUC Prevention - Dark Mode --}}
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    {{-- SEO Meta Tags --}}
    <x-seo-meta />

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ route('manifest') }}">

    {{-- Apple Touch Icon --}}
    @php
        $logoService = app(\App\Services\LogoService::class);
        $appleTouchIcon = $logoService->getSchemaLogoUrl();
    @endphp
    @if($appleTouchIcon)
        <link rel="apple-touch-icon" href="{{ $appleTouchIcon }}">
    @endif

    {{-- Google Fonts: Outfit + Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                container: {
                    center: true,
                    screens: {
                        sm: '100%',
                        md: '100%',
                        lg: '100%',
                        xl: '1280px',
                        '2xl': '1536px',
                    },
                },
                extend: {
                    fontFamily: {
                        heading: ['Outfit', 'sans-serif'],
                        body: ['Inter', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            50: '#f0f7fc',
                            100: '#dceaf5',
                            200: '#b8d5eb',
                            300: '#8ebfe0',
                            400: '#72aed4',
                            500: '#5590CF',
                            600: '#4a7eb8',
                            700: '#3d6a9c',
                            800: '#325680',
                            900: '#2a4a6e',
                            950: '#1e3a5c'
                        },
                        dark: {
                            50: '#f4f5f6',
                            100: '#e4e6e8',
                            200: '#c9cdd1',
                            300: '#a3a9af',
                            400: '#6b7280',
                            500: '#4a5158',
                            600: '#3A3F45',
                            700: '#2f3338',
                            800: '#24272b',
                            900: '#1a1c1f',
                            950: '#0f1012'
                        }
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- FontAwesome Pro --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.min.css') }}">

    {{-- AOS Animation --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">

    {{-- GLightbox --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css">

    {{-- Custom Styles - v4 Design (Birebir Kopya) --}}
    <style>
        [x-cloak] { display: none !important; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Smooth Scroll & Prevent Horizontal Overflow */
        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            color: #3A3F45;
            overflow-x: hidden;
        }

        .dark body {
            background: #1a1c1f;
            color: #dceaf5;
        }

        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        /* Icon Hover Effect: Light → Solid (FA7) */
        .fal {
            transition: font-weight 0.2s ease;
        }

        /* Group hover - icon becomes solid (FA7 uses --fa-style) */
        /* Only direct .group hover, not named groups like group/kurumsal */
        .group:hover > .fal,
        .group:hover > i.fal,
        a.group:hover .fal,
        button:hover > .fal,
        .hover-icon-solid:hover .fal {
            --fa-style: 900;
            font-weight: 900;
        }

        /* Card hover - only specific card classes */
        .card:hover .fal,
        .service-card:hover .fal,
        .mega-item:hover .fal {
            --fa-style: 900;
            font-weight: 900;
        }

        /* Design Token: Custom Blue + Dark Gray - v4 */
        .gradient-hero {
            background: linear-gradient(135deg, #3A3F45 0%, #4a5158 50%, #3A3F45 100%);
        }

        .gradient-hero-animated {
            background: linear-gradient(135deg, #3A3F45 0%, #4a5158 25%, #5590CF 50%, #4a5158 75%, #3A3F45 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Animated Text Gradient - High Contrast & Visible */
        @keyframes gradientTextShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .text-gradient-animated {
            background: linear-gradient(135deg, #1e40af, #5590CF, #f59e0b, #1e40af);
            background-size: 300% 300%;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientTextShift 4s ease-in-out infinite;
            display: inline-block;
        }

        .dark .text-gradient-animated {
            background: linear-gradient(135deg, #60a5fa, #fbbf24, #93c5fd, #60a5fa);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientTextShift 4s ease-in-out infinite;
        }

        /* Smooth Background Sections */
        .bg-smooth-light {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 50%, #f1f5f9 100%);
        }

        .bg-smooth-alt {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        }

        .dark .bg-smooth-light {
            background: linear-gradient(180deg, #1a1c1f 0%, #24272b 50%, #2f3338 100%);
        }

        .dark .bg-smooth-alt {
            background: linear-gradient(180deg, #24272b 0%, #2f3338 50%, #3A3F45 100%);
        }

        /* Hero Light Mode - Visible moving gradient */
        .hero-adaptive {
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(85, 144, 207, 0.25) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 80% 70%, rgba(61, 106, 156, 0.20) 0%, transparent 55%),
                radial-gradient(ellipse 60% 40% at 50% 50%, rgba(220, 234, 245, 0.5) 0%, transparent 50%),
                linear-gradient(135deg, #ffffff 0%, #f1f5f9 50%, #e2e8f0 100%);
            background-size: 200% 200%, 200% 200%, 150% 150%, 100% 100%;
            animation: heroGradientLight 20s ease infinite;
        }

        @keyframes heroGradientLight {
            0%, 100% { background-position: 0% 0%, 100% 100%, 50% 50%, 0% 0%; }
            33% { background-position: 100% 50%, 0% 50%, 30% 70%, 0% 0%; }
            66% { background-position: 50% 100%, 50% 0%, 70% 30%, 0% 0%; }
        }

        /* Hero Dark Mode - Visible blue accent movement */
        .dark .hero-adaptive {
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(85, 144, 207, 0.35) 0%, transparent 60%),
                radial-gradient(ellipse 70% 50% at 80% 70%, rgba(61, 106, 156, 0.25) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(47, 51, 56, 0.8) 0%, transparent 50%),
                linear-gradient(135deg, #1a1c1f 0%, #24272b 50%, #1a1c1f 100%);
            background-size: 200% 200%, 200% 200%, 150% 150%, 100% 100%;
            animation: heroGradientDark 25s ease infinite;
        }

        @keyframes heroGradientDark {
            0%, 100% { background-position: 0% 0%, 100% 100%, 50% 50%, 0% 0%; }
            33% { background-position: 100% 50%, 0% 50%, 30% 70%, 0% 0%; }
            66% { background-position: 50% 100%, 50% 0%, 70% 30%, 0% 0%; }
        }

        /* CTA Light/Dark Mode */
        .cta-adaptive {
            background: linear-gradient(135deg, #dceaf5 0%, #b8d5eb 25%, #dceaf5 50%, #b8d5eb 75%, #dceaf5 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }

        .dark .cta-adaptive {
            background: linear-gradient(135deg, #3A3F45 0%, #4a5158 25%, #5590CF 50%, #4a5158 75%, #3A3F45 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
        }

        /* CTA - Shimmer Effect (Metallic Shine) */
        .cta-mesh {
            background: linear-gradient(135deg, #3d6a9c 0%, #5590CF 50%, #4a7eb8 100%);
            position: relative;
            overflow: hidden;
        }

        .cta-mesh::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255,255,255,0.03) 25%,
                rgba(255,255,255,0.15) 50%,
                rgba(255,255,255,0.03) 75%,
                transparent 100%
            );
            transform: skewX(-20deg);
            animation: ctaShimmer 6s ease-in-out infinite;
        }

        .dark .cta-mesh {
            background: linear-gradient(135deg, #1a2f4a 0%, #2a4a6e 50%, #1e3a5c 100%);
        }

        .dark .cta-mesh::before {
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(85,144,207,0.02) 25%,
                rgba(85,144,207,0.12) 50%,
                rgba(85,144,207,0.02) 75%,
                transparent 100%
            );
        }

        @keyframes ctaShimmer {
            0% { left: -100%; }
            50%, 100% { left: 150%; }
        }

        /* Hover Effect #2: Icon Scale */
        .hover-icon-scale .icon-wrapper {
            transition: transform 0.3s ease;
        }
        .hover-icon-scale:hover .icon-wrapper {
            transform: scale(1.15);
        }

        /* Hover Effect #8: Underline Draw */
        .hover-underline {
            position: relative;
        }
        .hover-underline::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: currentColor;
            transition: width 0.3s ease;
        }
        .hover-underline:hover::after {
            width: 100%;
        }

        /* Gradient Border Hover Effect - Elegant */
        .hover-border-gradient {
            position: relative;
            background: white;
            border: 2px solid transparent;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .hover-border-gradient {
            background: #24272b;
        }
        .hover-border-gradient::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(135deg, transparent, transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
        }
        .hover-border-gradient:hover::before {
            background: linear-gradient(135deg, #5590CF 0%, #3d6a9c 25%, #5590CF 50%, #72aed4 75%, #5590CF 100%);
            background-size: 300% 300%;
            animation: borderGradientMove 3s ease infinite;
            opacity: 1;
        }
        @keyframes borderGradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Gradient Border - Always Visible */
        .hover-shadow {
            position: relative;
            border: none;
        }
        .hover-shadow::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(135deg, #94a3b8, #cbd5e1, #94a3b8);
            background-size: 200% 200%;
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            z-index: 1;
            transition: all 0.4s ease;
        }
        .dark .hover-shadow::before {
            background: linear-gradient(135deg, #5590CF, #ffffff40, #5590CF);
        }
        .hover-shadow:hover::before {
            background: linear-gradient(135deg, #5590CF, #3d6a9c, #72aed4, #5590CF);
            background-size: 300% 300%;
            animation: gradientRotate 2s linear infinite;
        }
        .dark .hover-shadow:hover::before {
            background: linear-gradient(135deg, #72aed4, #5590CF, #3d6a9c, #72aed4);
            background-size: 300% 300%;
        }
        @keyframes gradientRotate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated Section Background */
        .bg-animated-mesh {
            background:
                radial-gradient(ellipse at 20% 30%, rgba(85, 144, 207, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(85, 144, 207, 0.05) 0%, transparent 50%),
                linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            animation: meshMove 30s ease-in-out infinite;
        }
        .dark .bg-animated-mesh {
            background:
                radial-gradient(ellipse at 20% 30%, rgba(85, 144, 207, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 70%, rgba(85, 144, 207, 0.1) 0%, transparent 50%),
                linear-gradient(180deg, #1a1c1f 0%, #24272b 100%);
        }
        @keyframes meshMove {
            0%, 100% { background-position: 0% 0%, 100% 100%, 0% 0%; }
            50% { background-position: 100% 100%, 0% 0%, 0% 0%; }
        }

        /* Button Hover Enhancement */
        .btn-primary {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-primary:hover::before {
            left: 100%;
        }

        /* Link Arrow Animation */
        .link-arrow {
            transition: all 0.3s ease;
        }
        .link-arrow:hover {
            gap: 0.75rem;
        }
        .link-arrow i {
            transition: transform 0.3s ease;
        }
        .link-arrow:hover i {
            transform: translateX(4px);
        }

        /* Mega Menu */
        .mega-menu-container {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .mega-menu-container.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: all;
        }

        /* Slide transitions */
        .slide-content {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .slide-content.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Smooth gradient background */
        .gradient-smooth {
            background: linear-gradient(135deg, #0c4a6e 0%, #075985 50%, #0369a1 100%);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0c4a6e; }
        ::-webkit-scrollbar-thumb { background: #0ea5e9; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #38bdf8; }

        /* Section spacing - Design Token */
        .section-padding {
            padding-top: 4rem;
            padding-bottom: 4rem;
        }
        @media (min-width: 768px) {
            .section-padding { padding-top: 6rem; padding-bottom: 6rem; }
        }
        @media (min-width: 1024px) {
            .section-padding { padding-top: 8rem; padding-bottom: 8rem; }
        }

        /* Container custom padding */
        .container-custom { padding-left: 0.75rem; padding-right: 0.75rem; }
        @media (min-width: 640px) { .container-custom { padding-left: 1.5rem; padding-right: 1.5rem; } }
        @media (min-width: 768px) { .container-custom { padding-left: 2rem; padding-right: 2rem; } }
        @media (min-width: 1024px) { .container-custom { padding-left: 3rem; padding-right: 3rem; } }
        @media (min-width: 1280px) { .container-custom { padding-left: 4rem; padding-right: 4rem; } }

        /* Container full width until xl breakpoint */
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
<body x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    mobileMenu: false,
    stats: { projects: 0, experience: 0, clients: 0, area: 0 }
}"
x-init="
    document.documentElement.classList.toggle('dark', darkMode);
    $watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); });
    // Stats counter animation
    setTimeout(() => {
        const counters = [
            { target: 150, key: 'projects' },
            { target: 25, key: 'experience' },
            { target: 80, key: 'clients' },
            { target: 500, key: 'area' }
        ];
        counters.forEach(c => {
            let current = 0;
            const step = c.target / 50;
            const interval = setInterval(() => {
                current += step;
                if (current >= c.target) {
                    stats[c.key] = c.target;
                    clearInterval(interval);
                } else {
                    stats[c.key] = Math.floor(current);
                }
            }, 40);
        });
    }, 800);
"
:class="{ 'dark': darkMode }"
class="antialiased bg-white dark:bg-dark-900 text-dark-600 dark:text-primary-100 transition-colors duration-300">

    {{-- Header --}}
    @include('themes.t-4.layouts.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
        @yield('module_content')
    </main>

    {{-- Footer --}}
    @include('themes.t-4.layouts.footer')

    {{-- AOS Init --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true, offset: 100 });
    </script>

    {{-- GLightbox Init --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js"></script>
    <script>
        const lightbox = GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true, closeButton: true });
    </script>

    @stack('scripts')

    {{-- instant.page --}}
    <script src="{{ asset('js/instantpage.js') }}" type="module"></script>

    {{-- PWA Service Worker --}}
    <x-pwa-registration />
</body>
</html>
