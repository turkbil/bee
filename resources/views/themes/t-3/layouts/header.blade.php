{{-- t-3 Panjur Theme - Header --}}
@php
    $siteName = setting('site_title') ?: setting('site_company_name');
    $siteSlogan = setting('site_slogan');
    $sitePhone = setting('contact_phone_1');
    $siteMobile = setting('contact_phone_2') ?: setting('contact_whatsapp_1');
    $siteWhatsapp = setting('contact_whatsapp_1') ? preg_replace('/[^0-9]/', '', setting('contact_whatsapp_1')) : null;
    $whatsappUrl = whatsapp_link();

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];
@endphp

{{-- Top Bar - Telefon numaralari --}}
@if($sitePhone || $siteMobile)
<div class="bg-gradient-to-r from-gray-900 to-gray-800 dark:from-black dark:to-gray-900 text-white py-2 text-sm">
    <div class="container mx-auto px-4 sm:px-4 md:px-2">
        {{-- Mobil/Tablet: Numaralar --}}
        <div class="flex xl:hidden items-center justify-center gap-2 flex-wrap">
            @if($sitePhone)
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center gap-1.5 hover:text-primary-400 transition-colors bg-white/10 px-3 py-1 rounded-full">
                <i class="fat fa-phone text-primary-400 text-xs"></i>
                <span class="font-medium text-xs">{{ $sitePhone }}</span>
            </a>
            @endif
            @if($siteMobile)
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-center gap-1.5 hover:text-primary-400 transition-colors bg-white/10 px-3 py-1 rounded-full">
                <i class="fat fa-mobile text-primary-400 text-xs"></i>
                <span class="font-medium text-xs">{{ $siteMobile }}</span>
            </a>
            @endif
            @if($whatsappUrl)
            <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center gap-1.5 hover:text-green-400 transition-colors bg-green-600/30 px-3 py-1 rounded-full">
                <i class="fab fa-whatsapp text-green-400 text-xs"></i>
                <span class="font-medium text-xs">WhatsApp</span>
            </a>
            @endif
        </div>
        {{-- Desktop: Numaralar + Ek bilgiler --}}
        <div class="hidden xl:flex items-center justify-between">
            <div class="flex items-center gap-6">
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center gap-2 hover:text-primary-400 transition-colors">
                    <i class="fat fa-phone text-primary-400"></i>
                    <span>{{ $sitePhone }}</span>
                </a>
                @endif
                @if($siteMobile)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-center gap-2 hover:text-primary-400 transition-colors">
                    <i class="fat fa-mobile text-primary-400"></i>
                    <span>{{ $siteMobile }}</span>
                </a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-2">
                    <i class="fat fa-clock text-primary-400"></i>
                    7/24 Hizmet
                </span>
                <span class="flex items-center gap-2">
                    <i class="fat fa-map-marker-alt text-primary-400"></i>
                    İstanbul Geneli
                </span>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Main Header --}}
<header class="sticky top-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-b border-gray-200 dark:border-slate-800 transition-all duration-300">
    <div class="container mx-auto px-4 sm:px-4 md:px-2">
        <div class="flex items-center justify-between py-3 lg:py-4">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0">
                @if($hasLogo)
                    @if($logos['has_light'] && $logos['has_dark'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="dark:hidden h-9 lg:h-10 w-auto">
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="hidden dark:block h-9 lg:h-10 w-auto">
                    @elseif($logos['has_light'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-9 lg:h-10 w-auto">
                    @elseif($logos['has_dark'])
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-9 lg:h-10 w-auto">
                    @endif
                @else
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-primary-500/30 transition-all duration-300">
                        <i class="fat fa-blinds text-white text-xl lg:text-2xl group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-lg lg:text-xl font-bold font-heading text-gray-900 dark:text-white">{{ $siteName }}</span>
                        @if($siteSlogan)
                            <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">{{ $siteSlogan }}</p>
                        @endif
                    </div>
                @endif
            </a>

            {{-- Cache & AI Clear Buttons - SADECE ROOT --}}
            @auth
                @if(auth()->user()->hasRole('root'))
                <div class="flex items-center gap-1 ml-2">
                    <button
                        class="w-8 h-8 bg-gray-100 dark:bg-slate-800 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300"
                        title="Cache Sıfırla"
                        x-data
                        @click="
                            fetch('/admin/cache/clear', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }).then(() => window.location.reload());
                        "
                    >
                        <i class="fat fa-sync-alt text-sm"></i>
                    </button>
                    <button
                        class="w-8 h-8 bg-gray-100 dark:bg-slate-800 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-all duration-300"
                        title="AI Sıfırla"
                        x-data
                        @click="
                            const tenantId = '{{ tenant('id') ?? '' }}';
                            if (tenantId) {
                                localStorage.removeItem('tenant' + tenantId + '_ai_session');
                            }
                            window.location.reload();
                        "
                    >
                        <i class="fat fa-robot text-sm"></i>
                    </button>
                </div>
                @endif
            @endauth

            {{-- Desktop Navigation (xl+) --}}
            <nav class="hidden xl:flex items-center gap-8">
                <a href="{{ url('/service') }}" class="font-medium {{ request()->is('service*') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-300' }} hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Hizmetler</a>
                <a href="{{ url('/page/hakkimizda') }}" class="font-medium {{ request()->is('page/hakkimizda') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-300' }} hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Hakkımızda</a>
                <a href="{{ url('/page/iletisim') }}" class="font-medium {{ request()->is('page/iletisim') ? 'text-primary-600 dark:text-primary-400' : 'text-gray-600 dark:text-gray-300' }} hover:text-primary-600 dark:hover:text-primary-400 transition-colors">İletişim</a>
            </nav>

            {{-- Desktop CTA Buttons (xl+) --}}
            <div class="hidden xl:flex items-center gap-2">
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors">
                    <i :class="darkMode ? 'fat fa-sun' : 'fat fa-moon'" class="text-lg"></i>
                </button>
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium text-sm rounded-lg hover:from-primary-700 hover:to-primary-600 transition-all duration-300 shadow-lg shadow-primary-500/25 hover:shadow-primary-500/40">
                    <i class="fat fa-phone"></i>
                    <span>{{ $sitePhone }}</span>
                </a>
                @endif
                @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium text-sm rounded-lg hover:from-green-700 hover:to-green-600 transition-all duration-300 shadow-lg shadow-green-500/25 hover:shadow-green-500/40">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                </a>
                @endif
            </div>

            {{-- Mobile/Tablet: Dark Mode + Menu Button (below xl) --}}
            <div class="flex items-center gap-2 xl:hidden">
                <button @click="darkMode = !darkMode" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300">
                    <i :class="darkMode ? 'fat fa-sun text-yellow-500' : 'fat fa-moon text-blue-500'" class="text-xl"></i>
                </button>
                <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-lg bg-gray-100 dark:bg-slate-800">
                    <i :class="mobileMenu ? 'fat fa-xmark' : 'fat fa-bars'" class="text-xl text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu (below xl) --}}
    <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="xl:hidden bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex flex-col gap-2">
                <a href="{{ url('/service') }}" @click="mobileMenu = false" class="py-3 px-4 font-medium {{ request()->is('service*') ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800' }} rounded-lg transition-colors">Hizmetler</a>
                <a href="{{ url('/page/hakkimizda') }}" @click="mobileMenu = false" class="py-3 px-4 font-medium {{ request()->is('page/hakkimizda') ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800' }} rounded-lg transition-colors">Hakkımızda</a>
                <a href="{{ url('/page/iletisim') }}" @click="mobileMenu = false" class="py-3 px-4 font-medium {{ request()->is('page/iletisim') ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800' }} rounded-lg transition-colors">İletişim</a>
            </nav>
            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-slate-800">
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium text-sm rounded-lg">
                    <i class="fat fa-phone"></i>
                    <span class="hidden sm:inline">{{ $sitePhone }}</span>
                    <span class="sm:hidden">Sabit</span>
                </a>
                @endif
                @if($siteMobile)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteMobile) }}" class="flex items-center justify-center gap-2 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-medium text-sm rounded-lg">
                    <i class="fat fa-mobile"></i>
                    <span class="hidden sm:inline">{{ $siteMobile }}</span>
                    <span class="sm:hidden">Mobil</span>
                </a>
                @endif
            </div>
            @if($whatsappUrl)
            <a href="{{ $whatsappUrl }}" target="_blank" class="flex items-center justify-center gap-2 py-3 mt-2 bg-gradient-to-r from-green-600 to-green-500 text-white font-medium rounded-lg">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp ile Ulaşın</span>
            </a>
            @endif
        </div>
    </div>
</header>
