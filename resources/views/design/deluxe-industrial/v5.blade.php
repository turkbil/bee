@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V1</a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V2</a>
    <a href="{{ route('design.deluxe-industrial.v3') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V3</a>
    <a href="{{ route('design.deluxe-industrial.v4') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V4</a>
    <a href="{{ route('design.deluxe-industrial.v5') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">V5</a>
</div>

<style>
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.animate-slide-down { animation: slideDown 0.8s ease-out forwards; }
.animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
</style>

<!-- FULL-WIDTH HERO BANNER - Industrial B2B -->
<section class="relative py-24 lg:py-32 bg-gradient-to-r from-slate-900 via-gray-900 to-slate-900 dark:from-black dark:via-gray-950 dark:to-black overflow-hidden">
    <!-- Industrial Grid Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    </div>

    <!-- Accent Lines -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-yellow-500 to-transparent"></div>
    <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-yellow-500 to-transparent"></div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Industry Badge -->
        <div class="text-center mb-8 animate-slide-down delay-100">
            <span class="inline-block px-6 py-2 bg-yellow-500 text-black rounded text-sm font-black uppercase tracking-wider">
                Industrial Solutions
            </span>
        </div>

        <!-- Main Headline -->
        <div class="max-w-6xl mx-auto text-center mb-12 animate-slide-down delay-200">
            <h1 class="text-5xl lg:text-7xl xl:text-8xl font-black text-white leading-[1.1] mb-8">
                ENDÜSTRİYEL GÜÇ<br/>
                <span class="text-yellow-500">İŞİNİZE HİZMET EDER</span>
            </h1>
            
            <p class="text-2xl lg:text-3xl text-gray-300 font-bold mb-4">
                Forklift • Transpalet • İstif Makinesi • Depo Ekipmanı
            </p>
            
            <p class="text-lg text-gray-400 max-w-3xl mx-auto">
                Türkiye'nin en güvenilir endüstriyel ekipman tedarikçisi. 25 yıllık deneyim, 2.000+ kurumsal müşteri, 7/24 teknik destek.
            </p>
        </div>

        <!-- MEGA CTA Section -->
        <div class="grid lg:grid-cols-3 gap-6 max-w-6xl mx-auto mb-16 animate-slide-down delay-300">
            <!-- CTA 1 - Primary -->
            <button class="group bg-yellow-500 hover:bg-yellow-400 text-black px-10 py-8 rounded-2xl font-black text-xl transition-all hover:scale-105 shadow-2xl">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <div class="text-sm font-bold opacity-80 mb-1">HEMEN BAŞLA</div>
                        <div class="text-2xl">Teklif Al</div>
                    </div>
                    <i class="fas fa-arrow-right text-4xl group-hover:translate-x-2 transition-transform"></i>
                </div>
            </button>

            <!-- CTA 2 - Secondary -->
            <button class="group bg-white/10 hover:bg-white/20 backdrop-blur-sm border-2 border-white/30 text-white px-10 py-8 rounded-2xl font-bold text-xl transition-all hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <div class="text-sm opacity-70 mb-1">STOK GÖRÜNTÜLE</div>
                        <div class="text-2xl">Ürünler</div>
                    </div>
                    <i class="fas fa-boxes text-3xl group-hover:rotate-12 transition-transform"></i>
                </div>
            </button>

            <!-- CTA 3 - Tertiary -->
            <button class="group bg-white/10 hover:bg-white/20 backdrop-blur-sm border-2 border-white/30 text-white px-10 py-8 rounded-2xl font-bold text-xl transition-all hover:scale-105">
                <div class="flex items-center justify-between">
                    <div class="text-left">
                        <div class="text-sm opacity-70 mb-1">UZMAN DESTEK</div>
                        <div class="text-2xl">İletişim</div>
                    </div>
                    <i class="fas fa-headset text-3xl group-hover:scale-110 transition-transform"></i>
                </div>
            </button>
        </div>

        <!-- Trust Indicators -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto pt-12 border-t border-white/10">
            <div class="text-center">
                <div class="text-5xl font-black text-yellow-500 mb-2">25+</div>
                <div class="text-sm text-gray-400 uppercase tracking-wide">Yıl Deneyim</div>
            </div>
            <div class="text-center">
                <div class="text-5xl font-black text-yellow-500 mb-2">2K+</div>
                <div class="text-sm text-gray-400 uppercase tracking-wide">Kurumsal Müşteri</div>
            </div>
            <div class="text-center">
                <div class="text-5xl font-black text-yellow-500 mb-2">500+</div>
                <div class="text-sm text-gray-400 uppercase tracking-wide">Ürün Çeşidi</div>
            </div>
            <div class="text-center">
                <div class="text-5xl font-black text-yellow-500 mb-2">7/24</div>
                <div class="text-sm text-gray-400 uppercase tracking-wide">Teknik Destek</div>
            </div>
        </div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute top-1/4 left-12 w-20 h-20 bg-yellow-500/20 rounded-full blur-2xl animate-pulse-slow"></div>
    <div class="absolute bottom-1/4 right-12 w-32 h-32 bg-yellow-500/20 rounded-full blur-2xl animate-pulse-slow" style="animation-delay: 1s;"></div>
</section>

<!-- PRODUCT CATEGORIES - Industrial Cards -->
<section class="py-20 bg-gray-50 dark:bg-gray-950">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-4">Ürün Kategorileri</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400">Endüstriyel operasyonlarınız için her şey</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
            <!-- Forklift -->
            <a href="#" class="group bg-white dark:bg-gray-900 border-4 border-gray-200 dark:border-gray-800 hover:border-yellow-500 dark:hover:border-yellow-500 rounded-2xl p-8 transition-all hover:scale-105">
                <div class="w-20 h-20 bg-gray-900 dark:bg-yellow-500 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 dark:group-hover:bg-gray-900 transition-all">
                    <i class="fas fa-forklift text-4xl text-yellow-500 dark:text-gray-900 group-hover:text-gray-900 dark:group-hover:text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-all">
                    FORKLIFT
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Akülü, Dizel, LPG</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">150+ Model</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-yellow-500 group-hover:translate-x-2 transition-all"></i>
                </div>
            </a>

            <!-- Transpalet -->
            <a href="#" class="group bg-white dark:bg-gray-900 border-4 border-gray-200 dark:border-gray-800 hover:border-yellow-500 dark:hover:border-yellow-500 rounded-2xl p-8 transition-all hover:scale-105">
                <div class="w-20 h-20 bg-gray-900 dark:bg-yellow-500 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 dark:group-hover:bg-gray-900 transition-all">
                    <i class="fas fa-pallet text-4xl text-yellow-500 dark:text-gray-900 group-hover:text-gray-900 dark:group-hover:text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-all">
                    TRANSPALET
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Manuel, Akülü</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">200+ Model</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-yellow-500 group-hover:translate-x-2 transition-all"></i>
                </div>
            </a>

            <!-- İstif -->
            <a href="#" class="group bg-white dark:bg-gray-900 border-4 border-gray-200 dark:border-gray-800 hover:border-yellow-500 dark:hover:border-yellow-500 rounded-2xl p-8 transition-all hover:scale-105">
                <div class="w-20 h-20 bg-gray-900 dark:bg-yellow-500 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 dark:group-hover:bg-gray-900 transition-all">
                    <i class="fas fa-boxes-stacked text-4xl text-yellow-500 dark:text-gray-900 group-hover:text-gray-900 dark:group-hover:text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-all">
                    İSTİF MAKİNESİ
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Elektrikli</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">80+ Model</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-yellow-500 group-hover:translate-x-2 transition-all"></i>
                </div>
            </a>

            <!-- Depo -->
            <a href="#" class="group bg-white dark:bg-gray-900 border-4 border-gray-200 dark:border-gray-800 hover:border-yellow-500 dark:hover:border-yellow-500 rounded-2xl p-8 transition-all hover:scale-105">
                <div class="w-20 h-20 bg-gray-900 dark:bg-yellow-500 rounded-xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 dark:group-hover:bg-gray-900 transition-all">
                    <i class="fas fa-warehouse text-4xl text-yellow-500 dark:text-gray-900 group-hover:text-gray-900 dark:group-hover:text-yellow-500"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-all">
                    DEPO EKİPMANI
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Raf, Platform</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">120+ Model</span>
                    <i class="fas fa-arrow-right text-gray-400 group-hover:text-yellow-500 group-hover:translate-x-2 transition-all"></i>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- WHY US SECTION -->
<section class="py-20 bg-gray-900 dark:bg-black">
    <div class="container mx-auto px-6">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl lg:text-5xl font-black text-white text-center mb-16">
                Neden <span class="text-yellow-500">İXTİF</span>?
            </h2>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-3xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Sertifikalı Ürünler</h3>
                    <p class="text-gray-400">Tüm ürünlerimiz CE belgeli ve garantilidir</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-tools text-3xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Teknik Servis</h3>
                    <p class="text-gray-400">Ülke çapında 50+ yetkili servis noktası</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-truck-fast text-3xl text-black"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Hızlı Teslimat</h3>
                    <p class="text-gray-400">Stokta bulunan ürünlerde 24-72 saat teslimat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-40 hidden lg:block">
    <div class="bg-gray-900 dark:bg-yellow-500 border-2 border-yellow-500 dark:border-gray-900 rounded-full px-6 py-3 shadow-2xl">
        <span class="font-black text-yellow-500 dark:text-gray-900">#DELUXE-V5</span>
        <span class="mx-3 text-yellow-500/50 dark:text-gray-900/50">|</span>
        <span class="text-xs text-yellow-500/80 dark:text-gray-900/80 font-bold">INDUSTRIAL B2B POWERHOUSE</span>
    </div>
</div>
@endsection
