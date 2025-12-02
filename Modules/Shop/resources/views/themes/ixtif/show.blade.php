@php
    $themeService = app(\App\Services\ThemeService::class);
    $activeTheme = $themeService->getActiveTheme();
    $themeName = $activeTheme ? $activeTheme->name : 'simple';
@endphp
@extends('themes.' . $themeName . '.layouts.app')

{{-- V6: HYBRID - V4/V1 Content + V2 Sticky Sidebar --}}

@push('head')
{{-- Product View Event --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.trackProductView === 'function') {
            window.trackProductView(
                {{ $item->id }},
                @json($item->getTranslated('title', app()->getLocale())),
                @json($item->category->title ?? 'Uncategorized'),
                null
            );
        }
    });
</script>
@endpush

@section('module_content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);

            // Fallback: Eğer title slug formatında ise gösterme (örn: "basliksiz-16", "product-slug")
            if (str_starts_with($title, 'basliksiz-')) {
                $title = null; // "basliksiz-X" formatı = slug, başlık gösterilmeyecek
            } elseif (strlen($title) < 50 && !str_contains($title, ' ') && str_contains($title, '-')) {
                $title = null; // Kısa, boşluksuz, tire içeren = slug formatı
            }

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

            // Helper function ile multi-collection fallback
            $featuredImage = getFirstMediaWithFallback($item);
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
            class="relative flex items-center text-gray-900 dark:text-white overflow-hidden py-12 md:py-16">
            {{-- Animated Background Blobs - Anasayfa ile aynı --}}
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-300 dark:bg-white rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 -right-20 w-96 h-96 bg-blue-300 dark:bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
            </div>

            <div class="container mx-auto px-6 relative z-10"
                 x-data="{
                     // Fotoğraf yoksa açık başla, varsa kapalı
                     isChatOpen: {{ $featuredImage ? 'false' : 'true' }}
                 }">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        @if($item->stock_tracking && $item->current_stock > 0)
                            <div class="inline-flex items-center gap-2 bg-purple-100 dark:bg-white/20 backdrop-blur-lg px-4 py-2 rounded-full mb-6">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                <span class="text-sm font-medium text-purple-700 dark:text-white">Stokta Var</span>
                            </div>
                        @endif

                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                            {{ $title }}
                        </h1>

                        @if ($shortDescription)
                            <p class="text-xl text-gray-600 dark:text-purple-100 leading-relaxed mb-8">
                                {{ $shortDescription }}
                            </p>
                        @endif

                        {{-- Fiyat Gösterimi --}}
                        @if($item->base_price && $item->base_price > 0)
                            <div class="mb-8">
                                <div class="inline-flex flex-col gap-2">
                                    <div class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600 dark:from-purple-400 dark:to-blue-400">
                                        {{ formatPrice($item->base_price, $item->currency ?? 'TRY') }}
                                    </div>
                                    @if($item->compare_at_price && $item->compare_at_price > $item->base_price)
                                        <div class="text-xl text-gray-500 dark:text-gray-400 line-through">
                                            {{ formatPrice($item->compare_at_price, $item->currency ?? 'TRY') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col gap-4">
                            {{-- CTA Buttons - MOBİL: 3'lü grid | DESKTOP: Normal flex --}}
                            <div class="grid grid-cols-3 lg:flex gap-2 lg:gap-4">
                                {{-- Sepete Ekle Butonu --}}
                                @if(!$item->price_on_request && $item->base_price > 0)
                                    @livewire('shop::front.add-to-cart-button', [
                                        'productId' => $item->product_id,
                                        'quantity' => 1,
                                        'buttonText' => 'Sepete Ekle',
                                        'buttonClass' => 'inline-flex items-center justify-center gap-2 lg:gap-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-3 lg:px-8 py-4 rounded-2xl font-bold text-base lg:text-lg shadow-lg hover:shadow-2xl transition-all',
                                        'showQuantity' => false
                                    ])
                                @endif

                                {{-- Teklif Al --}}
                                <a href="#contact"
                                    class="inline-flex items-center justify-center gap-2 lg:gap-3 bg-white text-purple-600 px-3 lg:px-8 py-4 rounded-2xl font-bold text-base lg:text-lg hover:shadow-2xl transition-all group relative"
                                    x-data="{ showTooltip: false }"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false">
                                    <i class="fa-solid fa-envelope text-lg lg:text-xl"></i>
                                    <span class="hidden lg:inline">Teklif Al</span>
                                    <div x-show="showTooltip" x-transition class="absolute bottom-full mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg whitespace-nowrap pointer-events-none z-50 lg:hidden">
                                        Teklif Al
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </a>

                                {{-- WhatsApp --}}
                                @if($contactWhatsapp)
                                    <a href="{{ whatsapp_link() }}" target="_blank"
                                        class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white border-2 border-green-500 hover:border-green-600 px-3 lg:px-4 py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-2xl transition-all group relative"
                                        x-data="{ showTooltip: false }"
                                        @mouseenter="showTooltip = true"
                                        @mouseleave="showTooltip = false">
                                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                                        <div x-show="showTooltip" x-transition class="absolute bottom-full mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg whitespace-nowrap pointer-events-none z-50">
                                            WhatsApp ile İletişime Geç
                                            <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </a>
                                @endif

                                {{-- Yapay Zeka Chatbot - Sadece ikon (desktop) --}}
                                <button @click="isChatOpen = true"
                                    class="hidden lg:inline-flex items-center justify-center bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 text-white px-3 lg:px-4 py-4 rounded-2xl font-bold text-lg shadow-lg hover:shadow-2xl hover:scale-105 transition-all group relative"
                                    x-data="{ showTooltip: false }"
                                    @mouseenter="showTooltip = true"
                                    @mouseleave="showTooltip = false">
                                    <i class="fa-solid fa-robot text-2xl"></i>
                                    <div x-show="showTooltip" x-transition class="absolute bottom-full mb-2 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg whitespace-nowrap pointer-events-none z-50">
                                        Yapay Zeka ile Soru Sor
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </button>
                            </div>

                            {{-- Yapay Zeka ile Soru Sor - Mobil ve tablet için büyük buton (masaüstünde gizli) --}}
                            <button @click="isChatOpen = true"
                                class="lg:hidden inline-flex items-center justify-center gap-3 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl hover:scale-105 transition-all self-start">
                                <i class="fa-solid fa-robot"></i>
                                <span>Yapay Zeka ile Soru Sor</span>
                            </button>
                        </div>
                    </div>

                    {{-- Right Column: AI Chat Widget + Featured Image --}}
                    <div class="hidden lg:block -mr-4 sm:-mr-6 lg:-mr-8">

                        {{-- Sabit yükseklik container (hero değişmesin) --}}
                        <div class="relative h-[564px]">
                            {{-- AI Chat Widget - Always Open mode (kendi toggle'ı yok) --}}
                            <div x-show="isChatOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute inset-0 bg-white/70 dark:bg-white/5 backdrop-blur-md rounded-xl border border-white/30 dark:border-white/10 overflow-hidden">
                                <x-ai.inline-widget
                                    title="Ürün Hakkında Soru Sor"
                                    :product-id="$item->product_id"
                                    :always-open="true"
                                    height="500px"
                                    theme="blue" />

                                {{-- Kapatma butonu (sadece fotoğraf varsa göster) --}}
                                @if ($featuredImage)
                                    <button @click="isChatOpen = false"
                                            class="absolute top-4 right-4 w-10 h-10 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center text-white hover:scale-110 transition-all shadow-lg z-50 group">
                                        <i class="fa-solid fa-times text-xl group-hover:rotate-90 transition-transform"></i>
                                    </button>
                                @endif
                            </div>

                            {{-- Featured Image - Dark mode fix + Toggle butonu --}}
                            @if ($featuredImage)
                                <div x-show="!isChatOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0 rounded-xl overflow-hidden group"
                                     x-data="{ imgLoaded: false }"
                                     x-init="$nextTick(() => { if ($refs.featImg && $refs.featImg.complete) { imgLoaded = true; } })">
                                    {{-- Backdrop (PNG transparanlık fix) - Anasayfa ürün kartı ile aynı --}}
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-slate-600 dark:via-slate-500 dark:to-slate-600"></div>

                                    {{-- Blur Placeholder (LQIP) - Mini 50x50 ~3KB --}}
                                    <img src="{{ thumb($featuredImage, 50, 50, ['quality' => 50, 'scale' => 0, 'format' => 'webp']) }}"
                                         alt="{{ $title }}"
                                         x-show="!imgLoaded"
                                         class="absolute w-full h-full rounded-xl object-contain blur-2xl scale-110">

                                    {{-- Fotoğraf (border yok, sabit yükseklik) --}}
                                    <img x-ref="featImg"
                                         src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                         alt="{{ $title }}"
                                         x-show="imgLoaded"
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         class="relative w-full h-full rounded-xl object-contain z-10"
                                         @load="imgLoaded = true"
                                         loading="lazy">

                                    {{-- "Ürün Hakkında Soru Sor" butonu - Sağ alt köşe, küçük, hover'da büyür --}}
                                    <button @click="isChatOpen = true"
                                            class="absolute bottom-4 right-4 bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-2xl hover:scale-110 hover:px-6 transition-all flex items-center gap-2 backdrop-blur-sm z-10 opacity-90 hover:opacity-100 group-hover:bottom-6 group-hover:right-6">
                                        <i class="fa-solid fa-comments text-base"></i>
                                        <span class="text-xs whitespace-nowrap">Soru Sor</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 📋 TOC BAR - Header ile beraber hareket edecek --}}
        <nav id="toc-bar" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border-b border-gray-200 dark:border-slate-700 shadow-sm z-40 transition-all duration-300">
            <div class="container mx-auto px-6">
                <div class="flex items-center gap-2 overflow-x-auto py-1.5 scrollbar-hide">
                    @if ($longDescription)
                        <a href="#description"
                           class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                            <i class="fa-solid fa-file-lines mr-2"></i>Ürün Detayı
                        </a>
                    @endif

                    @if (!empty($primarySpecs))
                        <a href="#primary-specs"
                           class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                            <i class="fa-solid fa-star mr-2"></i>Öne Çıkan Özellikler
                        </a>
                    @endif

                    @if ($highlightedFeatures->isNotEmpty() || !empty($featuresList))
                        <a href="#features"
                           class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                            <i class="fa-solid fa-list-check mr-2"></i>Özellikler
                        </a>
                    @endif

                    @if ($faqEntries->isNotEmpty())
                        <a href="#faq"
                           class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                            <i class="fa-solid fa-circle-question mr-2"></i>S.S.S
                        </a>
                    @endif

                    <a href="#trust-signals"
                       class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                        <i class="fa-solid fa-shield-halved mr-2"></i>Güvenilirlik
                    </a>

                    <a href="#contact"
                       class="toc-link flex-shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all whitespace-nowrap">
                        <i class="fa-solid fa-envelope mr-2"></i>İletişim
                    </a>
                </div>
            </div>
        </nav>

        <div class="container mx-auto px-6">
            <div id="product-content-grid" class="grid lg:grid-cols-3 gap-8 items-start relative py-8">
                {{-- LEFT: Main Content (2/3) --}}
                <div id="main-content-column" class="lg:col-span-2 min-h-screen">

                    {{-- 1. Long Description --}}
                    @if ($longDescription)
                        <section id="description" class="scroll-mt-24 mb-20 lg:mb-24">
                            <div class="prose prose-lg max-w-none dark:prose-invert prose-section-spacing">
                                @php
                                    // İki aşamalı render: Önce widget parse, sonra Blade render
                                    $parsedDescription = parse_widget_shortcodes($longDescription ?? '');

                                    // 🎨 POST-PROCESSING: Görsellere lazy loading + Thumbmaker + Lightbox
                                    $parsedDescription = process_blog_images($parsedDescription);
                                @endphp
                                {!! Blade::render($parsedDescription, [], true) !!}
                            </div>
                        </section>
                    @endif

                    {{-- 2. Primary Specs (4'lü Önemli Kutu) --}}
                    @if (!empty($primarySpecs))
                        <section id="primary-specs" class="scroll-mt-24 mb-20 lg:mb-24">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
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

                    {{-- 3. Features (Özellikler) --}}
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
                                <div class="grid grid-cols-1 gap-8 mb-8">
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
                                <div class="grid md:grid-cols-2 gap-8">
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

                    {{-- 4. Competitive Advantages (Avantajlar) --}}
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

                    {{-- 5. Gallery (Galeri) --}}
                    @if ($galleryImages->count() > 0)
                        <section id="gallery" class="scroll-mt-24 mb-20 lg:mb-24">
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl mb-4">
                                    <i class="fa-solid fa-images text-3xl text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Ürün Görselleri
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">Ürünü farklı açılardan inceleyin</p>
                            </header>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                                @foreach ($galleryImages as $image)
                                    @php
                                        $altText = $image->getCustomProperty('alt_text')[$currentLocale] ?? $image->name ?? 'Ürün görseli';
                                        $title = $image->getCustomProperty('title')[$currentLocale] ?? $image->name ?? '';
                                        $description = $image->getCustomProperty('description')[$currentLocale] ?? '';
                                    @endphp
                                    <a href="{{ thumb($image, 1920, 1920, ['quality' => 90, 'scale' => 0]) }}"
                                        class="glightbox block rounded-xl overflow-hidden hover:ring-2 ring-blue-500 dark:ring-blue-400 transition-all hover:scale-105"
                                        data-gallery="prod"
                                        @if($title) data-title="{{ $title }}" @endif
                                        @if($description) data-description="{{ $description }}" @endif>
                                        <div class="relative w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200" x-data="{ loaded: false }">
                                            <div x-show="!loaded" class="absolute inset-0 z-10">
                                                <div class="w-full h-full relative overflow-hidden">
                                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent skeleton-shimmer"></div>
                                                </div>
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <i class="fa-solid fa-image text-3xl text-gray-300 animate-pulse"></i>
                                                </div>
                                            </div>
                                            <img src="{{ thumb($image, 400, 400, ['quality' => 85, 'scale' => 1, 'alignment' => 'c']) }}"
                                                 alt="{{ $altText }}"
                                                 class="w-full h-full object-cover transition-opacity duration-300"
                                                 :class="{ 'opacity-0': !loaded, 'opacity-100': loaded }"
                                                 @load="loaded = true"
                                                 loading="lazy"
                                                 decoding="async">
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- 6. Variants (Varyantlar) --}}
                    @if ($siblingVariants->count() > 0)
                        <section id="variants" class="scroll-mt-24 mb-20 lg:mb-24">
                            <header class="text-center mb-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl mb-4">
                                    <i class="fa-solid fa-layer-group text-3xl text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                                    Ürün Varyantları
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400 text-lg">İhtiyacınıza en uygun modeli keşfedin
                                </p>
                            </header>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                @foreach ($siblingVariants as $variant)
                                    @php
                                        $variantTitle =
                                            $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                        $variantDescription = $variant->getTranslated(
                                            'short_description',
                                            $currentLocale,
                                        );
                                        $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl(
                                            $variant,
                                            $currentLocale,
                                        );
                                        // Helper function ile multi-collection fallback
                                        $variantImage = getFirstMediaWithFallback($variant);

                                        // ✅ Fallback: Varyant fotoğrafı yoksa parent ürün (ana ürün) fotoğrafını kullan
                                        if (!$variantImage) {
                                            $variantImage = getFirstMediaWithFallback($item);
                                        }

                                        $variantImageUrl = $variantImage
                                            ? ($variantImage->hasGeneratedConversion('thumb')
                                                ? $variantImage->getUrl('thumb')
                                                : $variantImage->getUrl())
                                            : null;
                                    @endphp
                                    <a href="{{ $variantUrl }}"
                                        class="group bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl overflow-hidden hover:bg-white/80 dark:hover:bg-white/10 hover:-translate-y-1 transition-all duration-300">

                                        @if ($variantImageUrl)
                                            <div class="aspect-[4/3] overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 relative" x-data="{ loaded: false }">
                                                <div x-show="!loaded" class="absolute inset-0 z-10">
                                                    <div class="w-full h-full relative overflow-hidden">
                                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/40 to-transparent skeleton-shimmer"></div>
                                                    </div>
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <i class="fa-solid fa-box text-3xl text-gray-300 dark:text-gray-500 animate-pulse"></i>
                                                    </div>
                                                </div>
                                                <img src="{{ $variantImageUrl }}"
                                                     alt="{{ $variantTitle }}"
                                                     class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500"
                                                     :class="{ 'opacity-0': !loaded, 'opacity-100': loaded }"
                                                     @load="loaded = true"
                                                     loading="lazy"
                                                     decoding="async">
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
                                                class="flex items-center gap-2 text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded border border-gray-200 dark:border-gray-700">
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

                    {{-- 7. Technical Specs (Detaylı Teknik Özellikler) --}}
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
                        <div class="grid sm:grid-cols-2 gap-8">
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
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
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

                {{-- Review & Rating Section --}}
                <section id="yorumlar-ve-degerlendirmeler" class="scroll-mt-24 mb-20 lg:mb-24">
                    <header class="text-center mb-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-amber-100 to-yellow-100 dark:from-amber-900/30 dark:to-yellow-900/30 rounded-2xl mb-4">
                            <i class="fa-solid fa-star text-3xl text-amber-500 dark:text-amber-400"></i>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                            Değerlendirmeler
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-400">Müşteri yorumları ve puanları</p>
                    </header>

                    {{-- Rating Summary --}}
                    @php
                        $averageRating = method_exists($item, 'averageRating') ? $item->averageRating() : 5;
                        $ratingsCount = method_exists($item, 'ratingsCount') ? $item->ratingsCount() : 1;
                        $currentUserRating = auth()->check() && method_exists($item, 'userRating') ? $item->userRating(auth()->id()) : 0;
                        $starsDistribution = method_exists($item, 'getStarsDistribution') ? $item->getStarsDistribution() : [1=>0,2=>0,3=>0,4=>0,5=>1];
                    @endphp

                    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-2xl p-8 mb-8">
                        <div class="grid md:grid-cols-2 gap-8 items-center">
                            {{-- Left: Average Rating --}}
                            <div class="text-left">
                                <div class="flex items-center justify-start gap-3 mb-2">
                                    <span class="text-5xl font-bold text-gray-900 dark:text-white">{{ number_format($averageRating, 1) }}</span>
                                    <div class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= round($averageRating) ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600' }} text-xl"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">{{ $ratingsCount }} değerlendirme</p>
                            </div>

                            {{-- Right: Stars Distribution --}}
                            <div class="space-y-2">
                                @for($star = 5; $star >= 1; $star--)
                                    @php
                                        $count = $starsDistribution[$star] ?? 0;
                                        $percentage = $ratingsCount > 0 ? ($count / $ratingsCount) * 100 : 0;
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $star }} <i class="fas fa-star text-yellow-400 text-xs"></i></span>
                                        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-yellow-400 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="w-8 text-sm text-gray-500 dark:text-gray-400 text-right">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Interactive Rating (for logged-in users) --}}
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700"
                             x-data="productRating({
                                 modelClass: 'Modules\\Shop\\App\\Models\\ShopProduct',
                                 modelId: {{ $item->product_id }},
                                 currentRating: {{ $currentUserRating }},
                                 averageRating: {{ $averageRating }},
                                 ratingsCount: {{ $ratingsCount }},
                                 isAuthenticated: {{ auth()->check() ? 'true' : 'false' }}
                             })">

                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-center sm:text-left">
                                    <p class="font-medium text-gray-900 dark:text-white mb-1">Bu ürünü değerlendirin</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Deneyiminizi paylaşın</p>
                                </div>

                                {{-- Star Rating Input --}}
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        <template x-for="star in 5" :key="star">
                                            <button @click="rateItem(star)"
                                                    @mouseenter="hoverRating = star"
                                                    @mouseleave="hoverRating = 0"
                                                    class="text-2xl transition-transform hover:scale-110 cursor-pointer focus:outline-none">
                                                <i :class="getStarClass(star)"></i>
                                            </button>
                                        </template>
                                    </div>

                                    {{-- Toast Message --}}
                                    <div x-show="showMessage"
                                         x-transition.opacity
                                         :class="{
                                             'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200': messageType === 'success',
                                             'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200': messageType === 'error',
                                             'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200': messageType === 'warning'
                                         }"
                                         class="ml-4 px-3 py-1.5 rounded-lg text-sm font-medium">
                                        <span x-text="message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reviews List Component --}}
                    @include('reviewsystem::components.review-list', [
                        'model' => $item,
                        'showForm' => true,
                        'perPage' => 10
                    ])
                </section>

            </div>

            {{-- RIGHT: Sticky Sidebar (1/3) - Modern Native Sticky (MOBİLDE GİZLİ!) --}}
            <div class="hidden lg:block lg:col-span-1 order-first lg:order-last relative">
                <aside id="sticky-sidebar" class="space-y-8">
                    {{-- Product Info Card --}}
                    <div
                        class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 rounded-xl p-6">
                        @if ($shortDescription)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 leading-relaxed">
                                {{ $shortDescription }}</p>
                        @endif

                        {{-- CTA Section - V6 SaaS Modern Style --}}
                        <div class="mb-6">
                            {{-- Fiyat Kutusu --}}
                            @if($item->base_price && $item->base_price > 0)
                                <div class="rounded-xl p-6 mb-4"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    @if($item->stock_tracking && $item->current_stock > 0)
                                        <div class="inline-flex items-center gap-2 bg-white/20 px-3 py-1 rounded-md text-xs text-white mb-2">
                                            <span class="w-1.5 h-1.5 bg-green-400"></span>
                                            Stokta Var
                                        </div>
                                    @endif
                                    <div class="text-2xl font-bold text-white mb-1">{{ formatPrice($item->base_price, $item->currency ?? 'TRY') }}</div>
                                    @if($item->compare_at_price && $item->compare_at_price > $item->base_price)
                                        <div class="text-sm text-white/80 line-through">{{ formatPrice($item->compare_at_price, $item->currency ?? 'TRY') }}</div>
                                    @endif
                                </div>
                            @endif

                            {{-- CTA Buttons - MOBILE: 3'lü grid sadece ikonlar | DESKTOP: Normal --}}

                            {{-- MOBİL GÖRÜNÜM (< lg): 3'lü grid, sadece ikonlar --}}
                            <div class="lg:hidden space-y-3">
                                <div class="grid grid-cols-3 gap-2">
                                    {{-- Teklif Al İkonu --}}
                                    <a href="#contact"
                                        class="flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white py-4 rounded-lg transition-colors">
                                        <i class="fa-solid fa-envelope text-xl"></i>
                                    </a>

                                    {{-- Telefon İkonu --}}
                                    @if($contactPhone)
                                        <a href="tel:{{ str_replace(' ', '', $contactPhone) }}"
                                            class="flex items-center justify-center bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-700 text-white py-4 rounded-lg transition-colors">
                                            <i class="fa-solid fa-phone text-xl"></i>
                                        </a>
                                    @else
                                        <div class="flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-500 py-4 rounded-lg opacity-50">
                                            <i class="fa-solid fa-phone text-xl"></i>
                                        </div>
                                    @endif

                                    {{-- WhatsApp İkonu --}}
                                    @if($contactWhatsapp)
                                        <a href="{{ whatsapp_link() }}" target="_blank"
                                            class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white py-4 rounded-lg transition-colors">
                                            <i class="fa-brands fa-whatsapp text-xl"></i>
                                        </a>
                                    @else
                                        <div class="flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-500 py-4 rounded-lg opacity-50">
                                            <i class="fa-brands fa-whatsapp text-xl"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- PDF İndir (Altta) --}}
                                @php
                                    $pdfSlug = $item->getTranslated('slug', $currentLocale);
                                @endphp
                                <a href="{{ route('shop.pdf', ['slug' => $pdfSlug]) }}" target="_blank"
                                    class="flex items-center justify-center gap-2 border-2 border-red-500 dark:border-red-600 bg-white dark:bg-gray-800 text-red-600 dark:text-red-400 text-sm font-medium py-3 rounded-lg hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-900/20 dark:hover:text-red-300 hover:border-red-600 dark:hover:border-red-500 transition-all">
                                    <i class="fa-solid fa-file-pdf"></i>PDF İndir
                                </a>
                            </div>

                            {{-- DESKTOP GÖRÜNÜM (>= lg): Normal butonlar --}}
                            <div class="hidden lg:block space-y-4">
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
                                        <a href="{{ whatsapp_link() }}" target="_blank"
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
                </aside> {{-- Close sticky-sidebar --}}
            </div> {{-- Close lg:col-span-1 --}}
        </div>
    </div>


    {{-- Trust Signals - Modern 4 Column (Before Contact Form) --}}
    <section id="trust-signals" class="relative scroll-mt-24">
        <div class="container mx-auto px-6">
            <div
                class="bg-gradient-to-r from-blue-600 to-blue-700 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl py-12 px-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
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

                    {{-- TEKNİK SERVİS --}}
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-wrench text-4xl md:text-5xl text-yellow-400"></i>
                        <div>
                            <div class="font-bold text-base md:text-lg text-white">TEKNİK SERVİS</div>
                            <div class="text-xs md:text-sm text-blue-100">Profesyonel</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 📬 MODERN CONTACT FORM --}}
    <section id="contact" class="relative mt-12 overflow-hidden bg-gray-50 dark:bg-slate-900">
        {{-- Animated Background Blobs --}}
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-20 -left-20 w-96 h-96 bg-purple-300 dark:bg-white rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 -right-20 w-96 h-96 bg-orange-300 dark:bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="relative container mx-auto px-6 py-20">
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

                            <div class="grid md:grid-cols-2 gap-8">
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
                        <a href="{{ whatsapp_link() }}" target="_blank"
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
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        :style="`right: ${rightPosition}`"
        class="fixed bottom-8 z-[60] hidden lg:block">
        <a href="#contact"
            class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-full shadow-lg hover:shadow-xl transition-all">
            <i class="fa-solid fa-envelope"></i>
            <span>Teklif Al</span>
        </a>
    </div>

    {{-- Product Rating Alpine Component --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productRating', (config) => ({
                modelClass: config.modelClass,
                modelId: config.modelId,
                currentRating: config.currentRating,
                averageRating: config.averageRating,
                ratingsCount: config.ratingsCount,
                isAuthenticated: config.isAuthenticated,
                hoverRating: 0,
                showMessage: false,
                message: '',
                messageType: 'success',

                async rateItem(value) {
                    if (!this.isAuthenticated) {
                        this.showToast('Puan vermek için giriş yapmalısınız', 'warning');
                        return;
                    }
                    try {
                        const response = await fetch('/api/reviews/rating', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                model_class: this.modelClass,
                                model_id: this.modelId,
                                rating_value: value
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.currentRating = value;
                            this.averageRating = parseFloat(data.data.average_rating);
                            this.ratingsCount = parseInt(data.data.ratings_count);
                            this.showToast('Puanınız kaydedildi! ⭐', 'success');
                        } else {
                            this.showToast(data.message || 'Bir hata oluştu', 'error');
                        }
                    } catch (error) {
                        console.error('Rating error:', error);
                        this.showToast('Bir hata oluştu', 'error');
                    }
                },

                showToast(msg, type = 'success') {
                    this.message = msg;
                    this.messageType = type;
                    this.showMessage = true;
                    setTimeout(() => this.showMessage = false, 3000);
                },

                getStarClass(star) {
                    const rating = this.hoverRating > 0 ? this.hoverRating : (this.currentRating || this.averageRating);
                    return star <= rating ? 'fas fa-star text-yellow-400' : 'far fa-star text-gray-300 dark:text-gray-600';
                }
            }));
        });
    </script>

    {{-- AI Chat Context - Ürün Sayfası Bilgisi --}}
    <script>
        // Alpine yüklenene kadar bekle
        document.addEventListener('alpine:init', () => {
            // AI Chat store'a bu ürün bilgisini gönder
            if (typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
                Alpine.store('aiChat').updateContext({
                    product_id: {{ $item->id }},
                    category_id: {{ $item->category_id ?? 'null' }},
                    page_slug: '{{ $item->getTranslated('slug', app()->getLocale()) }}',
                });

                console.log('✅ AI Chat Context Updated:', {
                    product_id: {{ $item->id }},
                    product_title: @json($item->getTranslated('title', app()->getLocale())),
                    category_id: {{ $item->category_id ?? 'null' }},
                });
            }
        });

        // Eğer Alpine zaten yüklüyse direkt güncelle
        if (typeof Alpine !== 'undefined' && Alpine.store('aiChat')) {
            Alpine.store('aiChat').updateContext({
                product_id: {{ $item->id }},
                category_id: {{ $item->category_id ?? 'null' }},
                page_slug: '{{ $item->getTranslated('slug', app()->getLocale()) }}',
            });
        }
    </script>

    {{-- Smooth Scroll for TOC Links --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for TOC links
            document.querySelectorAll('a[href^="#"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>

    {{-- Topbar kaybolma ixtif-theme.js'de (header.classList.add('scrolled')) --}}

@endsection
