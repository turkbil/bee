@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
<div x-data="homepage()" x-init="init()">
    <section class="py-8 md:py-12 lg:py-16 flex items-center relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-20 items-center">
            <!-- Left Content -->
            <div class="text-gray-900 dark:text-white">
                <!-- Main Title with Animation -->
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black mb-12 leading-[1.2] overflow-visible" style="font-weight: 900;">
                    <span class="gradient-animate block py-2">
                        TÜRKİYE'NİN
                    </span>
                    <span class="gradient-animate block py-2">
                        İSTİF PAZARI
                    </span>
                </h1>

                <!-- Description -->
                <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-200 mb-14 leading-relaxed font-medium">
                    Profesyonel istif çözümleri, güçlü stok ve hızlı teslimat ile işletmenizin güvenilir ortağı
                </p>

                <!-- CTA Button -->
                <div class="mb-16">
                    <a href="{{ route('shop.index') }}" class="group bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-full font-bold text-lg transition-all inline-block text-center shadow-lg hover:shadow-xl">
                        <i class="fa-light fa-shopping-cart mr-2 inline-block group-hover:scale-125 group-hover:rotate-12 transition-all duration-300"></i>
                        Ürünleri İncele
                    </a>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fa-light fa-boxes-stacked text-blue-600 dark:text-blue-300 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-base">Güçlü Stok</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Zengin ürün çeşidi</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fa-light fa-certificate text-blue-600 dark:text-blue-300 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-base">Garantili Ürün</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fa-light fa-award text-blue-600 dark:text-blue-300 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white text-base">Profesyonel Ekip</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Uzman danışmanlık</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 xl:hidden">
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
                <img src="https://ixtif.com/storage/tenant2/13/hero.png"
                     alt="iXtif İstif Makinesi - Forklift"
                     class="w-full h-auto object-contain"
                     loading="lazy">
            </div>
        </div>
    </div>

</section>

<!-- Featured Products Section -->
<section class="w-full py-8 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <div class="text-center mb-12">
        </div>

        @php
            $featuredProducts = \Modules\Shop\app\Models\ShopProduct::with(['category', 'brand', 'media'])
                ->where('is_active', true)
                ->where('show_on_homepage', true)
                ->whereNotNull('published_at')
                ->whereNull('parent_product_id')
                ->orderBy('sort_order', 'asc')
                ->orderByDesc('published_at')
                ->take(9)
                ->get();
        @endphp

        <!-- Product Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($featuredProducts as $index => $product)
            @php
                $productTitle = $product->getTranslated('title', app()->getLocale());
                $productImage = $product->getFirstMediaUrl('featured_image');
                $productSlug = $product->getTranslated('slug', app()->getLocale());

                // Slug varsa slug ile, yoksa ID ile route oluştur
                if($productSlug) {
                    $productUrl = route('shop.show', $productSlug);
                } else {
                    $productUrl = route('shop.show.by-id', $product->id);
                }
            @endphp
            <div class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all {{ $index === 8 ? 'hidden lg:block xl:hidden' : '' }}">
                <!-- Product Image -->
                <a href="{{ $productUrl }}" class="block aspect-square rounded-xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
                    @if($productImage)
                        <img src="{{ $productImage }}"
                             alt="{{ $productTitle }}"
                             class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark group-hover:scale-110 transition-transform duration-700">
                    @else
                        @php
                            $categoryIcon = $product->category?->icon_class ?? 'fa-light fa-box';
                        @endphp
                        <i class="{{ $categoryIcon }} text-6xl text-blue-400 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
                    @endif
                </a>

                <!-- Content Section -->
                <div class="p-3 md:p-4 lg:p-6 space-y-3 md:space-y-4 lg:space-y-5">
                    <!-- Category -->
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs text-blue-800 dark:text-blue-300 font-medium uppercase tracking-wider">
                            {{ $product->category?->getTranslated('title', app()->getLocale()) ?? 'Genel' }}
                        </span>
                    </div>

                    <!-- Title -->
                    <a href="{{ $productUrl }}">
                        <h3 class="text-base md:text-lg lg:text-xl font-bold text-gray-950 dark:text-gray-50 leading-relaxed line-clamp-2 min-h-[2.8rem] md:min-h-[3.2rem] lg:min-h-[3.5rem] group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors">
                            {{ $productTitle }}
                        </h3>
                    </a>

                    <!-- Price -->
                    @if(!$product->price_on_request && $product->base_price && $product->base_price > 0)
                    <div class="pt-3 md:pt-4 lg:pt-5 mt-auto border-t border-gray-300 dark:border-gray-500">
                        <div class="text-lg md:text-xl lg:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300">
                            {{ number_format($product->base_price, 2, ',', '.') }} {{ $product->currency ?? 'TRY' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Service Categories Section -->
<section class="w-full py-20 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
        <!-- Service Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
            <!-- 1. Satın Alma -->
            <a href="{{ route('shop.index') }}" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                    <i class="fa-light fa-shopping-cart text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Satın Alma</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3">Akülü forklift, dizel forklift, transpalet, reach truck ve ikinci el forklift satışı</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 2. Kiralama -->
            <a href="#" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                    <i class="fa-light fa-calendar-days text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Kiralama</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3">Kiralık forklift, transpalet ve otonom istif makinesi günlük-uzun dönem kiralaması</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 3. Yedek Parça -->
            <a href="#" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                    <i class="fa-light fa-gears text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:rotate-90 transition-all"></i>
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">Yedek Parça</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3">Forklift yedek parça, akülü forklift ve reach truck parçaları 7/24 stok garantili</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- 4. Teknik Servis -->
            <a href="#" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                    <i class="fa-light fa-wrench text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:rotate-12 group-hover:scale-110 transition-all"></i>
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">Teknik Servis</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3">Forklift bakım, periyodik bakım, profesyonel servis ve bakım anlaşmaları</p>
                    <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                        <span class="text-sm lg:text-base">Keşfet</span>
                        <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>
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
     class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] flex items-center justify-center p-4"
     @click="closeModal()"
     style="display: none;">

    <div @click.stop class="bg-white dark:bg-slate-800 rounded-2xl md:rounded-3xl max-w-5xl w-full max-h-[90vh] overflow-y-auto relative shadow-2xl"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-90"
         x-transition:enter-end="opacity-100 transform scale-100">

        <!-- Close Button -->
        <button @click="closeModal()" class="sticky top-4 right-4 float-right w-10 h-10 md:w-12 md:h-12 bg-gray-900/80 dark:bg-white/20 hover:bg-gray-900 dark:hover:bg-white/30 rounded-full flex items-center justify-center z-10 transition-colors backdrop-blur-sm">
            <i class="fa-solid fa-xmark text-xl md:text-2xl text-white"></i>
        </button>

        <div class="p-4 md:p-6 lg:p-12">
            <div class="grid lg:grid-cols-2 gap-6 md:gap-8 lg:gap-12" x-show="selectedProduct">
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
                                <i class="fa-light fa-box text-[8rem] lg:text-[12rem] text-blue-400 dark:text-blue-300"></i>
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
                    <div class="text-sm text-gray-800 dark:text-gray-200 mb-2">
                        <span x-text="selectedProduct?.category || 'Kategori'"></span>
                        <template x-if="selectedProduct?.brand">
                            <span> / <span x-text="selectedProduct.brand"></span></span>
                        </template>
                    </div>

                    <!-- Title -->
                    <h2 class="text-3xl lg:text-4xl font-black text-gray-950 dark:text-gray-50 mb-4" x-text="selectedProduct?.title || 'Ürün Adı'"></h2>

                    <!-- SKU -->
                    <div class="mb-6" x-show="selectedProduct?.sku">
                        <span class="text-sm text-gray-800 dark:text-gray-200">Ürün Kodu: </span>
                        <span class="text-sm font-semibold text-gray-950 dark:text-gray-50" x-text="selectedProduct?.sku"></span>
                    </div>

                    <!-- Short Description (Hero Text) -->
                    <p class="text-lg text-gray-900 dark:text-gray-100 mb-6 leading-relaxed" x-show="selectedProduct?.shortDescription" x-text="selectedProduct?.shortDescription"></p>

                    <!-- Primary Specs (4 Main Features) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6" x-show="selectedProduct?.primarySpecs && selectedProduct.primarySpecs.length > 0">
                        <template x-for="(spec, index) in selectedProduct?.primarySpecs?.slice(0, 4)" :key="index">
                            <div class="group relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/15 via-purple-500/15 to-pink-500/15 dark:from-blue-300/25 dark:via-purple-300/25 dark:to-pink-300/25 rounded-xl blur-lg group-hover:blur-xl transition-all"></div>
                                <div class="relative bg-white/70 dark:bg-white/10 backdrop-blur-md border border-gray-300/50 dark:border-gray-500/50 rounded-xl p-4 hover:bg-white/80 dark:hover:bg-white/15 transition-all">
                                    <!-- Icon + Label -->
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-light fa-bolt text-white dark:text-gray-950 text-sm"></i>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100" x-text="spec.label"></h4>
                                    </div>
                                    <!-- Value -->
                                    <div class="text-xl font-bold text-gray-950 dark:text-gray-50" x-text="spec.value"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4">
                        <a :href="selectedProduct?.url || '#'"
                           class="flex-grow bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 dark:hover:from-blue-300 dark:hover:via-purple-300 dark:hover:to-pink-300 text-white dark:text-gray-950 py-4 rounded-xl font-bold text-base lg:text-lg transition-all text-center">
                            <i class="fa-light fa-arrow-right mr-2"></i>
                            Ürün Sayfasına Git
                        </a>
                        <button class="w-14 h-14 lg:w-16 lg:h-16 border-2 border-gray-400 dark:border-gray-400 text-gray-700 dark:text-gray-200 rounded-xl flex items-center justify-center hover:border-red-700 hover:text-red-700 dark:hover:border-red-300 dark:hover:text-red-300 transition-colors">
                            <i class="fa-light fa-heart text-xl lg:text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Categories Section -->
<section class="py-20">
    <div class="container mx-auto px-4 sm:px-4 md:px-0">
        <div class="flex flex-wrap lg:flex-nowrap">
            <!-- 1. Satın Alma -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-blue-500 dark:via-blue-400 to-transparent"></div>
                <a href="{{ route('shop.index') }}" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-shopping-cart text-2xl md:text-4xl text-white group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Satın Alma</h3>
                    </div>
                </a>
            </div>

            <!-- 2. Kiralama -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-yellow-500 dark:via-yellow-400 to-transparent"></div>
                <a href="#" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-calendar-days text-2xl md:text-4xl text-white group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Kiralama</h3>
                    </div>
                </a>
            </div>

            <!-- 3. Yedek Parça -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-orange-500 dark:via-orange-400 to-transparent"></div>
                <a href="#" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-gears text-2xl md:text-4xl text-white group-hover:rotate-90 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Yedek Parça</h3>
                    </div>
                </a>
            </div>

            <!-- 4. Teknik Servis -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <a href="#" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-wrench text-2xl md:text-4xl text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Teknik Servis</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section - V3 Glassmorphism -->
<section class="py-20">
    <div class="container mx-auto px-4 sm:px-4 md:px-0">
        <!-- Contact Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Phone -->
            <a href="tel:02167553555" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                    <i class="fa-light fa-phone text-5xl lg:text-6xl text-white"></i>
                </div>
                <div class="max-md:flex-1">
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">Telefon</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3 max-md:hidden">Hemen arayın</p>
                    <p class="text-sm lg:text-base font-semibold text-blue-600 dark:text-blue-400">0216 755 3 555</p>
                </div>
            </a>

            <!-- WhatsApp -->
            <a href="https://wa.me/905010056758" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                    <i class="fa-brands fa-whatsapp text-5xl lg:text-6xl text-white"></i>
                </div>
                <div class="max-md:flex-1">
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">WhatsApp</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3 max-md:hidden">Anında mesajlaşın</p>
                    <p class="text-sm lg:text-base font-semibold text-green-600 dark:text-green-400">0501 005 67 58</p>
                </div>
            </a>

            <!-- Email -->
            <a href="mailto:info@ixtif.com" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                    <i class="fa-light fa-envelope text-5xl lg:text-6xl text-white"></i>
                </div>
                <div class="max-md:flex-1">
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">E-posta</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3 max-md:hidden">Mail gönderin</p>
                    <p class="text-sm lg:text-base font-semibold text-purple-600 dark:text-purple-400">info@ixtif.com</p>
                </div>
            </a>

            <!-- Live Chat -->
            <button @click="if(window.Alpine?.store('aiChat')?.openFloating) { window.Alpine.store('aiChat').openFloating(); } else { console.warn('AI Chat store not ready'); }" class="relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                <!-- AI Badge -->
                <div class="absolute top-2 right-2 md:top-4 md:right-4">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 md:px-3 md:py-1 bg-gradient-to-r from-cyan-500/30 to-blue-500/30 backdrop-blur-sm text-gray-900 dark:text-white text-[10px] md:text-xs font-bold rounded-full italic border border-cyan-300 dark:border-cyan-600">
                        <i class="fa-light fa-sparkles text-yellow-600 dark:text-yellow-400"></i>
                        Yapay Zeka
                    </span>
                </div>
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                    <i class="fa-light fa-robot text-5xl lg:text-6xl text-white"></i>
                </div>
                <div class="max-md:flex-1">
                    <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-3">Canlı Destek</h3>
                    <p class="text-xs lg:text-sm text-gray-600 dark:text-gray-400 mb-3 max-md:hidden">Yapay Zeka Destekli</p>
                    <p class="text-sm lg:text-base font-semibold text-orange-600 dark:text-orange-400">Sohbete Başla</p>
                </div>
            </button>
        </div>
    </div>
</section>

<!-- Contact Section - Service Categories Style (Alternative) -->
<section class="py-20 bg-gray-50/50 dark:bg-gray-900/20">
    <div class="container mx-auto px-4 sm:px-4 md:px-0">
        <div class="flex flex-wrap lg:flex-nowrap">
            <!-- 1. Telefon -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-blue-500 dark:via-blue-400 to-transparent"></div>
                <a href="tel:02167553555" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-phone text-5xl lg:text-6xl text-white group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">Telefon</h3>
                        <p class="text-xs lg:text-sm text-blue-600 dark:text-blue-400 font-semibold">0216 755 3 555</p>
                    </div>
                </a>
            </div>

            <!-- 2. WhatsApp -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-green-500 dark:via-green-400 to-transparent"></div>
                <a href="https://wa.me/905010056758" target="_blank" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-brands fa-whatsapp text-5xl lg:text-6xl text-white group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">WhatsApp</h3>
                        <p class="text-xs lg:text-sm text-green-600 dark:text-green-400 font-semibold">0501 005 67 58</p>
                    </div>
                </a>
            </div>

            <!-- 3. E-posta -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-purple-500 dark:via-purple-400 to-transparent"></div>
                <a href="mailto:info@ixtif.com" class="group block">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-envelope text-5xl lg:text-6xl text-white group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">E-posta</h3>
                        <p class="text-xs lg:text-sm text-purple-600 dark:text-purple-400 font-semibold">info@ixtif.com</p>
                    </div>
                </a>
            </div>

            <!-- 4. Canlı Destek -->
            <div class="w-1/2 md:w-1/2 lg:w-1/4 relative">
                <button @click="if(window.Alpine?.store('aiChat')?.openFloating) { window.Alpine.store('aiChat').openFloating(); } else { console.warn('AI Chat store not ready'); }" class="group block w-full">
                    <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center relative">
                        <!-- AI Badge -->
                        <div class="absolute top-2 right-2 md:top-4 md:right-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 md:px-2 md:py-1 bg-gradient-to-r from-cyan-500/30 to-blue-500/30 backdrop-blur-sm text-gray-900 dark:text-white text-[10px] font-bold rounded-full italic border border-cyan-300 dark:border-cyan-600">
                                <i class="fa-light fa-sparkles text-yellow-600 dark:text-yellow-400"></i>
                                AI
                            </span>
                        </div>
                        <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                            <i class="fa-light fa-robot text-5xl lg:text-6xl text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500"></i>
                        </div>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-900 dark:text-white whitespace-nowrap mb-2">Canlı Destek</h3>
                        <p class="text-xs lg:text-sm text-orange-600 dark:text-orange-400 font-semibold">Sohbete Başla</p>
                    </div>
                </button>
            </div>
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
@endsection
