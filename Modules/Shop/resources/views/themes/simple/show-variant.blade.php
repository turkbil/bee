@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

@section('module_content')
    <div class="min-h-screen bg-white dark:bg-gray-900">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $shortDescription = $item->getTranslated('short_description', $currentLocale);
            $longDescription = $item->getTranslated('body', $currentLocale);

            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
            $shopIndexUrl = $localePrefix . '/' . $indexSlug;

            $featuredImage = $item->getFirstMedia('featured_image');
            $galleryImages = $item->getMedia('gallery');

            $resolveLocalized = static function ($data) use ($currentLocale, $defaultLocale) {
                if (!is_array($data)) {
                    return $data;
                }
                return $data[$currentLocale] ?? ($data[$defaultLocale] ?? ($data['en'] ?? reset($data)));
            };

            // Use Cases (Varyanta Özel)
            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = array_values(array_filter($resolvedUseCases));
                }
            }

            // Product-based Variants (diğer varyantlar)
            $siblingVariants = $siblingVariants ?? collect();
            $parentProduct = $parentProduct ?? null;
            $isVariantPage = $isVariantPage ?? false;
        @endphp

        {{-- ℹ️ VARIANT INFO BOX --}}
        @if ($isVariantPage && $parentProduct)
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
        <section id="hero-section"
            class="relative bg-gradient-to-r from-blue-600 via-slate-800 to-slate-950 text-white overflow-hidden">
            {{-- Decorative elements --}}
            <div class="absolute top-0 left-0 w-full h-full opacity-15">
                <div
                    class="absolute top-20 right-20 w-96 h-96 bg-blue-400 rounded-full mix-blend-overlay filter blur-3xl animate-pulse">
                </div>
                <div class="absolute bottom-10 left-10 w-[500px] h-[500px] bg-slate-500 rounded-full mix-blend-overlay filter blur-3xl animate-pulse"
                    style="animation-delay: 2s;"></div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full mb-6">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-sm font-medium">Stokta Mevcut</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                            {{ $title }}
                        </h1>

                        @if ($shortDescription)
                            <p class="text-xl text-blue-100 leading-relaxed mb-8">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact"
                                class="inline-flex items-center justify-center gap-3 bg-white text-blue-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Teklif Al</span>
                            </a>
                            <a href="tel:02167553555"
                                class="inline-flex items-center justify-center gap-3 bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-700 transition-colors">
                                <i class="fa-solid fa-phone"></i>
                                <span>0216 755 3 555</span>
                            </a>
                        </div>
                    </div>

                    @if ($featuredImage)
                        <div class="hidden lg:block">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                alt="{{ $title }}" class="w-full rounded-lg ">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- 📑 TABLE OF CONTENTS --}}
        <div id="toc-bar"
            class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky z-50 transition-transform duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
                <div class="flex items-center">
                    <div
                        class="flex-1 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
                        <div class="flex gap-2" id="toc-buttons">
                            @if ($parentProduct)
                                <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale) }}"
                                    data-target="parent"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-arrow-left mr-1.5"></i>Ana Ürün
                                </a>
                            @endif
                            @if ($galleryImages->count() > 0)
                                <a href="#gallery" data-target="gallery"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-images mr-1.5"></i>Galeri
                                </a>
                            @endif
                            @if ($siblingVariants->count() > 0)
                                <a href="#variants" data-target="variants"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-layer-group mr-1.5"></i>Varyantlar
                                </a>
                            @endif
                            @if (!empty($useCases))
                                <a href="#usecases" data-target="usecases"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-bullseye mr-1.5"></i>Kullanım
                                </a>
                            @endif
                            <a href="#contact" data-target="contact"
                                class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-blue-600 dark:bg-blue-600 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-700 transition-all whitespace-nowrap">
                                <i class="fa-solid fa-envelope mr-1.5"></i>Teklif Al
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- 🎨 GALLERY --}}
            @if ($galleryImages->count() > 0)
                <section id="gallery" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Ürün Galerisi</h2>
                        <p class="text-gray-600 dark:text-gray-400">Ürünü farklı açılardan inceleyin</p>
                    </header>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach ($galleryImages as $index => $image)
                            @php
                                $spanClass = match ($index % 6) {
                                    0 => 'md:col-span-2 md:row-span-2',
                                    3 => 'md:col-span-2',
                                    default => '',
                                };
                            @endphp
                            <a href="{{ $image->getUrl() }}"
                                class="glightbox block relative overflow-hidden rounded-lg group {{ $spanClass }}"
                                data-gallery="shop-gallery">
                                <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                    alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div
                                    class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                    <i
                                        class="fa-solid fa-search-plus text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 📝 VARIANT MARKETING CONTENT --}}
            @if ($longDescription)
                <section class="py-16">
                    <div class="grid lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div
                                class="prose prose-base md:prose-lg max-w-none dark:prose-invert prose-headings:text-gray-900 dark:prose-headings:text-white prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-strong:text-gray-900 dark:prose-strong:text-white prose-a:text-blue-600 dark:prose-a:text-blue-400">
                                @parsewidgets($longDescription)
                            </div>
                        </div>
                        <div class="lg:col-span-1">
                            <div class="sticky top-24 space-y-6">
                                {{-- Quick Contact --}}
                                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-6 text-white">
                                    <h3 class="text-xl font-bold mb-4">Hızlı İletişim</h3>
                                    <div class="space-y-4">
                                        <a href="tel:02167553555"
                                            class="flex items-center gap-3 text-white hover:text-blue-100 transition-colors">
                                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-phone"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs opacity-80">Telefon</div>
                                                <div class="font-semibold">0216 755 3 555</div>
                                            </div>
                                        </a>
                                        <a href="mailto:info@ixtif.com"
                                            class="flex items-center gap-3 text-white hover:text-blue-100 transition-colors">
                                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs opacity-80">E-posta</div>
                                                <div class="font-semibold">info@ixtif.com</div>
                                            </div>
                                        </a>
                                    </div>
                                    <a href="#contact-form"
                                        class="mt-6 block w-full bg-white text-blue-700 font-bold py-3 rounded-lg text-center hover:bg-blue-50 transition-colors">
                                        Teklif Formu
                                    </a>
                                </div>

                                {{-- Ana Ürüne Dön --}}
                                @if ($parentProduct)
                                    <div
                                        class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-box text-blue-600"></i>
                                            Ana Ürün
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                            Detaylı teknik özellikler, SSS ve tüm bilgiler için ana ürün sayfasını ziyaret
                                            edin.
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
            @if (!empty($useCases))
                <section id="usecases" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">Bu Varyantın Kullanım
                            Alanları</h2>
                        <p class="text-gray-600 dark:text-gray-400">Bu varyantın öne çıktığı spesifik senaryolar</p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                        @foreach ($useCases as $case)
                            <div
                                class="flex items-start gap-4 bg-blue-50 dark:bg-gray-800 p-6 rounded-lg border border-blue-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-700 hover:shadow-lg transition-all group">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-blue-600 dark:bg-blue-700 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-check text-white text-xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed pt-2">{{ $case }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 🔀 PRODUCT-BASED VARIANTS SECTION --}}
            @if ($siblingVariants->count() > 0)
                <section id="variants" class="py-16">
                    <header class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                            <i class="fa-solid fa-layer-group text-blue-600 mr-3"></i>Diğer Varyantlar
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">İhtiyacınıza en uygun modeli keşfedin</p>
                    </header>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($siblingVariants as $variant)
                            @php
                                $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                $variantSlug = $variant->getTranslated('slug', $currentLocale);
                                $variantDescription = $variant->getTranslated('short_description', $currentLocale);
                                $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl(
                                    $variant,
                                    $currentLocale,
                                );

                                $variantImage = $variant->getFirstMedia('featured_image');
                                $variantImageUrl = $variantImage
                                    ? ($variantImage->hasGeneratedConversion('thumb')
                                        ? $variantImage->getUrl('thumb')
                                        : $variantImage->getUrl())
                                    : null;
                            @endphp
                            <a href="{{ $variantUrl }}"
                                class="group bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:border-blue-500 dark:hover:border-blue-600 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">

                                @if ($variantImageUrl)
                                    <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        <img src="{{ $variantImageUrl }}" alt="{{ $variantTitle }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                @endif

                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3
                                            class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $variantTitle }}
                                        </h3>
                                        <i
                                            class="fa-solid fa-arrow-right text-blue-600 dark:text-blue-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    </div>

                                    @if ($variantDescription)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {{ $variantDescription }}
                                        </p>
                                    @endif

                                    @if ($variant->variant_type)
                                        <div
                                            class="inline-flex items-center gap-2 text-xs font-semibold text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1.5 rounded-full mb-3">
                                            <i class="fa-solid fa-tag"></i>
                                            <span>{{ ucfirst(str_replace('-', ' ', $variant->variant_type)) }}</span>
                                        </div>
                                    @endif

                                    <div
                                        class="flex items-center gap-2 text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-slate-800 px-3 py-2 rounded border border-gray-200 dark:border-slate-700">
                                        <i class="fa-solid fa-barcode"></i>
                                        <span>{{ $variant->sku }}</span>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <span
                                            class="text-sm font-semibold text-blue-600 dark:text-blue-400 group-hover:underline">
                                            Detayları Görüntüle <i class="fa-solid fa-chevron-right ml-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>
    </div>

    {{-- Footer --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
            <a href="{{ $shopIndexUrl }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-semibold rounded-lg transition-all hover:shadow-lg">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Tüm Ürünlere Dön</span>
            </a>
        </div>
    </div>

    {{-- Trust Signals - Modern 4 Column (Before Contact Form) --}}
    <section id="trust-signals" class="relative mt-32 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl py-12 px-6 shadow-xl">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                    {{-- GARANTİLİ --}}
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-shield-halved text-4xl md:text-5xl text-green-400"></i>
                        <div>
                            <div class="font-bold text-base md:text-lg text-white">GARANTİLİ</div>
                            <div class="text-xs md:text-sm text-blue-100">Orijinal Ürün</div>
                        </div>
                    </div>

                    {{-- HIZLI TESLİMAT --}}
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-truck-fast text-4xl md:text-5xl text-blue-300"></i>
                        <div>
                            <div class="font-bold text-base md:text-lg text-white">HIZLI TESLİMAT</div>
                            <div class="text-xs md:text-sm text-blue-100">Türkiye Geneli</div>
                        </div>
                    </div>

                    {{-- 7/24 DESTEK --}}
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-headset text-4xl md:text-5xl text-purple-400"></i>
                        <div>
                            <div class="font-bold text-base md:text-lg text-white">7/24 DESTEK</div>
                            <div class="text-xs md:text-sm text-blue-100">Kesintisiz Hizmet</div>
                        </div>
                    </div>

                    {{-- SERTİFİKALI --}}
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-award text-4xl md:text-5xl text-yellow-400"></i>
                        <div>
                            <div class="font-bold text-base md:text-lg text-white">SERTİFİKALI</div>
                            <div class="text-xs md:text-sm text-blue-100">Uluslararası</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 📬 MODERN CONTACT FORM --}}
    <section id="contact" class="relative mt-32 overflow-hidden">
        {{-- Gradient Background with Pattern --}}
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-slate-900"></div>
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl">
            </div>
            <div
                class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-500 rounded-full mix-blend-overlay filter blur-3xl">
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                {{-- SOL: FORM (7/12) --}}
                <div class="w-full md:w-7/12">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 md:p-10">
                        <div class="mb-8">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                                Hemen Teklif Alın
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Özel fiyat teklifi için formu doldurun.
                            </p>
                        </div>
                        <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                            <input type="hidden" name="product_title" value="{{ $title }}">

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fa-solid fa-user text-blue-600 dark:text-blue-400"></i>
                                        Ad Soyad *
                                    </label>
                                    <input type="text" name="name" required placeholder="Adınız Soyadınız"
                                        class="w-full px-5 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/50 transition-all">
                                </div>
                                <div>
                                    <label
                                        class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fa-solid fa-envelope text-blue-600 dark:text-blue-400"></i>
                                        E-posta *
                                    </label>
                                    <input type="email" name="email" required placeholder="ornek@email.com"
                                        class="w-full px-5 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/50 transition-all">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fa-solid fa-phone text-blue-600 dark:text-blue-400"></i>
                                    Telefon *
                                </label>
                                <input type="tel" name="phone" required placeholder="0555 555 55 55"
                                    class="w-full px-5 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/50 transition-all">
                            </div>

                            <div>
                                <label
                                    class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                    <i class="fa-solid fa-message text-blue-600 dark:text-blue-400"></i>
                                    Mesajınız
                                </label>
                                <textarea name="message" rows="5" placeholder="Ürün hakkında merak ettiklerinizi yazabilirsiniz..."
                                    class="w-full px-5 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/50 transition-all resize-none"></textarea>
                            </div>

                            <button type="submit"
                                class="group relative w-full bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 hover:from-blue-600 hover:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 text-white font-bold py-5 rounded-xl transition-all hover:shadow-xl transform hover:-translate-y-0.5">
                                <span class="flex items-center justify-center gap-3">
                                    <i
                                        class="fa-solid fa-paper-plane text-xl group-hover:rotate-45 transition-transform"></i>
                                    <span class="text-lg">Teklif İsteyin</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- SAĞ: DETAYLAR (5/12) --}}
                <div class="w-full md:w-5/12 space-y-8">
                    {{-- İletişim Bilgileri --}}
                    <a href="tel:02167553555"
                        class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-blue-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-blue-400 hover:shadow-lg cursor-pointer">
                        <div
                            class="w-14 h-14 bg-white rounded-xl flex items-center justify-center flex-shrink-0 group-hover:shadow-md transition-all duration-300">
                            <i class="fa-solid fa-phone text-blue-600 text-xl group-hover:animate-pulse"></i>
                        </div>
                        <div>
                            <div class="text-sm text-blue-100 dark:text-blue-200 mb-1">Telefon</div>
                            <div class="text-xl font-bold text-white">0216 755 3 555</div>
                        </div>
                    </a>

                    <a href="{{ whatsapp_link() }}" target="_blank"
                        class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-green-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-green-400 hover:shadow-lg cursor-pointer">
                        <div
                            class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-green-600 transition-all duration-300">
                            <i class="fa-brands fa-whatsapp text-white text-xl group-hover:animate-bounce"></i>
                        </div>
                        <div>
                            <div class="text-sm text-blue-100 dark:text-blue-200 mb-1">WhatsApp</div>
                            <div class="text-xl font-bold text-white">0501 005 67 58</div>
                        </div>
                    </a>

                    <a href="mailto:info@ixtif.com"
                        class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-purple-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-purple-400 hover:shadow-lg cursor-pointer">
                        <div
                            class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-purple-600 transition-all duration-300">
                            <i class="fa-solid fa-envelope text-white text-xl group-hover:animate-pulse"></i>
                        </div>
                        <div>
                            <div class="text-sm text-blue-100 dark:text-blue-200 mb-1">E-posta</div>
                            <div class="text-lg font-bold text-white break-all">info@ixtif.com</div>
                        </div>
                    </a>

                    {{-- Info Box --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                        <div class="text-center mb-5">
                            <h3 class="text-2xl font-bold text-white mb-1">Türkiye'nin İstif Pazarı
                            </h3>
                            <p class="text-sm text-blue-100">Forklift ve İstif Makineleri Merkezi
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-box text-blue-200 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">Sıfır</div>
                            </div>
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-recycle text-green-300 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">İkinci El</div>
                            </div>
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-key text-yellow-300 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">Kiralık</div>
                            </div>
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-gears text-orange-300 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">Yedek Parça</div>
                            </div>
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-wrench text-purple-300 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">Teknik Servis</div>
                            </div>
                            <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                <i class="fa-solid fa-shield-halved text-red-300 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-white">Bakım</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Floating CTA (Dinamik - AI bot'a göre yer açar, contact section'da gizlenir) --}}
    <div x-data="{
            show: false,
            hideButton: false,
            rightPosition: '135px',
            updatePosition() {
                // Mobile'da sabit kal
                if (window.innerWidth < 1024) {
                    this.rightPosition = '40px';
                    return;
                }

                // Desktop'ta AI bot durumuna göre
                const aiChat = window.Alpine?.store('aiChat');
                if (aiChat?.floatingOpen) {
                    this.rightPosition = '435px'; // AI bot genişliği (384px) + 51px margin
                } else {
                    this.rightPosition = '135px'; // AI bot kapalıyken - sağda, AI button ile 30px boşluk
                }
            },
            init() {
                this.updatePosition();
                // AI bot durumunu izle
                this.$watch('$store.aiChat.floatingOpen', () => {
                    this.updatePosition();
                });

                // Contact section'ı izle - görününce butonu gizle
                const contactSection = document.getElementById('contact');
                if (contactSection) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            this.hideButton = entry.isIntersecting;
                        });
                    }, {
                        threshold: 0.1,
                        rootMargin: '0px 0px -100px 0px'
                    });
                    observer.observe(contactSection);
                }
            }
        }"
        @scroll.window="show = (window.pageYOffset > 800)"
        @resize.window="updatePosition()"
        x-show="show && !hideButton"
        x-transition
        :style="`right: ${rightPosition}`"
        class="fixed bottom-8 z-[60] hidden lg:block transition-all duration-300">
        <a href="#contact"
            class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-full shadow-2xl hover:shadow-3xl transition-all">
            <i class="fa-solid fa-envelope"></i>
            <span>Teklif Al</span>
        </a>
    </div>

@endsection
