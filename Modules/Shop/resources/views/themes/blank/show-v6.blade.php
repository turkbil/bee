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
            $highlightedFeatures = collect($item->highlighted_features ?? [])->map(fn($feature) => is_array($feature) ? ['icon' => $feature['icon'] ?? 'bolt', 'title' => is_array($feature['title'] ?? null) ? $resolveLocalized($feature['title']) : ($feature['title'] ?? null), 'description' => is_array($feature['description'] ?? null) ? $resolveLocalized($feature['description']) : ($feature['description'] ?? null)] : null)->filter(fn($feature) => $feature && ($feature['title'] || $feature['description']));
            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = collect($resolvedUseCases)->map(fn($case) => is_array($case) && isset($case['text']) ? $case : ['icon' => 'check', 'text' => $case])->filter(fn($case) => !empty($case['text']))->values()->all();
                }
            }
            $technicalSpecs = is_array($item->technical_specs) ? $item->technical_specs : [];
            $featuresList = [];
            if (is_array($item->features)) {
                $resolvedFeatures = $resolveLocalized($item->features) ?? [];
                if (is_array($resolvedFeatures)) {
                    if (array_is_list($resolvedFeatures)) {
                        $featuresList = collect($resolvedFeatures)->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                    } else {
                        $featuresList = collect($resolvedFeatures['list'] ?? [])->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                    }
                }
            }
            $competitiveAdvantages = [];
            if (is_array($item->competitive_advantages)) {
                $resolvedAdvantages = $resolveLocalized($item->competitive_advantages);
                if (is_array($resolvedAdvantages)) {
                    $competitiveAdvantages = collect($resolvedAdvantages)->map(fn($adv) => is_array($adv) && isset($adv['text']) ? $adv : ['icon' => 'star', 'text' => $adv])->filter(fn($adv) => !empty($adv['text']))->values()->all();
                }
            }
            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }

            // Competitive Advantages (icon destekli format)
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

            // Target Industries (icon destekli format)
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
                ->map(fn($faq) => is_array($faq) ? [
                    'question' => $resolveLocalized($faq['question'] ?? null),
                    'answer' => $resolveLocalized($faq['answer'] ?? null),
                    'sort_order' => $faq['sort_order'] ?? 999,
                ] : null)
                ->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])
                ->sortBy('sort_order')
                ->values();

            $siblingVariants = $siblingVariants ?? collect();
        @endphp

        {{-- 🎯 HERO SECTION --}}
        <section id="hero-section" class="relative bg-gradient-to-r from-blue-600 via-slate-800 to-slate-950 text-white overflow-hidden">
            {{-- Decorative elements --}}
            <div class="absolute top-0 left-0 w-full h-full opacity-15">
                <div class="absolute top-20 right-20 w-96 h-96 bg-blue-400 rounded-full mix-blend-overlay filter blur-3xl animate-pulse"></div>
                <div class="absolute bottom-10 left-10 w-[500px] h-[500px] bg-slate-500 rounded-full mix-blend-overlay filter blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
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

                        @if($shortDescription)
                            <p class="text-xl text-blue-100 leading-relaxed mb-8">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact" class="inline-flex items-center justify-center gap-3 bg-white text-blue-700 px-8 py-4 rounded-lg font-bold text-lg hover:bg-blue-50 transition-colors">
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
                                 alt="{{ $title }}"
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
                            @if($galleryImages->count() > 0)
                                <a href="#gallery" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">📸 Galeri</a>
                            @endif
                            @if($highlightedFeatures->isNotEmpty())
                                <a href="#highlighted" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🌟 Öne Çıkanlar</a>
                            @endif
                            @if(!empty($featuresList))
                                <a href="#features" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">📋 Avantajlar</a>
                            @endif
                            @if(!empty($useCases))
                                <a href="#usecases" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🎯 Kullanım</a>
                            @endif
                            @if($siblingVariants->count() > 0)
                                <a href="#variants" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full hover:text-blue-700 dark:hover:text-blue-400 transition-colors whitespace-nowrap">🔀 Varyantlar</a>
                            @endif
                            <a href="#contact" class="px-3 py-1.5 text-xs font-medium bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors whitespace-nowrap">✉️ Teklif Al</a>
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid lg:grid-cols-3 gap-8 items-start">
                {{-- LEFT: Main Content (2/3) --}}
                <div class="lg:col-span-2 space-y-12 min-h-screen">

                    {{-- Gallery --}}
                    @if($galleryImages->count() > 0)
                        <section>
                            <h2 class="text-3xl font-bold mb-6">Ürün Görselleri</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($galleryImages as $image)
                                    <a href="{{ $image->getUrl() }}" class="glightbox block rounded-lg overflow-hidden hover:ring-2 ring-blue-500" data-gallery="prod">
                                        <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}" alt="" class="w-full h-48 object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Long Description --}}
                    @if($longDescription)
                        <section>
                            <div class="prose max-w-none dark:prose-invert">
                                @parsewidgets($longDescription)
                            </div>
                        </section>
                    @endif

                    {{-- Highlighted Features --}}
                    @if($highlightedFeatures->isNotEmpty())
                        <section id="highlighted">
                            <h2 class="text-3xl font-bold mb-6">Öne Çıkan Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($highlightedFeatures as $feature)
                                    <div class="flex gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-lg transition-all">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-{{ $feature['icon'] }} text-blue-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-900 dark:text-white mb-1">{{ $feature['title'] }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Features List --}}
                    @if(!empty($featuresList))
                        <section id="features">
                            <h2 class="text-3xl font-bold mb-6">Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($featuresList as $feature)
                                    @php
                                        $featureIcon = is_array($feature) ? ($feature['icon'] ?? 'check-circle') : 'check-circle';
                                        $featureText = is_array($feature) ? ($feature['text'] ?? $feature) : $feature;
                                    @endphp
                                    <div class="flex gap-3 items-start p-3 border border-gray-200 dark:border-gray-700 rounded hover:shadow-lg transition-all">
                                        <i class="fa-solid fa-{{ $featureIcon }} text-green-500 mt-1"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Use Cases --}}
                    @if(!empty($useCases))
                        <section id="usecases">
                            <h2 class="text-3xl font-bold mb-6">Kullanım Alanları</h2>
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($useCases as $case)
                                    @php
                                        $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                        $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                                    @endphp
                                    <div class="flex gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:shadow-lg transition-all">
                                        <i class="fa-solid fa-{{ $caseIcon }} text-blue-600 text-lg mt-1"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $caseText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Variants --}}
                    @if($siblingVariants->count() > 0)
                        <section id="variants">
                            <h2 class="text-3xl font-bold mb-6">Ürün Varyantları</h2>
                            <div class="grid md:grid-cols-3 gap-4">
                                @foreach($siblingVariants as $variant)
                                    @php
                                        $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                        $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                        $variantImage = $variant->getFirstMedia('featured_image');
                                    @endphp
                                    <a href="{{ $variantUrl }}" class="group border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 rounded-lg p-4 transition-all hover:shadow-lg">
                                        @if($variantImage)
                                            <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}"
                                                 alt="{{ $variantTitle }}"
                                                 class="w-full h-32 object-cover rounded mb-3">
                                        @endif
                                        <h3 class="font-bold text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $variantTitle }}</h3>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Competitive Advantages --}}
                    @if(!empty($competitiveAdvantages))
                        <section id="competitive">
                            <h2 class="text-3xl font-bold mb-6">Rekabet Avantajları</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($competitiveAdvantages as $advantage)
                                    @php
                                        $advIcon = is_array($advantage) ? ($advantage['icon'] ?? 'star') : 'star';
                                        $advText = is_array($advantage) ? ($advantage['text'] ?? $advantage) : $advantage;
                                    @endphp
                                    <div class="flex gap-4 p-6 border border-amber-200 dark:border-amber-800 rounded-lg hover:shadow-lg transition-all">
                                        <div class="w-12 h-12 bg-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-{{ $advIcon }} text-white text-xl"></i>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed pt-2">{{ $advText }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Target Industries --}}
                    @if(!empty($targetIndustries))
                        <section id="industries">
                            <h2 class="text-3xl font-bold mb-6">Uygun Olduğu Sektörler</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($targetIndustries as $industry)
                                    @php
                                        $industryIcon = is_array($industry) ? ($industry['icon'] ?? 'briefcase') : 'briefcase';
                                        $industryText = is_array($industry) ? ($industry['text'] ?? $industry) : $industry;
                                    @endphp
                                    <div class="p-6 border border-purple-200 dark:border-gray-700 rounded-lg hover:shadow-lg transition-all">
                                        <div class="flex flex-col items-center gap-3 text-center">
                                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-{{ $industryIcon }} text-white text-xl"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-purple-900 dark:text-gray-200">{{ $industryText }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Technical Specs --}}
                    @if(!empty($technicalSpecs))
                        <section id="technical">
                            <h2 class="text-3xl font-bold mb-6">Detaylı Teknik Özellikler</h2>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                @php
                                    $isFlat = !empty($technicalSpecs) && !collect($technicalSpecs)->contains(fn($v) => is_array($v));
                                @endphp

                                @if($isFlat)
                                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 border-b border-blue-800">
                                        <h3 class="text-lg font-bold text-white flex items-center gap-3">
                                            <i class="fa-solid fa-cog"></i>
                                            Teknik Özellikler
                                        </h3>
                                    </div>
                                    @foreach($technicalSpecs as $key => $value)
                                        <div class="flex items-center border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                            <div class="w-1/2 px-6 py-4 border-r border-gray-200 dark:border-gray-700">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $key }}</span>
                                            </div>
                                            <div class="w-1/2 px-6 py-4">
                                                <span class="font-semibold text-gray-900 dark:text-white">{{ $formatSpecValue($value) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($technicalSpecs as $sectionKey => $sectionValues)
                                        @if(is_array($sectionValues) && !empty($sectionValues))
                                            @php
                                                $sectionTitle = $sectionValues['_title'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $sectionKey));
                                                $sectionIcon = $sectionValues['_icon'] ?? 'cog';
                                                if (isset($sectionValues['_title'])) unset($sectionValues['_title']);
                                                if (isset($sectionValues['_icon'])) unset($sectionValues['_icon']);
                                            @endphp
                                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 border-b border-blue-800">
                                                <h3 class="text-lg font-bold text-white flex items-center gap-3">
                                                    <i class="fa-solid fa-{{ $sectionIcon }}"></i>
                                                    {{ $sectionTitle }}
                                                </h3>
                                            </div>
                                            @foreach($sectionValues as $key => $value)
                                                <div class="flex items-center border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                                    <div class="w-1/2 px-6 py-4 border-r border-gray-200 dark:border-gray-700">
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $key }}</span>
                                                    </div>
                                                    <div class="w-1/2 px-6 py-4">
                                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $formatSpecValue($value) }}</span>
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
                    @if(!empty($accessories))
                        <section id="accessories">
                            <h2 class="text-3xl font-bold mb-6">Aksesuarlar ve Opsiyonlar</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($accessories as $accessory)
                                    @php
                                        $isStandard = $accessory['is_standard'] ?? false;
                                        $price = $accessory['price'] ?? null;
                                        $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                                    @endphp
                                    <div class="border-2 {{ $isStandard ? 'border-green-500' : 'border-gray-200 dark:border-gray-700' }} rounded-lg p-6 hover:shadow-lg transition-all">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fa-solid fa-{{ $accIcon }} text-orange-600 text-xl"></i>
                                            </div>
                                            @if($isStandard)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                    <i class="fa-solid fa-check"></i> Standart
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                    <i class="fa-solid fa-plus"></i> Opsiyonel
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $accessory['name'] }}</h3>
                                        @if(!empty($accessory['description']))
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $accessory['description'] }}</p>
                                        @endif
                                        @if($price)
                                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $price }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Certifications --}}
                    @if(!empty($certifications))
                        <section id="certifications">
                            <h2 class="text-3xl font-bold mb-6">Sertifikalar ve Uygunluklar</h2>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                @foreach($certifications as $cert)
                                    @php
                                        $certIcon = $cert['icon'] ?? 'certificate';
                                    @endphp
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-lg transition-all text-center">
                                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fa-solid fa-{{ $certIcon }} text-blue-600 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $cert['name'] }}</h3>
                                        @if(!empty($cert['year']))
                                            <div class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-xs font-semibold text-gray-700 dark:text-gray-300 inline-flex items-center gap-2 mb-2">
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
                    @if($warrantyInfo)
                        <section id="warranty">
                            <div class="flex gap-6 p-6 border border-emerald-200 dark:border-emerald-800 rounded-lg">
                                <div class="w-16 h-16 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-shield-heart text-white text-3xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Garanti Bilgisi</h3>
                                    @if(is_array($warrantyInfo))
                                        @if($warrantyInfo['coverage'] ?? false)
                                            <p class="text-gray-700 dark:text-gray-300 mb-3">{{ $warrantyInfo['coverage'] }}</p>
                                        @endif
                                        @if($warrantyInfo['duration_months'] ?? false)
                                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/40 rounded-full text-emerald-900 dark:text-emerald-100 font-semibold">
                                                <i class="fa-solid fa-calendar-check"></i>
                                                {{ $warrantyInfo['duration_months'] }} ay garanti
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-gray-700 dark:text-gray-300">{{ $warrantyInfo }}</p>
                                    @endif
                                </div>
                            </div>
                        </section>
                    @endif

                    {{-- FAQ --}}
                    @if($faqEntries->isNotEmpty())
                        <section id="faq" x-data="{ openFaq: null }">
                            <h2 class="text-3xl font-bold mb-6">Sık Sorulan Sorular</h2>
                            <div class="space-y-4">
                                @foreach($faqEntries as $index => $faq)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                        <button @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                                class="w-full px-6 py-5 flex items-center justify-between gap-4 text-left"
                                                :class="openFaq === {{ $index }} ? 'bg-gray-50 dark:bg-gray-800' : ''">
                                            <span class="flex-1 font-semibold text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform"
                                               :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                        </button>
                                        <div x-show="openFaq === {{ $index }}"
                                             x-transition
                                             class="border-t border-gray-100 dark:border-gray-700"
                                             style="display: none;">
                                            <div class="px-6 py-5 text-gray-600 dark:text-gray-300 leading-relaxed">
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
                <div class="lg:col-span-1">
                    <div class="space-y-6" id="sticky-sidebar">
                        {{-- Product Info Card --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ $title }}</h1>

                            @if($shortDescription)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $shortDescription }}</p>
                            @endif

                            {{-- Quick Specs --}}
                            @if(!empty($primarySpecs))
                                <div class="border-t border-b border-gray-200 dark:border-gray-700 py-4 my-4 space-y-3">
                                    @foreach(array_slice($primarySpecs, 0, 4) as $spec)
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $spec['label'] }}</span>
                                            <span class="font-bold text-gray-900 dark:text-white">{{ $spec['value'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- CTA Section - V6 SaaS Modern Style --}}
                            <div class="mb-6">
                                {{-- Fiyat Kutusu --}}
                                <div class="rounded-xl p-6 mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full text-xs text-white mb-2">
                                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                                        Şimdi Mevcut
                                    </div>
                                    <div class="text-2xl font-bold text-white mb-1">Özel Fiyat Alın</div>
                                    <div class="text-sm text-violet-100">En iyi fiyat garantisi</div>
                                </div>

                                {{-- CTA Buttons --}}
                                <div class="space-y-3">
                                    <a href="#contact" class="block w-full bg-gradient-to-r from-violet-600 to-purple-600 text-white text-center font-semibold py-3.5 rounded-lg hover:from-violet-700 hover:to-purple-700 transition-all transform hover:scale-105">
                                        <i class="fa-solid fa-envelope mr-2"></i>Teklif İste
                                    </a>
                                    <div class="grid grid-cols-2 gap-3">
                                        <a href="tel:02167553555" class="flex items-center justify-center gap-2 border border-violet-200 dark:border-violet-800 text-violet-700 dark:text-violet-300 text-sm py-2 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                                            <i class="fa-solid fa-phone"></i>Ara
                                        </a>
                                        <a href="https://wa.me/905010056758" target="_blank" class="flex items-center justify-center gap-2 bg-green-500 text-white text-sm py-2 rounded-lg hover:bg-green-600 transition-colors">
                                            <i class="fa-brands fa-whatsapp"></i>Mesaj
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                <a href="{{ $shopIndexUrl }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Tüm Ürünlere Dön</span>
                </a>
            </div>
        </div>

        {{-- 📬 MODERN CONTACT FORM --}}
        <section id="contact" class="relative mt-16 overflow-hidden">
            {{-- Gradient Background with Pattern --}}
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-slate-900"></div>
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full mix-blend-overlay filter blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-500 rounded-full mix-blend-overlay filter blur-3xl"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
                {{-- Başlık --}}
                <div class="text-center mb-12">
                    <div class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-full mb-6">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-white">7/24 Destek</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Hemen Teklif Alın
                    </h2>
                    <p class="text-xl text-blue-100 leading-relaxed max-w-2xl mx-auto">
                        Ürünlerimiz hakkında detaylı bilgi almak ve size özel fiyat teklifi almak için formu doldurun.
                    </p>
                </div>

                <div class="flex flex-col md:flex-row gap-8 items-start">
                    {{-- SOL: FORM (7/12) --}}
                    <div class="w-full md:w-7/12">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8 md:p-10">
                            <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="hidden" name="product_title" value="{{ $title }}">

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                            <i class="fa-solid fa-user text-blue-600"></i>
                                            Ad Soyad *
                                        </label>
                                        <input type="text"
                                               name="name"
                                               required
                                               placeholder="Adınız Soyadınız"
                                               class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all">
                                    </div>
                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                            <i class="fa-solid fa-envelope text-blue-600"></i>
                                            E-posta *
                                        </label>
                                        <input type="email"
                                               name="email"
                                               required
                                               placeholder="ornek@email.com"
                                               class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fa-solid fa-phone text-blue-600"></i>
                                        Telefon *
                                    </label>
                                    <input type="tel"
                                           name="phone"
                                           required
                                           placeholder="0555 555 55 55"
                                           class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all">
                                </div>

                                <div>
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                                        <i class="fa-solid fa-message text-blue-600"></i>
                                        Mesajınız
                                    </label>
                                    <textarea name="message"
                                              rows="5"
                                              placeholder="Ürün hakkında merak ettiklerinizi yazabilirsiniz..."
                                              class="w-full px-5 py-4 border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 transition-all resize-none"></textarea>
                                </div>

                                <button type="submit"
                                        class="group relative w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-5 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <span class="flex items-center justify-center gap-3">
                                        <i class="fa-solid fa-paper-plane text-xl group-hover:rotate-45 transition-transform"></i>
                                        <span class="text-lg">Teklif İsteyin</span>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- SAĞ: DETAYLAR (5/12) --}}
                    <div class="w-full md:w-5/12 space-y-6">
                        {{-- İletişim Bilgileri --}}
                        <a href="tel:02167553555"
                           class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-blue-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-blue-400 hover:shadow-2xl hover:shadow-blue-500/20 transform hover:scale-105 cursor-pointer">
                            <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-xl group-hover:scale-110 transition-all duration-300">
                                <i class="fa-solid fa-phone text-blue-600 text-xl group-hover:animate-pulse"></i>
                            </div>
                            <div>
                                <div class="text-sm text-blue-200 mb-1">Telefon</div>
                                <div class="text-xl font-bold text-white">0216 755 3 555</div>
                                <div class="text-xs text-blue-300 mt-1">Hafta içi 09:00 - 18:00</div>
                            </div>
                        </a>

                        <a href="https://wa.me/905010056758"
                           target="_blank"
                           class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-green-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-green-400 hover:shadow-2xl hover:shadow-green-500/20 transform hover:scale-105 cursor-pointer">
                            <div class="w-14 h-14 bg-green-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-xl group-hover:scale-110 group-hover:bg-green-600 transition-all duration-300">
                                <i class="fa-brands fa-whatsapp text-white text-xl group-hover:animate-bounce"></i>
                            </div>
                            <div>
                                <div class="text-sm text-blue-200 mb-1">WhatsApp</div>
                                <div class="text-xl font-bold text-white">0501 005 67 58</div>
                                <div class="text-xs text-blue-300 mt-1">Anında yanıt alın</div>
                            </div>
                        </a>

                        <a href="mailto:info@ixtif.com"
                           class="group flex items-start gap-4 p-6 bg-white/10 backdrop-blur-sm hover:bg-purple-500/30 rounded-2xl transition-all duration-300 border border-white/20 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/20 transform hover:scale-105 cursor-pointer">
                            <div class="w-14 h-14 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-xl group-hover:scale-110 group-hover:bg-purple-600 transition-all duration-300">
                                <i class="fa-solid fa-envelope text-white text-xl group-hover:animate-pulse"></i>
                            </div>
                            <div>
                                <div class="text-sm text-blue-200 mb-1">E-posta</div>
                                <div class="text-lg font-bold text-white break-all">info@ixtif.com</div>
                                <div class="text-xs text-blue-300 mt-1">7/24 ulaşabilirsiniz</div>
                            </div>
                        </a>

                        {{-- Info Box --}}
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                            <div class="text-center mb-5">
                                <h3 class="text-2xl font-bold text-white mb-1">Türkiye'nin İstif Pazarı</h3>
                                <p class="text-sm text-blue-200">Forklift ve İstif Makineleri Merkezi</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="text-center p-3 bg-white/10 rounded-xl hover:bg-white/20 transition-all">
                                    <i class="fa-solid fa-box text-blue-300 text-xl mb-2"></i>
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
        <div x-data="{ show: false }"
             @scroll.window="show = (window.pageYOffset > 800)"
             x-show="show"
             x-transition
             class="fixed bottom-8 right-8 z-50 hidden md:block">
            <a href="#contact"
               class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-full shadow-2xl transition-colors">
                <i class="fa-solid fa-envelope"></i>
                <span>Teklif Al</span>
            </a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sticky-sidebar');
        const heroSection = document.getElementById('hero-section');
        const contactForm = document.getElementById('contact');
        const header = document.querySelector('header') || document.querySelector('nav');

        if (!sidebar || !heroSection || !contactForm) return;

        const sidebarParent = sidebar.parentElement;
        sidebarParent.style.position = 'relative';

        const GAP = 64;
        const HEADER_MARGIN = 32;

        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const heroHeight = heroSection.offsetHeight;
            const headerHeight = header ? header.offsetHeight : 0;
            const sidebarHeight = sidebar.offsetHeight;
            const sidebarWidth = sidebar.offsetWidth;
            const contactFormTop = contactForm.getBoundingClientRect().top + scrollTop;
            const parentTop = sidebarParent.getBoundingClientRect().top + scrollTop;

            if (scrollTop <= heroHeight) {
                sidebar.style.position = 'static';
                sidebar.style.top = '';
                sidebar.style.width = '';
                sidebar.style.zIndex = '';
                return;
            }

            const stickyOffset = headerHeight + HEADER_MARGIN;
            const stickyBottomPosition = scrollTop + stickyOffset + sidebarHeight;

            if (stickyBottomPosition + GAP >= contactFormTop) {
                const absoluteTop = contactFormTop - parentTop - sidebarHeight - GAP;
                sidebar.style.position = 'absolute';
                sidebar.style.top = absoluteTop + 'px';
                sidebar.style.width = sidebarWidth + 'px';
                sidebar.style.zIndex = '10';
                return;
            }

            sidebar.style.position = 'fixed';
            sidebar.style.top = stickyOffset + 'px';
            sidebar.style.width = sidebarWidth + 'px';
            sidebar.style.zIndex = '10';
        }

        window.addEventListener('scroll', handleScroll, { passive: true });
        window.addEventListener('resize', handleScroll);
        handleScroll();
    });
    </script>
@endsection
