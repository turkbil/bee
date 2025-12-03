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
    opacity: 0 !important;
    transition: opacity 800ms ease-in-out;
}

.swiper-slide-active {
    opacity: 1 !important;
}

.slide-content {
    animation: slideIn 1s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.price-pulse {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: white;
    opacity: 0.4;
    transition: all 0.3s ease;
}

.swiper-pagination-bullet-active {
    width: 40px;
    border-radius: 6px;
    opacity: 1;
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
}

.product-icon {
    filter: drop-shadow(0 0 40px rgba(96, 165, 250, 0.3));
}

.cta-button {
    position: relative;
    overflow: hidden;
}

.cta-button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.cta-button:hover::before {
    width: 300px;
    height: 300px;
}
</style>

<!-- MODERN TAILWIND PREMIUM SLIDER -->
<section class="relative premium-hero bg-gradient-to-br from-gray-900 via-gray-800 to-black overflow-hidden">
    <div class="swiper modernSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 - Toyota Forklift -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-cyan-600">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <!-- Animated gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-blue-500/10 to-cyan-500/20 animate-pulse"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                        <!-- Content -->
                        <div class="slide-content">
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border border-white/30 mb-8 shadow-2xl hover:scale-105 transition-transform duration-300">
                                <div class="w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full absolute"></div>
                                <span class="text-white font-bold text-sm ml-2">TOYOTA • Akülü Forklift Serisi</span>
                            </div>

                            <h1 class="text-7xl lg:text-8xl font-black text-white leading-none mb-6 tracking-tight">
                                8FBE25
                            </h1>

                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-1 w-20 bg-gradient-to-r from-cyan-400 to-blue-600 rounded-full"></div>
                                <p class="text-2xl text-cyan-100 font-bold">2.5 Ton Kapasite</p>
                            </div>

                            <div class="space-y-3 mb-10">
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-bolt text-cyan-300"></i>
                                    </div>
                                    <span>48V Elektrikli Motor</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-arrow-up text-cyan-300"></i>
                                    </div>
                                    <span>3.5m Kaldırma Yüksekliği</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-home text-cyan-300"></i>
                                    </div>
                                    <span>Kapalı Alan Kullanımı</span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-white/20 to-white/10 backdrop-blur-xl rounded-2xl p-6 mb-10 border border-white/30 shadow-2xl price-pulse">
                                <div class="text-sm text-white/70 mb-2">Fiyat</div>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-6xl font-black text-white">₺1.575.000</span>
                                    <span class="text-xl text-white/70">+ KDV</span>
                                </div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="cta-button px-10 py-5 bg-white text-blue-600 rounded-2xl font-black text-lg hover:bg-blue-50 transition-all shadow-2xl hover:scale-105 hover:shadow-blue-500/50 relative z-10">
                                    <i class="fas fa-shopping-cart mr-3"></i>Satın Al
                                </button>
                                <button class="px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-lg hover:bg-white/20 transition-all shadow-xl hover:scale-105">
                                    <i class="fas fa-info-circle mr-3"></i>Detaylı Bilgi
                                </button>
                            </div>
                        </div>

                        <!-- Product Visual -->
                        <div class="relative hidden lg:block">
                            <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
                            <i class="fas fa-forklift text-white/80 product-icon relative z-10" style="font-size: 28rem; display: block; text-align: center;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 - Mitsubishi Dizel -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-600 via-red-600 to-pink-600">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-orange-500/10 to-pink-500/20 animate-pulse"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                        <div class="slide-content">
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border border-white/30 mb-8 shadow-2xl hover:scale-105 transition-transform duration-300">
                                <div class="w-3 h-3 bg-orange-400 rounded-full animate-ping"></div>
                                <div class="w-3 h-3 bg-orange-400 rounded-full absolute"></div>
                                <span class="text-white font-bold text-sm ml-2">MITSUBISHI • Dizel Forklift Serisi</span>
                            </div>

                            <h1 class="text-7xl lg:text-8xl font-black text-white leading-none mb-6 tracking-tight">
                                FD35
                            </h1>

                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-1 w-20 bg-gradient-to-r from-orange-400 to-red-600 rounded-full"></div>
                                <p class="text-2xl text-orange-100 font-bold">3.5 Ton Kapasite</p>
                            </div>

                            <div class="space-y-3 mb-10">
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-fire text-orange-300"></i>
                                    </div>
                                    <span>Güçlü Dizel Motor</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-arrow-up text-orange-300"></i>
                                    </div>
                                    <span>4.5m Kaldırma Yüksekliği</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-sun text-orange-300"></i>
                                    </div>
                                    <span>Outdoor Kullanım</span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-white/20 to-white/10 backdrop-blur-xl rounded-2xl p-6 mb-10 border border-white/30 shadow-2xl price-pulse">
                                <div class="text-sm text-white/70 mb-2">Fiyat</div>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-6xl font-black text-white">₺2.450.000</span>
                                    <span class="text-xl text-white/70">+ KDV</span>
                                </div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="cta-button px-10 py-5 bg-white text-orange-600 rounded-2xl font-black text-lg hover:bg-orange-50 transition-all shadow-2xl hover:scale-105 hover:shadow-orange-500/50 relative z-10">
                                    <i class="fas fa-bolt mr-3"></i>Hemen Al
                                </button>
                                <button class="px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-lg hover:bg-white/20 transition-all shadow-xl hover:scale-105">
                                    <i class="fas fa-phone mr-3"></i>İletişime Geç
                                </button>
                            </div>
                        </div>

                        <div class="relative hidden lg:block">
                            <div class="absolute inset-0 bg-gradient-to-tr from-orange-500/20 to-red-500/20 rounded-full blur-3xl animate-pulse"></div>
                            <i class="fas fa-industry text-white/80 product-icon relative z-10" style="font-size: 28rem; display: block; text-align: center;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 - Crown Transpalet -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-600 via-violet-600 to-fuchsia-600">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-purple-500/10 to-fuchsia-500/20 animate-pulse"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                        <div class="slide-content">
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border border-white/30 mb-8 shadow-2xl hover:scale-105 transition-transform duration-300">
                                <div class="w-3 h-3 bg-purple-400 rounded-full animate-ping"></div>
                                <div class="w-3 h-3 bg-purple-400 rounded-full absolute"></div>
                                <span class="text-white font-bold text-sm ml-2">CROWN • Akülü Transpalet Serisi</span>
                            </div>

                            <h1 class="text-7xl lg:text-8xl font-black text-white leading-none mb-6 tracking-tight">
                                WP2000
                            </h1>

                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-1 w-20 bg-gradient-to-r from-purple-400 to-fuchsia-600 rounded-full"></div>
                                <p class="text-2xl text-purple-100 font-bold">2 Ton Kapasite</p>
                            </div>

                            <div class="space-y-3 mb-10">
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-cube text-purple-300"></i>
                                    </div>
                                    <span>Kompakt Tasarım</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-volume-off text-purple-300"></i>
                                    </div>
                                    <span>Sessiz Çalışma</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-warehouse text-purple-300"></i>
                                    </div>
                                    <span>Depo İçi Taşıma</span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-white/20 to-white/10 backdrop-blur-xl rounded-2xl p-6 mb-10 border border-white/30 shadow-2xl price-pulse">
                                <div class="text-sm text-white/70 mb-2">Fiyat</div>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-6xl font-black text-white">₺285.000</span>
                                    <span class="text-xl text-white/70">+ KDV</span>
                                </div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="cta-button px-10 py-5 bg-white text-purple-600 rounded-2xl font-black text-lg hover:bg-purple-50 transition-all shadow-2xl hover:scale-105 hover:shadow-purple-500/50 relative z-10">
                                    <i class="fas fa-cart-plus mr-3"></i>Sepete Ekle
                                </button>
                                <button class="px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-lg hover:bg-white/20 transition-all shadow-xl hover:scale-105">
                                    <i class="fas fa-video mr-3"></i>Demo İzle
                                </button>
                            </div>
                        </div>

                        <div class="relative hidden lg:block">
                            <div class="absolute inset-0 bg-gradient-to-tr from-purple-500/20 to-fuchsia-500/20 rounded-full blur-3xl animate-pulse"></div>
                            <i class="fas fa-pallet text-white/80 product-icon relative z-10" style="font-size: 28rem; display: block; text-align: center;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4 - BT İstif -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-green-600 via-emerald-600 to-teal-600">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-green-500/10 to-teal-500/20 animate-pulse"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 items-center w-full">
                        <div class="slide-content">
                            <div class="inline-flex items-center gap-3 px-6 py-3 bg-white/20 backdrop-blur-xl rounded-full border border-white/30 mb-8 shadow-2xl hover:scale-105 transition-transform duration-300">
                                <div class="w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                                <div class="w-3 h-3 bg-green-400 rounded-full absolute"></div>
                                <span class="text-white font-bold text-sm ml-2">BT • Elektrikli İstif Makinesi</span>
                            </div>

                            <h1 class="text-7xl lg:text-8xl font-black text-white leading-none mb-6 tracking-tight">
                                SWE120
                            </h1>

                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-1 w-20 bg-gradient-to-r from-green-400 to-teal-600 rounded-full"></div>
                                <p class="text-2xl text-green-100 font-bold">1.2 Ton Kapasite</p>
                            </div>

                            <div class="space-y-3 mb-10">
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-arrows-left-right text-green-300"></i>
                                    </div>
                                    <span>Dar Koridor Kullanımı</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-arrow-up text-green-300"></i>
                                    </div>
                                    <span>3m Kaldırma Yüksekliği</span>
                                </div>
                                <div class="flex items-center gap-3 text-white/90">
                                    <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                        <i class="fas fa-shield-halved text-green-300"></i>
                                    </div>
                                    <span>Güvenli Operasyon</span>
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-white/20 to-white/10 backdrop-blur-xl rounded-2xl p-6 mb-10 border border-white/30 shadow-2xl price-pulse">
                                <div class="text-sm text-white/70 mb-2">Fiyat</div>
                                <div class="flex items-baseline gap-3">
                                    <span class="text-6xl font-black text-white">₺195.000</span>
                                    <span class="text-xl text-white/70">+ KDV</span>
                                </div>
                            </div>

                            <div class="flex gap-4 flex-wrap">
                                <button class="cta-button px-10 py-5 bg-white text-green-600 rounded-2xl font-black text-lg hover:bg-green-50 transition-all shadow-2xl hover:scale-105 hover:shadow-green-500/50 relative z-10">
                                    <i class="fas fa-check mr-3"></i>Sipariş Ver
                                </button>
                                <button class="px-10 py-5 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-lg hover:bg-white/20 transition-all shadow-xl hover:scale-105">
                                    <i class="fas fa-file-invoice mr-3"></i>Teklif Al
                                </button>
                            </div>
                        </div>

                        <div class="relative hidden lg:block">
                            <div class="absolute inset-0 bg-gradient-to-tr from-green-500/20 to-teal-500/20 rounded-full blur-3xl animate-pulse"></div>
                            <i class="fas fa-boxes-stacked text-white/80 product-icon relative z-10" style="font-size: 28rem; display: block; text-align: center;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-12"></div>

        <!-- Navigation -->
        <div class="swiper-button-prev !text-white !w-14 !h-14 !bg-white/10 !backdrop-blur-xl !rounded-full !border !border-white/20 hover:!bg-white/20 !transition-all after:!text-2xl"></div>
        <div class="swiper-button-next !text-white !w-14 !h-14 !bg-white/10 !backdrop-blur-xl !rounded-full !border !border-white/20 hover:!bg-white/20 !transition-all after:!text-2xl"></div>
    </div>

    <!-- Info Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-black/40 backdrop-blur-2xl z-20 border-t border-white/10">
        <div class="container mx-auto px-8 py-6">
            <div class="flex items-center justify-between flex-wrap gap-6 text-white">
                <div class="flex items-center gap-4 group hover:scale-105 transition-transform duration-300">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-green-500/50 transition-shadow">
                        <i class="fas fa-shield-check text-xl text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Güvence</div>
                        <div class="font-bold text-lg">2 Yıl Garanti</div>
                    </div>
                </div>

                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>

                <div class="flex items-center gap-4 group hover:scale-105 transition-transform duration-300">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-blue-500/50 transition-shadow">
                        <i class="fas fa-truck-fast text-xl text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Teslimat</div>
                        <div class="font-bold text-lg">24-72 Saat</div>
                    </div>
                </div>

                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>

                <div class="flex items-center gap-4 group hover:scale-105 transition-transform duration-300">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-purple-500/50 transition-shadow">
                        <i class="fas fa-headset text-xl text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Destek</div>
                        <div class="font-bold text-lg">7/24 Hizmet</div>
                    </div>
                </div>

                <div class="h-12 w-px bg-white/20 hidden sm:block"></div>

                <div class="flex items-center gap-4 group hover:scale-105 transition-transform duration-300">
                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-yellow-500/50 transition-shadow">
                        <i class="fas fa-certificate text-xl text-white"></i>
                    </div>
                    <div>
                        <div class="text-sm opacity-70">Sertifika</div>
                        <div class="font-bold text-lg">CE Belgeli</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.modernSwiper', {
        speed: 800,
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
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
    });
});
</script>
@endsection
