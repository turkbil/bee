@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

{{--
    SHOP INDEX V1: MINIMAL GRID
    - Clean, airy, lots of whitespace
    - Soft shadows, subtle animations
    - 3-column grid on desktop
    - Focus on product images
    - Soft color palette
--}}

@section('module_content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

    {{-- Hero Section: Minimal, Clean --}}
    <section class="relative overflow-hidden border-b border-gray-100 dark:border-gray-800">
        {{-- Subtle Background Pattern --}}
        <div class="absolute inset-0 opacity-30 dark:opacity-20">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-200 dark:bg-blue-900/30 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl animate-blob"></div>
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-200 dark:bg-purple-900/30 rounded-full mix-blend-multiply dark:mix-blend-soft-light filter blur-3xl animate-blob animation-delay-2000"></div>
        </div>

        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="max-w-4xl mx-auto text-center">
                {{-- Breadcrumb --}}
                <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                    <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Ana Sayfa</a>
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                    <span class="text-gray-900 dark:text-white font-medium">Ürünler</span>
                </div>

                {{-- Title --}}
                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 dark:text-white mb-6 tracking-tight">
                    Ürün <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Kataloğu</span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed mb-10">
                    1000+ profesyonel forklift ve istif makinesi. Depo ve lojistik çözümleriniz için en iyi ekipmanlar.
                </p>

                {{-- Search Bar --}}
                <div class="max-w-2xl mx-auto">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl blur opacity-0 group-hover:opacity-20 transition-opacity duration-500"></div>
                        <div class="relative flex items-center bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 overflow-hidden border border-gray-200 dark:border-gray-700">
                            <input
                                type="text"
                                placeholder="Ürün ara... (ör: Forklift, Transpalet, Reach Truck)"
                                class="flex-1 px-6 py-5 bg-transparent text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none"
                                x-data
                                @keyup.enter="$el.closest('form')?.submit()"
                            >
                            <button class="px-8 py-5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold transition-all duration-300 flex items-center gap-2">
                                <i class="fa-solid fa-search"></i>
                                <span class="hidden sm:inline">Ara</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="border-b border-gray-100 dark:border-gray-800 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">{{ $products->total() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Toplam Ürün</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600 mb-2">{{ \Modules\Shop\App\Models\ShopCategory::count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Kategori</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600 mb-2">100%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Stokta</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-purple-600 mb-2">24/7</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Destek</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Products Section --}}
    <section class="py-16 md:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filter & Sort Bar --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                {{-- Filters --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filtrele</span>
                    </button>
                    <button class="px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-500 dark:hover:border-purple-500 hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Kategoriler</span>
                    </button>
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Sırala:</span>
                    <select class="px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-500 focus:outline-none focus:border-blue-500 transition-all">
                        <option>En Yeni</option>
                        <option>En Popüler</option>
                        <option>Fiyat (Düşük-Yüksek)</option>
                        <option>Fiyat (Yüksek-Düşük)</option>
                        <option>İsim (A-Z)</option>
                    </select>
                </div>
            </div>

            {{-- Products Grid: 3 Columns, Clean & Minimal --}}
            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                    @foreach($products as $product)
                        @php
                            $currentLocale = app()->getLocale();
                            $title = $product->getTranslated('title', $currentLocale);
                            $shortDesc = $product->getTranslated('short_description', $currentLocale);
                            $featuredImage = $product->getFirstMedia('featured_image');

                            // URL oluştur
                            $showSlug = \App\Services\ModuleSlugService::getSlug('Shop', 'show');
                            $productSlug = is_array($product->slug) ? ($product->slug[$currentLocale] ?? $product->slug['tr'] ?? '') : $product->slug;
                            $defaultLocale = get_tenant_default_locale();
                            $dynamicUrl = $currentLocale === $defaultLocale
                                ? '/' . $showSlug . '/' . $productSlug
                                : '/' . $currentLocale . '/' . $showSlug . '/' . $productSlug;
                        @endphp

                        {{-- Product Card: Ultra Clean --}}
                        <article class="group relative bg-white dark:bg-gray-800 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-gray-200 dark:hover:border-gray-600 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/10 dark:hover:shadow-blue-500/20">
                            <a href="{{ $dynamicUrl }}" class="block">
                                {{-- Image Container --}}
                                <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                                    @if($featuredImage)
                                        <img
                                            src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                            alt="{{ $title }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                            loading="lazy"
                                        >
                                    @else
                                        {{-- Placeholder with gradient --}}
                                        <div class="w-full h-full flex items-center justify-center">
                                            <div class="text-center">
                                                <i class="fa-solid fa-box text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                                <p class="text-sm text-gray-400 dark:text-gray-500">Görsel Bekleniyor</p>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Hover Overlay --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                    {{-- Badges --}}
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($product->is_featured)
                                            <span class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-semibold rounded-lg shadow-lg backdrop-blur-sm">
                                                <i class="fa-solid fa-star mr-1"></i>Öne Çıkan
                                            </span>
                                        @endif
                                        @if($product->is_bestseller)
                                            <span class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg shadow-lg backdrop-blur-sm">
                                                <i class="fa-solid fa-fire mr-1"></i>Çok Satan
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Quick Action Buttons (appear on hover) --}}
                                    <div class="absolute top-4 right-4 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                                        <button class="w-10 h-10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-heart"></i>
                                        </button>
                                        <button class="w-10 h-10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-lg shadow-lg hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Content: Minimal Padding, Clean Typography --}}
                                <div class="p-6 space-y-4">
                                    {{-- Category Badge --}}
                                    @if($product->category)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wider">
                                                {{ $product->category->getTranslated('title', $currentLocale) }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Title --}}
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-tight line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $title }}
                                    </h3>

                                    {{-- Description --}}
                                    @if($shortDesc)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                            {{ strip_tags($shortDesc) }}
                                        </p>
                                    @endif

                                    {{-- Meta Info --}}
                                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                        @if($product->sku)
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-barcode"></i>
                                                {{ $product->sku }}
                                            </span>
                                        @endif
                                        @if($product->view_count)
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-eye"></i>
                                                {{ number_format($product->view_count) }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Price & CTA --}}
                                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        @if($product->price_on_request)
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">Fiyat İsteyin</span>
                                        @elseif($product->base_price)
                                            <div class="space-y-1">
                                                @if($product->compare_at_price && $product->compare_at_price > $product->base_price)
                                                    <div class="text-xs text-gray-400 line-through">
                                                        {{ number_format($product->compare_at_price, 0, ',', '.') }} ₺
                                                    </div>
                                                @endif
                                                <div class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                                    {{ number_format($product->base_price, 0, ',', '.') }} ₺
                                                </div>
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-2 text-sm font-semibold text-blue-600 dark:text-blue-400 group-hover:gap-3 transition-all">
                                            <span>Detay</span>
                                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination: Minimal & Clean --}}
                @if($products->hasPages())
                    <div class="mt-16 flex justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-2">
                            {{ $products->links() }}
                        </div>
                    </div>
                @endif

            @else
                {{-- Empty State: Gentle & Friendly --}}
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 rounded-3xl mb-6 shadow-xl">
                        <i class="fa-solid fa-box-open text-4xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Ürün Bulunamadı</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8">
                        Aradığınız kriterlere uygun ürün bulamadık. Filtreleri değiştirip tekrar deneyin.
                    </p>
                    <button class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all">
                        <i class="fa-solid fa-rotate-right mr-2"></i>
                        Filtreleri Temizle
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA Section: Simple & Effective --}}
    <section class="border-t border-gray-100 dark:border-gray-800 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-900 dark:to-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                    Aradığınız Ürünü Bulamadınız mı?
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto">
                    Uzman ekibimiz size özel çözümler üretmek için hazır. Hemen iletişime geçin.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/iletisim" class="px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-xl hover:shadow-2xl transition-all flex items-center gap-2">
                        <i class="fa-solid fa-envelope"></i>
                        <span>İletişime Geç</span>
                    </a>
                    <a href="tel:+908503333333" class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all border border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <i class="fa-solid fa-phone"></i>
                        <span>0850 333 33 33</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- Custom Animations --}}
<style>
@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    25% { transform: translate(20px, -20px) scale(1.1); }
    50% { transform: translate(-20px, 20px) scale(0.9); }
    75% { transform: translate(20px, 20px) scale(1.05); }
}

.animate-blob {
    animation: blob 20s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}
</style>
@endsection
