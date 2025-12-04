@extends('themes.ixtif.layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.service-hub-container {
    height: 100vh;
    width: 100%;
    overflow: hidden;
}

.swiper-slide {
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.service-content {
    animation: fadeInUp 0.8s ease-out forwards;
    opacity: 0;
}

.swiper-slide-active .service-content {
    animation: fadeInUp 0.8s ease-out forwards;
}

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

.service-card {
    opacity: 0;
    animation: slideIn 0.6s ease-out forwards;
}

.service-card:nth-child(1) { animation-delay: 0.2s; }
.service-card:nth-child(2) { animation-delay: 0.3s; }
.service-card:nth-child(3) { animation-delay: 0.4s; }
.service-card:nth-child(4) { animation-delay: 0.5s; }

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.stat-number {
    font-size: 4rem;
    font-weight: 900;
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: countUp 1.5s ease-out;
}

@keyframes countUp {
    from { opacity: 0; transform: scale(0.5); }
    to { opacity: 1; transform: scale(1); }
}

.swiper-pagination-bullet {
    width: 8px;
    height: 8px;
    background: white;
    opacity: 0.3;
    margin: 0 4px !important;
}

.swiper-pagination-bullet-active {
    width: 30px;
    border-radius: 4px;
    opacity: 1;
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
}

.swiper-button-prev,
.swiper-button-next {
    color: white !important;
    width: 60px !important;
    height: 60px !important;
    background: rgba(255, 255, 255, 0.1) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 50% !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    transition: all 0.3s !important;
}

.swiper-button-prev:hover,
.swiper-button-next:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    transform: scale(1.1);
}

.swiper-button-prev::after,
.swiper-button-next::after {
    font-size: 24px !important;
}

.service-nav {
    position: fixed;
    top: 50%;
    right: 40px;
    transform: translateY(-50%);
    z-index: 50;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.service-nav-item {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s;
}

.service-nav-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(-5px);
}

.service-nav-item.active {
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    border-color: #60a5fa;
    transform: translateX(-5px);
}

@media (max-width: 768px) {
    .service-nav {
        right: 20px;
        gap: 8px;
    }

    .service-nav-item {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>

<div class="service-hub-container">
    <div class="swiper serviceHubSwiper">
        <div class="swiper-wrapper">

            <!-- SLIDE 1: KİRALAMA HUB -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-cyan-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-handshake text-cyan-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 1/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Kiralama Hub
                        </h1>
                        <p class="text-2xl lg:text-3xl text-cyan-200 font-semibold">Esnek Kiralama Çözümleri</p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 max-w-7xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                            <i class="fas fa-calendar-day text-6xl text-cyan-400 mb-4"></i>
                            <h3 class="text-2xl font-bold text-white mb-3">Günlük Kiralama</h3>
                            <p class="text-cyan-200 mb-4">Anlık ihtiyaçlar için hızlı teslimat</p>
                            <button class="w-full px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-xl font-bold hover:scale-105 transition-all">
                                Teklif Al
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                            <i class="fas fa-calendar-week text-6xl text-blue-400 mb-4"></i>
                            <h3 class="text-2xl font-bold text-white mb-3">Haftalık Paket</h3>
                            <p class="text-cyan-200 mb-4">%15 indirimli fiyat avantajı</p>
                            <button class="w-full px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold hover:scale-105 transition-all">
                                Teklif Al
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                            <i class="fas fa-calendar-alt text-6xl text-indigo-400 mb-4"></i>
                            <h3 class="text-2xl font-bold text-white mb-3">Aylık Kiralama</h3>
                            <p class="text-cyan-200 mb-4">%25 indirimli uzun dönem</p>
                            <button class="w-full px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold hover:scale-105 transition-all">
                                Teklif Al
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all hover:scale-105 shadow-xl">
                            <i class="fas fa-file-contract text-6xl text-purple-400 mb-4"></i>
                            <h3 class="text-2xl font-bold text-white mb-3">Operasyonel</h3>
                            <p class="text-cyan-200 mb-4">Bakım, onarım, sigorta dahil</p>
                            <button class="w-full px-4 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl font-bold hover:scale-105 transition-all">
                                Teklif Al
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
                        <div>
                            <div class="stat-number">2.670+</div>
                            <div class="text-white/80 text-lg font-semibold">Kiralık Ekipman</div>
                        </div>
                        <div>
                            <div class="stat-number">24/7</div>
                            <div class="text-white/80 text-lg font-semibold">Hızlı Teslimat</div>
                        </div>
                        <div>
                            <div class="stat-number">%100</div>
                            <div class="text-white/80 text-lg font-semibold">Servis Garantisi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 2: SERVİS & BAKIM -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-900 via-red-900 to-rose-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-tools text-orange-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 2/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Servis & Bakım
                        </h1>
                        <p class="text-2xl lg:text-3xl text-orange-200 font-semibold">Profesyonel Teknik Destek</p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-gradient-to-br from-orange-600/20 to-red-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-calendar-check text-6xl text-orange-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">Periyodik Bakım</h3>
                            <p class="text-orange-200 leading-relaxed mb-6">Düzenli kontroller ile arıza önleme ve uzun ömür</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-orange-400"></i>
                                    <span>Aylık kontrol programı</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-orange-400"></i>
                                    <span>Orijinal yedek parça</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-orange-400"></i>
                                    <span>Uzman teknisyen ekibi</span>
                                </li>
                            </ul>
                        </div>

                        <div class="service-card bg-gradient-to-br from-red-600/20 to-rose-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-wrench text-6xl text-red-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">Onarım Hizmeti</h3>
                            <p class="text-orange-200 leading-relaxed mb-6">Hızlı müdahale ile minimum duruş süresi</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-red-400"></i>
                                    <span>24 saat içinde müdahale</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-red-400"></i>
                                    <span>Yerinde onarım servisi</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-red-400"></i>
                                    <span>Garanti sertifikası</span>
                                </li>
                            </ul>
                        </div>

                        <div class="service-card bg-gradient-to-br from-rose-600/20 to-pink-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-shield-alt text-6xl text-rose-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">Bakım Sözleşmesi</h3>
                            <p class="text-orange-200 leading-relaxed mb-6">Yıllık paket ile maliyet avantajı</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-rose-400"></i>
                                    <span>%30'a varan indirim</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-rose-400"></i>
                                    <span>Öncelikli müdahale</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-rose-400"></i>
                                    <span>Yedek ekipman garantisi</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-8 text-center max-w-4xl mx-auto">
                        <div>
                            <div class="stat-number">15.000+</div>
                            <div class="text-white/80 text-lg font-semibold">Yıllık Servis</div>
                        </div>
                        <div>
                            <div class="stat-number">2 Saat</div>
                            <div class="text-white/80 text-lg font-semibold">Ortalama Müdahale</div>
                        </div>
                        <div>
                            <div class="stat-number">%98</div>
                            <div class="text-white/80 text-lg font-semibold">İlk Çözüm Oranı</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 3: DANIŞMANLIK & ÇÖZÜM -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-900 via-violet-900 to-fuchsia-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-lightbulb text-purple-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 3/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Danışmanlık & Çözüm
                        </h1>
                        <p class="text-2xl lg:text-3xl text-purple-200 font-semibold">İhtiyaca Özel Stratejik Planlama</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl">
                            <div class="flex items-start gap-6 mb-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-warehouse text-4xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-white mb-3">Depo Optimizasyonu</h3>
                                    <p class="text-purple-200 text-lg">Depo layout tasarımı ve iş akışı analizi</p>
                                </div>
                            </div>
                            <ul class="space-y-4 text-white/90 text-lg">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-purple-400"></i>
                                    <span>3D depo simülasyonu</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-purple-400"></i>
                                    <span>Ekipman ihtiyaç analizi</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-purple-400"></i>
                                    <span>Kapasite artırım planlaması</span>
                                </li>
                            </ul>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl">
                            <div class="flex items-start gap-6 mb-8">
                                <div class="w-20 h-20 bg-gradient-to-br from-violet-500 to-fuchsia-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-chart-line text-4xl text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-3xl font-bold text-white mb-3">Operasyon İyileştirme</h3>
                                    <p class="text-purple-200 text-lg">Verimlilik artırma ve maliyet düşürme</p>
                                </div>
                            </div>
                            <ul class="space-y-4 text-white/90 text-lg">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-violet-400"></i>
                                    <span>Zaman ve hareket etüdü</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-violet-400"></i>
                                    <span>Süreç optimizasyonu</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check-double text-violet-400"></i>
                                    <span>ROI analizi ve raporlama</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="px-16 py-6 bg-gradient-to-r from-purple-500 to-fuchsia-600 text-white rounded-2xl font-black text-2xl hover:scale-105 transition-all shadow-2xl">
                            <i class="fas fa-calendar-check mr-3"></i>Ücretsiz Analiz Talep Et
                        </button>
                    </div>
                </div>
            </div>

            <!-- SLIDE 4: FİLO YÖNETİMİ -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 via-teal-900 to-cyan-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-clipboard-list text-emerald-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 4/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Filo Yönetimi
                        </h1>
                        <p class="text-2xl lg:text-3xl text-emerald-200 font-semibold">Ekipmanlarınızı Akıllı Yönetin</p>
                    </div>

                    <div class="grid md:grid-cols-4 gap-6 mb-12 max-w-7xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all shadow-xl">
                            <i class="fas fa-map-marked-alt text-5xl text-emerald-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Canlı Takip</h3>
                            <p class="text-emerald-200 text-sm">GPS ile gerçek zamanlı konum izleme</p>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all shadow-xl">
                            <i class="fas fa-gas-pump text-5xl text-teal-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Yakıt Yönetimi</h3>
                            <p class="text-emerald-200 text-sm">Tüketim analizi ve maliyet kontrolü</p>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all shadow-xl">
                            <i class="fas fa-bell text-5xl text-cyan-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Bakım Uyarıları</h3>
                            <p class="text-emerald-200 text-sm">Otomatik bakım hatırlatma sistemi</p>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all shadow-xl">
                            <i class="fas fa-chart-pie text-5xl text-blue-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Performans Raporu</h3>
                            <p class="text-emerald-200 text-sm">Detaylı analitik ve raporlama</p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl max-w-5xl mx-auto">
                        <div class="grid md:grid-cols-2 gap-12">
                            <div>
                                <h3 class="text-3xl font-bold text-white mb-6">📱 Mobil Uygulama</h3>
                                <ul class="space-y-4 text-white/90 text-lg">
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-mobile-alt text-emerald-400 text-2xl"></i>
                                        <span>iOS & Android desteği</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-qrcode text-emerald-400 text-2xl"></i>
                                        <span>QR kod ile hızlı erişim</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-comments text-emerald-400 text-2xl"></i>
                                        <span>Anlık bildirimler</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-white mb-6">💻 Web Dashboard</h3>
                                <ul class="space-y-4 text-white/90 text-lg">
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-tachometer-alt text-cyan-400 text-2xl"></i>
                                        <span>Gerçek zamanlı dashboard</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-file-export text-cyan-400 text-2xl"></i>
                                        <span>Excel/PDF rapor indirme</span>
                                    </li>
                                    <li class="flex items-center gap-3">
                                        <i class="fas fa-users-cog text-cyan-400 text-2xl"></i>
                                        <span>Çoklu kullanıcı yönetimi</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 5: 24/7 TEKNİK DESTEK -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900 via-blue-900 to-purple-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-headset text-indigo-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 5/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            24/7 Teknik Destek
                        </h1>
                        <p class="text-2xl lg:text-3xl text-indigo-200 font-semibold">Her An Yanınızdayız</p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-gradient-to-br from-indigo-600/20 to-blue-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl text-center">
                            <div class="text-7xl font-black text-white mb-4">15 dk</div>
                            <h3 class="text-2xl font-bold text-indigo-200 mb-3">Telefon Yanıt Süresi</h3>
                            <p class="text-white/70">Ortalama cevaplama süresi</p>
                        </div>

                        <div class="service-card bg-gradient-to-br from-blue-600/20 to-purple-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl text-center">
                            <div class="text-7xl font-black text-white mb-4">2 Saat</div>
                            <h3 class="text-2xl font-bold text-blue-200 mb-3">Yerinde Müdahale</h3>
                            <p class="text-white/70">Ortalama ulaşma süresi</p>
                        </div>

                        <div class="service-card bg-gradient-to-br from-purple-600/20 to-pink-600/20 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl text-center">
                            <div class="text-7xl font-black text-white mb-4">%96</div>
                            <h3 class="text-2xl font-bold text-purple-200 mb-3">İlk Seferde Çözüm</h3>
                            <p class="text-white/70">Başarı oranımız</p>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl max-w-4xl mx-auto">
                        <h3 class="text-4xl font-black text-white text-center mb-10">İletişim Kanalları</h3>
                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="flex items-center gap-6 bg-white/10 rounded-2xl p-6 border border-white/20">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-phone-alt text-3xl text-white"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-xl mb-1">Telefon Destek</div>
                                    <div class="text-indigo-200">0850 XXX XX XX</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 bg-white/10 rounded-2xl p-6 border border-white/20">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center">
                                    <i class="fab fa-whatsapp text-3xl text-white"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-xl mb-1">WhatsApp Business</div>
                                    <div class="text-indigo-200">Anında cevap</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 bg-white/10 rounded-2xl p-6 border border-white/20">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-envelope text-3xl text-white"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-xl mb-1">E-posta Destek</div>
                                    <div class="text-indigo-200">destek@ixtif.com</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6 bg-white/10 rounded-2xl p-6 border border-white/20">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-comments text-3xl text-white"></i>
                                </div>
                                <div>
                                    <div class="text-white font-bold text-xl mb-1">Canlı Chat</div>
                                    <div class="text-indigo-200">Web sitesi üzerinden</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 6: EĞİTİM & SERTİFİKASYON -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-900 via-yellow-900 to-orange-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-graduation-cap text-amber-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 6/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Eğitim & Sertifikasyon
                        </h1>
                        <p class="text-2xl lg:text-3xl text-amber-200 font-semibold">Profesyonel Operatör Yetiştirin</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-10 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-certificate text-3xl text-white"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-white">Temel Operatör Eğitimi</h3>
                            </div>
                            <ul class="space-y-4 text-white/90 text-lg mb-6">
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-amber-400 text-xl mt-1"></i>
                                    <span>3 gün teorik + pratik eğitim</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-amber-400 text-xl mt-1"></i>
                                    <span>MEB onaylı sertifika</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-amber-400 text-xl mt-1"></i>
                                    <span>SRC belge desteği</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-amber-400 text-xl mt-1"></i>
                                    <span>İş güvenliği eğitimi dahil</span>
                                </li>
                            </ul>
                            <div class="text-amber-200 text-2xl font-bold mb-4">₺2.500 / Kişi</div>
                            <button class="w-full px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-bold text-lg hover:scale-105 transition-all">
                                Kayıt Ol
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-2xl flex items-center justify-center">
                                    <i class="fas fa-award text-3xl text-white"></i>
                                </div>
                                <h3 class="text-3xl font-bold text-white">İleri Seviye Eğitim</h3>
                            </div>
                            <ul class="space-y-4 text-white/90 text-lg mb-6">
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-yellow-400 text-xl mt-1"></i>
                                    <span>5 gün kapsamlı program</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-yellow-400 text-xl mt-1"></i>
                                    <span>Özel ekipman eğitimi</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-yellow-400 text-xl mt-1"></i>
                                    <span>Simülatör uygulaması</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-yellow-400 text-xl mt-1"></i>
                                    <span>Uluslararası sertifika</span>
                                </li>
                            </ul>
                            <div class="text-yellow-200 text-2xl font-bold mb-4">₺4.500 / Kişi</div>
                            <button class="w-full px-6 py-4 bg-gradient-to-r from-yellow-500 to-amber-600 text-white rounded-xl font-bold text-lg hover:scale-105 transition-all">
                                Kayıt Ol
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-6 max-w-6xl mx-auto">
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-black text-white mb-2">5.000+</div>
                            <div class="text-amber-200 font-semibold">Mezun Operatör</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-black text-white mb-2">350+</div>
                            <div class="text-amber-200 font-semibold">Kurumsal Eğitim</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-black text-white mb-2">%98</div>
                            <div class="text-amber-200 font-semibold">Başarı Oranı</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-black text-white mb-2">15 Yıl</div>
                            <div class="text-amber-200 font-semibold">Eğitim Deneyimi</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 7: LOJİSTİK ÇÖZÜMLERİ -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-gray-900 to-zinc-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-truck-loading text-slate-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 7/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Lojistik Çözümleri
                        </h1>
                        <p class="text-2xl lg:text-3xl text-slate-200 font-semibold">Eksiksiz Tedarik Zinciri Yönetimi</p>
                    </div>

                    <div class="grid md:grid-cols-4 gap-6 mb-12 max-w-7xl mx-auto">
                        <div class="service-card bg-gradient-to-br from-slate-700/30 to-gray-800/30 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-xl">
                            <i class="fas fa-shipping-fast text-5xl text-blue-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Hızlı Teslimat</h3>
                            <p class="text-slate-200 text-sm mb-4">24 saat içinde ekipman teslimi</p>
                            <div class="text-blue-300 font-bold">Türkiye Geneli</div>
                        </div>

                        <div class="service-card bg-gradient-to-br from-gray-700/30 to-zinc-800/30 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-xl">
                            <i class="fas fa-warehouse text-5xl text-emerald-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Depolama</h3>
                            <p class="text-slate-200 text-sm mb-4">50.000 m² kapalı alan</p>
                            <div class="text-emerald-300 font-bold">Güvenli Saklama</div>
                        </div>

                        <div class="service-card bg-gradient-to-br from-zinc-700/30 to-slate-800/30 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-xl">
                            <i class="fas fa-box-open text-5xl text-purple-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Paketleme</h3>
                            <p class="text-slate-200 text-sm mb-4">Özel ambalaj çözümleri</p>
                            <div class="text-purple-300 font-bold">İhracat Uygun</div>
                        </div>

                        <div class="service-card bg-gradient-to-br from-slate-700/30 to-gray-800/30 backdrop-blur-xl rounded-2xl p-8 border border-white/20 shadow-xl">
                            <i class="fas fa-globe text-5xl text-cyan-400 mb-4"></i>
                            <h3 class="text-xl font-bold text-white mb-3">Uluslararası</h3>
                            <p class="text-slate-200 text-sm mb-4">Gümrük ve evrak desteği</p>
                            <div class="text-cyan-300 font-bold">Dünya Geneli</div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl max-w-5xl mx-auto">
                        <h3 class="text-4xl font-black text-white text-center mb-10">Lojistik Süreç</h3>
                        <div class="grid md:grid-cols-5 gap-4">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-phone text-3xl text-white"></i>
                                </div>
                                <h4 class="text-white font-bold mb-2">1. Sipariş</h4>
                                <p class="text-slate-300 text-sm">Talebinizi iletin</p>
                            </div>

                            <div class="flex items-center justify-center">
                                <i class="fas fa-arrow-right text-3xl text-white/30"></i>
                            </div>

                            <div class="text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clipboard-check text-3xl text-white"></i>
                                </div>
                                <h4 class="text-white font-bold mb-2">2. Hazırlık</h4>
                                <p class="text-slate-300 text-sm">Ekipman kontrolü</p>
                            </div>

                            <div class="flex items-center justify-center">
                                <i class="fas fa-arrow-right text-3xl text-white/30"></i>
                            </div>

                            <div class="text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-truck text-3xl text-white"></i>
                                </div>
                                <h4 class="text-white font-bold mb-2">3. Teslimat</h4>
                                <p class="text-slate-300 text-sm">Hızlı nakil</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 8: YEDEK PARÇA HUB -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-rose-900 via-pink-900 to-fuchsia-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-cog text-rose-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 8/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Yedek Parça Hub
                        </h1>
                        <p class="text-2xl lg:text-3xl text-rose-200 font-semibold">Orijinal & Garantili Parçalar</p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-warehouse text-6xl text-rose-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">50.000+ Parça Stoğu</h3>
                            <p class="text-rose-200 leading-relaxed mb-6">Tüm marka ve modeller için geniş yedek parça envanteri</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-rose-400"></i>
                                    <span>Orijinal parça garantisi</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-rose-400"></i>
                                    <span>1 yıl garanti</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-rose-400"></i>
                                    <span>Hızlı teslimat</span>
                                </li>
                            </ul>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-search text-6xl text-pink-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">Akıllı Parça Bulucu</h3>
                            <p class="text-rose-200 leading-relaxed mb-6">Model ve seri numarasıyla anında parça bulun</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-pink-400"></i>
                                    <span>Online katalog</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-pink-400"></i>
                                    <span>3D şema desteği</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-pink-400"></i>
                                    <span>Fiyat karşılaştırma</span>
                                </li>
                            </ul>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <i class="fas fa-truck-fast text-6xl text-fuchsia-400 mb-6"></i>
                            <h3 class="text-3xl font-bold text-white mb-4">Acil Tedarik</h3>
                            <p class="text-rose-200 leading-relaxed mb-6">Kritik parçalar için express servis</p>
                            <ul class="space-y-3 text-white/90">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-fuchsia-400"></i>
                                    <span>4 saat içinde teslimat</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-fuchsia-400"></i>
                                    <span>7/24 sipariş hattı</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-check text-fuchsia-400"></i>
                                    <span>Ülke geneli kargo</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="px-16 py-6 bg-gradient-to-r from-rose-500 to-pink-600 text-white rounded-2xl font-black text-2xl hover:scale-105 transition-all shadow-2xl">
                            <i class="fas fa-search mr-3"></i>Parça Ara
                        </button>
                    </div>
                </div>
            </div>

            <!-- SLIDE 9: OPERASYON OPTİMİZASYONU -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-lime-900 via-green-900 to-emerald-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-chart-line text-lime-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 9/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Operasyon Optimizasyonu
                        </h1>
                        <p class="text-2xl lg:text-3xl text-lime-200 font-semibold">Verimliliği Maksimize Edin</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 mb-12 max-w-6xl mx-auto">
                        <div class="service-card bg-gradient-to-br from-red-600/20 to-orange-600/20 backdrop-blur-xl rounded-3xl p-10 border-2 border-red-500/50 shadow-2xl">
                            <div class="text-center mb-8">
                                <div class="inline-block px-6 py-3 bg-red-500/30 rounded-full text-red-200 font-bold text-lg mb-4">
                                    MEVCUT DURUM
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="flex items-center gap-4 bg-red-500/10 rounded-2xl p-6">
                                    <i class="fas fa-times-circle text-4xl text-red-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%40 Boş Geçen Süre</div>
                                        <div class="text-red-200">Verimsiz rota planlaması</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-red-500/10 rounded-2xl p-6">
                                    <i class="fas fa-times-circle text-4xl text-red-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%35 Fazla Yakıt</div>
                                        <div class="text-red-200">Gereksiz hareketler</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-red-500/10 rounded-2xl p-6">
                                    <i class="fas fa-times-circle text-4xl text-red-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%25 Düşük Kapasite</div>
                                        <div class="text-red-200">Yanlış ekipman seçimi</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="service-card bg-gradient-to-br from-green-600/20 to-emerald-600/20 backdrop-blur-xl rounded-3xl p-10 border-2 border-green-500/50 shadow-2xl">
                            <div class="text-center mb-8">
                                <div class="inline-block px-6 py-3 bg-green-500/30 rounded-full text-green-200 font-bold text-lg mb-4">
                                    OPTİMİZE SONRASI
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="flex items-center gap-4 bg-green-500/10 rounded-2xl p-6">
                                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%90 Verimlilik</div>
                                        <div class="text-green-200">Akıllı rota optimizasyonu</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-green-500/10 rounded-2xl p-6">
                                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%40 Yakıt Tasarrufu</div>
                                        <div class="text-green-200">Minimum hareket planı</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 bg-green-500/10 rounded-2xl p-6">
                                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                                    <div>
                                        <div class="text-white font-bold text-xl">%60 Kapasite Artışı</div>
                                        <div class="text-green-200">Doğru ekipman analizi</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-6 max-w-6xl mx-auto">
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <i class="fas fa-clock text-4xl text-lime-400 mb-3"></i>
                            <div class="text-3xl font-black text-white mb-2">%50</div>
                            <div class="text-lime-200 font-semibold">Zaman Tasarrufu</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <i class="fas fa-coins text-4xl text-green-400 mb-3"></i>
                            <div class="text-3xl font-black text-white mb-2">%45</div>
                            <div class="text-green-200 font-semibold">Maliyet Düşüşü</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <i class="fas fa-chart-bar text-4xl text-emerald-400 mb-3"></i>
                            <div class="text-3xl font-black text-white mb-2">%70</div>
                            <div class="text-emerald-200 font-semibold">Verim Artışı</div>
                        </div>
                        <div class="service-card text-center bg-white/10 backdrop-blur-xl rounded-2xl p-6 border border-white/20">
                            <i class="fas fa-leaf text-4xl text-teal-400 mb-3"></i>
                            <div class="text-3xl font-black text-white mb-2">%60</div>
                            <div class="text-teal-200 font-semibold">CO₂ Azaltma</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 10: FİNANSAL ÇÖZÜMLERs -->
            <div class="swiper-slide">
                <div class="absolute inset-0 bg-gradient-to-br from-sky-900 via-blue-900 to-indigo-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-30"></div>
                </div>

                <div class="container mx-auto px-8 lg:px-16 relative z-10 service-content">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20 mb-6 shadow-2xl">
                            <i class="fas fa-hand-holding-usd text-sky-400 text-2xl"></i>
                            <span class="text-white font-bold text-lg">HİZMET 10/10</span>
                        </div>
                        <h1 class="text-6xl lg:text-8xl font-black text-white mb-4">
                            Finansal Çözümler
                        </h1>
                        <p class="text-2xl lg:text-3xl text-sky-200 font-semibold">Esnek Ödeme & Finansman</p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 mb-12 max-w-7xl mx-auto">
                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <div class="text-center mb-6">
                                <div class="inline-block px-6 py-3 bg-gradient-to-r from-sky-500 to-blue-600 rounded-full text-white font-bold text-lg mb-4">
                                    LEASING
                                </div>
                            </div>
                            <div class="text-center mb-6">
                                <div class="text-5xl font-black text-white mb-2">36-60 Ay</div>
                                <p class="text-sky-200">Finansal Kiralama</p>
                            </div>
                            <ul class="space-y-4 text-white/90 mb-6">
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-percent text-sky-400 text-xl mt-1"></i>
                                    <span>%10-20 peşinat</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-percent text-sky-400 text-xl mt-1"></i>
                                    <span>Sabit aylık taksit</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-percent text-sky-400 text-xl mt-1"></i>
                                    <span>Sözleşme sonu devir</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-percent text-sky-400 text-xl mt-1"></i>
                                    <span>Vergi avantajı</span>
                                </li>
                            </ul>
                            <button class="w-full px-6 py-4 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl font-bold text-lg hover:scale-105 transition-all">
                                Hesapla
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <div class="text-center mb-6">
                                <div class="inline-block px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full text-white font-bold text-lg mb-4">
                                    KREDİ
                                </div>
                            </div>
                            <div class="text-center mb-6">
                                <div class="text-5xl font-black text-white mb-2">12-48 Ay</div>
                                <p class="text-blue-200">Taşıt Kredisi</p>
                            </div>
                            <ul class="space-y-4 text-white/90 mb-6">
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-blue-400 text-xl mt-1"></i>
                                    <span>%5-30 peşinat</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-blue-400 text-xl mt-1"></i>
                                    <span>Hızlı onay süreci</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-blue-400 text-xl mt-1"></i>
                                    <span>Rekabetçi faiz</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-blue-400 text-xl mt-1"></i>
                                    <span>Anında sahiplik</span>
                                </li>
                            </ul>
                            <button class="w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-bold text-lg hover:scale-105 transition-all">
                                Başvur
                            </button>
                        </div>

                        <div class="service-card bg-white/10 backdrop-blur-xl rounded-3xl p-10 border border-white/30 shadow-2xl">
                            <div class="text-center mb-6">
                                <div class="inline-block px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full text-white font-bold text-lg mb-4">
                                    TAKSİTLİ
                                </div>
                            </div>
                            <div class="text-center mb-6">
                                <div class="text-5xl font-black text-white mb-2">3-12 Ay</div>
                                <p class="text-indigo-200">Kredi Kartı</p>
                            </div>
                            <ul class="space-y-4 text-white/90 mb-6">
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-credit-card text-indigo-400 text-xl mt-1"></i>
                                    <span>Peşinatsız</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-credit-card text-indigo-400 text-xl mt-1"></i>
                                    <span>Tüm bankalar</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-credit-card text-indigo-400 text-xl mt-1"></i>
                                    <span>Online işlem</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-credit-card text-indigo-400 text-xl mt-1"></i>
                                    <span>Anında onay</span>
                                </li>
                            </ul>
                            <button class="w-full px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-bold text-lg hover:scale-105 transition-all">
                                Satın Al
                            </button>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-sky-600/20 to-indigo-600/20 backdrop-blur-xl rounded-3xl p-12 border border-white/30 shadow-2xl max-w-5xl mx-auto">
                        <div class="grid md:grid-cols-4 gap-8 text-center">
                            <div>
                                <i class="fas fa-bolt text-5xl text-yellow-400 mb-4"></i>
                                <div class="text-3xl font-black text-white mb-2">24 Saat</div>
                                <div class="text-sky-200">Hızlı Onay</div>
                            </div>
                            <div>
                                <i class="fas fa-shield-alt text-5xl text-emerald-400 mb-4"></i>
                                <div class="text-3xl font-black text-white mb-2">%100</div>
                                <div class="text-sky-200">Güvenli İşlem</div>
                            </div>
                            <div>
                                <i class="fas fa-chart-line text-5xl text-cyan-400 mb-4"></i>
                                <div class="text-3xl font-black text-white mb-2">%95</div>
                                <div class="text-sky-200">Onay Oranı</div>
                            </div>
                            <div>
                                <i class="fas fa-handshake text-5xl text-purple-400 mb-4"></i>
                                <div class="text-3xl font-black text-white mb-2">1.500+</div>
                                <div class="text-sky-200">Mutlu Müşteri</div>
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
        <div class="swiper-pagination"></div>
    </div>

    <!-- Service Navigation (Right Side) -->
    <div class="service-nav">
        <div class="service-nav-item" data-slide="0" title="Kiralama Hub">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="service-nav-item" data-slide="1" title="Servis & Bakım">
            <i class="fas fa-tools"></i>
        </div>
        <div class="service-nav-item" data-slide="2" title="Danışmanlık">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="service-nav-item" data-slide="3" title="Filo Yönetimi">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="service-nav-item" data-slide="4" title="24/7 Destek">
            <i class="fas fa-headset"></i>
        </div>
        <div class="service-nav-item" data-slide="5" title="Eğitim">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="service-nav-item" data-slide="6" title="Lojistik">
            <i class="fas fa-truck-loading"></i>
        </div>
        <div class="service-nav-item" data-slide="7" title="Yedek Parça">
            <i class="fas fa-cog"></i>
        </div>
        <div class="service-nav-item" data-slide="8" title="Optimizasyon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="service-nav-item" data-slide="9" title="Finansman">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const swiper = new Swiper('.serviceHubSwiper', {
        direction: 'horizontal',
        speed: 800,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        mousewheel: true,
        keyboard: {
            enabled: true,
        },
        on: {
            slideChange: function() {
                // Update service nav active state
                document.querySelectorAll('.service-nav-item').forEach((item, index) => {
                    if (index === this.activeIndex) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }
        }
    });

    // Service nav click handlers
    document.querySelectorAll('.service-nav-item').forEach((item, index) => {
        item.addEventListener('click', function() {
            const slideIndex = parseInt(this.dataset.slide);
            swiper.slideTo(slideIndex);
        });
    });

    // Set initial active state
    document.querySelector('.service-nav-item').classList.add('active');
});
</script>
@endsection
