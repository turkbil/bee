@extends('themes.blank.layouts.app')

@section('module_content')
    <div class="min-h-screen bg-white dark:bg-gray-900">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $shortDescription = $item->getTranslated('short_description', $currentLocale);
            $longDescription = $item->getTranslated('long_description', $currentLocale);

            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
            $shopIndexUrl = $localePrefix . '/' . $indexSlug;

            $featuredImage = $item->getFirstMedia('featured_image');
            $galleryImages = $item->getMedia('gallery');

            $resolveLocalized = static function ($data) use ($currentLocale, $defaultLocale) {
                if (!is_array($data)) return $data;
                return $data[$currentLocale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data);
            };

            // Use Cases (Varyanta Özel)
            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) $useCases = array_values(array_filter($resolvedUseCases));
            }

            // Product-based Variants (diğer varyantlar)
            $siblingVariants = $siblingVariants ?? collect();
            $parentProduct = $parentProduct ?? null;
            $isVariantPage = $isVariantPage ?? false;
        @endphp

        {{-- ℹ️ VARIANT INFO BOX --}}
        @if($isVariantPage && $parentProduct)
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-600 dark:border-blue-500">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-info-circle text-blue-600 dark:text-blue-400 text-xl"></i>
                        <div class="flex-1">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Bu bir varyant ürünüdür.</strong>
                                Ana ürün:
                                <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale) }}"
                                   class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">
                                    {{ $parentProduct->getTranslated('title', $currentLocale) }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 🎯 HERO SECTION --}}
        <section class="bg-blue-600 dark:bg-blue-700 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full mb-6">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-sm font-medium">Stokta Mevcut</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                            {{ $title }}
                        </h1>

                        @if($shortDescription)
                            <p class="text-xl text-blue-100 leading-relaxed mb-8">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact-form" class="inline-flex items-center justify-center gap-3 bg-white text-blue-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Teklif Al</span>
                            </a>
                            <a href="tel:02167553555" class="inline-flex items-center justify-center gap-3 bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-700 transition-colors">
                                <i class="fa-solid fa-phone"></i>
                                <span>0216 755 3 555</span>
                            </a>
                        </div>
                    </div>

                    @if($featuredImage)
                        <div class="hidden lg:block">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                 class="w-full rounded-lg shadow-2xl">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- 📑 TABLE OF CONTENTS --}}
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-start gap-3">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap pt-1.5">Hızlı Erişim:</span>
                    <div class="flex-1 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
                        <div class="flex gap-2 pb-2">
                            @if($parentProduct)
                                <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale) }}" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🏠 Ana Ürün</a>
                            @endif
                            @if($siblingVariants->count() > 0)
                                <a href="#variants" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🔀 Diğer Varyantlar</a>
                            @endif
                            @if($galleryImages->count() > 0)
                                <a href="#gallery" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">📸 Galeri</a>
                            @endif
                            @if(!empty($useCases))
                                <a href="#usecases" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🎯 Kullanım Alanları</a>
                            @endif
                            <a href="#contact-form" class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors whitespace-nowrap">✉️ Teklif Al</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 💪 TRUST SIGNALS --}}
        <section class="bg-gray-50 dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    @foreach([
                        ['label' => 'Orijinal Yedek Parça', 'icon' => 'gears'],
                        ['label' => 'Garantili Ürün', 'icon' => 'shield-halved'],
                        ['label' => '7/24 Teknik Destek', 'icon' => 'headset'],
                        ['label' => 'Hızlı Teslimat', 'icon' => 'truck-fast'],
                    ] as $stat)
                        <div class="space-y-3 group hover:scale-105 transition-transform">
                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto group-hover:bg-blue-600 dark:group-hover:bg-blue-800 transition-colors">
                                <i class="fa-solid fa-{{ $stat['icon'] }} text-blue-600 dark:text-blue-400 text-2xl group-hover:text-white transition-colors"></i>
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 font-semibold">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- 🎨 GALLERY --}}
            @if($galleryImages->count() > 0)
                <section id="gallery" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Ürün Galerisi</h2>
                        <p class="text-gray-600 dark:text-gray-400">Ürünü farklı açılardan inceleyin</p>
                    </header>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($galleryImages as $index => $image)
                            @php
                                $spanClass = match($index % 6) {
                                    0 => 'md:col-span-2 md:row-span-2',
                                    3 => 'md:col-span-2',
                                    default => '',
                                };
                            @endphp
                            <a href="{{ $image->getUrl() }}" class="glightbox block relative overflow-hidden rounded-lg group {{ $spanClass }}" data-gallery="shop-gallery">
                                <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                     alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <i class="fa-solid fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 📝 VARIANT MARKETING CONTENT --}}
            @if($longDescription)
                <section class="py-16">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="prose prose-base md:prose-lg max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-strong:text-gray-900 dark:prose-strong:text-white prose-a:text-blue-600 dark:prose-a:text-blue-400">
                                @parsewidgets($longDescription)
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="sticky top-24 space-y-6">
                                {{-- Quick Contact --}}
                                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 text-white">
                                    <h3 class="text-xl font-bold mb-4">Hızlı İletişim</h3>
                                    <div class="space-y-4">
                                        <a href="tel:02167553555" class="flex items-center gap-3 text-white hover:text-blue-100 transition-colors">
                                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-phone"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs opacity-80">Telefon</div>
                                                <div class="font-semibold">0216 755 3 555</div>
                                            </div>
                                        </a>
                                        <a href="mailto:info@ixtif.com" class="flex items-center gap-3 text-white hover:text-blue-100 transition-colors">
                                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs opacity-80">E-posta</div>
                                                <div class="font-semibold">info@ixtif.com</div>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="#contact-form" class="mt-6 block w-full bg-white text-blue-700 font-bold py-3 rounded-lg text-center hover:bg-blue-50 transition-colors">
                                        Teklif Formu
                                    </a>
                                </div>

                                {{-- Ana Ürüne Dön --}}
                                @if($parentProduct)
                                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-box text-blue-600"></i>
                                            Ana Ürün
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            Detaylı teknik özellikler, SSS ve tüm bilgiler için ana ürün sayfasını ziyaret edin.
                                        </p>
                                        <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale) }}"
                                           class="block w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold py-3 rounded-lg text-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fa-solid fa-arrow-left mr-2"></i>
                                            Ana Ürüne Git
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 🎯 USE CASES --}}
            @if(!empty($useCases))
                <section id="usecases" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Bu Varyantın Kullanım Alanları</h2>
                        <p class="text-gray-600 dark:text-gray-400">Bu varyantın öne çıktığı spesifik senaryolar</p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        @foreach($useCases as $case)
                            <div class="flex items-start gap-4 bg-blue-50 dark:bg-gray-800 p-6 rounded-lg border border-blue-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-700 hover:shadow-lg transition-all group">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 dark:bg-blue-700 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-check text-white text-xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed pt-2">{{ $case }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 🔀 PRODUCT-BASED VARIANTS SECTION --}}
            @if($siblingVariants->count() > 0)
                <section id="variants" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fa-solid fa-layer-group text-blue-600 mr-3"></i>Diğer Varyantlar
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">İhtiyacınıza en uygun modeli keşfedin</p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($siblingVariants as $variant)
                            @php
                                $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                $variantSlug = $variant->getTranslated('slug', $currentLocale);
                                $variantDescription = $variant->getTranslated('short_description', $currentLocale);
                                $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);

                                $variantImage = $variant->getFirstMedia('featured_image');
                                $variantImageUrl = $variantImage
                                    ? ($variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl())
                                    : null;
                            @endphp
                            <a href="{{ $variantUrl }}"
                               class="group bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:border-blue-500 dark:hover:border-blue-600 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                                @if($variantImageUrl)
                                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <img src="{{ $variantImageUrl }}"
                                             alt="{{ $variantTitle }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                @endif

                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $variantTitle }}
                                        </h3>
                                        <i class="fa-solid fa-arrow-right text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    </div>

                                    @if($variantDescription)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {{ $variantDescription }}
                                        </p>
                                    @endif

                                    @if($variant->variant_type)
                                        <div class="inline-flex items-center gap-2 text-xs font-semibold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 rounded-full mb-3">
                                            <i class="fa-solid fa-tag"></i>
                                            <span>{{ ucfirst(str_replace('-', ' ', $variant->variant_type)) }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2 text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 px-3 py-2 rounded border border-gray-200 dark:border-slate-700">
                                        <i class="fa-solid fa-barcode"></i>
                                        <span>{{ $variant->sku }}</span>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <span class="text-sm font-semibold text-blue-600 dark:text-blue-400 group-hover:underline">
                                            Detayları Görüntüle <i class="fa-solid fa-chevron-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 📧 CONTACT FORM --}}
            <section id="contact-form" class="py-16">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-8 md:p-12 border border-gray-200 dark:border-gray-700">
                    <header class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-paper-plane text-3xl text-blue-600 dark:text-blue-300"></i>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Teklif Al</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">Bu varyant için özel fiyat teklifi almak üzere formu doldurun</p>
                    </header>

                    <form action="{{ route('shop.quote.submit') }}" method="POST" class="max-w-3xl mx-auto space-y-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                        <input type="hidden" name="product_title" value="{{ $title }}">

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fa-solid fa-user mr-2 text-blue-600"></i>Ad Soyad *
                                </label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fa-solid fa-envelope mr-2 text-blue-600"></i>E-posta *
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fa-solid fa-phone mr-2 text-blue-600"></i>Telefon *
                            </label>
                            <input type="tel" id="phone" name="phone" required placeholder="0216 755 3 555"
                                   class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fa-solid fa-message mr-2 text-blue-600"></i>Mesajınız
                            </label>
                            <textarea id="message" name="message" rows="5" placeholder="Bu varyant hakkında detaylı bilgi almak istiyorum..."
                                      class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition-colors text-lg">
                            <i class="fa-solid fa-paper-plane mr-3"></i>Teklif İste
                        </button>
                    </form>
                </div>
            </section>

            {{-- FOOTER --}}
            <footer class="py-8 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ $shopIndexUrl }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>{{ __('shop::front.general.all_shops') }}</span>
                </a>
            </footer>
        </div>

        {{-- 🚀 FLOATING CTA --}}
        <div x-data="{ show: false }"
             @scroll.window="show = (window.pageYOffset > 800)"
             x-show="show"
             x-transition
             class="fixed bottom-8 right-8 z-50 hidden md:block">
            <a href="#contact-form"
               class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-full shadow-2xl transition-colors">
                <i class="fa-solid fa-envelope"></i>
                <span>Teklif Al</span>
            </a>
        </div>
    </div>
@endsection
