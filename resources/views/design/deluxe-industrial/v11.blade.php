@extends('themes.ixtif.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.premium-hero {
    height: 90vh;
    min-height: 700px;
}

.swiper-slide {
    background-size: cover;
    background-position: center;
}

.slide-content {
    background: linear-gradient(135deg, rgba(0,0,0,0.85), rgba(0,0,0,0.6));
}

.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: white;
    opacity: 0.4;
}

.swiper-pagination-bullet-active {
    width: 40px;
    border-radius: 6px;
    opacity: 1;
    background: white;
}
</style>

<!-- PROFESSIONAL PREMIUM SLIDER -->
<section class="relative premium-hero bg-black">
    <div class="swiper professionalSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-blue-800 to-cyan-800"></div>
                <div class="absolute inset-0 slide-content flex items-center">
                    <div class="container mx-auto px-8 lg:px-16">
                        <div class="max-w-3xl">
                            <div class="inline-block px-5 py-2 bg-blue-500 text-white rounded-lg text-sm font-bold mb-8">
                                TOYOTA • Akülü Forklift Serisi
                            </div>

                            <h1 class="text-6xl lg:text-8xl font-black text-white leading-tight mb-8">
                                8FBE25<br/>
                                2.5 Ton Forklift
                            </h1>

                            <p class="text-xl text-white/90 mb-12 max-w-2xl leading-relaxed">
                                48V akülü elektrikli motor, 3.5m kaldırma yüksekliği, kapalı alan kullanımı için ideal.
                            </p>

                            <div class="flex items-baseline gap-6 mb-12">
                                <div class="text-6xl font-black text-white">₺1.575.000</div>
                                <div class="text-xl text-white/70">+ KDV</div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="px-10 py-5 bg-white text-blue-600 rounded-xl font-bold text-lg hover:bg-blue-50 transition-all shadow-2xl">
                                    <i class="fas fa-shopping-cart mr-2"></i>Satın Al
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                                    <i class="fas fa-info-circle mr-2"></i>Detaylı Bilgi
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Icon -->
                    <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:block opacity-20">
                        <i class="fas fa-forklift text-white" style="font-size: 25rem;"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-r from-orange-900 via-red-900 to-pink-900"></div>
                <div class="absolute inset-0 slide-content flex items-center">
                    <div class="container mx-auto px-8 lg:px-16">
                        <div class="max-w-3xl">
                            <div class="inline-block px-5 py-2 bg-orange-500 text-white rounded-lg text-sm font-bold mb-8">
                                MITSUBISHI • Dizel Forklift Serisi
                            </div>

                            <h1 class="text-6xl lg:text-8xl font-black text-white leading-tight mb-8">
                                FD35<br/>
                                3.5 Ton Forklift
                            </h1>

                            <p class="text-xl text-white/90 mb-12 max-w-2xl leading-relaxed">
                                Güçlü dizel motor, 4.5m kaldırma yüksekliği, outdoor kullanım için maksimum performans.
                            </p>

                            <div class="flex items-baseline gap-6 mb-12">
                                <div class="text-6xl font-black text-white">₺2.450.000</div>
                                <div class="text-xl text-white/70">+ KDV</div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="px-10 py-5 bg-white text-orange-600 rounded-xl font-bold text-lg hover:bg-orange-50 transition-all shadow-2xl">
                                    <i class="fas fa-bolt mr-2"></i>Hemen Al
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                                    <i class="fas fa-phone mr-2"></i>İletişime Geç
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:block opacity-20">
                        <i class="fas fa-industry text-white" style="font-size: 25rem;"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-900 via-violet-900 to-fuchsia-900"></div>
                <div class="absolute inset-0 slide-content flex items-center">
                    <div class="container mx-auto px-8 lg:px-16">
                        <div class="max-w-3xl">
                            <div class="inline-block px-5 py-2 bg-purple-500 text-white rounded-lg text-sm font-bold mb-8">
                                CROWN • Akülü Transpalet Serisi
                            </div>

                            <h1 class="text-6xl lg:text-8xl font-black text-white leading-tight mb-8">
                                WP2000<br/>
                                2 Ton Transpalet
                            </h1>

                            <p class="text-xl text-white/90 mb-12 max-w-2xl leading-relaxed">
                                Kompakt tasarım, sessiz çalışma, kolay manevra. Depo içi taşımada maksimum verimlilik.
                            </p>

                            <div class="flex items-baseline gap-6 mb-12">
                                <div class="text-6xl font-black text-white">₺285.000</div>
                                <div class="text-xl text-white/70">+ KDV</div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="px-10 py-5 bg-white text-purple-600 rounded-xl font-bold text-lg hover:bg-purple-50 transition-all shadow-2xl">
                                    <i class="fas fa-cart-plus mr-2"></i>Sepete Ekle
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                                    <i class="fas fa-video mr-2"></i>Demo İzle
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:block opacity-20">
                        <i class="fas fa-pallet text-white" style="font-size: 25rem;"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-r from-green-900 via-emerald-900 to-teal-900"></div>
                <div class="absolute inset-0 slide-content flex items-center">
                    <div class="container mx-auto px-8 lg:px-16">
                        <div class="max-w-3xl">
                            <div class="inline-block px-5 py-2 bg-green-500 text-white rounded-lg text-sm font-bold mb-8">
                                BT • Elektrikli İstif Makinesi
                            </div>

                            <h1 class="text-6xl lg:text-8xl font-black text-white leading-tight mb-8">
                                SWE120<br/>
                                1.2 Ton İstif
                            </h1>

                            <p class="text-xl text-white/90 mb-12 max-w-2xl leading-relaxed">
                                Dar koridor kullanımı, 3m kaldırma yüksekliği, elektrikli güvenli operasyon sistemi.
                            </p>

                            <div class="flex items-baseline gap-6 mb-12">
                                <div class="text-6xl font-black text-white">₺195.000</div>
                                <div class="text-xl text-white/70">+ KDV</div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="px-10 py-5 bg-white text-green-600 rounded-xl font-bold text-lg hover:bg-green-50 transition-all shadow-2xl">
                                    <i class="fas fa-check mr-2"></i>Sipariş Ver
                                </button>
                                <button class="px-10 py-5 border-2 border-white text-white rounded-xl font-bold text-lg hover:bg-white/10 transition-all">
                                    <i class="fas fa-file-invoice mr-2"></i>Teklif Al
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="absolute right-10 top-1/2 -translate-y-1/2 hidden lg:block opacity-20">
                        <i class="fas fa-boxes-stacked text-white" style="font-size: 25rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-12"></div>

        <!-- Navigation -->
        <div class="swiper-button-prev !text-white !w-16 !h-16 !bg-white/20 !backdrop-blur-sm !rounded-full hover:!bg-white/30 !transition-all"></div>
        <div class="swiper-button-next !text-white !w-16 !h-16 !bg-white/20 !backdrop-blur-sm !rounded-full hover:!bg-white/30 !transition-all"></div>
    </div>

    <!-- Info Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-black/40 backdrop-blur-md z-20 border-t border-white/10">
        <div class="container mx-auto px-8 py-6">
            <div class="flex items-center justify-between flex-wrap gap-6 text-white">
                <div class="flex items-center gap-3">
                    <i class="fas fa-shield-check text-2xl text-green-400"></i>
                    <div>
                        <div class="text-sm opacity-70">Güvence</div>
                        <div class="font-bold">2 Yıl Garanti</div>
                    </div>
                </div>
                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-truck-fast text-2xl text-blue-400"></i>
                    <div>
                        <div class="text-sm opacity-70">Teslimat</div>
                        <div class="font-bold">24-72 Saat</div>
                    </div>
                </div>
                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-headset text-2xl text-purple-400"></i>
                    <div>
                        <div class="text-sm opacity-70">Destek</div>
                        <div class="font-bold">7/24 Hizmet</div>
                    </div>
                </div>
                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-certificate text-2xl text-yellow-400"></i>
                    <div>
                        <div class="text-sm opacity-70">Sertifika</div>
                        <div class="font-bold">CE Belgeli</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.professionalSwiper', {
        effect: 'fade',
        speed: 1000,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        loop: true,
    });
});
</script>
@endsection
