@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    @for($i = 1; $i <= 10; $i++)
        <a href="{{ route('design.deluxe-industrial.v'.$i) }}" class="px-4 py-2 {{ $i == 8 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600' }} rounded-lg font-bold transition-all">V{{ $i }}</a>
    @endfor
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.diagonal-slider {
    clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
}

.swiper-pagination-bullet {
    width: 14px;
    height: 14px;
    background: white;
    opacity: 0.5;
}

.swiper-pagination-bullet-active {
    opacity: 1;
    width: 40px;
    border-radius: 10px;
}
</style>

<!-- DIAGONAL SPLIT SLIDER HERO -->
<section class="relative h-screen overflow-hidden bg-gray-900 dark:bg-black">
    <div class="swiper diagonalSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="relative h-full grid lg:grid-cols-2">
                    <!-- Left: Content -->
                    <div class="flex items-center justify-center bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600 dark:from-blue-900 dark:via-blue-950 dark:to-cyan-950 diagonal-slider relative z-10 px-12">
                        <div class="max-w-xl">
                            <div class="inline-block px-5 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold mb-8">
                                ✨ 2025 YENİ MODEL
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white leading-none mb-6">
                                Toyota<br/>
                                <span class="text-cyan-300">8FBE25</span><br/>
                                Akülü Forklift
                            </h1>
                            <p class="text-xl text-white/90 mb-8">
                                2.5 ton kapasite • 48V Elektrikli • 3.5m yükseklik
                            </p>
                            <div class="flex items-baseline gap-4 mb-10">
                                <div class="text-5xl font-black text-white">₺1.575.000</div>
                                <div class="text-lg text-white/70">+ KDV</div>
                            </div>
                            <div class="flex gap-4">
                                <button class="px-10 py-5 bg-white text-blue-600 rounded-2xl font-black text-lg hover:bg-blue-50 transition-all shadow-2xl">
                                    Hemen Al
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                                    Teklif İste
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Product Visual -->
                    <div class="flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 dark:from-gray-950 dark:to-black px-12">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-3xl"></div>
                            <i class="fas fa-forklift text-[25rem] text-white relative z-10"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="relative h-full grid lg:grid-cols-2">
                    <div class="flex items-center justify-center bg-gradient-to-br from-orange-600 via-red-600 to-pink-600 dark:from-orange-900 dark:via-red-950 dark:to-pink-950 diagonal-slider relative z-10 px-12">
                        <div class="max-w-xl">
                            <div class="inline-block px-5 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold mb-8">
                                🔥 ÇOK SATAN
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white leading-none mb-6">
                                Mitsubishi<br/>
                                <span class="text-orange-300">FD35</span><br/>
                                Dizel Forklift
                            </h1>
                            <p class="text-xl text-white/90 mb-8">
                                3.5 ton kapasite • Outdoor • 4.5m yükseklik
                            </p>
                            <div class="flex items-baseline gap-4 mb-10">
                                <div class="text-5xl font-black text-white">₺2.450.000</div>
                                <div class="text-lg text-white/70">+ KDV</div>
                            </div>
                            <div class="flex gap-4">
                                <button class="px-10 py-5 bg-white text-orange-600 rounded-2xl font-black text-lg hover:bg-orange-50 transition-all shadow-2xl">
                                    Hemen Al
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                                    Detaylı Bilgi
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 dark:from-gray-950 dark:to-black px-12">
                        <div class="relative">
                            <div class="absolute inset-0 bg-orange-500/20 rounded-full blur-3xl"></div>
                            <i class="fas fa-industry text-[25rem] text-white relative z-10"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="relative h-full grid lg:grid-cols-2">
                    <div class="flex items-center justify-center bg-gradient-to-br from-purple-600 via-violet-600 to-fuchsia-600 dark:from-purple-900 dark:via-violet-950 dark:to-fuchsia-950 diagonal-slider relative z-10 px-12">
                        <div class="max-w-xl">
                            <div class="inline-block px-5 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold mb-8">
                                ⚡ AKILLI TEKNOLOJİ
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white leading-none mb-6">
                                Crown<br/>
                                <span class="text-purple-300">WP 2000</span><br/>
                                Transpalet
                            </h1>
                            <p class="text-xl text-white/90 mb-8">
                                2 ton kapasite • Akülü • Kompakt tasarım
                            </p>
                            <div class="flex items-baseline gap-4 mb-10">
                                <div class="text-5xl font-black text-white">₺285.000</div>
                                <div class="text-lg text-white/70">+ KDV</div>
                            </div>
                            <div class="flex gap-4">
                                <button class="px-10 py-5 bg-white text-purple-600 rounded-2xl font-black text-lg hover:bg-purple-50 transition-all shadow-2xl">
                                    Satın Al
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                                    Demo Talep Et
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 dark:from-gray-950 dark:to-black px-12">
                        <div class="relative">
                            <div class="absolute inset-0 bg-purple-500/20 rounded-full blur-3xl"></div>
                            <i class="fas fa-pallet text-[25rem] text-white relative z-10"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="swiper-slide">
                <div class="relative h-full grid lg:grid-cols-2">
                    <div class="flex items-center justify-center bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600 dark:from-green-900 dark:via-emerald-950 dark:to-teal-950 diagonal-slider relative z-10 px-12">
                        <div class="max-w-xl">
                            <div class="inline-block px-5 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-bold mb-8">
                                📦 YENİ ÜRÜN
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white leading-none mb-6">
                                BT<br/>
                                <span class="text-green-300">SWE 120</span><br/>
                                İstif Makinesi
                            </h1>
                            <p class="text-xl text-white/90 mb-8">
                                1.2 ton • Elektrikli • 3m yükseklik
                            </p>
                            <div class="flex items-baseline gap-4 mb-10">
                                <div class="text-5xl font-black text-white">₺195.000</div>
                                <div class="text-lg text-white/70">+ KDV</div>
                            </div>
                            <div class="flex gap-4">
                                <button class="px-10 py-5 bg-white text-green-600 rounded-2xl font-black text-lg hover:bg-green-50 transition-all shadow-2xl">
                                    Sipariş Ver
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                                    Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 dark:from-gray-950 dark:to-black px-12">
                        <div class="relative">
                            <div class="absolute inset-0 bg-green-500/20 rounded-full blur-3xl"></div>
                            <i class="fas fa-boxes-stacked text-[25rem] text-white relative z-10"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-12"></div>

        <!-- Navigation Arrows -->
        <div class="absolute bottom-12 left-12 z-20 flex gap-4">
            <button class="swiper-button-prev-custom w-14 h-14 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="swiper-button-next-custom w-14 h-14 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Info Bar -->
        <div class="absolute top-12 left-12 z-20 flex items-center gap-6">
            <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                <i class="fas fa-shield-check text-green-400 text-lg"></i>
                <span class="text-white text-sm font-semibold">2 Yıl Garanti</span>
            </div>
            <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                <i class="fas fa-truck-fast text-blue-400 text-lg"></i>
                <span class="text-white text-sm font-semibold">Ücretsiz Kargo</span>
            </div>
            <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full">
                <i class="fas fa-headset text-orange-400 text-lg"></i>
                <span class="text-white text-sm font-semibold">7/24 Destek</span>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.diagonalSwiper', {
        effect: 'fade',
        speed: 800,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next-custom',
            prevEl: '.swiper-button-prev-custom',
        },
        loop: true,
    });
});
</script>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-50 hidden lg:block">
    <div class="bg-black/80 backdrop-blur-md border-2 border-purple-500 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-purple-400">#DELUXE-V8</span>
        <span class="mx-3 text-purple-500/50">|</span>
        <span class="text-xs text-purple-400/80 font-bold">DIAGONAL SPLIT SLIDER</span>
    </div>
</div>
@endsection
