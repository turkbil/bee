@props([
    'product',
    'layout' => 'vertical', // vertical | horizontal
    'showAddToCart' => true,
    'imageSize' => '400x400',
    'index' => null,
])

@php
    // Yuvarlama Fonksiyonu: Compare at price'ı sadece 00 veya 50'ye yuvarla
    // function_exists() ile sadece bir kez tanımla (multiple card'larda redeclare hatası önlenir)
    if (!function_exists('roundComparePrice')) {
        function roundComparePrice($price) {
            if (!$price) return null;

            $lastTwo = $price % 100;

            if ($lastTwo <= 24) {
                // 00'a yuvarla (aşağı)
                return floor($price / 100) * 100;
            }
            elseif ($lastTwo <= 74) {
                // 50'ye yuvarla
                return floor($price / 100) * 100 + 50;
            }
            else {
                // 00'a yuvarla (yukarı)
                return ceil($price / 100) * 100;
            }
        }
    }

    // Detect if product is array (homepage) or model (shop)
    $isArray = is_array($product);

    // Extract data based on type
    if ($isArray) {
        // Homepage format (array from PageController)
        $productId = $product['id'];
        $productTitle = $product['title'];
        $productUrl = $product['url'];
        $productImage = $product['image'];
        $productCategory = $product['category'] ?? 'Genel';
        $productCategoryIcon = $product['category_icon'] ?? 'fa-light fa-box';
        $productFormattedPrice = $product['formatted_price'];
        $productFeatured = $product['featured'] ?? false;

        // Currency data (from array - now includes exchange rate and TRY price)
        $productCurrencyCode = $product['currency'] ?? 'TRY';
        $productBasePrice = $product['price'] ?? 0;
        $productTryPrice = $product['try_price'] ?? null;
        $productExchangeRate = $product['exchange_rate'] ?? 1;

        // Old price (discount) data
        $productCompareAtPrice = $product['compare_at_price'] ?? null;

        // Yuvarlama uygula (00 veya 50)
        if ($productCompareAtPrice) {
            $productCompareAtPrice = roundComparePrice($productCompareAtPrice);
        }

        $productFormattedComparePrice = null;
        if ($productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productFormattedComparePrice = $product['currency_symbol'] ?? '₺';
            $productFormattedComparePrice = number_format($productCompareAtPrice, 0, ',', '.') . ' ' . $productFormattedComparePrice;
        }

        $productDiscountPercentage = null;
        if ($productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productDiscountPercentage = round((($productCompareAtPrice - $productBasePrice) / $productCompareAtPrice) * 100);
        }
    } else {
        // Shop format (ShopProduct model)
        $productId = $product->product_id;
        $productTitle = $product->getTranslated('title', app()->getLocale());
        $productUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product);
        $productImage = $product->hasMedia('featured_image')
            ? thumb($product->getFirstMedia('featured_image'), 400, 400, ['quality' => 85, 'scale' => 0, 'format' => 'webp'])
            : null;
        $productCategory = $product->category ? $product->category->getTranslated('title', app()->getLocale()) : 'Genel';
        $productCategoryIcon = $product->category->icon_class ?? 'fa-light fa-box';

        // Currency-aware price formatting
        $currencyRelation = null;
        if ($product->currency_id) {
            $currencyRelation = \Modules\Shop\App\Models\ShopCurrency::find($product->currency_id);
        }
        $productFormattedPrice = $currencyRelation
            ? $currencyRelation->formatPrice($product->base_price)
            : number_format($product->base_price, 0, ',', '.') . ' ₺';

        $productFeatured = $product->is_featured ?? false;

        // Currency conversion data
        $productCurrencyCode = $currencyRelation ? $currencyRelation->code : 'TRY';
        $productBasePrice = $product->base_price;
        $productExchangeRate = $currencyRelation ? $currencyRelation->exchange_rate : 1;
        $productTryPrice = $productCurrencyCode !== 'TRY'
            ? number_format($productBasePrice * $productExchangeRate, 0, ',', '.')
            : null;

        // Old price (discount) data
        $productCompareAtPrice = $product->compare_at_price ?? null;

        // Yuvarlama uygula (00 veya 50)
        if ($productCompareAtPrice) {
            $productCompareAtPrice = roundComparePrice($productCompareAtPrice);
        }

        $productDiscountPercentage = null;
        if ($productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productDiscountPercentage = round((($productCompareAtPrice - $productBasePrice) / $productCompareAtPrice) * 100);
        }

        $productFormattedComparePrice = null;
        if ($productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productFormattedComparePrice = $currencyRelation
                ? $currencyRelation->formatPrice($productCompareAtPrice)
                : number_format($productCompareAtPrice, 0, ',', '.') . ' ₺';
        }
    }

    // Layout classes
    $layoutClasses = $layout === 'horizontal'
        ? 'flex flex-row gap-4 p-4'
        : 'flex flex-col';

    $imageContainerClasses = $layout === 'horizontal'
        ? 'w-32 h-32 flex-shrink-0'
        : 'aspect-square';

    $contentClasses = $layout === 'horizontal'
        ? 'flex-1 flex flex-col justify-between'
        : 'p-3 md:p-4 lg:p-6 space-y-3 md:space-y-4 lg:space-y-5';

    // Grid responsive visibility (sadece homepage için)
    $visibilityClass = ($index === 8) ? 'hidden lg:block xl:hidden' : '';
@endphp

<div x-data="{
    priceHovered: false
}" class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all {{ $visibilityClass }}">

    <div class="{{ $layoutClasses }}">
        {{-- Product Image --}}
        <a href="{{ $productUrl }}" class="block {{ $imageContainerClasses }} rounded-xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600">
            @if($productImage)
                <img src="{{ $productImage }}"
                     alt="{{ $productTitle }}"
                     class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark"
                     loading="lazy">
            @else
                <i class="{{ $productCategoryIcon }} text-4xl md:text-6xl text-blue-400 dark:text-blue-400"></i>
            @endif
        </a>

        {{-- Content Section --}}
        <div class="{{ $contentClasses }}">
            {{-- Category Badge with Icon --}}
            <div class="flex items-center gap-2 {{ $layout === 'horizontal' ? 'mb-2' : 'mb-4' }}">
                <span class="flex items-center gap-1.5 text-xs text-blue-800 dark:text-blue-300 font-medium uppercase tracking-[0.1em]">
                    <i class="{{ $productCategoryIcon }} text-sm"></i>
                    {{ $productCategory }}
                </span>

                @if($productFeatured)
                    <span class="text-xs bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-2 py-0.5 rounded-full font-bold">
                        ⭐ Öne Çıkan
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <a href="{{ $productUrl }}">
                <h3 class="{{ $layout === 'horizontal' ? 'text-sm md:text-base font-semibold line-clamp-2' : 'text-base md:text-lg lg:text-xl font-bold line-clamp-2 min-h-[2.8rem] md:min-h-[3.2rem] lg:min-h-[3.5rem]' }} text-gray-950 dark:text-gray-50 leading-relaxed group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors tracking-wide">
                    {{ $productTitle }}
                </h3>
            </a>

            {{-- Price & Actions --}}
            <div class="{{ $layout === 'horizontal' ? 'flex items-center justify-between gap-4 mt-auto' : 'pt-3 md:pt-4 lg:pt-5 mt-auto border-t border-gray-300 dark:border-gray-500 flex items-center justify-between gap-3' }}">
                {{-- Price with Transform Effect (USD ⇄ TRY) + Old Price --}}
                <div class="flex-1 min-w-0">
                    {{-- Discount Badge (Foto üstünde) - sadece %10+ indirim varsa --}}
                    @if($productDiscountPercentage && $productDiscountPercentage >= 10)
                        <div class="absolute top-3 left-3 z-10 bg-gradient-to-br from-orange-600 to-red-600 text-white px-2.5 py-1 rounded-lg shadow-lg text-xs font-bold">
                            -%{{ $productDiscountPercentage }}
                        </div>
                    @endif

                    @if($productTryPrice && $productCurrencyCode !== 'TRY')
                        {{-- Old Price (üstü çapraz çizili) - varsa --}}
                        @if(isset($productFormattedComparePrice))
                            <div class="relative inline-block mb-1">
                                <span class="text-xs md:text-sm text-gray-400 dark:text-gray-500 font-medium">
                                    {{ $productFormattedComparePrice }}
                                </span>
                                {{-- Çapraz çizgi (diagonal line) --}}
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="w-full h-[1.5px] bg-gradient-to-r from-transparent via-red-500 to-transparent transform rotate-[-8deg] opacity-70"></span>
                                </span>
                            </div>
                        @endif

                        <div class="relative h-8 flex items-center"
                             @mouseenter="priceHovered = true"
                             @mouseleave="priceHovered = false">
                            {{-- USD Price (default) --}}
                            <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 transition-all duration-200 whitespace-nowrap"
                                 x-show="!priceHovered"
                                 x-transition:enter="transition ease-in duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-out duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95">
                                {{ $productFormattedPrice }}
                            </div>

                            {{-- TRY Price (hover) --}}
                            <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 dark:from-green-300 dark:via-emerald-300 dark:to-teal-300 absolute top-0 left-0 transition-all duration-150 whitespace-nowrap scale-105 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]"
                                 x-show="priceHovered"
                                 x-transition:enter="transition ease-in duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-105"
                                 x-transition:leave="transition ease-out duration-150"
                                 x-transition:leave-start="opacity-100 scale-105"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 style="display: none;">
                                {{ $productTryPrice }} ₺
                            </div>
                        </div>
                    @else
                        {{-- Old Price (üstü çapraz çizili) - varsa --}}
                        @if(isset($productFormattedComparePrice))
                            <div class="relative inline-block mb-1">
                                <span class="text-xs md:text-sm text-gray-400 dark:text-gray-500 font-medium">
                                    {{ $productFormattedComparePrice }}
                                </span>
                                {{-- Çapraz çizgi (diagonal line) --}}
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="w-full h-[1.5px] bg-gradient-to-r from-transparent via-red-500 to-transparent transform rotate-[-8deg] opacity-70"></span>
                                </span>
                            </div>
                        @endif

                        {{-- TRY Only Price --}}
                        <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 whitespace-nowrap h-8 flex items-center">
                            {{ $productFormattedPrice }}
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Button / Price Quote Button --}}
                @if($showAddToCart)
                    @php
                        // Fiyat 0 veya price_on_request kontrolü
                        $isPriceOnRequest = false;
                        if (!$isArray && isset($product->price_on_request)) {
                            $isPriceOnRequest = $product->price_on_request;
                        } elseif ($isArray && isset($product['price']) && $product['price'] == 0) {
                            $isPriceOnRequest = true;
                        }
                    @endphp

                    @if($isPriceOnRequest)
                        {{-- Price Quote Button --}}
                        <a href="{{ $productUrl }}"
                           class="flex-shrink-0 bg-gradient-to-br from-orange-700 to-red-700 hover:from-orange-800 hover:to-red-800 text-white rounded-lg shadow-md transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 hover:shadow-2xl hover:shadow-orange-500/50 active:scale-95">
                            <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:-rotate-12">
                                <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                            </span>
                            <span class="max-w-0 overflow-hidden group-hover:max-w-[5rem] transition-all duration-300 text-[10px] font-light pl-0 group-hover:pl-2 leading-[1.1] flex items-center">
                                <span class="whitespace-pre-line">Fiyat{{ "\n" }}Teklifi</span>
                            </span>
                        </a>
                    @else
                        {{-- Add to Cart Button --}}
                        <button
                            type="button"
                            x-data="{ loading: false, success: false }"
                            @click="
                                loading = true;
                                window.dispatchEvent(new CustomEvent('add-to-cart', {
                                    detail: {
                                        productId: {{ $productId }},
                                        quantity: 1
                                    }
                                }));
                                setTimeout(() => {
                                    loading = false;
                                    success = true;
                                    setTimeout(() => { success = false; }, 2000);
                                }, 500);
                            "
                            :disabled="loading || success"
                            class="flex-shrink-0 bg-gradient-to-br from-blue-700 to-purple-700 hover:from-blue-800 hover:to-purple-800 text-white rounded-lg shadow-md transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 hover:shadow-2xl hover:shadow-blue-500/50 active:scale-95 disabled:opacity-75 disabled:cursor-not-allowed"
                            :class="{ 'animate-pulse': loading, '!bg-gradient-to-br !from-green-600 !to-emerald-600': success }">
                            <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:-rotate-12 group-hover:scale-110">
                                <i class="fa-solid text-base transition-all duration-300"
                                   :class="{
                                       'fa-spinner fa-spin': loading,
                                       'fa-check scale-125': success,
                                       'fa-cart-plus': !loading && !success
                                   }"></i>
                            </span>
                            <span class="max-w-0 overflow-hidden group-hover:max-w-[3.5rem] transition-all duration-300 text-[11px] font-semibold pl-0 group-hover:pl-2 leading-tight flex items-center whitespace-nowrap">
                                <span x-text="loading ? 'Ekle' : (success ? 'Tamam!' : 'Ekle')"></span>
                            </span>
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
