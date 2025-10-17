@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@push('styles')
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    /* Animated Gradient - Light & Dark Mode */
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .gradient-animated {
        background-size: 200% 200%;
        animation: gradient-shift 4s linear infinite;
    }

    /* S/X Letter Switch Animation */
    @keyframes letterFadeOut {
        0%, 40% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }

    @keyframes letterFadeIn {
        0%, 50% { opacity: 0; }
        60%, 100% { opacity: 1; }
    }

    .letter-s {
        animation: letterFadeOut 3s ease-in-out infinite;
    }

    .letter-x {
        animation: letterFadeIn 3s ease-in-out infinite;
    }
</style>
@endpush

@section('module_content')
@if(isset($is_homepage) && $is_homepage)
    <div x-data="homepage()" x-init="init()">
        <section class="min-h-screen flex items-center relative overflow-hidden -mt-[72px] pt-[72px]">
        <!-- Animated Background Blobs -->
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-20 -left-20 w-96 h-96 bg-white/10 dark:bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 -right-20 w-96 h-96 bg-white/10 dark:bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-gray-900 dark:text-white">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm px-6 py-3 rounded-full text-sm font-bold mb-8 border border-gray-200 dark:border-gray-700">
                        <i class="fa-solid fa-fire text-blue-600 dark:text-blue-400"></i>
                        <span class="text-gray-700 dark:text-gray-200">Türkiye'nin #1 İstif Ekipmanları Tedarikçisi</span>
                    </div>

                    <!-- Main Title with Animation -->
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-6 leading-[1.4] overflow-visible pb-4">
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-cyan-500 to-purple-600 dark:from-orange-400 dark:via-pink-400 dark:to-purple-400 gradient-animated block py-1">
                            TÜRKİYE'NİN
                        </span>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 via-cyan-500 to-purple-600 dark:from-orange-400 dark:via-pink-400 dark:to-purple-400 gradient-animated block py-1">
                            İ<span class="relative inline-block" style="width: 0.7em; height: 1.4em; top: 0.05em;">
                                <span class="letter-s absolute inset-0 flex items-center justify-center">S</span>
                                <span class="letter-x absolute inset-0 flex items-center justify-center">X</span>
                            </span>TİF PAZARI
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="text-lg text-gray-700 dark:text-gray-200 mb-6 leading-relaxed">
                        Profesyonel istif çözümleri, güçlü stok ve hızlı teslimat ile işletmenizin güvenilir ortağı
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="{{ route('shop.index') }}" class="group bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold text-base transition-all inline-block text-center">
                            <i class="fa-solid fa-shopping-cart mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                            Ürünleri İncele
                        </a>
                        <a href="#nasil-calisir" class="group bg-white dark:bg-gray-800 text-gray-800 dark:text-white px-8 py-3 rounded-full font-semibold text-base transition-all inline-block text-center border border-gray-200 dark:border-gray-700">
                            <i class="fa-solid fa-headset mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                            Teklif Al
                        </a>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-boxes-stacked text-blue-600 dark:text-blue-300 text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Güçlü Stok</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Anında teslimat</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-certificate text-blue-600 dark:text-blue-300 text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Garantili Ürün</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Teknik servis</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-award text-blue-600 dark:text-blue-300 text-lg"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Profesyonel Ekip</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">Uzman danışmanlık</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Hero Image -->
                <div class="relative">
                    <div class="relative rounded-3xl overflow-hidden">
                        <!-- Placeholder for Hero Image -->
                        <div class="aspect-[4/3] bg-gradient-to-br from-blue-600 to-purple-600 dark:from-indigo-600 dark:to-purple-600 flex items-center justify-center">
                            <!-- Bu alan ileride gerçek forklift görseli ile değiştirilecek -->
                            <div class="text-center text-white p-8">
                                <i class="fa-solid fa-forklift text-9xl mb-6 opacity-50"></i>
                                <p class="text-2xl font-bold opacity-75">İstif Ekipmanları Görseli</p>
                                <p class="text-sm opacity-50 mt-2">(Profesyonel fotoğraf eklenecek)</p>
                            </div>
                        </div>

                        <!-- Floating Badge -->
                        <div class="absolute top-6 right-6 bg-white/95 dark:bg-slate-700/50 rounded-2xl px-5 py-3 border border-gray-200 dark:border-slate-600/30">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">
                                20.000+
                            </div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                Ürün Çeşidi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Categories Section -->
    <section class="w-full py-20 relative overflow-hidden">

        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Category Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Forklift -->
                <a href="{{ route('shop.index') }}" class="group relative h-80 rounded-3xl overflow-hidden transition-all duration-500
                    bg-blue-50/70 dark:bg-slate-700/50
                    border border-blue-100/50 dark:border-slate-600/30">
                    <div class="relative h-full flex flex-col justify-end p-8">
                        <i class="fa-solid fa-warehouse text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-transform"></i>
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Forklift</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-3">128 Ürün</p>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span>Keşfet</span>
                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Transpalet -->
                <a href="{{ route('shop.index') }}" class="group relative h-80 rounded-3xl overflow-hidden transition-all duration-500
                    bg-blue-50/70 dark:bg-slate-700/50
                    border border-blue-100/50 dark:border-slate-600/30">
                    <div class="relative h-full flex flex-col justify-end p-8">
                        <i class="fa-solid fa-dolly text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-transform"></i>
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Transpalet</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-3">69 Ürün</p>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span>Keşfet</span>
                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- İstif Makinesi -->
                <a href="{{ route('shop.index') }}" class="group relative h-80 rounded-3xl overflow-hidden transition-all duration-500
                    bg-blue-50/70 dark:bg-slate-700/50
                    border border-blue-100/50 dark:border-slate-600/30">
                    <div class="relative h-full flex flex-col justify-end p-8">
                        <i class="fa-solid fa-boxes-stacked text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-transform"></i>
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">İstif Makinesi</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-3">106 Ürün</p>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span>Keşfet</span>
                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Reach Truck -->
                <a href="{{ route('shop.index') }}" class="group relative h-80 rounded-3xl overflow-hidden transition-all duration-500
                    bg-blue-50/70 dark:bg-slate-700/50
                    border border-blue-100/50 dark:border-slate-600/30">
                    <div class="relative h-full flex flex-col justify-end p-8">
                        <i class="fa-solid fa-truck-ramp-box text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-transform"></i>
                        <h3 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Reach Truck</h3>
                        <p class="text-gray-600 dark:text-gray-300 text-lg mb-3">84 Ürün</p>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span>Keşfet</span>
                            <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="w-full py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-gray-900 dark:text-white mb-4">Öne Çıkan Ürünler</h2>
                <p class="text-xl text-gray-600 dark:text-gray-300">Hızlı görüntüleme ile detaylı bilgi</p>
            </div>

            @php
                $featuredProducts = \Modules\Shop\app\Models\ShopProduct::where('is_active', true)
                    ->inRandomOrder()
                    ->take(8)
                    ->get();
            @endphp

            <!-- Product Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredProducts as $product)
                @php
                    $productTitle = $product->getTranslated('title', app()->getLocale());
                    $productImage = $product->getFirstMediaUrl('product_images');
                    $productSlug = $product->getTranslated('slug', app()->getLocale());

                    // Slug varsa slug ile, yoksa ID ile route oluştur
                    if($productSlug) {
                        $productUrl = route('shop.show', $productSlug);
                    } else {
                        $productUrl = route('shop.show.by-id', $product->id);
                    }
                @endphp
                <div class="group bg-white/5 dark:bg-white/5 backdrop-blur-lg rounded-2xl p-6 border border-white/20 hover:border-white/40 transition-all cursor-pointer">
                    <!-- Product Image -->
                    <a href="{{ $productUrl }}" class="block aspect-square bg-white/5 dark:bg-white/5 rounded-xl flex items-center justify-center mb-4 overflow-hidden">
                        @if($productImage)
                            <img src="{{ $productImage }}"
                                 alt="{{ $productTitle }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <i class="fa-solid fa-box text-6xl text-blue-400 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
                        @endif
                    </a>

                    <!-- Product Info -->
                    <a href="{{ $productUrl }}" class="block">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ $productTitle }}</h3>
                    </a>

                    <!-- Quick View Button -->
                    <button
                        @click="openProductModal({
                            id: {{ $product->id }},
                            title: '{{ addslashes($productTitle) }}',
                            slug: '{{ $productSlug }}',
                            url: '{{ $productUrl }}',
                            image: '{{ $productImage }}',
                            category: '{{ $product->category?->getTranslated('title', app()->getLocale()) ?? 'Genel' }}',
                            brand: '{{ $product->brand?->getTranslated('title', app()->getLocale()) ?? '' }}',
                            shortDescription: '{{ addslashes($product->getTranslated('short_description', app()->getLocale()) ?? '') }}',
                            sku: '{{ $product->sku ?? '' }}',
                            primarySpecs: {{ json_encode(array_values(array_filter($product->primary_specs ?? [], fn($spec) => is_array($spec) && ($spec['label'] ?? false) && ($spec['value'] ?? false)))) }},
                            images: {{ json_encode($product->getMedia('product_images')->map(fn($media) => $media->getUrl())->toArray()) }}
                        })"
                        class="mt-4 w-full bg-white/10 dark:bg-white/10 hover:bg-white/20 dark:hover:bg-white/20 text-gray-900 dark:text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                        <i class="fa-solid fa-eye mr-2"></i>
                        Hızlı Bak
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Quick View Modal -->
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 lg:p-8"
         @click="closeModal()"
         style="display: none;">

        <div @click.stop class="bg-white dark:bg-slate-800 rounded-3xl max-w-5xl w-full max-h-[90vh] overflow-y-auto relative"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100">

            <!-- Close Button -->
            <button @click="closeModal()" class="absolute top-4 right-4 w-12 h-12 bg-black/10 dark:bg-white/10 hover:bg-black/20 dark:hover:bg-white/20 rounded-full flex items-center justify-center z-10 transition-colors">
                <i class="fa-solid fa-xmark text-2xl text-gray-900 dark:text-white"></i>
            </button>

            <div class="p-6 lg:p-12">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-12" x-show="selectedProduct">
                    <!-- Left: Product Images -->
                    <div>
                        <!-- Main Image -->
                        <div x-data="{ currentImage: 0 }">
                            <div class="aspect-square bg-gradient-to-br from-blue-100 to-purple-100 dark:from-slate-700 dark:to-slate-600 rounded-2xl flex items-center justify-center mb-6 overflow-hidden">
                                <template x-if="selectedProduct && selectedProduct.images && selectedProduct.images.length > 0">
                                    <img :src="selectedProduct.images[currentImage]"
                                         :alt="selectedProduct.title"
                                         class="w-full h-full object-cover">
                                </template>
                                <template x-if="selectedProduct && (!selectedProduct.images || selectedProduct.images.length === 0)">
                                    <i class="fa-solid fa-box text-[8rem] lg:text-[12rem] text-blue-400 dark:text-blue-300"></i>
                                </template>
                            </div>

                            <!-- Thumbnails -->
                            <div class="grid grid-cols-4 gap-3" x-show="selectedProduct && selectedProduct.images && selectedProduct.images.length > 1">
                                <template x-for="(image, index) in (selectedProduct?.images || [])" :key="index">
                                    <button @click="currentImage = index"
                                            class="aspect-square bg-gray-100 dark:bg-slate-700 rounded-lg flex items-center justify-center overflow-hidden transition-all"
                                            :class="currentImage === index ? 'ring-2 ring-2-blue-500' : 'opacity-60 hover:opacity-100'">
                                        <img :src="image" :alt="'Thumbnail ' + (index + 1)" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Product Details -->
                    <div>
                        <!-- Category & Brand -->
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span x-text="selectedProduct?.category || 'Kategori'"></span>
                            <template x-if="selectedProduct?.brand">
                                <span> / <span x-text="selectedProduct.brand"></span></span>
                            </template>
                        </div>

                        <!-- Title -->
                        <h2 class="text-3xl lg:text-4xl font-black text-gray-900 dark:text-white mb-4" x-text="selectedProduct?.title || 'Ürün Adı'"></h2>

                        <!-- SKU -->
                        <div class="mb-6" x-show="selectedProduct?.sku">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ürün Kodu: </span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="selectedProduct?.sku"></span>
                        </div>

                        <!-- Short Description (Hero Text) -->
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6 leading-relaxed" x-show="selectedProduct?.shortDescription" x-text="selectedProduct?.shortDescription"></p>

                        <!-- Primary Specs (4 Main Features) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6" x-show="selectedProduct?.primarySpecs && selectedProduct.primarySpecs.length > 0">
                            <template x-for="(spec, index) in selectedProduct?.primarySpecs?.slice(0, 4)" :key="index">
                                <div class="group relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 dark:from-blue-500/5 dark:to-purple-500/5 rounded-xl blur-lg group-hover:blur-xl transition-all"></div>
                                    <div class="relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-4 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                        <!-- Icon + Label -->
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-bolt text-white text-sm"></i>
                                            </div>
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="spec.label"></h4>
                                        </div>
                                        <!-- Value -->
                                        <div class="text-xl font-bold text-gray-900 dark:text-white" x-text="spec.value"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <a :href="selectedProduct?.url || '#'"
                               class="flex-grow bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-4 rounded-xl font-bold text-base lg:text-lg transition-all text-center">
                                <i class="fa-solid fa-arrow-right mr-2"></i>
                                Ürün Sayfasına Git
                            </a>
                            <button class="w-14 h-14 lg:w-16 lg:h-16 border-2 border-gray-300 dark:border-gray-600 rounded-xl flex items-center justify-center hover:border-red-500 hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-heart text-xl lg:text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Categories Section -->
    <section class="py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <!-- Satın Alma -->
                <a href="{{ route('shop.index') }}" class="group bg-blue-50/70 dark:bg-slate-700/50 border border-blue-100/50 dark:border-slate-600/30 rounded-3xl p-8 text-center transition-all hover:bg-blue-100/70 dark:hover:bg-slate-700/70">
                    <i class="fa-solid fa-shopping-cart text-5xl text-blue-600 dark:text-blue-400 mb-4 transition-all"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">Satın Alma</h3>
                </a>

                <!-- Kiralama -->
                <a href="#" class="group bg-yellow-50/70 dark:bg-slate-700/50 border border-yellow-100/50 dark:border-slate-600/30 rounded-3xl p-8 text-center transition-all hover:bg-yellow-100/70 dark:hover:bg-slate-700/70">
                    <i class="fa-solid fa-calendar-days text-5xl text-yellow-600 dark:text-yellow-400 mb-4 transition-all"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">Kiralama</h3>
                </a>

                <!-- İkinci El -->
                <a href="#" class="group bg-green-50/70 dark:bg-slate-700/50 border border-green-100/50 dark:border-slate-600/30 rounded-3xl p-8 text-center transition-all hover:bg-green-100/70 dark:hover:bg-slate-700/70">
                    <i class="fa-solid fa-recycle text-5xl text-green-600 dark:text-green-400 mb-4 transition-all"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">İkinci El</h3>
                </a>

                <!-- Yedek Parça -->
                <a href="#" class="group bg-orange-50/70 dark:bg-slate-700/50 border border-orange-100/50 dark:border-slate-600/30 rounded-3xl p-8 text-center transition-all hover:bg-orange-100/70 dark:hover:bg-slate-700/70">
                    <i class="fa-solid fa-gears text-5xl text-orange-600 dark:text-orange-400 mb-4 transition-all"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">Yedek Parça</h3>
                </a>

                <!-- Teknik Servis -->
                <a href="#" class="group bg-purple-50/70 dark:bg-slate-700/50 border border-purple-100/50 dark:border-slate-600/30 rounded-3xl p-8 text-center transition-all hover:bg-purple-100/70 dark:hover:bg-slate-700/70">
                    <i class="fa-solid fa-wrench text-5xl text-purple-600 dark:text-purple-400 mb-4 transition-all"></i>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">Teknik Servis</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Contact Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Phone -->
                <a href="tel:02167553555" class="bg-blue-50/70 dark:bg-slate-700/50 border border-blue-100/50 dark:border-slate-600/30 rounded-3xl p-8 transition-all text-center group hover:bg-blue-100/70 dark:hover:bg-slate-700/70">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-phone text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Telefon</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Hemen arayın</p>
                    <p class="text-xl font-black text-blue-600 dark:text-blue-400">0216 755 3 555</p>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/905010056758" target="_blank" class="bg-green-50/70 dark:bg-slate-700/50 border border-green-100/50 dark:border-slate-600/30 rounded-3xl p-8 transition-all text-center group hover:bg-green-100/70 dark:hover:bg-slate-700/70">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-brands fa-whatsapp text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">WhatsApp</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Anında mesajlaşın</p>
                    <p class="text-xl font-black text-green-600 dark:text-green-400">0501 005 67 58</p>
                </a>

                <!-- Email -->
                <a href="mailto:info@ixtif.com" class="bg-purple-50/70 dark:bg-slate-700/50 border border-purple-100/50 dark:border-slate-600/30 rounded-3xl p-8 transition-all text-center group hover:bg-purple-100/70 dark:hover:bg-slate-700/70">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-envelope text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">E-posta</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Mail gönderin</p>
                    <p class="text-xl font-black text-purple-600 dark:text-purple-400">info@ixtif.com</p>
                </a>

                <!-- Live Chat -->
                <button onclick="if(window.$store && window.$store.aiChat) { window.$store.aiChat.openFloating(); } else if(window.openAIChat) { window.openAIChat(); }" class="bg-cyan-50/70 dark:bg-slate-700/50 border border-cyan-100/50 dark:border-slate-600/30 rounded-3xl p-8 transition-all text-center group relative hover:bg-cyan-100/70 dark:hover:bg-slate-700/70">
                    <!-- AI Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-cyan-500/30 to-blue-500/30 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-bold rounded-full italic border border-cyan-300 dark:border-cyan-600">
                            <i class="fa-solid fa-sparkles text-yellow-600 dark:text-yellow-400"></i>
                            Yapay Zeka
                        </span>
                    </div>
                    <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-robot text-4xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Canlı Destek</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Yapay Zeka Destekli</p>
                    <p class="text-xl font-black text-orange-600 dark:text-orange-400">Sohbete Başla</p>
                </button>
            </div>
        </div>
    </section>

    {{-- Alpine.js Component --}}
    <script>
        function homepage() {
            return {
                loaded: false,
                showX: false,
                showModal: false,
                selectedProduct: null,

                init() {
                    this.$nextTick(() => {
                        this.loaded = true;
                    });

                    // İSTİF ↔ İXTİF animasyonu (S ↔ X değişimi)
                    setInterval(() => {
                        this.showX = !this.showX;
                    }, 2000);
                },

                openProductModal(productData) {
                    this.selectedProduct = productData;
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                }
            }
        }
    </script>
</div>

@else
<div class="min-h-screen">
    @php
        $currentLocale = app()->getLocale();
        $title = $item->getTranslated('title', $currentLocale);
        $body = $item->getTranslated('body', $currentLocale);
        
        $moduleSlugService = app(\App\Services\ModuleSlugService::class);
        $indexSlug = $moduleSlugService->getMultiLangSlug('Page', 'index', $currentLocale);
        $defaultLocale = get_tenant_default_locale();
        $localePrefix = ($currentLocale !== $defaultLocale) ? '/' . $currentLocale : '';
        $pageIndexUrl = $localePrefix . '/' . $indexSlug;
    @endphp
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <article class="prose prose-xl max-w-none dark:prose-invert 
                      prose-headings:text-gray-900 dark:prose-headings:text-white 
                      prose-p:text-gray-700 dark:prose-p:text-gray-300 
                      prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                      prose-strong:text-gray-900 dark:prose-strong:text-white
                      prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-900/10
                      prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50 dark:prose-code:bg-blue-900/20
                      prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800
                      prose-img:rounded-lg">
            @parsewidgets($body ?? '')
        </article>
        
        @if(isset($item->js))
        <script>{!! $item->js !!}</script>
        @endif
        
        @if(isset($item->css))
        <style>{!! $item->css !!}</style>
        @endif
        
        <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ $pageIndexUrl }}" 
               class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('page::front.general.all_pages') }}
            </a>
        </footer>
    </div>
</div>
@endif
@endsection