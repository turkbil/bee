@extends('themes.blank.layouts.app')

{{-- V2: E-COMMERCE STYLE - Split layout with sticky sidebar, tabs --}}

@section('module_content')
    <div class="min-h-screen bg-white dark:bg-gray-900" x-data="{ activeTab: 'overview' }">
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
            $faqEntries = collect($item->faq_data ?? [])->map(fn($faq) => is_array($faq) ? ['question' => $resolveLocalized($faq['question'] ?? null), 'answer' => $resolveLocalized($faq['answer'] ?? null), 'sort_order' => $faq['sort_order'] ?? 999] : null)->filter(fn($faq) => $faq && $faq['question'] && $faq['answer'])->sortBy('sort_order')->values();
            $technicalSpecs = is_array($item->technical_specs) ? $item->technical_specs : [];
            $brandingInfo = null;
            $featuresList = [];
            if (is_array($item->features)) {
                $resolvedFeatures = $resolveLocalized($item->features) ?? [];
                if (is_array($resolvedFeatures)) {
                    if (array_is_list($resolvedFeatures)) {
                        $featuresList = collect($resolvedFeatures)->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                    } else {
                        $featuresList = collect($resolvedFeatures['list'] ?? [])->map(fn($f) => is_array($f) && isset($f['text']) ? $f : ['icon' => 'check-circle', 'text' => $f])->filter(fn($f) => !empty($f['text']))->values()->all();
                        $brandingInfo = $resolvedFeatures['branding'] ?? null;
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
            $targetIndustries = [];
            if (is_array($item->target_industries)) {
                $resolvedIndustries = $resolveLocalized($item->target_industries);
                if (is_array($resolvedIndustries)) {
                    $targetIndustries = collect($resolvedIndustries)->map(fn($ind) => is_array($ind) && isset($ind['text']) ? $ind : ['icon' => 'briefcase', 'text' => $ind])->filter(fn($ind) => !empty($ind['text']))->values()->all();
                }
            }
            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }
            $accessories = [];
            if (is_array($item->accessories)) {
                $accessories = collect($item->accessories)->filter(fn($acc) => is_array($acc) && !empty($acc['name']))->values()->all();
            }
            $certifications = [];
            if (is_array($item->certifications)) {
                $certifications = collect($item->certifications)->filter(fn($cert) => is_array($cert) && !empty($cert['name']))->values()->all();
            }
            $siblingVariants = $siblingVariants ?? collect();
        @endphp

        <div class="container mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-3 gap-8">
                {{-- LEFT: Product Images & Gallery --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Badges --}}
                    <div class="flex gap-2">
                        <span class="px-4 py-2 bg-green-500 text-white text-sm font-bold rounded">STOKTA</span>
                        <span class="px-4 py-2 bg-blue-500 text-white text-sm font-bold rounded">YENİ MODEL</span>
                    </div>

                    {{-- Main Image --}}
                    @if($featuredImage)
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden">
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $title }}"
                                 class="w-full">
                        </div>
                    @endif

                    {{-- Gallery Thumbnails --}}
                    @if($galleryImages->count() > 0)
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($galleryImages->take(4) as $image)
                                <a href="{{ $image->getUrl() }}" class="glightbox block bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden hover:ring-2 ring-blue-500" data-gallery="product">
                                    <img src="{{ $image->hasGeneratedConversion('thumb') ? $image->getUrl('thumb') : $image->getUrl() }}"
                                         alt=""
                                         class="w-full h-24 object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Tabs Navigation --}}
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <div class="flex gap-4 overflow-x-auto">
                            <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">Genel Bakış</button>
                            <button @click="activeTab = 'features'" :class="activeTab === 'features' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">Özellikler</button>
                            <button @click="activeTab = 'specs'" :class="activeTab === 'specs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">Teknik Özellikler</button>
                            <button @click="activeTab = 'usecases'" :class="activeTab === 'usecases' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">Kullanım</button>
                            <button @click="activeTab = 'accessories'" :class="activeTab === 'accessories' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">Aksesuarlar</button>
                            <button @click="activeTab = 'faq'" :class="activeTab === 'faq' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'" class="px-4 py-3 border-b-2 font-medium whitespace-nowrap">SSS</button>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="py-6">
                        {{-- Overview Tab --}}
                        <div x-show="activeTab === 'overview'" x-transition>
                            @if($longDescription)
                                <div class="prose max-w-none dark:prose-invert mb-8">
                                    @parsewidgets($longDescription)
                                </div>
                            @endif

                            @if($highlightedFeatures->isNotEmpty())
                                <h3 class="text-2xl font-bold mb-6">Öne Çıkan Özellikler</h3>
                                <div class="grid md:grid-cols-2 gap-6 mb-8">
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
                            @endif

                            @if(!empty($competitiveAdvantages))
                                <h3 class="text-2xl font-bold mb-6">Rekabet Avantajları</h3>
                                <div class="grid gap-4">
                                    @foreach($competitiveAdvantages as $adv)
                                        @php
                                            $advIcon = is_array($adv) ? ($adv['icon'] ?? 'star') : 'star';
                                            $advText = is_array($adv) ? ($adv['text'] ?? $adv) : $adv;
                                        @endphp
                                        <div class="flex gap-3 items-start p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                            <i class="fa-solid fa-{{ $advIcon }} text-amber-600 text-lg mt-1"></i>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $advText }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Features Tab --}}
                        <div x-show="activeTab === 'features'" x-transition style="display: none;">
                            @if(!empty($featuresList))
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
                            @endif
                        </div>

                        {{-- Specs Tab --}}
                        <div x-show="activeTab === 'specs'" x-transition style="display: none;">
                            @if(!empty($technicalSpecs))
                                @php
                                    $isFlat = !empty($technicalSpecs) && !collect($technicalSpecs)->contains(fn($v) => is_array($v));
                                @endphp

                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    @if($isFlat)
                                        @foreach($technicalSpecs as $key => $value)
                                            <div class="flex border-b border-gray-200 dark:border-gray-700 last:border-0">
                                                <div class="w-1/3 p-4 bg-gray-50 dark:bg-gray-800 font-medium">{{ $key }}</div>
                                                <div class="w-2/3 p-4">{{ $formatSpecValue($value) }}</div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($technicalSpecs as $sectionKey => $sectionValues)
                                            @if(is_array($sectionValues) && !empty($sectionValues))
                                                @php
                                                    $sectionTitle = $sectionValues['_title'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $sectionKey));
                                                    unset($sectionValues['_title'], $sectionValues['_icon']);
                                                @endphp
                                                <div class="p-4 bg-blue-600 text-white font-bold">{{ $sectionTitle }}</div>
                                                @foreach($sectionValues as $key => $value)
                                                    @php
                                                        $propertyLabel = is_array($value) && isset($value['_label']) ? $value['_label'] : $key;
                                                        $propertyValue = is_array($value) && isset($value['_value']) ? $value['_value'] : $value;
                                                    @endphp
                                                    <div class="flex border-b border-gray-200 dark:border-gray-700 last:border-0">
                                                        <div class="w-1/3 p-4 bg-gray-50 dark:bg-gray-800 font-medium">{{ $propertyLabel }}</div>
                                                        <div class="w-2/3 p-4">{{ $formatSpecValue($propertyValue) }}</div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Use Cases Tab --}}
                        <div x-show="activeTab === 'usecases'" x-transition style="display: none;">
                            @if(!empty($useCases))
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
                            @endif

                            @if(!empty($targetIndustries))
                                <h3 class="text-xl font-bold mt-8 mb-4">Hedef Sektörler</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($targetIndustries as $industry)
                                        @php
                                            $industryIcon = is_array($industry) ? ($industry['icon'] ?? 'briefcase') : 'briefcase';
                                            $industryText = is_array($industry) ? ($industry['text'] ?? $industry) : $industry;
                                        @endphp
                                        <div class="text-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <i class="fa-solid fa-{{ $industryIcon }} text-purple-600 text-2xl mb-2"></i>
                                            <div class="text-sm text-gray-700 dark:text-gray-300">{{ $industryText }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Accessories Tab --}}
                        <div x-show="activeTab === 'accessories'" x-transition style="display: none;">
                            @if(!empty($accessories))
                                <div class="grid md:grid-cols-2 gap-6">
                                    @foreach($accessories as $accessory)
                                        @php
                                            $isStandard = $accessory['is_standard'] ?? false;
                                            $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                                        @endphp
                                        <div class="border-2 {{ $isStandard ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }} rounded-lg p-6">
                                            <div class="flex items-start justify-between mb-4">
                                                <i class="fa-solid fa-{{ $accIcon }} text-2xl text-orange-600"></i>
                                                @if($isStandard)
                                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded">STANDART</span>
                                                @else
                                                    <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded">OPSİYONEL</span>
                                                @endif
                                            </div>
                                            <h4 class="font-bold text-lg mb-2">{{ $accessory['name'] }}</h4>
                                            @if(!empty($accessory['description']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $accessory['description'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($certifications))
                                <h3 class="text-xl font-bold mt-8 mb-4">Sertifikalar</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($certifications as $cert)
                                        @php $certIcon = $cert['icon'] ?? 'certificate'; @endphp
                                        <div class="text-center p-6 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <i class="fa-solid fa-{{ $certIcon }} text-4xl text-blue-600 mb-3"></i>
                                            <h5 class="font-bold text-sm">{{ $cert['name'] }}</h5>
                                            @if(!empty($cert['year']))
                                                <div class="text-xs text-gray-500 mt-1">{{ $cert['year'] }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- FAQ Tab --}}
                        <div x-show="activeTab === 'faq'" x-transition x-data="{ openFaq: null }" style="display: none;">
                            @if($faqEntries->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($faqEntries as $index => $faq)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <button @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})" class="w-full px-6 py-4 flex items-center justify-between text-left">
                                                <span class="font-bold">{{ $faq['question'] }}</span>
                                                <i class="fa-solid fa-chevron-down transition-transform" :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                            </button>
                                            <div x-show="openFaq === {{ $index }}" x-transition class="px-6 pb-4 text-gray-600 dark:text-gray-400" style="display: none;">
                                                {!! nl2br(e($faq['answer'])) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Sticky Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-4 space-y-6">
                        {{-- Product Info Card --}}
                        <div class="bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-lg">
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

                            {{-- Price Placeholder --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Fiyat</div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">Teklif Alın</div>
                            </div>

                            {{-- CTA Buttons --}}
                            <div class="space-y-3">
                                <a href="#contact" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-bold py-4 rounded-lg transition-colors">
                                    <i class="fa-solid fa-envelope mr-2"></i>Teklif Al
                                </a>
                                <a href="tel:02167553555" class="block w-full border-2 border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 text-center font-bold py-4 rounded-lg transition-colors">
                                    <i class="fa-solid fa-phone mr-2"></i>0216 755 3 555
                                </a>
                            </div>
                        </div>

                        {{-- Trust Badges --}}
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                            <h3 class="font-bold mb-4">Güvenli Alışveriş</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-shield-halved text-green-500 text-xl"></i>
                                    <span class="text-sm">Garantili Ürün</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-truck-fast text-blue-500 text-xl"></i>
                                    <span class="text-sm">Hızlı Teslimat</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-headset text-purple-500 text-xl"></i>
                                    <span class="text-sm">7/24 Destek</span>
                                </div>
                            </div>
                        </div>

                        {{-- Warranty Info --}}
                        @if($warrantyInfo)
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <i class="fa-solid fa-shield-heart text-emerald-600 text-2xl"></i>
                                    <h3 class="font-bold">Garanti</h3>
                                </div>
                                @if(is_array($warrantyInfo))
                                    @if($warrantyInfo['coverage'] ?? false)
                                        <p class="text-sm mb-2">{{ $warrantyInfo['coverage'] }}</p>
                                    @endif
                                    @if($warrantyInfo['duration_months'] ?? false)
                                        <div class="font-bold text-emerald-600">{{ $warrantyInfo['duration_months'] }} ay</div>
                                    @endif
                                @else
                                    <p class="text-sm">{{ $warrantyInfo }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Variants Section --}}
            @if($siblingVariants->count() > 0)
                <div class="mt-16">
                    <h2 class="text-3xl font-bold mb-8">Ürün Varyantları</h2>
                    <div class="grid md:grid-cols-4 gap-6">
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
                                <h3 class="font-bold text-sm group-hover:text-blue-600">{{ $variantTitle }}</h3>
                                @if($variant->variant_type)
                                    <span class="inline-block mt-2 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-400 text-xs rounded">{{ ucfirst(str_replace('-', ' ', $variant->variant_type)) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Contact Form --}}
            <div id="contact" class="mt-16 bg-gray-50 dark:bg-gray-800 rounded-lg p-8">
                <h2 class="text-3xl font-bold text-center mb-8">Teklif İsteyin</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="max-w-2xl mx-auto space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block font-bold mb-2">Ad Soyad *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-bold mb-2">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Telefon *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg">
                    </div>

                    <div>
                        <label class="block font-bold mb-2">Mesajınız</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition-colors">
                        <i class="fa-solid fa-paper-plane mr-2"></i>Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
