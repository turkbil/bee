@extends('themes.blank.layouts.app')

{{-- V6: HYBRID - V4/V1 Content + V2 Sticky Sidebar --}}

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
                if (!is_array($data)) {
                    return $data;
                }
                $hasLocaleKeys = isset($data[$currentLocale]) || isset($data[$defaultLocale]) || isset($data['en']);
                if (!$hasLocaleKeys && array_is_list($data)) {
                    return $data;
                }
                return $data[$currentLocale] ?? ($data[$defaultLocale] ?? ($data['en'] ?? reset($data)));
            };

            $formatSpecValue = static function ($value) use (&$formatSpecValue) {
                if (is_bool($value)) {
                    return $value ? 'Var' : 'Yok';
                }
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
                            if ($key === 'unit') {
                                continue;
                            }
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal . ' ' . $unit;
                        }
                        return implode(' | ', $parts);
                    }
                    if (isset($value['min']) || isset($value['max'])) {
                        $parts = [];
                        if (isset($value['min'])) {
                            $parts[] = 'Min: ' . $value['min'];
                        }
                        if (isset($value['max'])) {
                            $parts[] = 'Max: ' . $value['max'];
                        }
                        $unit = $value['unit'] ?? '';
                        return implode(' | ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    if (count($value) > 1) {
                        $unit = $value['unit'] ?? '';
                        $parts = [];
                        foreach ($value as $key => $val) {
                            if ($key === 'unit') {
                                continue;
                            }
                            $formattedVal = is_array($val) ? $formatSpecValue($val) : $val;
                            $parts[] = ucfirst($key) . ': ' . $formattedVal;
                        }
                        return implode(' × ', $parts) . ($unit ? ' ' . $unit : '');
                    }
                    return json_encode($value);
                }
                return (string) $value;
            };

            $primarySpecs = is_array($item->primary_specs)
                ? array_values(
                    array_filter(
                        $item->primary_specs,
                        fn($spec) => is_array($spec) && ($spec['label'] ?? false) && ($spec['value'] ?? false),
                    ),
                )
                : [];
            $highlightedFeatures = collect($item->highlighted_features ?? [])
                ->map(
                    fn($feature) => is_array($feature)
                        ? [
                            'icon' => $feature['icon'] ?? 'bolt',
                            'title' => is_array($feature['title'] ?? null)
                                ? $resolveLocalized($feature['title'])
                                : $feature['title'] ?? null,
                            'description' => is_array($feature['description'] ?? null)
                                ? $resolveLocalized($feature['description'])
                                : $feature['description'] ?? null,
                        ]
                        : null,
                )
                ->filter(fn($feature) => $feature && ($feature['title'] || $feature['description']));
            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = collect($resolvedUseCases)
                        ->map(
                            fn($case) => is_array($case) && isset($case['text'])
                                ? $case
                                : ['icon' => 'check', 'text' => $case],
                        )
                        ->filter(fn($case) => !empty($case['text']))
                        ->values()
                        ->all();
                }
            }
            $technicalSpecs = is_array($item->technical_specs) ? $item->technical_specs : [];
            $featuresList = [];
            if (is_array($item->features)) {
                $resolvedFeatures = $resolveLocalized($item->features) ?? [];
                if (is_array($resolvedFeatures)) {
                    if (array_is_list($resolvedFeatures)) {
                        $featuresList = collect($resolvedFeatures)
                            ->map(
                                fn($f) => is_array($f) && isset($f['text'])
                                    ? $f
                                    : ['icon' => 'check-circle', 'text' => $f],
                            )
                            ->filter(fn($f) => !empty($f['text']))
                            ->values()
                            ->all();
                    } else {
                        $featuresList = collect($resolvedFeatures['list'] ?? [])
                            ->map(
                                fn($f) => is_array($f) && isset($f['text'])
                                    ? $f
                                    : ['icon' => 'check-circle', 'text' => $f],
                            )
                            ->filter(fn($f) => !empty($f['text']))
                            ->values()
                            ->all();
                    }
                }
            }
            $competitiveAdvantages = [];
            if (is_array($item->competitive_advantages)) {
                $resolvedAdvantages = $resolveLocalized($item->competitive_advantages);
                if (is_array($resolvedAdvantages)) {
                    $competitiveAdvantages = collect($resolvedAdvantages)
                        ->map(
                            fn($adv) => is_array($adv) && isset($adv['text'])
                                ? $adv
                                : ['icon' => 'star', 'text' => $adv],
                        )
                        ->filter(fn($adv) => !empty($adv['text']))
                        ->values()
                        ->all();
                }
            }
            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }


            // Target Industries (icon destekli format)
            $targetIndustries = [];
            if (is_array($item->target_industries)) {
                $resolvedIndustries = $resolveLocalized($item->target_industries);
                if (is_array($resolvedIndustries)) {
                    $targetIndustries = collect($resolvedIndustries)
                        ->map(
                            fn($ind) => is_array($ind) && isset($ind['text'])
                                ? $ind
                                : ['icon' => 'briefcase', 'text' => $ind],
                        )
                        ->filter(fn($ind) => !empty($ind['text']))
                        ->values()
                        ->all();
                }
            }

            // Accessories
            $accessories = [];
            if (is_array($item->accessories)) {
                $accessories = collect($item->accessories)
                    ->filter(fn($acc) => is_array($acc) && !empty($acc['name']))
                    ->values()
                    ->all();
            }

            // Certifications
            $certifications = [];
            if (is_array($item->certifications)) {
                $certifications = collect($item->certifications)
                    ->filter(fn($cert) => is_array($cert) && !empty($cert['name']))
                    ->values()
                    ->all();
            }

            // FAQ Entries
            $faqEntries = collect($item->faq_data ?? [])
                ->map(
                    fn($faq) => is_array($faq)
                        ? [
                            'question' => $resolveLocalized($faq['question'] ?? null),
                            'answer' => $resolveLocalized($faq['answer'] ?? null),
                            'sort_order' => $faq['sort_order'] ?? 999,
                        ]
                        : null,
                )
                ->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])
                ->sortBy('sort_order')
                ->values();

            $siblingVariants = $siblingVariants ?? collect();
        @endphp

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
                            @if ($galleryImages->count() > 0)
                                <a href="#gallery" data-target="gallery"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-images mr-1.5"></i>Galeri
                                </a>
                            @endif
                            @if ($longDescription)
                                <a href="#description" data-target="description"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-align-left mr-1.5"></i>Açıklama
                                </a>
                            @endif
                            @if (!empty($primarySpecs))
                                <a href="#primary-specs" data-target="primary-specs"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-list-check mr-1.5"></i>Özellikler
                                </a>
                            @endif
                            @if ($highlightedFeatures->isNotEmpty())
                                <a href="#highlighted" data-target="highlighted"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-star mr-1.5"></i>Öne Çıkanlar
                                </a>
                            @endif
                            @if (!empty($featuresList))
                                <a href="#features" data-target="features"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-check-circle mr-1.5"></i>Avantajlar
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
                            @if (!empty($competitiveAdvantages))
                                <a href="#competitive" data-target="competitive"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-trophy mr-1.5"></i>Avantaj
                                </a>
                            @endif
                            @if (!empty($technicalSpecs))
                                <a href="#technical" data-target="technical"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-cogs mr-1.5"></i>Teknik
                                </a>
                            @endif
                            @if (!empty($targetIndustries))
                                <a href="#industries" data-target="industries"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-briefcase mr-1.5"></i>Sektörler
                                </a>
                            @endif
                            @if ($faqEntries->isNotEmpty())
                                <a href="#faq" data-target="faq"
                                    class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-question-circle mr-1.5"></i>SSS
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
            <div class="grid lg:grid-cols-3 gap-8 items-start">
                {{-- LEFT: Main Content (2/3) --}}
                <div class="lg:col-span-2 min-h-screen">

                    {{-- Gallery --}}
                    @if ($galleryImages->count() > 0)
                        <section id="gallery" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Ürün Görselleri</h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @foreach ($galleryImages as $image)
                                    <a href="{{ $image->getUrl() }}"
                                        class="glightbox block rounded-xl overflow-hidden hover:ring-2 ring-blue-500 dark:ring-blue-400 transition-all hover:scale-105"
                                        data-gallery="prod">
                                        <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                            alt="" class="w-full h-48 object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Long Description --}}
                    @if ($longDescription)
                        <section id="description" class="scroll-mt-24 mb-20 lg:mb-24">
                            <div class="prose prose-lg max-w-none dark:prose-invert prose-section-spacing">
                                @parsewidgets($longDescription)
                            </div>
                        </section>
                    @endif

                    {{-- Primary Specs (moved from sticky bar) --}}
                    @if (!empty($primarySpecs))
                        <section id="primary-specs" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Temel Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($primarySpecs as $spec)
                                    <div
                                        class="flex justify-between items-center p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-600 transition-all">
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $spec['label'] }}</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $spec['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Highlighted Features --}}
                    @if ($highlightedFeatures->isNotEmpty())
                        <section id="highlighted" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Öne Çıkan Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($highlightedFeatures as $feature)
                                    <div
                                        class="flex gap-4 p-6 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-lg transition-all">
                                        <div
                                            class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i
                                                class="fa-solid fa-{{ $feature['icon'] }} text-blue-600 dark:text-blue-400 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white mb-2">
                                                {{ $feature['title'] }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                {{ $feature['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Features List --}}
                    @if (!empty($featuresList))
                        <section id="features" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Özellikler ve Avantajlar</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($featuresList as $feature)
                                    @php
                                        $featureIcon = is_array($feature)
                                            ? $feature['icon'] ?? 'check-circle'
                                            : 'check-circle';
                                        $featureText = is_array($feature) ? $feature['text'] ?? $feature : $feature;
                                    @endphp
                                    <div
                                        class="flex gap-3 items-start p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-green-300 dark:hover:border-green-600 transition-all">
                                        <i
                                            class="fa-solid fa-{{ $featureIcon }} text-green-600 dark:text-green-400 mt-1 text-lg"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Variants (moved after features) --}}
                    @if ($siblingVariants->count() > 0)
                        <section id="variants" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Ürün Varyantları</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($siblingVariants as $variant)
                                    @php
                                        $variantTitle =
                                            $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                        $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl(
                                            $variant,
                                            $currentLocale,
                                        );
                                        $variantImage = $variant->getFirstMedia('featured_image');
                                    @endphp
                                    <a href="{{ $variantUrl }}"
                                        class="group border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400 rounded-xl p-5 transition-all hover:shadow-lg">
                                        @if ($variantImage)
                                            <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}"
                                                alt="{{ $variantTitle }}" class="w-full h-40 object-cover rounded-lg mb-4">
                                        @endif
                                        <h3
                                            class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            {{ $variantTitle }}</h3>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Use Cases --}}
                    @if (!empty($useCases))
                        <section id="usecases" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Kullanım Alanları</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach ($useCases as $case)
                                    @php
                                        $caseIcon = is_array($case) ? $case['icon'] ?? 'check' : 'check';
                                        $caseText = is_array($case) ? $case['text'] ?? $case : $case;
                                    @endphp
                                    <div
                                        class="flex gap-4 p-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl hover:border-blue-400 dark:hover:border-blue-500 transition-all">
                                        <i
                                            class="fa-solid fa-{{ $caseIcon }} text-blue-600 dark:text-blue-400 text-lg mt-1"></i>
                                        <span class="text-gray-800 dark:text-gray-200">{{ $caseText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Competitive Advantages --}}
                    @if (!empty($competitiveAdvantages))
                        <section id="competitive" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Rekabet Avantajları</h2>
                            <div class="grid md:grid-cols-2 gap-8">
                                @foreach ($competitiveAdvantages as $advantage)
                                    @php
                                        $advIcon = is_array($advantage) ? $advantage['icon'] ?? 'star' : 'star';
                                        $advText = is_array($advantage) ? $advantage['text'] ?? $advantage : $advantage;
                                    @endphp
                                    <div
                                        class="flex gap-4 p-6 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-amber-300 dark:hover:border-amber-600 transition-all">
                                        <div
                                            class="w-14 h-14 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i
                                                class="fa-solid fa-{{ $advIcon }} text-amber-700 dark:text-amber-300 text-xl"></i>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $advText }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Technical Specs - Compact Design --}}
                    @if (!empty($technicalSpecs))
                        <section id="technical" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Detaylı Teknik Özellikler
                            </h2>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                                @php
                                    $isFlat =
                                        !empty($technicalSpecs) &&
                                        !collect($technicalSpecs)->contains(fn($v) => is_array($v));
                                @endphp

                                @if ($isFlat)
                                    <div
                                        class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-5 py-4">
                                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                                            <i class="fa-solid fa-cog text-sm"></i>
                                            Teknik Özellikler
                                        </h3>
                                    </div>
                                    @foreach ($technicalSpecs as $key => $value)
                                        <div
                                            class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800/50 transition-colors">
                                            <div class="px-5 py-3.5">
                                                <span
                                                    class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</span>
                                            </div>
                                            <div class="px-5 py-3.5">
                                                <span
                                                    class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formatSpecValue($value) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach ($technicalSpecs as $sectionKey => $sectionValues)
                                        @if (is_array($sectionValues) && !empty($sectionValues))
                                            @php
                                                $sectionTitle =
                                                    $sectionValues['_title'] ??
                                                    \Illuminate\Support\Str::headline(
                                                        str_replace('_', ' ', $sectionKey),
                                                    );
                                                $sectionIcon = $sectionValues['_icon'] ?? 'cog';
                                                if (isset($sectionValues['_title'])) {
                                                    unset($sectionValues['_title']);
                                                }
                                                if (isset($sectionValues['_icon'])) {
                                                    unset($sectionValues['_icon']);
                                                }
                                            @endphp
                                            <div
                                                class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-700 dark:to-blue-800 px-5 py-4">
                                                <h3 class="text-base font-bold text-white flex items-center gap-2">
                                                    <i class="fa-solid fa-{{ $sectionIcon }} text-sm"></i>
                                                    {{ $sectionTitle }}
                                                </h3>
                                            </div>
                                            @foreach ($sectionValues as $key => $value)
                                                <div
                                                    class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800/50 transition-colors">
                                                    <div class="px-5 py-3.5">
                                                        <span
                                                            class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $key }}</span>
                                                    </div>
                                                    <div class="px-5 py-3.5">
                                                        <span
                                                            class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formatSpecValue($value) }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </section>
                    @endif

                    {{-- Accessories --}}
                    @if (!empty($accessories))
                        <section id="accessories" class="scroll-mt-24 mb-20 lg:mb-24">
                            <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Aksesuarlar ve Opsiyonlar
                            </h2>
                            <div class="grid md:grid-cols-2 gap-8">
                                @foreach ($accessories as $accessory)
                                    @php
                                        $isStandard = $accessory['is_standard'] ?? false;
                                        $price = $accessory['price'] ?? null;
                                        $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                                    @endphp
                                    @if ($isStandard)
                                        <div
                                            class="border-2 border-green-500 dark:border-green-600 rounded-xl p-6 transition-all hover:shadow-lg">
                                        @else
                                            <div
                                                class="border-2 border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-all hover:shadow-lg">
                                    @endif
                                    <div class="flex items-start justify-between mb-4">
                                        <div
                                            class="w-14 h-14 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                                            <i
                                                class="fa-solid fa-{{ $accIcon }} text-orange-600 dark:text-orange-400 text-xl"></i>
                                        </div>
                                        @if ($isStandard)
                                            <span
                                                class="px-3 py-1.5 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">
                                                <i class="fa-solid fa-check"></i> Standart
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold rounded-full">
                                                <i class="fa-solid fa-plus"></i> Opsiyonel
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                        {{ $accessory['name'] }}</h3>
                                    @if (!empty($accessory['description']))
                                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                            {{ $accessory['description'] }}</p>
                                    @endif
                                    @if ($price)
                                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                            <span
                                                class="text-lg font-bold text-gray-900 dark:text-white">{{ $price }}</span>
                                        </div>
                                    @endif
                            </div>
                    @endforeach
                </div>
                </section>
                @endif

                {{-- Certifications --}}
                @if (!empty($certifications))
                    <section id="certifications" class="scroll-mt-24 mb-20 lg:mb-24">
                        <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Sertifikalar ve Uygunluklar
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach ($certifications as $cert)
                                @php
                                    $certIcon = $cert['icon'] ?? 'certificate';
                                @endphp
                                <div
                                    class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-all text-center hover:border-blue-400 dark:hover:border-blue-600 hover:shadow-lg">
                                    <div
                                        class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i
                                            class="fa-solid fa-{{ $certIcon }} text-blue-600 dark:text-blue-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">
                                        {{ $cert['name'] }}</h3>
                                    @if (!empty($cert['year']))
                                        <div
                                            class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-semibold text-gray-700 dark:text-gray-300 inline-flex items-center gap-2">
                                            <i class="fa-solid fa-calendar"></i>
                                            {{ $cert['year'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Warranty Info --}}
                @if ($warrantyInfo)
                    <section id="warranty" class="scroll-mt-24 mb-20 lg:mb-24">
                        <div class="flex gap-6 p-6 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-lg transition-all">
                            <div
                                class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-shield-heart text-emerald-600 dark:text-emerald-400 text-3xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Garanti Bilgisi</h3>
                                @if (is_array($warrantyInfo))
                                    @if ($warrantyInfo['coverage'] ?? false)
                                        <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">
                                            {{ $warrantyInfo['coverage'] }}</p>
                                    @endif
                                    @if ($warrantyInfo['duration_months'] ?? false)
                                        <div
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/40 rounded-full text-emerald-900 dark:text-emerald-100 font-semibold">
                                            <i class="fa-solid fa-calendar-check"></i>
                                            {{ $warrantyInfo['duration_months'] }} ay garanti
                                        </div>
                                    @endif
                                @else
                                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $warrantyInfo }}</p>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Target Industries (moved before FAQ) --}}
                @if (!empty($targetIndustries))
                    <section id="industries" class="scroll-mt-24 mb-20 lg:mb-24">
                        <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Uygun Olduğu Sektörler</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($targetIndustries as $industry)
                                @php
                                    $industryIcon = is_array($industry)
                                        ? $industry['icon'] ?? 'briefcase'
                                        : 'briefcase';
                                    $industryText = is_array($industry) ? $industry['text'] ?? $industry : $industry;
                                @endphp
                                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl transition-all hover:border-purple-400 dark:hover:border-purple-500 hover:shadow-lg">
                                    <div class="flex flex-col items-center gap-3 text-center">
                                        <div
                                            class="w-14 h-14 bg-purple-100 dark:bg-purple-500/20 rounded-xl flex items-center justify-center">
                                            <i
                                                class="fa-solid fa-{{ $industryIcon }} text-purple-700 dark:text-purple-400 text-xl"></i>
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-gray-900 dark:text-gray-200">{{ $industryText }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- FAQ --}}
                @if ($faqEntries->isNotEmpty())
                    <section id="faq" x-data="{ openFaq: null }" class="scroll-mt-24 mb-20 lg:mb-24">
                        <h2 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Sık Sorulan Sorular</h2>
                        <div class="space-y-4">
                            @foreach ($faqEntries as $index => $faq)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:border-blue-300 dark:hover:border-blue-600 transition-all bg-white dark:bg-gray-800">
                                    <button
                                        @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                        class="w-full px-6 py-5 flex items-center justify-between gap-4 text-left transition-colors"
                                        :class="openFaq === {{ $index }} ? 'bg-blue-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800'">
                                        <span
                                            class="flex-1 font-semibold text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                        <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform"
                                            :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="openFaq === {{ $index }}" x-transition
                                        class="border-t border-gray-200 dark:border-gray-700" style="display: none;">
                                        <div class="px-6 py-5 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {!! nl2br(e($faq['answer'])) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif


            </div>

            {{-- RIGHT: Sticky Sidebar (1/3) - Clean Version --}}
            <div class="lg:col-span-1 order-first lg:order-last">
                <div class="space-y-8 lg:sticky lg:top-24" id="sticky-sidebar">
                    {{-- Product Info Card --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $title }}</h1>

                        @if ($shortDescription)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6 leading-relaxed">{{ $shortDescription }}</p>
                        @endif

                        {{-- CTA Section - V6 SaaS Modern Style --}}
                        <div class="mb-6">
                            {{-- Fiyat Kutusu --}}
                            <div class="rounded-xl p-6 mb-4"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div
                                    class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-md text-xs text-white mb-2">
                                    <span class="w-1.5 h-1.5 bg-green-400"></span>
                                    Stokta Mevcut
                                </div>
                                <div class="text-2xl font-bold text-white mb-1">Özel Fiyat Alın</div>
                                <div class="text-sm text-white">En iyi fiyat garantisi</div>
                            </div>

                            {{-- CTA Buttons --}}
                            <div class="space-y-4">
                                <a href="#contact"
                                    class="flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium py-3 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white hover:border-blue-400 dark:hover:border-blue-500 transition-all">
                                    <i class="fa-solid fa-envelope"></i>Teklif İste
                                </a>
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="tel:02167553555"
                                        class="flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium py-3 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white hover:border-blue-400 dark:hover:border-blue-500 transition-all">
                                        <i class="fa-solid fa-phone"></i>Ara
                                    </a>
                                    <a href="https://wa.me/905010056758" target="_blank"
                                        class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                                        <i class="fa-brands fa-whatsapp"></i>WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl py-12 px-6 shadow-xl">
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
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 md:p-10">
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

                    <a href="https://wa.me/905010056758" target="_blank"
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

    {{-- Floating CTA --}}
    <div x-data="{ show: false }" @scroll.window="show = (window.pageYOffset > 800)" x-show="show" x-transition
        class="fixed bottom-8 right-8 z-50 hidden md:block">
        <a href="#contact"
            class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-full shadow-2xl hover:shadow-3xl transition-all">
            <i class="fa-solid fa-envelope"></i>
            <span>Teklif Al</span>
        </a>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/themes/blank/shop-product-show.js') }}"></script>
@endpush
