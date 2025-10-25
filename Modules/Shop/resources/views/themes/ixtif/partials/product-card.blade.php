{{-- Product Card Component - iXtif Theme --}}
@php
    $viewMode = $viewMode ?? 'grid'; // Default to grid mode
    $productUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product);
@endphp

{{-- GRID MODE: Foto üstte, yazı altta (PC: 4 sütun) --}}
@if($viewMode === 'grid')
<div class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
    {{-- Product Image --}}
    <a href="{{ $productUrl }}"
       class="block aspect-square rounded-xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
        @php
            $productMedia = $product->hasMedia('featured_image')
                ? $product->getFirstMedia('featured_image')
                : ($product->hasMedia('gallery')
                    ? $product->getFirstMedia('gallery')
                    : null);
        @endphp

        @if($productMedia)
            <div class="w-full h-full p-4 md:p-6 flex items-center justify-center">
                <img src="{{ thumb($productMedia, 400, 400, ['quality' => 85, 'scale' => 0, 'format' => 'webp']) }}"
                     alt="{{ $product->getTranslated('title') }}"
                     class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark transition-transform duration-700"
                     loading="lazy"
                     decoding="async"
                     fetchpriority="low"
                     width="400"
                     height="400">
            </div>
        @else
            {{-- Fallback: Kategori ikonu --}}
            <div class="w-full h-full flex flex-col items-center justify-center gap-4 bg-gradient-to-br from-blue-100 via-purple-50 to-pink-100 dark:from-blue-900/20 dark:via-purple-900/20 dark:to-pink-900/20">
                @if($product->category && $product->category->icon_class)
                    {{-- Kategori ikonu büyük fallback --}}
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-400 dark:to-purple-500 rounded-3xl flex items-center justify-center shadow-2xl transition-all duration-500">
                        <i class="{{ $product->category->icon_class }} text-7xl text-white"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-600 dark:text-gray-400 text-center">
                        {{ $product->category->getTranslated('title') }}
                    </span>
                @else
                    {{-- Varsayılan box ikonu --}}
                    <div class="w-32 h-32 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 rounded-3xl flex items-center justify-center shadow-xl">
                        <i class="fa-light fa-box text-7xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                @endif
            </div>
        @endif
    </a>

    {{-- Content Section --}}
    <div class="p-4 md:p-4 lg:p-5 space-y-2.5 md:space-y-3 lg:space-y-3.5">
        {{-- Category with Icon --}}
        @if($product->category)
        <div class="flex items-center gap-1.5 mb-3 md:mb-3.5 lg:mb-4">
            @if($product->category->icon_class)
                <i class="{{ $product->category->icon_class }} text-blue-600 dark:text-blue-400 text-xs"></i>
            @endif
            <span class="text-[10px] md:text-[11px] text-blue-800 dark:text-blue-300 font-normal uppercase tracking-wider">
                {{ $product->category->getTranslated('title') }}
            </span>
        </div>
        @endif

        {{-- Title --}}
        <a href="{{ $productUrl }}">
            <h3 class="text-sm md:text-base lg:text-lg font-bold text-gray-950 dark:text-gray-50 leading-relaxed line-clamp-2 min-h-[2.8em] group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors">
                {{ $product->getTranslated('title') }}
            </h3>
        </a>

        {{-- Price --}}
        @if(!$product->price_on_request && $product->base_price && $product->base_price > 0)
            <div class="pt-3 md:pt-3.5 lg:pt-4 mt-auto border-t border-gray-200/60 dark:border-gray-700/60">
                <div class="text-base md:text-lg lg:text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300">
                    {{ number_format($product->base_price, 2, ',', '.') }} {{ $product->currency ?? 'TRY' }}
                </div>
            </div>
        @endif
    </div>
</div>
@endif

{{-- LIST MODE: Foto solda (col-4), yazı sağda (col-8) - PC: 2 sütun --}}
@if($viewMode === 'list')
<div class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
        {{-- Product Image - col-4 (md:col-span-4) --}}
        <div class="md:col-span-4">
            <a href="{{ $productUrl }}"
               class="block aspect-square flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600 h-full rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none">
                @php
                    $listProductMedia = $product->hasMedia('featured_image')
                        ? $product->getFirstMedia('featured_image')
                        : ($product->hasMedia('gallery')
                            ? $product->getFirstMedia('gallery')
                            : null);
                @endphp

                @if($listProductMedia)
                    <div class="w-full h-full p-4 md:p-6 flex items-center justify-center">
                        <img src="{{ thumb($listProductMedia, 400, 400, ['quality' => 85, 'scale' => 0, 'format' => 'webp']) }}"
                             alt="{{ $product->getTranslated('title') }}"
                             class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark transition-transform duration-700"
                             loading="lazy"
                             decoding="async"
                             fetchpriority="low"
                             width="400"
                             height="400">
                    </div>
                @else
                    {{-- Fallback: Kategori ikonu --}}
                    <div class="w-full h-full flex flex-col items-center justify-center gap-3 bg-gradient-to-br from-blue-100 via-purple-50 to-pink-100 dark:from-blue-900/20 dark:via-purple-900/20 dark:to-pink-900/20">
                        @if($product->category && $product->category->icon_class)
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-400 dark:to-purple-500 rounded-2xl flex items-center justify-center shadow-2xl">
                                <i class="{{ $product->category->icon_class }} text-5xl text-white"></i>
                            </div>
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 text-center">
                                {{ $product->category->getTranslated('title') }}
                            </span>
                        @else
                            <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 rounded-2xl flex items-center justify-center shadow-xl">
                                <i class="fa-light fa-box text-5xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                        @endif
                    </div>
                @endif
            </a>
        </div>

        {{-- Content Section - col-8 (md:col-span-8) --}}
        <div class="md:col-span-8 p-5 md:p-6 lg:p-7 flex flex-col justify-between">
            <div class="space-y-3 md:space-y-4">
                {{-- Category with Icon --}}
                @if($product->category)
                <div class="flex items-center gap-2 mb-3">
                    @if($product->category->icon_class)
                        <i class="{{ $product->category->icon_class }} text-blue-600 dark:text-blue-400 text-sm"></i>
                    @endif
                    <span class="text-xs md:text-sm text-blue-800 dark:text-blue-300 font-semibold uppercase tracking-wider">
                        {{ $product->category->getTranslated('title') }}
                    </span>
                </div>
                @endif

                {{-- Title --}}
                <a href="{{ $productUrl }}">
                    <h3 class="text-sm md:text-base lg:text-lg font-bold text-gray-950 dark:text-gray-50 leading-relaxed line-clamp-2 min-h-[2.8em] group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors">
                        {{ $product->getTranslated('title') }}
                    </h3>
                </a>

                {{-- Description (sadece list modda göster) --}}
                @if($product->getTranslated('body'))
                    <div class="text-sm md:text-base text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                        {!! strip_tags($product->getTranslated('body')) !!}
                    </div>
                @endif
            </div>

            {{-- Price --}}
            @if(!$product->price_on_request && $product->base_price && $product->base_price > 0)
                <div class="mt-4 md:mt-6 pt-4 border-t border-gray-200/60 dark:border-gray-700/60">
                    <div class="text-xl md:text-2xl lg:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300">
                        {{ number_format($product->base_price, 2, ',', '.') }} {{ $product->currency ?? 'TRY' }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endif
