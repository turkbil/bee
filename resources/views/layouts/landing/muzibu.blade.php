<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('meta')

    {{-- Tailwind CSS - Tenant Aware (tenant-1001.css) --}}
    <link rel="stylesheet" href="{{ tenant_css() }}">

    {{-- FontAwesome Pro 7.1.0 (Local) --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/fontawesome-pro@7.1.0/css/all.css') }}">

    {{-- AOS Animation Library --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    {{-- Custom Styles --}}
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('themes/muzibu/css/muzibu-custom.css') }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    @stack('styles')

    <style>
        /* Landing Page Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Hide scrollbar for main content (Muzibu style) */
        .landing-content::-webkit-scrollbar {
            display: none;
        }
        .landing-content {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-black text-white overflow-hidden">

    {{-- Grid Container (Muzibu Layout) --}}
    <div class="h-[100dvh] w-full grid grid-rows-[56px_1fr] lg:grid-cols-[220px_1fr] overflow-hidden gap-0 md:gap-3 px-0 pb-0 pt-0 md:px-3 md:pt-3">

        {{-- Header - Tüm genişlikte (2 kolonu da kapla) --}}
        <div class="row-start-1 lg:col-span-2">
            @include('themes.muzibu.components.header')
        </div>

        {{-- Left Sidebar - Desktop Only --}}
        <div class="row-start-2 lg:col-start-1 overflow-hidden">
            @include('themes.muzibu.components.sidebar-left')
        </div>

        {{-- Main Content Area --}}
        <main class="row-start-2 lg:col-start-2 relative overflow-hidden bg-black">
            <div class="overflow-y-auto h-full relative landing-content">
                @yield('content')
            </div>
        </main>

    </div>

    {{-- AOS Animation Library JS --}}
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    {{-- Alpine.js (Livewire bundle) --}}
    @livewireScripts

    @stack('scripts')

    <script>
        // Global Variables (Landing Page - Muzibu uyumlu)
        window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
        window.currentUser = @if(auth()->check())
            {
                id: {{ auth()->id() }},
                name: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}"
            }
        @else
            null
        @endif;

        // Config for Alpine.js (Header/Sidebar uyumluluğu için)
        window.muzibuPlayerConfig = {
            isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
            currentUser: window.currentUser,
            frontLang: {
                user: {
                    user: 'Kullanıcı',
                    login: 'Giriş Yap',
                    register: 'Kayıt Ol'
                }
            }
        };

        // AOS Init - Scroll container ile
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                const scrollContainer = document.querySelector('.landing-content');

                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                    // Custom scroll container
                    container: scrollContainer || window,
                    disable: false
                });

                // Scroll container varsa AOS'u manuel tetikle
                if (scrollContainer) {
                    scrollContainer.addEventListener('scroll', function() {
                        AOS.refresh();
                    });
                }
            }
        });

        // Accordion Toggle
        function toggleAccordion(button) {
            const accordionItem = button.parentElement;
            const content = button.nextElementSibling;
            const icon = button.querySelector('i');

            // Toggle açık/kapalı
            const isHidden = content.classList.contains('hidden');

            if (isHidden) {
                // Aç
                content.classList.remove('hidden');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                // Kapat
                content.classList.add('hidden');
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        }
    </script>

</body>
</html>
