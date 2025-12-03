@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    @for($i = 1; $i <= 10; $i++)
        <a href="{{ route('design.deluxe-industrial.v'.$i) }}" class="px-4 py-2 {{ $i == 9 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-600' }} rounded-lg font-bold transition-all">V{{ $i }}</a>
    @endfor
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.parallax-bg {
    transition: transform 0.5s ease-out;
}
</style>

<!-- 3D PARALLAX SLIDER HERO -->
<section class="relative h-screen overflow-hidden bg-black">
    <div class="swiper parallaxSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide relative">
                <div class="absolute inset-0 parallax-bg bg-gradient-to-r from-blue-900 via-blue-800 to-cyan-900" data-swiper-parallax="-23%">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>

                <div class="container mx-auto px-12 h-full flex items-center relative z-10">
                    <div class="max-w-4xl" data-swiper-parallax="-300">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-blue-500 text-white rounded-full font-bold mb-8">
                            <i class="fas fa-bolt text-xl"></i>
                            <span>AKÜLÜ SERİSİ</span>
                        </div>

                        <h1 class="text-7xl lg:text-9xl font-black text-white leading-none mb-8" data-swiper-parallax="-200">
                            TOYOTA<br/>
                            8FBE25
                        </h1>

                        <div class="text-3xl text-cyan-300 font-bold mb-8" data-swiper-parallax="-100">
                            Akülü Forklift • 2.5 Ton
                        </div>

                        <div class="flex items-center gap-8 mb-12" data-swiper-parallax="-50">
                            <div>
                                <div class="text-sm text-white/60 mb-2">Fiyat</div>
                                <div class="text-5xl font-black text-white">₺1.575.000</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Durum</div>
                                <div class="text-2xl font-bold text-green-400">Stokta</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Teslimat</div>
                                <div class="text-2xl font-bold text-orange-400">24-72 saat</div>
                            </div>
                        </div>

                        <div class="flex gap-6" data-swiper-parallax="0">
                            <button class="px-12 py-6 bg-white text-blue-600 rounded-2xl font-black text-xl hover:bg-blue-50 transition-all shadow-2xl hover:scale-105">
                                <i class="fas fa-shopping-cart mr-3"></i>Satın Al
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white/10 transition-all">
                                <i class="fas fa-info-circle mr-3"></i>Detaylar
                            </button>
                        </div>
                    </div>

                    <div class="absolute right-24 top-1/2 -translate-y-1/2" data-swiper-parallax="200">
                        <i class="fas fa-forklift text-[32rem] text-white/10"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide relative">
                <div class="absolute inset-0 parallax-bg bg-gradient-to-r from-orange-900 via-red-900 to-pink-900" data-swiper-parallax="-23%">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>

                <div class="container mx-auto px-12 h-full flex items-center relative z-10">
                    <div class="max-w-4xl" data-swiper-parallax="-300">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-orange-500 text-white rounded-full font-bold mb-8">
                            <i class="fas fa-fire text-xl"></i>
                            <span>DİZEL GÜÇ</span>
                        </div>

                        <h1 class="text-7xl lg:text-9xl font-black text-white leading-none mb-8" data-swiper-parallax="-200">
                            MITSUBISHI<br/>
                            FD35
                        </h1>

                        <div class="text-3xl text-orange-300 font-bold mb-8" data-swiper-parallax="-100">
                            Dizel Forklift • 3.5 Ton
                        </div>

                        <div class="flex items-center gap-8 mb-12" data-swiper-parallax="-50">
                            <div>
                                <div class="text-sm text-white/60 mb-2">Fiyat</div>
                                <div class="text-5xl font-black text-white">₺2.450.000</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Durum</div>
                                <div class="text-2xl font-bold text-green-400">Stokta</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Garanti</div>
                                <div class="text-2xl font-bold text-blue-400">2 Yıl</div>
                            </div>
                        </div>

                        <div class="flex gap-6" data-swiper-parallax="0">
                            <button class="px-12 py-6 bg-white text-orange-600 rounded-2xl font-black text-xl hover:bg-orange-50 transition-all shadow-2xl hover:scale-105">
                                <i class="fas fa-bolt mr-3"></i>Hemen Al
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white/10 transition-all">
                                <i class="fas fa-file-invoice mr-3"></i>Teklif İste
                            </button>
                        </div>
                    </div>

                    <div class="absolute right-24 top-1/2 -translate-y-1/2" data-swiper-parallax="200">
                        <i class="fas fa-industry text-[32rem] text-white/10"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide relative">
                <div class="absolute inset-0 parallax-bg bg-gradient-to-r from-purple-900 via-violet-900 to-fuchsia-900" data-swiper-parallax="-23%">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>

                <div class="container mx-auto px-12 h-full flex items-center relative z-10">
                    <div class="max-w-4xl" data-swiper-parallax="-300">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-purple-500 text-white rounded-full font-bold mb-8">
                            <i class="fas fa-zap text-xl"></i>
                            <span>KOMPAKT ÇÖZÜM</span>
                        </div>

                        <h1 class="text-7xl lg:text-9xl font-black text-white leading-none mb-8" data-swiper-parallax="-200">
                            CROWN<br/>
                            WP2000
                        </h1>

                        <div class="text-3xl text-purple-300 font-bold mb-8" data-swiper-parallax="-100">
                            Akülü Transpalet • 2 Ton
                        </div>

                        <div class="flex items-center gap-8 mb-12" data-swiper-parallax="-50">
                            <div>
                                <div class="text-sm text-white/60 mb-2">Fiyat</div>
                                <div class="text-5xl font-black text-white">₺285.000</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Rating</div>
                                <div class="flex text-yellow-400 text-2xl">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Satış</div>
                                <div class="text-2xl font-bold text-green-400">156 adet</div>
                            </div>
                        </div>

                        <div class="flex gap-6" data-swiper-parallax="0">
                            <button class="px-12 py-6 bg-white text-purple-600 rounded-2xl font-black text-xl hover:bg-purple-50 transition-all shadow-2xl hover:scale-105">
                                <i class="fas fa-cart-plus mr-3"></i>Sepete Ekle
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white/10 transition-all">
                                <i class="fas fa-video mr-3"></i>Demo İzle
                            </button>
                        </div>
                    </div>

                    <div class="absolute right-24 top-1/2 -translate-y-1/2" data-swiper-parallax="200">
                        <i class="fas fa-pallet text-[32rem] text-white/10"></i>
                    </div>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="swiper-slide relative">
                <div class="absolute inset-0 parallax-bg bg-gradient-to-r from-green-900 via-emerald-900 to-teal-900" data-swiper-parallax="-23%">
                    <div class="absolute inset-0 bg-black/40"></div>
                </div>

                <div class="container mx-auto px-12 h-full flex items-center relative z-10">
                    <div class="max-w-4xl" data-swiper-parallax="-300">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-green-500 text-white rounded-full font-bold mb-8">
                            <i class="fas fa-arrow-up text-xl"></i>
                            <span>YÜKSEK PERFORMANS</span>
                        </div>

                        <h1 class="text-7xl lg:text-9xl font-black text-white leading-none mb-8" data-swiper-parallax="-200">
                            BT<br/>
                            SWE120
                        </h1>

                        <div class="text-3xl text-green-300 font-bold mb-8" data-swiper-parallax="-100">
                            İstif Makinesi • 1.2 Ton
                        </div>

                        <div class="flex items-center gap-8 mb-12" data-swiper-parallax="-50">
                            <div>
                                <div class="text-sm text-white/60 mb-2">Fiyat</div>
                                <div class="text-5xl font-black text-white">₺195.000</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Yükseklik</div>
                                <div class="text-2xl font-bold text-green-400">3 Metre</div>
                            </div>
                            <div class="h-16 w-px bg-white/20"></div>
                            <div>
                                <div class="text-sm text-white/60 mb-2">Kargo</div>
                                <div class="text-2xl font-bold text-blue-400">Ücretsiz</div>
                            </div>
                        </div>

                        <div class="flex gap-6" data-swiper-parallax="0">
                            <button class="px-12 py-6 bg-white text-green-600 rounded-2xl font-black text-xl hover:bg-green-50 transition-all shadow-2xl hover:scale-105">
                                <i class="fas fa-check mr-3"></i>Sipariş Ver
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white/10 transition-all">
                                <i class="fas fa-phone mr-3"></i>Ara
                            </button>
                        </div>
                    </div>

                    <div class="absolute right-24 top-1/2 -translate-y-1/2" data-swiper-parallax="200">
                        <i class="fas fa-boxes-stacked text-[32rem] text-white/10"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-12"></div>

        <!-- Navigation -->
        <div class="absolute bottom-12 right-12 z-20 flex gap-4">
            <button class="swiper-button-prev-custom w-16 h-16 bg-white rounded-full flex items-center justify-center text-gray-900 hover:scale-110 transition-all shadow-2xl">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button class="swiper-button-next-custom w-16 h-16 bg-white rounded-full flex items-center justify-center text-gray-900 hover:scale-110 transition-all shadow-2xl">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.parallaxSwiper', {
        speed: 1000,
        parallax: true,
        autoplay: {
            delay: 5000,
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
    <div class="bg-black/80 backdrop-blur-md border-2 border-green-500 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-green-400">#DELUXE-V9</span>
        <span class="mx-3 text-green-500/50">|</span>
        <span class="text-xs text-green-400/80 font-bold">3D PARALLAX SLIDER</span>
    </div>
</div>
@endsection
