@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V1</a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V2</a>
    <a href="{{ route('design.deluxe-industrial.v3') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V3</a>
    <a href="{{ route('design.deluxe-industrial.v4') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V4</a>
    <a href="{{ route('design.deluxe-industrial.v5') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V5</a>
    <a href="{{ route('design.deluxe-industrial.v6') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">V6</a>
    <a href="{{ route('design.deluxe-industrial.v7') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V7</a>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.hero-video-overlay {
    background: linear-gradient(135deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.4) 100%);
}

@keyframes heroFadeIn {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

.hero-content > * {
    animation: heroFadeIn 1s ease-out forwards;
}

.hero-content > *:nth-child(1) { animation-delay: 0.2s; opacity: 0; }
.hero-content > *:nth-child(2) { animation-delay: 0.4s; opacity: 0; }
.hero-content > *:nth-child(3) { animation-delay: 0.6s; opacity: 0; }
.hero-content > *:nth-child(4) { animation-delay: 0.8s; opacity: 0; }

.swiper-slide {
    background-size: cover;
    background-position: center;
}

.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: white;
    opacity: 0.5;
}

.swiper-pagination-bullet-active {
    opacity: 1;
    background: #3b82f6;
}
</style>

<!-- FULL-SCREEN VIDEO HERO WITH SLIDER -->
<section class="relative h-screen overflow-hidden">
    <!-- Swiper Slider -->
    <div class="swiper heroSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1: Forklift -->
            <div class="swiper-slide bg-gradient-to-br from-blue-900 via-blue-700 to-cyan-600 dark:from-gray-950 dark:via-blue-950 dark:to-cyan-950">
                <div class="absolute inset-0 hero-video-overlay"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-forklift text-[30rem] text-white opacity-10 absolute"></i>
                </div>
                <div class="container mx-auto px-6 h-full flex items-center relative z-10">
                    <div class="max-w-4xl hero-content">
                        <div class="inline-block px-6 py-2 bg-blue-500 text-white rounded-full text-sm font-bold mb-8">
                            <i class="fas fa-bolt mr-2"></i>AKÜLÜ TEKNOLOJİ
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white leading-none mb-8">
                            Elektrikli<br/>
                            <span class="text-cyan-400">Forklift</span><br/>
                            Serisi
                        </h1>
                        <p class="text-2xl text-white/90 mb-12 max-w-2xl">
                            Çevre dostu, sessiz çalışma, düşük işletme maliyeti. 2.5T - 5T kapasite aralığında 50+ model.
                        </p>
                        <div class="flex gap-6">
                            <button class="px-12 py-6 bg-white text-blue-600 rounded-2xl font-black text-xl hover:bg-blue-50 transition-all shadow-2xl hover:scale-105">
                                Modelleri Gör
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white hover:text-blue-600 transition-all">
                                Fiyat Teklifi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2: Dizel Forklift -->
            <div class="swiper-slide bg-gradient-to-br from-orange-900 via-red-700 to-pink-600 dark:from-gray-950 dark:via-orange-950 dark:to-red-950">
                <div class="absolute inset-0 hero-video-overlay"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-industry text-[28rem] text-white opacity-10 absolute"></i>
                </div>
                <div class="container mx-auto px-6 h-full flex items-center relative z-10">
                    <div class="max-w-4xl hero-content">
                        <div class="inline-block px-6 py-2 bg-orange-500 text-white rounded-full text-sm font-bold mb-8">
                            <i class="fas fa-fire mr-2"></i>HEAVY DUTY
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white leading-none mb-8">
                            Dizel Güç<br/>
                            <span class="text-orange-400">Outdoor</span><br/>
                            Forklift
                        </h1>
                        <p class="text-2xl text-white/90 mb-12 max-w-2xl">
                            Açık alan kullanımı için maksimum güç. 3T - 10T arası yüksek kapasite, zorlu koşullara dayanıklı.
                        </p>
                        <div class="flex gap-6">
                            <button class="px-12 py-6 bg-white text-orange-600 rounded-2xl font-black text-xl hover:bg-orange-50 transition-all shadow-2xl hover:scale-105">
                                Keşfet
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white hover:text-orange-600 transition-all">
                                İletişime Geç
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3: Transpalet -->
            <div class="swiper-slide bg-gradient-to-br from-purple-900 via-violet-700 to-fuchsia-600 dark:from-gray-950 dark:via-purple-950 dark:to-fuchsia-950">
                <div class="absolute inset-0 hero-video-overlay"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-pallet text-[28rem] text-white opacity-10 absolute"></i>
                </div>
                <div class="container mx-auto px-6 h-full flex items-center relative z-10">
                    <div class="max-w-4xl hero-content">
                        <div class="inline-block px-6 py-2 bg-purple-500 text-white rounded-full text-sm font-bold mb-8">
                            <i class="fas fa-zap mr-2"></i>AKILLI HAREKET
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white leading-none mb-8">
                            Akülü<br/>
                            <span class="text-purple-400">Transpalet</span><br/>
                            Çözümleri
                        </h1>
                        <p class="text-2xl text-white/90 mb-12 max-w-2xl">
                            Depo içi taşımada yeni nesil teknoloji. Kompakt tasarım, kolay manevra, sessiz çalışma.
                        </p>
                        <div class="flex gap-6">
                            <button class="px-12 py-6 bg-white text-purple-600 rounded-2xl font-black text-xl hover:bg-purple-50 transition-all shadow-2xl hover:scale-105">
                                Ürünleri İncele
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white hover:text-purple-600 transition-all">
                                Demo Talep Et
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4: İstif Makinesi -->
            <div class="swiper-slide bg-gradient-to-br from-green-900 via-emerald-700 to-teal-600 dark:from-gray-950 dark:via-green-950 dark:to-teal-950">
                <div class="absolute inset-0 hero-video-overlay"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-[26rem] text-white opacity-10 absolute"></i>
                </div>
                <div class="container mx-auto px-6 h-full flex items-center relative z-10">
                    <div class="max-w-4xl hero-content">
                        <div class="inline-block px-6 py-2 bg-green-500 text-white rounded-full text-sm font-bold mb-8">
                            <i class="fas fa-arrow-up mr-2"></i>YÜKSEK PERFORMANS
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white leading-none mb-8">
                            İstif<br/>
                            <span class="text-green-400">Makineleri</span><br/>
                            3-6 Metre
                        </h1>
                        <p class="text-2xl text-white/90 mb-12 max-w-2xl">
                            Dar koridorlarda maksimum verim. Elektrikli yığma sistemleri, ergonomik tasarım, güvenli operasyon.
                        </p>
                        <div class="flex gap-6">
                            <button class="px-12 py-6 bg-white text-green-600 rounded-2xl font-black text-xl hover:bg-green-50 transition-all shadow-2xl hover:scale-105">
                                Tüm Modeller
                            </button>
                            <button class="px-12 py-6 border-3 border-white text-white rounded-2xl font-bold text-xl hover:bg-white hover:text-green-600 transition-all">
                                Teknik Detay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev !text-white !w-16 !h-16 !bg-white/10 !backdrop-blur-sm !rounded-full hover:!bg-white/20 transition-all"></div>
        <div class="swiper-button-next !text-white !w-16 !h-16 !bg-white/10 !backdrop-blur-sm !rounded-full hover:!bg-white/20 transition-all"></div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 flex flex-col items-center gap-3 text-white/70 hover:text-white transition-all cursor-pointer">
        <span class="text-sm font-semibold tracking-wider uppercase">Aşağı Kaydır</span>
        <i class="fas fa-chevron-down text-2xl animate-bounce"></i>
    </div>

    <!-- Info Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-black/30 backdrop-blur-md border-t border-white/10 z-20">
        <div class="container mx-auto px-6 py-6">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-phone text-2xl text-blue-400"></i>
                        <div>
                            <div class="text-xs text-white/60">Hemen Ara</div>
                            <div class="font-bold">0850 xxx xx xx</div>
                        </div>
                    </div>
                    <div class="h-12 w-px bg-white/20"></div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clock text-2xl text-green-400"></i>
                        <div>
                            <div class="text-xs text-white/60">Çalışma Saati</div>
                            <div class="font-bold">7/24 Destek</div>
                        </div>
                    </div>
                    <div class="h-12 w-px bg-white/20"></div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-warehouse text-2xl text-orange-400"></i>
                        <div>
                            <div class="text-xs text-white/60">Showroom</div>
                            <div class="font-bold">İstanbul & Ankara</div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-white/60 mb-1">Tüm ürünlerde</div>
                    <div class="text-xl font-black text-yellow-400">2 YIL GARANTİ</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.heroSwiper', {
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

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-50 hidden lg:block">
    <div class="bg-black/80 backdrop-blur-md border-2 border-blue-500 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-blue-400">#DELUXE-V6</span>
        <span class="mx-3 text-blue-500/50">|</span>
        <span class="text-xs text-blue-400/80 font-bold">CINEMATIC SLIDER HERO</span>
    </div>
</div>
@endsection
