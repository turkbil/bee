@extends('themes.ixtif.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.hero-slider-container {
    height: auto;
    min-height: 500px;
    width: 100%;
    overflow: hidden;
}

.swiper-slide {
    height: 100%;
    display: flex;
    align-items: center;
    position: relative;
}

/* Alt kısımda body background ile birleşme - 70%'den sonra transparent 0 */
.swiper-slide::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 300px;
    background: linear-gradient(to bottom, transparent 0%, transparent 70%, white 100%);
    pointer-events: none;
    z-index: 1;
}

.dark .swiper-slide::after {
    background: linear-gradient(to bottom, transparent 0%, transparent 70%, #111827 100%);
}

/* Gradient Animation */
@keyframes gradientFlow {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.gradient-animate {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #667eea 75%, #764ba2 100%);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradientFlow 8s ease infinite;
}

/* Dark mode gradient */
.dark .gradient-animate {
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 25%, #f472b6 50%, #60a5fa 75%, #a78bfa 100%);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* UZAKTAN GELİP YERİNDE DURACAK ANİMASYONLAR */

/* Title - Soldan dramatic giriş + pulse */
@keyframes titleEntry {
    0% {
        opacity: 0;
        transform: translateX(-150px) scale(0.8);
    }
    60% {
        transform: translateX(10px) scale(1.05);
    }
    100% {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

@keyframes titlePulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
}

/* Description - Sağdan giriş */
@keyframes descriptionEntry {
    0% {
        opacity: 0;
        transform: translateX(100px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

/* CTA - Aşağıdan dramatic */
@keyframes ctaEntry {
    0% {
        opacity: 0;
        transform: translateY(80px) scale(0.7);
    }
    70% {
        transform: translateY(-5px) scale(1.05);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Features - Pop in */
@keyframes featureEntry {
    0% {
        opacity: 0;
        transform: scale(0.5) rotate(-5deg);
    }
    70% {
        transform: scale(1.1) rotate(2deg);
    }
    100% {
        opacity: 1;
        transform: scale(1) rotate(0);
    }
}

/* Animated elements */
.animate-title {
    animation: titleEntry 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    opacity: 0;
}

.animate-description {
    animation: descriptionEntry 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    opacity: 0;
}

.animate-cta {
    animation: ctaEntry 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    opacity: 0;
}

.feature-item {
    animation: featureEntry 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    opacity: 0;
}

/* Staggered delays */
.swiper-slide-active .animate-title { animation-delay: 0s; }
.swiper-slide-active .animate-description { animation-delay: 0.2s; }
.swiper-slide-active .animate-cta { animation-delay: 0.4s; }

/* Feature items */
.swiper-slide-active .feature-item:nth-child(1) { animation-delay: 0.6s; }
.swiper-slide-active .feature-item:nth-child(2) { animation-delay: 0.7s; }
.swiper-slide-active .feature-item:nth-child(3) { animation-delay: 0.8s; }
.swiper-slide-active .feature-item:nth-child(4) { animation-delay: 0.9s; }

/* LAZY ANIMATION - Aktif olmayan slide'larda animasyon resetle */
.swiper-slide:not(.swiper-slide-active) .animate-title,
.swiper-slide:not(.swiper-slide-active) .animate-description,
.swiper-slide:not(.swiper-slide-active) .animate-cta,
.swiper-slide:not(.swiper-slide-active) .feature-item {
    animation: none !important;
    opacity: 0;
    transform: none;
}

/* Image - completely static */
.hero-image {
    /* No animation, no hover effects */
}

/* Navigation styling */
.swiper-button-prev,
.swiper-button-next {
    color: #667eea !important;
    width: 60px !important;
    height: 60px !important;
    background: rgba(255, 255, 255, 0.9) !important;
    border-radius: 50% !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.3s !important;
}

.dark .swiper-button-prev,
.dark .swiper-button-next {
    background: rgba(30, 41, 59, 0.9) !important;
    color: #60a5fa !important;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    transform: scale(1.1);
}

.swiper-button-prev::after,
.swiper-button-next::after {
    font-size: 24px !important;
    font-weight: bold;
}

.swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: #667eea;
    opacity: 0.3;
}

.swiper-pagination-bullet-active {
    width: 40px;
    border-radius: 6px;
    opacity: 1;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* SLIDE BACKGROUNDS - Light & Dark Mode */
.slide-1-bg {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 50%, #fce7f3 100%);
}
.dark .slide-1-bg {
    background: linear-gradient(135deg, #1e293b 0%, #312e81 50%, #831843 100%);
}

.slide-2-bg {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #c7d2fe 100%);
}
.dark .slide-2-bg {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%);
}

.slide-3-bg {
    background: linear-gradient(135deg, #fae8ff 0%, #f3e8ff 50%, #fbcfe8 100%);
}
.dark .slide-3-bg {
    background: linear-gradient(135deg, #581c87 0%, #6b21a8 50%, #831843 100%);
}

.slide-4-bg {
    background: linear-gradient(135deg, #fed7aa 0%, #fecaca 50%, #fca5a5 100%);
}
.dark .slide-4-bg {
    background: linear-gradient(135deg, #7c2d12 0%, #991b1b 50%, #b91c1c 100%);
}

.slide-5-bg {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
}
.dark .slide-5-bg {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
}

.slide-6-bg {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%);
}
.dark .slide-6-bg {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
}

/* Lazy animation reset - Her slide değişiminde yazılar tekrar geliyor */
.swiper-slide:not(.swiper-slide-active) .animate-title,
.swiper-slide:not(.swiper-slide-active) .animate-description,
.swiper-slide:not(.swiper-slide-active) .animate-cta,
.swiper-slide:not(.swiper-slide-active) .feature-item {
    animation: none !important;
    opacity: 0;
    transform: none;
}
</style>

<div class="hero-slider-container bg-white dark:bg-gray-900 relative">

    <!-- EFEKT SEÇİCİ -->
    <div class="absolute top-4 left-4 z-50">
        <select id="effectSelector" class="px-6 py-3 bg-white dark:bg-gray-800 border-2 border-blue-500 rounded-xl shadow-lg text-gray-900 dark:text-white font-bold text-sm cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition-all">
            <option value="slide">🎯 Normal Slide</option>
            <option value="fade">✨ Fade (Solma)</option>
            <option value="cube">🎲 Cube (3D Küp)</option>
            <option value="coverflow">📚 Coverflow</option>
            <option value="flip">🔄 Flip (Çevirme)</option>
            <option value="cards">🃏 Cards (Kartlar)</option>
            <option value="creative-1">🌊 Creative: Wave</option>
            <option value="creative-2">🚀 Creative: Zoom</option>
            <option value="creative-3">💫 Creative: Rotate</option>
        </select>
    </div>

    <div class="swiper heroSwiper h-full">
        <div class="swiper-wrapper">

            <!-- SLIDE 1: ANA HERO - TÜRKİYE'NİN İSTİF PAZARI -->
            <div class="swiper-slide slide-1-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <!-- Left Content -->
                        <div class="text-gray-900 dark:text-white">
                            <!-- Main Title -->
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    TÜRKİYE'NİN
                                </span>
                                <span class="gradient-animate block py-2">
                                    İSTİF PAZARI
                                </span>
                            </h1>

                            <!-- Description -->
                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Profesyonel istif çözümleri, güçlü stok ve hızlı teslimat ile işletmenizin güvenilir ortağı
                            </p>

                            <!-- CTA Button -->
                            <div class="animate-cta mb-16">
                                <a href="/shop" class="group bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-shopping-cart mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                    Ürünleri İncele
                                </a>
                            </div>

                            <!-- Features -->
                            <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-boxes-stacked text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Güçlü Stok</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Zengin ürün çeşidi</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-certificate text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Garantili Ürün</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
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

                        <!-- Right Content - Hero Image -->
                        <div class="flex items-center justify-center">
                            <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                 alt="iXtif İstif Makinesi"
                                 class="w-full h-auto object-contain hero-image"
                                 loading="eager"
                                >
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: FORKLIFT SATIŞ ODAKLI -->
            <div class="swiper-slide slide-2-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    PREMİUM FORKLIFT
                                </span>
                                <span class="gradient-animate block py-2">
                                    SATIŞINDA LİDER
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Akülü, dizel ve LPG forklift modelleri ile her ihtiyaca uygun çözümler
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/shop" class="group bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-forklift mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Forklift Kataloğu
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-bolt text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Akülü Forklift</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">1.5-3.5 ton kapasite</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-fire text-orange-600 dark:text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Dizel Forklift</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">2.5-10 ton kapasite</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-shield-check text-green-600 dark:text-green-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">2 Yıl Garanti</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Orijinal yedek parça</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-headset text-purple-600 dark:text-purple-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">7/24 Destek</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl opacity-20 blur-2xl"></div>
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                     alt="Premium Forklift"
                                     class="relative w-full h-auto object-contain hero-image"
                                     loading="lazy"
                                    >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: KİRALAMA ODAKLI -->
            <div class="swiper-slide slide-3-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    ESNEK KİRALAMA
                                </span>
                                <span class="gradient-animate block py-2">
                                    ÇÖZÜMLERİ
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Günlük, haftalık veya uzun dönem kiralama seçenekleri ile esneklik
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/kiralama" class="group bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-calendar-days mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Kiralama Koşulları
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-clock text-purple-600 dark:text-purple-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Günlük Kiralama</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Anlık ihtiyaçlar</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-infinity text-pink-600 dark:text-pink-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Uzun Dönem</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Özel fiyatlandırma</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-tools text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Bakım Dahil</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Ek maliyet yok</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-shield-alt text-green-600 dark:text-green-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Sigorta</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Tam kasko dahil</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-purple-600 to-pink-600 rounded-3xl opacity-20 blur-2xl"></div>
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                     alt="Kiralama Hizmetleri"
                                     class="relative w-full h-auto object-contain hero-image"
                                     loading="lazy"
                                    >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4: SERVİS & BAKIM -->
            <div class="swiper-slide slide-4-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    PROFESYONEL
                                </span>
                                <span class="gradient-animate block py-2">
                                    TEKNİK SERVİS
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Uzman kadromuz ile periyodik bakım, onarım ve yedek parça hizmetleri
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/teknik-servis" class="group bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-wrench mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                    Servis Talebi
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-calendar-check text-orange-600 dark:text-orange-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Periyodik Bakım</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Düzenli kontrol</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-screwdriver-wrench text-red-600 dark:text-red-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Hızlı Onarım</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">24 saat müdahale</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-cog text-amber-600 dark:text-amber-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Orijinal Parça</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Garantili ürünler</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-user-gear text-blue-600 dark:text-blue-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Uzman Ekip</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Sertifikalı teknisyen</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-orange-600 to-red-600 rounded-3xl opacity-20 blur-2xl"></div>
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                     alt="Teknik Servis"
                                     class="relative w-full h-auto object-contain hero-image"
                                     loading="lazy"
                                    >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 5: KAMPANYA / İNDİRİM -->
            <div class="swiper-slide slide-5-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <!-- Special Badge -->
                            <div class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-orange-500 text-white rounded-full font-bold text-sm mb-6 shadow-lg animate-pulse">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                                ÖZEL KAMPANYA
                            </div>

                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    %40'A VARAN
                                </span>
                                <span class="gradient-animate block py-2">
                                    İNDİRİMLER
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Yılsonu fırsatları! Forklift, transpalet ve istif makinelerinde büyük indirim
                            </p>

                            <div class="mb-16 flex gap-4 flex-wrap">
                                <a href="/shop" class="group bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-tags mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Kampanyalı Ürünler
                                </a>
                                <div class="inline-flex items-center gap-2 px-6 py-4 bg-white dark:bg-gray-800 rounded-full font-bold text-emerald-600 dark:text-emerald-400 border-2 border-emerald-600 dark:border-emerald-400">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Son 15 Gün!</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-percent text-emerald-600 dark:text-emerald-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">%40 İndirim</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Seçili ürünlerde</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-gift text-teal-600 dark:text-teal-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Hediye Çeki</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">5.000 TL'ye kadar</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-truck-fast text-blue-600 dark:text-blue-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Ücretsiz Kargo</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Tüm siparişlerde</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-credit-card text-purple-600 dark:text-purple-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">12 Taksit</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Faizsiz imkan</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-3xl opacity-20 blur-2xl"></div>
                                <!-- Campaign Badge on Image -->
                                <div class="absolute -top-6 -right-6 w-32 h-32 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center shadow-2xl z-10 animate-pulse">
                                    <div class="text-center text-white">
                                        <div class="text-3xl font-black">%40</div>
                                        <div class="text-xs font-bold">İNDİRİM</div>
                                    </div>
                                </div>
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                     alt="Kampanya Ürünleri"
                                     class="relative w-full h-auto object-contain hero-image"
                                     loading="lazy"
                                    >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 6: KURUMSAL / GÜVEN -->
            <div class="swiper-slide slide-6-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    20 YILLIK
                                </span>
                                <span class="gradient-animate block py-2">
                                    GÜVEN VE DENEYİM
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Türkiye'nin önde gelen kurumsal firmalarının tercihi, 500+ referans
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/hakkimizda" class="group bg-gradient-to-r from-slate-700 to-gray-700 hover:from-slate-800 hover:to-gray-800 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-building mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Kurumsal Bilgiler
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-trophy text-amber-600 dark:text-amber-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">500+ Referans</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Kurumsal müşteri</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-calendar text-blue-600 dark:text-blue-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">20 Yıl Deneyim</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Sektör liderliği</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-shield-check text-green-600 dark:text-green-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">ISO Sertifikalı</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Kalite güvencesi</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-users text-purple-600 dark:text-purple-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Uzman Ekip</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">50+ çalışan</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <div class="relative">
                                <div class="absolute -inset-4 bg-gradient-to-r from-slate-600 to-gray-600 rounded-3xl opacity-20 blur-2xl"></div>
                                <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                     alt="Kurumsal Güven"
                                     class="relative w-full h-auto object-contain hero-image"
                                     loading="lazy"
                                    >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Navigation Arrows -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-8"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let swiper = null;

    // Efekt konfigürasyonları
    const effectConfigs = {
        'slide': {
            effect: 'slide',
            speed: 600,
        },
        'fade': {
            effect: 'fade',
            speed: 800,
            fadeEffect: {
                crossFade: true
            },
        },
        'cube': {
            effect: 'cube',
            speed: 1000,
            cubeEffect: {
                shadow: true,
                slideShadows: true,
                shadowOffset: 20,
                shadowScale: 0.94,
            },
        },
        'coverflow': {
            effect: 'coverflow',
            speed: 600,
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
        },
        'flip': {
            effect: 'flip',
            speed: 800,
            flipEffect: {
                slideShadows: true,
                limitRotation: true,
            },
        },
        'cards': {
            effect: 'cards',
            speed: 600,
            cardsEffect: {
                perSlideOffset: 8,
                perSlideRotate: 2,
                rotate: true,
                slideShadows: true,
            },
        },
        'creative-1': {
            effect: 'creative',
            speed: 800,
            creativeEffect: {
                prev: {
                    translate: ['-120%', 0, -500],
                },
                next: {
                    translate: ['120%', 0, -500],
                },
            },
        },
        'creative-2': {
            effect: 'creative',
            speed: 600,
            creativeEffect: {
                prev: {
                    translate: [0, 0, -400],
                    scale: 0.7,
                },
                next: {
                    translate: [0, 0, -400],
                    scale: 0.7,
                },
            },
        },
        'creative-3': {
            effect: 'creative',
            speed: 800,
            creativeEffect: {
                prev: {
                    translate: ['-50%', 0, -1],
                    rotate: [0, 0, -15],
                },
                next: {
                    translate: ['50%', 0, -1],
                    rotate: [0, 0, 15],
                },
            },
        },
    };

    // Swiper başlatma fonksiyonu
    function initSwiper(effectType = 'slide') {
        // Mevcut swiper varsa yok et
        if (swiper) {
            swiper.destroy(true, true);
        }

        const config = effectConfigs[effectType] || effectConfigs['slide'];

        // Base config
        const baseConfig = {
            direction: 'horizontal',
            loop: true,
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
            keyboard: {
                enabled: true,
            },
            mousewheel: false,
            grabCursor: true,
        };

        // Merge configs
        swiper = new Swiper('.heroSwiper', {...baseConfig, ...config});
    }

    // İlk yükleme
    initSwiper('slide');

    // Select değiştiğinde
    document.getElementById('effectSelector').addEventListener('change', function(e) {
        initSwiper(e.target.value);
    });
});
</script>
@endsection
