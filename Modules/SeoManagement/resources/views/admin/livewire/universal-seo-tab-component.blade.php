{{--
    UNIVERSAL SEO TAB COMPONENT BLADE VIEW
    Pattern: A1 CMS Universal System

    Tüm modüller için ortak SEO Tab view'ı
    Polymorphic relationship ile çalışır
--}}

{{-- UNIVERSAL SEO TAB - Temiz ve basit --}}
<div>
    @foreach ($availableLanguages as $lang)
        <div class="seo-language-content"
             data-language="{{ $lang }}"
             style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

            {{-- AI SEO TOOLBAR - A1 PATTERN --}}
            @php
                // A1 pattern: Check if analysis data exists
                $hasStaticAnalysis = isset($staticAiAnalysis[$lang]) && !empty($staticAiAnalysis[$lang]);
                $hasDynamicAnalysis = isset($dynamicAiAnalysis[$lang]) && !empty($dynamicAiAnalysis[$lang]);
                $analysisButtonHasData = $hasStaticAnalysis || $hasDynamicAnalysis;
            @endphp

            @if ($lang === get_tenant_default_locale())
            <div class="ai-seo-toolbar mb-4">
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button type="button" class="btn btn-primary ai-seo-recommendations-btn"
                        data-seo-feature="seo-smart-recommendations" data-language="{{ $lang }}"
                        style="z-index: 9999; position: relative;">
                        <i class="fas fa-magic me-1"></i>
                        {{ __('seomanagement::admin.ai_recommendations') }}
                    </button>
                </div>
            </div>
            @endif

            {{-- AI RECOMMENDATIONS LOADING --}}
            @if (isset($recommendationLoaders[$lang]) && $recommendationLoaders[$lang])
                <div class="text-center p-4 mb-4 bg-light rounded">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">{{ __('seomanagement::admin.ai_recommendations_generating') }}</span>
                    </div>
                    <h5 class="mt-3 mb-1">🤖 {{ __('seomanagement::admin.ai_recommendations_preparing') }}</h5>
                    <p class="text-muted">{{ __('seomanagement::admin.ai_recommendations_analyzing') }}</p>
                </div>
            @else
                {{-- AI RECOMMENDATIONS RESULTS --}}
                @php
                    // FIXED: Get recommendations from database instead of empty props
                    $currentRecommendations = [];

                    // UNIVERSAL: Try to get from current model's seoSetting (works for all modules)
                    if (!empty($this->modelId) && !empty($this->modelClass)) {
                        try {
                            // QUICK FIX: Direct database query (universal approach)
                            $seoSetting = \Modules\SeoManagement\app\Models\SeoSetting::where('seoable_type', $this->modelClass)
                                ->where('seoable_id', $this->modelId)
                                ->first();

                            if ($seoSetting && !empty($seoSetting->ai_suggestions)) {
                                $allSuggestions = is_string($seoSetting->ai_suggestions)
                                    ? json_decode($seoSetting->ai_suggestions, true)
                                    : $seoSetting->ai_suggestions;

                                // Multi-language fallback strategy
                                if (isset($allSuggestions[$lang])) {
                                    $currentRecommendations = $allSuggestions[$lang];
                                } elseif (isset($allSuggestions[get_tenant_default_locale()])) {
                                    $currentRecommendations = $allSuggestions[get_tenant_default_locale()];
                                } elseif (!empty($allSuggestions)) {
                                    $currentRecommendations = reset($allSuggestions);
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to load AI suggestions: ' . $e->getMessage());
                        }
                    }

                    // Fallback to dynamic recommendations if database is empty
                    if (empty($currentRecommendations)) {
                        $currentRecommendations = isset($dynamicAiRecommendations[$lang]) && !empty($dynamicAiRecommendations[$lang])
                            ? $dynamicAiRecommendations[$lang]
                            : (isset($staticAiRecommendations[$lang]) && !empty($staticAiRecommendations[$lang]) ? $staticAiRecommendations[$lang] : []);
                    }
                @endphp

                @if (!empty($currentRecommendations) && $lang === get_tenant_default_locale())
                    {{-- A1 PATTERN: ADVANCED RECOMMENDATIONS LIST --}}
                        @php
                            // FIXED: Support both data structures
                            $recommendationsData = $currentRecommendations['data']['recommendations'] ?? $currentRecommendations['recommendations'] ?? [];
                        @endphp
                        @if (!empty($recommendationsData) && is_array($recommendationsData))
                            <div class="mt-3">
                                @php
                                    // A1 pattern: SEO ve Social medya önerilerini ayır
                                    $seoRecs = collect($recommendationsData)->filter(function ($rec) {
                                        return in_array($rec['type'] ?? '', ['title', 'description']);
                                    });
                                    $socialRecs = collect($recommendationsData)->filter(function ($rec) {
                                        return in_array($rec['type'] ?? '', ['og_title', 'og_description']);
                                    });
                                @endphp

                                {{-- SEO RECOMMENDATIONS --}}
                                @if ($seoRecs->count() > 0)
                                    <div class="row mb-4">
                                        @foreach ($seoRecs as $index => $rec)
                                            <div class="col-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            @if(($rec['type'] ?? '') === 'title')
                                                                SEO Başlığı
                                                            @elseif(($rec['type'] ?? '') === 'description')
                                                                SEO Açıklaması
                                                            @else
                                                                {{ $rec['title'] ?? 'SEO Önerisi' }}
                                                            @endif
                                                        </h3>
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

                                {{-- SOCIAL MEDIA RECOMMENDATIONS --}}
                                @if ($socialRecs->count() > 0)
                                    <div class="row mb-4">
                                        @foreach ($socialRecs as $index => $rec)
                                            <div class="col-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h3 class="card-title">
                                                            @if(($rec['type'] ?? '') === 'og_title')
                                                                Sosyal Medya Başlığı
                                                            @elseif(($rec['type'] ?? '') === 'og_description')
                                                                Sosyal Medya Açıklaması
                                                            @else
                                                                {{ $rec['title'] ?? 'Sosyal Medya Önerisi' }}
                                                            @endif
                                                        </h3>
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

                                {{-- FALLBACK: Basic recommendations for other types --}}
                                @if ($seoRecs->count() === 0 && $socialRecs->count() === 0)
                                    @foreach ($recommendationsData as $recommendation)
                                        <div class="d-flex align-items-start mb-3 p-2 border rounded">
                                            <div class="me-2">
                                                <span class="badge bg-primary rounded-pill">{{ $loop->iteration }}</span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $recommendation['title'] ?? '' }}</h6>
                                                <p class="mb-0 text-muted">{{ $recommendation['description'] ?? '' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                @endif
            @endif

            {{-- TEMEL SEO BİLGİLERİ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-search me-2"></i>
                        Temel SEO Bilgileri
                        <small class="ms-2">Mutlaka doldurulması gerekenler</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        {{-- SEO Title --}}
                        <div class="col-12 col-md-6">
                            <div class="position-relative mb-3 mb-md-0">
                                <div class="form-floating">
                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.seo_title"
                                        class="form-control seo-no-enter" placeholder="SEO Başlık"
                                        maxlength="60" oninput="updateCharCounter(this, '{{ $lang }}', 'title')">
                                    <label>SEO Başlık ({{ strtoupper($lang) }})</label>
                                    <span class="character-counter position-absolute"
                                        id="title_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5;">
                                        <small>0/60</small>
                                    </span>
                                    <div class="form-text">
                                        <small>Arama motorlarında görünecek başlık</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO Description --}}
                        <div class="col-12 col-md-6">
                            <div class="position-relative">
                                <div class="form-floating">
                                    <textarea wire:model="seoDataCache.{{ $lang }}.seo_description"
                                        class="form-control seo-no-enter" placeholder="SEO Açıklama"
                                        style="height: 100px; resize: vertical;" maxlength="160"
                                        oninput="updateCharCounter(this, '{{ $lang }}', 'description')"></textarea>
                                    <label>SEO Açıklama ({{ strtoupper($lang) }})</label>
                                    <span class="character-counter position-absolute"
                                        id="description_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5;">
                                        <small>0/160</small>
                                    </span>
                                    <div class="form-text">
                                        <small>Arama sonuçlarında görünecek açıklama</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mb-4">
                        {{-- Schema.org Article Type (2025 SEO Standard - Rich Results) --}}
                        <div class="col-12 col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <select wire:model="seoDataCache.{{ $lang }}.schema_type"
                                    class="form-select" id="schema_type_{{ $lang }}">
                                    <option value="WebPage" selected>🌐 WebPage (Varsayılan - Genel Sayfa)</option>
                                    <option value="Article">📰 Article (Blog/Haber İçeriği)</option>
                                    <option value="BlogPosting">✍️ BlogPosting (Blog Yazısı)</option>
                                    <option value="NewsArticle">📢 NewsArticle (Haber Makalesi)</option>
                                    <option value="FAQPage">❓ FAQPage (Sık Sorulan Sorular)</option>
                                    <option value="HowTo">🔧 HowTo (Nasıl Yapılır Rehberi)</option>
                                    <option value="Product">🛍️ Product (Ürün Sayfası)</option>
                                    <option value="Service">🛠️ Service (Hizmet Sayfası)</option>
                                    <option value="AboutPage">ℹ️ AboutPage (Hakkımızda)</option>
                                    <option value="ContactPage">📞 ContactPage (İletişim)</option>
                                </select>
                                <label>Schema.org Sayfa Tipi ({{ strtoupper($lang) }})</label>
                                <div class="form-text">
                                    <small><i class="fas fa-code me-1"></i>Google Rich Results için sayfa tipini seçin</small>
                                </div>
                            </div>
                        </div>

                        {{-- Priority Score --}}
                        <div class="col-12 col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0">SEO Önceliği</label>
                                <span class="badge bg-warning priority-badge" id="priority_badge_{{ $lang }}">
                                    <span class="priority-value">5</span>/10 - <span class="priority-text">Orta</span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold">1</span>
                                <span class="small">Düşük</span>
                                <input type="range" wire:model="seoDataCache.{{ $lang }}.priority_score"
                                    class="form-range flex-grow-1 mx-2" min="1" max="10" step="1"
                                    value="5" oninput="updatePriorityDisplay(this, '{{ $lang }}')">
                                <span class="small">Kritik</span>
                                <span class="small fw-bold">10</span>
                            </div>
                            <div class="form-text mt-2">
                                <small>
                                    <strong>1-3:</strong> Blog yazıları • <strong>4-6:</strong> Ürün sayfaları •
                                    <strong>7-8:</strong> Önemli kategoriler • <strong>9-10:</strong> Ana sayfa
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SOSYAL MEDYA BİLGİLERİ --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fa-brands fa-facebook me-2"></i>
                        Sosyal Medya Bilgileri
                        <small class="ms-2">Facebook, LinkedIn, Twitter paylaşımları için</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        {{-- Özelleştirme Switch --}}
                        <div class="col-12 col-md-6">
                            <div class="mt-3 mb-3 mb-lg-0">
                                @if(isset($seoDataCache[$lang]))
                                    @php
                                        // Sosyal medya alanları doluysa otomatik checked
                                        $ogTitle = $seoDataCache[$lang]['og_title'] ?? '';
                                        $ogDescription = $seoDataCache[$lang]['og_description'] ?? '';
                                        $autoChecked = !empty(trim($ogTitle)) || !empty(trim($ogDescription));
                                    @endphp
                                    <div class="pretty p-switch p-fill">
                                        <input type="checkbox"
                                            wire:model.defer="seoDataCache.{{ $lang }}.og_custom_enabled"
                                            id="og_custom_{{ $lang }}"
                                            onchange="toggleOgCustomFields(this, '{{ $lang }}');"
                                            {{ $autoChecked ? 'checked' : '' }}>
                                        <div class="state">
                                            <label style="margin-left: 10px;">Özel sosyal medya ayarlarını kullan</label>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <small class="text-muted">Kapalıysa yukarıdaki SEO bilgileri kullanılır</small>
                                    </div>
                                @else
                                    <div class="pretty p-switch p-fill">
                                        <input type="checkbox"
                                            wire:model.defer="seoDataCache.{{ $lang }}.og_custom_enabled"
                                            id="og_custom_{{ $lang }}"
                                            onchange="toggleOgCustomFields(this, '{{ $lang }}');">
                                        <div class="state">
                                            <label style="margin-left: 10px;">Özel sosyal medya ayarlarını kullan</label>
                                        </div>
                                    </div>
                                    <div class="form-text">
                                        <small class="text-muted">Kapalıysa yukarıdaki SEO bilgileri kullanılır</small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- OG Image Media Manager --}}
                        <div class="col-12 col-md-6">
                            @if($lang === get_tenant_default_locale())
                                <livewire:mediamanagement.universal-media
                                    :model-id="$modelId"
                                    :model-type="$modelType"
                                    :model-class="$modelClass"
                                    :collections="['seo_og_image']"
                                    :sortable="false"
                                    :set-featured-from-gallery="false"
                                    :key="'seo-og-image-' . ($modelId ?? 'new')"
                                />
                            @else
                                <div class="alert alert-info py-2 px-3 mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Sosyal medya görseli tüm diller için ortaktır ve varsayılan dil üzerinden yönetilir.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- OG Custom Fields - Düz çizgi kaldırıldı --}}
                    @php
                        $shouldShowOgFields = isset($seoDataCache[$lang]) &&
                                            (!empty(trim($seoDataCache[$lang]['og_title'] ?? '')) ||
                                             !empty(trim($seoDataCache[$lang]['og_description'] ?? '')));
                    @endphp
                    <div class="og-custom-fields" id="og_custom_fields_{{ $lang }}" style="display: {{ $shouldShowOgFields ? 'block' : 'none' }}; margin-top: 1rem;">
                        <div class="row mb-4">
                            {{-- OG Title --}}
                            <div class="col-12 col-md-6">
                                <div class="form-floating position-relative mb-3 mb-md-0">
                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.og_title"
                                        class="form-control seo-no-enter" placeholder="Facebook/LinkedIn'de görünecek özel başlık"
                                        maxlength="60" oninput="updateCharCounter(this, '{{ $lang }}', 'og_title')">
                                    <label>
                                        Sosyal Medya Başlığı
                                        <small class="ms-2">Maksimum 60 karakter</small>
                                    </label>
                                    <span class="character-counter position-absolute"
                                        id="og_title_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5;">
                                        <small>0/60</small>
                                    </span>
                                    <div class="form-text">
                                        <small>Facebook/LinkedIn'de görünecek başlık</small>
                                    </div>
                                </div>
                            </div>

                            {{-- OG Description --}}
                            <div class="col-12 col-md-6">
                                <div class="form-floating position-relative">
                                    <textarea wire:model="seoDataCache.{{ $lang }}.og_description"
                                        class="form-control seo-no-enter" placeholder="Facebook/LinkedIn'de görünecek özel açıklama"
                                        style="height: 100px; resize: vertical;" maxlength="155"
                                        oninput="updateCharCounter(this, '{{ $lang }}', 'og_description')"></textarea>
                                    <label>
                                        Sosyal Medya Açıklaması
                                        <small class="ms-2">Maksimum 155 karakter</small>
                                    </label>
                                    <span class="character-counter position-absolute"
                                        id="og_description_counter_{{ $lang }}"
                                        style="top: 8px; right: 12px; z-index: 5;">
                                        <small>0/155</small>
                                    </span>
                                    <div class="form-text">
                                        <small>Sosyal medya paylaşımlarında görünecek açıklama</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ROBOTS META CONTROLS (2025 SEO Best Practice) --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        Arama Motoru Direktifleri
                        <small class="ms-2">Google bot kontrolleri</small>
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        // Robots meta verilerini al - varsayılanlar true
                        $robotsMeta = $seoDataCache[$lang]['robots_meta'] ?? [
                            'index' => true,
                            'follow' => true,
                            'archive' => true,
                            'snippet' => true,
                        ];
                    @endphp

                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="robots_index_{{ $lang }}"
                                    wire:model="seoDataCache.{{ $lang }}.robots_meta.index"
                                    @if($robotsMeta['index'] ?? true) checked @endif>
                                <div class="state p-success p-on ms-2">
                                    <label>Index</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>NoIndex</label>
                                </div>
                            </div>
                            <div class="form-text mt-2"><small>Arama motorlarında göster</small></div>
                        </div>
                        <div class="col-md-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="robots_follow_{{ $lang }}"
                                    wire:model="seoDataCache.{{ $lang }}.robots_meta.follow"
                                    @if($robotsMeta['follow'] ?? true) checked @endif>
                                <div class="state p-success p-on ms-2">
                                    <label>Follow</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>NoFollow</label>
                                </div>
                            </div>
                            <div class="form-text mt-2"><small>Linkleri takip et</small></div>
                        </div>
                        <div class="col-md-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="robots_archive_{{ $lang }}"
                                    wire:model="seoDataCache.{{ $lang }}.robots_meta.archive"
                                    @if($robotsMeta['archive'] ?? true) checked @endif>
                                <div class="state p-success p-on ms-2">
                                    <label>Archive</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>NoArchive</label>
                                </div>
                            </div>
                            <div class="form-text mt-2"><small>Arşivlenebilir</small></div>
                        </div>
                        <div class="col-md-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="robots_snippet_{{ $lang }}"
                                    wire:model="seoDataCache.{{ $lang }}.robots_meta.snippet"
                                    @if($robotsMeta['snippet'] ?? true) checked @endif>
                                <div class="state p-success p-on ms-2">
                                    <label>Snippet</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>NoSnippet</label>
                                </div>
                            </div>
                            <div class="form-text mt-2"><small>Meta description göster</small></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PUBLISHER BİLGİLERİ - SADECE DEFAULT LANGUAGE --}}
            @if ($lang === get_tenant_default_locale())
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Publisher Bilgileri
                        <small class="ms-2">Tek yazar - tüm diller için aynı</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        {{-- Author Name --}}
                        <div class="col-12 col-md-6">
                            <div class="form-floating mb-3 mb-md-0">
                                <input type="text" wire:model="seoDataCache.{{ $lang }}.author_name"
                                    class="form-control" placeholder="Yazar adı">
                                <label>Yazar Adı</label>
                                <div class="form-text">
                                    <small>İçeriği yazan kişinin adı (tüm diller için aynı)</small>
                                </div>
                            </div>
                        </div>

                        {{-- Author URL --}}
                        <div class="col-12 col-md-6">
                            <div class="form-floating">
                                <input type="url" wire:model="seoDataCache.{{ $lang }}.author_url"
                                    class="form-control" placeholder="https://example.com">
                                <label>Yazar Web Sitesi</label>
                                <div class="form-text">
                                    <small>Yazarın kişisel web sitesi veya profil sayfası</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    (function() {
        const modelClass = @json($this->modelClass ?? null);
        const modelType = @json($this->modelType ?? null);
        const modelId = @json($this->modelId ?? null);

        if (modelClass && (!window.currentModelClass || window.currentModelClass !== modelClass)) {
            window.currentModelClass = modelClass;
        }

        if (modelType) {
            if (!window.currentModelType) {
                window.currentModelType = modelType;
            }

            if (!window.currentModuleName) {
                window.currentModuleName = modelType;
            }
        }

        if (modelId && !window.currentModelId) {
            window.currentModelId = modelId;
        }
    })();

    // Social media switch'lerini AI önerilerinden sonra kontrol et
    document.addEventListener('DOMContentLoaded', function() {
        checkSocialMediaSwitches();
        syncRobotsMetaPrettyCheckboxes();
    });

    // Livewire morph sonrası kontrol - State'i koru
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('element.updated', (el, component) => {
            setTimeout(() => {
                checkSocialMediaSwitches();
                preserveOgFieldsState();
                syncRobotsMetaPrettyCheckboxes();
            }, 50);
        });
    }

    // Robots Meta Pretty Checkbox'larını Livewire data ile senkronize et
    function syncRobotsMetaPrettyCheckboxes() {
        const languages = {!! json_encode($availableLanguages) !!};
        languages.forEach(lang => {
            // Livewire component data'sını al
            if (typeof @this !== 'undefined' && @this.seoDataCache && @this.seoDataCache[lang]) {
                const robotsMeta = @this.seoDataCache[lang].robots_meta || {};

                // Her robots meta checkbox'ını güncelle
                ['index', 'follow', 'archive', 'snippet'].forEach(type => {
                    const checkbox = document.getElementById(`robots_${type}_${lang}`);
                    if (checkbox && typeof robotsMeta[type] !== 'undefined') {
                        checkbox.checked = robotsMeta[type];
                    }
                });
            }
        });
    }

    function checkSocialMediaSwitches() {
        const languages = {!! json_encode($availableLanguages) !!};
        languages.forEach(lang => {
            // Checkbox'ı kontrol et
            let checkbox = document.getElementById(`og_custom_${lang}`);
            const ogFields = document.getElementById(`og_custom_fields_${lang}`);

            // OG alanlarını kontrol et
            const ogTitleInput = document.querySelector(`[wire\\:model="seoDataCache.${lang}.og_title"]`);
            const ogDescInput = document.querySelector(`[wire\\:model="seoDataCache.${lang}.og_description"]`);

            // Değerler varsa ve checkbox varsa işaretle
            if ((ogTitleInput?.value || ogDescInput?.value) && checkbox) {
                checkbox.checked = true;
                if (ogFields) {
                    ogFields.style.display = 'block';
                }
            }
        });
    }

    // Livewire update sonrası OG fields state'ini koru
    function preserveOgFieldsState() {
        const languages = {!! json_encode($availableLanguages) !!};
        languages.forEach(lang => {
            const checkbox = document.getElementById(`og_custom_${lang}`);
            const ogFields = document.getElementById(`og_custom_fields_${lang}`);

            if (checkbox && ogFields) {
                // Checkbox checked ise fields'i görünür tut
                if (checkbox.checked) {
                    ogFields.style.display = 'block';
                }
            }
        });
    }

    // 🔍 DEBUG: SEO DATA CACHE CONSOLE
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔍 UNIVERSAL SEO TAB - DEBUG START');

        // Tüm wire:model="seoDataCache.*" inputları bul
        const seoInputs = document.querySelectorAll('[wire\\:model^="seoDataCache"]');
        console.log('📊 SEO Input sayısı:', seoInputs.length);

        seoInputs.forEach((input, index) => {
            const wireModel = input.getAttribute('wire:model');
            const value = input.value || input.textContent || '';

            console.log(`  SEO Input ${index}:`, {
                wireModel: wireModel,
                value: value ? value.substring(0, 50) + '...' : '🔴 BOŞ',
                hasValue: !!value,
                id: input.id || 'no-id',
                tag: input.tagName
            });
        });

        // Livewire component data'sını kontrol et
        const seoComponent = @this;
        if (seoComponent) {
            console.log('✅ Livewire Component bulundu');
            console.log('📦 seoDataCache:', seoComponent.seoDataCache);
            console.log('🌍 currentLanguage:', seoComponent.currentLanguage);
            console.log('📋 availableLanguages:', seoComponent.availableLanguages);
            console.log('🆔 modelId:', seoComponent.modelId);
            console.log('🏷️ modelType:', seoComponent.modelType);
            console.log('📦 modelClass:', seoComponent.modelClass);

            // Global debug fonksiyonu
            window.debugSeoTab = function() {
                console.log('🔍 SEO TAB DEBUG:');
                console.log('  seoDataCache:', seoComponent.seoDataCache);
                console.log('  currentLanguage:', seoComponent.currentLanguage);
                return seoComponent.seoDataCache;
            };

            console.log('💡 TIP: Konsola "debugSeoTab()" yaz');
        } else {
            console.log('❌ Livewire Component bulunamadı!');
        }

        console.log('🔍 UNIVERSAL SEO TAB - DEBUG END');
    });
</script>
@endpush
