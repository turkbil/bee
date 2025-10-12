@extends('themes.blank.layouts.app')

{{-- V6 VARIANT: HYBRID - V4/V1 Content + V2 Sticky Sidebar --}}

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
            $siblingVariants = $siblingVariants ?? collect();
            $parentProduct = $parentProduct ?? null;
            $variantType = $item->variant_type;
        @endphp

        {{-- 🎯 HERO SECTION --}}
        <section id="hero-section" class="bg-blue-600 dark:bg-blue-700 text-white">
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- Variant Badge --}}
            @if($variantType)
                <div class="mb-6">
                    <span class="inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-4 py-2 rounded-full font-bold">
                        <i class="fa-solid fa-layer-group mr-2"></i>{{ ucfirst($variantType) }} Varyantı
                    </span>
                </div>
            @endif

            {{-- Parent Product Link --}}
            @if($parentProduct)
                @php
                    $parentTitle = $parentProduct->getTranslated('title', $currentLocale);
                    $parentUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($parentProduct, $currentLocale);
                @endphp
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-arrow-left text-blue-600"></i>
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Ana Ürün</div>
                            <a href="{{ $parentUrl }}" class="text-lg font-bold text-blue-600 hover:text-blue-700">{{ $parentTitle }}</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid lg:grid-cols-3 gap-8 items-start">
                {{-- LEFT: Main Content (2/3) --}}
                <div class="lg:col-span-2 space-y-12 min-h-screen">

                    {{-- Gallery --}}
                    @if($galleryImages->count() > 0)
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
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
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <div class="prose max-w-none dark:prose-invert">
                                @parsewidgets($longDescription)
                            </div>
                        </section>
                    @endif

                    {{-- Highlighted Features --}}
                    @if($highlightedFeatures->isNotEmpty())
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <h2 class="text-3xl font-bold mb-6">Öne Çıkan Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                @foreach($highlightedFeatures as $feature)
                                    <div class="flex gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
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
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <h2 class="text-3xl font-bold mb-6">Özellikler</h2>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($featuresList as $feature)
                                    @php
                                        $featureIcon = is_array($feature) ? ($feature['icon'] ?? 'check-circle') : 'check-circle';
                                        $featureText = is_array($feature) ? ($feature['text'] ?? $feature) : $feature;
                                    @endphp
                                    <div class="flex gap-3 items-start p-3 border border-gray-200 dark:border-gray-700 rounded">
                                        <i class="fa-solid fa-{{ $featureIcon }} text-green-500 mt-1"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Use Cases --}}
                    @if(!empty($useCases))
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <h2 class="text-3xl font-bold mb-6">Kullanım Alanları</h2>
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($useCases as $case)
                                    @php
                                        $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                        $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                                    @endphp
                                    <div class="flex gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                        <i class="fa-solid fa-{{ $caseIcon }} text-blue-600 text-lg mt-1"></i>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $caseText }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Sibling Variants --}}
                    @if($siblingVariants->count() > 0)
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <h2 class="text-3xl font-bold mb-6">Diğer Varyantlar</h2>
                            <div class="grid md:grid-cols-3 gap-4">
                                @foreach($siblingVariants as $variant)
                                    @php
                                        $variantTitle = $variant->getTranslated('title', $currentLocale) ?? $variant->sku;
                                        $variantUrl = \Modules\Shop\App\Http\Controllers\Front\ShopController::resolveProductUrl($variant, $currentLocale);
                                        $variantImage = $variant->getFirstMedia('featured_image');
                                    @endphp
                                    <a href="{{ $variantUrl }}" class="group border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 rounded-lg p-4 transition-all">
                                        @if($variantImage)
                                            <img src="{{ $variantImage->hasGeneratedConversion('thumb') ? $variantImage->getUrl('thumb') : $variantImage->getUrl() }}"
                                                 alt="{{ $variantTitle }}"
                                                 class="w-full h-32 object-cover rounded mb-3">
                                        @endif
                                        <h3 class="font-bold text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $variantTitle }}</h3>
                                        @if($variant->variant_type)
                                            <span class="inline-block mt-2 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-1 rounded">
                                                {{ ucfirst($variant->variant_type) }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Technical Specs --}}
                    @if(!empty($technicalSpecs))
                        <section class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                            <h2 class="text-3xl font-bold mb-6">Teknik Özellikler</h2>
                            <div class="space-y-3">
                                @foreach($technicalSpecs as $category => $specs)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                                        <h3 class="font-bold text-lg mb-3 text-gray-900 dark:text-white">{{ ucfirst($category) }}</h3>
                                        <div class="grid md:grid-cols-2 gap-3">
                                            @foreach($specs as $key => $value)
                                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-900/50 rounded">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                                                    <span class="font-bold text-gray-900 dark:text-white">{{ $formatSpecValue($value) }}</span>
                                                </div>
                                            @endforeach
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

                            {{-- Variant Type Badge --}}
                            @if($variantType)
                                <div class="mb-4">
                                    <span class="inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-3 py-1 rounded-full text-sm font-bold">
                                        🔖 {{ ucfirst($variantType) }} Varyant
                                    </span>
                                </div>
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

            {{-- Contact Form --}}
            <div id="contact" class="mt-16 bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg">
                <h2 class="text-3xl font-bold text-center mb-8">Teklif İsteyin</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="max-w-2xl mx-auto space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-bold mb-2">Ad Soyad *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900">
                        </div>
                        <div>
                            <label class="block font-bold mb-2">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Telefon *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900">
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Mesajınız</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition-colors">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Gönder
                    </button>
                </form>
            </div>
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

        const GAP = 64; // Contact form'a bu kadar yaklaşınca dur (sol kolon ile aynı mesafe)
        const HEADER_MARGIN = 32; // Header'dan bu kadar boşluk

        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            // Güncel ölçümleri al
            const heroHeight = heroSection.offsetHeight;
            const headerHeight = header ? header.offsetHeight : 0;
            const sidebarHeight = sidebar.offsetHeight;
            const sidebarWidth = sidebar.offsetWidth;

            // Contact form ve parent'ın sayfadaki pozisyonları
            const contactFormTop = contactForm.getBoundingClientRect().top + scrollTop;
            const parentTop = sidebarParent.getBoundingClientRect().top + scrollTop;

            // Hero'yu geçmediyse static pozisyon
            if (scrollTop <= heroHeight) {
                sidebar.style.position = 'static';
                sidebar.style.top = '';
                sidebar.style.width = '';
                sidebar.style.zIndex = '';
                return;
            }

            // Sticky pozisyonda sidebar'ın alt kenarı (ekranda)
            const stickyOffset = headerHeight + HEADER_MARGIN;
            const stickyBottomPosition = scrollTop + stickyOffset + sidebarHeight;

            // Contact form'a GAP kadar yaklaştıysa DUR (absolute)
            if (stickyBottomPosition + GAP >= contactFormTop) {
                // Parent'a göre relative pozisyon hesapla
                const absoluteTop = contactFormTop - parentTop - sidebarHeight - GAP;
                sidebar.style.position = 'absolute';
                sidebar.style.top = absoluteTop + 'px';
                sidebar.style.width = sidebarWidth + 'px';
                sidebar.style.zIndex = '10'; // Header'ın altında kalması için çok düşük z-index
                return;
            }

            // Normal sticky davranış (header + boşluk)
            sidebar.style.position = 'fixed';
            sidebar.style.top = stickyOffset + 'px';
            sidebar.style.width = sidebarWidth + 'px';
            sidebar.style.zIndex = '10'; // Header'ın altında kalması için çok düşük z-index
        }

        // Scroll ve resize'da çalıştır
        window.addEventListener('scroll', handleScroll, { passive: true });
        window.addEventListener('resize', handleScroll);

        // İlk yükleme
        handleScroll();
    });
    </script>
@endsection
