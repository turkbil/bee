@extends('themes.blank.layouts.app')

{{-- V1: MODERN & MINIMALIST - Variant Page --}}

@section('module_content')
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
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

            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) $useCases = array_values(array_filter($resolvedUseCases));
            }

            $siblingVariants = $siblingVariants ?? collect();
            $parentProduct = $parentProduct ?? null;
        @endphp

        {{-- PARENT PRODUCT LINK --}}
        @if($parentProduct)
            <div class="bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
                <div class="max-w-6xl mx-auto px-6 py-4">
                    <a href="{{ \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale) }}"
                       class="inline-flex items-center gap-2 text-blue-700 dark:text-blue-400 hover:underline">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Ana Ürün: {{ $parentProduct->getTranslated('title', $currentLocale) }}</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- HERO SECTION --}}
        <section class="bg-white dark:bg-gray-800 py-16 md:py-24">
            <div class="max-w-6xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    @if($featuredImage)
                        <div class="order-2 lg:order-1">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $title }}"
                                 class="w-full rounded-2xl shadow-sm">
                        </div>
                    @endif

                    <div class="order-1 lg:order-2">
                        <div class="inline-block px-4 py-1.5 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 text-sm font-medium rounded-full mb-6">
                            Varyant Ürün
                        </div>

                        <h1 class="text-4xl md:text-5xl font-light text-gray-900 dark:text-white mb-6 leading-tight">
                            {{ $title }}
                        </h1>

                        @if($shortDescription)
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-medium transition-all">
                                Teklif Al
                            </a>
                            <a href="tel:02167553555" class="inline-flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-4 rounded-xl font-medium hover:border-blue-600 dark:hover:border-blue-500 transition-all">
                                0216 755 3 555
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- GALLERY --}}
        @if($galleryImages->count() > 0)
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Galeri</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($galleryImages as $image)
                            <a href="{{ $image->getUrl() }}" class="glightbox block rounded-2xl overflow-hidden group" data-gallery="gallery">
                                <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                     alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                     class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- LONG DESCRIPTION --}}
        @if($longDescription)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-4xl mx-auto px-6 lg:px-8">
                    <div class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-light prose-p:text-gray-600 dark:prose-p:text-gray-400">
                        @parsewidgets($longDescription)
                    </div>
                </div>
            </section>
        @endif

        {{-- USE CASES --}}
        @if(!empty($useCases))
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Kullanım Alanları</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($useCases as $case)
                            <div class="flex items-start gap-4 p-6 bg-white dark:bg-gray-800 rounded-2xl">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 pt-2">{{ $case }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- OTHER VARIANTS --}}
        @if($siblingVariants->count() > 0)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Diğer Varyantlar</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($siblingVariants as $variant)
                            @php
                                $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                $variantImage = $variant->getFirstMedia('featured_image');
                            @endphp
                            <a href="{{ $variantUrl }}" class="group block p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                                @if($variantImage)
                                    <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}"
                                         alt="{{ $variantTitle }}"
                                         class="w-full h-48 object-cover rounded-xl mb-4">
                                @endif
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $variantTitle }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CONTACT FORM --}}
        <section id="contact" class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-3xl mx-auto px-6 lg:px-8">
                <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Teklif Al</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Ad Soyad *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Telefon *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 bg-white dark:bg-gray-800 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Mesajınız</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 bg-white dark:bg-gray-800 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 rounded-xl transition-colors">
                        Teklif İste
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
