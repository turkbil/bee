<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ecrin Turizm | Olçun Travel - A Grubu Seyahat Acentası' }}</title>
    <meta name="description" content="{{ $description ?? '2008\'den beri A Grubu Seyahat Acentası olarak profesyonel taşımacılık hizmetleri. Turizm, personel ve öğrenci taşımacılığı, otel rezervasyonları, yat kiralama.' }}">

    <!-- Fonts: Inter + Roboto -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Pro 7 LOCAL -->
    <link rel="stylesheet" href="/assets/libs/fontawesome-pro@7.1.0/css/all.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
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
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Container - Tema Kuralları */
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        @media (min-width: 768px) {
            .container {
                padding-left: 32px !important;
                padding-right: 32px !important;
            }
        }

        @media (min-width: 1024px) {
            .container {
                padding-left: 48px !important;
                padding-right: 48px !important;
            }
        }

        @media (min-width: 1280px) {
            .container {
                max-width: 1280px !important;
                padding-left: 32px !important;
                padding-right: 32px !important;
            }
        }

        @media (min-width: 1536px) {
            .container {
                max-width: 1536px !important;
            }
        }

        /* Base Styles */
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', sans-serif;
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6, #0284c7);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Animated Gradient Text */
        .animated-gradient-text {
            background: linear-gradient(90deg, #0ea5e9, #3b82f6, #06b6d4, #0ea5e9);
            background-size: 300% 100%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: gradientFlow 4s ease infinite;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Gradient Border */
        .gradient-border {
            position: relative;
            padding: 2px;
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            border-radius: 1rem;
        }

        .gradient-border-inner {
            background: white;
            border-radius: calc(1rem - 2px);
        }

        .dark .gradient-border-inner {
            background: #0f172a;
        }

        /* Hover Card Effect */
        .card-hover {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .card-hover:hover {
            border-color: #0ea5e9;
        }

        /* Scale kaldırıldı - tema kurallarına göre yasak */

        .card-hover:hover .title-hover {
            color: #0ea5e9;
        }

        /* Icon Transition */
        .icon-hover {
            transition: all 0.3s ease;
        }

        /* Scroll Indicator */
        .scroll-indicator {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Header Transform */
        .header-scrolled {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .dark .header-scrolled {
            background: rgba(15, 23, 42, 0.95) !important;
        }

        /* Mobile Menu */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Process Step Line */
        .process-line::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #0ea5e9, transparent);
        }

        .process-line:last-child::after {
            display: none;
        }

        /* Stat Counter Animation */
        .stat-value {
            background: linear-gradient(135deg, #0ea5e9, #3b82f6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-body antialiased" x-data="{ darkMode: false, mobileMenu: false }" :class="{ 'dark': darkMode }">

    {{-- Header --}}
    @include('themes.t-5.layouts.header')

    {{-- Main Content --}}
    <main class="flex-1">
        {{ $slot ?? '' }}

        @yield('content')
        @yield('module_content')
    </main>

    {{-- Footer --}}
    @include('themes.t-5.layouts.footer')

    @stack('scripts')
</body>
</html>
