@extends('themes.ixtif.layouts.app')

@section('content')
<!-- V1: FORKLIFT HERO - Amazon Business Style -->
<section class="relative overflow-hidden bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-blue-900">
    <div class="container mx-auto px-4 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            <!-- LEFT: Hero Content -->
            <div class="space-y-8">
                <!-- Badge -->
                <div class="inline-block">
                    <span class="px-6 py-2 bg-blue-600 text-white rounded-full text-sm font-bold">
                        Endüstriyel Ekipman Uzman1
                    </span>
                </div>

                <!-- Main Title -->
                <h1 class="text-5xl lg:text-7xl font-black text-gray-900 dark:text-white leading-tight">
                    <span class="block">Profesyonel</span>
                    <span class="block text-blue-600">Forklift & 0stif</span>
                    <span class="block">Çözümleri</span>
                </h1>

                <!-- Description -->
                <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                    Akülü forklift, dizel istif makineleri ve transpaletlerde toptan sat1_ ve kurumsal çözümler.
                    Türkiye'nin güvenilir endüstriyel ekipman tedarikçisi.
                </p>

                <!-- Stats Bar -->
                <div class="grid grid-cols-3 gap-6 py-8 border-y border-gray-200 dark:border-gray-700">
                    <div>
                        <div class="text-4xl font-black text-blue-600">500+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Ürün Çe_idi</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-blue-600">7/24</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Destek</div>
                    </div>
                    <div>
                        <div class="text-4xl font-black text-blue-600">B2B</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Toptan Sat1_</div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('shop.index') }}" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all hover:scale-105">
                        <i class="fas fa-box mr-2"></i>
                        Tüm Ürünleri 0ncele
                    </a>
                    <a href="#contact" class="px-8 py-4 bg-white dark:bg-gray-800 border-2 border-blue-600 text-blue-600 dark:text-blue-400 rounded-xl font-bold text-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition-all">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Fiyat Teklifi Al
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-wrap items-center gap-6 pt-4">
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-shield-check text-green-600 text-xl"></i>
                        <span class="text-sm">Güvenli Ödeme</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-truck-fast text-blue-600 text-xl"></i>
                        <span class="text-sm">H1zl1 Teslimat</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <i class="fas fa-headset text-purple-600 text-xl"></i>
                        <span class="text-sm">7/24 Destek</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Featured Products Showcase -->
            <div class="relative">
                <!-- Main Featured Product -->
                <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 hover:shadow-3xl transition-all group">
                    <!-- Product Image Placeholder -->
                    <div class="aspect-video bg-gradient-to-br from-blue-100 to-blue-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl mb-6 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <i class="fas fa-forklift text-8xl text-blue-600 dark:text-blue-400"></i>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Öne Ç1kan Ürün</span>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Akülü Forklift 2.5 Ton</h3>
                            </div>
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full text-sm font-bold">Stokta</span>
                        </div>

                        <ul class="space-y-2 text-gray-600 dark:text-gray-300">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span>2.5 Ton kald1rma kapasitesi</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span>Akülü elektrikli motor</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600"></i>
                                <span>Kapal1 alan kullan1m1</span>
                            </li>
                        </ul>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Toptan Fiyat</div>
                                <div class="text-3xl font-black text-blue-600">Teklif Al</div>
                            </div>
                            <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all hover:scale-105">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                0ncele
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Floating Mini Cards -->
                <div class="absolute -top-6 -right-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4 w-48">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pallet text-2xl text-orange-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">Transpalet</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">150+ Model</div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-6 -left-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4 w-48">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-warehouse text-2xl text-purple-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">Depo Ekipman1</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">200+ Çe_it</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Category Links -->
        <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6">
            <a href="#" class="group p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all hover:scale-105">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-forklift text-3xl text-blue-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Forklift</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Akülü & Dizel</p>
            </a>

            <a href="#" class="group p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all hover:scale-105">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900 rounded-xl flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-pallet text-3xl text-orange-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Transpalet</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Manuel & Akülü</p>
            </a>

            <a href="#" class="group p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all hover:scale-105">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-boxes-stacked text-3xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">0stif Makinesi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Tam Elektrikli</p>
            </a>

            <a href="#" class="group p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all hover:scale-105">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center mb-4 group-hover:rotate-6 transition-transform">
                    <i class="fas fa-warehouse text-3xl text-purple-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">Depo Ekipman1</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Raf & Sistem</p>
            </a>
        </div>
    </div>
</section>
@endsection
