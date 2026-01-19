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

    {{-- Tailwind CSS CDN with Typography Plugin --}}
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
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
                    },
                    fontFamily: {
                        heading: ['Inter', 'sans-serif'],
                        body: ['Inter', 'sans-serif'],
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

    {{-- Custom Styles --}}
    <style>
        [x-cloak] { display: none !important; }
        .floating { animation: float 3s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); }
        .icon-hover { transition: all 0.3s ease; }
        .card-hover:hover .icon-hover { transform: scale(1.1); }
    </style>

    @stack('styles')
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches), mobileMenu: false }"
      x-init="document.documentElement.classList.toggle('dark', darkMode); $watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); });"
      :class="{ 'dark': darkMode }"
      class="bg-white dark:bg-slate-900 text-gray-900 dark:text-white transition-colors duration-300">

    {{-- Header --}}
    @include('themes.t-3.layouts.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
        @yield('module_content')
    </main>

    {{-- Footer --}}
    @include('themes.t-3.layouts.footer')

    {{-- AOS Init --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            offset: 0,
            delay: 0,
            startEvent: 'DOMContentLoaded',
            disable: window.innerWidth < 768 ? false : false,
            anchorPlacement: 'top-bottom'
        });

        // Mobilde hemen göster, scroll beklemeden
        if (window.innerWidth < 768) {
            setTimeout(() => AOS.refresh(), 100);
        }
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

    @stack('scripts')
</body>
</html>
