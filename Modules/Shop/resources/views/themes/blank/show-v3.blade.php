@extends('themes.blank.layouts.app')

{{-- V3: CORPORATE/PROFESSIONAL - Navy/gray colors, accordion sections, formal tables --}}

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

        {{-- HERO: Corporate Header --}}
        <section class="bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 text-white py-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-block px-4 py-2 bg-blue-600/30 border border-blue-400/50 rounded-lg mb-6 text-sm font-semibold uppercase tracking-wider">
                            Endüstriyel Çözümler
                        </div>
                        <h1 class="text-5xl font-bold mb-6 leading-tight">{{ $title }}</h1>
                        @if($shortDescription)
                            <p class="text-xl text-blue-100 mb-8 leading-relaxed">{{ $shortDescription }}</p>
                        @endif
                        <div class="flex gap-4">
                            <a href="#iletisim" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition">
                                Detaylı Bilgi Al
                            </a>
                            <a href="tel:02167553555" class="px-8 py-4 border-2 border-white/30 hover:border-white/50 rounded-lg font-semibold transition">
                                0216 755 3 555
                            </a>
                        </div>
                    </div>

                    @if($featuredImage)
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-500/20 rounded-2xl blur-3xl"></div>
                            <img src="{{ $featuredImage->hasGeneratedConversion('large') ? $featuredImage->getUrl('large') : $featuredImage->getUrl() }}"
                                 alt="{{ $title }}"
                                 class="relative rounded-2xl shadow-2xl">
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- PRIMARY SPECS: Table Format --}}
        @if(!empty($primarySpecs))
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center border-b-4 border-blue-600 inline-block pb-2">Teknik Parametreler</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-700 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left font-semibold">Parametre</th>
                                    <th class="px-6 py-4 text-left font-semibold">Değer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($primarySpecs as $spec)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">{{ $spec['label'] }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-white font-semibold">{{ $spec['value'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endif

        {{-- ACCORDION SECTIONS --}}
        <section class="py-16 bg-gray-100 dark:bg-gray-900" x-data="{ activeSection: 'genel' }">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">

                {{-- Section Tabs --}}
                <div class="flex flex-wrap gap-2 mb-8 border-b-2 border-gray-300 dark:border-gray-700 pb-4">
                    <button @click="activeSection = 'genel'" :class="activeSection === 'genel' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Genel Bilgiler</button>
                    @if($highlightedFeatures->isNotEmpty())
                        <button @click="activeSection = 'ozellikler'" :class="activeSection === 'ozellikler' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Öne Çıkan Özellikler</button>
                    @endif
                    @if(!empty($technicalSpecs))
                        <button @click="activeSection = 'teknik'" :class="activeSection === 'teknik' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Teknik Detaylar</button>
                    @endif
                    @if(!empty($useCases))
                        <button @click="activeSection = 'kullanim'" :class="activeSection === 'kullanim' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Kullanım Alanları</button>
                    @endif
                    @if(!empty($accessories))
                        <button @click="activeSection = 'aksesuar'" :class="activeSection === 'aksesuar' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Aksesuarlar</button>
                    @endif
                    @if(!empty($certifications))
                        <button @click="activeSection = 'sertifika'" :class="activeSection === 'sertifika' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">Sertifikalar</button>
                    @endif
                    @if($faqEntries->isNotEmpty())
                        <button @click="activeSection = 'sss'" :class="activeSection === 'sss' ? 'bg-blue-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300'" class="px-6 py-3 rounded-t-lg font-semibold transition">SSS</button>
                    @endif
                </div>

                {{-- Genel Bilgiler --}}
                <div x-show="activeSection === 'genel'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                    @if($longDescription)
                        <div class="prose prose-lg max-w-none dark:prose-invert mb-8">
                            @parsewidgets($longDescription)
                        </div>
                    @endif

                    @if($galleryImages->count() > 0)
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Ürün Görselleri</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($galleryImages as $image)
                                <a href="{{ $image->getUrl() }}" class="glightbox block rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 transition" data-gallery="gallery">
                                    <img src="{{ $image->hasGeneratedConversion('medium') ? $image->getUrl('medium') : $image->getUrl() }}"
                                         alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                         class="w-full h-48 object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($featuresList))
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 mt-8">Avantajlar</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($featuresList as $feature)
                                @php
                                    $featureIcon = is_array($feature) ? ($feature['icon'] ?? 'check-circle') : 'check-circle';
                                    $featureText = is_array($feature) ? ($feature['text'] ?? $feature) : $feature;
                                @endphp
                                <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                    <i class="fa-solid fa-{{ $featureIcon }} text-green-600 text-xl mt-1"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $featureText }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($competitiveAdvantages))
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 mt-8">Rekabet Avantajları</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($competitiveAdvantages as $advantage)
                                @php
                                    $advIcon = is_array($advantage) ? ($advantage['icon'] ?? 'star') : 'star';
                                    $advText = is_array($advantage) ? ($advantage['text'] ?? $advantage) : $advantage;
                                @endphp
                                <div class="flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                    <i class="fa-solid fa-{{ $advIcon }} text-amber-600 text-xl mt-1"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $advText }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($targetIndustries))
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 mt-8">Kullanım Sektörleri</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($targetIndustries as $industry)
                                @php
                                    $industryIcon = is_array($industry) ? ($industry['icon'] ?? 'briefcase') : 'briefcase';
                                    $industryText = is_array($industry) ? ($industry['text'] ?? $industry) : $industry;
                                @endphp
                                <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <i class="fa-solid fa-{{ $industryIcon }} text-purple-600 text-3xl mb-2"></i>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $industryText }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($warrantyInfo)
                        <div class="mt-8 p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border-l-4 border-emerald-600">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-shield-heart text-emerald-600"></i>
                                Garanti Bilgileri
                            </h3>
                            @if(is_array($warrantyInfo))
                                @if($warrantyInfo['coverage'] ?? false)
                                    <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $warrantyInfo['coverage'] }}</p>
                                @endif
                                @if($warrantyInfo['duration_months'] ?? false)
                                    <p class="text-gray-700 dark:text-gray-300 font-semibold">Garanti Süresi: {{ $warrantyInfo['duration_months'] }} ay</p>
                                @endif
                            @else
                                <p class="text-gray-700 dark:text-gray-300">{{ $warrantyInfo }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Highlighted Features --}}
                @if($highlightedFeatures->isNotEmpty())
                    <div x-show="activeSection === 'ozellikler'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                        <div class="grid md:grid-cols-3 gap-8">
                            @foreach($highlightedFeatures as $feature)
                                <div class="text-center p-6 bg-slate-50 dark:bg-slate-900/50 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-{{ $feature['icon'] }} text-white text-3xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $feature['title'] }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Technical Specs --}}
                @if(!empty($technicalSpecs))
                    <div x-show="activeSection === 'teknik'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                        @php
                            $isFlat = !empty($technicalSpecs) && !collect($technicalSpecs)->contains(fn($v) => is_array($v));
                        @endphp

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-slate-700 text-white">
                                    <tr>
                                        <th class="px-6 py-4 text-left font-semibold w-1/2">Özellik</th>
                                        <th class="px-6 py-4 text-left font-semibold w-1/2">Değer</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @if($isFlat)
                                        @foreach($technicalSpecs as $key => $value)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-3 text-gray-700 dark:text-gray-300">{{ $key }}</td>
                                                <td class="px-6 py-3 text-gray-900 dark:text-white font-semibold">{{ $formatSpecValue($value) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($technicalSpecs as $sectionKey => $sectionValues)
                                            @if(is_array($sectionValues) && !empty($sectionValues))
                                                @php
                                                    $sectionTitle = $sectionValues['_title'] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $sectionKey));
                                                    unset($sectionValues['_title'], $sectionValues['_icon']);
                                                @endphp
                                                <tr class="bg-slate-100 dark:bg-slate-800">
                                                    <td colspan="2" class="px-6 py-4 font-bold text-gray-900 dark:text-white text-lg">{{ $sectionTitle }}</td>
                                                </tr>
                                                @foreach($sectionValues as $key => $value)
                                                    @php
                                                        $propertyLabel = is_array($value) && isset($value['_label']) ? $value['_label'] : $key;
                                                        $propertyValue = is_array($value) && isset($value['_value']) ? $value['_value'] : $value;
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                        <td class="px-6 py-3 text-gray-700 dark:text-gray-300 pl-12">{{ $propertyLabel }}</td>
                                                        <td class="px-6 py-3 text-gray-900 dark:text-white font-semibold">{{ $formatSpecValue($propertyValue) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Use Cases --}}
                @if(!empty($useCases))
                    <div x-show="activeSection === 'kullanim'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach($useCases as $case)
                                @php
                                    $caseIcon = is_array($case) ? ($case['icon'] ?? 'check') : 'check';
                                    $caseText = is_array($case) ? ($case['text'] ?? $case) : $case;
                                @endphp
                                <div class="flex items-start gap-4 p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-600">
                                    <i class="fa-solid fa-{{ $caseIcon }} text-blue-600 text-2xl mt-1"></i>
                                    <p class="text-gray-700 dark:text-gray-300 text-lg">{{ $caseText }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Accessories --}}
                @if(!empty($accessories))
                    <div x-show="activeSection === 'aksesuar'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                        <div class="grid md:grid-cols-3 gap-6">
                            @foreach($accessories as $accessory)
                                @php
                                    $isStandard = $accessory['is_standard'] ?? false;
                                    $accIcon = $accessory['icon'] ?? 'puzzle-piece';
                                @endphp
                                <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 {{ $isStandard ? 'border-green-500' : 'border-gray-200 dark:border-gray-700' }}">
                                    @if($isStandard)
                                        <span class="inline-block px-3 py-1 bg-green-600 text-white text-xs font-bold rounded mb-3 uppercase">Standart</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded mb-3 uppercase">Opsiyonel</span>
                                    @endif
                                    <div class="w-14 h-14 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-{{ $accIcon }} text-orange-600 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ $accessory['name'] }}</h3>
                                    @if(!empty($accessory['description']))
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $accessory['description'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Certifications --}}
                @if(!empty($certifications))
                    <div x-show="activeSection === 'sertifika'" class="bg-white dark:bg-gray-800 rounded-lg p-8">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            @foreach($certifications as $cert)
                                @php
                                    $certIcon = $cert['icon'] ?? 'certificate';
                                @endphp
                                <div class="text-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-2 border-blue-200 dark:border-blue-700">
                                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fa-solid fa-{{ $certIcon }} text-white text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $cert['name'] }}</h3>
                                    @if(!empty($cert['year']))
                                        <div class="text-sm text-gray-500 dark:text-gray-400 font-semibold">{{ $cert['year'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- FAQ --}}
                @if($faqEntries->isNotEmpty())
                    <div x-show="activeSection === 'sss'" class="bg-white dark:bg-gray-800 rounded-lg p-8" x-data="{ openFaq: null }">
                        <div class="space-y-4">
                            @foreach($faqEntries as $index => $faq)
                                <div class="border-2 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <button @click="openFaq = (openFaq === {{ $index }} ? null : {{ $index }})"
                                            class="w-full px-6 py-4 flex items-center justify-between text-left bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-900 transition">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                        <i class="fa-solid fa-chevron-down text-blue-600 text-xl transition-transform" :class="openFaq === {{ $index }} ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="openFaq === {{ $index }}" x-transition class="px-6 py-4 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400" style="display: none;">
                                        {!! nl2br(e($faq['answer'])) !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </section>

        {{-- VARIANTS --}}
        @if($siblingVariants->count() > 0)
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8 text-center border-b-4 border-blue-600 inline-block pb-2">Diğer Modeller</h2>
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
                                         class="w-full h-48 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600">{{ $variantTitle }}</h3>
                                    @if($variant->variant_type)
                                        <span class="inline-block mt-2 px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs rounded uppercase">{{ ucfirst(str_replace('-', ' ', $variant->variant_type)) }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- CONTACT FORM --}}
        <section id="iletisim" class="py-16 bg-gradient-to-r from-slate-900 via-blue-900 to-slate-900 text-white">
            <div class="max-w-4xl mx-auto px-6 lg:px-8">
                <h2 class="text-4xl font-bold mb-12 text-center">Detaylı Bilgi ve Teklif Talebi</h2>
                <form action="{{ route('shop.quote.submit') }}" method="POST" class="space-y-6 bg-white/10 backdrop-blur-lg rounded-2xl p-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                    <input type="hidden" name="product_title" value="{{ $title }}">

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Ad Soyad *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded-lg text-white placeholder-white/70 focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Firma</label>
                            <input type="text" name="company" class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded-lg text-white placeholder-white/70 focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2">E-posta *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded-lg text-white placeholder-white/70 focus:ring-2 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Telefon *</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded-lg text-white placeholder-white/70 focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-2">Mesajınız</label>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 bg-white/20 border-2 border-white/30 rounded-lg text-white placeholder-white/70 focus:ring-2 focus:ring-blue-400"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition text-lg">
                        Teklif Gönder
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
