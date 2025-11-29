@props([
    'product',
    'layout' => 'vertical', // vertical | horizontal
    'showAddToCart' => true,
    'showCategory' => true,
    'showDivider' => true,
    'compactImage' => false,
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

        // ✨ OTOMATIK İNDİRİM SİSTEMİ - Badge için SABİT yüzde (5, 10, 15, 20)
        $productDiscountPercentage = $product['auto_discount_percentage'] ?? null;

        // Yuvarlama uygula (00 veya 50)
        if ($productCompareAtPrice) {
            $productCompareAtPrice = roundComparePrice($productCompareAtPrice);
        }

        // Manuel compare_at_price varsa, gerçek indirim yüzdesini hesapla
        if (!$productDiscountPercentage && $productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productDiscountPercentage = round((($productCompareAtPrice - $productBasePrice) / $productCompareAtPrice) * 100);
        }

        $productFormattedComparePrice = null;
        if ($productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
            $productFormattedComparePrice = $product['currency_symbol'] ?? '₺';
            $productFormattedComparePrice = number_format($productCompareAtPrice, 0, ',', '.') . ' ' . $productFormattedComparePrice;
        }
    } else {
        // Shop format (ShopProduct model)
        $productId = $product->product_id;
        $productTitle = $product->getTranslated('title', app()->getLocale());
        $productUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($product);
        // 🎯 Helper function ile multi-collection fallback
        $productMedia = getFirstMediaWithFallback($product);
        $productImage = $productMedia
            ? thumb($productMedia, 400, 400, ['quality' => 85, 'scale' => 0, 'format' => 'webp'])
            : null;
        $productCategory = $product->category ? $product->category->getTranslated('title', app()->getLocale()) : 'Genel';
        $productCategoryIcon = $product->category->icon_class ?? 'fa-light fa-box';

        // Currency-aware price formatting (use eager loaded relation to avoid N+1)
        // NOTE: $product->currency is a STRING column ('USD'), not the relation!
        // We need to get the relation via getRelation() if loaded, otherwise load it
        $currencyRelation = null;
        if ($product->currency_id) {
            // Try to get loaded relation first (performance)
            if (method_exists($product, 'relationLoaded') && $product->relationLoaded('currency')) {
                $rel = $product->getRelation('currency');
                if ($rel instanceof \Modules\Shop\App\Models\ShopCurrency) {
                    $currencyRelation = $rel;
                }
            }

            // Fallback: Load it now if not loaded (N+1 but ensures it works)
            if (!$currencyRelation) {
                $currencyRelation = \Modules\Shop\App\Models\ShopCurrency::find($product->currency_id);
            }
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

        // ✨ OTOMATIK İNDİRİM SİSTEMİ (Model formatı için)
        $productDiscountPercentage = null;

        // Eğer compare_at_price yoksa veya base_price'dan küçükse, otomatik hesapla
        if (!$productCompareAtPrice || $productCompareAtPrice <= $productBasePrice) {
            // Hedef indirim yüzdesi (badge için - SABİT: %5, %10, %15, %20)
            $productDiscountPercentage = (($productId % 4) * 5 + 5);

            // Eski fiyatı hesapla (ters formül: old = new / (1 - discount))
            $productCompareAtPrice = $productBasePrice / (1 - ($productDiscountPercentage / 100));
        }

        // Yuvarlama uygula (00 veya 50)
        if ($productCompareAtPrice) {
            $productCompareAtPrice = roundComparePrice($productCompareAtPrice);
        }

        // Manuel compare_at_price varsa, gerçek indirim yüzdesini hesapla
        if (!$productDiscountPercentage && $productCompareAtPrice && $productCompareAtPrice > $productBasePrice) {
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
        ? 'w-28 h-28 md:w-36 md:h-36 flex-shrink-0'
        : 'aspect-square';

    $contentClasses = $layout === 'horizontal'
        ? 'flex-1 flex flex-col justify-between'
        : 'p-3 md:p-4 lg:p-6 space-y-3 md:space-y-4 lg:space-y-5';

    // Grid responsive visibility (sadece homepage için)
    $visibilityClass = ($index === 8) ? 'hidden lg:block xl:hidden' : '';

    // Badge sistemi - hem array hem model format destekli
    $productBadges = [];

    if ($isArray) {
        // Array formatı (homepage) - badges field'ını direkt al
        if (isset($product['badges']) && is_array($product['badges'])) {
            $productBadges = array_filter($product['badges'], function($badge) {
                return isset($badge['is_active']) && $badge['is_active'];
            });
            // Priority'ye göre sırala
            usort($productBadges, function($a, $b) {
                return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
            });
        }
    } else {
        // Model formatı (shop) - badge'leri filtrele (aktif olanlar)
        if (isset($product->badges) && is_array($product->badges)) {
            $productBadges = array_filter($product->badges, function($badge) {
                return isset($badge['is_active']) && $badge['is_active'];
            });
            // Priority'ye göre sırala
            usort($productBadges, function($a, $b) {
                return ($a['priority'] ?? 999) <=> ($b['priority'] ?? 999);
            });
        }
    }

    // Badge renk gradient map
    $badgeGradients = [
        'green' => 'from-emerald-600 to-green-600',
        'red' => 'from-red-600 to-rose-600',
        'orange' => 'from-orange-600 to-amber-600',
        'blue' => 'from-blue-600 to-cyan-600',
        'yellow' => 'from-yellow-500 to-amber-500',
        'purple' => 'from-purple-600 to-fuchsia-600',
        'emerald' => 'from-emerald-500 to-teal-600',
        'indigo' => 'from-indigo-600 to-blue-600',
        'cyan' => 'from-cyan-600 to-blue-500',
        'gray' => 'from-gray-600 to-slate-600',
    ];

    // Badge label helper
    $getBadgeLabel = function($badge) {
        $labels = [
            'new_arrival' => 'Yeni',
            'discount' => '%' . ($badge['value'] ?? '0') . ' İndirim',
            'limited_stock' => 'Son ' . ($badge['value'] ?? '0') . ' Adet',
            'free_shipping' => 'Ücretsiz Kargo',
            'bestseller' => 'Çok Satan',
            'featured' => 'Öne Çıkan',
            'eco_friendly' => 'Çevre Dostu',
            'warranty' => ($badge['value'] ?? '0') . ' Ay Garanti',
            'pre_order' => 'Ön Sipariş',
            'imported' => 'İthal',
            'custom' => $badge['label']['tr'] ?? 'Özel',
        ];
        return $labels[$badge['type']] ?? ($badge['label']['tr'] ?? 'Badge');
    };
@endphp

@once
<script>
document.addEventListener('alpine:init', () => {
    // Product card price component WITH add to cart
    Alpine.data('productCard', (hasTryPrice = false, productId = null) => ({
        priceHovered: false,
        showTryPrice: false,
        priceTimer: null,
        hasTryPrice: hasTryPrice,
        productId: productId,
        loading: false,
        success: false,
        init() {
            if (this.hasTryPrice) {
                this.startPriceCycle();
            }
        },
        startPriceCycle() {
            this.priceTimer = setInterval(() => {
                if (!this.priceHovered) {
                    this.showTryPrice = true;
                    setTimeout(() => {
                        if (!this.priceHovered) {
                            this.showTryPrice = false;
                        }
                    }, 1500); // TL: 1.5 saniye
                }
            }, 4500); // Döngü: 4.5 saniye (USD 3s + TRY 1.5s)
        },
        async addToCart() {
            console.log('🛒 Alpine: addToCart clicked', { productId: this.productId });
            this.loading = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // localStorage'dan cart_id al (varsa)
                const cartId = localStorage.getItem('cart_id');
                console.log('🛒 Alpine: Current cart_id from localStorage:', cartId);
                console.log('🛒 Alpine: Sending request to /api/cart/add');

                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        quantity: 1,
                        cart_id: cartId ? parseInt(cartId) : null
                    })
                });

                const data = await response.json();
                console.log('🛒 Alpine: API Response', data);

                if (data.success) {
                    this.success = true;

                    // API'den dönen cart_id'yi localStorage'a kaydet
                    if (data.data && data.data.cart_id) {
                        localStorage.setItem('cart_id', data.data.cart_id);
                        console.log('💾 Alpine: cart_id saved to localStorage:', data.data.cart_id);
                    }

                    // CartWidget'ı güncelle - Window event (daha güvenilir)
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { cartId: data.data?.cart_id, itemCount: data.data?.item_count }
                    }));
                    console.log('✅ Alpine: window.dispatchEvent(cart-updated)');

                    // Livewire dispatch (yedek)
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('cartUpdated');
                    }

                    setTimeout(() => { this.success = false; }, 2000);
                } else {
                    console.error('❌ Alpine: API returned error', data.message);
                    alert(data.message || 'Ürün sepete eklenirken hata oluştu');
                }
            } catch (error) {
                console.error('❌ Alpine: Fetch error', error);
                alert('Ürün sepete eklenirken hata oluştu');
            } finally {
                this.loading = false;
            }
        },
        destroy() {
            if (this.priceTimer) clearInterval(this.priceTimer);
        }
    }));

    // Add to cart button component
    Alpine.data('addToCartButton', (productId) => ({
        loading: false,
        success: false,
        async addToCart() {
            console.log('🛒 Alpine: addToCart clicked', { productId });
            this.loading = true;

            try {
                // CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // 🔑 localStorage'dan cart_id al (varsa)
                const cartId = localStorage.getItem('cart_id');
                console.log('🛒 Alpine: Current cart_id from localStorage:', cartId);

                console.log('🛒 Alpine: Sending request to /api/cart/add');

                const response = await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1,
                        cart_id: cartId ? parseInt(cartId) : null  // 🔑 cart_id gönder
                    })
                });

                const data = await response.json();
                console.log('🛒 Alpine: API Response', data);

                if (data.success) {
                    this.success = true;

                    // 🔑 API'den dönen cart_id'yi localStorage'a kaydet
                    if (data.data && data.data.cart_id) {
                        localStorage.setItem('cart_id', data.data.cart_id);
                        console.log('💾 Alpine: cart_id saved to localStorage:', data.data.cart_id);
                    }

                    // CartWidget'ı güncelle - Livewire event dispatch
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('cartUpdated');
                        console.log('✅ Alpine: Livewire.dispatch(cartUpdated) - Badge güncellenecek');
                    }

                    setTimeout(() => { this.success = false; }, 2000);
                } else {
                    console.error('❌ Alpine: API returned error', data.message);
                    alert(data.message || 'Ürün sepete eklenirken hata oluştu');
                }
            } catch (error) {
                console.error('❌ Alpine: Fetch error', error);
                alert('Ürün sepete eklenirken hata oluştu');
            } finally {
                this.loading = false;
            }
        }
    }));

    // 🔄 Sayfa yüklendiğinde localStorage cart_id ile sepeti senkronize et
    document.addEventListener('DOMContentLoaded', () => {
        const cartId = localStorage.getItem('cart_id');
        if (cartId && typeof Livewire !== 'undefined') {
            console.log('🔄 Page Init: Found cart_id in localStorage, refreshing CartWidget...', cartId);
            // CartWidget'ı refresh et (Livewire event)
            setTimeout(() => {
                Livewire.dispatch('cartUpdated');
                console.log('✅ Page Init: CartWidget refresh triggered');
            }, 500); // Livewire init bekle
        }
    });
});
</script>
@endonce

<div x-data="productCard({{ $productTryPrice ? 'true' : 'false' }}, {{ $productId }})" class="group relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-gray-200 dark:border-white/10 rounded-2xl overflow-hidden hover:bg-white/90 dark:hover:bg-white/10 hover:shadow-xl hover:border-blue-300 dark:hover:border-white/20 transition-all {{ $visibilityClass }}">

    <div class="{{ $layoutClasses }}">
        {{-- Product Image --}}
        <div class="relative {{ $imageContainerClasses }}">
            <a href="{{ $productUrl }}" class="block w-full h-full rounded-xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600 {{ $layout === 'horizontal' ? 'p-1' : ($compactImage ? 'p-2 md:p-4 lg:p-6' : 'p-2 md:p-8 lg:p-12') }}">
                @if($productImage)
                    <img src="{{ $productImage }}"
                         alt="{{ $productTitle }}"
                         class="w-full h-full object-contain drop-shadow-product-light dark:drop-shadow-product-dark"
                         loading="lazy">
                @else
                    <i class="{{ $productCategoryIcon }} text-4xl md:text-6xl text-blue-400 dark:text-blue-400"></i>
                @endif
            </a>

            {{-- Badges Container (Foto üstünde - sadece vertical layout) --}}
            @if($layout === 'vertical')
                <div class="absolute top-2 left-2 lg:top-3 lg:left-3 z-10 flex flex-col gap-1.5 lg:gap-2 items-start">
                    {{-- Discount Badge - sadece %5+ indirim varsa --}}
                    @if($productDiscountPercentage && $productDiscountPercentage >= 5)
                        <div class="w-fit bg-gradient-to-br from-orange-600 to-red-600 text-white px-1.5 py-0.5 lg:px-2.5 lg:py-1 rounded-lg shadow-lg text-xs font-bold">
                            -%{{ $productDiscountPercentage }}
                        </div>
                    @endif

                    {{-- Custom Badges --}}
                    @foreach($productBadges as $index => $badge)
                        @php
                            $badgeColor = $badge['color'] ?? 'gray';
                            $badgeGradient = $badgeGradients[$badgeColor] ?? 'from-gray-600 to-slate-600';
                            // İlk custom badge animasyonlu (priority 1 veya ilk sıradaki)
                            $isFirstBadge = $index === 0;
                            $animationClass = $isFirstBadge ? 'bg-[length:200%_200%] animate-gradient' : '';
                        @endphp
                        <div class="w-fit bg-gradient-to-br {{ $badgeGradient }} {{ $animationClass }} text-white px-1.5 py-0.5 lg:px-2.5 lg:py-1 rounded-lg shadow-lg text-xs font-bold">
                            {{ $getBadgeLabel($badge) }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Content Section --}}
        <div class="{{ $contentClasses }}">
            {{-- Category Badge with Icon + Custom Badges (Vertical: sağda, Horizontal: solda) --}}
            @if($showCategory || $layout === 'horizontal')
            <div class="flex items-center gap-2 {{ $layout === 'horizontal' ? 'mb-2' : 'mb-4 justify-end lg:justify-between' }}">
                {{-- Category: Mobilde gizli, tablet+ göster --}}
                @if($showCategory)
                <span class="hidden lg:flex items-center gap-1.5 text-xs text-blue-800 dark:text-blue-300 font-medium uppercase tracking-[0.1em]">
                    <i class="{{ $productCategoryIcon }} text-sm"></i>
                    {{ $productCategory }}
                </span>
                @endif

                {{-- Badges (Horizontal layout: sağda | Vertical: görsel üzerinde) --}}
                @if($layout === 'horizontal')
                    {{-- Mobilde: flex-row-reverse (indirim sağda), Desktop: normal --}}
                    <div class="flex items-center gap-1.5 lg:gap-2 flex-wrap justify-end flex-row-reverse lg:flex-row">
                        {{-- Discount Badge - sadece %5+ indirim varsa --}}
                        @if($productDiscountPercentage && $productDiscountPercentage >= 5)
                            <div class="w-fit bg-gradient-to-br from-orange-600 to-red-600 text-white px-1.5 py-0.5 lg:px-2.5 lg:py-1 rounded-lg shadow-lg text-xs font-bold">
                                -%{{ $productDiscountPercentage }}
                            </div>
                        @endif

                        {{-- Custom Badges --}}
                        @foreach($productBadges as $index => $badge)
                            @php
                                $badgeColor = $badge['color'] ?? 'gray';
                                $badgeGradient = $badgeGradients[$badgeColor] ?? 'from-gray-600 to-slate-600';
                                // İlk custom badge animasyonlu (priority 1 veya ilk sıradaki)
                                $isFirstBadge = $index === 0;
                                $animationClass = $isFirstBadge ? 'bg-[length:200%_200%] animate-gradient' : '';
                            @endphp
                            <div class="w-fit bg-gradient-to-br {{ $badgeGradient }} {{ $animationClass }} text-white px-1.5 py-0.5 lg:px-2.5 lg:py-1 rounded-lg shadow-lg text-xs font-bold">
                                {{ $getBadgeLabel($badge) }}
                            </div>
                        @endforeach
                    </div>
                @else
                    @if($productFeatured && $showCategory)
                        <span class="text-xs bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-2 py-0.5 rounded-full font-bold hidden lg:inline-block">
                            ⭐ Öne Çıkan
                        </span>
                    @endif
                @endif
            </div>
            @endif

            {{-- Title --}}
            <a href="{{ $productUrl }}">
                <h3 class="{{ $layout === 'horizontal' ? 'text-sm md:text-base font-semibold line-clamp-2' : 'text-base md:text-lg lg:text-xl font-bold line-clamp-2 min-h-[2.8rem] md:min-h-[3.2rem] lg:min-h-[3.5rem]' }} text-gray-950 dark:text-gray-50 leading-relaxed group-hover:text-blue-800 dark:group-hover:text-blue-300 transition-colors tracking-wide">
                    {{ $productTitle }}
                </h3>
            </a>

            {{-- Price & Actions --}}
            <div class="{{ $layout === 'horizontal' ? 'flex items-center justify-between gap-4 mt-auto' : 'pt-3 md:pt-4 lg:pt-5 mt-auto flex items-center justify-between gap-3' }} {{ $layout === 'vertical' && $showDivider ? 'border-t border-gray-300 dark:border-gray-500' : '' }}">
                {{-- Price with Transform Effect (USD ⇄ TRY) + Old Price --}}
                <div class="flex-1 min-w-0">
                    @if($productBasePrice <= 0)
                        {{-- Fiyatsız Ürün: "Teklif Alın" --}}
                        <a href="{{ url('/sizi-arayalim') }}"
                           @click="
                               localStorage.setItem('callMeBack_productId', '{{ $product->product_id ?? '' }}');
                               localStorage.setItem('callMeBack_productName', '{{ addslashes($product->getTranslated('title', app()->getLocale()) ?? '') }}');
                               localStorage.setItem('callMeBack_fromUrl', '{{ url()->current() }}');
                           "
                           class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 dark:from-green-300 dark:via-emerald-300 dark:to-teal-300 whitespace-nowrap h-8 flex items-center no-underline hover:scale-105 transition-transform duration-200">
                            Teklif Alın
                        </a>
                    @elseif($productTryPrice && $productCurrencyCode !== 'TRY')
                        {{-- Old Price (üstü çapraz çizili) - varsa --}}
                        @if(isset($productFormattedComparePrice))
                            <div class="relative inline-block -mb-4 lg:-mb-2">
                                <span class="text-xs lg:text-sm text-gray-400 dark:text-gray-500 font-medium leading-none">
                                    {{ $productFormattedComparePrice }}
                                </span>
                                {{-- Çapraz çizgi (diagonal line) --}}
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="w-full h-[1.5px] bg-gradient-to-r from-transparent via-red-500 to-transparent transform rotate-[-8deg] opacity-70"></span>
                                </span>
                            </div>
                        @endif

                        <div class="relative h-8 w-fit flex items-center cursor-pointer"
                             @mouseenter="priceHovered = true; showTryPrice = true"
                             @mouseleave="priceHovered = false; showTryPrice = false">
                            {{-- USD Price (default) --}}
                            <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 transition-all duration-150 whitespace-nowrap"
                                 x-show="!showTryPrice"
                                 x-transition:enter="transition ease-in duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-out duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95">
                                {{ $productFormattedPrice }}
                            </div>

                            {{-- TRY Price (hover ile gösterim) --}}
                            <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 dark:from-green-300 dark:via-emerald-300 dark:to-teal-300 absolute top-0 left-0 transition-all duration-150 scale-105 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)] whitespace-nowrap"
                                 x-show="showTryPrice"
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
                            <div class="relative inline-block -mb-4 lg:-mb-2">
                                <span class="text-xs lg:text-sm text-gray-400 dark:text-gray-500 font-medium leading-none">
                                    {{ $productFormattedComparePrice }}
                                </span>
                                {{-- Çapraz çizgi (diagonal line) --}}
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="w-full h-[1.5px] bg-gradient-to-r from-transparent via-red-500 to-transparent transform rotate-[-8deg] opacity-70"></span>
                                </span>
                            </div>
                        @endif

                        {{-- TRY Only Price --}}
                        <div class="{{ $layout === 'horizontal' ? 'text-base md:text-lg font-bold' : 'text-lg md:text-xl lg:text-2xl font-bold' }} text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 dark:from-blue-300 dark:via-purple-300 dark:to-pink-300 h-8 flex items-center whitespace-nowrap">
                            {{ $productFormattedPrice }}
                        </div>
                    @endif
                </div>

                {{-- Add to Cart Button / Price Quote Button --}}
                @if($showAddToCart)
                    @php
                        // Fiyatsız ürün kontrolü (Sadece fiyat 0 veya negatifse)
                        $hasNoPrice = ($productBasePrice <= 0);
                    @endphp

                    @if($hasNoPrice)
                        {{-- Sizi Arayalım Button (Fiyatsız ürünler için) --}}
                        <a href="{{ url('/sizi-arayalim') }}"
                           @click="
                               localStorage.setItem('callMeBack_productId', '{{ $product->product_id ?? '' }}');
                               localStorage.setItem('callMeBack_productName', '{{ addslashes($product->getTranslated('title', app()->getLocale()) ?? '') }}');
                               localStorage.setItem('callMeBack_fromUrl', '{{ url()->current() }}');
                           "
                           class="flex-shrink-0 bg-gradient-to-br from-green-700 to-emerald-700 hover:from-green-800 hover:to-emerald-800 text-white rounded-lg shadow-md transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 hover:shadow-2xl hover:shadow-green-500/50 active:scale-95">
                            <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                                <i class="fa-solid fa-phone text-base"></i>
                            </span>
                            <span class="max-w-0 overflow-hidden group-hover:max-w-[5rem] transition-all duration-300 text-[10px] font-semibold pl-0 group-hover:pl-2 leading-[1.1] flex items-center">
                                <span class="whitespace-pre-line">Sizi{{ "\n" }}Arayalım</span>
                            </span>
                        </a>
                    @else
                        {{-- Add to Cart Button --}}
                        <button
                            type="button"
                            @click="addToCart()"
                            :disabled="loading || success"
                            class="flex-shrink-0 border-2 border-blue-600 dark:border-blue-400 hover:border-blue-700 dark:hover:border-blue-300 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all duration-300 flex flex-row-reverse items-center gap-0 overflow-hidden h-10 min-w-[2.5rem] hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{ 'animate-pulse': loading, '!border-green-600 !text-green-600 !bg-green-50 dark:!bg-green-900/20': success }">
                            <span class="flex items-center justify-center w-10 h-10 flex-shrink-0 transition-transform duration-300 group-hover:-rotate-6 group-hover:scale-110">
                                <i class="fa-solid text-lg transition-all duration-300"
                                   :class="{
                                       'fa-spinner fa-spin': loading,
                                       'fa-check scale-125': success,
                                       'fa-cart-plus': !loading && !success
                                   }"></i>
                            </span>
                            <span class="max-w-0 overflow-hidden group-hover:max-w-[5rem] transition-all duration-300 text-[10px] font-medium pl-0 group-hover:pl-2 leading-[1.2] flex items-center text-center">
                                <span class="whitespace-pre-line block" x-html="loading ? 'Ekle' : (success ? 'Tamam!' : 'Sepete<br>Ekle')"></span>
                            </span>
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
