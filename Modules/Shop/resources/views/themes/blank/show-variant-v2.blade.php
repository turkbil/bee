@extends('themes.blank.layouts.app')

{{-- V2 VARIANT: E-COMMERCE STYLE - Product focused, sidebar sticky --}}

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
                $hasLocaleKeys = isset($data[$currentLocale]) || isset($data[$defaultLocale]) || isset($data['en']);
                if (!$hasLocaleKeys && array_is_list($data)) return $data;
                return $data[$currentLocale] ?? $data[$defaultLocale] ?? $data['en'] ?? reset($data);
            };

            $formatSpecValue = static function ($value) use (&$formatSpecValue) {
                if (is_bool($value)) return $value ? 'Var' : 'Yok';
                if (is_array($value)) {
                    if (isset($value['value']) && isset($value['unit'])) {
                        $note = $value['note'] ?? '';
                        $formatted = $value['value'] . ' ' . $value['unit'];
                        return $note ? $formatted . ' (' . $note . ')' : $formatted;
                    }
                    if (isset($value['unit'])) {
                        $unit = $value['unit'];
                        $parts = [];
                        foreach ($value as $key => $val) {
                            if ($key === 'unit') continue;
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal . ' ' . $unit;
                        }
                        return implode(' | ', $parts);
                    }
                    if (isset($value['min']) || isset($value['max'])) {
                        $parts = [];
                        if (isset($value['min'])) $parts[] = 'Min: ' . $value['min'];
                        if (isset($value['max'])) $parts[] = 'Max: ' . $value['max'];
                        $unit = $value['unit'] ?? '';
                        return implode(' | ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    if (count($value) > 1) {
                        $unit = $value['unit'] ?? '';
                        $parts = [];
                        foreach ($value as $key => $val) {
                            if ($key === 'unit') continue;
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal;
                        }
                        return implode(' × ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    return json_encode($value);
                }
                return (string) $value;
            };

            $primarySpecs = is_array($item->primary_specs) ? array_values(array_filter($item->primary_specs, fn($spec) => is_array($spec) && ($spec['label'] ?? false) && ($spec['value'] ?? false))) : [];

            $highlightedFeatures = collect($item->highlighted_features ?? [])
                ->map(fn($feature) => is_array($feature) ? [
                    'icon' => $feature['icon'] ?? 'bolt',
                    'title' => is_array($feature['title'] ?? null) ? $resolveLocalized($feature['title']) : ($feature['title'] ?? null),
                    'description' => is_array($feature['description'] ?? null) ? $resolveLocalized($feature['description']) : ($feature['description'] ?? null),
                ] : null)
                ->filter(fn($feature) => $feature && ($feature['title'] || $feature['description']));

            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = collect($resolvedUseCases)
                        ->map(fn($case) => is_array($case) && isset($case['text']) ? $case : ['icon' => 'check', 'text' => $case])
                        ->filter(fn($case) => !empty($case['text']))
                        ->values()
                        ->all();
                }
            }

            $faqEntries = collect($item->faq_data ?? [])
                ->map(fn($faq) => is_array($faq) ? [
                    'question' => $resolveLocalized($faq['question'] ?? null),
                    'answer' => $resolveLocalized($faq['answer'] ?? null),
                    'sort_order' => $faq['sort_order'] ?? 999,
                ] : null)
                ->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])
                ->sortBy('sort_order')
                ->values();

            $technicalSpecs = is_array($item->technical_specs) ? $item->technical_specs : [];

            $brandingInfo = null;
            $featuresList = [];
            if (is_array($item->features)) {
                $resolvedFeatures = $resolveLocalized($item->features) ?? [];
                if (is_array($resolvedFeatures)) {
                    if (array_is_list($resolvedFeatures)) {
                        $featuresList = collect($resolvedFeatures)
                            ->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])
                            ->filter(fn($f) => !empty($f['text']))
                            ->values()
                            ->all();
                    } else {
                        $featuresList = collect($resolvedFeatures['list'] ?? [])
                            ->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])
                            ->filter(fn($f) => !empty($f['text']))
                            ->values()
                            ->all();
                        $brandingInfo = $resolvedFeatures['branding'] ?? null;
                    }
                }
            }

            $competitiveAdvantages = [];
            if (is_array($item->competitive_advantages)) {
                $resolvedAdvantages = $resolveLocalized($item->competitive_advantages);
                if (is_array($resolvedAdvantages)) {
                    $competitiveAdvantages = collect($resolvedAdvantages)
                        ->map(fn($adv) => is_array($adv) && isset($adv['text']) ? $adv : ['icon' => 'star', 'text' => $adv])
                        ->filter(fn($adv) => !empty($adv['text']))
                        ->values()
                        ->all();
                }
            }

            $targetIndustries = [];
            if (is_array($item->target_industries)) {
                $resolvedIndustries = $resolveLocalized($item->target_industries);
                if (is_array($resolvedIndustries)) {
                    $targetIndustries = collect($resolvedIndustries)
                        ->map(fn($ind) => is_array($ind) && isset($ind['text']) ? $ind : ['icon' => 'briefcase', 'text' => $ind])
                        ->filter(fn($ind) => !empty($ind['text']))
                        ->values()
                        ->all();
                }
            }

            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }

            $accessories = [];
            if (is_array($item->accessories)) {
                $accessories = collect($item->accessories)
                    ->filter(fn($acc) => is_array($acc) && !empty($acc['name']))
                    ->values()
                    ->all();
            }

            $certifications = [];
            if (is_array($item->certifications)) {
                $certifications = collect($item->certifications)
                    ->filter(fn($cert) => is_array($cert) && !empty($cert['name']))
                    ->values()
                    ->all();
            }

            $siblingVariants = $siblingVariants ?? collect();
            $parentProduct = $parentProduct ?? null;
            $isVariantPage = $isVariantPage ?? false;
        @endphp

        {{-- HERO SECTION: E-commerce Product Header --}}
        <section class="bg-white dark:bg-gray-800 py-8 border-b dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-sm breadcrumbs mb-4">
                    <ul class="flex items-center gap-2 text-gray-500">
                        <li><a href="{{ $shopIndexUrl }}" class="hover:text-blue-600">Ürünler</a></li>
                        <li><i class="fa-solid fa-chevron-right text-xs"></i></li>
                        <li class="text-gray-900 dark:text-white">{{ $title }}</li>
                    </ul>
                </div>

                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-semibold rounded-full">Stokta</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- MAIN CONTENT: 2-Column Layout --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid lg:grid-cols-3 gap-8">

                {{-- LEFT: Product Images & Details (2/3) --}}
                <div class="lg:col-span-2 space-y-8">

                    {{-- Featured Image --}}
                    @if($featuredImage)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 flex items-center justify-center">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $title }}"
                                 class="max-w-full h-auto rounded-lg">
                        </div>
                    @endif

                    {{-- Gallery Thumbnails --}}
                    @if($galleryImages->count() > 0)
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($galleryImages->take(4) as $image)
                                <a href="{{ $image->getUrl() }}" class="glightbox block bg-white dark:bg-gray-800 rounded-lg overflow-hidden hover:ring-2 ring-blue-500 transition" data-gallery="gallery">
                                    <img src="{{ $image->hasGeneratedConversion('thumb') ? $image->getUrl('thumb') : $image->getUrl() }}"
                                         alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                         class="w-full h-24 object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Short Description --}}
                    @if($shortDescription)
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                            <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $shortDescription }}</p>
                        </div>
                    @endif

                    {{-- Use Cases --}}
                    @if(!empty($useCases))
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-bullseye text-blue-600"></i>
                                Kullanım Alanları
                            </h2>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($useCases as $case)
                                    @php
                                        $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                        $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                                    @endphp
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-{{ $caseIcon }} text-green-600 text-sm mt-1"></i>
                                        <span class="text-gray-700 dark:text-gray-300 text-sm">{{ $caseText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Gallery (Full) --}}
                    @if($galleryImages->count() > 4)
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Ürün Görselleri</h2>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach($galleryImages->skip(4) as $image)
                                    <a href="{{ $image->getUrl() }}" class="glightbox block rounded-lg overflow-hidden" data-gallery="gallery">
                                        <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                             alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                             class="w-full h-48 object-cover hover:scale-105 transition-transform">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- RIGHT: Sticky Sidebar (1/3) --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-4 space-y-6">

                        {{-- Contact Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-lg border-2 border-blue-500">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Hızlı Teklif Al</h3>
                            <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="hidden" name="product_title" value="{{ $title }}">

                                <input type="text" name="name" placeholder="Adınız Soyadınız" required
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">

                                <input type="email" name="email" placeholder="E-posta Adresiniz" required
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">

                                <input type="tel" name="phone" placeholder="Telefon Numaranız" required
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">

                                <textarea name="message" rows="3" placeholder="Mesajınız (opsiyonel)"
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"></textarea>

                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    Teklif İste
                                </button>
                            </form>

                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <a href="tel:02167553555" class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-700 font-semibold">
                                    <i class="fa-solid fa-phone"></i>
                                    0216 755 3 555
                                </a>
                            </div>
                        </div>

                        {{-- Primary Specs --}}
                        @if(!empty($primarySpecs))
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Teknik Özellikler</h3>
                                <div class="space-y-3">
                                    @foreach($primarySpecs as $spec)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $spec['label'] }}</span>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $spec['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Siblings --}}
                        @if($siblingVariants->count() > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Diğer Varyantlar</h3>
                                <div class="space-y-2">
                                    @foreach($siblingVariants->take(3) as $variant)
                                        @php
                                            $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                            $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                        @endphp
                                        <a href="{{ $variantUrl }}" class="block p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $variantTitle }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
