@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link rel="stylesheet" href="{{ asset('themes/ixtif/css/homepage.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('themes/ixtif/js/homepage.js') }}"></script>
@endpush

@section('module_content')
<div x-data="homepage()" x-init="init()">
    <!-- Hero Slider Section -->
    <section class="hero-section relative">
        <!-- Progress Bar (Top) -->
        <div class="hero-progress-container absolute top-0 left-0 right-0 z-20">
            <div class="hero-progress-bar h-full ease-linear"></div>
        </div>

        <div class="swiper heroSwiper relative">
            <div class="swiper-wrapper">

                    <!-- Slide 1: Teslimatlar -->
                    <div class="swiper-slide slide-1-bg">
                        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                            <div class="text-gray-900 dark:text-white">
                                <h1 class="hero-title text-3xl md:text-5xl lg:text-7xl font-black mb-6 leading-[1.2] overflow-visible opacity-0">
                                    <span class="gradient-animate block py-2">İXTİF TÜRKİYE'DE</span>
                                    <span class="gradient-animate block py-2">HER YERDE</span>
                                </h1>
                                <p class="hero-desc text-base md:text-xl lg:text-2xl text-gray-700 dark:text-gray-200 mb-8 leading-relaxed font-medium opacity-0">
                                    Güvenli paketleme ve hızlı kargo ile sorunsuz teslimat, her zaman zamanında ve güvenle
                                </p>
                                <div class="hero-cta mb-8 opacity-0">
                                    <a href="{{ route('shop.index') }}" class="group bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-800 text-slate-900 dark:text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                                        <i class="fa-light fa-truck-fast mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                        Teslimat Bilgisi Al
                                    </a>
                                </div>
                                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-truck-fast text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Hızlı Teslim</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Türkiye geneli</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-boxes text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Güvenli Paket</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Sağlam gönderi</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-warehouse text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base whitespace-nowrap">Düzenli Sevkiyat</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Titiz süreç</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 xl:hidden opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-clock text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Tam Zamanında</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Teslim garantisi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-image flex items-center justify-center lg:justify-end lg:justify-self-end opacity-0">
                                <img src="{{ thumb('https://ixtif.com/storage/tenant2/1474/teslimat.png', 900, null, ['format' => 'webp', 'quality' => 85]) }}" alt="İXTİF Teslimat" class="h-auto object-contain" loading="eager">
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Slide 2: HELI -->
                    <div class="swiper-slide slide-2-bg">
                        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                                <div class="text-gray-900 dark:text-white">
                                <h1 class="hero-title text-3xl md:text-5xl lg:text-7xl font-black mb-6 leading-[1.2] overflow-visible opacity-0">
                                    <span class="gradient-animate block py-2">DÜNYA DEVİ HELI</span>
                                    <span class="gradient-animate block py-2">ARTIK İXTİF'TE</span>
                                </h1>
                                <p class="hero-desc text-base md:text-xl lg:text-2xl text-gray-700 dark:text-gray-200 mb-8 leading-relaxed font-medium opacity-0">
                                    Yedek parça stoğu, yetkili servis ve teknik destek garantisiyle tam hizmet ve güvence
                                </p>
                                <div class="hero-cta mb-8 opacity-0">
                                    <a href="{{ route('shop.index') }}" class="group bg-red-50 hover:bg-red-100 dark:bg-red-700 dark:hover:bg-red-800 text-red-900 dark:text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                                        <i class="fa-light fa-forklift mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                        HELI Ürünleri İncele
                                    </a>
                                </div>
                                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-globe text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Global Lider</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Dünya markası</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-medal text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Üstün Kalite</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Uzun ömür</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-shield-check text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base whitespace-nowrap">Tam Güvence</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Garantili ürün</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 xl:hidden opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-layer-group text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Geniş Çeşit</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Her ihtiyaca</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-image flex items-center justify-center lg:justify-end lg:justify-self-end opacity-0">
                                <img src="{{ thumb('https://ixtif.com/storage/tenant2/1436/heli.png', 900, null, ['format' => 'webp', 'quality' => 85]) }}" alt="HELI Forklift" class="h-auto object-contain" loading="lazy">
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 3: EP -->
                    <div class="swiper-slide slide-3-bg">
                        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                            <div class="text-gray-900 dark:text-white">
                                <h1 class="hero-title text-3xl md:text-5xl lg:text-7xl font-black mb-6 leading-[1.2] overflow-visible opacity-0">
                                    <span class="gradient-animate block py-2">EP İLE MODERN</span>
                                    <span class="gradient-animate block py-2">DEPOLAR</span>
                                </h1>
                                <p class="hero-desc text-base md:text-xl lg:text-2xl text-gray-700 dark:text-gray-200 mb-8 leading-relaxed font-medium opacity-0">
                                    Elektrikli motor, düşük işletme maliyeti, sessiz çalışma ve çevre dostu teknoloji ile üretim
                                </p>
                                <div class="hero-cta mb-8 opacity-0">
                                    <a href="{{ route('shop.index') }}" class="group bg-green-100 hover:bg-green-200 dark:bg-green-700 dark:hover:bg-green-800 text-green-900 dark:text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                                        <i class="fa-light fa-bolt mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                        EP Dünyasını Keşfet
                                    </a>
                                </div>
                                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-leaf text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Çevre Dostu</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Yeşil teknoloji</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-sparkles text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Yeni Nesil</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Modern tasarım</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-volume-slash text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base whitespace-nowrap">Sessiz Çalışma</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Konforlu kullanım</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 xl:hidden opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-piggy-bank text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Düşük Maliyet</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Enerji tasarrufu</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-image flex items-center justify-center lg:justify-end lg:justify-self-end opacity-0">
                                <img src="{{ thumb('https://ixtif.com/storage/tenant2/1435/ep.png', 900, null, ['format' => 'webp', 'quality' => 85]) }}" alt="EP Elektrikli İstif" class="h-auto object-contain" loading="lazy">
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4: Türkiye'nin İstif Pazarı -->
                    <div class="swiper-slide slide-4-bg">
                        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
                            <div class="grid lg:grid-cols-2 gap-8 items-center">
                            <div class="text-gray-900 dark:text-white">
                                <h1 class="hero-title text-3xl md:text-5xl lg:text-7xl font-black mb-6 leading-[1.2] overflow-visible opacity-0">
                                    <span class="gradient-animate block py-2">TÜRKİYE'NİN</span>
                                    <span class="gradient-animate block py-2">İSTİF PAZARI</span>
                                </h1>
                                <p class="hero-desc text-base md:text-xl lg:text-2xl text-gray-700 dark:text-gray-200 mb-8 leading-relaxed font-medium opacity-0">
                                    Profesyonel istif çözümleri, güçlü stok ve hızlı teslimat ile işletmenizin güvenilir ortağı
                                </p>
                                <div class="hero-cta mb-8 opacity-0">
                                    <a href="{{ route('shop.index') }}" class="group bg-blue-100 hover:bg-blue-200 dark:bg-blue-700 dark:hover:bg-blue-800 text-blue-900 dark:text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                                        <i class="fa-light fa-shopping-cart mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                        Ürünleri İncele
                                    </a>
                                </div>
                                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-boxes-stacked text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Güçlü Stok</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Zengin ürün çeşidi</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-certificate text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Garantili Ürün</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-award text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base whitespace-nowrap">Profesyonel Ekip</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Uzman danışmanlık</div>
                                        </div>
                                    </div>
                                    <div class="hero-feature flex items-center gap-4 xl:hidden opacity-0">
                                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-truck-fast text-blue-600 dark:text-blue-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-base">Hızlı Teslimat</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Aynı gün kargo</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hero-image flex items-center justify-center lg:justify-end lg:justify-self-end opacity-0">
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png" alt="İXTİF İstif Makinesi" class="h-auto object-contain" loading="lazy">
                            </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Navigation Arrows (Outside Slider - Minimal & Transparent) -->
                <button class="hero-nav-prev absolute left-2 lg:left-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 lg:w-12 lg:h-12 flex items-center justify-center rounded-full bg-white/70 dark:bg-slate-800/30 backdrop-blur-sm border border-slate-300/50 dark:border-slate-700/50 hover:bg-white/90 dark:hover:bg-slate-800/50 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-300 group shadow-lg">
                    <i class="fa-solid fa-chevron-left text-slate-700 dark:text-white text-xl lg:text-2xl group-hover:-translate-x-0.5 transition-transform"></i>
                </button>
                <button class="hero-nav-next absolute right-2 lg:right-4 top-1/2 -translate-y-1/2 z-10 w-10 h-10 lg:w-12 lg:h-12 flex items-center justify-center rounded-full bg-white/70 dark:bg-slate-800/30 backdrop-blur-sm border border-slate-300/50 dark:border-slate-700/50 hover:bg-white/90 dark:hover:bg-slate-800/50 hover:border-slate-400 dark:hover:border-slate-600 transition-all duration-300 group shadow-lg">
                    <i class="fa-solid fa-chevron-right text-slate-700 dark:text-white text-xl lg:text-2xl group-hover:translate-x-0.5 transition-transform"></i>
                </button>
            </div>

            <!-- Modern Pagination with Play/Pause (Outside swiper) -->
            <div class="container mx-auto px-4 sm:px-4 md:px-0">
                <div class="flex justify-start items-center gap-4 mt-4">
                    <!-- Play/Pause Button -->
                    <button id="heroPlayPause" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/30 dark:bg-slate-700/30 backdrop-blur-sm hover:bg-blue-500/60 dark:hover:bg-blue-500/50 transition-all duration-300 group">
                        <i class="fa-solid fa-pause text-slate-900 dark:text-slate-300 text-sm group-hover:text-white transition-colors"></i>
                    </button>

                    <!-- Pagination Dots -->
                    <div class="swiper-pagination-custom flex justify-start items-center gap-3"></div>
                </div>
            </div>
    </section>

<!-- Featured Products Section -->
<section class="w-full py-8 relative overflow-hidden" x-data="{
    viewMode: (window.innerWidth < 1024 ? 'list' : 'grid'),

    // Cookie helper functions
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },

    setCookie(name, value, days = 365) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = `${name}=${value}; expires=${expires}; path=/; SameSite=Lax`;
    },

    getDefaultView() {
        return window.innerWidth < 1024 ? 'list' : 'grid';
    },

    init() {
        // Priority: Cookie > localStorage > Responsive Default
        const cookieValue = this.getCookie('viewMode');
        const localValue = localStorage.getItem('viewMode');

        if (cookieValue) {
            this.viewMode = cookieValue;
            // Cookie varsa localStorage'ı da güncelle (senkronize)
            localStorage.setItem('viewMode', cookieValue);
        } else if (localValue) {
            this.viewMode = localValue;
            // localStorage varsa cookie'ye de yaz
            this.setCookie('viewMode', localValue);
        } else {
            // Hiçbiri yoksa responsive default
            this.viewMode = this.getDefaultView();
        }

        // viewMode değişikliklerini hem localStorage hem cookie'ye kaydet
        this.$watch('viewMode', value => {
            localStorage.setItem('viewMode', value);
            this.setCookie('viewMode', value);
        });
    }
}" x-init="init()">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">

        {{-- Section Header with View Toggle --}}
        <div class="flex items-center justify-between mb-12">
            {{-- Title --}}
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-12 bg-gradient-to-b from-blue-600 via-purple-600 to-pink-600 rounded-full"></div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                    Efsane Yılsonu Fırsatları İXTİF'te Başladı! <span class="fire-emoji inline-block">🔥</span>
                </h2>
            </div>

            {{-- Animated View Toggle Button --}}
            <button
                @click="viewMode = viewMode === 'grid' ? 'list' : 'grid'"
                class="relative w-14 h-14 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 group overflow-hidden">

                {{-- Grid Icon --}}
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-500"
                     :class="viewMode === 'grid' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-50'">
                    <i class="fa-solid fa-grid-2 text-xl text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                </div>

                {{-- List Icon --}}
                <div class="absolute inset-0 flex items-center justify-center transition-all duration-500"
                     :class="viewMode === 'list' ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-90 scale-50'">
                    <i class="fa-solid fa-list text-xl text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                </div>

                {{-- Hover Gradient Ring --}}
                <div class="product-card-gradient absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </button>
        </div>

        {{-- Products Container --}}
        <div x-show="viewMode === 'grid'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            {{-- VIP Products (İlk 2 ürün - Görsel solda, yazı sağda, col-6, BÜYÜK) --}}
            @if($homepageProducts->count() >= 2)
            <div class="grid grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
                @foreach($homepageProducts->take(2) as $index => $product)
                @php
                    $vipProductId = $product['id'];
                    $vipProductTitle = $product['title'];
                    $vipProductUrl = $product['url'];
                    $vipProductImage = $product['image'];
                    $vipProductCategory = $product['category'] ?? 'Genel';
                    $vipProductCategoryIcon = $product['category_icon'] ?? 'fa-light fa-box';
                    $vipProductFormattedPrice = $product['formatted_price'];
                    $vipProductTryPrice = $product['try_price'] ?? null;
                    $vipProductCurrencyCode = $product['currency'] ?? 'TRY';
                    $vipProductBasePrice = $product['price'] ?? 0;
                    $vipProductDiscountPercentage = $product['auto_discount_percentage'] ?? null;
                    $vipProductBadges = $product['badges'] ?? [];

                    // Compare price logic (component ile aynı)
                    $vipProductCompareAtPrice = $product['compare_at_price'] ?? null;
                    $vipProductCurrencySymbol = $product['currency_symbol'] ?? '₺';

                    // Yuvarlama fonksiyonu tanımla (component'teki gibi)
                    if (!function_exists('roundComparePrice')) {
                        function roundComparePrice($price) {
                            if (!$price) return null;
                            $lastTwo = $price % 100;
                            if ($lastTwo <= 24) {
                                return floor($price / 100) * 100;
                            }
                            elseif ($lastTwo <= 74) {
                                return floor($price / 100) * 100 + 50;
                            }
                            else {
                                return ceil($price / 100) * 100;
                            }
                        }
                    }

                    // Yuvarlama uygula (00 veya 50'ye)
                    if ($vipProductCompareAtPrice) {
                        $vipProductCompareAtPrice = roundComparePrice($vipProductCompareAtPrice);
                    }

                    // Format et (sadece base price'dan büyükse göster)
                    $vipFormattedComparePrice = null;
                    if ($vipProductCompareAtPrice && $vipProductCompareAtPrice > $vipProductBasePrice) {
                        $vipFormattedComparePrice = number_format($vipProductCompareAtPrice, 0, ',', '.') . ' ' . $vipProductCurrencySymbol;
                    }

                    // Badge renk gradient map
                    $badgeGradients = [
                        'green' => 'from-emerald-600 to-green-600',
                        'red' => 'from-red-600 to-rose-600',
                        'orange' => 'from-orange-600 to-amber-600',
                        'blue' => 'from-blue-600 to-cyan-600',
                        'yellow' => 'from-yellow-500 to-amber-500',
                        'purple' => 'from-purple-600 to-fuchsia-600',
                        'emerald' => 'from-emerald-500 to-teal-600',
                        'indigo' => 'from-indigo-600 to-blue-600',
                        'cyan' => 'from-cyan-600 to-blue-500',
                        'gray' => 'from-gray-600 to-slate-600',
                    ];

                    // Badge label helper
                    $getBadgeLabel = function($badge) {
                        $labels = [
                            'new_arrival' => 'Yeni',
                            'discount' => '%' . ($badge['value'] ?? '0') . ' İndirim',
                            'limited_stock' => 'Son ' . ($badge['value'] ?? '0') . ' Adet',
                            'free_shipping' => 'Ücretsiz Kargo',
                            'bestseller' => 'Çok Satan',
                            'featured' => 'Öne Çıkan',
                            'eco_friendly' => 'Çevre Dostu',
                            'warranty' => ($badge['value'] ?? '0') . ' Ay Garanti',
                            'pre_order' => 'Ön Sipariş',
                            'imported' => 'İthal',
                            'custom' => $badge['label']['tr'] ?? 'Özel',
                        ];
                        return $labels[$badge['type'] ?? 'custom'] ?? ($badge['label']['tr'] ?? 'Badge');
                    };

                    // Aktif badge'leri filtrele ve sırala (is_array kontrolü ekle)
                    $vipActiveBadges = [];
                    if (is_array($vipProductBadges) && count($vipProductBadges) > 0) {
                        $vipActiveBadges = array_filter($vipProductBadges, fn($b) => is_array($b) && ($b['is_active'] ?? false));
                        usort($vipActiveBadges, fn($a, $b) => ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999));
                    }
                @endphp
                <div x-data="productCard({{ $vipProductTryPrice ? 'true' : 'false' }}, {{ $vipProductId }})"
                     class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">

                    {{-- Badges (Ana container üstünde - responsive) --}}
                    <div class="absolute top-2 left-2 md:top-3 md:left-3 z-20 flex flex-col gap-1.5 md:gap-2 items-start">
                        {{-- İndirim Badge --}}
                        @if($vipProductDiscountPercentage && $vipProductDiscountPercentage >= 5)
                            <div class="bg-gradient-to-br from-orange-600 to-red-600 text-white px-2 py-1 md:px-3 md:py-1.5 rounded-lg shadow-lg text-xs md:text-sm font-bold">
                                -%{{ $vipProductDiscountPercentage }}
                            </div>
                        @endif

                        {{-- Dinamik Badge'ler (renk ve animasyon destekli) --}}
                        @foreach($vipActiveBadges as $badgeIndex => $badge)
                            @php
                                $badgeColor = $badge['color'] ?? 'gray';
                                $badgeGradient = $badgeGradients[$badgeColor] ?? 'from-gray-600 to-slate-600';
                                $isFirstBadge = $badgeIndex === 0;
                                $animationClass = $isFirstBadge ? 'bg-[length:200%_200%] animate-gradient' : '';
                            @endphp
                            <div class="bg-gradient-to-br {{ $badgeGradient }} {{ $animationClass }} text-white px-2 py-1 md:px-3 md:py-1.5 rounded-lg shadow-lg text-xs md:text-sm font-bold">
                                {{ $getBadgeLabel($badge) }}
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col md:flex-row">
                        {{-- Sol: Büyük Görsel --}}
                        <div class="relative md:w-1/2 aspect-square md:aspect-auto md:min-h-[280px]">
                            <a href="{{ $vipProductUrl }}" class="block w-full h-full rounded-xl md:rounded-l-2xl md:rounded-r-none flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
                                @if($vipProductImage)
                                    <img src="{{ $vipProductImage }}"
                                         alt="{{ $vipProductTitle }}"
                                         class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark p-6 md:p-8"
                                         loading="lazy">
                                @else
                                    <i class="{{ $vipProductCategoryIcon }} text-6xl md:text-8xl text-blue-400 dark:text-blue-400"></i>
                                @endif
                            </a>
                        </div>

                        {{-- Sağ: İçerik --}}
                        <div class="md:w-1/2 p-5 md:p-6 lg:p-8 flex flex-col justify-between">
                            {{-- Kategori --}}
                            <div class="flex items-center gap-2 mb-3">
                                <span class="flex items-center gap-1.5 text-sm text-blue-800 dark:text-blue-300 font-medium uppercase tracking-wider">
                                    <i class="{{ $vipProductCategoryIcon }} text-base"></i>
                                    {{ $vipProductCategory }}
                                </span>
                            </div>

                            {{-- Başlık (BÜYÜK) --}}
                            <a href="{{ $vipProductUrl }}">
                                <h3 class="text-xl md:text-2xl lg:text-3xl font-bold line-clamp-3 text-gray-950 dark:text-gray-50 leading-tight group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors mb-4">
                                    {{ $vipProductTitle }}
                                </h3>
                            </a>

                            {{-- Fiyat & Sepete Ekle --}}
                            <div class="pt-4 mt-auto flex items-center justify-between gap-2 sm:gap-3 md:gap-4">
                                <div class="flex-1 min-w-0 overflow-hidden">
                                    {{-- Üstü çizili eski fiyat (varsa) - Component ile aynı --}}
                                    @if(isset($vipFormattedComparePrice) && $vipFormattedComparePrice)
                                        <div class="relative inline-block -mb-3 sm:-mb-4 lg:-mb-2">
                                            <span class="text-xs sm:text-sm lg:text-sm text-gray-400 dark:text-gray-500 font-medium leading-none">
                                                {{ $vipFormattedComparePrice }}
                                            </span>
                                            {{-- Çapraz çizgi (diagonal line) --}}
                                            <span class="absolute inset-0 flex items-center justify-center">
                                                <span class="w-full h-[1.5px] bg-gradient-to-r from-transparent via-red-500 to-transparent transform rotate-[-8deg] opacity-70"></span>
                                            </span>
                                        </div>
                                    @endif

                                    @if($vipProductTryPrice && $vipProductCurrencyCode !== 'TRY')
                                        <div class="relative h-8 w-fit flex items-center cursor-pointer"
                                             @mouseenter="priceHovered = true; showTryPrice = true"
                                             @mouseleave="priceHovered = false; showTryPrice = false">
                                            {{-- USD Price (default) --}}
                                            <div class="text-lg md:text-xl lg:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 transition-all duration-150 whitespace-nowrap"
                                                 x-show="!showTryPrice">
                                                {{ $vipProductFormattedPrice }}
                                            </div>
                                            {{-- TRY Price (hover ile gösterim) --}}
                                            <div class="text-lg md:text-xl lg:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 dark:from-green-300 dark:via-emerald-300 dark:to-teal-300 absolute top-0 left-0 transition-all duration-150 scale-105 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)] whitespace-nowrap"
                                                 x-show="showTryPrice" style="display: none;">
                                                {{ $vipProductTryPrice }} ₺
                                            </div>
                                        </div>
                                    @else
                                        {{-- TRY Only Price --}}
                                        <div class="text-lg md:text-xl lg:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 h-8 flex items-center whitespace-nowrap">
                                            {{ $vipProductFormattedPrice }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Sepete Ekle (alttakilerle aynı style) --}}
                                @if($vipProductBasePrice > 0)
                                <button type="button" @click="addToCart()" :disabled="loading || success"
                                        class="flex-shrink-0 border-2 border-blue-600 dark:border-blue-400 hover:border-blue-700 dark:hover:border-blue-300 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                        :class="{ 'animate-pulse': loading, '!border-green-600 !text-green-600 !bg-green-50 dark:!bg-green-900/20': success }">
                                    <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-110">
                                        <i class="fa-solid text-lg transition-all duration-300" :class="{ 'fa-spinner fa-spin': loading, 'fa-check scale-125': success, 'fa-cart-plus': !loading && !success }"></i>
                                    </span>
                                    <span class="max-w-0 overflow-hidden group-hover:max-w-[5rem] transition-all duration-300 text-[10px] font-medium pl-0 group-hover:pl-2 leading-[1.2] flex items-center text-center">
                                        <span class="whitespace-pre-line block" x-html="loading ? 'Ekle' : (success ? 'Tamam!' : 'Sepete<br>Ekle')"></span>
                                    </span>
                                </button>
                                @else
                                <a href="{{ url('/sizi-arayalim') }}" class="flex-shrink-0 bg-gradient-to-br from-green-700 to-emerald-700 hover:from-green-800 hover:to-emerald-800 text-white rounded-lg shadow-md transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 hover:shadow-2xl hover:shadow-green-500/50 active:scale-95">
                                    <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                                        <i class="fa-solid fa-phone text-base"></i>
                                    </span>
                                    <span class="max-w-0 overflow-hidden group-hover:max-w-[5rem] transition-all duration-300 text-[10px] font-semibold pl-0 group-hover:pl-2 leading-[1.1] flex items-center">
                                        <span class="whitespace-pre-line">Sizi{{ "\n" }}Arayalım</span>
                                    </span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Regular Products (2 VIP + 15 normal = 17 toplam) --}}
            {{-- Responsive gizleme: 2 kolon=4 göster, 3 kolon=6 göster, 4 kolon=12 göster, 5 kolon=15 göster --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                @foreach($homepageProducts->skip(2)->take(15) as $index => $product)
                    <x-ixtif.product-card
                        :product="$product"
                        layout="vertical"
                        :showAddToCart="true"
                        :showDivider="false"
                        :index="$index + 2"
                        class="
                            {{ $index >= 4 ? 'hidden md:block' : '' }}
                            {{ $index >= 6 ? 'hidden lg:block' : '' }}
                            {{ $index >= 12 ? 'hidden xl:block' : '' }}
                        "
                    />
                @endforeach
            </div>
        </div>

        <div x-show="viewMode === 'list'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="grid grid-cols-1 lg:grid-cols-2 gap-6"
             style="display: none;">
            @foreach($homepageProducts as $index => $product)
                <x-ixtif.product-card
                    :product="$product"
                    layout="horizontal"
                    :showAddToCart="true"
                    :index="$index"
                />
            @endforeach
        </div>
    </div>
</section>

<!-- Service Categories Section -->
<section class="w-full py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <!-- Service Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- 1. Satın Alma -->
            <a href="{{ href('Page', 'show', 'satin-alma') }}" class="group relative h-56 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-4 md:p-6 lg:p-8">
                    <i class="fa-light fa-shopping-cart text-4xl md:text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-3 md:mb-4 group-hover:animate-slide-x"></i>
                    <h3 class="text-lg md:text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Satın Alma</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-3">Akülü forklift, dizel forklift, transpalet, reach truck ve ikinci el forklift satışı</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 2. Kiralama -->
            <a href="{{ href('Page', 'show', 'kiralama') }}" class="group relative h-56 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-4 md:p-6 lg:p-8">
                    <i class="fa-light fa-calendar-days text-4xl md:text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-3 md:mb-4 group-hover:animate-swing"></i>
                    <h3 class="text-lg md:text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Kiralama</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-3">Kiralık forklift, transpalet ve otonom istif makinesi günlük-uzun dönem kiralaması</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 3. Yedek Parça -->
            <a href="{{ href('Page', 'show', 'yedek-parca') }}" class="group relative h-56 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-4 md:p-6 lg:p-8">
                    <i class="fa-light fa-gear text-4xl md:text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-3 md:mb-4 group-hover:animate-spin transition-all"></i>
                    <h3 class="text-lg md:text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">Yedek Parça</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-3">Forklift yedek parça, akülü forklift ve reach truck parçaları 7/24 stok garantili</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 4. Teknik Servis -->
            <a href="{{ href('Page', 'show', 'teknik-servis') }}" class="group relative h-56 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-4 md:p-6 lg:p-8">
                    <i class="fa-light fa-wrench text-4xl md:text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-3 md:mb-4 group-hover:animate-rotate-wiggle"></i>
                    <h3 class="text-lg md:text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">Teknik Servis</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2 md:mb-3">Forklift bakım, periyodik bakım, profesyonel servis ve bakım anlaşmaları</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="w-full py-8 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Sol: Görsel (Ken Burns + Parallax Efekt) --}}
            <div class="relative rounded-3xl h-[350px] md:h-[450px] lg:h-[600px] overflow-hidden" id="aboutPhotoContainer">
                <img src="https://ixtif.com/storage/tenant2/5/super-hero.jpg"
                     alt="İxtif - Depoda, Üretimde, Dağıtımda"
                     class="w-full h-full object-cover about-hero-photo"
                     loading="lazy"
                     id="aboutHeroPhoto">
            </div>

            {{-- Sağ: İçerik --}}
            <div>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-6">Depoda, Üretimde, Dağıtımda</h2>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                    Depoda, üretimde, dağıtımda işletmelerin yükünü hafifletmek için çalışan <span class="md:whitespace-nowrap">İXTİF İÇ VE DIŞ TİCARET ANONİM ŞİRKETİ,</span> satışın yanında kiralama, ikinci el, teknik servis ve bakım anlaşmalarıyla tam çözüm sunar.
                </p>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                    Garanti kapsamlı ürünler, hızlı yedek parça temini ve 7/24 saha desteğiyle operasyonlarınızı güvenle sürdürmenizi sağlar.
                </p>

                {{-- Özellikler --}}
                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="text-center group cursor-pointer">
                        <div class="text-4xl font-black text-blue-600 mb-2">
                            <i class="fa-light fa-truck-fast inline-block group-hover:animate-slide-x-infinite"></i>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">Hızlı Teslimat</div>
                    </div>
                    <div class="text-center group cursor-pointer">
                        <div class="text-4xl font-black text-blue-600 mb-2">
                            <i class="fa-light fa-shield-check inline-block group-hover:animate-pulse-scale"></i>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">Garanti Güvencesi</div>
                    </div>
                    <div class="text-center group cursor-pointer">
                        <div class="text-4xl font-black text-blue-600 mb-2">
                            <i class="fa-light fa-screwdriver-wrench inline-block group-hover:animate-rotate-wiggle-infinite"></i>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">Teknik Servis</div>
                    </div>
                </div>

                {{-- CTA Butonu --}}
                <a href="{{ href('Page', 'show', 'hakkimizda') }}" class="group bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                    <i class="fa-regular fa-circle-info mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                    Hakkımızda Daha Fazla
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Son Blog Yazıları Section - 5 Farklı Glass Tasarım -->
@php
    // Media kaydı olan blogları çek (hero veya featured_image)
    $latestBlogs = \Modules\Blog\App\Models\Blog::published()
        ->with('media')
        ->whereHas('media', function($query) {
            $query->whereIn('collection_name', ['hero', 'featured_image', 'gallery']);
        })
        ->orderBy('published_at', 'desc')
        ->take(6)
        ->get();
@endphp

@if($latestBlogs->isNotEmpty())

{{-- ==================== BLOG SECTION: STACKED GLASS LAYERS ==================== --}}
<section class="w-full py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="flex items-center justify-between mb-12">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-12 bg-gradient-to-b from-blue-600 via-purple-600 to-pink-600 rounded-full"></div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">iXtif Akademi</h2>
            </div>
            <a href="/blog" class="hidden md:flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:text-blue-700 font-semibold transition-colors">
                Tümünü Gör <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            @foreach($latestBlogs as $blog)
                @php
                    $blogTitle = is_array($blog->title) ? ($blog->title['tr'] ?? '') : $blog->title;
                    $blogSlug = is_array($blog->slug) ? ($blog->slug['tr'] ?? '') : $blog->slug;
                    $blogExcerpt = is_array($blog->excerpt) ? ($blog->excerpt['tr'] ?? '') : ($blog->excerpt ?? '');
                    $blogUrl = '/blog/' . $blogSlug;
                    // Helper function ile multi-collection fallback
                    $blogMedia = getFirstMediaWithFallback($blog);
                    $blogImage = $blogMedia ? thumb($blogMedia, 400, 300, ['quality' => 85, 'format' => 'webp']) : null;
                    $blogDate = $blog->published_at ? $blog->published_at->format('d.m.Y') : '';

                    // Okuma süresi - Blog detay sayfasıyla aynı metod
                    $readTime = $blog->calculateReadingTime('tr');

                    // Content'i al (excerpt için fallback)
                    $blogContent = is_array($blog->content) ? ($blog->content['tr'] ?? '') : ($blog->content ?? '');
                @endphp

                <a href="{{ $blogUrl }}" class="group block h-full">
                    {{-- Card Container --}}
                    <div class="relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-500/50 hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                        {{-- Image with Layered Glass Effect --}}
                        <div class="relative h-52 sm:h-56 lg:h-64 overflow-hidden flex-shrink-0">
                            @if($blogImage)
                                <img src="{{ $blogImage }}" alt="{{ $blogTitle }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 via-purple-600 to-pink-600 flex items-center justify-center">
                                    <i class="fa-solid fa-newspaper text-5xl text-white/30"></i>
                                </div>
                            @endif

                            {{-- Multi-layer Glass Overlay --}}
                            <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-white dark:from-gray-900 via-white/40 dark:via-gray-900/40 to-transparent backdrop-blur-[1px]"></div>

                            {{-- Floating Glass Stats --}}
                            <div class="absolute top-2 left-2 right-2 sm:top-4 sm:left-4 sm:right-4 flex justify-between items-start">
                                <div class="flex items-center gap-1 sm:gap-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 sm:px-3 sm:py-1.5 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg text-[10px] sm:text-xs font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 shadow-lg">
                                        <i class="fa-regular fa-calendar text-blue-500 dark:text-blue-400"></i>
                                        <span class="hidden sm:inline">{{ $blogDate }}</span>
                                        <span class="sm:hidden">{{ $blog->published_at ? $blog->published_at->format('d.m') : $blog->created_at->format('d.m') }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 sm:px-3 sm:py-1.5 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg text-[10px] sm:text-xs font-medium text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-600 shadow-lg">
                                        <i class="fa-regular fa-clock text-blue-500 dark:text-blue-400"></i>
                                        <span>{{ $readTime }}<span class="hidden sm:inline"> dakika</span></span>
                                    </span>
                                </div>
                                {{-- Favoriye Ekle Butonu --}}
                                @auth
                                <div x-data="favoriteButton('{{ addslashes(get_class($blog)) }}', {{ $blog->id }}, {{ $blog->isFavoritedBy(auth()->id()) ? 'true' : 'false' }})"
                                     @click.prevent.stop="toggleFavorite()"
                                     class="group/fav w-6 h-6 sm:w-8 sm:h-8 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600 shadow-lg hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-300 dark:hover:border-red-500/50 hover:scale-110 transition-all duration-200 cursor-pointer"
                                     aria-label="Favorilere Ekle">
                                    <i :class="favorited ? 'fa-solid fa-heart text-red-500' : 'fa-regular fa-heart text-gray-400 group-hover/fav:text-red-400'" class="text-[10px] sm:text-sm transition-all duration-200"></i>
                                </div>
                                @else
                                <span onclick="event.preventDefault(); event.stopPropagation(); window.location.href='{{ route('login') }}'"
                                   class="group/fav w-6 h-6 sm:w-8 sm:h-8 bg-white/90 dark:bg-gray-800/80 backdrop-blur-md rounded-md sm:rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600 shadow-lg hover:bg-red-50 dark:hover:bg-red-900/30 hover:border-red-300 dark:hover:border-red-500/50 hover:scale-110 transition-all duration-200 cursor-pointer"
                                   aria-label="Giriş Yap">
                                    <i class="fa-regular fa-heart text-gray-400 group-hover/fav:text-red-400 text-[10px] sm:text-sm transition-all duration-200"></i>
                                </span>
                                @endauth
                            </div>

                        </div>

                        {{-- Content --}}
                        <div class="p-3 pt-2 sm:p-6 sm:pt-4 flex-1 flex flex-col relative z-10 bg-white/70 dark:bg-white/5">
                            <h3 class="text-sm sm:text-lg font-bold text-gray-900 dark:text-white mb-2 sm:mb-3 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $blogTitle }}
                            </h3>

                            {{-- Excerpt - Sabit yükseklik --}}
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 line-clamp-2 sm:line-clamp-3 flex-1">
                                {{ $blogExcerpt ?: Str::limit(strip_tags($blogContent), 120) }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

@endif

<!-- Quick View Modal -->
<div x-show="showModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
     @click="closeModal()"
     style="display: none;">

    <div @click.stop class="bg-white dark:bg-slate-800 rounded-2xl md:rounded-3xl max-w-5xl w-full max-h-[90vh] overflow-y-auto relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100">

        <!-- Close Button -->
        <button @click="closeModal()" class="sticky top-4 right-4 float-right w-10 h-10 md:w-12 md:h-12 bg-gray-900/80 dark:bg-white/20 hover:bg-gray-900 dark:hover:bg-white/30 rounded-full flex items-center justify-center z-10 transition-colors backdrop-blur-sm">
            <i class="fa-solid fa-xmark text-xl md:text-2xl text-white"></i>
        </button>

        <div class="p-4 md:p-6 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-6 md:gap-8 lg:gap-12" x-show="selectedProduct">
                <!-- Left: Product Images -->
                <div>
                    <!-- Main Image -->
                    <div x-data="{ currentImage: 0 }">
                        <div class="aspect-square bg-gradient-to-br from-blue-100 to-purple-100 dark:from-slate-700 dark:to-slate-600 rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                            <template x-if="selectedProduct && selectedProduct.images && selectedProduct.images.length > 0">
                                <img :src="selectedProduct.images[currentImage]"
                                     :alt="selectedProduct.title"
                                     class="w-full h-full object-cover">
                            </template>
                            <template x-if="selectedProduct && (!selectedProduct.images || selectedProduct.images.length === 0)">
                                <i class="fa-light fa-box text-[8rem] lg:text-[12rem] text-blue-400 dark:text-blue-300"></i>
                            </template>
                        </div>

                        <!-- Thumbnails -->
                        <div class="grid grid-cols-4 gap-3" x-show="selectedProduct && selectedProduct.images && selectedProduct.images.length > 1">
                            <template x-for="(image, index) in (selectedProduct?.images || [])" :key="index">
                                <button @click="currentImage = index"
                                        class="aspect-square bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden transition-all"
                                        :class="currentImage === index ? 'ring-2 ring-2-blue-500' : 'opacity-60 hover:opacity-100'">
                                    <img :src="image" :alt="'Thumbnail ' + (index + 1)" class="w-full h-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right: Product Details -->
                <div>
                    <!-- Category & Brand -->
                    <div class="text-sm text-gray-800 dark:text-gray-200 mb-2">
                        <span x-text="selectedProduct?.category || 'Kategori'"></span>
                        <template x-if="selectedProduct?.brand">
                            <span> / <span x-text="selectedProduct.brand"></span></span>
                        </template>
                    </div>

                    <!-- Title -->
                    <h2 class="text-3xl lg:text-4xl font-black text-gray-950 dark:text-gray-50 mb-4" x-text="selectedProduct?.title || 'Ürün Adı'"></h2>

                    <!-- SKU -->
                    <div class="mb-6" x-show="selectedProduct?.sku">
                        <span class="text-sm text-gray-800 dark:text-gray-200">Ürün Kodu: </span>
                        <span class="text-sm font-semibold text-gray-950 dark:text-gray-50" x-text="selectedProduct?.sku"></span>
                    </div>

                    <!-- Short Description (Hero Text) -->
                    <p class="text-lg text-gray-900 dark:text-gray-100 mb-6 leading-relaxed" x-show="selectedProduct?.shortDescription" x-text="selectedProduct?.shortDescription"></p>

                    <!-- Primary Specs (4 Main Features) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6" x-show="selectedProduct?.primarySpecs && selectedProduct.primarySpecs.length > 0">
                        <template x-for="(spec, index) in selectedProduct?.primarySpecs?.slice(0, 4)" :key="index">
                            <div class="group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/15 via-purple-500/15 to-pink-500/15 dark:from-blue-300/25 dark:via-purple-300/25 dark:to-pink-300/25 rounded-xl blur-lg group-hover:blur-xl transition-all"></div>
                                <div class="relative bg-white/70 dark:bg-white/10 backdrop-blur-md border border-gray-300/50 dark:border-gray-500/50 rounded-xl p-4 hover:bg-white/80 dark:hover:bg-white/15 transition-all">
                                    <!-- Icon + Label -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-bolt text-white dark:text-gray-950 text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="spec.label"></h4>
                                    </div>
                                    <!-- Value -->
                                    <div class="text-xl font-bold text-gray-950 dark:text-gray-50" x-text="spec.value"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4">
                        <a :href="selectedProduct?.url || '#'"
                           class="flex-grow bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 dark:hover:from-blue-300 dark:hover:via-purple-300 dark:hover:to-pink-300 text-white dark:text-gray-950 py-4 rounded-xl font-bold text-base lg:text-lg transition-all text-center">
                            <i class="fa-light fa-arrow-right mr-2"></i>
                            Ürün Sayfasına Git
                        </a>
                        <button class="w-14 h-14 lg:w-16 lg:h-16 border-2 border-gray-400 dark:border-gray-400 text-gray-700 dark:text-gray-200 rounded-xl flex items-center justify-center hover:border-red-700 hover:text-red-700 dark:hover:border-red-300 dark:hover:text-red-300 transition-colors">
                            <i class="fa-light fa-heart text-xl lg:text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section - Service Categories Style (Alternative) -->
<section class="py-20">
    <div class="container mx-auto px-4 sm:px-4 md:px-0">
        <div class="flex flex-wrap lg:flex-nowrap">
            <!-- 1. Telefon -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="hidden lg:block absolute right-0 top-0 bottom-0 w-[3px] bg-gradient-to-b from-transparent via-blue-500 dark:via-blue-400 to-transparent"></div>
                <a href="tel:02167553555" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500 group-hover:rotate-12">
                            <i class="fa-light fa-phone text-5xl lg:text-6xl text-white transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">Telefon</h3>
                        <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2">Hemen arayın</p>
                        <p class="text-sm lg:text-base text-blue-600 dark:text-blue-400 font-semibold">0216 755 3 555</p>
                    </div>
                </a>
            </div>

            <!-- 2. WhatsApp -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="hidden lg:block absolute right-0 top-0 bottom-0 w-[3px] bg-gradient-to-b from-transparent via-blue-500 dark:via-blue-400 to-transparent"></div>
                <a href="{{ whatsapp_link() }}" target="_blank" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500 group-hover:rotate-12">
                            <i class="fa-brands fa-whatsapp text-5xl lg:text-6xl text-white transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">WhatsApp</h3>
                        <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2">Anında mesajlaşın</p>
                        <p class="text-sm lg:text-base text-blue-600 dark:text-blue-400 font-semibold">0501 005 67 58</p>
                    </div>
                </a>
            </div>

            <!-- 3. E-posta -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="hidden lg:block absolute right-0 top-0 bottom-0 w-[3px] bg-gradient-to-b from-transparent via-blue-500 dark:via-blue-400 to-transparent"></div>
                <a href="mailto:info@ixtif.com" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500 group-hover:rotate-12">
                            <i class="fa-light fa-envelope text-5xl lg:text-6xl text-white transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">E-posta</h3>
                        <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2">Mail gönderin</p>
                        <p class="text-sm lg:text-base text-blue-600 dark:text-blue-400 font-semibold">info@ixtif.com</p>
                    </div>
                </a>
            </div>

            <!-- 4. Canlı Destek -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <button @click="if(window.Alpine?.store('aiChat')?.openFloating) { window.Alpine.store('aiChat').openFloating(); } else { console.warn('AI Chat store not ready'); }" class="group block w-full">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center relative">
                        <!-- Yapay Zeka Badge -->
                        <div class="absolute top-2 right-2 md:top-4 md:right-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 md:px-2 md:py-1 bg-gradient-to-r from-blue-500/30 to-blue-500/30 backdrop-blur-sm text-gray-900 dark:text-white text-[10px] font-bold rounded-full italic border border-blue-300 dark:border-blue-600">
                                <i class="fa-light fa-sparkles text-blue-600 dark:text-blue-400"></i>
                                Yapay Zeka
                            </span>
                        </div>
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500 group-hover:rotate-12">
                            <i class="fa-light fa-robot text-5xl lg:text-6xl text-white transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">Canlı Destek</h3>
                        <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-2">Yapay Zeka Destekli</p>
                        <p class="text-sm lg:text-base text-blue-600 dark:text-blue-400 font-semibold">Sohbete Başla</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- Alpine.js Component --}}
{{-- CSS and JS moved to external files: public/themes/ixtif/css/homepage.css and public/themes/ixtif/js/homepage.js --}}
</div>
@endsection
