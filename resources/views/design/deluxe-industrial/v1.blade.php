@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">
        V1
    </a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">
        V2
    </a>
    <a href="{{ route('design.deluxe-industrial.v3') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">
        V3
    </a>
    <a href="{{ route('design.deluxe-industrial.v4') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">
        V4
    </a>
    <a href="{{ route('design.deluxe-industrial.v5') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">
        V5
    </a>
</div>

<style>
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

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

.animate-float {
    animation: float 4s ease-in-out infinite;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
</style>

<!-- HERO SECTION - Amazon Business Style -->
<section class="relative overflow-hidden py-16 lg:py-24 bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5 dark:opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
    </div>

    <div class="container mx-auto px-6 relative">
        <div class="grid lg:grid-cols-2 gap-12 items-center max-w-7xl mx-auto">

            <!-- LEFT: Content -->
            <div class="space-y-8 animate-fade-in-up delay-100">
                <!-- Category Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg text-sm font-bold shadow-lg">
                    <i class="fas fa-industry"></i>
                    <span>Endüstriyel Ekipman</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl lg:text-6xl font-black text-gray-900 dark:text-white leading-tight">
                    Forklift ve<br/>
                    <span class="text-blue-600 dark:text-blue-400">İstif Makineleri</span><br/>
                    Toptan Satış
                </h1>

                <!-- Description -->
                <p class="text-lg lg:text-xl text-gray-700 dark:text-gray-300 leading-relaxed max-w-xl">
                    <strong class="text-gray-900 dark:text-white">2.000+ işletme</strong> bize güveniyor.
                    Akülü forklift, dizel istif makinesi, transpalet ve depo ekipmanlarında
                    <span class="text-blue-600 dark:text-blue-400 font-bold">toptan fiyat avantajı</span> ile hızlı teslimat.
                </p>

                <!-- Stats - Amazon Style -->
                <div class="grid grid-cols-3 gap-6 py-6 border-y border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-black text-blue-600 dark:text-blue-400 mb-1">500+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ürün Çeşidi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-black text-green-600 dark:text-green-400 mb-1">24h</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Hızlı Kargo</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl lg:text-4xl font-black text-orange-600 dark:text-orange-400 mb-1">B2B</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Toptan Fiyat</div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4">
                    <button class="px-8 py-4 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Ürünleri İncele
                    </button>
                    <button class="px-8 py-4 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl font-bold text-lg hover:border-blue-600 dark:hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Toptan Fiyat Al
                    </button>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap items-center gap-6 pt-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-shield-check text-green-600 dark:text-green-400 text-2xl"></i>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Güvenli Alışveriş</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-truck-fast text-blue-600 dark:text-blue-400 text-2xl"></i>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Express Teslimat</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-headset text-purple-600 dark:text-purple-400 text-2xl"></i>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">7/24 Destek</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Featured Product Card -->
            <div class="animate-fade-in-up delay-300">
                <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 hover:shadow-3xl transition-all animate-float">
                    <!-- Product Badge -->
                    <div class="absolute -top-4 -right-4 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-full font-bold text-sm shadow-lg">
                        <i class="fas fa-fire mr-1"></i>
                        ÇOK SATANLAR
                    </div>

                    <!-- Product Image Placeholder -->
                    <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 dark:from-gray-700 dark:to-gray-600 rounded-xl mb-6 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="fas fa-forklift text-8xl lg:text-9xl text-blue-600 dark:text-blue-400"></i>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-4">
                        <div>
                            <span class="text-sm text-orange-600 dark:text-orange-400 font-bold">ÖNERİLEN ÜRÜN</span>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">Akülü Forklift 2.5 Ton</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Kapalı alan kullanımı için ideal elektrikli forklift</p>
                        </div>

                        <!-- Features -->
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span class="text-sm">2.5 Ton kaldırma kapasitesi</span>
                            </li>
                            <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span class="text-sm">48V Akülü elektrikli motor</span>
                            </li>
                            <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span class="text-sm">3m kaldırma yüksekliği</span>
                            </li>
                            <li class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-circle-check text-green-600 dark:text-green-400"></i>
                                <span class="text-sm">CE belgeli, 2 yıl garanti</span>
                            </li>
                        </ul>

                        <!-- Price & CTA -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Toptan Fiyat</div>
                                <div class="text-3xl font-black text-blue-600 dark:text-blue-400">Teklif Al</div>
                                <div class="text-xs text-green-600 dark:text-green-400 font-bold">✓ Stokta mevcut</div>
                            </div>
                            <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-lg font-bold transition-all hover:scale-105 shadow-lg">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORY SHOWCASE - Product Category Cards -->
<section class="py-16 lg:py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12 animate-fade-in-up delay-100">
            <h2 class="text-3xl lg:text-4xl font-black text-gray-900 dark:text-white mb-4">Ürün Kategorileri</h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Endüstriyel ekipman ihtiyaçlarınız için geniş ürün yelpazesi</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto">
            <!-- Forklift -->
            <a href="#" class="group bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 transition-all hover:scale-105 animate-fade-in-up delay-200">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-200 dark:group-hover:bg-blue-800 transition-all">
                    <i class="fas fa-forklift text-3xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Forklift</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Akülü & Dizel</p>
                <div class="mt-3 text-xs text-blue-600 dark:text-blue-400 font-bold">150+ ürün →</div>
            </a>

            <!-- Transpalet -->
            <a href="#" class="group bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 transition-all hover:scale-105 animate-fade-in-up delay-300">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-800 transition-all">
                    <i class="fas fa-pallet text-3xl text-orange-600 dark:text-orange-400"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Transpalet</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manuel & Akülü</p>
                <div class="mt-3 text-xs text-orange-600 dark:text-orange-400 font-bold">200+ ürün →</div>
            </a>

            <!-- İstif Makinesi -->
            <a href="#" class="group bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 transition-all hover:scale-105 animate-fade-in-up delay-400">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4 group-hover:bg-green-200 dark:group-hover:bg-green-800 transition-all">
                    <i class="fas fa-boxes-stacked text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">İstif Makinesi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Elektrikli</p>
                <div class="mt-3 text-xs text-green-600 dark:text-green-400 font-bold">80+ ürün →</div>
            </a>

            <!-- Depo Ekipmanı -->
            <a href="#" class="group bg-gray-50 dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl p-6 transition-all hover:scale-105 animate-fade-in-up delay-400">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4 group-hover:bg-purple-200 dark:group-hover:bg-purple-800 transition-all">
                    <i class="fas fa-warehouse text-3xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-1">Depo Ekipmanı</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Raf & Platform</p>
                <div class="mt-3 text-xs text-purple-600 dark:text-purple-400 font-bold">120+ ürün →</div>
            </a>
        </div>
    </div>
</section>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-40 hidden lg:block">
    <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-full px-6 py-3 shadow-xl">
        <span class="font-bold text-gray-900 dark:text-white">#DELUXE-V1</span>
        <span class="mx-3 text-gray-300 dark:text-gray-600">|</span>
        <span class="text-xs text-gray-600 dark:text-gray-400">AMAZON BUSINESS STYLE</span>
    </div>
</div>
@endsection
