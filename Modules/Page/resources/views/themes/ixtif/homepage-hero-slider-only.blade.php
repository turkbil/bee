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

/* Overlay kaldırıldı - slide gradient'leri direkt transparent'a geçiyor */

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

/* TEXT STYLING - Her Slide Kendine Özel Gradient */

/* LIGHT MODE: Koyu gradientler - Tüm açık arka planlarda okunur */
.slide-1-bg .gradient-animate {
    background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #2563eb 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.slide-2-bg .gradient-animate {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.slide-3-bg .gradient-animate {
    background: linear-gradient(135deg, #9a3412 0%, #c2410c 50%, #ea580c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.slide-4-bg .gradient-animate {
    background: linear-gradient(135deg, #155e75 0%, #0e7490 50%, #0891b2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.slide-5-bg .gradient-animate {
    background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.slide-6-bg .gradient-animate {
    background: linear-gradient(135deg, #92400e 0%, #b45309 50%, #d97706 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

/* DARK MODE: Parlak gradientler - Slide temasına uygun */
.dark .slide-1-bg .gradient-animate {
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 30%, #60a5fa 70%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.dark .slide-2-bg .gradient-animate {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 30%, #cbd5e1 70%, #94a3b8 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.dark .slide-3-bg .gradient-animate {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 30%, #fb923c 70%, #f97316 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.dark .slide-4-bg .gradient-animate {
    background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 30%, #67e8f9 70%, #22d3ee 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.dark .slide-5-bg .gradient-animate {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 30%, #6ee7b7 70%, #34d399 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}

.dark .slide-6-bg .gradient-animate {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 30%, #fcd34d 70%, #fbbf24 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
}


/* BUTTON STYLING - Light: DARK & SOLID / Dark: Current Colors */

/* Light mode: Her slide için koyu, kontrast butonlar */
.slide-1-bg .group { background: #1e40af !important; } /* Dark blue */
.slide-1-bg .group:hover { background: #1e3a8a !important; }

.slide-2-bg .group { background: #1e293b !important; } /* Dark slate */
.slide-2-bg .group:hover { background: #0f172a !important; }

.slide-3-bg .group { background: #c2410c !important; } /* Dark orange */
.slide-3-bg .group:hover { background: #9a3412 !important; }

.slide-4-bg .group { background: #0e7490 !important; } /* Dark cyan */
.slide-4-bg .group:hover { background: #155e75 !important; }

.slide-5-bg .group { background: #047857 !important; } /* Dark green */
.slide-5-bg .group:hover { background: #065f46 !important; }

.slide-6-bg .group { background: #b45309 !important; } /* Dark amber */
.slide-6-bg .group:hover { background: #92400e !important; }

/* Dark mode: Orijinal buton renkleri kalsın (zaten iyi) */


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
/* FOTOĞRAF EFEKT - SADECE AKTİFTE */
.hero-image {
    opacity: 1;
}

.swiper-slide-active .hero-image {
    animation: imageFadeIn 1.2s ease-out forwards;
}

@keyframes imageFadeIn {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

/* TRANSPARAN NAVIGATION - HOVER'DA GÖRÜNÜR */
.swiper-button-prev,
.swiper-button-next {
    width: 60px !important;
    height: 60px !important;
    background: rgba(255, 255, 255, 0.15) !important;
    border-radius: 50% !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    backdrop-filter: blur(10px);
    opacity: 0 !important;
    transition: all 0.4s ease !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.swiper-button-prev::after,
.swiper-button-next::after {
    color: rgba(0, 0, 0, 0.6) !important;
    font-size: 22px !important;
    font-weight: 700 !important;
}

/* Hover'da görünür */
.hero-slider-container:hover .swiper-button-prev,
.hero-slider-container:hover .swiper-button-next {
    opacity: 1 !important;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    transform: scale(1.1) !important;
    background: rgba(255, 255, 255, 0.25) !important;
}

/* Pozisyon */
.swiper-button-prev {
    left: 20px !important;
}

.swiper-button-next {
    right: 20px !important;
}

/* Dark Mode */
.dark .swiper-button-prev,
.dark .swiper-button-next {
    background: rgba(30, 41, 59, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.2);
}

.dark .swiper-button-prev::after,
.dark .swiper-button-next::after {
    color: rgba(255, 255, 255, 0.8) !important;
}

/* SADE OPAK PAGINATION */
.swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #d1d5db;
    border-radius: 20px;
    opacity: 1;
    margin: 0 6px !important;
    transition: all 0.4s ease;
    position: relative;
}

/* Progress fill */
.swiper-pagination-bullet::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 0;
    height: 100%;
    background: #3b82f6;
    border-radius: 20px;
    transform: translateY(-50%);
    transition: none;
}

/* Active state */
.swiper-pagination-bullet-active {
    width: 40px;
    height: 10px;
    background: #e5e7eb;
}

.swiper-pagination-bullet-active::before {
    width: calc(100% - 2px);
    transition: width var(--autoplay-delay, 5000ms) linear;
}

/* Hover */
.swiper-pagination-bullet:hover {
    background: #9ca3af;
}

/* Dark Mode */
.dark .swiper-pagination-bullet {
    background: #4b5563;
}

.dark .swiper-pagination-bullet::before {
    background: #60a5fa;
}

.dark .swiper-pagination-bullet-active {
    background: #374151;
}

.dark .swiper-pagination-bullet:hover {
    background: #6b7280;
}

/* SLIDE BACKGROUNDS - Light: AÇIK PASTEL / Dark: TAM RENK */

/* Slide 1 - Sky Blue (Profesyonel) */
.slide-1-bg {
    background: linear-gradient(to bottom,
        #e0f2fe 0%,
        #bae6fd 30%,
        #7dd3fc 50%,
        rgba(125, 211, 252, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-1-bg {
    background: linear-gradient(to bottom,
        #1e40af 0%,
        #1d4ed8 30%,
        #2563eb 50%,
        rgba(37, 99, 235, 0.3) 75%,
        transparent 100%
    );
}

/* Slide 2 - Slate Gray (Güçlü) */
.slide-2-bg {
    background: linear-gradient(to bottom,
        #f1f5f9 0%,
        #e2e8f0 30%,
        #cbd5e1 50%,
        rgba(203, 213, 225, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-2-bg {
    background: linear-gradient(to bottom,
        #334155 0%,
        #475569 30%,
        #64748b 50%,
        rgba(100, 116, 139, 0.3) 75%,
        transparent 100%
    );
}

/* Slide 3 - Peach Orange (Enerji) */
.slide-3-bg {
    background: linear-gradient(to bottom,
        #ffedd5 0%,
        #fed7aa 30%,
        #fdba74 50%,
        rgba(253, 186, 116, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-3-bg {
    background: linear-gradient(to bottom,
        #ea580c 0%,
        #f97316 30%,
        #fb923c 50%,
        rgba(251, 146, 60, 0.3) 75%,
        transparent 100%
    );
}

/* Slide 4 - Cyan Blue (Teknolojik) */
.slide-4-bg {
    background: linear-gradient(to bottom,
        #cffafe 0%,
        #a5f3fc 30%,
        #67e8f9 50%,
        rgba(103, 232, 249, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-4-bg {
    background: linear-gradient(to bottom,
        #0e7490 0%,
        #0891b2 30%,
        #06b6d4 50%,
        rgba(6, 182, 212, 0.3) 75%,
        transparent 100%
    );
}

/* Slide 5 - Mint Green (Verimlilik) */
.slide-5-bg {
    background: linear-gradient(to bottom,
        #d1fae5 0%,
        #a7f3d0 30%,
        #6ee7b7 50%,
        rgba(110, 231, 183, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-5-bg {
    background: linear-gradient(to bottom,
        #047857 0%,
        #059669 30%,
        #10b981 50%,
        rgba(16, 185, 129, 0.3) 75%,
        transparent 100%
    );
}

/* Slide 6 - Amber Brown (Yedek Parça/Dayanıklılık) */
.slide-6-bg {
    background: linear-gradient(to bottom,
        #fef3c7 0%,
        #fde68a 30%,
        #fcd34d 50%,
        rgba(252, 211, 77, 0.4) 75%,
        transparent 100%
    );
}
.dark .slide-6-bg {
    background: linear-gradient(to bottom,
        #78350f 0%,
        #92400e 30%,
        #b45309 50%,
        rgba(180, 83, 9, 0.3) 75%,
        transparent 100%
    );
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

<div class="hero-slider-container relative">

    <!-- LINEAR PROGRESS BAR -->
    <div class="absolute top-0 left-0 right-0 z-50 h-1 bg-gray-200 dark:bg-gray-700">
        <div id="linearProgress" class="h-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all" style="width: 0%"></div>
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
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-boxes-stacked text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Güçlü Stok</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Zengin ürün çeşidi</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-certificate text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Garantili Ürün</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
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

            <!-- SLIDE 3: FORKLIFT SATIŞ ODAKLI -->
            <div class="swiper-slide slide-3-bg">
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
                                <a href="/shop/kategori/forklift" class="group bg-orange-600 hover:bg-orange-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-forklift mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Forklift Kataloğu
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-bolt text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Akülü Forklift</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">1.5-3.5 ton kapasite</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-fire text-orange-600 dark:text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Dizel Forklift</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">2.5-10 ton kapasite</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-shield-check text-green-600 dark:text-green-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">2 Yıl Garanti</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Orijinal yedek parça</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
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

            <!-- SLIDE 4: KİRALAMA ODAKLI -->
            <div class="swiper-slide slide-4-bg">
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
                                Forklift, transpalet ve istif makinesi kiralama seçenekleri ile esneklik
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/kiralama" class="group bg-cyan-600 hover:bg-cyan-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-calendar-days mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Kiralama Koşulları
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-clock text-purple-600 dark:text-purple-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Günlük Kiralama</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Anlık ihtiyaçlar</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-infinity text-pink-600 dark:text-pink-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Uzun Dönem</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Özel fiyatlandırma</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-tools text-indigo-600 dark:text-indigo-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Bakım Dahil</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Ek maliyet yok</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
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

            <!-- SLIDE 5: SERVİS & BAKIM -->
            <div class="swiper-slide slide-5-bg">
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
                                <a href="/teknik-servis" class="group bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-wrench mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                                    Servis Talebi
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-calendar-check text-orange-600 dark:text-orange-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Periyodik Bakım</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Düzenli kontrol</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-screwdriver-wrench text-red-600 dark:text-red-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Hızlı Onarım</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">24 saat müdahale</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-cog text-amber-600 dark:text-amber-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Orijinal Parça</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Garantili ürünler</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
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

            <!-- SLIDE 2: TRANSPALETLER -->
            <div class="swiper-slide slide-2-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    TRANSPALET
                                </span>
                                <span class="gradient-animate block py-2">
                                    ÇEŞİTLERİ
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Manuel, akülü ve paslanmaz transpalet modelleri ile her sektöre uygun çözümler
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/shop/kategori/transpalet" class="group bg-slate-600 hover:bg-slate-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-dolly mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Transpalet Kataloğu
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-hand text-slate-600 dark:text-slate-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Manuel Transpalet</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">2.5-5 ton kapasite</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-battery-bolt text-blue-600 dark:text-blue-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Akülü Transpalet</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Elektrikli sistem</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-droplet text-cyan-600 dark:text-cyan-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Paslanmaz Transpalet</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Gıda sektörü için</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-ruler text-orange-600 dark:text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Özel Ölçüler</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Dar koridor modelleri</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                 alt="Transpalet Çeşitleri"
                                 class="w-full h-auto object-contain hero-image"
                                 loading="eager"
                                >
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 6: YEDEK PARÇA -->
            <div class="swiper-slide slide-6-bg">
                <div class="container mx-auto px-4 sm:px-4 md:px-8 lg:px-16 py-8 md:py-12 lg:py-16 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                        <div class="text-gray-900 dark:text-white">
                            <h1 class="animate-title text-5xl md:text-6xl lg:text-7xl font-black mb-6 leading-[1.2]" style="font-weight: 900;">
                                <span class="gradient-animate block py-2">
                                    ORİJİNAL
                                </span>
                                <span class="gradient-animate block py-2">
                                    YEDEK PARÇA
                                </span>
                            </h1>

                            <p class="animate-description text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                                Forklift ve istif makineleri için geniş yedek parça stoğu ile hızlı çözüm
                            </p>

                            <div class="animate-cta mb-16">
                                <a href="/yedek-parca" class="group bg-amber-600 hover:bg-amber-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fa-light fa-gears mr-2 inline-block group-hover:scale-125 transition-all duration-300"></i>
                                    Yedek Parça Kataloğu
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-certificate text-amber-600 dark:text-amber-300 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Orijinal Parça</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Garantili ürünler</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-boxes-stacked text-orange-600 dark:text-orange-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Geniş Stok</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">1000+ parça çeşidi</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-truck-fast text-blue-600 dark:text-blue-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Hızlı Teslimat</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Aynı gün kargo</div>
                                    </div>
                                </div>
                                <div class="feature-item flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/60 dark:bg-white/5 border border-gray-300/30 dark:border-white/10 rounded-full backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                                        <i class="fa-light fa-percent text-green-600 dark:text-green-400 text-xl"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white text-base">Uygun Fiyat</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Rekabetçi fiyatlar</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center">
                            <img src="https://ixtif.com/storage/tenant2/4/hero.png"
                                 alt="Yedek Parça"
                                 class="w-full h-auto object-contain hero-image"
                                 loading="lazy"
                                >
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
    let progressInterval = null;

    // Linear progress bar güncelleme
    function updateLinearProgress() {
        const progressBar = document.getElementById('linearProgress');
        let progress = 0;
        const step = 100 / (5000 / 50); // 5 saniye

        if (progressInterval) clearInterval(progressInterval);

        progressInterval = setInterval(() => {
            progress += step;
            if (progress >= 100) {
                // Yumuşak geçişle sıfırla
                progressBar.style.transition = 'width 0.3s ease';
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.transition = '';
                    progress = 0;
                }, 300);
            } else {
                progressBar.style.width = progress + '%';
            }
        }, 50);
    }

    // Pagination için autoplay delay'i CSS variable olarak set et
    document.documentElement.style.setProperty('--autoplay-delay', '5000ms');

    // Swiper başlatma - SABİT AYARLAR
    const swiper = new Swiper('.heroSwiper', {
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 1000, // 1 saniye
        loop: true,
        autoplay: {
            delay: 5000, // 5 saniye
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
        on: {
            slideChange: function() {
                // Reset linear progress
                document.getElementById('linearProgress').style.width = '0%';
                updateLinearProgress();
            },
            init: function() {
                updateLinearProgress();
            }
        }
    });
});
</script>
@endsection
