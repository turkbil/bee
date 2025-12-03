@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V1</a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V2</a>
    <a href="{{ route('design.deluxe-industrial.v3') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V3</a>
    <a href="{{ route('design.deluxe-industrial.v4') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">V4</a>
    <a href="{{ route('design.deluxe-industrial.v5') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">V5</a>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes scaleUp {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
.animate-scale-up { animation: scaleUp 0.6s ease-out forwards; }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
</style>

<!-- HERO SECTION - Modern SaaS Style -->
<section class="py-16 lg:py-20 bg-gradient-to-br from-violet-50 via-white to-cyan-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950">
    <div class="container mx-auto px-6">
        <!-- Centered Hero Content -->
        <div class="max-w-4xl mx-auto text-center mb-16 animate-fade-in delay-100">
            <div class="inline-block mb-6">
                <span class="px-5 py-2 bg-gradient-to-r from-violet-500 to-cyan-500 text-white rounded-full text-sm font-bold shadow-lg">
                    Yeni Nesil Platform
                </span>
            </div>
            
            <h1 class="text-5xl lg:text-7xl font-black text-gray-900 dark:text-white leading-tight mb-8">
                Endüstriyel Ekipman<br/>
                <span class="bg-gradient-to-r from-violet-600 to-cyan-600 dark:from-violet-400 dark:to-cyan-400 bg-clip-text text-transparent">
                    Dijital Pazaryeri
                </span>
            </h1>

            <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-400 mb-10 max-w-2xl mx-auto font-light">
                Yapay zeka destekli arama, anlık fiyat karşılaştırma ve güvenli ödeme ile yeni nesil B2B deneyimi.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button class="px-10 py-5 bg-gradient-to-r from-violet-600 to-cyan-600 text-white rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all">
                    Ücretsiz Başla
                </button>
                <button class="px-10 py-5 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white rounded-2xl font-bold text-lg hover:border-violet-500 dark:hover:border-violet-500 transition-all">
                    Demo İzle
                </button>
            </div>
        </div>

        <!-- BENTO GRID - Asymmetric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-7xl mx-auto">
            <!-- Large Card - Forklift -->
            <div class="lg:col-span-2 lg:row-span-2 bg-gradient-to-br from-blue-500 to-cyan-500 dark:from-blue-600 dark:to-cyan-600 rounded-3xl p-10 relative overflow-hidden group animate-scale-up delay-200">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10">
                    <div class="inline-block px-4 py-1.5 bg-white/20 backdrop-blur-sm text-white rounded-full text-xs font-bold mb-6">
                        EN POPÜLER
                    </div>
                    <h2 class="text-4xl font-black text-white mb-4">Forklift Seçenekleri</h2>
                    <p class="text-white/90 text-lg mb-8 max-w-md">
                        Akülü, dizel ve LPG'li forkliftlerde 150+ model. Anında karşılaştır, teklif al.
                    </p>
                    <button class="px-8 py-4 bg-white text-blue-600 rounded-xl font-bold hover:bg-blue-50 transition-all flex items-center gap-2">
                        Keşfet <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                <div class="absolute bottom-0 right-0 opacity-20 group-hover:opacity-30 transition-opacity">
                    <i class="fas fa-forklift text-[20rem] text-white"></i>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 border-2 border-gray-200 dark:border-gray-800 animate-scale-up delay-300">
                <div class="w-14 h-14 bg-violet-100 dark:bg-violet-900 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-chart-line text-3xl text-violet-600 dark:text-violet-400"></i>
                </div>
                <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">2.000+</div>
                <div class="text-gray-600 dark:text-gray-400">Aktif Tedarikçi</div>
                <div class="mt-4 text-sm text-green-600 dark:text-green-400 font-semibold flex items-center gap-2">
                    <i class="fas fa-arrow-up"></i> %25 artış
                </div>
            </div>

            <!-- Transpalet Card -->
            <div class="bg-gradient-to-br from-orange-500 to-red-500 dark:from-orange-600 dark:to-red-600 rounded-3xl p-8 relative overflow-hidden animate-scale-up delay-400">
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-pallet text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black text-white mb-3">Transpalet</h3>
                    <p class="text-white/90 mb-6">Manuel ve akülü modeller</p>
                    <button class="text-white font-bold flex items-center gap-2 hover:gap-3 transition-all">
                        İncele <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Feature Card - AI Search -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 border-2 border-gray-200 dark:border-gray-800 animate-scale-up delay-500">
                <div class="w-14 h-14 bg-cyan-100 dark:bg-cyan-900 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-robot text-3xl text-cyan-600 dark:text-cyan-400"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">AI Arama</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    Yapay zeka ile ihtiyacınıza en uygun ürünü saniyeler içinde bulun.
                </p>
            </div>

            <!-- İstif Card -->
            <div class="lg:col-span-2 bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-600 dark:to-emerald-600 rounded-3xl p-10 relative overflow-hidden animate-scale-up delay-400">
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <h3 class="text-3xl font-black text-white mb-3">İstif Makineleri</h3>
                        <p class="text-white/90 text-lg mb-6 max-w-md">
                            Elektrikli istif makinelerinde 80+ seçenek. 3-6m yükseklik aralığı.
                        </p>
                        <button class="px-6 py-3 bg-white text-green-600 rounded-xl font-bold hover:bg-green-50 transition-all">
                            Tüm Modeller
                        </button>
                    </div>
                    <div class="hidden lg:block">
                        <i class="fas fa-boxes-stacked text-[10rem] text-white/20"></i>
                    </div>
                </div>
            </div>

            <!-- Trust Badge Card -->
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-8 border-2 border-gray-200 dark:border-gray-800 animate-scale-up delay-300">
                <div class="w-14 h-14 bg-green-100 dark:bg-green-900 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-shield-check text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-3">Güvenli Ödeme</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    256-bit SSL şifreleme ile güvenli alışveriş garantisi.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES SECTION -->
<section class="py-20 bg-white dark:bg-gray-950">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-6">Platform Özellikleri</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Modern teknoloji ile endüstriyel ekipman alışverişini kolaylaştırıyoruz
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
            <div class="text-center">
                <div class="w-16 h-16 bg-violet-100 dark:bg-violet-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-bolt text-3xl text-violet-600 dark:text-violet-400"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Anlık Teklif</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">5 dakikada fiyat teklifi alın</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-simple text-3xl text-cyan-600 dark:text-cyan-400"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Fiyat Karşılaştır</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Tüm tedarikçileri kıyaslayın</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-truck-fast text-3xl text-orange-600 dark:text-orange-400"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">Hızlı Teslimat</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">24-72 saat içinde</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-headset text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">7/24 Destek</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Her zaman yanınızdayız</p>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-40 hidden lg:block">
    <div class="bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-800 rounded-full px-6 py-3 shadow-xl">
        <span class="font-bold text-gray-900 dark:text-white">#DELUXE-V4</span>
        <span class="mx-3 text-gray-300 dark:text-gray-700">|</span>
        <span class="text-xs text-gray-600 dark:text-gray-400">MODERN SAAS BENTO GRID</span>
    </div>
</div>
@endsection
