<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('meta')

    <!-- Performance - Inline Critical CSS -->
    <style>
        /* Critical CSS - Above the fold */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #030712; color: #fff; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }

        /* Alpine.js x-cloak */
        [x-cloak] { display: none !important; }

        @keyframes gold-shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .gold-gradient {
            background: linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37, #f4e5a1);
            background-size: 200% auto;
            animation: gold-shimmer 3s ease infinite;
        }
        .gold-gradient-strong {
            background: linear-gradient(135deg, #FFD700 0%, #FFF 50%, #FFD700 100%);
            background-size: 200% auto;
            animation: gold-shimmer 4s ease infinite;
        }
    </style>

    <!-- Compiled CSS (Tailwind + Custom) -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    <!-- Font Awesome (Local veya CSP-approved CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- AI Chat Widget CSS -->
    <link rel="stylesheet" href="/assets/css/ai-chat.css?v={{ time() }}">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://wa.me">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-P8HKHCG9');</script>

    @stack('schema')
    @stack('scripts-head')
</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P8HKHCG9" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <!-- Minimal Header - Google Ads Optimized -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-black/95 backdrop-blur-xl border-b border-gray-800/30 py-3 px-4">
        <div class="container mx-auto flex items-center justify-between">
            <!-- Logo - Settings'den çekilir -->
            @php
                $logoService = app(\App\Services\LogoService::class);
                $logos = $logoService->getLogos();
                $logoUrl = $logos['light_logo_url'] ?? null;
                $logoDarkUrl = $logos['dark_logo_url'] ?? null;
                $siteTitle = $logos['site_title'] ?? setting('site_title', 'iXtif');
            @endphp

            <a href="/" class="flex items-center hover:opacity-80 transition-opacity">
                @if($logoDarkUrl)
                    {{-- Dark logo kullan (2. logo) --}}
                    <img src="{{ $logoDarkUrl }}" alt="{{ $siteTitle }}" class="h-8 md:h-10 w-auto object-contain">
                @elseif($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteTitle }}" class="h-8 md:h-10 w-auto object-contain">
                @else
                    {{-- Fallback: Text logo --}}
                    <span class="text-2xl font-black gold-gradient bg-clip-text text-transparent">{{ $siteTitle }}</span>
                @endif
            </a>

            <!-- Contact Icons - Settings'den çekilir -->
            @php
                $contactPhone = setting('contact_phone_1', '0216 755 35 55');
                $contactWhatsapp = setting('contact_whatsapp_1', '905309555885');
            @endphp

            <div class="flex items-center gap-3">
                <!-- WhatsApp -->
                @if($contactWhatsapp)
                    <a href="{{ whatsapp_link(null, 'Elektrikli Transpalet') }}"
                       target="_blank"
                       class="flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white transition-colors">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span class="hidden sm:inline font-semibold text-sm">WhatsApp</span>
                    </a>
                @endif

                <!-- Telefon -->
                @if($contactPhone)
                    <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                       class="flex items-center gap-2 px-3 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-white transition-colors">
                        <i class="fas fa-phone"></i>
                        <span class="hidden md:inline font-semibold text-sm">{{ $contactPhone }}</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Minimal Footer - Google Ads Optimized -->
    <footer class="bg-black py-6 border-t border-gray-800">
        <div class="container mx-auto px-4 text-center">
            <p class="text-sm text-gray-600">
                © {{ date('Y') }} İXTİF İç ve Dış Ticaret A.Ş. | Tüm hakları saklıdır.
            </p>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Alpine.js Plugins (Must load BEFORE Alpine core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <!-- Alpine.js (Required for chat widget) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- AI Chat Widget JS -->
    <script src="/assets/js/ai-chat.js?v={{ time() }}"></script>

    <!-- Chat Widget - AI Floating Widget (www.ixtif.com ile aynı) -->
    <x-ai.floating-widget theme="gray" />

    @stack('scripts-footer')
</body>
</html>
