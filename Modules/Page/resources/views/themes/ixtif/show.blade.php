@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
@if(isset($is_homepage) && $is_homepage)
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
                        <div class="flex items-center gap-4 group cursor-pointer">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0 transition-all group-hover:bg-blue-100 dark:group-hover:bg-slate-600/50">
                                <i class="fa-light fa-boxes-stacked text-blue-600 dark:text-blue-300 text-xl transition-all"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base">Güçlü Stok</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Zengin ürün çeşidi</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group cursor-pointer">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0 transition-all group-hover:bg-blue-100 dark:group-hover:bg-slate-600/50">
                                <i class="fa-light fa-certificate text-blue-600 dark:text-blue-300 text-xl transition-all"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base">Garantili Ürün</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Teknik servis</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group cursor-pointer">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0 transition-all group-hover:bg-blue-100 dark:group-hover:bg-slate-600/50">
                                <i class="fa-light fa-award text-blue-600 dark:text-blue-300 text-xl transition-all"></i>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white text-base">Profesyonel Ekip</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Uzman danışmanlık</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 xl:hidden group cursor-pointer">
                            <div class="w-12 h-12 bg-blue-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center flex-shrink-0 transition-all group-hover:bg-blue-100 dark:group-hover:bg-slate-600/50">
                                <i class="fa-light fa-truck-fast text-blue-600 dark:text-blue-300 text-xl transition-all"></i>
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

    <!-- Categories Section -->
    <section class="w-full py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
            <!-- Category Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <!-- Forklift -->
                <a href="{{ route('shop.index') }}" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                    <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                        <i class="fa-light fa-warehouse text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Forklift</h3>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span class="text-sm lg:text-base">Keşfet</span>
                            <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Transpalet -->
                <a href="{{ route('shop.index') }}" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                    <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                        <i class="fa-light fa-dolly text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2">Transpalet</h3>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span class="text-sm lg:text-base">Keşfet</span>
                            <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- İstif Makinesi -->
                <a href="{{ route('shop.index') }}" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                    <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                        <i class="fa-light fa-boxes-stacked text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">İstif Makinesi</h3>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span class="text-sm lg:text-base">Keşfet</span>
                            <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- Reach Truck -->
                <a href="{{ route('shop.index') }}" class="group relative h-48 md:h-64 lg:h-80 rounded-3xl overflow-hidden transition-all duration-500 border-2 border-gray-200 dark:border-white/10 hover:border-blue-400 dark:hover:border-white/20 hover:shadow-xl">
                    <div class="relative h-full flex flex-col justify-end p-6 lg:p-8">
                        <i class="fa-light fa-truck-ramp-box text-5xl lg:text-6xl text-blue-400 dark:text-blue-300 mb-4 group-hover:scale-110 transition-all"></i>
                        <h3 class="text-xl lg:text-3xl font-bold text-gray-800 dark:text-white mb-2 whitespace-nowrap">Reach Truck</h3>
                        <div class="flex items-center text-gray-700 dark:text-gray-200 font-semibold">
                            <span class="text-sm lg:text-base">Keşfet</span>
                            <i class="fa-light fa-arrow-right ml-2 text-sm lg:text-base group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="w-full py-20 relative overflow-hidden">
        <div class="container mx-auto px-4 sm:px-4 md:px-0 relative z-10">
            <div class="text-center mb-12">
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
                <div class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl p-6 hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all cursor-pointer">
                    <!-- Product Image -->
                    <a href="{{ $productUrl }}" class="block aspect-square rounded-xl flex items-center justify-center mb-4 overflow-hidden bg-white/5 dark:bg-white/5">
                        @if($productImage)
                            <img src="{{ $productImage }}"
                                 alt="{{ $productTitle }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <i class="fa-light fa-box text-6xl text-blue-400 dark:text-blue-400 group-hover:scale-110 transition-transform"></i>
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
                        <i class="fa-light fa-eye mr-2"></i>
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
                <i class="fa-light fa-xmark text-2xl text-gray-900 dark:text-white"></i>
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
                                                <i class="fa-light fa-bolt text-white text-sm"></i>
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
                                <i class="fa-light fa-arrow-right mr-2"></i>
                                Ürün Sayfasına Git
                            </a>
                            <button class="w-14 h-14 lg:w-16 lg:h-16 border-2 border-gray-300 dark:border-gray-600 rounded-xl flex items-center justify-center hover:border-red-500 hover:text-red-500 transition-colors">
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
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
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
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
                    <div class="hidden md:block absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-yellow-500 dark:via-yellow-400 to-transparent"></div>
                    <a href="#" class="group block">
                        <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                            <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                                <i class="fa-light fa-calendar-days text-2xl md:text-4xl text-white group-hover:scale-110 transition-all duration-500"></i>
                            </div>
                            <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Kiralama</h3>
                        </div>
                    </a>
                </div>

                <!-- 3. İkinci El -->
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
                    <div class="hidden lg:block absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-green-500 dark:via-green-400 to-transparent"></div>
                    <a href="#" class="group block">
                        <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                            <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                                <i class="fa-light fa-recycle text-2xl md:text-4xl text-white group-hover:rotate-180 transition-all duration-500"></i>
                            </div>
                            <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">İkinci El</h3>
                        </div>
                    </a>
                </div>

                <!-- 4. Yedek Parça -->
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
                    <div class="hidden md:block absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-orange-500 dark:via-orange-400 to-transparent"></div>
                    <a href="#" class="group block">
                        <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                            <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                                <i class="fa-light fa-gears text-2xl md:text-4xl text-white group-hover:rotate-90 transition-all duration-500"></i>
                            </div>
                            <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Yedek Parça</h3>
                        </div>
                    </a>
                </div>

                <!-- 5. Teknik Servis -->
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
                    <div class="absolute right-0 top-0 bottom-0 w-[2px] bg-gradient-to-b from-transparent via-purple-500 dark:via-purple-400 to-transparent"></div>
                    <a href="#" class="group block">
                        <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg min-h-[140px] md:min-h-[180px] flex flex-col items-center justify-center">
                            <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                                <i class="fa-light fa-wrench text-2xl md:text-4xl text-white group-hover:rotate-12 group-hover:scale-110 transition-all duration-500"></i>
                            </div>
                            <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Teknik Servis</h3>
                        </div>
                    </a>
                </div>

                <!-- 6. Bakım Anlaşması -->
                <div class="w-1/2 md:w-1/3 lg:w-1/6 relative">
                    <a href="#" class="group block">
                        <div class="p-4 md:p-8 text-center transition-all hover:bg-gray-50/30 dark:hover:bg-gray-800/30 rounded-lg relative">
                            <div class="w-14 h-14 md:w-20 md:h-20 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center mx-auto mb-3 md:mb-6 transition-all duration-500">
                                <i class="fa-light fa-file-contract text-2xl md:text-4xl text-white group-hover:scale-110 transition-all duration-500"></i>
                            </div>
                            <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white whitespace-nowrap">Bakım Anlaşması</h3>
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
                        <i class="fa-light fa-phone text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="max-md:flex-1">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-3">Telefon</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-md:hidden">Hemen arayın</p>
                        <p class="text-base md:text-xl font-black text-blue-600 dark:text-blue-400">0216 755 3 555</p>
                    </div>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/905010056758" target="_blank" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                        <i class="fa-brands fa-whatsapp text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="max-md:flex-1">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-3">WhatsApp</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-md:hidden">Anında mesajlaşın</p>
                        <p class="text-base md:text-xl font-black text-green-600 dark:text-green-400">0501 005 67 58</p>
                    </div>
                </a>

                <!-- Email -->
                <a href="mailto:info@ixtif.com" class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-3xl p-6 md:p-8 hover:scale-105 hover:shadow-2xl hover:bg-white/90 dark:hover:bg-white/10 transition-all duration-300 text-center max-md:text-left group max-md:flex max-md:flex-row max-md:items-center max-md:gap-4">
                    <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-6 max-md:mx-0 max-md:mb-0 max-md:flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                        <i class="fa-light fa-envelope text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="max-md:flex-1">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-3">E-posta</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-md:hidden">Mail gönderin</p>
                        <p class="text-base md:text-xl font-black text-purple-600 dark:text-purple-400">info@ixtif.com</p>
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
                        <i class="fa-light fa-robot text-3xl md:text-4xl text-white"></i>
                    </div>
                    <div class="max-md:flex-1">
                        <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-3">Canlı Destek</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 max-md:hidden">Yapay Zeka Destekli</p>
                        <p class="text-base md:text-xl font-black text-orange-600 dark:text-orange-400">Sohbete Başla</p>
                    </div>
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
    
    <div class="container mx-auto py-12">
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