@extends('themes.blank.layouts.app')

{{-- V3 VARIANT: CORPORATE VARIANT - Simplified corporate variant page --}}

@section('module_content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
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

        {{-- HERO --}}
        <section class="bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    @if($featuredImage)
                        <div class="order-2 lg:order-1">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $title }}"
                                 class="rounded-lg shadow-2xl">
                        </div>
                    @endif

                    <div class="order-1 lg:order-2">
                        <span class="inline-block px-4 py-2 bg-blue-600/30 border border-blue-400/50 rounded text-sm font-semibold uppercase tracking-wider mb-4">
                            Varyant Modeli
                        </span>
                        <h1 class="text-4xl font-bold mb-4">{{ $title }}</h1>
                        @if($shortDescription)
                            <p class="text-xl text-blue-100 mb-6">{{ $shortDescription }}</p>
                        @endif
                        <a href="#iletisim" class="inline-block px-8 py-4 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition">
                            Bilgi Al
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- GALLERY --}}
        @if($galleryImages->count() > 0)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 border-b-4 border-blue-600 inline-block pb-2">Ürün Görselleri</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($galleryImages as $image)
                            <a href="{{ $image->getUrl() }}" class="glightbox block rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 transition" data-gallery="gallery">
                                <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                     alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                     class="w-full h-48 object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- USE CASES --}}
        @if(!empty($useCases))
            <section class="py-16 bg-gray-100 dark:bg-gray-900">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 border-b-4 border-blue-600 inline-block pb-2">Kullanım Alanları</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($useCases as $case)
                            @php
                                $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                            @endphp
                            <div class="flex items-start gap-3 p-4 bg-white dark:bg-gray-800 rounded-lg border-l-4 border-blue-600">
                                <i class="fa-solid fa-{{ $caseIcon }} text-blue-600 text-xl mt-1"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $caseText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- SIBLINGS --}}
        @if($siblingVariants->count() > 0)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 border-b-4 border-blue-600 inline-block pb-2">Diğer Varyantlar</h2>
                    <div class="grid md:grid-cols-4 gap-6">
                        @foreach($siblingVariants as $variant)
                            @php
                                $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                $variantImage = $variant->getFirstMedia('featured_image');
                            @endphp
                            <a href="{{ $variantUrl }}" class="group block bg-gray-50 dark:bg-gray-900/50 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 hover:border-blue-600 transition">
                                @if($variantImage)
                                    <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}"
                                         alt="{{ $variantTitle }}"
                                         class="w-full h-40 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 text-sm">{{ $variantTitle }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CONTACT --}}
        <section id="iletisim" class="py-16 bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 text-white">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <h2 class="text-3xl font-bold mb-8 text-center">İletişime Geçin</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6 bg-white/10 backdrop-blur-lg rounded-lg p-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <input type="text" name="name" placeholder="Ad Soyad *" required
                               class="px-4 py-3 bg-white/20 border-2 border-white/30 rounded text-white placeholder-white/70">
                        <input type="email" name="email" placeholder="E-posta *" required
                               class="px-4 py-3 bg-white/20 border-2 border-white/30 rounded text-white placeholder-white/70">
                    </div>

                    <input type="tel" name="phone" placeholder="Telefon *" required
                           class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded text-white placeholder-white/70">

                    <textarea name="message" rows="3" placeholder="Mesajınız"
                              class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded text-white placeholder-white/70"></textarea>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded transition">
                        Gönder
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
