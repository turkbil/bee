<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    @stack('styles')

    <style>
        /* Reset any overflow restrictions */
        html, body {
            margin: 0;
            padding: 0;
            height: auto !important;
            min-height: 100%;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        /* Landing Page Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Hide scrollbar */
        ::-webkit-scrollbar {
            width: 0;
            display: none;
        }
        * {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-black text-white">
    @yield('content')

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

        // Config for Alpine.js
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

        // AOS Init
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 100,
                    disable: false
                });
            }
        });

        // Accordion Toggle
        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i');
            const isHidden = content.classList.contains('hidden');

            if (isHidden) {
                content.classList.remove('hidden');
                icon.classList.remove('fa-plus');
                icon.classList.add('fa-minus');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-plus');
            }
        }
    </script>

</body>
</html>
