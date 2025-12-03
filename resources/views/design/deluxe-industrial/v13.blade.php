@extends('themes.ixtif.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
.hero-section {
    height: 90vh;
    min-height: 700px;
}

.swiper-slide {
    opacity: 0 !important;
    transition: opacity 1000ms ease-in-out;
}

.swiper-slide-active {
    opacity: 1 !important;
}

.product-grid-item {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}

.product-grid-item:nth-child(1) { animation-delay: 0.2s; }
.product-grid-item:nth-child(2) { animation-delay: 0.3s; }
.product-grid-item:nth-child(3) { animation-delay: 0.4s; }
.product-grid-item:nth-child(4) { animation-delay: 0.5s; }
.product-grid-item:nth-child(5) { animation-delay: 0.6s; }
.product-grid-item:nth-child(6) { animation-delay: 0.7s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-number {
    font-size: 4rem;
    font-weight: 900;
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: countUp 2s ease-out;
}

@keyframes countUp {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
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
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
}
</style>

<!-- KURUMSAL GÜÇ SHOWCASE SLIDER -->
<section class="relative hero-section bg-gradient-to-br from-gray-900 via-gray-800 to-black overflow-hidden">
    <div class="swiper powerSwiper h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 - Forklift Filosu -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="w-full">
                        <!-- Header -->
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                                <i class="fas fa-industry text-cyan-400 text-2xl"></i>
                                <span class="text-white font-bold text-lg">ELEKTRİKLİ FORKLIFT FİLOMUZ</span>
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white mb-4">
                                Akülü Forklift Çözümleri
                            </h1>
                            <p class="text-2xl text-cyan-200 font-semibold">Kapalı Alan Operasyonlarında Lider</p>
                        </div>

                        <!-- Product Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-12">
                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-cyan-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Toyota 8FBE</h3>
                                    <p class="text-cyan-200 text-lg mb-1">1.5 - 3.5 Ton</p>
                                    <p class="text-white/70 text-sm">3-5m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-blue-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Linde E16</h3>
                                    <p class="text-cyan-200 text-lg mb-1">1.6 - 2.0 Ton</p>
                                    <p class="text-white/70 text-sm">3-4.5m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-indigo-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Still RX</h3>
                                    <p class="text-cyan-200 text-lg mb-1">2.0 - 3.0 Ton</p>
                                    <p class="text-white/70 text-sm">4-6m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-sky-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Jungheinrich</h3>
                                    <p class="text-cyan-200 text-lg mb-1">1.8 - 2.5 Ton</p>
                                    <p class="text-white/70 text-sm">3-5.5m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-cyan-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Crown FC</h3>
                                    <p class="text-cyan-200 text-lg mb-1">2.0 - 3.5 Ton</p>
                                    <p class="text-white/70 text-sm">3.5-6m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-forklift text-6xl text-blue-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Yale ERP</h3>
                                    <p class="text-cyan-200 text-lg mb-1">1.6 - 2.5 Ton</p>
                                    <p class="text-white/70 text-sm">3-5m Yükseklik</p>
                                </div>
                            </div>
                        </div>

                        <!-- Power Stats -->
                        <div class="grid grid-cols-3 gap-8 text-center">
                            <div>
                                <div class="stat-number">850+</div>
                                <div class="text-white/80 text-lg font-semibold">Aktif Forklift</div>
                            </div>
                            <div>
                                <div class="stat-number">15+</div>
                                <div class="text-white/80 text-lg font-semibold">Marka Çeşitliliği</div>
                            </div>
                            <div>
                                <div class="stat-number">24/7</div>
                                <div class="text-white/80 text-lg font-semibold">Teknik Destek</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 - Dizel Forklift Gücü -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-900 via-red-900 to-rose-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="w-full">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                                <i class="fas fa-fire text-orange-400 text-2xl"></i>
                                <span class="text-white font-bold text-lg">DİZEL FORKLIFT GÜCÜ</span>
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white mb-4">
                                Heavy Duty Çözümler
                            </h1>
                            <p class="text-2xl text-orange-200 font-semibold">Dış Mekan & Zorlu Koşullar</p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-12">
                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-orange-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Mitsubishi FD</h3>
                                    <p class="text-orange-200 text-lg mb-1">2.5 - 5.0 Ton</p>
                                    <p class="text-white/70 text-sm">3-7m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-red-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Komatsu FD</h3>
                                    <p class="text-orange-200 text-lg mb-1">3.0 - 8.0 Ton</p>
                                    <p class="text-white/70 text-sm">4-8m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-rose-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">TCM FD</h3>
                                    <p class="text-orange-200 text-lg mb-1">2.0 - 4.5 Ton</p>
                                    <p class="text-white/70 text-sm">3-6m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-amber-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Hyster H</h3>
                                    <p class="text-orange-200 text-lg mb-1">3.5 - 7.0 Ton</p>
                                    <p class="text-white/70 text-sm">4-8m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-orange-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Nissan FD</h3>
                                    <p class="text-orange-200 text-lg mb-1">2.5 - 5.0 Ton</p>
                                    <p class="text-white/70 text-sm">3-7m Yükseklik</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-industry text-6xl text-red-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Heli CPCD</h3>
                                    <p class="text-orange-200 text-lg mb-1">3.0 - 10 Ton</p>
                                    <p class="text-white/70 text-sm">3-8m Yükseklik</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-8 text-center">
                            <div>
                                <div class="stat-number">620+</div>
                                <div class="text-white/80 text-lg font-semibold">Dizel Filo</div>
                            </div>
                            <div>
                                <div class="stat-number">10+</div>
                                <div class="text-white/80 text-lg font-semibold">Ton Kapasiteye Kadar</div>
                            </div>
                            <div>
                                <div class="stat-number">%98</div>
                                <div class="text-white/80 text-lg font-semibold">Müşteri Memnuniyeti</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 - Transpalet & İstif -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-violet-900 to-fuchsia-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="w-full">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                                <i class="fas fa-boxes-stacked text-purple-400 text-2xl"></i>
                                <span class="text-white font-bold text-lg">TRANSPALET & İSTİF MAKİNELERİ</span>
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white mb-4">
                                Depo Operasyon Çözümleri
                            </h1>
                            <p class="text-2xl text-purple-200 font-semibold">Verimlilik & Hız</p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-12">
                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-pallet text-6xl text-purple-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Crown WP</h3>
                                    <p class="text-purple-200 text-lg mb-1">2.0 - 2.5 Ton</p>
                                    <p class="text-white/70 text-sm">Akülü Transpalet</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-boxes-stacked text-6xl text-violet-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">BT SWE</h3>
                                    <p class="text-purple-200 text-lg mb-1">1.2 - 1.6 Ton</p>
                                    <p class="text-white/70 text-sm">İstif Makinesi</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-pallet text-6xl text-fuchsia-400 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Linde T16</h3>
                                    <p class="text-purple-200 text-lg mb-1">1.6 - 2.0 Ton</p>
                                    <p class="text-white/70 text-sm">Elektrikli Transpalet</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-boxes-stacked text-6xl text-purple-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Still EXV</h3>
                                    <p class="text-purple-200 text-lg mb-1">1.0 - 1.4 Ton</p>
                                    <p class="text-white/70 text-sm">Kompakt İstif</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-pallet text-6xl text-violet-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Jungheinrich</h3>
                                    <p class="text-purple-200 text-lg mb-1">2.0 - 2.5 Ton</p>
                                    <p class="text-white/70 text-sm">Manuel & Elektrikli</p>
                                </div>
                            </div>

                            <div class="product-grid-item bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <div class="text-center">
                                    <i class="fas fa-boxes-stacked text-6xl text-fuchsia-300 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-white mb-2">Yale MPE</h3>
                                    <p class="text-purple-200 text-lg mb-1">1.5 - 2.0 Ton</p>
                                    <p class="text-white/70 text-sm">Çok Yönlü İstif</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-8 text-center">
                            <div>
                                <div class="stat-number">1200+</div>
                                <div class="text-white/80 text-lg font-semibold">Transpalet Filosu</div>
                            </div>
                            <div>
                                <div class="stat-number">20+</div>
                                <div class="text-white/80 text-lg font-semibold">Yıllık Deneyim</div>
                            </div>
                            <div>
                                <div class="stat-number">500+</div>
                                <div class="text-white/80 text-lg font-semibold">Kurumsal Müşteri</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 4 - Kurumsal Güç -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-teal-900 to-cyan-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 h-full flex items-center relative z-10">
                    <div class="w-full">
                        <div class="text-center mb-16">
                            <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                                <i class="fas fa-trophy text-emerald-400 text-2xl"></i>
                                <span class="text-white font-bold text-lg">TÜRKİYE'NİN LİDER FİRMASI</span>
                            </div>
                            <h1 class="text-6xl lg:text-7xl font-black text-white mb-8">
                                Güvenilir İş Ortağınız
                            </h1>
                            <p class="text-2xl text-emerald-200 font-semibold mb-12">Endüstriyel Ekipman Çözümlerinde 20 Yıllık Liderlik</p>
                        </div>

                        <!-- Power Grid -->
                        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                            <div class="product-grid-item bg-gradient-to-br from-emerald-600/20 to-teal-600/20 backdrop-blur-xl rounded-2xl p-8 border border-white/30 text-center shadow-2xl">
                                <div class="text-7xl font-black text-white mb-3">2.670+</div>
                                <div class="text-emerald-200 text-xl font-bold mb-2">Toplam Ekipman</div>
                                <div class="text-white/70">Türkiye'nin En Büyük Filosu</div>
                            </div>

                            <div class="product-grid-item bg-gradient-to-br from-teal-600/20 to-cyan-600/20 backdrop-blur-xl rounded-2xl p-8 border border-white/30 text-center shadow-2xl">
                                <div class="text-7xl font-black text-white mb-3">500+</div>
                                <div class="text-teal-200 text-xl font-bold mb-2">Kurumsal Müşteri</div>
                                <div class="text-white/70">Fortune 500 Firmaları</div>
                            </div>

                            <div class="product-grid-item bg-gradient-to-br from-cyan-600/20 to-blue-600/20 backdrop-blur-xl rounded-2xl p-8 border border-white/30 text-center shadow-2xl">
                                <div class="text-7xl font-black text-white mb-3">20</div>
                                <div class="text-cyan-200 text-xl font-bold mb-2">Yıllık Deneyim</div>
                                <div class="text-white/70">Sektörde Öncü</div>
                            </div>

                            <div class="product-grid-item bg-gradient-to-br from-blue-600/20 to-indigo-600/20 backdrop-blur-xl rounded-2xl p-8 border border-white/30 text-center shadow-2xl">
                                <div class="text-7xl font-black text-white mb-3">24/7</div>
                                <div class="text-blue-200 text-xl font-bold mb-2">Teknik Destek</div>
                                <div class="text-white/70">7 Gün 24 Saat</div>
                            </div>
                        </div>

                        <!-- CTA -->
                        <div class="flex justify-center gap-6 flex-wrap">
                            <button class="px-12 py-6 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-black text-xl hover:scale-105 transition-all shadow-2xl hover:shadow-emerald-500/50 border-2 border-white/20">
                                <i class="fas fa-file-pdf mr-3"></i>Katalog İndirin
                            </button>
                            <button class="px-12 py-6 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-xl hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <i class="fas fa-phone mr-3"></i>İletişime Geçin
                            </button>
                            <button class="px-12 py-6 bg-white/10 backdrop-blur-xl border-2 border-white/30 text-white rounded-2xl font-bold text-xl hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                                <i class="fas fa-calendar mr-3"></i>Randevu Alın
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="swiper-pagination !bottom-8"></div>

        <!-- Navigation -->
        <div class="swiper-button-prev !text-white !w-14 !h-14 !bg-white/10 !backdrop-blur-xl !rounded-full !border !border-white/20 hover:!bg-white/20 !transition-all after:!text-xl !left-8"></div>
        <div class="swiper-button-next !text-white !w-14 !h-14 !bg-white/10 !backdrop-blur-xl !rounded-full !border !border-white/20 hover:!bg-white/20 !transition-all after:!text-xl !right-8"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.powerSwiper', {
        speed: 1000,
        autoplay: {
            delay: 6000,
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
