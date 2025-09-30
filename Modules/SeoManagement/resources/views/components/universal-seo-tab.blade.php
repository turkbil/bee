{{--
    A1'deki Page manage SEO tab'ından AYNEN kopyalanan universal component
    Kullanım: <x-seomanagement::universal-seo-tab :model="$model" :available-languages="$availableLanguages" :current-language="$currentLanguage" :seo-data-cache="$seoDataCache" />
--}}

<div class="universal-seo-tab-wrapper">
    <style>
        .hover-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .hover-card:hover {
            background-color: rgba(0, 0, 0, 0.03);
            border-color: rgba(0, 0, 0, 0.2);
        }

        .hover-element {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .hover-element:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .accordion-button {
            transition: all 0.2s ease;
        }

        .accordion-button:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .list-group-item.active {
            border-left: none !important;
            border-left-width: 0 !important;
        }

        .list-group-item.list-group-item-action.active {
            border-left: none !important;
            border-left-width: 0 !important;
        }

        .accordion-item {
            transition: all 0.2s ease;
        }

        .accordion-item:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .accordion-header button {
            transition: all 0.2s ease;
        }

        .accordion-header button:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        .accordion-collapse.show {
            background-color: rgba(0, 123, 255, 0.02);
        }

        .accordion-header .accordion-button:not(.collapsed) {
            background-color: rgba(0, 123, 255, 0.15) !important;
            color: #0d6efd !important;
            border-color: rgba(0, 123, 255, 0.2) !important;
        }

        .accordion-header .accordion-button:not(.collapsed):hover {
            background-color: rgba(0, 123, 255, 0.25) !important;
        }
    </style>

    @props([
        'model' => null,
        'availableLanguages' => [],
        'currentLanguage' => app()->getLocale(),
        'seoDataCache' => [],
        'pageId' => null,
        'disabled' => false, // Önizleme için disable özelliği
        'staticAiRecommendations' => [],
        'dynamicAiRecommendations' => [],
        'recommendationLoaders' => [],
        'recommendationErrors' => [],
    ])

    @php
        $resolvedModel = $model;

        if (!$resolvedModel && $pageId) {
            $resolvedModel = \App\Services\GlobalCacheService::getPageWithSeo($pageId);
        }

        $seoSettings = $resolvedModel?->seoSetting ?? null;

        $activeLanguages = $availableLanguages;

        if (empty($activeLanguages)) {
            try {
                $activeLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::query()
                    ->where('is_active', true)
                    ->pluck('code')
                    ->toArray();
            } catch (\Throwable $e) {
                $activeLanguages = [];
            }

            if (empty($activeLanguages)) {
                $activeLanguages = [session('site_default_language', config('app.locale', 'tr'))];
            }
        }

        $availableLanguages = array_values(array_unique(array_filter($activeLanguages)));

        $resolvedLanguage = $currentLanguage ?: session('page_manage_language');
        if (!$resolvedLanguage || !in_array($resolvedLanguage, $availableLanguages, true)) {
            $resolvedLanguage = $availableLanguages[0] ?? config('app.locale', 'tr');
        }
        $currentLanguage = $resolvedLanguage;

        if (empty($seoDataCache) && $seoSettings) {
            $preparedCache = [];
            foreach ($availableLanguages as $lang) {
                $preparedCache[$lang] = [
                    'seo_title' => $seoSettings->titles[$lang] ?? '',
                    'seo_description' => $seoSettings->descriptions[$lang] ?? '',
                    'seo_keywords' => is_array($seoSettings->keywords[$lang] ?? null)
                        ? implode(', ', $seoSettings->keywords[$lang])
                        : $seoSettings->keywords[$lang] ?? '',
                    'focus_keywords' => is_array($seoSettings->focus_keywords[$lang] ?? null)
                        ? implode(', ', $seoSettings->focus_keywords[$lang])
                        : $seoSettings->focus_keywords[$lang] ?? '',
                    'og_title' => $seoSettings->og_titles[$lang] ?? '',
                    'og_description' => $seoSettings->og_descriptions[$lang] ?? '',
                    'og_image' => $seoSettings->og_images[$lang] ?? ($seoSettings->og_image ?? ''),
                    'content_type' => $seoSettings->content_types[$lang] ?? ($seoSettings->content_type ?? 'website'),
                    'content_type_custom' => $seoSettings->content_type_custom[$lang] ?? '',
                    'priority_score' => $seoSettings->priority_scores[$lang] ?? ($seoSettings->priority_score ?? 5),
                    'author_name' => $seoSettings->author_names[$lang] ?? '',
                    'author_url' => $seoSettings->author_urls[$lang] ?? '',
                    'og_custom_enabled' =>
                        !empty($seoSettings->og_titles[$lang] ?? null) ||
                        !empty($seoSettings->og_descriptions[$lang] ?? null),
                ];
            }
            $seoDataCache = $preparedCache;
        }

        foreach ($availableLanguages as $lang) {
            if (!isset($seoDataCache[$lang])) {
                $seoDataCache[$lang] = [
                    'seo_title' => '',
                    'seo_description' => '',
                    'seo_keywords' => '',
                    'focus_keywords' => '',
                    'og_title' => '',
                    'og_description' => '',
                    'og_image' => '',
                    'content_type' => 'website',
                    'content_type_custom' => '',
                    'priority_score' => 5,
                    'author_name' => '',
                    'author_url' => '',
                    'og_custom_enabled' => false,
                ];
            }
        }


        $hasAiRecommendations = false;
        $aiRecommendations = null;

        if ($seoSettings && !empty($seoSettings->ai_suggestions)) {
            $allAiSuggestions = $seoSettings->ai_suggestions;
            if (is_array($allAiSuggestions) && isset($allAiSuggestions[$currentLanguage])) {
                $hasAiRecommendations = true;
                $aiRecommendations = $allAiSuggestions[$currentLanguage];
            }
        }
    @endphp

    @foreach ($availableLanguages as $lang)
        @php
            $recommendationLoader = $recommendationLoaders[$lang] ?? false;
            $recommendationError = $recommendationErrors[$lang] ?? null;

            // AUTO: Use database data from seoSettings - ALL LANGUAGES OR DEFAULT ONLY
            $staticRecommendationState = [];
            $debugInfo = [];

            if ($seoSettings && !empty($seoSettings->ai_suggestions)) {
                $allSuggestions = is_string($seoSettings->ai_suggestions)
                    ? json_decode($seoSettings->ai_suggestions, true)
                    : $seoSettings->ai_suggestions;

                $debugInfo['available_langs'] = array_keys($allSuggestions ?? []);
                $debugInfo['current_lang'] = $lang;
                $debugInfo['default_lang'] = get_tenant_default_locale();

                // STRATEGY 1: Try current language first
                if (isset($allSuggestions[$lang])) {
                    $staticRecommendationState = $allSuggestions[$lang];
                    $debugInfo['used_strategy'] = "1_current_lang_$lang";
                }
                // STRATEGY 2: Fallback to default tenant language
                else {
                    $defaultLang = get_tenant_default_locale();
                    if (isset($allSuggestions[$defaultLang])) {
                        $staticRecommendationState = $allSuggestions[$defaultLang];
                        $debugInfo['used_strategy'] = "2_default_lang_$defaultLang";
                    }
                    // STRATEGY 3: Fallback to any available language
                    else if (!empty($allSuggestions)) {
                        $staticRecommendationState = reset($allSuggestions); // First available
                        $debugInfo['used_strategy'] = "3_first_available_" . array_keys($allSuggestions)[0];
                    }
                    else {
                        $debugInfo['used_strategy'] = "no_data";
                    }
                }
            } else {
                $debugInfo['error'] = $seoSettings ? 'ai_suggestions_empty' : 'no_seo_settings';
            }

            $dynamicRecommendationState = $dynamicAiRecommendations[$lang] ?? [];
            $hasStaticRecommendations = !empty($staticRecommendationState);
            $hasDynamicRecommendations = !empty($dynamicRecommendationState);
            $activeRecommendationState = $hasDynamicRecommendations
                ? $dynamicRecommendationState
                : $staticRecommendationState;
            $hasActiveRecommendations = !empty($activeRecommendationState);
        @endphp

        <div class="seo-language-content" data-language="{{ $lang }}"
            style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

            {{-- AI ÖNERİLERİ TOOLBAR - TÜM DİLLER İÇİN OTOMATİK --}}
            @if (!$disabled && (get_tenant_default_locale() === $lang))
                <div class="ai-seo-toolbar mb-4">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <button type="button" class="btn btn-primary ai-seo-recommendations-btn"
                            data-seo-feature="seo-smart-recommendations"
                            data-language="auto"
                            data-all-languages="{{ implode(',', $availableLanguages) }}"
                            style="z-index: 9999; position: relative;">
                            <i class="fas fa-magic me-1"></i>
                            AI Önerileri
                            <small class="ms-1">(Tüm Diller)</small>
                        </button>
                    </div>
                </div>

            @endif


            {{-- 💡 AI SEO ÖNERİLERİ - BAĞIMSIZ LOADİNG SİSTEMİ --}}
            @if (
                !$disabled &&
                (get_tenant_default_locale() === $lang) &&
                (
                    (isset($staticAiRecommendations[$lang]) && !empty($staticAiRecommendations[$lang]) && isset($staticAiRecommendations[$lang]['data']['recommendations']) && !empty($staticAiRecommendations[$lang]['data']['recommendations'])) ||
                    (isset($dynamicAiRecommendations[$lang]) && !empty($dynamicAiRecommendations[$lang]) && isset($dynamicAiRecommendations[$lang]['data']['recommendations']) && !empty($dynamicAiRecommendations[$lang]['data']['recommendations'])) ||
                    (isset($recommendationLoaders[$lang]) && $recommendationLoaders[$lang])
                )
            )
                <div class="mt-4" wire:key="ai-recommendations-container-{{ $lang }}-{{ md5(serialize($staticAiRecommendations[$lang] ?? []) . serialize($dynamicAiRecommendations[$lang] ?? [])) }}">
                    {{-- AI RECOMMENDATION LOADING DURUMU - SADECE LOADER GÖSTERİLİR --}}
                    @if (isset($recommendationLoaders[$lang]) && $recommendationLoaders[$lang])
                        <div class="text-center p-4 mb-4 bg-light rounded" wire:key="ai-recommendations-loader-{{ $lang }}">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">AI önerileri üretiliyor...</span>
                            </div>
                            <h5 class="mt-3 mb-1">🤖 AI Önerileri Hazırlanıyor</h5>
                            <p class="text-muted">Sayfanız analiz ediliyor ve özelleştirilmiş öneriler üretiliyor...</p>
                        </div>
                    @else
                        {{-- AI RECOMMENDATION LOADING DEĞİLSE NORMAL İÇERİK GÖSTERİLİR --}}
                        <div wire:ignore.self class="ai-seo-recommendations-section" id="aiSeoRecommendationsSection_{{ $lang }}" wire:key="ai-recommendations-{{ $lang }}-{{ $loop->iteration ?? 1 }}">
                            <div class="row">
                                <div class="col-12">
                                    <div class="bg-light border p-3 rounded-3 mb-3 position-relative">
                                        <h3 class="mb-0">
                                            <i class="fas fa-magic me-2"></i>
                                            AI SEO Önerileri
                                            @php
                                                $currentRecommendations = isset($dynamicAiRecommendations[$lang]) && !empty($dynamicAiRecommendations[$lang])
                                                    ? $dynamicAiRecommendations[$lang]
                                                    : (isset($staticAiRecommendations[$lang]) && !empty($staticAiRecommendations[$lang]) ? $staticAiRecommendations[$lang] : []);
                                            @endphp
                                            @if (!empty($currentRecommendations))
                                                @if (isset($dynamicAiRecommendations[$lang]) && !empty($dynamicAiRecommendations[$lang]))
                                                    <span class="badge bg-success ms-2">🤖 AI Güncel</span>
                                                @elseif(isset($staticAiRecommendations[$lang]) && !empty($staticAiRecommendations[$lang]))
                                                    <span class="badge bg-secondary ms-2">📊 Kaydedilmiş</span>
                                                @endif
                                            @endif
                                        </h3>
                                        @if (!$disabled)
                                            <button type="button" class="btn btn-outline-danger btn-sm position-absolute"
                                                style="right: 1rem; top: 50%; transform: translateY(-50%);"
                                                wire:click="clearAiRecommendations"
                                                onclick="return confirm('AI önerileri silinecek. Emin misiniz?')"
                                                title="AI önerilerini sıfırla">
                                                <i class="fas fa-trash-alt me-1"></i>
                                                Sıfırla
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                {{-- ORIGINAL PATTERN RECOMMENDATIONS CONTENT --}}
                                @php
                                    // CurrentRecommendations'ı yeniden hesapla (scope issue)
                                    $currentRecommendations = isset($dynamicAiRecommendations[$lang]) && !empty($dynamicAiRecommendations[$lang])
                                        ? $dynamicAiRecommendations[$lang]
                                        : (isset($staticAiRecommendations[$lang]) && !empty($staticAiRecommendations[$lang]) ? $staticAiRecommendations[$lang] : []);
                                    $recommendationsData = $currentRecommendations['data']['recommendations'] ?? $currentRecommendations['recommendations'] ?? [];
                                @endphp

                                {{-- RECOMMENDATIONS LIST --}}
                                <div class="ai-recommendations-list" wire:key="ai-recommendations-list-{{ $lang }}">
                                    @if (!empty($recommendationsData))
                                        @php
                                            $seoRecs = collect($recommendationsData)->filter(function ($rec) {
                                                return in_array($rec['type'], [
                                                    'title',
                                                    'description',
                                                    'seo_title',
                                                    'seo_description',
                                                ]);
                                            });
                                            $socialRecs = collect($recommendationsData)->filter(function ($rec) {
                                                return str_contains($rec['type'], 'og_') ||
                                                    str_contains($rec['type'], 'social');
                                            });
                                        @endphp

                                        @if ($seoRecs->count() > 0)
                                            <div class="row mb-4" wire:key="seo-recs-{{ $lang }}">
                                                @foreach ($seoRecs as $index => $rec)
                                                    <div class="col-6" wire:key="seo-rec-{{ $lang }}-{{ $index }}">
                                                        <div class="card" wire:key="seo-card-{{ $lang }}-{{ $index }}">
                                                            <div class="card-header">
                                                                <h3 class="card-title">{{ $rec['title'] ?? 'SEO Önerisi' }}</h3>
                                                            </div>
                                                            <div class="list-group list-group-flush">
                                                                @if (isset($rec['alternatives']) && !empty($rec['alternatives']))
                                                                    @foreach ($rec['alternatives'] as $altIndex => $alt)
                                                                        <a href="#"
                                                                            class="list-group-item list-group-item-action{{ $altIndex === 0 ? ' active' : '' }}"
                                                                            onclick="applyAlternativeDirectly('{{ $rec['field_target'] }}', '{{ addslashes($alt['value']) }}', this); return false;">
                                                                            {{ $alt['value'] }}
                                                                        </a>
                                                                    @endforeach
                                                                @else
                                                                    <a href="#" class="list-group-item list-group-item-action">
                                                                        {{ $rec['value'] ?? ($rec['suggested_value'] ?? '') }}
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($socialRecs->count() > 0)
                                            <div class="row mb-4" wire:key="social-recs-{{ $lang }}">
                                                @foreach ($socialRecs as $index => $rec)
                                                    <div class="col-6" wire:key="social-rec-{{ $lang }}-{{ $index }}">
                                                        <div class="card" wire:key="social-card-{{ $lang }}-{{ $index }}">
                                                            <div class="card-header">
                                                                <h3 class="card-title">{{ $rec['title'] ?? 'Sosyal Medya Önerisi' }}</h3>
                                                            </div>
                                                            <div class="list-group list-group-flush">
                                                                @if (isset($rec['alternatives']) && !empty($rec['alternatives']))
                                                                    @foreach ($rec['alternatives'] as $altIndex => $alt)
                                                                        <a href="#"
                                                                            class="list-group-item list-group-item-action{{ $altIndex === 0 ? ' active' : '' }}"
                                                                            onclick="applyAlternativeDirectly('{{ $rec['field_target'] }}', '{{ addslashes($alt['value']) }}', this); return false;">
                                                                            {{ $alt['value'] }}
                                                                        </a>
                                                                    @endforeach
                                                                @else
                                                                    <a href="#" class="list-group-item list-group-item-action">
                                                                        {{ $rec['value'] ?? ($rec['suggested_value'] ?? '') }}
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            AI önerileri bulunamadı. Yeni öneriler oluşturmak için "AI Önerileri" butonunu kullanın.

                                            {{-- DEBUG INFO --}}
                                            @if (!empty($debugInfo))
                                                <hr>
                                                <small>
                                                    <strong>Debug:</strong><br>
                                                    @foreach ($debugInfo as $key => $value)
                                                        {{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}<br>
                                                    @endforeach
                                                </small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- SADECE AI ÖNERİLERİ --}}

            {{-- TEMEL SEO ALANLARI --}}
            <div class="card border-primary mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        Temel SEO Ayarları
                        <small class=" ms-2">Mutlaka doldurulması gerekenler</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Meta Title --}}
                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <div class="form-floating">
                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.seo_title"
                                        class="form-control seo-no-enter @error('seoDataCache.' . $lang . '.seo_title') is-invalid @enderror"
                                        placeholder="{{ __('page::admin.seo_title_placeholder') }}" maxlength="60"
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <label>
                                        {{ __('page::admin.seo_title') }}
                                    </label>
                                    {{-- Character counter - sağ üst köşe --}}
                                    <span class="character-counter position-absolute"
                                        id="title_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                        <small>0/60</small>
                                    </span>
                                    <div class="form-text">
                                        <small>
                                            {{ __('page::admin.seo_title_help') }}
                                        </small>
                                    </div>
                                    @error('seoDataCache.' . $lang . '.seo_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- Meta Description --}}
                        <div class="col-md-6 mb-3">
                            <div class="position-relative">
                                <div class="form-floating">
                                    <textarea wire:model="seoDataCache.{{ $lang }}.seo_description"
                                        class="form-control seo-no-enter @error('seoDataCache.' . $lang . '.seo_description') is-invalid @enderror"
                                        placeholder="{{ __('page::admin.seo_description_placeholder') }}" style="height: 100px; resize: vertical;"
                                        maxlength="160" {{ $disabled ? 'disabled' : '' }}></textarea>
                                    <label>
                                        {{ __('page::admin.seo_description') }}
                                    </label>
                                    {{-- Character counter - sağ üst köşe --}}
                                    <span class="character-counter position-absolute"
                                        id="description_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                        <small>0/160</small>
                                    </span>
                                    <div class="form-text">
                                        <small>
                                            {{ __('page::admin.seo_description_help') }}
                                        </small>
                                    </div>
                                    @error('seoDataCache.' . $lang . '.seo_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>


                        {{-- İçerik Türü --}}
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <select wire:model="seoDataCache.{{ $lang }}.content_type"
                                    class="form-select"
                                    onchange="toggleCustomContentType(this, '{{ $lang }}')"
                                    {{ $disabled ? 'disabled' : '' }}>
                                    <option value="website">Website/WebPage (Genel Site)</option>
                                    <option value="article">Article (Makale/Blog)</option>
                                    <option value="product">Product (Ürün)</option>
                                    <option value="organization">Organization (Organizasyon)</option>
                                    <option value="local_business">LocalBusiness (Yerel İşletme)</option>
                                    <option value="event">Event (Etkinlik)</option>
                                    <option value="person">Person (Kişi)</option>
                                    <option value="video">Video (Film/Video)</option>
                                    <option value="music">Music (Müzik)</option>
                                    <option value="faq">FAQ (Sıkça Sorulan Sorular)</option>
                                    <option value="custom">Diğer (Manuel Giriş)</option>
                                </select>
                                <label>
                                    İçerik Türü
                                    <small class="ms-2">Schema.org + OpenGraph</small>
                                </label>
                                <div class="form-text">
                                    <small>
                                        Hem sosyal medya hem arama motorları için kullanılır
                                    </small>
                                </div>
                            </div>

                            {{-- Custom Content Type Input --}}
                            <div class="mt-3" id="custom_content_type_{{ $lang }}"
                                style="display: none;">
                                <div class="form-floating">
                                    <input type="text"
                                        wire:model="seoDataCache.{{ $lang }}.content_type_custom"
                                        class="form-control seo-no-enter" placeholder="Örn: Recipe, Book, Course..."
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <label>
                                        Özel İçerik Türü
                                        <small class="ms-2">Manuel giriş</small>
                                    </label>
                                    <div class="form-text">
                                        <small>
                                            Schema.org'dan geçerli bir tür girin (Recipe, Book, Course...)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Priority --}}
                        <div class="col-md-6 mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">

                                    SEO Önceliği
                                </label>
                                @php
                                    $priorityValue = $seoDataCache[$lang]['priority_score'] ?? 5;
                                    $badgeClass = 'bg-warning'; // Default for Orta
                                    $priorityText = 'Orta';

                                    if ($priorityValue >= 1 && $priorityValue <= 3) {
                                        $badgeClass = 'bg-info';
                                        $priorityText = 'Düşük';
                                    } elseif ($priorityValue >= 4 && $priorityValue <= 6) {
                                        $badgeClass = 'bg-warning';
                                        $priorityText = 'Orta';
                                    } elseif ($priorityValue >= 7 && $priorityValue <= 8) {
                                        $badgeClass = 'bg-success';
                                        $priorityText = 'Yüksek';
                                    } else {
                                        $badgeClass = 'bg-danger';
                                        $priorityText = 'Kritik';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} priority-badge" style="position: relative;">
                                    <span class="priority-value">{{ $priorityValue }}</span>/10 - <span
                                        class="priority-text">{{ $priorityText }}</span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold">1</span>
                                <span class="small">Düşük</span>
                                <input type="range" wire:model="seoDataCache.{{ $lang }}.priority_score"
                                    class="form-range flex-grow-1 mx-2" min="1" max="10" step="1"
                                    value="{{ $seoDataCache[$lang]['priority_score'] ?? 5 }}"
                                    oninput="onManualPriorityChange(this, '{{ $lang }}')"
                                    {{ $disabled ? 'disabled' : '' }}>
                                <span class="small">Kritik</span>
                                <span class="small fw-bold">10</span>
                            </div>
                            <div class="form-text mt-2 priority-examples">
                                <small>

                                    <span class="priority-example" data-range="1-3"
                                        style="opacity: 0.4;"><strong>1-3:</strong> Blog yazıları, arşiv</span>
                                    &nbsp;•&nbsp;
                                    <span class="priority-example" data-range="4-6"
                                        style="opacity: 1;"><strong>4-6:</strong> Ürün sayfaları</span> &nbsp;•&nbsp;
                                    <span class="priority-example" data-range="7-8"
                                        style="opacity: 0.4;"><strong>7-8:</strong> Önemli kategoriler</span>
                                    &nbsp;•&nbsp;
                                    <span class="priority-example" data-range="9-10"
                                        style="opacity: 0.4;"><strong>9-10:</strong> Ana sayfa, kampanyalar</span>
                                </small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- SOSYAL MEDYA & PAYLAŞIM SECTİON --}}
            <hr class="my-4">
            <h6 class="mb-3">
                Sosyal Medya & Schema Ayarları
            </h6>

            {{-- SOSYAL MEDYA AYARLARI --}}
            <div class="card border-success mb-4"
                style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem !important; transition: border-radius 0.15s;">
                <div class="card-header bg-success text-white"
                    style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem 0.25rem 0px 0px !important;">
                    <h6 class="mb-0">
                        Sosyal Medya Paylaşım Ayarları
                        <small class=" ms-2">Facebook, LinkedIn, WhatsApp için</small>
                    </h6>
                </div>
                <div class="card-body" style="border-radius: 0px 0px 0.25rem 0.25rem !important;">
                    @if ($lang === ($availableLanguages[0] ?? 'tr'))
                        <div class="row">
                            {{-- Sosyal Medya Görseli --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Sosyal Medya Resmi
                                    <small class="ms-2">1200x630 önerilen</small>
                                </label>

                                {{-- Media Preview --}}
                                @if (!empty($seoDataCache[$lang]['og_image']))
                                    <div class="media-preview-container mb-2 position-relative">
                                        <img src="{{ $seoDataCache[$lang]['og_image'] }}"
                                            class="img-fluid rounded border" style="max-height: 120px; width: auto;"
                                            alt="OG Image Preview">
                                        <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                            wire:click="$set('seoDataCache.{{ $lang }}.og_image', '')"
                                            {{ $disabled ? 'disabled' : '' }}>
                                            ×
                                        </button>
                                    </div>
                                @endif

                                {{-- Media Selection Buttons --}}
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm flex-fill"
                                        onclick="document.getElementById('og_image_file_{{ $lang }}').click()"
                                        {{ $disabled ? 'disabled' : '' }}>

                                        {{ empty($seoDataCache[$lang]['og_image']) ? 'Resim Seç' : 'Resim Değiştir' }}
                                    </button>

                                    <input type="url" wire:model="seoDataCache.{{ $lang }}.og_image_url"
                                        class="form-control form-control-sm" placeholder="Veya URL girin"
                                        style="flex: 2;" {{ $disabled ? 'disabled' : '' }}>
                                </div>

                                {{-- Hidden File Input --}}
                                <input type="file" id="og_image_file_{{ $lang }}"
                                    wire:model="seoImageFiles.og_image" class="d-none"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    {{ $disabled ? 'disabled' : '' }}>

                                {{-- Upload Progress --}}
                                <div class="progress mt-2" wire:loading wire:target="seoImageFiles.og_image"
                                    style="height: 4px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                        style="width: 100%"></div>
                                </div>

                                <div class="form-text mt-2">
                                    <small>
                                        Facebook, LinkedIn, WhatsApp paylaşımlarında görünür
                                    </small>
                                </div>
                            </div>

                            {{-- Özelleştirme Switch --}}
                            <div class="col-md-6 mb-3">
                                <div class="mt-3">
                                    <div class="pretty p-switch">
                                        @php
                                            // TR dili için OG alanları doluysa otomatik checked
                                            $autoChecked = false;
                                            if ($lang === 'tr' && isset($seoDataCache[$lang])) {
                                                $ogTitle = $seoDataCache[$lang]['og_title'] ?? '';
                                                $ogDescription = $seoDataCache[$lang]['og_description'] ?? '';
                                                $autoChecked = !empty(trim($ogTitle)) || !empty(trim($ogDescription));
                                            }
                                        @endphp
                                        <input type="checkbox"
                                            wire:model="seoDataCache.{{ $lang }}.og_custom_enabled"
                                            id="og_custom_{{ $lang }}"
                                            onchange="toggleOgCustomFields(this, '{{ $lang }}')"
                                            {{ $autoChecked ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                                        <div class="state">
                                            <label for="og_custom_{{ $lang }}">
                                                Ayarları özelleştirmek istiyorum
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <small>
                                            Kapalıysa yukarıdaki SEO verilerini kullanır (otomatik sistem)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">

                            <strong>Bilgi:</strong> Sosyal medya ayarları tüm diller için ortaktır.
                        </div>
                    @endif

                    {{-- OG Custom Fields (Collapsible) --}}
                    <div class="og-custom-fields" id="og_custom_fields_{{ $lang }}"
                        style="display: {{ $autoChecked ? 'block' : 'none' }}; max-height: none; overflow: visible;">
                        <hr class="my-3">
                        <div class="row">
                            {{-- OG Title --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-floating position-relative"
                                    style="border-radius: 0.25rem !important; overflow: hidden !important;">
                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.og_title"
                                        class="form-control seo-no-enter"
                                        placeholder="Facebook/LinkedIn'de görünecek özel başlık" maxlength="60"
                                        style="border-radius: 0.25rem !important;" {{ $disabled ? 'disabled' : '' }}>
                                    <label>
                                        Sosyal Medya Başlığı
                                        <small class="ms-2">Maksimum 60 karakter</small>
                                    </label>
                                    {{-- Character counter - sağ üst köşe --}}
                                    <span class="character-counter position-absolute"
                                        id="og_title_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                        <small>0/60</small>
                                    </span>
                                    <div class="form-text">
                                        <small>
                                            Sosyal medya paylaşımlarında görünecek başlık
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- OG Description --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-floating position-relative"
                                    style="border-radius: 0.25rem !important; overflow: hidden !important;">
                                    <textarea wire:model="seoDataCache.{{ $lang }}.og_description" class="form-control seo-no-enter"
                                        placeholder="Facebook/LinkedIn'de görünecek özel açıklama"
                                        style="height: 100px; resize: vertical; border-radius: 0.25rem !important;" maxlength="155"
                                        {{ $disabled ? 'disabled' : '' }}></textarea>
                                    <label>
                                        Sosyal Medya Açıklaması
                                        <small class="ms-2">Maksimum 155 karakter</small>
                                    </label>
                                    {{-- Character counter - sağ üst köşe --}}
                                    <span class="character-counter position-absolute"
                                        id="og_description_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                        <small>0/155</small>
                                    </span>
                                    <div class="form-text">
                                        <small>
                                            Sosyal medyada görünecek çekici açıklama
                                        </small>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            {{-- İÇERİK BİLGİLERİ --}}
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        İçerik Bilgileri
                        <small class=" ms-2">Yazar ve içerik metadata</small>
                    </h6>
                </div>
                <div class="card-body">
                    @if ($lang === ($availableLanguages[0] ?? 'tr'))
                        <div class="row">



                            {{-- Author Name --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.author_name"
                                        class="form-control seo-no-enter" placeholder="Nurullah Okatan"
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <label>
                                        Yazar Adı
                                        <small class="ms-2">İçerik yazarı</small>
                                    </label>
                                    <div class="form-text">
                                        <small>
                                            Bu içeriği yazan kişinin adı (schema.org author)
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Author URL/Profile --}}
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <input type="url" wire:model="seoDataCache.{{ $lang }}.author_url"
                                        class="form-control seo-no-enter"
                                        placeholder="https://example.com/author/nurullah-okatan"
                                        {{ $disabled ? 'disabled' : '' }}>
                                    <label>
                                        Yazar Profil URL'si
                                        <small class="ms-2">Yazarın profil sayfası</small>
                                    </label>
                                    <div class="form-text">
                                        <small>
                                            Yazarın profil sayfası veya kişisel web sitesi
                                        </small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @else
                        <div class="alert alert-info">

                            <strong>Bilgi:</strong> İçerik bilgileri tüm diller için ortaktır.
                        </div>
                    @endif
                </div>
            </div>


        </div>
    @endforeach

    {{-- ========== AI ÖNERİLERİ CONTAINER ========== --}}
    <div id="aiRecommendationsContainer"></div>

    @if (!$disabled)
        {{-- Mobile Responsive CSS --}}
        <style>
            @media (max-width: 768px) {
                .card-body {
                    padding: 1rem !important;
                }

                .card-header {
                    padding: 0.75rem 1rem !important;
                }

                .ai-seo-toolbar .card-body {
                    padding: 0.75rem !important;
                }

                .accordion-body {
                    padding: 1rem !important;
                }

                .col-6 {
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                }
            }
        </style>

        {{-- SEO JavaScript initialization --}}
        <script>
            // Component için SEO data hazırlama
            @if (!isset($seoJsInitialized))
                window.currentModelId =
                    @if ($pageId)
                        {{ $pageId }}
                    @else
                        null
                    @endif ;
                window.currentLanguage = '{{ $currentLanguage }}';

                // ULTRA PERFORMANCE: Tüm dillerin SEO verileri (ZERO API CALLS)
                try {
                    @php
                        // SEO Data Cache'den JavaScript için veri hazırla - HEM YENİ HEM ESKİ SAYFA
$allLangSeoData = $seoDataCache ?? [];

// Boş cache varsa her dil için boş veri oluştur (yeni sayfa için)
if (empty($allLangSeoData) && !empty($availableLanguages)) {
    foreach ($availableLanguages as $lang) {
        $allLangSeoData[$lang] = [
            'seo_title' => '',
            'seo_description' => '',
                                ];
                            }
                        }
                    @endphp
                    window.allLanguagesSeoData = @json($allLangSeoData);
                    console.log('✅ SEO Component Data JSON başarıyla yüklendi:', window.allLanguagesSeoData);
                    console.log('🔍 Mevcut diller:', Object.keys(window.allLanguagesSeoData || {}));
                    console.log('🌍 Mevcut aktif dil:', window.currentLanguage);
                } catch (error) {
                    console.error('❌ SEO Component Data JSON hatası:', error);
                    window.allLanguagesSeoData = {};
                }

                // Global değişkenler
                let currentLanguage = '{{ $currentLanguage }}';


                // Priority Display Update Function
                function updatePriorityDisplay(rangeInput, language) {
                    if (!rangeInput) {
                        console.warn('⚠️ updatePriorityDisplay: rangeInput null');
                        return;
                    }

                    const value = parseInt(rangeInput.value);
                    const parentContainer = rangeInput.closest('.seo-language-content');

                    if (!parentContainer) {
                        console.warn('⚠️ updatePriorityDisplay: parentContainer bulunamadı');
                        return;
                    }

                    const badge = parentContainer.querySelector('.priority-badge');
                    if (!badge) {
                        console.warn('⚠️ updatePriorityDisplay: priority-badge bulunamadı');
                        return;
                    }

                    const priorityValue = badge.querySelector('.priority-value');
                    const priorityText = badge.querySelector('.priority-text');
                    const examples = parentContainer.querySelectorAll('.priority-example');

                    if (!priorityValue || !priorityText) {
                        console.warn('⚠️ updatePriorityDisplay: priority-value veya priority-text bulunamadı');
                        return;
                    }

                    // Update badge value
                    priorityValue.textContent = value;

                    // Update priority text and badge color based on value
                    let priorityLabel = '';
                    let badgeClass = '';

                    if (value >= 1 && value <= 3) {
                        priorityLabel = 'Düşük';
                        badgeClass = 'bg-info';
                    } else if (value >= 4 && value <= 6) {
                        priorityLabel = 'Orta';
                        badgeClass = 'bg-warning';
                    } else if (value >= 7 && value <= 8) {
                        priorityLabel = 'Yüksek';
                        badgeClass = 'bg-success';
                    } else if (value >= 9 && value <= 10) {
                        priorityLabel = 'Kritik';
                        badgeClass = 'bg-danger';
                    }

                    // Remove all possible badge classes and add the new one
                    badge.className = badge.className.replace(/bg-(primary|secondary|success|danger|warning|info|light|dark)/g,
                        '');
                    badge.classList.add(badgeClass);

                    priorityText.textContent = priorityLabel;

                    // Update examples opacity
                    examples.forEach(example => {
                        const range = example.getAttribute('data-range');
                        const [min, max] = range.split('-').map(Number);

                        if (value >= min && value <= max) {
                            example.style.opacity = '1';
                            example.style.fontWeight = 'bold';
                        } else {
                            example.style.opacity = '0.4';
                            example.style.fontWeight = 'normal';
                        }
                    });

                    console.log(`🎯 Priority updated for ${language}: ${value} (${priorityLabel})`);
                }

                // Manuel priority değişiklik fonksiyonu (sadece display günceller)
                function onManualPriorityChange(rangeInput, language) {
                    updatePriorityDisplay(rangeInput, language);
                    console.log(`🎯 Manual priority changed for ${language}: ${rangeInput.value}`);
                }

                // Initialize range sliders for visible language
                function initializePrioritySliders() {
                    // Sadece görünür olan dil content'i için range slider'ları initialize et
                    const visibleContent = document.querySelector(
                        '.seo-language-content[style*="display: block"], .seo-language-content[style=""], .seo-language-content:not([style*="display: none"])'
                        );
                    if (visibleContent) {
                        const rangeInputs = visibleContent.querySelectorAll('input[type="range"]');
                        const language = visibleContent.getAttribute('data-language');

                        rangeInputs.forEach(rangeInput => {
                            updatePriorityDisplay(rangeInput, language);
                        });

                        console.log(`🎯 Priority sliders initialized for language: ${language}`);
                    }
                }

                // Initialize range sliders on page load
                document.addEventListener('DOMContentLoaded', function() {
                    initializePrioritySliders();
                });

                // Re-initialize when language changes
                document.addEventListener('livewire:navigated', function() {
                    setTimeout(initializePrioritySliders, 100);
                });

                // Listen for language switch events
                if (typeof window.addEventListener !== 'undefined') {
                    window.addEventListener('seo-language-changed', function(event) {
                        setTimeout(initializePrioritySliders, 100);
                    });
                }

                // 🔥 KRİTİK FIX: Livewire SEO dil değişimi listener
                document.addEventListener('livewire:navigated', function() {
                    Livewire.on('seo-language-switched', (event) => {
                        const language = event.language;
                        const seoData = event.seoData;

                        console.log(`🎯 SEO dil değişimi alındı: ${language}`, seoData);

                        // Priority slider'ları yenile
                        setTimeout(() => {
                            initializePrioritySliders();
                            console.log(`✅ SEO priority sliders yenilendi: ${language}`);
                        }, 100);
                    });
                });

                @php $seoJsInitialized = true; @endphp
            @endif

            // Universal Content Type Custom Toggle Function
            function toggleCustomContentType(selectElement, language) {
                const customDiv = document.getElementById('custom_content_type_' + language);
                const isCustom = selectElement.value === 'custom';

                if (customDiv) {
                    customDiv.style.display = isCustom ? 'block' : 'none';

                    // Eğer custom değilse, custom input'u temizle
                    if (!isCustom) {
                        const customInput = customDiv.querySelector('input');
                        if (customInput) {
                            customInput.value = '';
                            // Livewire'a da bildir
                            customInput.dispatchEvent(new Event('input'));
                        }
                    }
                }

                console.log(`🎯 Universal Content Type changed for ${language}: ${selectElement.value}`);
            }

            // OG Custom Fields Toggle Function
            function toggleOgCustomFields(checkbox, language) {
                const customDiv = document.getElementById('og_custom_fields_' + language);
                const isEnabled = checkbox.checked;

                if (customDiv) {
                    if (isEnabled) {
                        customDiv.style.display = 'block';
                        // Smooth animation
                        customDiv.style.maxHeight = 'none';
                        customDiv.style.overflow = 'visible';
                    } else {
                        customDiv.style.display = 'none';
                        // Clear OG custom fields if disabled
                        const ogInputs = customDiv.querySelectorAll('input, textarea, select');
                        ogInputs.forEach(input => {
                            if (input.type !== 'checkbox') {
                                input.value = '';
                                // Livewire'a da bildir
                                input.dispatchEvent(new Event('input'));
                            }
                        });
                    }
                }

                console.log(`📘 OpenGraph custom fields ${isEnabled ? 'enabled' : 'disabled'} for ${language}`);
            }

            // Sayfa yüklendiğinde mevcut değerleri kontrol et
            document.addEventListener('DOMContentLoaded', function() {
                const contentTypeSelects = document.querySelectorAll('select[wire\\:model*="content_type"]');
                contentTypeSelects.forEach(select => {
                    const wireModel = select.getAttribute('wire:model');
                    if (!wireModel) return;
                    const match = wireModel.match(/\.(\w+)\./);
                    if (!match || !match[1]) return;
                    const language = match[1];
                    if (select.value === 'custom') {
                        toggleCustomContentType(select, language);
                    }
                });
            });

            // Universal Content Type initialization listener
            document.addEventListener('livewire:navigated', function() {
                setTimeout(function() {
                    const contentTypeSelects = document.querySelectorAll(
                        'select[wire\\:model*=\"content_type\"]');
                    contentTypeSelects.forEach(select => {
                        const wireModel = select.getAttribute('wire:model');
                        if (wireModel) {
                            const match = wireModel.match(/\\.(\\w+)\\./);
                            if (!match || !match[1]) return;
                            const language = match[1];
                            if (select.value === 'custom') {
                                toggleCustomContentType(select, language);
                            }
                        }
                    });
                }, 100);
            });

            // Character Counter Functions
            function updateCharacterCounter(inputElement, language, fieldType) {
                if (!inputElement) return;

                const length = inputElement.value.length;
                const maxLength = fieldType === 'title' ? 60 : 160;
                const counterId = `${fieldType}_counter_${language}`;
                const counter = document.getElementById(counterId);

                if (counter) {
                    const small = counter.querySelector('small');
                    if (small) {
                        small.textContent = `${length}/${maxLength}`;

                        // Color coding
                        if (length > maxLength) {
                            small.className = 'text-danger';
                        } else if (length >= maxLength * 0.9) {
                            small.className = 'text-warning';
                        } else if (length >= maxLength * 0.7) {
                            small.className = 'text-success';
                        } else {
                            small.className = '';
                        }
                    }
                }
            }

            // Initialize character counters
            function initializeCharacterCounters() {
                const visibleContent = document.querySelector(
                    '.seo-language-content[style*="display: block"], .seo-language-content[style=""], .seo-language-content:not([style*="display: none"])'
                    );
                if (!visibleContent) return;

                const language = visibleContent.getAttribute('data-language');

                // Title input
                const titleInput = visibleContent.querySelector(`input[wire\\:model*="seo_title"]`);
                if (titleInput) {
                    updateCharacterCounter(titleInput, language, 'title');
                    titleInput.addEventListener('input', () => updateCharacterCounter(titleInput, language, 'title'));
                }

                // Description textarea
                const descInput = visibleContent.querySelector(`textarea[wire\\:model*="seo_description"]`);
                if (descInput) {
                    updateCharacterCounter(descInput, language, 'description');
                    descInput.addEventListener('input', () => updateCharacterCounter(descInput, language, 'description'));
                }
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initializeCharacterCounters, 100);
            });

            // Re-initialize on language change
            document.addEventListener('livewire:navigated', function() {
                setTimeout(initializeCharacterCounters, 200);
            });

            // Listen for language switch events
            if (typeof window.addEventListener !== 'undefined') {
                window.addEventListener('seo-language-changed', function(event) {
                    setTimeout(initializeCharacterCounters, 100);
                });
            }

            // File Upload Success Handler (for future expansion)
            document.addEventListener('livewire:load', function() {
                // Listen for successful file uploads
                Livewire.on('seoImageUploaded', function(data) {
                    console.log('📷 SEO image uploaded successfully:', data);

                    // Could show success notification here
                    // Toast.success(`${data.type} resmi başarıyla yüklendi!`);
                });
            });

            // Fill SEO Title Function
            function fillSeoTitle(language, titleText) {
                // Find the active language content
                const visibleContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);
                if (!visibleContent) {
                    console.warn('Language content not found for:', language);
                    return;
                }

                // Find the title input
                const titleInput = visibleContent.querySelector(`input[wire\\:model*="seo_title"]`);
                if (!titleInput) {
                    console.warn('Title input not found for language:', language);
                    return;
                }

                // Fill the input
                titleInput.value = titleText;

                // Trigger input event to update Livewire
                titleInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));

                // Update character counter
                updateCharacterCounter(titleInput, language, 'title');

                // Scroll to the input with smooth animation
                titleInput.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Add visual feedback - highlight the input briefly
                titleInput.style.transition = 'all 0.3s ease';
                titleInput.style.background = '#e3f2fd';
                titleInput.style.borderColor = '#2196f3';

                setTimeout(() => {
                    titleInput.style.background = '';
                    titleInput.style.borderColor = '';
                }, 1500);

                // Show success feedback
                const button = event.target.closest('button');
                if (button) {
                    const icon = button.querySelector('i');
                    const originalClass = icon.className;

                    icon.className = 'fas fa-check text-success';

                    setTimeout(() => {
                        icon.className = originalClass;
                    }, 1000);
                }

                console.log('SEO title filled:', titleText, 'for language:', language);
            }
        </script>


        </script>

        {{-- AI SEO Integration JavaScript --}}
        <script src="{{ asset('assets/js/ai-seo-integration.js') }}"></script>
    @endif

</div>
