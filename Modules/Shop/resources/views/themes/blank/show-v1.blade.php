@extends('themes.blank.layouts.app')

{{-- V1: MODERN & MINIMALIST - Clean, spacious, soft colors --}}

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

        {{-- HERO SECTION: Minimal & Clean --}}
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
                        <div class="inline-block px-4 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-sm font-medium rounded-full mb-6">
                            Stokta Mevcut
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

        {{-- PRIMARY SPECS: Minimal Cards --}}
        @if(!empty($primarySpecs))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($primarySpecs as $spec)
                            <div class="text-center p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <div class="text-3xl font-light text-gray-900 dark:text-white mb-2">{{ $spec['value'] }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $spec['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

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

        {{-- HIGHLIGHTED FEATURES --}}
        @if($highlightedFeatures->isNotEmpty())
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Öne Çıkan Özellikler</h2>
                    <div class="grid md:grid-cols-3 gap-8">
                        @foreach($highlightedFeatures as $feature)
                            <div class="text-center p-8 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fa-solid fa-{{ $feature['icon'] }} text-blue-600 dark:text-blue-400 text-2xl"></i>
                                </div>
                                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">{{ $feature['title'] }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- LONG DESCRIPTION --}}
        @if($longDescription)
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-4xl mx-auto px-6 lg:px-8">
                    <div class="prose prose-lg max-w-none dark:prose-invert prose-headings:font-light prose-p:text-gray-600 dark:prose-p:text-gray-400">
                        @parsewidgets($longDescription)
                    </div>
                </div>
            </section>
        @endif

        {{-- FEATURES LIST --}}
        @if(!empty($featuresList))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Avantajlar</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($featuresList as $feature)
                            @php
                                $featureIcon = is_array($feature) ? ($feature['icon'] ?? 'check-circle') : 'check-circle';
                                $featureText = is_array($feature) ? ($feature['text'] ?? $feature) : $feature;
                            @endphp
                            <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                <i class="fa-solid fa-{{ $featureIcon }} text-green-500 text-xl mt-0.5"></i>
                                <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- COMPETITIVE ADVANTAGES --}}
        @if(!empty($competitiveAdvantages))
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Rekabet Avantajları</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($competitiveAdvantages as $advantage)
                            @php
                                $advIcon = is_array($advantage) ? ($advantage['icon'] ?? 'star') : 'star';
                                $advText = is_array($advantage) ? ($advantage['text'] ?? $advantage) : $advantage;
                            @endphp
                            <div class="flex items-start gap-4 p-6 bg-white dark:bg-gray-800 rounded-2xl">
                                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-{{ $advIcon }} text-amber-600 dark:text-amber-400 text-xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 pt-2">{{ $advText }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- TARGET INDUSTRIES --}}
        @if(!empty($targetIndustries))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Kullanım Sektörleri</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($targetIndustries as $industry)
                            @php
                                $industryIcon = is_array($industry) ? ($industry['icon'] ?? 'briefcase') : 'briefcase';
                                $industryText = is_array($industry) ? ($industry['text'] ?? $industry) : $industry;
                            @endphp
                            <div class="text-center p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-{{ $industryIcon }} text-purple-600 dark:text-purple-400 text-xl"></i>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $industryText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- TECHNICAL SPECS --}}
        @if(!empty($technicalSpecs))
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Teknik Özellikler</h2>

                    @php
                        $isFlat = !empty($technicalSpecs) && !collect($technicalSpecs)->contains(fn($v) => is_array($v));
                    @endphp

                    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
                        @if($isFlat)
                            @foreach($technicalSpecs as $key => $value)
                                <div class="flex items-center border-b border-gray-100 dark:border-gray-700 last:border-b-0 p-4">
                                    <div class="w-1/2 text-sm text-gray-500 dark:text-gray-400">{{ $key }}</div>
                                    <div class="w-1/2 text-gray-900 dark:text-white font-medium">{{ $formatSpecValue($value) }}</div>
                                </div>
                            @endforeach
                        @else
                            @foreach($technicalSpecs as $sectionKey => $sectionValues)
                                @if(is_array($sectionValues) && !empty($sectionValues))
                                    @php
                                        $sectionTitle = $sectionValues['_title'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $sectionKey));
                                        $sectionIcon = $sectionValues['_icon'] ?? 'cog';
                                        unset($sectionValues['_title'], $sectionValues['_icon']);
                                    @endphp

                                    <div class="p-6 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                            <i class="fa-solid fa-{{ $sectionIcon }} text-blue-600"></i>
                                            {{ $sectionTitle }}
                                        </h3>
                                    </div>

                                    @foreach($sectionValues as $key => $value)
                                        @php
                                            $propertyLabel = is_array($value) && isset($value['_label']) ? $value['_label'] : $key;
                                            $propertyValue = is_array($value) && isset($value['_value']) ? $value['_value'] : $value;
                                        @endphp
                                        <div class="flex items-center border-b border-gray-100 dark:border-gray-700 last:border-b-0 p-4">
                                            <div class="w-1/2 text-sm text-gray-500 dark:text-gray-400">{{ $propertyLabel }}</div>
                                            <div class="w-1/2 text-gray-900 dark:text-white font-medium">{{ $formatSpecValue($propertyValue) }}</div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </section>
        @endif

        {{-- USE CASES --}}
        @if(!empty($useCases))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Kullanım Alanları</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($useCases as $case)
                            @php
                                $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                            @endphp
                            <div class="flex items-start gap-4 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-{{ $caseIcon }} text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 pt-2">{{ $caseText }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ACCESSORIES --}}
        @if(!empty($accessories))
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Aksesuarlar</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        @foreach($accessories as $accessory)
                            @php
                                $isStandard = $accessory['is_standard'] ?? false;
                                $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                            @endphp
                            <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl {{ $isStandard ? 'ring-2 ring-green-500' : '' }}">
                                @if($isStandard)
                                    <span class="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-medium rounded-full mb-4">Standart</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-full mb-4">Opsiyonel</span>
                                @endif

                                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-{{ $accIcon }} text-orange-600 dark:text-orange-400 text-xl"></i>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $accessory['name'] }}</h3>
                                @if(!empty($accessory['description']))
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $accessory['description'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CERTIFICATIONS --}}
        @if(!empty($certifications))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Sertifikalar</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($certifications as $cert)
                            @php
                                $certIcon = $cert['icon'] ?? 'certificate';
                            @endphp
                            <div class="text-center p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-{{ $certIcon }} text-blue-600 dark:text-blue-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">{{ $cert['name'] }}</h3>
                                @if(!empty($cert['year']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cert['year'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- WARRANTY --}}
        @if($warrantyInfo)
            <section class="py-16 bg-gray-50 dark:bg-gray-900">
                <div class="max-w-4xl mx-auto px-6 lg:px-8">
                    <div class="p-8 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl text-center">
                        <div class="w-16 h-16 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-shield-heart text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-light text-gray-900 dark:text-white mb-4">Garanti</h3>
                        @if(is_array($warrantyInfo))
                            @if($warrantyInfo['coverage'] ?? false)
                                <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $warrantyInfo['coverage'] }}</p>
                            @endif
                            @if($warrantyInfo['duration_months'] ?? false)
                                <div class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-emerald-900/40 rounded-full text-emerald-900 dark:text-emerald-100 font-medium">
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

        {{-- VARIANTS --}}
        @if($siblingVariants->count() > 0)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-6xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Varyantlar</h2>
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
                                @if($variant->variant_type)
                                    <span class="inline-block mt-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs rounded-full">{{ ucfirst(str_replace('-', ' ', $variant->variant_type)) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- FAQ --}}
        @if($faqEntries->isNotEmpty())
            <section class="py-16 bg-gray-50 dark:bg-gray-900" x-data="{ openFaq: null }">
                <div class="max-w-4xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Sık Sorulan Sorular</h2>
                    <div class="space-y-4">
                        @foreach($faqEntries as $index => $faq)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden">
                                <button @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                        class="w-full px-6 py-4 flex items-center justify-between text-left">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                    <i class="fa-solid fa-chevron-down text-gray-400 transition-transform" :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                </button>
                                <div x-show="openFaq === {{ $index }}" x-transition class="px-6 pb-4 text-gray-600 dark:text-gray-400" style="display: none;">
                                    {!! nl2br(e($faq['answer'])) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CONTACT FORM --}}
        <section id="contact" class="py-16 bg-white dark:bg-gray-800">
            <div class="max-w-3xl mx-auto px-6 lg:px-8">
                <h2 class="text-3xl font-light text-gray-900 dark:text-white mb-12 text-center">Teklif Al</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Ad Soyad *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Telefon *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">Mesajınız</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-4 rounded-xl transition-colors">
                        Teklif İste
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
