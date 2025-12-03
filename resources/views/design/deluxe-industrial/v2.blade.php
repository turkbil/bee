@extends('themes.ixtif.layouts.app')

@section('content')
<!-- Version Navigation -->
<div class="fixed top-24 right-6 z-50 flex flex-col gap-2">
    <a href="{{ route('design.deluxe-industrial.v1') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:border-blue-600 dark:hover:border-blue-500 transition-all">
        V1
    </a>
    <a href="{{ route('design.deluxe-industrial.v2') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold shadow-lg">
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
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-60px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(60px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-slide-in-left {
    animation: slideInLeft 1s ease-out forwards;
}

.animate-slide-in-right {
    animation: slideInRight 1s ease-out forwards;
}

.animate-scale-in {
    animation: scaleIn 1.2s ease-out forwards;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
.delay-600 { animation-delay: 0.6s; }
</style>

<!-- SPLIT SCREEN HERO - Apple Style -->
<section class="relative min-h-[85vh] flex items-center bg-white dark:bg-black overflow-hidden">
    <!-- LEFT: Content Side -->
    <div class="w-full lg:w-1/2 px-8 lg:px-16 xl:px-24 py-20">
        <div class="max-w-2xl">
            <!-- Small Badge -->
            <div class="inline-block mb-8 animate-slide-in-left delay-100">
                <span class="px-4 py-1.5 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-full text-xs font-semibold tracking-wide uppercase">
                    Endüstriyel Çözümler
                </span>
            </div>

            <!-- Main Title - Minimal & Bold -->
            <h1 class="text-5xl lg:text-7xl xl:text-8xl font-black text-gray-900 dark:text-white leading-[0.95] mb-10 animate-slide-in-left delay-200">
                Güç.<br/>
                Verimlilik.<br/>
                <span class="text-gray-400 dark:text-gray-600">Kalite.</span>
            </h1>

            <!-- Description - Clean & Minimal -->
            <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-400 mb-12 leading-relaxed font-light animate-slide-in-left delay-300">
                Forklift ve istif makinelerinde yeni standart.
                Türkiye'nin en geniş endüstriyel ekipman koleksiyonu.
            </p>

            <!-- Clean Stats -->
            <div class="grid grid-cols-3 gap-8 mb-16 animate-slide-in-left delay-400">
                <div>
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-1">500+</div>
                    <div class="text-sm text-gray-500 dark:text-gray-500 font-medium">Ürün</div>
                </div>
                <div>
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-1">2K+</div>
                    <div class="text-sm text-gray-500 dark:text-gray-500 font-medium">Müşteri</div>
                </div>
                <div>
                    <div class="text-4xl font-black text-gray-900 dark:text-white mb-1">24h</div>
                    <div class="text-sm text-gray-500 dark:text-gray-500 font-medium">Teslimat</div>
                </div>
            </div>

            <!-- Minimal CTA -->
            <div class="flex flex-col sm:flex-row gap-4 animate-slide-in-left delay-500">
                <button class="px-10 py-5 bg-gray-900 dark:bg-white text-white dark:text-black rounded-full font-semibold text-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-all shadow-lg hover:shadow-2xl hover:scale-105">
                    Keşfet
                </button>
                <button class="px-10 py-5 border-2 border-gray-900 dark:border-white text-gray-900 dark:text-white rounded-full font-semibold text-lg hover:bg-gray-50 dark:hover:bg-gray-900 transition-all">
                    Teklif Al
                </button>
            </div>

            <!-- Minimal Trust Line -->
            <div class="mt-16 flex items-center gap-8 text-gray-500 dark:text-gray-500 text-sm animate-slide-in-left delay-600">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>2 yıl garanti</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-shield-check"></i>
                    <span>CE belgeli</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-headset"></i>
                    <span>7/24 destek</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Visual Side -->
    <div class="hidden lg:block lg:w-1/2 h-[85vh] relative animate-slide-in-right delay-300">
        <!-- Gradient Overlay for depth -->
        <div class="absolute inset-0 bg-gradient-to-br from-transparent via-blue-500/5 to-purple-500/10 dark:from-transparent dark:via-blue-500/20 dark:to-purple-500/30 z-10"></div>
        
        <!-- Large Product Visual -->
        <div class="w-full h-full bg-gradient-to-br from-gray-100 via-gray-50 to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-blue-950 flex items-center justify-center relative overflow-hidden">
            <!-- Background Shapes -->
            <div class="absolute top-20 right-20 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/20 rounded-full blur-3xl animate-scale-in delay-400"></div>
            <div class="absolute bottom-20 left-20 w-80 h-80 bg-purple-500/10 dark:bg-purple-500/20 rounded-full blur-3xl animate-scale-in delay-500"></div>
            
            <!-- Main Forklift Icon - Huge & Centered -->
            <div class="relative z-20 animate-scale-in delay-600">
                <i class="fas fa-forklift text-[20rem] text-gray-900 dark:text-white opacity-90"></i>
            </div>

            <!-- Floating Mini Cards -->
            <div class="absolute top-1/4 left-12 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-5 animate-slide-in-left delay-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-bolt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">Elektrikli</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Çevre dostu</div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-1/4 right-12 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-5 animate-slide-in-right delay-800">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                        <i class="fas fa-gauge-high text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">3.5 Ton</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Kapasite</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MINIMAL CATEGORY GRID -->
<section class="py-24 bg-gray-50 dark:bg-gray-950">
    <div class="container mx-auto px-8">
        <!-- Section Header - Minimal -->
        <div class="max-w-4xl mx-auto text-center mb-20">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 dark:text-white mb-6">Ürün Aileleri</h2>
            <p class="text-xl text-gray-600 dark:text-gray-400 font-light">Profesyonel endüstriyel ekipmanlar, tek platformda.</p>
        </div>

        <!-- Clean 4-Col Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-7xl mx-auto">
            <!-- Forklift -->
            <a href="#" class="group bg-white dark:bg-gray-900 rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-100 dark:group-hover:bg-blue-900 transition-all duration-500">
                    <i class="fas fa-forklift text-4xl text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Forklift</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 font-light">Akülü ve dizel modeller</p>
                <div class="text-sm text-gray-900 dark:text-white font-semibold group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-all">
                    150+ model →
                </div>
            </a>

            <!-- Transpalet -->
            <a href="#" class="group bg-white dark:bg-gray-900 rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-100 dark:group-hover:bg-orange-900 transition-all duration-500">
                    <i class="fas fa-pallet text-4xl text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Transpalet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 font-light">Manuel ve elektrikli</p>
                <div class="text-sm text-gray-900 dark:text-white font-semibold group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-all">
                    200+ model →
                </div>
            </a>

            <!-- İstif -->
            <a href="#" class="group bg-white dark:bg-gray-900 rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-green-100 dark:group-hover:bg-green-900 transition-all duration-500">
                    <i class="fas fa-boxes-stacked text-4xl text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-all"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">İstif Makinesi</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 font-light">Elektrikli yığma</p>
                <div class="text-sm text-gray-900 dark:text-white font-semibold group-hover:text-green-600 dark:group-hover:text-green-400 transition-all">
                    80+ model →
                </div>
            </a>

            <!-- Depo -->
            <a href="#" class="group bg-white dark:bg-gray-900 rounded-3xl p-8 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-100 dark:group-hover:bg-purple-900 transition-all duration-500">
                    <i class="fas fa-warehouse text-4xl text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-all"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Depo Ekipmanı</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 font-light">Raf ve platformlar</p>
                <div class="text-sm text-gray-900 dark:text-white font-semibold group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-all">
                    120+ model →
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Bottom Badge -->
<div class="fixed bottom-8 left-8 z-40 hidden lg:block">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-full px-6 py-3 shadow-xl">
        <span class="font-bold text-gray-900 dark:text-white">#DELUXE-V2</span>
        <span class="mx-3 text-gray-300 dark:text-gray-700">|</span>
        <span class="text-xs text-gray-600 dark:text-gray-400">APPLE MINIMALIST STYLE</span>
    </div>
</div>
@endsection
