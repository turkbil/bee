@extends('themes.blank.layouts.app')

@section('module_content')
    <div class="min-h-screen">
        @php
            $currentLocale = app()->getLocale();
            $title = $item->getTranslated('title', $currentLocale);
            $longDescription = $item->getTranslated('long_description', $currentLocale);

            $moduleSlugService = app(\App\Services\ModuleSlugService::class);
            $indexSlug = $moduleSlugService->getMultiLangSlug('Shop', 'index', $currentLocale);
            $defaultLocale = get_tenant_default_locale();
            $localePrefix = $currentLocale !== $defaultLocale ? '/' . $currentLocale : '';
            $shopIndexUrl = $localePrefix . '/' . $indexSlug;

            // Medya verilerini çek
            $featuredImage = $item->getFirstMedia('featured_image');
            $galleryImages = $item->getMedia('gallery');
            $shortDescription = $item->getTranslated('short_description', $currentLocale);

            $resolveLocalized = static function (?array $data) use ($currentLocale, $defaultLocale) {
                if (!is_array($data)) {
                    return null;
                }

                if (array_key_exists($currentLocale, $data)) {
                    return $data[$currentLocale];
                }

                if ($defaultLocale && array_key_exists($defaultLocale, $data)) {
                    return $data[$defaultLocale];
                }

                if (array_key_exists('en', $data)) {
                    return $data['en'];
                }

                return reset($data);
            };

            $uiText = static function (string $key) use ($currentLocale, $defaultLocale) {
                $phrases = [
                    'featured_highlights' => ['tr' => 'Öne Çıkan Özellikler', 'en' => 'Key Highlights'],
                    'featured_highlights_subtitle' => [
                        'tr' => 'Performans ve verimliliği öne çıkaran kilit noktalar',
                        'en' => 'Performance-driven highlights at a glance',
                    ],
                    'key_features' => ['tr' => 'Temel Avantajlar', 'en' => 'Key Advantages'],
                    'technical_specs' => ['tr' => 'Teknik Özellikler', 'en' => 'Technical Specifications'],
                    'technical_specs_subtitle' => [
                        'tr' => 'Operasyon planlaması için ölçümler ve performans verileri',
                        'en' => 'Measurements and performance data for operations planning',
                    ],
                    'warranty_info' => ['tr' => 'Garanti Bilgisi', 'en' => 'Warranty Information'],
                    'warranty_months' => ['tr' => 'ay garanti', 'en' => 'month warranty'],
                    'use_cases' => ['tr' => 'Kullanım Senaryoları', 'en' => 'Use Cases'],
                    'competitive_advantages' => ['tr' => 'Rekabet Avantajları', 'en' => 'Competitive Advantages'],
                    'target_industries' => ['tr' => 'Hedef Sektörler', 'en' => 'Target Industries'],
                    'faq_title' => ['tr' => 'Sık Sorulan Sorular', 'en' => 'Frequently Asked Questions'],
                    'faq_subtitle' => [
                        'tr' => 'Karar verirken müşterilerimizin en çok merak ettiği başlıklar',
                        'en' => 'Key questions customers ask before making a decision',
                    ],
                ];

                $default = $phrases[$key]['en'] ?? '';

                if (isset($phrases[$key][$currentLocale])) {
                    return $phrases[$key][$currentLocale];
                }

                if ($defaultLocale && isset($phrases[$key][$defaultLocale])) {
                    return $phrases[$key][$defaultLocale];
                }

                return $default;
            };

            $specSectionLabels = [
                'capacity' => 'Kapasite',
                'dimensions' => 'Boyutlar',
                'performance' => 'Performans',
                'electrical' => 'Elektrik Sistemi',
                'tyres' => 'Tekerlekler',
                'options' => 'Opsiyonlar'
            ];

            $specFieldLabels = [
                'load_capacity' => 'Taşıma Kapasitesi',
                'service_weight' => 'Servis Ağırlığı',
                'axle_load_laden' => 'Yüklü Dingil Yükü',
                'axle_load_unladen' => 'Boş Dingil Yükü',
                'load_center_distance' => 'Yük Merkezi Mesafesi',
                'overall_length' => 'Toplam Uzunluk',
                'length_to_face_of_forks' => 'Fork Uçlarına Kadar Uzunluk',
                'overall_width' => 'Toplam Genişlik',
                'fork_dimensions' => 'Fork Ölçüleri',
                'fork_spread' => 'Fork Açıklığı',
                'ground_clearance' => 'Yerden Yükseklik',
                'turning_radius' => 'Dönüş Yarıçapı',
                'aisle_width_1000x1200' => 'Koridor Genişliği 1000×1200',
                'aisle_width_800x1200' => 'Koridor Genişliği 800×1200',
                'lift_height' => 'Kaldırma Yüksekliği',
                'lowered_height' => 'Fork Alt Yüksekliği',
                'tiller_height' => 'Tiller Yüksekliği',
                'travel_speed' => 'Seyir Hızı',
                'lift_speed' => 'Kaldırma Hızı',
                'lowering_speed' => 'İndirme Hızı',
                'max_gradeability' => 'Maksimum Tırmanma Açısı',
                'turnover_output' => 'VDI 2198 Çıktı',
                'turnover_efficiency' => 'VDI 2198 Verimlilik',
                'drive_motor_rating' => 'Sürüş Motoru Gücü',
                'lift_motor_rating' => 'Kaldırma Motoru Gücü',
                'battery_system' => 'Batarya Sistemi',
                'battery_weight' => 'Batarya Ağırlığı',
                'charger_options' => 'Şarj Opsiyonları',
                'energy_consumption' => 'Enerji Tüketimi',
                'drive_control' => 'Sürüş Kontrolü',
                'steering_design' => 'Direksiyon Tasarımı',
                'noise_level' => 'Ses Seviyesi',
                'type' => 'Lastik Türü',
                'drive_wheel' => 'Sürüş Tekerleği',
                'load_wheel' => 'Yük Tekerleği',
                'caster_wheel' => 'Destek Tekerleği',
                'wheel_configuration' => 'Tekerlek Konfigürasyonu',
                'fork_lengths_mm' => 'Fork Uzunlukları (mm)',
                'fork_spreads_mm' => 'Fork Açıklıkları (mm)',
                'battery_expansion' => 'Batarya Konfigürasyonu',
                'stabilizing_wheels' => 'Stabilizasyon Tekerlekleri'
            ];

            $specSubKeyLabels = [
                'standard' => 'Standart',
                'wide' => 'Geniş',
                'max' => 'Maks',
                'min' => 'Min',
                'front' => 'Ön',
                'rear' => 'Arka',
                'optional' => 'Opsiyonel',
                'configuration' => 'Konfigürasyon'
            ];

            $resolveSectionLabel = static function (string $key) use ($specSectionLabels) {
                return $specSectionLabels[$key] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $key));
            };

            $resolveFieldLabel = static function (string $key) use ($specFieldLabels) {
                return $specFieldLabels[$key] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $key));
            };

            $resolveSubLabel = static function (string $key) use ($specSubKeyLabels) {
                return $specSubKeyLabels[$key] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $key));
            };

            $featuresList = [];
            if (is_array($item->features)) {
                $featuresList = $resolveLocalized($item->features) ?? [];
                if (!is_array($featuresList)) {
                    $featuresList = [];
                }
            }

            $highlightedFeatures = collect($item->highlighted_features ?? [])
                ->map(function ($feature) use ($resolveLocalized) {
                    if (!is_array($feature)) {
                        return null;
                    }

                    $title = $resolveLocalized($feature['title'] ?? null);
                    $description = $resolveLocalized($feature['description'] ?? null);

                    return [
                        'icon' => $feature['icon'] ?? null,
                        'title' => $title,
                        'description' => $description,
                    ];
                })
                ->filter(fn ($feature) => $feature && ($feature['title'] || $feature['description']));

            $useCases = [];
            if (is_array($item->use_cases)) {
                $resolvedUseCases = $resolveLocalized($item->use_cases);
                if (is_array($resolvedUseCases)) {
                    $useCases = array_values(array_filter($resolvedUseCases));
                }
            }

            $competitiveAdvantages = [];
            if (is_array($item->competitive_advantages)) {
                $resolvedAdvantages = $resolveLocalized($item->competitive_advantages);
                if (is_array($resolvedAdvantages)) {
                    $competitiveAdvantages = array_values(array_filter($resolvedAdvantages));
                }
            }

            $targetIndustries = [];
            if (is_array($item->target_industries)) {
                $resolvedIndustries = $resolveLocalized($item->target_industries);
                if (is_array($resolvedIndustries)) {
                    $targetIndustries = array_values(array_filter($resolvedIndustries));
                }
            }

            $warrantyInfo = null;
            if (is_array($item->warranty_info)) {
                $warrantyInfo = $resolveLocalized($item->warranty_info);
            }

            $faqEntries = collect($item->faq_data ?? [])
                ->map(function ($faq) use ($resolveLocalized) {
                    if (!is_array($faq)) {
                        return null;
                    }

                    $question = $resolveLocalized($faq['question'] ?? null);
                    $answer = $resolveLocalized($faq['answer'] ?? null);
                    $order = $faq['sort_order'] ?? null;

                    if (!$question || !$answer) {
                        return null;
                    }

                    return [
                        'question' => $question,
                        'answer' => $answer,
                        'sort_order' => $order,
                    ];
                })
                ->filter()
                ->sortBy(fn ($faq) => $faq['sort_order'] ?? 999)
                ->values();

            $formatSpecValue = null;
            $formatSpecValue = static function ($value) use (&$formatSpecValue, $resolveSubLabel) {
                if (is_bool($value)) {
                    return $value ? 'Var' : 'Yok';
                }

                if (is_array($value)) {
                    if (array_key_exists('value', $value)) {
                        $unit = $value['unit'] ?? '';
                        $note = $value['note'] ?? '';
                        $formatted = $value['value'] . ($unit ? ' ' . $unit : '');

                        return $note ? $formatted . ' (' . $note . ')' : $formatted;
                    }

                    if (array_key_exists('front', $value) || array_key_exists('rear', $value)) {
                        $unit = $value['unit'] ?? '';
                        $parts = [];

                        if (isset($value['front'])) {
                            $parts[] = $resolveSubLabel('front') . ': ' . $value['front'] . ($unit ? ' ' . $unit : '');
                        }

                        if (isset($value['rear'])) {
                            $parts[] = $resolveSubLabel('rear') . ': ' . $value['rear'] . ($unit ? ' ' . $unit : '');
                        }

                        return implode(' • ', $parts);
                    }

                    if (array_key_exists('standard', $value) || array_key_exists('wide', $value)) {
                        $unit = $value['unit'] ?? '';
                        $parts = [];

                        foreach ($value as $key => $item) {
                            if ($key === 'unit') {
                                continue;
                            }

                            $label = $resolveSubLabel((string) $key);
                            $formattedItem = $formatSpecValue($item);
                            $isNumericItem = !is_array($item) && is_numeric($item);
                            $parts[] = trim($label . ': ' . $formattedItem . ($unit && $isNumericItem ? ' ' . $unit : ''));
                        }

                        return implode(' • ', $parts);
                    }

                    if (array_is_list($value)) {
                        $resolved = array_map(static fn ($item) => $formatSpecValue($item), $value);

                        return implode(', ', array_filter($resolved));
                    }

                    $parts = [];
                    $unit = $value['unit'] ?? null;

                    foreach ($value as $key => $item) {
                        if ($key === 'unit') {
                            continue;
                        }

                        $label = $resolveSubLabel((string) $key);
                        $formattedItem = $formatSpecValue($item);
                        $isNumericItem = !is_array($item) && is_numeric($item);
                        $parts[] = trim($label . ': ' . $formattedItem . ($unit && $isNumericItem ? ' ' . $unit : ''));
                    }

                    $joined = implode(' • ', array_filter($parts));

                    return $unit && $joined ? $joined . ' ' . $unit : $joined;
                }

                return (string) $value;
            };

            $technicalSpecGroups = [];
            if (is_array($item->technical_specs)) {
                foreach ($item->technical_specs as $sectionKey => $sectionValues) {
                    if (!is_array($sectionValues)) {
                        continue;
                    }

                    $rows = [];

                    foreach ($sectionValues as $key => $value) {
                        $label = $resolveFieldLabel((string) $key);
                        $formattedValue = $formatSpecValue($value);

                        if ($formattedValue === '') {
                            continue;
                        }

                        $rows[] = [
                            'label' => $label,
                            'value' => $formattedValue,
                        ];
                    }

                    if (!empty($rows)) {
                        $technicalSpecGroups[] = [
                            'title' => $resolveSectionLabel((string) $sectionKey),
                            'rows' => $rows,
                        ];
                    }
                }
            }

            $tags = is_array($item->tags) ? $item->tags : [];
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            {{-- Sayfa Başlığı --}}
            <header class="mb-8 md:mb-12">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                    {{ $title }}
                </h1>
                <div class="h-1 w-20 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 lg:gap-10">
                {{-- Ana Görsel - Sol Taraf --}}
                @if($featuredImage)
                    <div class="lg:col-span-1 order-2 lg:order-1">
                        <figure class="sticky top-8">
                            <a href="{{ $featuredImage->getUrl() }}"
                               class="glightbox"
                               data-gallery="shop-gallery"
                               data-title="{{ $featuredImage->getCustomProperty('title')[$currentLocale] ?? '' }}"
                               data-description="{{ $featuredImage->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                <img src="{{ $featuredImage->hasGeneratedConversion('medium') ? $featuredImage->getUrl('medium') : $featuredImage->getUrl() }}"
                                     alt="{{ $featuredImage->getCustomProperty('alt_text')[$currentLocale] ?? $title }}"
                                     class="w-full rounded-xl shadow-lg cursor-pointer hover:shadow-2xl transition-all duration-300 hover:scale-[1.02]">
                            </a>
                            @if($featuredImage->getCustomProperty('title')[$currentLocale] ?? false)
                                <figcaption class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    <strong class="block font-semibold text-gray-900 dark:text-white mb-1">
                                        {{ $featuredImage->getCustomProperty('title')[$currentLocale] }}
                                    </strong>
                                    @if($featuredImage->getCustomProperty('description')[$currentLocale] ?? false)
                                        <span class="block leading-relaxed">
                                            {{ $featuredImage->getCustomProperty('description')[$currentLocale] }}
                                        </span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    </div>
                @endif

                {{-- İçerik --}}
                <article class="{{ $featuredImage ? 'lg:col-span-2' : 'lg:col-span-3' }} order-1 lg:order-2 space-y-10">
                    @if($shortDescription)
                        <section class="bg-blue-50/60 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 rounded-2xl p-6 shadow-sm">
                            <p class="text-lg md:text-xl text-blue-900 dark:text-blue-100 leading-relaxed">
                                {{ $shortDescription }}
                            </p>
                        </section>
                    @endif

                    @if($highlightedFeatures->isNotEmpty())
                        <section>
                            <header class="mb-5">
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('featured_highlights') }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $uiText('featured_highlights_subtitle') }}
                                </p>
                            </header>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                                @foreach($highlightedFeatures as $feature)
                                    <div class="relative overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/60 p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                                        @if($feature['icon'])
                                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-300 mb-4">
                                                <i class="fa-solid fa-{{ $feature['icon'] }} text-xl"></i>
                                            </div>
                                        @endif
                                        @if($feature['title'])
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                                {{ $feature['title'] }}
                                            </h3>
                                        @endif
                                        @if($feature['description'])
                                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                                {{ $feature['description'] }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <div class="prose prose-lg md:prose-xl max-w-none dark:prose-invert
                          prose-headings:font-bold prose-headings:tracking-tight
                          prose-headings:text-gray-900 dark:prose-headings:text-white
                          prose-h2:text-2xl prose-h2:md:text-3xl prose-h2:mt-12 prose-h2:mb-6
                          prose-h3:text-xl prose-h3:md:text-2xl prose-h3:mt-10 prose-h3:mb-4
                          prose-h4:text-lg prose-h4:md:text-xl prose-h4:mt-8 prose-h4:mb-3
                          prose-p:text-gray-700 dark:prose-p:text-gray-300 prose-p:leading-relaxed prose-p:mb-6
                          prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline
                          prose-a:font-medium hover:prose-a:text-blue-700 dark:hover:prose-a:text-blue-300
                          prose-strong:text-gray-900 dark:prose-strong:text-white prose-strong:font-semibold
                          prose-ul:my-6 prose-ol:my-6 prose-li:my-2 prose-li:leading-relaxed
                          prose-blockquote:border-l-4 prose-blockquote:border-l-blue-500 prose-blockquote:bg-blue-50/50
                          dark:prose-blockquote:bg-blue-900/10 prose-blockquote:py-4 prose-blockquote:px-6
                          prose-blockquote:italic prose-blockquote:my-8
                          prose-code:text-blue-600 dark:prose-code:text-blue-400 prose-code:bg-blue-50
                          dark:prose-code:bg-blue-900/20 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                          prose-code:font-mono prose-code:text-sm
                          prose-pre:bg-gray-900 dark:prose-pre:bg-gray-800 prose-pre:rounded-lg
                          prose-pre:shadow-lg prose-pre:my-8
                          prose-img:rounded-xl prose-img:shadow-md prose-img:my-8
                          prose-hr:my-12 prose-hr:border-gray-200 dark:prose-hr:border-gray-700">
                        @parsewidgets($longDescription ?? '')
                    </div>

                    @if(!empty($featuresList))
                        <section>
                            <header class="mb-4">
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('key_features') }}
                                </h2>
                            </header>
                            <ul class="grid grid-cols-1 gap-3">
                                @foreach($featuresList as $feature)
                                    <li class="flex items-start gap-3 bg-white dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-xl px-4 py-3">
                                        <span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-300">
                                            <i class="fa-solid fa-check text-sm"></i>
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if(!empty($useCases))
                        <section>
                            <header class="mb-4">
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('use_cases') }}
                                </h2>
                            </header>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($useCases as $case)
                                    <div class="flex items-start gap-3 p-4 rounded-2xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50/70 dark:bg-indigo-900/30 shadow-sm">
                                        <span class="mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-white">
                                            <i class="fa-solid fa-location-dot text-sm"></i>
                                        </span>
                                        <p class="text-sm text-indigo-900 dark:text-indigo-100 leading-relaxed">{{ $case }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if(!empty($competitiveAdvantages))
                        <section>
                            <header class="mb-4">
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('competitive_advantages') }}
                                </h2>
                            </header>
                            <ul class="space-y-3">
                                @foreach($competitiveAdvantages as $advantage)
                                    <li class="flex items-start gap-3 bg-amber-50/70 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl px-4 py-3 shadow-sm">
                                        <span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white">
                                            <i class="fa-solid fa-star text-xs"></i>
                                        </span>
                                        <span class="text-sm text-amber-900 dark:text-amber-100 leading-relaxed">{{ $advantage }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if(!empty($targetIndustries))
                        <section>
                            <header class="mb-4">
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('target_industries') }}
                                </h2>
                            </header>
                            <div class="flex flex-wrap gap-2">
                                @foreach($targetIndustries as $industry)
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-200 text-sm">
                                        <i class="fa-solid fa-briefcase"></i>
                                        {{ $industry }}
                                    </span>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if(!empty($technicalSpecGroups))
                        <section class="space-y-6">
                            <header>
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('technical_specs') }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $uiText('technical_specs_subtitle') }}
                                </p>
                            </header>

                            @foreach($technicalSpecGroups as $group)
                                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900/60 shadow-sm">
                                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $group['title'] }}
                                        </h3>
                                    </div>
                                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 px-6 py-6">
                                        @foreach($group['rows'] as $row)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                    {{ $row['label'] }}
                                                </dt>
                                                <dd class="mt-1 text-base text-gray-900 dark:text-gray-100">
                                                    {{ $row['value'] }}
                                                </dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            @endforeach
                        </section>
                    @endif

                    @if($faqEntries->isNotEmpty())
                        <section class="space-y-4">
                            <header>
                                <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-white">
                                    {{ $uiText('faq_title') }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $uiText('faq_subtitle') }}
                                </p>
                            </header>
                            @foreach($faqEntries as $faq)
                                <details class="group border border-gray-200 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900/60 shadow-sm hover:shadow-md transition-all duration-300">
                                    <summary class="cursor-pointer list-none px-5 py-4 flex items-center justify-between gap-4">
                                        <span class="text-base font-semibold text-gray-900 dark:text-white leading-relaxed">{{ $faq['question'] }}</span>
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-300 transition-transform duration-300 group-open:rotate-45">
                                            <i class="fa-solid fa-plus"></i>
                                        </span>
                                    </summary>
                                    <div class="px-5 pb-5 -mt-2 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {!! nl2br(e($faq['answer'])) !!}
                                    </div>
                                </details>
                            @endforeach
                        </section>
                    @endif

                    @if($warrantyInfo)
                        <section>
                            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50/80 dark:bg-emerald-900/30 p-6">
                                <h2 class="text-xl font-semibold text-emerald-900 dark:text-emerald-100 mb-3">
                                    {{ $uiText('warranty_info') }}
                                </h2>
                                <p class="text-sm text-emerald-800 dark:text-emerald-100 leading-relaxed whitespace-pre-line">
                                    {{ $warrantyInfo['coverage'] ?? ($warrantyInfo['support'] ?? ($warrantyInfo['description'] ?? '')) }}
                                </p>
                                @if(isset($warrantyInfo['duration_months']))
                                    <p class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/60 dark:bg-emerald-900/60 text-emerald-900 dark:text-emerald-100 text-sm font-medium shadow-sm">
                                        <i class="fa-solid fa-shield-heart"></i>
                                        {{ $warrantyInfo['duration_months'] }} {{ $uiText('warranty_months') }}
                                    </p>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if(!empty($tags))
                        <section class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-200">
                                    <i class="fa-solid fa-hashtag mr-2 text-gray-500 dark:text-gray-400"></i>
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </section>
                    @endif
                </article>
            </div>

            {{-- Galeri - İçeriğin Altında --}}
            @if($galleryImages->count() > 0)
                <div class="mt-16 md:mt-20 pt-12 border-t-2 border-gray-200 dark:border-gray-700">
                    <header class="mb-8">
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ __('mediamanagement::admin.gallery') }}
                        </h2>
                        <div class="h-1 w-16 bg-gradient-to-r from-blue-600 to-blue-400 dark:from-blue-500 dark:to-blue-300 rounded-full"></div>
                    </header>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach($galleryImages as $image)
                            <figure class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition-all duration-300">
                                <a href="{{ $image->getUrl() }}"
                                   class="glightbox"
                                   data-gallery="shop-gallery"
                                   data-title="{{ $image->getCustomProperty('title')[$currentLocale] ?? '' }}"
                                   data-description="{{ $image->getCustomProperty('description')[$currentLocale] ?? '' }}">
                                    <img src="{{ $image->getUrl('thumb') }}"
                                         alt="{{ $image->getCustomProperty('alt_text')[$currentLocale] ?? '' }}"
                                         class="w-full h-48 md:h-56 object-cover cursor-pointer transition-transform duration-500 group-hover:scale-110">
                                </a>
                                @if($image->getCustomProperty('title')[$currentLocale] ?? false)
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end p-4 pointer-events-none">
                                        <div class="text-white transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                            <strong class="block text-sm font-semibold mb-1">
                                                {{ $image->getCustomProperty('title')[$currentLocale] }}
                                            </strong>
                                            @if($image->getCustomProperty('description')[$currentLocale] ?? false)
                                                <span class="block text-xs leading-relaxed line-clamp-2">
                                                    {{ $image->getCustomProperty('description')[$currentLocale] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($item->js))
                <script>
                    {!! $item->js !!}
                </script>
            @endif

            @if (isset($item->css))
                <style>
                    {!! $item->css !!}
                </style>
            @endif

            <footer class="mt-16 md:mt-20 pt-8 border-t-2 border-gray-200 dark:border-gray-700">
                <a href="{{ $shopIndexUrl }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 dark:from-blue-500 dark:to-blue-400 dark:hover:from-blue-600 dark:hover:to-blue-500 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>{{ __('shop::front.general.all_shops') }}</span>
                </a>
            </footer>
        </div>
    </div>

@endsection
