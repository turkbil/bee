@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    @for($i = 1; $i <= 10; $i++)
        <a href="{{ route('design.deluxe-industrial.v'.$i) }}" class="px-4 py-2 {{ $i == 10 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600' }} rounded-lg font-bold transition-all">V{{ $i }}</a>
    @endfor
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.card-swiper {
    height: 600px;
}

.card-swiper .swiper-slide {
    width: 350px;
    margin-right: 20px;
}

@media (max-width: 768px) {
    .card-swiper {
        height: 500px;
    }
    .card-swiper .swiper-slide {
        width: 280px;
    }
}
</style>

<!-- VERTICAL CARD STACK SLIDER -->
<section class="relative min-h-screen py-20 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
    <div class="container mx-auto px-6">
        <!-- Header -->
        <div class="mb-16">
            <div class="inline-block px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full text-sm font-bold mb-6">
                <i class="fas fa-fire mr-2"></i>EN YENİ ÜRÜNLER
            </div>
            <h1 class="text-5xl lg:text-7xl font-black text-gray-900 dark:text-white mb-6">
                Premium<br/>
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    Forklift Koleksiyonu
                </span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl">
                2025 model akülü ve dizel forkliftler. Hemen teklif alın, aynı gün yanıt garantisi.
            </p>
        </div>

        <!-- Product Cards Slider -->
        <div class="swiper card-swiper mb-16">
            <div class="swiper-wrapper">
                <!-- Product Card 1 -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all h-full flex flex-col">
                        <!-- Image -->
                        <div class="relative aspect-square bg-gradient-to-br from-blue-100 to-blue-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                            <i class="fas fa-forklift text-9xl text-blue-600 dark:text-blue-400"></i>
                            <div class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white rounded-full text-xs font-bold">
                                -15%
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs text-blue-600 dark:text-blue-400 font-bold mb-2">TOYOTA • 8FBE25</div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">
                                Akülü Forklift 2.5T
                            </h3>

                            <!-- Features -->
                            <ul class="space-y-1.5 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    48V Elektrikli motor
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    3.5m yükseklik
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    2 yıl garanti
                                </li>
                            </ul>

                            <!-- Price -->
                            <div class="mt-auto">
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-3xl font-black text-blue-600 dark:text-blue-400">₺1.575.000</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-500">+ KDV</span>
                                </div>

                                <button class="w-full px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                    <i class="fas fa-shopping-cart mr-2"></i>Sepete Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all h-full flex flex-col">
                        <div class="relative aspect-square bg-gradient-to-br from-orange-100 to-orange-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                            <i class="fas fa-industry text-9xl text-orange-600 dark:text-orange-400"></i>
                            <div class="absolute top-4 right-4 px-4 py-2 bg-green-500 text-white rounded-full text-xs font-bold">
                                STOKTA
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs text-orange-600 dark:text-orange-400 font-bold mb-2">MITSUBISHI • FD35</div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">
                                Dizel Forklift 3.5T
                            </h3>

                            <ul class="space-y-1.5 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Outdoor kullanım
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    4.5m yükseklik
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    CE belgeli
                                </li>
                            </ul>

                            <div class="mt-auto">
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-3xl font-black text-orange-600 dark:text-orange-400">₺2.450.000</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-500">+ KDV</span>
                                </div>

                                <button class="w-full px-6 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                    <i class="fas fa-bolt mr-2"></i>Hemen Al
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all h-full flex flex-col">
                        <div class="relative aspect-square bg-gradient-to-br from-purple-100 to-purple-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                            <i class="fas fa-pallet text-9xl text-purple-600 dark:text-purple-400"></i>
                            <div class="absolute top-4 right-4 px-4 py-2 bg-blue-500 text-white rounded-full text-xs font-bold">
                                YENİ
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs text-purple-600 dark:text-purple-400 font-bold mb-2">CROWN • WP2000</div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">
                                Akülü Transpalet 2T
                            </h3>

                            <ul class="space-y-1.5 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Kompakt tasarım
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Sessiz çalışma
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Kolay manevra
                                </li>
                            </ul>

                            <div class="mt-auto">
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-3xl font-black text-purple-600 dark:text-purple-400">₺285.000</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-500">+ KDV</span>
                                </div>

                                <button class="w-full px-6 py-4 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                    <i class="fas fa-cart-plus mr-2"></i>Sepete At
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all h-full flex flex-col">
                        <div class="relative aspect-square bg-gradient-to-br from-green-100 to-green-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                            <i class="fas fa-boxes-stacked text-9xl text-green-600 dark:text-green-400"></i>
                            <div class="absolute top-4 right-4 px-4 py-2 bg-purple-500 text-white rounded-full text-xs font-bold">
                                PREMİUM
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs text-green-600 dark:text-green-400 font-bold mb-2">BT • SWE120</div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">
                                İstif Makinesi 1.2T
                            </h3>

                            <ul class="space-y-1.5 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    3m yükseklik
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Elektrikli sistem
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Güvenli operasyon
                                </li>
                            </ul>

                            <div class="mt-auto">
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-3xl font-black text-green-600 dark:text-green-400">₺195.000</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-500">+ KDV</span>
                                </div>

                                <button class="w-full px-6 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                    <i class="fas fa-check mr-2"></i>Sipariş Ver
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Card 5 -->
                <div class="swiper-slide">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all h-full flex flex-col">
                        <div class="relative aspect-square bg-gradient-to-br from-red-100 to-red-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                            <i class="fas fa-forklift text-9xl text-red-600 dark:text-red-400"></i>
                            <div class="absolute top-4 right-4 px-4 py-2 bg-yellow-500 text-black rounded-full text-xs font-bold">
                                KAMPANYA
                            </div>
                        </div>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-xs text-red-600 dark:text-red-400 font-bold mb-2">HYUNDAI • 25L-7</div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">
                                LPG Forklift 2.5T
                            </h3>

                            <ul class="space-y-1.5 mb-6 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    LPG yakıt sistemi
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    İç/dış alan
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="fas fa-check text-green-600 text-xs"></i>
                                    Ekonomik işletme
                                </li>
                            </ul>

                            <div class="mt-auto">
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-3xl font-black text-red-600 dark:text-red-400">₺1.750.000</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-500">+ KDV</span>
                                </div>

                                <button class="w-full px-6 py-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                    <i class="fas fa-fire mr-2"></i>Kaçırma!
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="swiper-scrollbar !bg-gray-200 dark:!bg-gray-800 !h-2 !rounded-full mt-8"></div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center">
            <button class="px-12 py-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-2xl font-black text-xl hover:scale-105 transition-all shadow-2xl">
                <i class="fas fa-th-large mr-3"></i>Tüm Kategorileri Gör
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.card-swiper', {
        slidesPerView: 'auto',
        spaceBetween: 30,
        freeMode: true,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true,
        },
        mousewheel: {
            forceToAxis: true,
        },
    });
});
</script>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-50 hidden lg:block">
    <div class="bg-black/80 backdrop-blur-md border-2 border-pink-500 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-pink-400">#DELUXE-V10</span>
        <span class="mx-3 text-pink-500/50">|</span>
        <span class="text-xs text-pink-400/80 font-bold">VERTICAL CARD STACK</span>
    </div>
</div>
@endsection
