@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V1</a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V2</a>
    <a href="{{ route('design.deluxe-industrial.v3') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">V3</a>
    <a href="{{ route('design.deluxe-industrial.v4') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V4</a>
    <a href="{{ route('design.deluxe-industrial.v5') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V5</a>
</div>

<style>
@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInFromLeft {
    from { opacity: 0; transform: translateX(-100px); }
    to { opacity: 1; transform: translateX(0); }
}

.animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
.animate-slide-from-left { animation: slideInFromLeft 0.8s ease-out forwards; }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
</style>

<!-- HERO SECTION - Alibaba B2B Style -->
<section class="relative py-12 lg:py-16 bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 dark:from-orange-600 dark:via-red-600 dark:to-pink-600 overflow-hidden">
    <!-- Pattern Overlay -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-block mb-6 animate-slide-up delay-100">
                <span class="px-6 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold border border-white/30">
                    <i class="fas fa-globe mr-2"></i>Global Tedarikçi
                </span>
            </div>

            <!-- Main Title -->
            <h1 class="text-4xl lg:text-6xl xl:text-7xl font-black text-white leading-tight mb-6 animate-slide-up delay-200">
                Türkiye'nin En Büyük<br/>
                Endüstriyel Ekipman Pazarı
            </h1>

            <!-- Description -->
            <p class="text-xl lg:text-2xl text-white/90 mb-10 max-w-3xl mx-auto animate-slide-up delay-300">
                500+ marka, 2.000+ tedarikçi, anlık fiyat teklifleri ve hızlı teslimat. B2B alıcılar için özel fiyatlandırma.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12 animate-slide-up delay-400">
                <button class="px-10 py-5 bg-white text-orange-600 rounded-xl font-bold text-lg shadow-2xl hover:shadow-3xl hover:scale-105 transition-all">
                    <i class="fas fa-search mr-2"></i>Ürün Ara
                </button>
                <button class="px-10 py-5 bg-transparent border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white hover:text-orange-600 transition-all">
                    <i class="fas fa-store mr-2"></i>Tedarikçi Ol
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20 animate-slide-up delay-400">
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">500+</div>
                    <div class="text-sm text-white/80">Marka</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">2K+</div>
                    <div class="text-sm text-white/80">Tedarikçi</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">15K+</div>
                    <div class="text-sm text-white/80">Ürün</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-black text-white mb-1">24/7</div>
                    <div class="text-sm text-white/80">Destek</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCT CAROUSEL SECTION -->
<section class="py-16 lg:py-24 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-6">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl lg:text-5xl font-black text-gray-900 dark:text-white mb-2">Öne Çıkan Ürünler</h2>
                <p class="text-gray-600 dark:text-gray-400">En çok tercih edilen endüstriyel ekipmanlar</p>
            </div>
            <div class="hidden md:flex gap-3">
                <button class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center hover:bg-orange-500 hover:text-white dark:hover:bg-orange-500 transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Product Grid (Simulating Carousel) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Product Card 1 -->
            <div class="group bg-white dark:bg-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-orange-500 dark:hover:border-orange-500 transition-all hover:shadow-2xl">
                <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center relative overflow-hidden">
                    <i class="fas fa-forklift text-8xl text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
                    <div class="absolute top-3 right-3 px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold">
                        YENİ
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                        Akülü Forklift 2.5T
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">48V Elektrikli Motor</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">Başlangıç</div>
                            <div class="text-2xl font-black text-orange-600 dark:text-orange-400">Teklif Al</div>
                        </div>
                        <button class="w-10 h-10 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 2 -->
            <div class="group bg-white dark:bg-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-orange-500 dark:hover:border-orange-500 transition-all hover:shadow-2xl">
                <div class="aspect-square bg-gradient-to-br from-orange-100 to-orange-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center relative overflow-hidden">
                    <i class="fas fa-pallet text-8xl text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform"></i>
                    <div class="absolute top-3 right-3 px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">
                        STOKTA
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                        Akülü Transpalet
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">1.5T Kapasite</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">Başlangıç</div>
                            <div class="text-2xl font-black text-orange-600 dark:text-orange-400">Teklif Al</div>
                        </div>
                        <button class="w-10 h-10 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 3 -->
            <div class="group bg-white dark:bg-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-orange-500 dark:hover:border-orange-500 transition-all hover:shadow-2xl">
                <div class="aspect-square bg-gradient-to-br from-green-100 to-green-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center relative overflow-hidden">
                    <i class="fas fa-boxes-stacked text-8xl text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform"></i>
                    <div class="absolute top-3 right-3 px-3 py-1 bg-orange-500 text-white rounded-full text-xs font-bold">
                        ÇOK SATAN
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                        İstif Makinesi
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Elektrikli 3.5m</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">Başlangıç</div>
                            <div class="text-2xl font-black text-orange-600 dark:text-orange-400">Teklif Al</div>
                        </div>
                        <button class="w-10 h-10 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product Card 4 -->
            <div class="group bg-white dark:bg-gray-900 rounded-2xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-orange-500 dark:hover:border-orange-500 transition-all hover:shadow-2xl">
                <div class="aspect-square bg-gradient-to-br from-purple-100 to-purple-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center relative overflow-hidden">
                    <i class="fas fa-warehouse text-8xl text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                        Depo Raf Sistemleri
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Modüler Yapı</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">Başlangıç</div>
                            <div class="text-2xl font-black text-orange-600 dark:text-orange-400">Teklif Al</div>
                        </div>
                        <button class="w-10 h-10 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-all">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SUPPLIER SECTION -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 dark:text-white mb-4">Tedarikçi Kategorileri</h2>
            <p class="text-gray-600 dark:text-gray-400">Doğru tedarikçiyi kolayca bulun</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-forklift text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">Forklift</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">150+</div>
            </a>

            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-pallet text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">Transpalet</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">200+</div>
            </a>

            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">İstif</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">80+</div>
            </a>

            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-warehouse text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">Depo</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">120+</div>
            </a>

            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-truck text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">Lojistik</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">60+</div>
            </a>

            <a href="#" class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center hover:shadow-lg transition-all hover:-translate-y-1">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-wrench text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="font-bold text-sm text-gray-900 dark:text-white">Servis</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">40+</div>
            </a>
        </div>
    </div>
</section>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-40 hidden lg:block">
    <div class="bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-800 rounded-full px-6 py-3 shadow-xl">
        <span class="font-bold text-gray-900 dark:text-white">#DELUXE-V3</span>
        <span class="mx-3 text-gray-300 dark:text-gray-700">|</span>
        <span class="text-xs text-gray-600 dark:text-gray-400">ALIBABA B2B MARKETPLACE</span>
    </div>
</div>
@endsection
