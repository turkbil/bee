@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    @for($i = 1; $i <= 10; $i++)
        <a href="{{ route('design.deluxe-industrial.v'.$i) }}" class="px-4 py-2 {{ $i == 7 ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600 dark:hover:border-blue-500' }} rounded-lg font-bold transition-all {{ $i == 7 ? 'shadow-lg' : '' }}">
            V{{ $i }}
        </a>
    @endfor
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.product-swiper .swiper-slide {
    height: auto;
}

.swiper-button-prev, .swiper-button-next {
    background: white;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.swiper-button-prev:after, .swiper-button-next:after {
    font-size: 24px;
    color: #1f2937;
    font-weight: 900;
}

.dark .swiper-button-prev, .dark .swiper-button-next {
    background: #1f2937;
}

.dark .swiper-button-prev:after, .dark .swiper-button-next:after {
    color: white;
}
</style>

<!-- PRODUCT SHOWCASE HERO -->
<section class="py-16 lg:py-20 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-6">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-block px-6 py-2 bg-red-500 text-white rounded-full text-sm font-bold mb-6">
                <i class="fas fa-fire mr-2"></i>EN ÇOK SATANLAR
            </div>
            <h1 class="text-5xl lg:text-7xl font-black text-gray-900 dark:text-white mb-6">
                Forklift <span class="text-blue-600 dark:text-blue-400">Modelleri</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto mb-8">
                2.5T - 5T kapasite aralığında akülü ve dizel forkliftler. Hemen teklif alın, 24 saat içinde size ulaşalım.
            </p>
            <div class="flex items-center justify-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shield-check text-green-600"></i>
                    <span>2 Yıl Garanti</span>
                </div>
                <div class="w-px h-4 bg-gray-300 dark:bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-truck-fast text-blue-600"></i>
                    <span>Ücretsiz Kargo</span>
                </div>
                <div class="w-px h-4 bg-gray-300 dark:bg-gray-700"></div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-certificate text-orange-600"></i>
                    <span>CE Belgeli</span>
                </div>
            </div>
        </div>

        <!-- Product Slider -->
        <div class="swiper product-swiper mb-16">
            <div class="swiper-wrapper pb-12">
                <!-- Product 1: Akülü Forklift 2.5T -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-2xl group">
                        <!-- Product Image -->
                        <div class="relative aspect-square bg-gradient-to-br from-blue-50 to-blue-100 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-forklift text-9xl text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-xs font-bold">STOKTA</span>
                                <span class="px-4 py-1.5 bg-red-500 text-white rounded-full text-xs font-bold">%15 İNDİRİM</span>
                            </div>
                            <button class="absolute top-4 right-4 w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all">
                                <i class="far fa-heart text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <div class="text-sm text-blue-600 dark:text-blue-400 font-bold mb-2">Toyota - Seri: 8FBE</div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all">
                                Akülü Forklift 2.5 Ton
                            </h3>

                            <!-- Rating -->
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">(47 değerlendirme)</span>
                            </div>

                            <!-- Features -->
                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    48V Akülü elektrikli motor
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    3.5m kaldırma yüksekliği
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    Kapalı alan kullanımı
                                </li>
                            </ul>

                            <!-- Price -->
                            <div class="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div class="text-sm text-gray-500 dark:text-gray-400 line-through">₺1.850.000</div>
                                <div class="text-3xl font-black text-blue-600 dark:text-blue-400">₺1.575.000</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">+ KDV</div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <button class="px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all hover:scale-105 shadow-lg">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sepete Ekle
                                </button>
                                <button class="px-6 py-4 bg-white dark:bg-gray-800 border-2 border-blue-600 text-blue-600 dark:text-blue-400 rounded-xl font-bold hover:bg-blue-50 dark:hover:bg-gray-700 transition-all">
                                    Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 2: Dizel Forklift 3.5T -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-orange-500 dark:hover:border-orange-500 transition-all hover:shadow-2xl group">
                        <div class="relative aspect-square bg-gradient-to-br from-orange-50 to-orange-100 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-industry text-9xl text-orange-600 dark:text-orange-400 group-hover:scale-110 transition-transform"></i>
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-xs font-bold">STOKTA</span>
                                <span class="px-4 py-1.5 bg-orange-500 text-white rounded-full text-xs font-bold">ÇOK SATAN</span>
                            </div>
                            <button class="absolute top-4 right-4 w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all">
                                <i class="far fa-heart text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="text-sm text-orange-600 dark:text-orange-400 font-bold mb-2">Mitsubishi - FD35</div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                                Dizel Forklift 3.5 Ton
                            </h3>

                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">(89 değerlendirme)</span>
                            </div>

                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    Mitsubishi dizel motor
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    4.5m kaldırma yüksekliği
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    Outdoor kullanım
                                </li>
                            </ul>

                            <div class="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div class="text-3xl font-black text-orange-600 dark:text-orange-400">₺2.450.000</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">+ KDV</div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button class="px-6 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition-all hover:scale-105 shadow-lg">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sepete Ekle
                                </button>
                                <button class="px-6 py-4 bg-white dark:bg-gray-800 border-2 border-orange-600 text-orange-600 dark:text-orange-400 rounded-xl font-bold hover:bg-orange-50 dark:hover:bg-gray-700 transition-all">
                                    Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 3: LPG Forklift 2.5T -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-green-500 dark:hover:border-green-500 transition-all hover:shadow-2xl group">
                        <div class="relative aspect-square bg-gradient-to-br from-green-50 to-green-100 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-forklift text-9xl text-green-600 dark:text-green-400 group-hover:scale-110 transition-transform"></i>
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-4 py-1.5 bg-yellow-500 text-white rounded-full text-xs font-bold">SİPARİŞ</span>
                                <span class="px-4 py-1.5 bg-blue-500 text-white rounded-full text-xs font-bold">YENİ MODEL</span>
                            </div>
                            <button class="absolute top-4 right-4 w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all">
                                <i class="far fa-heart text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="text-sm text-green-600 dark:text-green-400 font-bold mb-2">Hyundai - 25L-7</div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-green-600 dark:group-hover:text-green-400 transition-all">
                                LPG Forklift 2.5 Ton
                            </h3>

                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">(23 değerlendirme)</span>
                            </div>

                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    LPG yakıt sistemi
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    3m kaldırma yüksekliği
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    İç/dış alan kullanım
                                </li>
                            </ul>

                            <div class="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div class="text-3xl font-black text-green-600 dark:text-green-400">₺1.750.000</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">+ KDV</div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button class="px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all hover:scale-105 shadow-lg">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sepete Ekle
                                </button>
                                <button class="px-6 py-4 bg-white dark:bg-gray-800 border-2 border-green-600 text-green-600 dark:text-green-400 rounded-xl font-bold hover:bg-green-50 dark:hover:bg-gray-700 transition-all">
                                    Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 4: Heavy Duty 5T -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl border-2 border-gray-200 dark:border-gray-800 overflow-hidden hover:border-purple-500 dark:hover:border-purple-500 transition-all hover:shadow-2xl group">
                        <div class="relative aspect-square bg-gradient-to-br from-purple-50 to-purple-100 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-industry text-9xl text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform"></i>
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-xs font-bold">STOKTA</span>
                                <span class="px-4 py-1.5 bg-purple-500 text-white rounded-full text-xs font-bold">PREMİUM</span>
                            </div>
                            <button class="absolute top-4 right-4 w-12 h-12 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all">
                                <i class="far fa-heart text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>

                        <div class="p-6">
                            <div class="text-sm text-purple-600 dark:text-purple-400 font-bold mb-2">Linde - H50D</div>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-all">
                                Heavy Duty Dizel 5 Ton
                            </h3>

                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">(156 değerlendirme)</span>
                            </div>

                            <ul class="space-y-2 mb-6">
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    5 ton kapasiteli güç ünitesi
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    6m kaldırma yüksekliği
                                </li>
                                <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-check-circle text-green-600 text-base"></i>
                                    Ağır yük taşıma
                                </li>
                            </ul>

                            <div class="flex items-baseline gap-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-800">
                                <div class="text-3xl font-black text-purple-600 dark:text-purple-400">₺3.850.000</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">+ KDV</div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button class="px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all hover:scale-105 shadow-lg">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sepete Ekle
                                </button>
                                <button class="px-6 py-4 bg-white dark:bg-gray-800 border-2 border-purple-600 text-purple-600 dark:text-purple-400 rounded-xl font-bold hover:bg-purple-50 dark:hover:bg-gray-700 transition-all">
                                    Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center">
            <button class="px-12 py-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-black text-xl hover:scale-105 transition-all shadow-2xl">
                <i class="fas fa-boxes mr-3"></i>Tüm Forklift Modellerini Gör (150+)
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.product-swiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            640: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
            1280: { slidesPerView: 4 },
        },
    });
});
</script>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-50 hidden lg:block">
    <div class="bg-black/80 backdrop-blur-md border-2 border-blue-500 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-blue-400">#DELUXE-V7</span>
        <span class="mx-3 text-blue-500/50">|</span>
        <span class="text-xs text-blue-400/80 font-bold">E-COMMERCE PRODUCT SHOWCASE</span>
    </div>
</div>
@endsection
