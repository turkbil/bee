{{-- Varilsan Grup Header --}}
@php
    $siteName = setting('site_name');
    $siteSlogan = setting('site_slogan');

    // Logo Service
    $logoService = app(\App\Services\LogoService::class);
    $logos = $logoService->getLogos();
    $hasLogo = $logos['has_light'] || $logos['has_dark'];
@endphp

<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        x-data="{ scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 50"
        :class="scrolled ? 'bg-white/95 dark:bg-slate-900/95 backdrop-blur-md shadow-lg' : 'bg-transparent'">

    <div class="container mx-auto">
        <nav class="flex items-center justify-between h-20 lg:h-24">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-4 group">
                @if($hasLogo)
                    @if($logos['has_light'] && $logos['has_dark'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="dark:hidden h-12 w-auto">
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="hidden dark:block h-12 w-auto">
                    @elseif($logos['has_light'])
                        <img src="{{ $logos['light_logo_url'] }}" alt="{{ $siteName }}" class="h-12 w-auto">
                    @elseif($logos['has_dark'])
                        <img src="{{ $logos['dark_logo_url'] }}" alt="{{ $siteName }}" class="h-12 w-auto">
                    @endif
                @else
                    <div class="w-14 h-14 rounded-2xl gradient-shift flex items-center justify-center shadow-lg">
                        <span class="text-white font-heading font-bold text-2xl">V</span>
                    </div>
                    <div>
                        <span class="block font-heading font-bold text-xl" :class="scrolled ? 'text-slate-900 dark:text-white' : 'text-white'">{{ $siteName }}</span>
                        <span class="block text-xs tracking-[0.3em]" :class="scrolled ? 'text-slate-500' : 'text-white/70'">GRUP</span>
                    </div>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex items-center gap-8">

                {{-- 1. ŞİRKETLERİMİZ - Mega Menu --}}
                <div class="relative" x-data="{ megaOpen: false }" @mouseenter="megaOpen = true" @mouseleave="megaOpen = false">
                    <button class="flex items-center gap-2 font-medium transition-colors py-2" :class="scrolled ? 'text-slate-700 dark:text-slate-300 hover:text-sky-600' : 'text-white/90 hover:text-white'">
                        <span>Şirketlerimiz</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform duration-300" :class="megaOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="absolute left-0 right-0 h-4 top-full" x-show="megaOpen"></div>
                    <div x-show="megaOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="fixed left-1/2 -translate-x-1/2 top-20 lg:top-24 w-[calc(100vw-2rem)] max-w-4xl z-50">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                            <div class="p-6">
                                <div class="grid grid-cols-3 gap-5">
                                    <a href="#" @click="megaOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-sky-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-industry text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">Varilsan Polimer</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">IBC Tank & Plastik Bidon Üretimi</p>
                                            <span class="text-xs text-sky-600 font-medium mt-2 inline-block">Gebze, Kocaeli</span>
                                        </div>
                                    </a>
                                    <a href="#" @click="megaOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-sky-600 to-sky-700 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-sky-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-recycle text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">Varilsan Ambalaj</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">IBC Yenileme & Geri Dönüşüm</p>
                                            <span class="text-xs text-sky-600 font-medium mt-2 inline-block">Kocaeli</span>
                                        </div>
                                    </a>
                                    <a href="#" @click="megaOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-emerald-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-leaf text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">Varilsan Plastik</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">HDPE Granül & Geri Kazanım</p>
                                            <span class="text-xs text-emerald-600 font-medium mt-2 inline-block">Kemalpaşa, İzmir</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. ÜRÜNLERİMİZ - Mega Menu --}}
                <div class="relative" x-data="{ productsOpen: false }" @mouseenter="productsOpen = true" @mouseleave="productsOpen = false">
                    <button class="flex items-center gap-2 font-medium transition-colors py-2" :class="scrolled ? 'text-slate-700 dark:text-slate-300 hover:text-sky-600' : 'text-white/90 hover:text-white'">
                        <span>Ürünlerimiz</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform duration-300" :class="productsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="absolute left-0 right-0 h-4 top-full" x-show="productsOpen"></div>
                    <div x-show="productsOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="fixed left-1/2 -translate-x-1/2 top-20 lg:top-24 w-[calc(100vw-2rem)] max-w-4xl z-50">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                            <div class="p-6">
                                <div class="grid grid-cols-4 gap-5">
                                    <a href="#" @click="productsOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-sky-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-cube text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">IBC Tank</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">1000L Konteyner</p>
                                        </div>
                                    </a>
                                    <a href="#" @click="productsOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-sky-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-wine-bottle text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">Plastik Bidon</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">20L - 220L</p>
                                        </div>
                                    </a>
                                    <a href="#" @click="productsOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-slate-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-oil-can text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">Metal Varil</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">200L - 220L</p>
                                        </div>
                                    </a>
                                    <a href="#" @click="productsOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-start gap-4 p-4 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform shadow-lg shadow-emerald-500/20">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-cubes text-white text-lg transition-all duration-300"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-heading font-semibold text-slate-900 dark:text-white group-hover:text-sky-600 transition-colors">HDPE Granül</h4>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Geri Dönüşüm</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. KURUMSAL - Mega Menu --}}
                <div class="relative" x-data="{ corporateOpen: false }" @mouseenter="corporateOpen = true" @mouseleave="corporateOpen = false">
                    <button class="flex items-center gap-2 font-medium transition-colors py-2" :class="scrolled ? 'text-slate-700 dark:text-slate-300 hover:text-sky-600' : 'text-white/90 hover:text-white'">
                        <span>Kurumsal</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform duration-300" :class="corporateOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div class="absolute left-0 right-0 h-4 top-full" x-show="corporateOpen"></div>
                    <div x-show="corporateOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="fixed left-1/2 -translate-x-1/2 top-20 lg:top-24 w-[calc(100vw-2rem)] max-w-3xl z-50">
                        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-x-8 gap-y-1">
                                    {{-- Sol Kolon --}}
                                    <div class="space-y-1">
                                        <a href="#hakkimizda" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-building text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Hakkımızda</span>
                                        </a>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-book-open text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Katalog</span>
                                        </a>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-award text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Belgelerimiz</span>
                                        </a>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-shield-halved text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">UN & ADR</span>
                                        </a>
                                    </div>
                                    {{-- Sağ Kolon - Galeriler --}}
                                    <div class="border-l border-slate-200 dark:border-slate-700 pl-8">
                                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider px-4 mb-2 block">Galeriler</span>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-circle-play text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Tanıtım Videosu</span>
                                        </a>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-images text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Foto Galeri</span>
                                        </a>
                                        <a href="#" @click="corporateOpen = false" x-data="{ h: false }" @mouseenter="h = true" @mouseleave="h = false" class="group flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all">
                                            <i :class="h ? 'fa-solid' : 'fa-light'" class="fa-calendar-check text-sky-600 w-5 transition-all duration-300"></i>
                                            <span class="font-medium text-slate-700 dark:text-slate-300 group-hover:text-sky-600 transition-colors">Fuarlar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. İLETİŞİM --}}
                <a href="#iletisim" class="font-medium transition-colors" :class="scrolled ? 'text-slate-700 dark:text-slate-300 hover:text-sky-600' : 'text-white/90 hover:text-white'">
                    İletişim
                </a>
            </div>

            {{-- Right Actions --}}
            <div class="flex items-center gap-4">
                {{-- Dark Mode Toggle --}}
                <button @click="toggleDarkMode()" class="w-12 h-12 rounded-xl flex items-center justify-center transition-all" :class="scrolled ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' : 'bg-white/10 text-white'">
                    <i class="fa-light fa-sun text-lg" x-show="darkMode"></i>
                    <i class="fa-light fa-moon text-lg" x-show="!darkMode"></i>
                </button>

                {{-- CTA Button --}}
                <a href="#iletisim" class="hidden lg:inline-flex items-center gap-2 gradient-shift text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 transition-all">
                    <span>Teklif Al</span>
                    <i class="fa-light fa-arrow-right text-sm"></i>
                </a>

                {{-- Mobile Menu Button --}}
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden w-12 h-12 rounded-xl flex items-center justify-center" :class="scrolled ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' : 'bg-white/10 text-white'">
                    <i class="fa-light fa-bars text-xl" x-show="!mobileMenu"></i>
                    <i class="fa-light fa-xmark text-xl" x-show="mobileMenu"></i>
                </button>
            </div>
        </nav>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenu" x-transition class="lg:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 max-h-[80vh] overflow-y-auto">
        <div class="container mx-auto py-6">
            <div class="flex flex-col gap-2">
                {{-- Şirketlerimiz --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full py-3 font-medium text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span>Şirketlerimiz</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 py-2 space-y-2">
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Varilsan Polimer</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Varilsan Ambalaj</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Varilsan Plastik</a>
                    </div>
                </div>
                {{-- Ürünlerimiz --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full py-3 font-medium text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span>Ürünlerimiz</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 py-2 space-y-2">
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">IBC Tank</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Plastik Bidon</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Metal Varil</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">HDPE Granül</a>
                    </div>
                </div>
                {{-- Kurumsal --}}
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full py-3 font-medium text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span>Kurumsal</span>
                        <i class="fa-light fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-collapse class="pl-4 py-2 space-y-2">
                        <a href="#hakkimizda" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Hakkımızda</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Tanıtım Videosu</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Foto Galeri</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Fuar</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Katalog</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">Belgelerimiz</a>
                        <a href="#" @click="mobileMenu = false" class="block py-2 text-sm text-slate-500">UN & ADR</a>
                    </div>
                </div>
                {{-- İletişim --}}
                <a href="#iletisim" @click="mobileMenu = false" class="py-3 font-medium text-slate-700 dark:text-slate-300">İletişim</a>
                {{-- CTA --}}
                <a href="#iletisim" @click="mobileMenu = false" class="mt-4 gradient-shift text-white px-6 py-4 rounded-xl text-center font-semibold">
                    Teklif Al
                </a>
            </div>
        </div>
    </div>
</header>
