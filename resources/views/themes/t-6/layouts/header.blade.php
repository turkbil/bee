{{-- t-6 Theme - Art Deco Header --}}
@php
    $siteName = setting('site_title');
    $siteSlogan = setting('site_slogan');
    $sitePhone = setting('contact_phone_1');
    $siteEmail = setting('contact_email_1');
    $siteWhatsapp = setting('contact_whatsapp_1');
    $whatsappUrl = whatsapp_link();

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];

    // Services for menu
    $services = \Modules\Service\App\Models\Service::where('is_active', true)
        ->orderBy('service_id')
        ->take(8)
        ->get(['service_id', 'title', 'slug', 'body']);

    // Service icons mapping
    $serviceIcons = [
        'ticaret-hukuku' => 'fa-handshake',
        'sirketler-hukuku' => 'fa-building',
        'ceza-hukuku' => 'fa-gavel',
        'saglik-hukuku' => 'fa-stethoscope',
        'sigorta-hukuku' => 'fa-shield-check',
        'idare-ve-imar-hukuku' => 'fa-landmark',
        'borclar-hukuku' => 'fa-file-contract',
        'is-hukuku' => 'fa-briefcase',
    ];
@endphp

{{-- Main Header --}}
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="header">
    <div class="bg-white/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-amber-500/10 dark:border-amber-500/10">
        <div class="container mx-auto px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20">
            <nav class="flex items-center justify-between py-4 lg:py-5">

                {{-- Logo (Left) --}}
                <div class="flex-shrink-0">
                    <a href="{{ url('/') }}" class="flex flex-col items-center group">
                        @if($hasLogo)
                            @if($logos['has_light'] && $logos['has_dark'])
                                <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="dark:hidden h-10 w-auto">
                                <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="hidden dark:block h-10 w-auto">
                            @elseif($logos['has_light'])
                                <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                            @elseif($logos['has_dark'])
                                <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-10 w-auto">
                            @endif
                        @else
                            <div class="flex items-center space-x-2">
                                <div class="art-deco-diamond"></div>
                                <span class="font-heading text-lg lg:text-xl tracking-[0.15em] uppercase gradient-text font-semibold">{{ $siteName }}</span>
                                <div class="art-deco-diamond"></div>
                            </div>
                            <span class="font-heading text-[9px] lg:text-[10px] tracking-[0.3em] uppercase text-amber-700 dark:text-amber-500/70 mt-0.5">{{ $siteSlogan }}</span>
                        @endif
                    </a>
                </div>

                {{-- Nav Links (Right - Desktop) --}}
                <div class="hidden lg:flex items-center space-x-8">
                    {{-- Services Megamenu --}}
                    @if($services->count() > 0)
                    <div class="relative" x-data="{ open: false }">
                        <a href="{{ url('/service') }}"
                           @mouseenter="open = true"
                           class="nav-link font-heading text-sm font-semibold tracking-widest uppercase {{ request()->is('service*') ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }} hover:text-amber-600 dark:hover:text-amber-400 transition-colors flex items-center py-2">
                            Hizmetler
                            <i class="fat fa-chevron-down ml-2 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </a>

                        {{-- Megamenu Panel --}}
                        <div x-show="open"
                             @mouseenter="open = true"
                             @mouseleave="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             x-cloak
                             class="absolute top-full left-1/2 -translate-x-1/2 pt-4 w-[700px]">

                            {{-- Invisible bridge to prevent closing --}}
                            <div class="absolute -top-4 left-0 right-0 h-4"></div>

                            <div class="bg-white dark:bg-slate-950 border border-amber-500/20 rounded-2xl shadow-2xl overflow-hidden">
                                {{-- Header --}}
                                <div class="bg-gradient-to-r from-amber-500/10 to-amber-600/5 px-6 py-4 border-b border-amber-500/10">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-heading text-lg font-semibold text-slate-900 dark:text-white tracking-wide">Hizmet Alanlarımız</h3>
                                            <p class="text-sm text-slate-600 dark:text-slate-400">Profesyonel hukuki danışmanlık</p>
                                        </div>
                                        <a href="{{ url('/service') }}" class="text-amber-700 dark:text-amber-400 font-heading text-xs tracking-wider uppercase hover:text-amber-600 flex items-center gap-1">
                                            Tümünü Gör
                                            <i class="fat fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>

                                {{-- Services Grid --}}
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-3">
                                        @foreach($services as $service)
                                        @php
                                            $icon = $serviceIcons[$service->slug] ?? 'fa-scale-balanced';
                                        @endphp
                                        <a href="{{ url('/service/' . $service->slug) }}"
                                           class="megamenu-item group flex items-start gap-4 p-4 rounded-xl hover:bg-amber-500/5 transition-all duration-200">
                                            <div class="w-11 h-11 rounded-lg bg-amber-500/10 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500/20 transition-colors">
                                                <i class="fat {{ $icon }} icon-hover text-lg text-amber-700 dark:text-amber-400"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-heading text-sm font-semibold text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                                    {{ $service->title }}
                                                </h4>
                                                @if($service->body)
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                                                    {{ Str::limit(strip_tags($service->body), 60) }}
                                                </p>
                                                @endif
                                            </div>
                                            <i class="fat fa-chevron-right text-xs text-slate-400 group-hover:text-amber-500 transition-colors mt-1"></i>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Footer CTA --}}
                                <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 border-t border-amber-500/10">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm text-slate-600 dark:text-slate-400">
                                            <i class="fat fa-phone-volume text-amber-500 mr-2"></i>
                                            Ücretsiz ön görüşme için bizi arayın
                                        </p>
                                        @if($sitePhone)
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="font-heading text-sm font-semibold text-amber-700 dark:text-amber-400 hover:text-amber-600">
                                            {{ $sitePhone }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <a href="{{ url('/page/hakkimizda') }}" class="nav-link font-heading text-sm font-semibold tracking-widest uppercase {{ request()->is('page/hakkimizda') ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }} hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Hakkımızda</a>
                    <a href="{{ url('/page/iletisim') }}" class="nav-link font-heading text-sm font-semibold tracking-widest uppercase {{ request()->is('page/iletisim') ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }} hover:text-amber-600 dark:hover:text-amber-400 transition-colors">İletişim</a>

                    {{-- Dark/Light Toggle --}}
                    <button onclick="toggleTheme()" class="p-2 text-amber-700 dark:text-amber-400 hover:text-amber-500 dark:hover:text-amber-300 transition-colors" aria-label="Tema Değiştir">
                        <i class="fat fa-sun-bright text-lg hidden dark:inline"></i>
                        <i class="fat fa-moon-stars text-lg dark:hidden"></i>
                    </button>

                    {{-- CTA Button --}}
                    @if($sitePhone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn-shine bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading text-sm tracking-widest uppercase px-6 py-3 rounded-lg hover:from-amber-500 hover:to-amber-400 transition-all">
                        Randevu
                    </a>
                    @endif
                </div>

                {{-- Mobile: Theme Toggle + Menu Button --}}
                <div class="flex items-center gap-2 lg:hidden">
                    <button onclick="toggleTheme()" class="p-2 text-amber-700 dark:text-amber-400 hover:text-amber-500 dark:hover:text-amber-300 transition-colors" aria-label="Tema Değiştir">
                        <i class="fat fa-sun-bright text-xl hidden dark:inline"></i>
                        <i class="fat fa-moon-stars text-xl dark:hidden"></i>
                    </button>
                    <button @click="mobileMenu = !mobileMenu" class="p-2 text-amber-700 dark:text-amber-400" aria-label="Menu">
                        <i class="fat fa-bars text-2xl"></i>
                    </button>
                </div>
            </nav>
        </div>
    </div>
</header>

{{-- Mobile Menu --}}
<div x-show="mobileMenu"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak
     class="fixed inset-0 z-50 lg:hidden">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/60" @click="mobileMenu = false"></div>

    {{-- Menu Panel --}}
    <div x-show="mobileMenu"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute inset-y-0 right-0 w-80 max-w-full bg-white dark:bg-slate-950 shadow-2xl">
        <div class="flex flex-col h-full p-6">
            <div class="flex justify-between items-center mb-8">
                <span class="font-heading text-lg tracking-widest uppercase gradient-text">Menu</span>
                <button @click="mobileMenu = false" class="p-2 text-amber-700 dark:text-amber-400">
                    <i class="fat fa-xmark text-2xl"></i>
                </button>
            </div>
            <nav class="flex flex-col space-y-4">
                @if($services->count() > 0)
                <div x-data="{ servicesOpen: false }">
                    <button @click="servicesOpen = !servicesOpen" class="w-full flex items-center justify-between font-heading text-lg font-semibold tracking-widest uppercase text-slate-800 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors py-3 border-b border-slate-200 dark:border-slate-800">
                        Hizmetler
                        <i class="fat fa-chevron-down text-sm transition-transform" :class="servicesOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="servicesOpen" x-collapse class="pl-4 space-y-2 py-2">
                        @foreach($services as $service)
                        <a href="{{ url('/service/' . $service->slug) }}" @click="mobileMenu = false" class="block py-2 text-slate-600 dark:text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                            {{ $service->title }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <a href="{{ url('/page/hakkimizda') }}" @click="mobileMenu = false" class="font-heading text-lg font-semibold tracking-widest uppercase text-slate-800 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors py-3 border-b border-slate-200 dark:border-slate-800">Hakkımızda</a>
                <a href="{{ url('/page/iletisim') }}" @click="mobileMenu = false" class="font-heading text-lg font-semibold tracking-widest uppercase text-slate-800 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors py-3 border-b border-slate-200 dark:border-slate-800">İletişim</a>
            </nav>
            <div class="mt-auto">
                @if($sitePhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn-shine block text-center bg-gradient-to-r from-amber-600 to-amber-500 text-white font-heading tracking-widest uppercase px-6 py-4 rounded-lg">
                    Randevu Al
                </a>
                @endif
                <div class="flex items-center justify-center mt-6 space-x-4">
                    <button onclick="toggleTheme()" class="p-3 text-amber-700 dark:text-amber-400 border border-amber-600/30 dark:border-amber-500/30 rounded-lg">
                        <i class="fat fa-sun-bright hidden dark:inline"></i>
                        <i class="fat fa-moon-stars dark:hidden"></i>
                    </button>
                    @if($sitePhone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="p-3 text-amber-700 dark:text-amber-400 border border-amber-600/30 dark:border-amber-500/30 rounded-lg">
                        <i class="fat fa-phone"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Header scroll effect script --}}
@push('scripts')
<script>
    const header = document.getElementById('header');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('shadow-xl');
        } else {
            header.classList.remove('shadow-xl');
        }

        lastScroll = currentScroll;
    });
</script>
@endpush
