@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'ixtif';
    
    $version = (int) request()->query('v', 1);
    $version = max(1, min(10, $version));
    
    $prevVersion = $version > 1 ? $version - 1 : null;
    $nextVersion = $version < 10 ? $version + 1 : null;
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
<div class="relative">

    {{-- ============================================================
         VERSION 1: SITE + HİZMETLER TANITIMI
         Focus: Platform özellikleri, ne sunuyoruz
         Slayt: Ürün carousel (optional, bottom)
    ============================================================ --}}
    @if($version === 1)
    <section class="relative py-16 md:py-20 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="container mx-auto px-4">
            {{-- Main Content --}}
            <div class="max-w-6xl mx-auto text-center space-y-12">
                
                {{-- Hero Message --}}
                <div class="space-y-6">
                    <div class="inline-block px-6 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">🏆 Türkiye'nin #1 İstif Platformu</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white leading-tight">
                        İstif Ekipmanı<br/>
                        <span class="text-blue-600 dark:text-blue-400">Aradığınız Her Şey</span><br/>
                        Bir Arada
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                        500+ ürün çeşidi, anlık stok takibi, hızlı teslimat ve profesyonel destek. 
                        İhtiyacınız olan her şey için tek adres.
                    </p>
                </div>

                {{-- Hizmetler Grid --}}
                <div class="grid md:grid-cols-4 gap-6 pt-8">
                    <div class="group p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-xl transition-all">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-search text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Akıllı Arama</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">500+ ürün arasından anında bulun</p>
                    </div>

                    <div class="group p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 hover:shadow-xl transition-all">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-bolt text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Hızlı Teslimat</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Sipariş sonrası 24 saat içinde</p>
                    </div>

                    <div class="group p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400 hover:shadow-xl transition-all">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-headset text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">7/24 Destek</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Her zaman yanınızdayız</p>
                    </div>

                    <div class="group p-6 bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-400 hover:shadow-xl transition-all">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-shield-check text-white text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Güvenli Alışveriş</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">SSL sertifikalı ödeme</p>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex justify-center gap-4 pt-6">
                    <a href="/shop" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all">
                        <i class="fa-solid fa-arrow-right mr-2"></i>
                        Hemen Başla
                    </a>
                    <a href="/iletisim" class="px-8 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-600 dark:hover:border-blue-400 text-gray-900 dark:text-white rounded-xl font-bold text-lg hover:shadow-xl transition-all">
                        Teklif Al
                    </a>
                </div>

                {{-- Ürün Slaytı (Küçük, Altta) --}}
                <div class="pt-12 border-t-2 border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6">Popüler Ürünler</h3>
                    <div class="flex overflow-x-auto gap-4 pb-4 scrollbar-hide">
                        <div class="flex-shrink-0 w-40 h-40 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:shadow-lg transition-shadow">
                            <i class="fa-solid fa-forklift text-5xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-shrink-0 w-40 h-40 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:shadow-lg transition-shadow">
                            <i class="fa-solid fa-truck text-5xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="flex-shrink-0 w-40 h-40 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:shadow-lg transition-shadow">
                            <i class="fa-solid fa-warehouse text-5xl text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="flex-shrink-0 w-40 h-40 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center hover:shadow-lg transition-shadow">
                            <i class="fa-solid fa-box text-5xl text-orange-600 dark:text-orange-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 2: SPLIT SCREEN MAGAZINE STYLE
         Focus: Editorial layout, sol content + sağ showcase
         Platform odaklı, modern magazin tarzı
    ============================================================ --}}
    @if($version === 2)
    <section class="relative py-20 md:py-24 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center max-w-7xl mx-auto">

                {{-- LEFT: Content Side --}}
                <div class="space-y-8">
                    {{-- Overline --}}
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-[2px] bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400"></div>
                        <span class="text-sm font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400">İstif Platformu</span>
                    </div>

                    {{-- Main Headline --}}
                    <div class="space-y-4">
                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 dark:text-white leading-[1.1]">
                            Endüstriyel<br/>
                            <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 dark:from-blue-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent">Ekipman</span><br/>
                            Çözümleri
                        </h1>

                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 leading-relaxed max-w-xl">
                            Profesyonel istif ekipmanı tedarikinde Türkiye'nin dijital platformu.
                            <strong class="text-gray-900 dark:text-white">Hızlı, güvenilir, teknolojik.</strong>
                        </p>
                    </div>

                    {{-- Services List --}}
                    <div class="space-y-4 pt-4">
                        <div class="group flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all cursor-pointer">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-layer-group text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Geniş Ürün Yelpazesi</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Forklift'ten transpalet'e 500+ çeşit</p>
                            </div>
                        </div>

                        <div class="group flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all cursor-pointer">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-gauge-high text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Anlık Stok Takibi</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Gerçek zamanlı envanter yönetimi</p>
                            </div>
                        </div>

                        <div class="group flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all cursor-pointer">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-truck-fast text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Express Teslimat</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">24 saat içinde kapınızda</p>
                            </div>
                        </div>

                        <div class="group flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all cursor-pointer">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-shield-halved text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">Güvenli İşlem</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">SSL sertifikalı ödeme altyapısı</p>
                            </div>
                        </div>
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-wrap gap-4 pt-4">
                        <a href="/shop" class="group px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-bold text-lg shadow-2xl hover:shadow-blue-500/50 dark:hover:shadow-blue-400/30 transition-all flex items-center gap-2">
                            Platformu Keşfet
                            <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="/iletisim" class="px-8 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-600 dark:hover:border-blue-400 text-gray-900 dark:text-white rounded-xl font-bold text-lg hover:shadow-xl transition-all">
                            İletişime Geç
                        </a>
                    </div>
                </div>

                {{-- RIGHT: Visual Showcase Side --}}
                <div class="relative lg:min-h-[600px]">
                    {{-- Background Decorative Elements --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-purple-50 to-blue-50 dark:from-gray-800 dark:via-gray-800/50 dark:to-gray-800 rounded-3xl"></div>
                    <div class="absolute top-10 right-10 w-72 h-72 bg-blue-400/20 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-10 left-10 w-64 h-64 bg-purple-400/20 dark:bg-purple-500/10 rounded-full blur-3xl"></div>

                    {{-- Content Cards --}}
                    <div class="relative h-full flex items-center justify-center p-8">
                        <div class="space-y-6 w-full max-w-md">
                            {{-- Stat Card 1 --}}
                            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl transform hover:scale-105 transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-boxes-stacked text-white text-2xl"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-black text-gray-900 dark:text-white">500+</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Ürün Çeşidi</div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Her ihtiyaca uygun ekipman seçeneği</p>
                            </div>

                            {{-- Stat Card 2 --}}
                            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl transform hover:scale-105 transition-all ml-auto max-w-xs">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-clock text-white text-2xl"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-black text-gray-900 dark:text-white">24sa</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Teslimat</div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Hızlı kargo desteği</p>
                            </div>

                            {{-- Stat Card 3 --}}
                            <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl p-6 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl transform hover:scale-105 transition-all">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                        <i class="fa-solid fa-headset text-white text-2xl"></i>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-black text-gray-900 dark:text-white">7/24</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Destek</div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">Profesyonel teknik destek ekibi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Trust Bar --}}
        <div class="container mx-auto px-4 pt-16">
            <div class="max-w-7xl mx-auto border-t border-gray-200 dark:border-gray-800 pt-8">
                <div class="flex flex-wrap items-center justify-center gap-8 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                        <span>SSL Güvenliği</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                        <span>Anlık Stok</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                        <span>Hızlı Kargo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                        <span>Profesyonel Destek</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 3: AUTO-ROTATING FEATURE SLIDER
         Focus: Otomatik dönen platform özellik slaytı
         Alpine.js ile smooth transitions
    ============================================================ --}}
    @if($version === 3)
    <section class="relative py-20 md:py-24 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 dark:from-black dark:via-gray-900 dark:to-black overflow-hidden"
             x-data="{
                currentSlide: 0,
                slides: [
                    {
                        title: 'Geniş Ürün Yelpazesi',
                        subtitle: '500+ Çeşit Ekipman',
                        description: 'Forklift, transpalet, istif makinası ve daha fazlası. Her ihtiyacınız için profesyonel çözümler.',
                        icon: 'fa-boxes-stacked',
                        color: 'from-blue-500 to-cyan-500',
                        bgColor: 'from-blue-500/20 to-cyan-500/20'
                    },
                    {
                        title: 'Anlık Stok Takibi',
                        subtitle: 'Gerçek Zamanlı',
                        description: 'Tüm ürünlerin stok durumunu anlık görün. Sipariş vermeden önce müsaitlik kontrolü yapın.',
                        icon: 'fa-gauge-high',
                        color: 'from-purple-500 to-pink-500',
                        bgColor: 'from-purple-500/20 to-pink-500/20'
                    },
                    {
                        title: 'Hızlı Teslimat',
                        subtitle: '24 Saat İçinde',
                        description: 'Express kargo ile sipariş sonrası 24 saat içinde adresinize teslim. Türkiye geneli hızlı gönderim.',
                        icon: 'fa-truck-fast',
                        color: 'from-green-500 to-emerald-500',
                        bgColor: 'from-green-500/20 to-emerald-500/20'
                    },
                    {
                        title: 'Profesyonel Destek',
                        subtitle: '7/24 Canlı Yardım',
                        description: 'Teknik ekibimiz her zaman yanınızda. Ürün seçiminden satış sonrasına kadar tam destek.',
                        icon: 'fa-headset',
                        color: 'from-orange-500 to-red-500',
                        bgColor: 'from-orange-500/20 to-red-500/20'
                    }
                ],
                autoplay: true,
                interval: null,
                init() {
                    this.startAutoplay();
                },
                startAutoplay() {
                    this.interval = setInterval(() => {
                        if (this.autoplay) {
                            this.nextSlide();
                        }
                    }, 4000);
                },
                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                },
                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                },
                goToSlide(index) {
                    this.currentSlide = index;
                    this.autoplay = false;
                }
             }"
             @mouseenter="autoplay = false"
             @mouseleave="autoplay = true">

        {{-- Background Animated Gradient --}}
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-6xl mx-auto">

                {{-- Slider Content --}}
                <div class="min-h-[500px] flex items-center justify-center">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="currentSlide === index"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="text-center space-y-8 absolute inset-0 flex flex-col items-center justify-center">

                            {{-- Icon with Gradient Background --}}
                            <div class="relative">
                                <div :class="'absolute inset-0 bg-gradient-to-br ' + slide.bgColor + ' rounded-full blur-2xl'"></div>
                                <div :class="'relative w-32 h-32 bg-gradient-to-br ' + slide.color + ' rounded-3xl flex items-center justify-center transform hover:scale-110 transition-transform shadow-2xl'">
                                    <i :class="'fa-solid ' + slide.icon + ' text-white text-5xl'"></i>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="space-y-4">
                                <div>
                                    <div class="text-sm font-bold uppercase tracking-widest text-cyan-400 mb-2" x-text="slide.subtitle"></div>
                                    <h1 class="text-5xl md:text-7xl font-black text-white leading-tight" x-text="slide.title"></h1>
                                </div>
                                <p class="text-xl md:text-2xl text-gray-300 max-w-3xl mx-auto leading-relaxed" x-text="slide.description"></p>
                            </div>

                            {{-- CTA --}}
                            <div class="flex flex-wrap gap-4 justify-center pt-4">
                                <a href="/shop" :class="'px-8 py-4 bg-gradient-to-r ' + slide.color + ' text-white rounded-xl font-bold text-lg shadow-2xl hover:shadow-cyan-500/50 transition-all hover:scale-105'">
                                    Keşfet
                                </a>
                                <a href="/iletisim" class="px-8 py-4 bg-white/10 backdrop-blur-md border-2 border-white/30 text-white rounded-xl font-bold text-lg hover:bg-white/20 transition-all">
                                    İletişim
                                </a>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Navigation Dots --}}
                <div class="flex justify-center items-center gap-3 pt-12">
                    <button @click="prevSlide(); autoplay = false"
                            class="w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>

                    <div class="flex gap-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="goToSlide(index)"
                                    :class="currentSlide === index ? 'w-12 bg-white' : 'w-3 bg-white/30 hover:bg-white/50'"
                                    class="h-3 rounded-full transition-all duration-300">
                            </button>
                        </template>
                    </div>

                    <button @click="nextSlide(); autoplay = false"
                            class="w-10 h-10 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 rounded-full flex items-center justify-center text-white transition-all hover:scale-110">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>

                {{-- Trust Indicators --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-16 border-t border-white/10 mt-12">
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">500+</div>
                        <div class="text-sm text-gray-400">Ürün Çeşidi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">24sa</div>
                        <div class="text-sm text-gray-400">Hızlı Teslimat</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">7/24</div>
                        <div class="text-sm text-gray-400">Canlı Destek</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white mb-1">%100</div>
                        <div class="text-sm text-gray-400">Güvenli Ödeme</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 4: STATIC HERO + PRODUCT CAROUSEL
         Focus: Sabit hero content + altta dönen ürün kartları
         Hybrid yaklaşım
    ============================================================ --}}
    @if($version === 4)
    <section class="relative py-20 md:py-24 bg-gradient-to-b from-white via-blue-50/30 to-white dark:from-gray-900 dark:via-blue-950/20 dark:to-gray-900">
        <div class="container mx-auto px-4">

            {{-- Static Hero Content --}}
            <div class="max-w-5xl mx-auto text-center space-y-8 mb-16">
                <div class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full text-sm font-bold">
                    <i class="fa-solid fa-rocket"></i>
                    <span>Türkiye'nin Dijital İstif Platformu</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-black text-gray-900 dark:text-white leading-tight">
                    Profesyonel<br/>
                    <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 dark:from-blue-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent">İstif Ekipmanı</span><br/>
                    Tek Platformda
                </h1>

                <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Geniş ürün yelpazesi, anlık stok takibi, hızlı teslimat.<br/>
                    <strong class="text-gray-900 dark:text-white">İhtiyacınız olan her şey burada.</strong>
                </p>

                <div class="flex flex-wrap gap-4 justify-center pt-6">
                    <a href="/shop" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl font-bold text-lg shadow-2xl hover:shadow-blue-500/50 transition-all hover:scale-105">
                        Ürünleri İncele
                    </a>
                    <a href="/iletisim" class="px-10 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-600 dark:hover:border-blue-400 text-gray-900 dark:text-white rounded-2xl font-bold text-lg hover:shadow-xl transition-all">
                        Teklif Al
                    </a>
                </div>
            </div>

            {{-- Product Categories Carousel --}}
            <div class="relative" x-data="{ scrollPosition: 0 }">
                <h3 class="text-center text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-8">Popüler Kategoriler</h3>

                <div class="relative overflow-hidden">
                    {{-- Carousel Container --}}
                    <div class="flex gap-6 overflow-x-auto scrollbar-hide pb-4 px-4 snap-x snap-mandatory" style="scroll-behavior: smooth;">
                        {{-- Card 1 --}}
                        <div class="flex-shrink-0 w-72 snap-start group">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-2xl transition-all h-full">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-forklift text-white text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-3">Forklift</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Elektrikli ve dizel forklift çeşitleri</p>
                                <div class="text-sm text-blue-600 dark:text-blue-400 font-semibold">150+ Model →</div>
                            </div>
                        </div>

                        {{-- Card 2 --}}
                        <div class="flex-shrink-0 w-72 snap-start group">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-400 hover:shadow-2xl transition-all h-full">
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-dolly text-white text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-3">Transpalet</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Manuel ve elektrikli transpalet</p>
                                <div class="text-sm text-purple-600 dark:text-purple-400 font-semibold">120+ Model →</div>
                            </div>
                        </div>

                        {{-- Card 3 --}}
                        <div class="flex-shrink-0 w-72 snap-start group">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-400 hover:shadow-2xl transition-all h-full">
                                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-warehouse text-white text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-3">İstif Makinası</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Yüksek tonajlı istif ekipmanları</p>
                                <div class="text-sm text-green-600 dark:text-green-400 font-semibold">80+ Model →</div>
                            </div>
                        </div>

                        {{-- Card 4 --}}
                        <div class="flex-shrink-0 w-72 snap-start group">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-700 hover:border-orange-500 dark:hover:border-orange-400 hover:shadow-2xl transition-all h-full">
                                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-truck-ramp-box text-white text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-3">Rampa Sistemleri</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Yükleme ve boşaltma rampası</p>
                                <div class="text-sm text-orange-600 dark:text-orange-400 font-semibold">50+ Model →</div>
                            </div>
                        </div>

                        {{-- Card 5 --}}
                        <div class="flex-shrink-0 w-72 snap-start group">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border-2 border-gray-200 dark:border-gray-700 hover:border-red-500 dark:hover:border-red-400 hover:shadow-2xl transition-all h-full">
                                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-pallet text-white text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-2xl text-gray-900 dark:text-white mb-3">Palet & Raf</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Depolama çözümleri</p>
                                <div class="text-sm text-red-600 dark:text-red-400 font-semibold">100+ Model →</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trust Stats --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mt-16 pt-12 border-t border-gray-200 dark:border-gray-800">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-3">
                            <i class="fa-solid fa-boxes-stacked text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mb-1">500+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ürün Çeşidi</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-3">
                            <i class="fa-solid fa-truck-fast text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mb-1">24sa</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Express Teslimat</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-3">
                            <i class="fa-solid fa-headset text-green-600 dark:text-green-400 text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mb-1">7/24</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Canlı Destek</div>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full mb-3">
                            <i class="fa-solid fa-shield-check text-orange-600 dark:text-orange-400 text-2xl"></i>
                        </div>
                        <div class="text-3xl font-black text-gray-900 dark:text-white mb-1">%100</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Güvenli Ödeme</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 5: CHANGING BACKGROUND + STATIC CONTENT
         Focus: Arka plan değişiyor, içerik sabit kalıyor
         Parallax effect
    ============================================================ --}}
    @if($version === 5)
    <section class="relative py-24 md:py-32 overflow-hidden"
             x-data="{
                currentBg: 0,
                backgrounds: [
                    'from-blue-600 to-cyan-500',
                    'from-purple-600 to-pink-500',
                    'from-green-600 to-emerald-500',
                    'from-orange-600 to-red-500'
                ],
                init() {
                    setInterval(() => {
                        this.currentBg = (this.currentBg + 1) % this.backgrounds.length;
                    }, 3000);
                }
             }">

        {{-- Animated Background --}}
        <template x-for="(bg, index) in backgrounds" :key="index">
            <div x-show="currentBg === index"
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="'absolute inset-0 bg-gradient-to-br ' + bg"
                 class="transition-all duration-1000"></div>
        </template>

        {{-- Static Content Overlay --}}
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center space-y-10 text-white">

                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-6 py-3 bg-white/20 backdrop-blur-md border border-white/30 rounded-full">
                    <i class="fa-solid fa-star"></i>
                    <span class="font-bold">Dijital Platform</span>
                </div>

                {{-- Main Headline --}}
                <h1 class="text-6xl md:text-8xl font-black leading-tight">
                    Endüstriyel<br/>
                    Ekipman<br/>
                    Platformu
                </h1>

                {{-- Features Grid --}}
                <div class="grid md:grid-cols-2 gap-6 pt-8 max-w-3xl mx-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-left hover:bg-white/20 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-boxes-stacked text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">Geniş Katalog</h3>
                                <p class="text-white/80">500+ ürün çeşidiyle her ihtiyaca çözüm</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-left hover:bg-white/20 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-bolt text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">Hızlı Süreç</h3>
                                <p class="text-white/80">Sipariş sonrası 24 saat teslimat</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-left hover:bg-white/20 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-headset text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">7/24 Destek</h3>
                                <p class="text-white/80">Profesyonel teknik yardım ekibi</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-left hover:bg-white/20 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-shield-check text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-xl mb-2">Güvenli Alışveriş</h3>
                                <p class="text-white/80">SSL sertifikalı ödeme sistemi</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex flex-wrap gap-4 justify-center pt-8">
                    <a href="/shop" class="px-10 py-5 bg-white text-gray-900 rounded-2xl font-bold text-lg shadow-2xl hover:shadow-white/50 transition-all hover:scale-105">
                        Ürünleri Keşfet
                    </a>
                    <a href="/iletisim" class="px-10 py-5 bg-white/20 backdrop-blur-md border-2 border-white/40 text-white rounded-2xl font-bold text-lg hover:bg-white/30 transition-all">
                        Bize Ulaşın
                    </a>
                </div>

                {{-- Color Indicator --}}
                <div class="flex gap-3 justify-center pt-8">
                    <template x-for="(bg, index) in backgrounds" :key="index">
                        <div :class="currentBg === index ? 'w-12 h-3' : 'w-3 h-3'"
                             class="bg-white/50 rounded-full transition-all duration-300"></div>
                    </template>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 6: TESTIMONIAL STYLE WITH ROTATING CARDS
         Focus: Müşteri yorumu tarzı dönen kartlar
         Social proof odaklı
    ============================================================ --}}
    @if($version === 6)
    <section class="relative py-20 md:py-24 bg-gray-50 dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">

                {{-- Header --}}
                <div class="text-center mb-16 space-y-4">
                    <div class="inline-block px-6 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                        <span class="text-sm font-bold text-blue-600 dark:text-blue-400">Neden İxtif?</span>
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white leading-tight">
                        Platform Avantajları
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Profesyonel ekipman tedarikinde teknolojik çözümler
                    </p>
                </div>

                {{-- Rotating Feature Cards --}}
                <div class="grid md:grid-cols-3 gap-8"
                     x-data="{
                        active: 0,
                        features: [
                            { id: 0, title: 'Akıllı Arama', icon: 'fa-search' },
                            { id: 1, title: 'Anlık Stok', icon: 'fa-warehouse' },
                            { id: 2, title: 'Hızlı Kargo', icon: 'fa-truck-fast' }
                        ],
                        init() {
                            setInterval(() => {
                                this.active = (this.active + 1) % this.features.length;
                            }, 3000);
                        }
                     }">

                    {{-- Card 1 --}}
                    <div @click="active = 0"
                         :class="active === 0 ? 'ring-4 ring-blue-500 dark:ring-blue-400 scale-105' : 'hover:scale-102'"
                         class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transition-all cursor-pointer">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-search text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Akıllı Arama Sistemi</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            500+ ürün arasında gelişmiş filtreleme. Marka, kapasite, tip bazında anlık sonuç.
                        </p>
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>Anlık filtreleme</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>Karşılaştırma özelliği</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div @click="active = 1"
                         :class="active === 1 ? 'ring-4 ring-purple-500 dark:ring-purple-400 scale-105' : 'hover:scale-102'"
                         class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transition-all cursor-pointer">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-warehouse text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Anlık Stok Takibi</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Gerçek zamanlı stok görüntüleme. Sipariş öncesi müsaitlik kontrolü yapın.
                        </p>
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>Gerçek zamanlı güncelleme</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>Bekleme süresi tahmini</span>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div @click="active = 2"
                         :class="active === 2 ? 'ring-4 ring-green-500 dark:ring-green-400 scale-105' : 'hover:scale-102'"
                         class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transition-all cursor-pointer">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fa-solid fa-truck-fast text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Express Teslimat</h3>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                            Sipariş sonrası 24 saat içinde adresinizde. Türkiye geneli hızlı kargo.
                        </p>
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>24 saat içinde teslimat</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span>Kargo takip sistemi</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="text-center mt-16 space-y-6">
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="/shop" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl font-bold text-lg shadow-2xl transition-all hover:scale-105">
                            Platformu Deneyin
                        </a>
                        <a href="/iletisim" class="px-10 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-600 dark:hover:border-blue-400 text-gray-900 dark:text-white rounded-2xl font-bold text-lg hover:shadow-xl transition-all">
                            Demo Talep Et
                        </a>
                    </div>

                    {{-- Stats --}}
                    <div class="flex flex-wrap gap-8 justify-center pt-8 text-sm text-gray-600 dark:text-gray-400">
                        <div><strong class="text-2xl font-black text-gray-900 dark:text-white block">500+</strong> Ürün</div>
                        <div><strong class="text-2xl font-black text-gray-900 dark:text-white block">24sa</strong> Teslimat</div>
                        <div><strong class="text-2xl font-black text-gray-900 dark:text-white block">7/24</strong> Destek</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 7: FULL-WIDTH HERO WITH OVERLAY CONTENT
         Focus: Tam genişlik arka plan + üstte içerik
         Dramatic visual impact
    ============================================================ --}}
    @if($version === 7)
    <section class="relative py-32 md:py-40 overflow-hidden">
        {{-- Background with overlay --}}
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-blue-900 to-purple-900"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDEzNGgtOHYtOGg4djh6bTAtMTZoLTh2LThoOHY4em0xNiAxNmgtOHYtOGg4djh6bTAtMTZoLTh2LThoOHY4em0xNiAxNmgtOHYtOGg4djh6bTAtMTZoLTh2LThoOHY4em0xNiAxNmgtOHYtOGg4djh6bTAtMTZoLTh2LThoOHY4ek0xMDQgMTM0aC04di04aDh2OHptMC0xNmgtOHYtOGg4djh6bTAgMTZoLTh2LThoOHY4em0wLTE2aC04di04aDh2OHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>

        {{-- Content --}}
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-5xl mx-auto text-center space-y-10 text-white">
                <h1 class="text-6xl md:text-8xl lg:text-9xl font-black leading-none tracking-tight">
                    İSTİF<br/>
                    <span class="bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">PLATFORMU</span>
                </h1>

                <p class="text-2xl md:text-3xl text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Endüstriyel ekipman tedarikinde<br/>
                    <strong class="text-white">Türkiye'nin dijital adresi</strong>
                </p>

                {{-- Feature Pills --}}
                <div class="flex flex-wrap gap-4 justify-center pt-8">
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-cyan-400"></i>
                        <span>500+ Ürün</span>
                    </div>
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-cyan-400"></i>
                        <span>24 Saat Teslimat</span>
                    </div>
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-cyan-400"></i>
                        <span>7/24 Destek</span>
                    </div>
                    <div class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-2">
                        <i class="fa-solid fa-check-circle text-cyan-400"></i>
                        <span>Güvenli Ödeme</span>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="flex flex-wrap gap-4 justify-center pt-8">
                    <a href="/shop" class="group px-12 py-5 bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white rounded-2xl font-bold text-xl shadow-2xl hover:shadow-cyan-500/50 transition-all hover:scale-105 flex items-center gap-3">
                        <span>Platform'a Gir</span>
                        <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                {{-- Category Icons --}}
                <div class="grid grid-cols-5 gap-6 pt-16 max-w-2xl mx-auto">
                    <div class="group text-center">
                        <div class="w-16 h-16 mx-auto bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-forklift text-2xl"></i>
                        </div>
                        <div class="text-xs text-gray-400">Forklift</div>
                    </div>
                    <div class="group text-center">
                        <div class="w-16 h-16 mx-auto bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-dolly text-2xl"></i>
                        </div>
                        <div class="text-xs text-gray-400">Transpalet</div>
                    </div>
                    <div class="group text-center">
                        <div class="w-16 h-16 mx-auto bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-warehouse text-2xl"></i>
                        </div>
                        <div class="text-xs text-gray-400">İstif</div>
                    </div>
                    <div class="group text-center">
                        <div class="w-16 h-16 mx-auto bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-pallet text-2xl"></i>
                        </div>
                        <div class="text-xs text-gray-400">Palet</div>
                    </div>
                    <div class="group text-center">
                        <div class="w-16 h-16 mx-auto bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl flex items-center justify-center mb-3 transition-all group-hover:scale-110">
                            <i class="fa-solid fa-truck-ramp-box text-2xl"></i>
                        </div>
                        <div class="text-xs text-gray-400">Rampa</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 8: COMPACT WITH ANIMATED COUNTERS
         Focus: Kompakt hero + sayısal veriler animasyonlu
         Data-driven approach
    ============================================================ --}}
    @if($version === 8)
    <section class="relative py-20 md:py-24 bg-white dark:bg-gray-900">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">

                {{-- Compact Hero --}}
                <div class="text-center space-y-6 mb-16">
                    <h1 class="text-5xl md:text-6xl font-black text-gray-900 dark:text-white leading-tight">
                        Profesyonel Ekipman<br/>
                        <span class="bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">Tek Platform</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        İstif ekipmanı tedarikinde dijital çözümler
                    </p>
                </div>

                {{-- Animated Stats Grid --}}
                <div class="grid md:grid-cols-4 gap-8 mb-16"
                     x-data="{
                        stats: [
                            { value: 500, suffix: '+', label: 'Ürün Çeşidi', icon: 'fa-boxes-stacked', color: 'blue' },
                            { value: 24, suffix: 'sa', label: 'Teslimat Süresi', icon: 'fa-truck-fast', color: 'purple' },
                            { value: 7, suffix: '/24', label: 'Canlı Destek', icon: 'fa-headset', color: 'green' },
                            { value: 100, suffix: '%', label: 'Güvenli Ödeme', icon: 'fa-shield-check', color: 'orange' }
                        ]
                     }">
                    <template x-for="(stat, index) in stats" :key="index">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-3xl p-8 text-center hover:shadow-2xl transition-all group cursor-pointer">
                            <div :class="'w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-' + stat.color + '-500 to-' + stat.color + '-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform'">
                                <i :class="'fa-solid ' + stat.icon + ' text-white text-2xl'"></i>
                            </div>
                            <div class="text-5xl font-black text-gray-900 dark:text-white mb-2">
                                <span x-text="stat.value"></span><span x-text="stat.suffix" class="text-3xl"></span>
                            </div>
                            <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider" x-text="stat.label"></div>
                        </div>
                    </template>
                </div>

                {{-- Services Row --}}
                <div class="grid md:grid-cols-3 gap-6 mb-12">
                    <div class="flex items-center gap-4 p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl border-2 border-blue-200 dark:border-blue-800">
                        <i class="fa-solid fa-magnifying-glass text-3xl text-blue-600 dark:text-blue-400"></i>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Akıllı Arama</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gelişmiş filtreleme sistemi</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl border-2 border-purple-200 dark:border-purple-800">
                        <i class="fa-solid fa-gauge-high text-3xl text-purple-600 dark:text-purple-400"></i>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Anlık Stok</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gerçek zamanlı güncelleme</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl border-2 border-green-200 dark:border-green-800">
                        <i class="fa-solid fa-rocket text-3xl text-green-600 dark:text-green-400"></i>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Hızlı Kargo</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Express teslimat seçeneği</p>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="text-center">
                    <a href="/shop" class="inline-flex items-center gap-3 px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-2xl font-bold text-lg shadow-2xl transition-all hover:scale-105">
                        <span>Hemen Başla</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 9: VIDEO-STYLE ANIMATED HERO
         Focus: Video tarzı animasyonlu arka plan + minimal text
         Modern & dynamic
    ============================================================ --}}
    @if($version === 9)
    <section class="relative py-32 md:py-40 overflow-hidden bg-black">
        {{-- Animated Gradient Background (simulates video) --}}
        <div class="absolute inset-0"
             x-data="{ frame: 0 }"
             x-init="setInterval(() => { frame = (frame + 1) % 360 }, 50)">
            <div class="absolute inset-0 opacity-60"
                 :style="'background: linear-gradient(' + frame + 'deg, #667eea 0%, #764ba2 50%, #f093fb 100%); transition: all 0.05s linear;'"></div>
        </div>

        {{-- Overlay Pattern --}}
        <div class="absolute inset-0 bg-black/40"></div>

        {{-- Content --}}
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center text-white space-y-12">

                {{-- Main Message --}}
                <div class="space-y-6">
                    <h1 class="text-7xl md:text-9xl font-black leading-none tracking-tighter">
                        İXTİF
                    </h1>
                    <div class="text-2xl md:text-3xl font-light tracking-wide">
                        ENDÜSTRİYEL EKİPMAN PLATFORMU
                    </div>
                </div>

                {{-- Separator --}}
                <div class="flex items-center gap-4 justify-center">
                    <div class="h-[2px] w-16 bg-white"></div>
                    <i class="fa-solid fa-star text-xl"></i>
                    <div class="h-[2px] w-16 bg-white"></div>
                </div>

                {{-- Features Row --}}
                <div class="flex flex-wrap gap-8 justify-center text-lg font-semibold">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle text-xs text-cyan-400"></i>
                        <span>500+ Ürün</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle text-xs text-cyan-400"></i>
                        <span>24 Saat Teslimat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle text-xs text-cyan-400"></i>
                        <span>7/24 Destek</span>
                    </div>
                </div>

                {{-- CTA --}}
                <div class="pt-8">
                    <a href="/shop" class="inline-block px-12 py-5 bg-white text-black rounded-full font-bold text-xl hover:bg-gray-100 transition-all hover:scale-105 shadow-2xl">
                        Keşfet
                    </a>
                </div>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white/60 animate-bounce">
            <i class="fa-solid fa-chevron-down text-2xl"></i>
        </div>
    </section>
    @endif

    {{-- ============================================================
         VERSION 10: MINIMAL CENTERED HERO
         Focus: Ultra minimal, centered, elegant
         Apple-style simplicity
    ============================================================ --}}
    @if($version === 10)
    <section class="relative py-32 md:py-48 bg-white dark:bg-black">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center space-y-16">

                {{-- Main Headline --}}
                <div class="space-y-8">
                    <h1 class="text-6xl md:text-8xl font-light text-gray-900 dark:text-white tracking-tight">
                        İstif Ekipmanı.<br/>
                        <strong class="font-black">Yeniden Tasarlandı.</strong>
                    </h1>

                    <p class="text-2xl md:text-3xl text-gray-600 dark:text-gray-400 font-light max-w-2xl mx-auto leading-relaxed">
                        Dijital platform ile profesyonel ekipman tedariki artık çok daha basit.
                    </p>
                </div>

                {{-- Simple CTA --}}
                <div class="flex flex-wrap gap-6 justify-center">
                    <a href="/shop" class="px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-semibold text-lg transition-all">
                        Platform'u Keşfet
                    </a>
                    <a href="/iletisim" class="px-10 py-4 border-2 border-gray-300 dark:border-gray-700 hover:border-blue-600 dark:hover:border-blue-400 text-gray-900 dark:text-white rounded-full font-semibold text-lg transition-all">
                        Daha Fazla Bilgi
                    </a>
                </div>

                {{-- Minimal Stats --}}
                <div class="pt-16 border-t border-gray-200 dark:border-gray-800">
                    <div class="grid grid-cols-3 gap-12">
                        <div>
                            <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">500+</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Ürün Çeşidi</div>
                        </div>
                        <div>
                            <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">24sa</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Teslimat</div>
                        </div>
                        <div>
                            <div class="text-5xl font-bold text-gray-900 dark:text-white mb-2">7/24</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Destek</div>
                        </div>
                    </div>
                </div>

                {{-- Category Links (Minimal) --}}
                <div class="pt-16">
                    <div class="text-xs text-gray-500 dark:text-gray-500 uppercase tracking-wider mb-6">Kategoriler</div>
                    <div class="flex flex-wrap gap-4 justify-center text-sm">
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Forklift</a>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Transpalet</a>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">İstif Makinası</a>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Palet & Raf</a>
                        <span class="text-gray-300 dark:text-gray-700">•</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Rampa Sistemleri</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Navigation --}}
    <div class="fixed bottom-8 right-8 z-50 flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-4 border border-gray-200 dark:border-gray-700">
        @if($prevVersion)
            <a href="?v={{ $prevVersion }}" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all hover:scale-110">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        @else
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500 opacity-50 cursor-not-allowed">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        @endif

        <div class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg text-white font-bold">
            <span class="text-2xl">{{ $version }}</span><span class="text-sm opacity-80">/10</span>
        </div>

        @if($nextVersion)
            <a href="?v={{ $nextVersion }}" class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400 transition-all hover:scale-110">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400 dark:text-gray-500 opacity-50 cursor-not-allowed">
                <i class="fa-solid fa-chevron-right"></i>
            </div>
        @endif
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
