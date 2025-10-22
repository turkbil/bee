@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

{{-- V6: HYBRID - V4/V1 Content + V2 Sticky Sidebar --}}

@section('module_content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $shortDescription = $item->getTranslated('short_description', $currentLocale);
            $longDescription = $item->getTranslated('body', $currentLocale);

            // 📞 İletişim Bilgileri - Settings'ten al
            $contactPhone = setting('contact_phone_1');
            $contactWhatsapp = setting('contact_whatsapp_1');
            $contactEmail = setting('contact_email_1');

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
            $parentProduct = $parentProduct ?? null;
            $isVariantPage = $isVariantPage ?? false;
        @endphp

        {{-- 🎯 HERO SECTION --}}
        <section id="hero-section"
            class="relative min-h-screen flex items-center text-gray-900 dark:text-white overflow-hidden">
            {{-- Animated Background Blobs - Anasayfa ile aynı --}}
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-300 dark:bg-white rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 -right-20 w-96 h-96 bg-blue-300 dark:bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            </div>

            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 relative z-10">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-purple-100 dark:bg-white/20 backdrop-blur-lg px-4 py-2 rounded-full mb-6">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-sm font-medium text-purple-700 dark:text-white">Stokta Mevcut</span>
                        </div>

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                            {{ $title }}
                        </h1>

                        @if ($shortDescription)
                            <p class="text-xl text-gray-600 dark:text-purple-100 leading-relaxed mb-8">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#contact"
                                class="inline-flex items-center justify-center gap-3 bg-white text-purple-600 px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl transition-all">
                                <i class="fa-solid fa-envelope"></i>
                                <span>Teklif Al</span>
                            </a>
                            @if($contactPhone)
                                <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                                    class="inline-flex items-center justify-center gap-3 bg-gray-100 dark:bg-white/10 backdrop-blur-lg text-gray-900 dark:text-white border-2 border-gray-300 dark:border-white/30 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-gray-200 dark:hover:bg-white/20 transition-all">
                                    <i class="fa-solid fa-phone"></i>
                                    <span>{{ $contactPhone }}</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Right Column: AI Chat Widget + Featured Image --}}
                    <div class="hidden lg:block space-y-6 -mr-4 sm:-mr-6 lg:-mr-8">
                        {{-- AI Chat Widget - Always Open --}}
                        <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 overflow-hidden">
                            <x-ai.inline-widget title="Ürün Hakkında Soru Sor" :product-id="$item->product_id" :initially-open="true"
                                height="500px" theme="blue" />
                        </div>

                        @if ($featuredImage)
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                alt="{{ $title }}" class="w-full rounded-xl border border-white/30 dark:border-white/10">
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- 📑 TABLE OF CONTENTS - Initially relative, becomes fixed on scroll --}}
        {{-- Placeholder: TOC fixed olduğunda layout shift önlemek için --}}
        <div id="toc-placeholder" style="display: none;"></div>

        <div id="toc-bar"
            class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 left-0 right-0"
            style="position: relative; z-index: 40; transition: none;">
            <div id="toc-container" class="container mx-auto px-4 sm:px-6 lg:px-8 py-2"
                 style="transition: padding 0.2s ease-in-out;">
                <div class="flex items-center">
                    <div
                        class="flex-1 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
                        <div class="flex gap-2" id="toc-buttons">
                            @if ($siblingVariants->count() > 0)
                                <a href="#variants" data-target="variants"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-layer-group mr-1.5"></i>Varyantlar
                                </a>
                            @endif
                            @if ($galleryImages->count() > 0)
                                <a href="#gallery" data-target="gallery"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-images mr-1.5"></i>Galeri
                                </a>
                            @endif
                            @if ($longDescription)
                                <a href="#description" data-target="description"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-align-left mr-1.5"></i>Açıklama
                                </a>
                            @endif
                            @if ($highlightedFeatures->isNotEmpty() || !empty($featuresList))
                                <a href="#features" data-target="features"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-sparkles mr-1.5"></i>Özellikler
                                </a>
                            @endif
                            @if (!empty($competitiveAdvantages))
                                <a href="#competitive" data-target="competitive"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-trophy mr-1.5"></i>Avantajlar
                                </a>
                            @endif
                            @if (!empty($technicalSpecs))
                                <a href="#technical" data-target="technical"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-cogs mr-1.5"></i>Teknik
                                </a>
                            @endif
                            @if (!empty($accessories))
                                <a href="#accessories" data-target="accessories"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-puzzle-piece mr-1.5"></i>Aksesuarlar
                                </a>
                            @endif
                            @if (!empty($useCases))
                                <a href="#usecases" data-target="usecases"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-bullseye mr-1.5"></i>Kullanım
                                </a>
                            @endif
                            @if (!empty($targetIndustries))
                                <a href="#industries" data-target="industries"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-briefcase mr-1.5"></i>Sektörler
                                </a>
                            @endif
                            @if (!empty($certifications))
                                <a href="#certifications" data-target="certifications"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-certificate mr-1.5"></i>Sertifikalar
                                </a>
                            @endif
                            @if ($warrantyInfo)
                                <a href="#warranty" data-target="warranty"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-shield-heart mr-1.5"></i>Garanti
                                </a>
                            @endif
                            @if ($faqEntries->isNotEmpty())
                                <a href="#faq" data-target="faq"
                                    class="toc-link inline-flex items-center px-2 py-2 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-blue-600 dark:hover:text-white transition-all whitespace-nowrap">
                                    <i class="fa-solid fa-question-circle mr-1.5"></i>SSS
                                </a>
                            @endif
                            <a href="#contact" data-target="contact"
                                class="toc-link inline-flex items-center px-3 py-2 text-xs font-medium bg-blue-600 dark:bg-blue-600 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-700 transition-all whitespace-nowrap">
                                <i class="fa-solid fa-envelope mr-1.5"></i>Teklif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid lg:grid-cols-3 gap-8 items-start">
                {{-- LEFT: Main Content (2/3) --}}
                <div class="lg:col-span-2 min-h-screen">

                    {{-- 1. Long Description --}}
                    @if ($longDescription)
                        <section id="description" class="scroll-mt-24 mb-20 lg:mb-24">
                            <div class="prose prose-lg max-w-none dark:prose-invert prose-section-spacing">
                                @parsewidgets($longDescription)
                            </div>
                        </section>
                    @endif
                    @if (!empty($primarySpecs))
                        <section id="primary-specs" class="scroll-mt-24 mb-20 lg:mb-24">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                @foreach ($primarySpecs as $spec)
                                    <div class="group relative overflow-hidden">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 dark:from-blue-500/5 dark:to-purple-500/5 rounded-xl blur-lg group-hover:blur-xl transition-all">
                                        </div>
                                        <div
                                            class="relative bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-6 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                            {{-- İkon + Başlık (Üstte) --}}
                                            <div class="flex items-center gap-3 mb-4">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-bolt text-white text-lg"></i>
                                                </div>
                                                <h4 class="text-base font-semibold text-gray-700 dark:text-gray-300">
                                                    {{ $spec['label'] }}
                                                </h4>
                                            </div>

                                            {{-- Değer (Altta) --}}
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ $spec['value'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($highlightedFeatures->isNotEmpty() || !empty($featuresList))
                        <section id="features" class="scroll-mt-24 mb-20 lg:mb-24">
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-yellow-100 to-green-100 dark:from-yellow-900/30 dark:to-green-900/30 rounded-2xl mb-4">
                                    <i class="fa-solid fa-sparkles text-3xl text-yellow-600 dark:text-yellow-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Özellikler ve Avantajlar
                                </h2>
                                <p class="text-lg text-gray-600 dark:text-gray-400">Ürünü öne çıkaran tüm özellikler ve
                                    sağladığı faydalar</p>
                            </header>

                            @if ($highlightedFeatures->isNotEmpty())
                                <div class="grid grid-cols-1 gap-6 mb-8">
                                    @foreach ($highlightedFeatures as $feature)
                                        <div
                                            class="flex gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                            <div
                                                class="w-14 h-14 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i
                                                    class="fa-solid fa-{{ $feature['icon'] }} text-blue-600 dark:text-blue-400 text-xl"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 dark:text-white mb-2">
                                                    {{ $feature['title'] }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                                    {{ $feature['description'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (!empty($featuresList))
                                <div class="grid md:grid-cols-2 gap-6">
                                    @foreach ($featuresList as $feature)
                                        @php
                                            $featureIcon = is_array($feature)
                                                ? $feature['icon'] ?? 'check-circle'
                                                : 'check-circle';
                                            $featureText = is_array($feature) ? $feature['text'] ?? $feature : $feature;
                                        @endphp
                                        <div
                                            class="flex gap-3 items-start p-4 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                            <i
                                                class="fa-solid fa-{{ $featureIcon }} text-green-600 dark:text-green-400 mt-1 text-lg"></i>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endif

                    @if (!empty($competitiveAdvantages))
                        <section id="competitive" class="scroll-mt-24 mb-20 lg:mb-24">
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl mb-4">
                                    <i class="fa-solid fa-trophy text-3xl text-amber-600 dark:text-amber-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Ürünün Güçlü Yanları
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">Bu ürünü rakiplerinden ayıran
                                    özellikler</p>
                            </header>
                            <div class="grid md:grid-cols-2 gap-8">
                                @foreach ($competitiveAdvantages as $advantage)
                                    @php
                                        $advIcon = is_array($advantage) ? $advantage['icon'] ?? 'star' : 'star';
                                        $advText = is_array($advantage) ? $advantage['text'] ?? $advantage : $advantage;
                                    @endphp
                                    <div
                                        class="flex gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
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
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-2xl mb-4">
                                    <i class="fa-solid fa-cogs text-3xl text-slate-600 dark:text-slate-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Detaylı Teknik Özellikler
                                </h2>
                                <p class="text-lg text-gray-600 dark:text-gray-400">Ölçümler, performans ve kapasiteler</p>
                            </header>
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
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl mb-4">
                                    <i class="fa-solid fa-puzzle-piece text-3xl text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Aksesuarlar ve Opsiyonlar
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">Ekstra ekipmanlar ve seçenekler</p>
                            </header>
                            <div class="grid md:grid-cols-2 gap-8">
                                @foreach ($accessories as $accessory)
                                    @php
                                        $isStandard = $accessory['is_standard'] ?? false;
                                        $price = $accessory['price'] ?? null;
                                        $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                                    @endphp
                                    @if ($isStandard)
                                        <div
                                            class="bg-white/70 dark:bg-white/5 backdrop-blur-md border-2 border-green-500 dark:border-green-600 rounded-xl p-6 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                        @else
                                            <div
                                                class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-6 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
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

                @if (!empty($useCases))
                    <section id="usecases" class="scroll-mt-24 mb-20 lg:mb-24">
                        <header class="text-center mb-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-2xl mb-4">
                                <i class="fa-solid fa-bullseye text-3xl text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                Kullanım Alanları
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Hangi durumlarda kullanılır?</p>
                        </header>
                        <div class="grid sm:grid-cols-2 gap-6">
                            @foreach ($useCases as $case)
                                @php
                                    $caseIcon = is_array($case) ? $case['icon'] ?? 'check' : 'check';
                                    $caseText = is_array($case) ? $case['text'] ?? $case : $case;
                                @endphp
                                <div class="group relative">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 dark:from-blue-500/5 dark:to-cyan-500/5 rounded-xl blur-lg group-hover:blur-xl transition-all">
                                    </div>
                                    <div
                                        class="relative flex gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 dark:from-blue-600 dark:to-cyan-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-{{ $caseIcon }} text-white text-lg"></i>
                                        </div>
                                        <p
                                            class="flex-1 min-w-0 text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                            {{ $caseText }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (!empty($targetIndustries))
                    <section id="industries" class="scroll-mt-24 mb-20 lg:mb-24">
                        <header class="text-center mb-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-2xl mb-4">
                                <i class="fa-solid fa-briefcase text-3xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                Uygun Olduğu Sektörler
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Hangi iş kollarında tercih ediliyor?</p>
                        </header>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($targetIndustries as $industry)
                                @php
                                    $industryIcon = is_array($industry)
                                        ? $industry['icon'] ?? 'briefcase'
                                        : 'briefcase';
                                    $industryText = is_array($industry) ? $industry['text'] ?? $industry : $industry;
                                @endphp
                                <div class="group relative">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-500/10 dark:from-purple-500/5 dark:to-pink-500/5 rounded-xl blur-lg group-hover:blur-xl transition-all">
                                    </div>
                                    <div
                                        class="relative flex flex-col items-center text-center p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                        <div
                                            class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 dark:from-purple-600 dark:to-pink-700 rounded-lg flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-{{ $industryIcon }} text-white text-2xl"></i>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed font-medium">
                                            {{ $industryText }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Certifications --}}
                @if (!empty($certifications))
                    <section id="certifications" class="scroll-mt-24 mb-20 lg:mb-24">
                        <header class="text-center mb-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-100 to-blue-100 dark:from-cyan-900/30 dark:to-blue-900/30 rounded-2xl mb-4">
                                <i class="fa-solid fa-certificate text-3xl text-cyan-600 dark:text-cyan-400"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                Sertifikalar ve Uygunluklar
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Kalite belgelerimiz ve standartlara
                                uygunluk</p>
                        </header>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach ($certifications as $cert)
                                @php
                                    $certIcon = $cert['icon'] ?? 'certificate';
                                @endphp
                                <div
                                    class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-6 hover:bg-white/80 dark:hover:bg-white/10 transition-all text-center">
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
                        <header class="text-center mb-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-100 to-green-100 dark:from-emerald-900/30 dark:to-green-900/30 rounded-2xl mb-4">
                                <i class="fa-solid fa-shield-heart text-3xl text-emerald-600 dark:text-emerald-400"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                Garanti Bilgisi
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Güvenle alın, endişesiz kullanın</p>
                        </header>
                        <div
                            class="flex gap-6 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                            <div
                                class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-shield-heart text-emerald-600 dark:text-emerald-400 text-3xl"></i>
                            </div>
                            <div class="flex-1">
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

                {{-- FAQ --}}
                @if ($faqEntries->isNotEmpty())
                    <section id="faq" x-data="{ openFaq: null }" class="scroll-mt-24 mb-20 lg:mb-24">
                        <header class="text-center mb-12">
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-pink-100 to-rose-100 dark:from-pink-900/30 dark:to-rose-900/30 rounded-2xl mb-4">
                                <i class="fa-solid fa-question-circle text-3xl text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                Sık Sorulan Sorular
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Merak ettikleriniz hakkında bilgiler</p>
                        </header>
                        <div class="space-y-4">
                            @foreach ($faqEntries as $index => $faq)
                                <div
                                    class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl overflow-hidden hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                    <button
                                        @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                        class="w-full px-6 py-5 flex items-center justify-between gap-4 text-left transition-colors"
                                        :class="openFaq === {{ $index }} ? 'bg-blue-50 dark:bg-gray-700' :
                                            'bg-white dark:bg-gray-800'">
                                        <span
                                            class="flex-1 font-semibold text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                        <i class="fa-solid fa-chevron-down text-gray-400 dark:text-gray-500 transition-transform"
                                            :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div
                                        x-show="openFaq === {{ $index }}"
                                        x-transition:enter="transition-all ease-out duration-300"
                                        x-transition:enter-start="opacity-0 max-h-0"
                                        x-transition:enter-end="opacity-100 max-h-96"
                                        x-transition:leave="transition-all ease-in duration-200"
                                        x-transition:leave-start="opacity-100 max-h-96"
                                        x-transition:leave-end="opacity-0 max-h-0"
                                        class="border-t border-gray-200 dark:border-gray-700 overflow-hidden"
                                        style="display: none;">
                                        <div
                                            class="px-6 py-5 bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 leading-relaxed">
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
                    <div
                        class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $title }}</h1>

                        @if ($shortDescription)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6 leading-relaxed">
                                {{ $shortDescription }}</p>
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
                                    @if($contactPhone)
                                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                                            class="flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium py-3 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white hover:border-blue-400 dark:hover:border-blue-500 transition-all">
                                            <i class="fa-solid fa-phone"></i>Ara
                                        </a>
                                    @endif
                                    @if($contactWhatsapp)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank"
                                            class="flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium py-3 rounded-lg transition-colors">
                                            <i class="fa-brands fa-whatsapp"></i>WhatsApp
                                        </a>
                                    @endif
                                </div>

                                {{-- PDF İndir Butonu --}}
                                @php
                                    $pdfSlug = $item->getTranslated('slug', $currentLocale);
                                @endphp
                                <a href="{{ route('shop.pdf', ['slug' => $pdfSlug]) }}" target="_blank"
                                    class="flex items-center justify-center gap-2 border-2 border-red-500 dark:border-red-600 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 text-sm font-medium py-3 rounded-lg hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-300 hover:border-red-600 dark:hover:border-red-500 transition-all">
                                    <i class="fa-solid fa-file-pdf"></i>PDF İndir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Trust Signals - Modern 4 Column (Before Contact Form) --}}
    <section id="trust-signals" class="relative mt-16 scroll-mt-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl py-12 px-6">
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
    <section id="contact" class="relative mt-32 overflow-hidden bg-gradient-to-br from-purple-600 via-purple-700 to-orange-600 dark:from-purple-900 dark:via-purple-800 dark:to-yellow-600">
        {{-- Animated Background Blobs --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-300 dark:bg-white rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 -right-20 w-96 h-96 bg-orange-300 dark:bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
            <div class="flex flex-col md:flex-row gap-8 items-start">
                {{-- SOL: FORM (7/12) --}}
                <div class="w-full md:w-7/12">
                    <div
                        class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-white/30 dark:border-white/10 p-8 md:p-10">
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
                                class="group relative w-full bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 hover:from-blue-600 hover:to-blue-700 dark:hover:from-blue-700 dark:hover:to-blue-800 text-white font-bold py-5 rounded-xl transition-all transform hover:-translate-y-0.5">
                                <span class="flex items-center justify-center gap-3">
                                    <i
                                        class="fa-solid fa-paper-plane text-xl group-hover:rotate-45 transition-transform"></i>
                                    <span class="text-lg">Teklif İsteyin</span>
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- SAĞ: DETAYLAR (5/12) - V3 Glassmorphism --}}
                <div class="w-full md:w-5/12 space-y-6">
                    {{-- İletişim Bilgileri - V3 Glassmorphism --}}
                    @if($contactPhone)
                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                            class="group flex items-start gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-3xl hover:scale-105 hover:shadow-2xl hover:bg-white/80 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <i class="fa-solid fa-phone text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-1 font-semibold">Telefon</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Hemen arayın</div>
                                <div class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $contactPhone }}</div>
                            </div>
                        </a>
                    @endif

                    @if($contactWhatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank"
                            class="group flex items-start gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-3xl hover:scale-105 hover:shadow-2xl hover:bg-white/80 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                <i class="fa-brands fa-whatsapp text-white text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-700 dark:text-gray-300 mb-1 font-semibold">WhatsApp</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Anında mesajlaşın</div>
                                <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ $contactWhatsapp }}</div>
                            </div>
                        </a>
                    @endif

                    @if($contactEmail)
                        <a href="mailto:{{ $contactEmail }}"
                        class="group flex items-start gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-3xl hover:scale-105 hover:shadow-2xl hover:bg-white/80 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <i class="fa-solid fa-envelope text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-gray-700 dark:text-gray-300 mb-1 font-semibold">E-posta</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Mail gönderin</div>
                            <div class="text-base font-bold text-purple-600 dark:text-purple-400 break-all">{{ $contactEmail }}</div>
                        </div>
                    </a>
                    @endif

                    {{-- AI Canlı Destek Kutusu - V3 Glassmorphism --}}
                    <div class="group flex items-start gap-4 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-3xl hover:scale-105 hover:shadow-2xl hover:bg-white/80 dark:hover:bg-white/10 transition-all duration-300 cursor-pointer relative"
                         @click="$store.aiChat.openFloating()">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <i class="fa-solid fa-robot text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-cyan-500/30 to-blue-500/30 backdrop-blur-sm text-gray-900 dark:text-white text-xs font-bold rounded-full mb-2 italic border border-cyan-300 dark:border-cyan-600">
                                <i class="fa-solid fa-sparkles text-yellow-600 dark:text-yellow-400"></i>
                                Yapay Zeka
                            </span>
                            <div class="text-base font-bold text-gray-900 dark:text-white mb-1">Canlı Destek</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-comments mr-1"></i> Sohbete Başla
                            </div>
                        </div>
                    </div>

                    {{-- AI Chat click handler moved to shop-product-show.js --}}

                    {{-- Info Box --}}
                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-2xl p-6 border border-white/30 dark:border-white/10">
                        <div class="text-center mb-5">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Türkiye'nin İstif Pazarı
                            </h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Forklift ve İstif Makineleri Merkezi
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-box text-blue-600 dark:text-blue-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">Satın Alma</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-key text-yellow-600 dark:text-yellow-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">Kiralama</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-recycle text-green-600 dark:text-green-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">İkinci El</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-gears text-orange-600 dark:text-orange-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">Yedek Parça</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-wrench text-purple-600 dark:text-purple-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">Teknik Servis</div>
                            </div>
                            <div class="text-center p-3 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 hover:bg-white/80 dark:hover:bg-white/10 transition-all">
                                <i class="fa-solid fa-robot text-cyan-600 dark:text-cyan-400 text-xl mb-2"></i>
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">AI Canlı Destek</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- AI Chat Section --}}
    <section class="py-16 bg-white dark:bg-gray-800">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Inline AI Widget - Commented out, moved to hero section
            <x-ai.inline-widget
                title="Ürün Hakkında Soru Sor"
                :product-id="$item->product_id"
                :initially-open="false"
                height="600px"
                theme="blue"
            />
            --}}
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

