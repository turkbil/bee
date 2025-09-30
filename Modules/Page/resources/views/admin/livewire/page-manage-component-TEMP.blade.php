<div>
    @php
        View::share('pretitle', $pageId ? 'Sayfa Düzenleme' : 'Yeni Sayfa Ekleme');
    @endphp

    @include('page::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="page_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $pageId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}"
                            target="_blank" class="btn btn-outline-primary" style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('page::admin.studio.editor') }}
                        </a>
                    </li>
                @endif

                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>

            <div class="card-body">
                <div class="tab-content" id="contentTabContent">

                    <!-- TEMEL BİLGİLER TAB -->
                    <div class="tab-pane fade" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                // Tenant languages'den dil ismini al
                                $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where(
                                    'is_active',
                                    true,
                                )->get();
                                $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('page::admin.title_field') }}">
                                            <label>
                                                {{ __('page::admin.title_field') }}
                                                @if ($lang === get_tenant_default_locale())
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug" maxlength="255"
                                                placeholder="sayfa-url-slug">
                                            <label>
                                                {{ __('admin.page_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- İçerik editörü - AI button artık global component'te --}}
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('page::admin.content'),
                                    'placeholder' => __('page::admin.content_placeholder'),
                                    ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('page::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('page::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB -->
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        <style>
                            /* A1 PATTERN STYLES */
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
                            /* ORIGINAL STYLES */
                            .character-counter {
                                font-size: 11px;
                                color: #6c757d;
                            }
                            .priority-badge {
                                transition: all 0.3s ease;
                            }
                            .seo-no-enter {
                                /* Custom styling for SEO fields */
                            }
                        </style>

                        @foreach ($availableLanguages as $lang)
                            <div class="seo-language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

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
                                        {{-- AI SEO ANALYSIS BUTTON REMOVED --}}
                                        <button type="button" class="btn btn-primary ai-seo-recommendations-btn"
                                            data-seo-feature="seo-smart-recommendations" data-language="{{ $lang }}"
                                            style="z-index: 9999; position: relative;">
                                            <i class="fas fa-magic me-1"></i>
                                            AI Önerileri
                                        </button>
                                    </div>
                                </div>
                                @endif

                                {{-- TEMEL SEO BİLGİLERİ --}}
                                                            @if (isset($dynamicAiAnalysis[$lang]) && !empty($dynamicAiAnalysis[$lang]))
                                                                <span class="badge bg-primary ms-2">🤖 AI Güncel</span>
                                                            @elseif(isset($staticAiAnalysis[$lang]) && !empty($staticAiAnalysis[$lang]))
                                                                <span class="badge bg-secondary ms-2">📊 Kaydedilmiş</span>
                                                            @endif
                                                        </h3>
                                                        @if (isset($currentAnalysis['timestamp']))
                                                            <small class="text-muted">
                                                                {{ \Carbon\Carbon::parse($currentAnalysis['timestamp'])->diffForHumans() }}
                                                            </small>
                                                        @elseif(isset($staticAiAnalysis[$lang]) && !empty($staticAiAnalysis[$lang]))
                                                            @php
                                                                // Try to get timestamp from SeoSetting
                                                                $seoSetting = \Modules\SeoManagement\app\Models\SeoSetting::where('model_type', 'page')
                                                                    ->where('model_id', $pageId ?? 0)
                                                                    ->first();
                                                            @endphp
                                                            @if($seoSetting && $seoSetting->analysis_date)
                                                                <small class="text-muted">
                                                                    {{ \Carbon\Carbon::parse($seoSetting->analysis_date)->diffForHumans() }}
                                                                </small>
                                                            @endif
                                                        @endif
                                                    </div>

                                                    {{-- A1 PATTERN: GENEL DURUM ÖZETİ --}}
                                                    @php
                                                        // A1 PATTERN: Extract scores with multiple fallback paths
                                                        $overallScore = $currentAnalysis['metrics']['overall_score'] ?? $currentAnalysis['overall_score'] ?? 0;

                                                        // Multiple fallback for detailed scores
                                                        $detailedScores = $currentAnalysis['detailed_scores'] ?? [];
                                                        $titleScore = $detailedScores['title']['score'] ?? $detailedScores['title']['breakdown']['score'] ?? 0;
                                                        $descScore = $detailedScores['description']['score'] ?? $detailedScores['description']['breakdown']['score'] ?? 0;
                                                        $contentScore = $detailedScores['content']['score'] ?? $detailedScores['content']['breakdown']['score'] ?? 0;
                                                        $socialScore = $detailedScores['social']['score'] ?? $detailedScores['social']['breakdown']['score'] ?? 0;

                                                        // A1 PATTERN: Determine status colors
                                                        $overallStatus = $overallScore >= 80 ? 'success' : ($overallScore >= 60 ? 'warning' : 'danger');
                                                        $titleStatus = $titleScore >= 80 ? 'success' : ($titleScore >= 60 ? 'warning' : 'danger');
                                                        $descStatus = $descScore >= 80 ? 'success' : ($descScore >= 60 ? 'warning' : 'danger');
                                                        $contentStatus = $contentScore >= 80 ? 'success' : ($contentScore >= 60 ? 'warning' : 'danger');
                                                        $socialStatus = $socialScore >= 80 ? 'success' : ($socialScore >= 60 ? 'warning' : 'danger');

                                                        // A1 PATTERN: Multiple message fallbacks
                                                        $overallMessage = $currentAnalysis['metrics']['health_status'] ??
                                                                         ($overallScore >= 80 ? 'Mükemmel' :
                                                                         ($overallScore >= 60 ? 'İyi' : 'Geliştirilebilir'));
                                                    @endphp

                                                    <div class="row mb-4">
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <div class="avatar avatar-xl bg-{{ $overallStatus }} text-white mb-2">
                                                                    {{ $overallScore }}
                                                                </div>
                                                                <h5>Genel SEO Skoru</h5>
                                                                <p>{{ $overallMessage }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="row g-3">
                                                                <div class="col-md-3">
                                                                    <div class="card border-{{ $titleStatus }} hover-card">
                                                                        <div class="card-body text-center p-3">
                                                                            <i class="fas fa-heading fa-2x mb-2"></i>
                                                                            <h6>Meta Title</h6>
                                                                            <div class="progress mb-1">
                                                                                <div class="progress-bar bg-{{ $titleStatus }}"
                                                                                    style="width: {{ $titleScore }}%"></div>
                                                                            </div>
                                                                            <div>{{ $titleScore }}/100</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="card border-{{ $descStatus }} hover-card">
                                                                        <div class="card-body text-center p-3">
                                                                            <i class="fas fa-align-left fa-2x mb-2"></i>
                                                                            <h6>Meta Description</h6>
                                                                            <div class="progress mb-1">
                                                                                <div class="progress-bar bg-{{ $descStatus }}"
                                                                                    style="width: {{ $descScore }}%"></div>
                                                                            </div>
                                                                            <div>{{ $descScore }}/100</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="card border-{{ $contentStatus }} hover-card">
                                                                        <div class="card-body text-center p-3">
                                                                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                                            <h6>İçerik Kalitesi</h6>
                                                                            <div class="progress mb-1">
                                                                                <div class="progress-bar bg-{{ $contentStatus }}"
                                                                                    style="width: {{ $contentScore }}%"></div>
                                                                            </div>
                                                                            <div>{{ $contentScore }}/100</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="card border-{{ $socialStatus }} hover-card">
                                                                        <div class="card-body text-center p-3">
                                                                            <i class="fas fa-share-alt fa-2x mb-2"></i>
                                                                            <h6>Sosyal Medya</h6>
                                                                            <div class="progress mb-1">
                                                                                <div class="progress-bar bg-{{ $socialStatus }}"
                                                                                    style="width: {{ $socialScore }}%"></div>
                                                                            </div>
                                                                            <div>{{ $socialScore }}/100</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- A1 PATTERN: DETAYLI ANALİZ ACCORDION --}}
                                                    <div class="accordion mt-4" id="realTimeSeoAccordion">
                                                        {{-- 1. META ETİKET ANALİZİ --}}
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed position-relative" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#metaAnalysis"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-tags me-2"></i>
                                                                    Meta Etiket Analizi
                                                                    <span class="badge bg-{{ $titleStatus }} position-absolute"
                                                                        style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ round(($titleScore + $descScore) / 2) }}/100</span>
                                                                </button>
                                                            </h2>
                                                            <div id="metaAnalysis" class="accordion-collapse collapse"
                                                                data-bs-parent="#realTimeSeoAccordion">
                                                                <div class="accordion-body pt-4">
                                                                    @php
                                                                        // A1 EXACT PATTERN: Live data extraction from forms
                                                                        $pageTitle = '';
                                                                        $pageSlug = '';
                                                                        $pageBody = '';

                                                                        if (is_array($this->title ?? null)) {
                                                                            $pageTitle = collect($this->title)->get($lang, '');
                                                                        } else {
                                                                            $pageTitle = $this->title ?? '';
                                                                        }

                                                                        if (is_array($this->slug ?? null)) {
                                                                            $pageSlug = collect($this->slug)->get($lang, '');
                                                                        } else {
                                                                            $pageSlug = $this->slug ?? '';
                                                                        }

                                                                        if (is_array($this->body ?? null)) {
                                                                            $pageBody = collect($this->body)->get($lang, '');
                                                                        } else {
                                                                            $pageBody = $this->body ?? '';
                                                                        }

                                                                        // SEO Data from cache
                                                                        $metaTitle = $seoDataCache[$lang]['seo_title'] ?? '';
                                                                        $metaDescription = $seoDataCache[$lang]['seo_description'] ?? '';

                                                                        // A1 PATTERN: Real-time analysis
                                                                        $titleToAnalyze = !empty($metaTitle) ? $metaTitle : $pageTitle;
                                                                        $titleLength = strlen($titleToAnalyze);
                                                                        $descLength = strlen($metaDescription);
                                                                    @endphp

                                                                    <div class="mb-4">
                                                                        <h5 class="mb-3">Meta Title</h5>
                                                                        <div class="p-3 rounded border">
                                                                            @if (empty($titleToAnalyze))
                                                                                <p class="mb-1">Başlık bulunamadı</p>
                                                                            @else
                                                                                <p class="mb-1">"{{ $titleToAnalyze }}"</p>
                                                                            @endif
                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                <span>{{ $titleLength }} karakter</span>
                                                                                @if (!empty($metaTitle))
                                                                                    <span class="badge bg-success">Meta Title</span>
                                                                                @elseif(!empty($pageTitle))
                                                                                    <span class="badge bg-warning">Sayfa Başlığı</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        @if ($titleScore < 80)
                                                                            <p class="mb-0 mt-2">
                                                                                @if (empty($metaTitle))
                                                                                    SEO tab'ında meta title alanını doldurun
                                                                                @elseif($titleLength < 30)
                                                                                    Başlığı genişletin (en az 30 karakter)
                                                                                @elseif($titleLength > 60)
                                                                                    Başlığı kısaltın (maksimum 60 karakter)
                                                                                @endif
                                                                            </p>
                                                                        @endif
                                                                    </div>

                                                                    <div>
                                                                        <h5 class="mb-3">Meta Description</h5>
                                                                        <div class="p-3 rounded border">
                                                                            @if (empty($metaDescription))
                                                                                <p class="mb-1">Meta açıklama yok</p>
                                                                            @else
                                                                                <p class="mb-1">"{{ Str::limit($metaDescription, 100) }}"</p>
                                                                            @endif
                                                                            <span>{{ $descLength }} karakter</span>
                                                                        </div>
                                                                        @if ($descScore < 80)
                                                                            <p class="mb-0 mt-2">
                                                                                @if (empty($metaDescription))
                                                                                    SEO tab'ında meta açıklama alanını doldurun
                                                                                @elseif($descLength < 120)
                                                                                    Açıklamayı genişletin (120-160 karakter arası ideal)
                                                                                @elseif($descLength > 160)
                                                                                    Açıklamayı kısaltın (maksimum 160 karakter)
                                                                                @endif
                                                                            </p>
                                                                        @endif
                                                                    </div>

                                                                    {{-- AI STRENGTHS & IMPROVEMENTS ENTEGRASYONU --}}
                                                                    @if (!empty($currentAnalysis))
                                                                        @if (isset($currentAnalysis['strengths']) && is_array($currentAnalysis['strengths']) && count($currentAnalysis['strengths']) > 0)
                                                                            <div class="mt-4">
                                                                                <h5 class="mb-3 text-success">
                                                                                    <i class="fas fa-check-circle me-2"></i>
                                                                                    🤖 AI Güçlü Yönler
                                                                                </h5>
                                                                                <div class="p-3 rounded border border-success bg-success bg-opacity-10">
                                                                                    @foreach ($currentAnalysis['strengths'] as $strength)
                                                                                        <div class="d-flex align-items-center mb-2">
                                                                                            <i class="fas fa-check text-success me-2"></i>
                                                                                            <span>{{ $strength }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        @if (isset($currentAnalysis['improvements']) && is_array($currentAnalysis['improvements']) && count($currentAnalysis['improvements']) > 0)
                                                                            <div class="mt-4">
                                                                                <h5 class="mb-3 text-warning">
                                                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                                                    🤖 AI İyileştirme Önerileri
                                                                                </h5>
                                                                                <div class="p-3 rounded border border-warning bg-warning bg-opacity-10">
                                                                                    @foreach ($currentAnalysis['improvements'] as $improvement)
                                                                                        <div class="d-flex align-items-center mb-2">
                                                                                            <i class="fas fa-arrow-up text-warning me-2"></i>
                                                                                            <span>{{ $improvement }}</span>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- 2. İÇERİK KALİTE ANALİZİ - A1 EXACT PATTERN --}}
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed position-relative" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#contentQualityAnalysis"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-file-alt me-2"></i>
                                                                    İçerik Kalite Analizi
                                                                    <span class="badge bg-{{ $contentStatus }} position-absolute"
                                                                        style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ $contentScore }}/100</span>
                                                                </button>
                                                            </h2>
                                                            <div id="contentQualityAnalysis" class="accordion-collapse collapse"
                                                                data-bs-parent="#realTimeSeoAccordion">
                                                                <div class="accordion-body pt-4">
                                                                    @php
                                                                        // A1 EXACT PATTERN: Real-time content analysis
                                                                        $wordCount = str_word_count(strip_tags($pageBody));
                                                                        $charCount = strlen(strip_tags($pageBody));
                                                                        $hasH1 = strpos($pageBody, '<h1') !== false;
                                                                        $hasH2 = strpos($pageBody, '<h2') !== false;
                                                                        $linkCount = substr_count($pageBody, '<a');
                                                                    @endphp

                                                                    <div class="mb-4">
                                                                        <h5 class="mb-3">İçerik İstatistikleri</h5>
                                                                        <div class="row g-3">
                                                                            <div class="col-6">
                                                                                <div class="p-3 rounded border text-center hover-element">
                                                                                    <div class="h4 mb-1">{{ $wordCount }}</div>
                                                                                    <div>Kelime</div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <div class="p-3 rounded border text-center hover-element">
                                                                                    <div class="h4 mb-1">{{ $charCount }}</div>
                                                                                    <div>Karakter</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="mb-4">
                                                                        <h5 class="mb-3">Yapısal Öğeler</h5>
                                                                        <div class="list-group list-group-flush">
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <span>H1 Ana Başlık</span>
                                                                                @if ($hasH1)
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <span>H2 Alt Başlıklar</span>
                                                                                @if ($hasH2)
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <span>İç Linkler</span>
                                                                                <span class="badge bg-{{ $linkCount > 0 ? 'success' : 'secondary' }}">{{ $linkCount }} adet</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @if ($contentScore < 80)
                                                                        <div>
                                                                            <h5 class="mb-3">Öneriler</h5>
                                                                            <div class="p-3 rounded border">
                                                                                @if ($wordCount < 300)
                                                                                    <p class="mb-2">• İçeriği en az 300 kelimeye çıkarın (şu an: {{ $wordCount }})</p>
                                                                                @endif
                                                                                @if (!$hasH1)
                                                                                    <p class="mb-2">• Ana tab'ta başlık alanını doldurun veya içeriğe H1 ekleyin</p>
                                                                                @endif
                                                                                @if (!$hasH2)
                                                                                    <p class="mb-2">• İçeriğe 2-3 alt başlık (H2) ekleyin</p>
                                                                                @endif
                                                                                @if ($linkCount == 0)
                                                                                    <p class="mb-0">• İlgili sayfalara bağlantı ekleyin</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="p-3 rounded border text-center">
                                                                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                                                            <p class="text-success mb-0">İçerik yapısı uygun!</p>
                                                                        </div>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- 3. SOSYAL MEDYA HAZIRLIĞI - A1 EXACT PATTERN --}}
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header">
                                                                <button class="accordion-button collapsed position-relative" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#socialMediaAnalysis"
                                                                    aria-expanded="false">
                                                                    <i class="fas fa-share-alt me-2"></i>
                                                                    Sosyal Medya Hazırlığı
                                                                    <span class="badge bg-{{ $socialStatus }} position-absolute"
                                                                        style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ $socialScore }}/100</span>
                                                                </button>
                                                            </h2>
                                                            <div id="socialMediaAnalysis" class="accordion-collapse collapse"
                                                                data-bs-parent="#realTimeSeoAccordion">
                                                                <div class="accordion-body pt-4">
                                                                    @php
                                                                        // A1 EXACT PATTERN: SEO data social variables
                                                                        $ogTitle = $seoDataCache[$lang]['og_title'] ?? '';
                                                                        $ogDescription = $seoDataCache[$lang]['og_description'] ?? '';
                                                                        $ogImage = $seoDataCache[$lang]['og_image'] ?? '';
                                                                        $authorName = $seoDataCache[$lang]['author_name'] ?? '';
                                                                    @endphp

                                                                    <div class="mb-4">
                                                                        <h5 class="mb-3">OpenGraph Durumu</h5>
                                                                        <div class="list-group list-group-flush">
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <strong>og:title</strong>
                                                                                    <div>
                                                                                        {{ !empty($ogTitle) ? 'Özel başlık' : (!empty($metaTitle) ? 'Meta title kullanılıyor' : 'Yok') }}
                                                                                    </div>
                                                                                </div>
                                                                                @if (!empty($ogTitle) || !empty($metaTitle))
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <strong>og:description</strong>
                                                                                    <div>
                                                                                        {{ !empty($ogDescription) ? 'Özel açıklama' : (!empty($metaDescription) ? 'Meta description kullanılıyor' : 'Yok') }}
                                                                                    </div>
                                                                                </div>
                                                                                @if (!empty($ogDescription) || !empty($metaDescription))
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <strong>og:image</strong>
                                                                                    <div>1200x630px önerilen</div>
                                                                                </div>
                                                                                @if (!empty($ogImage))
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <strong>Yazar Bilgisi</strong>
                                                                                    <div>
                                                                                        {{ !empty($authorName) ? $authorName : 'Belirtilmemiş' }}
                                                                                    </div>
                                                                                </div>
                                                                                @if (!empty($authorName))
                                                                                    <span class="badge bg-success">Mevcut</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Yok</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    @if ($socialScore < 70)
                                                                        <div>
                                                                            <h5 class="mb-3">Öneriler</h5>
                                                                            <div class="p-3 rounded border">
                                                                                @if (empty($ogImage))
                                                                                    <p class="mb-2">• 1200x630px sosyal medya görseli ekleyin</p>
                                                                                @endif
                                                                                @if (empty($ogTitle) && empty($metaTitle))
                                                                                    <p class="mb-2">• Meta title veya OG title ekleyin</p>
                                                                                @endif
                                                                                @if (empty($ogDescription) && empty($metaDescription))
                                                                                    <p class="mb-2">• Meta description veya OG description ekleyin</p>
                                                                                @endif
                                                                                @if (empty($authorName))
                                                                                    <p class="mb-0">• SEO tab'ından yazar adı ekleyin</p>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="p-3 rounded border text-center">
                                                                            <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                                                            <p class="text-success mb-0">Sosyal medya paylaşıma hazır!</p>
                                                                        </div>
                                                                    @endif

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- AI ACTION ITEMS - A1 PATTERN --}}
                                                    @if (isset($currentAnalysis['action_items']) && is_array($currentAnalysis['action_items']) && count($currentAnalysis['action_items']) > 0)
                                                        <div class="mt-4">
                                                            <h6 class="mb-3">
                                                                <span class="badge bg-primary me-2">🤖 AI</span>
                                                                Öncelikli AI Önerileri
                                                            </h6>
                                                            @foreach ($currentAnalysis['action_items'] as $item)
                                                                <div class="d-flex align-items-start mb-3 p-2 border rounded">
                                                                    <div class="me-2">
                                                                        <span class="badge bg-warning rounded-pill">{{ $item['priority'] ?? $loop->iteration }}</span>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1">{{ $item['task'] ?? $item['title'] ?? '' }}</h6>
                                                                        <p class="mb-0 text-muted">{{ $item['area'] ?? $item['description'] ?? '' }}</p>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif

                                        {{-- AI RECOMMENDATIONS LOADING --}}
                                        @if (isset($recommendationLoaders[$lang]) && $recommendationLoaders[$lang])
                                            <div class="text-center p-4 mb-4 bg-light rounded">
                                                <div class="spinner-border text-success" role="status">
                                                    <span class="visually-hidden">AI önerileri üretiliyor...</span>
                                                </div>
                                                <h5 class="mt-3 mb-1">🤖 AI Önerileri Hazırlanıyor</h5>
                                                <p class="text-muted">Sayfanız analiz ediliyor ve özelleştirilmiş öneriler üretiliyor...</p>
                                            </div>
                                        @else
                                            {{-- AI RECOMMENDATIONS RESULTS --}}
                                            @php
                                                // FIXED: Get recommendations from database instead of empty props
                                                $currentRecommendations = [];

                                                // UNIVERSAL: Try to get from current model's seoSetting (works for all modules)
                                                if ($this->pageId) {
                                                    try {
                                                        // QUICK FIX: Direct database query (universal approach)
                                                        $seoSetting = \Modules\SeoManagement\app\Models\SeoSetting::where('seoable_type', 'Modules\Page\App\Models\Page')
                                                            ->where('seoable_id', $this->pageId)
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
                                                <div class="bg-light border p-3 rounded-3 mb-3">

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
                                                    @else
                                                        <div class="alert alert-info">
                                                            <i class="fas fa-info-circle me-2"></i>
                                                            AI önerileri bulunamadı. Yeni öneriler oluşturmak için "AI Önerileri" butonunu kullanın.

                                                            {{-- DEBUG --}}
                                                            <hr>
                                                            <small>
                                                                <strong>DEBUG:</strong><br>
                                                                currentRecommendations var mı: {{ !empty($currentRecommendations) ? 'EVET' : 'HAYIR' }}<br>
                                                                recommendationsData count: {{ count($recommendationsData ?? []) }}<br>
                                                                @if (!empty($currentRecommendations))
                                                                    Keys: {{ implode(', ', array_keys($currentRecommendations)) }}<br>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    </div>
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
                                        <div class="row">
                                            {{-- SEO Title --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="position-relative">
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
                                            <div class="col-md-6 mb-3">
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

                                            {{-- Focus Keywords --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.focus_keywords"
                                                        class="form-control" placeholder="Anahtar kelimeler">
                                                    <label>Focus Keywords ({{ strtoupper($lang) }})</label>
                                                    <div class="form-text">
                                                        <small>Virgülle ayırarak yazın (örn: web tasarım, seo, dijital pazarlama)</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Priority Score --}}
                                            <div class="col-md-6 mb-3">
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
                                            <i class="fab fa-facebook me-2"></i>
                                            Sosyal Medya Bilgileri
                                            <small class="ms-2">Facebook, LinkedIn, Twitter paylaşımları için</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Özelleştirme Switch --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="mt-3">
                                                    <div class="pretty p-switch">
                                                        @php
                                                            // Sosyal medya alanları doluysa otomatik checked
                                                            $ogTitle = $seoDataCache[$lang]['og_title'] ?? '';
                                                            $ogDescription = $seoDataCache[$lang]['og_description'] ?? '';
                                                            $autoChecked = !empty(trim($ogTitle)) || !empty(trim($ogDescription));
                                                        @endphp
                                                        <input type="checkbox"
                                                            wire:model.defer="seoDataCache.{{ $lang }}.og_custom_enabled"
                                                            id="og_custom_{{ $lang }}"
                                                            onchange="toggleOgCustomFields(this, '{{ $lang }}')"
                                                            {{ $autoChecked ? 'checked' : '' }}>
                                                        <div class="state">
                                                            <label for="og_custom_{{ $lang }}">Özel sosyal medya ayarlarını kullan</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-text">
                                                        <small>Kapalıysa yukarıdaki SEO bilgileri kullanılır</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- OG Image - Bağımsız (Her zaman görünür) --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    Sosyal Medya Resmi
                                                    <small class="ms-2">1200x630 önerilen</small>
                                                </label>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm flex-fill"
                                                        onclick="document.getElementById('og_image_file_{{ $lang }}').click()">
                                                        Resim Seç
                                                    </button>
                                                    <input type="url" wire:model="seoDataCache.{{ $lang }}.og_image_url"
                                                        class="form-control form-control-sm" placeholder="Veya URL girin"
                                                        style="flex: 2;">
                                                </div>
                                                <input type="file" id="og_image_file_{{ $lang }}"
                                                    class="d-none" accept="image/jpeg,image/jpg,image/png,image/webp">
                                                <div class="form-text">
                                                    <small>Önerilen boyut: 1200x630px (Facebook, LinkedIn için)</small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- OG Custom Fields --}}
                                        <div class="og-custom-fields" id="og_custom_fields_{{ $lang }}" style="display: {{ $autoChecked ? 'block' : 'none' }};">
                                            <hr class="my-3">
                                            <div class="row">
                                                {{-- OG Title --}}
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-floating position-relative">
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
                                                <div class="col-md-6 mb-3">
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

                                {{-- PUBLISHER BİLGİLERİ --}}
                                <div class="card mb-4">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-edit me-2"></i>
                                            Publisher Bilgileri
                                            <small class="ms-2">Yazar ve organizasyon bilgileri</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Author Name --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" wire:model="seoDataCache.{{ $lang }}.author_name"
                                                        class="form-control" placeholder="Yazar adı">
                                                    <label>Yazar Adı ({{ strtoupper($lang) }})</label>
                                                    <div class="form-text">
                                                        <small>İçeriği yazan kişinin adı</small>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Author URL --}}
                                            <div class="col-md-6 mb-3">
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
                            </div>
                        @endforeach
                    </div>

                    <!-- CODE TAB -->
                    <div class="tab-pane fade" id="2" role="tabpanel">
                        <!-- CSS Editor -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0 fw-bold">CSS</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-muted px-3 py-2" onclick="formatCssCode()" title="Format" onmouseover="this.className='btn btn-outline-primary px-3 py-2'" onmouseout="this.className='btn btn-outline-muted px-3 py-2'">
                                        <i class="fas fa-magic me-1"></i>Format
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="findInCss()" title="Ara" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-search me-1"></i>Ara
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleCssFold()" title="Katla/Aç" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-compress-alt me-1"></i>Katla
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleCssTheme()" title="Tema" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-palette me-1"></i>Tema
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleCssFullscreen()" title="Tam Ekran" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-expand me-1" id="css-fullscreen-icon"></i>Tam Ekran
                                    </button>
                                </div>
                            </div>
                            <div id="monaco-css-editor" style="height: 350px; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1px;"></div>
                            <textarea wire:model="inputs.css" id="css-textarea" style="display: none;">{{ $inputs['css'] ?? '' }}</textarea>
                        </div>

                        <!-- JavaScript Editor -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0 fw-bold">JavaScript</label>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-muted px-3 py-2" onclick="formatJsCode()" title="Format" onmouseover="this.className='btn btn-outline-primary px-3 py-2'" onmouseout="this.className='btn btn-outline-muted px-3 py-2'">
                                        <i class="fas fa-magic me-1"></i>Format
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="findInJs()" title="Ara" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-search me-1"></i>Ara
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleJsFold()" title="Katla/Aç" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-compress-alt me-1"></i>Katla
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleJsTheme()" title="Tema" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-palette me-1"></i>Tema
                                    </button>
                                    <button type="button" class="btn btn-outline-muted px-3 py-2 ms-1" onclick="toggleJsFullscreen()" title="Tam Ekran" onmouseover="this.className='btn btn-outline-primary px-3 py-2 ms-1'" onmouseout="this.className='btn btn-outline-muted px-3 py-2 ms-1'">
                                        <i class="fas fa-expand me-1" id="js-fullscreen-icon"></i>Tam Ekran
                                    </button>
                                </div>
                            </div>
                            <div id="monaco-js-editor" style="height: 350px; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1px;"></div>
                            <textarea wire:model="inputs.js" id="js-textarea" style="display: none;">{{ $inputs['js'] ?? '' }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.page" :model-id="$pageId" />

        </div>
    </form>

@push('scripts')
    <script>
        // 🔒 MONACO LOADER - AMD CONFLICT PREVENTION
        (function() {
            // AMD sistemini geçici olarak devre dışı bırak
            let originalDefine = window.define;
            let originalRequire = window.require;

            // Monaco loader'ı yükle
            const monacoScript = document.createElement('script');
            monacoScript.src = 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js';
            monacoScript.onload = function() {
                // Monaco loader yüklendikten sonra require'ı kullanılabilir hale getir
                window.monacoLoaderReady = true;
                console.log('✅ Monaco Editor loader yüklendi - AMD conflict önlendi');

                // Eğer Monaco editörleri initialize etme fonksiyonu bekliyorsa çağır
                if (typeof window.initializeMonacoEditorsWhenReady === 'function') {
                    window.initializeMonacoEditorsWhenReady();
                }
            };
            document.head.appendChild(monacoScript);
        })();
    </script>
    <script>
        window.currentPageId = {{ $jsVariables['currentPageId'] ?? 'null' }};
        window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

        let cssEditor, jsEditor;
        let cssTheme = 'vs-dark', jsTheme = 'vs-dark';
        let cssFoldState = false, jsFoldState = false;
        let cssFullscreen = false, jsFullscreen = false;

        // TinyMCE Content Update Helper Function
        window.updateTinyMCEContent = function(content, targetField = 'body') {
            try {
                const currentLang = window.currentLanguage || 'tr';
                const editorId = `multiLangInputs.${currentLang}.${targetField}`;

                console.log('🎯 updateTinyMCEContent çağırıldı:', {
                    editorId,
                    currentLang,
                    targetField,
                    contentLength: content ? content.length : 0
                });

                // HugeRTE/TinyMCE editor'ları tara
                if (typeof hugerte !== 'undefined') {
                    let targetEditor = null;

                    // Method 1: hugerte.editors array
                    if (hugerte.editors && Array.isArray(hugerte.editors)) {
                        targetEditor = hugerte.editors.find(ed =>
                            ed.id && (ed.id.includes(targetField) || ed.id.includes(currentLang))
                        );
                    }

                    // Method 2: hugerte.activeEditor
                    if (!targetEditor && hugerte.activeEditor) {
                        targetEditor = hugerte.activeEditor;
                    }

                    if (targetEditor && targetEditor.setContent) {
                        console.log('✅ HugeRTE editor bulundu:', targetEditor.id);
                        targetEditor.setContent(content);

                        // Livewire sync
                        const textareaElement = document.getElementById(targetEditor.id);
                        if (textareaElement) {
                            textareaElement.value = content;
                            textareaElement.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        console.log('✅ HugeRTE content güncellendi!');
                        return true;
                    }
                }

                // TinyMCE fallback
                if (typeof tinyMCE !== 'undefined' && tinyMCE.editors) {
                    const editorKeys = Object.keys(tinyMCE.editors);
                    const matchingKey = editorKeys.find(key =>
                        key.includes(targetField) || key.includes(currentLang)
                    );

                    if (matchingKey) {
                        const editor = tinyMCE.editors[matchingKey];
                        if (editor && editor.setContent) {
                            editor.setContent(content);
                            console.log('✅ TinyMCE content güncellendi!');
                            return true;
                        }
                    }
                }

                console.error('❌ Hiçbir editor bulunamadı');
                return false;
            } catch (e) {
                console.error('❌ updateTinyMCEContent error:', e);
                return false;
            }
        };

        // GLOBAL receiveGeneratedContent function
        if (typeof window.receiveGeneratedContent === 'undefined') {
            window.receiveGeneratedContent = function(content, targetField = 'body') {
                try {
                    console.log('🎯 AI Content received:', {
                        content: content ? content.substring(0, 100) + '...' : 'empty',
                        targetField
                    });

                    // ÖNCE TinyMCE editörünü direkt güncelle
                    window.updateTinyMCEContent(content, targetField);

                    // SONRA Livewire component'i güncelle
                    if (window.Livewire) {
                        if (window.Livewire.getByName) {
                            try {
                                const pageComponent = window.Livewire.getByName('page-manage-component')[0];
                                if (pageComponent && pageComponent.call) {
                                    console.log('✅ PageManageComponent bulundu, receiveGeneratedContent çağırılıyor...');
                                    pageComponent.call('receiveGeneratedContent', content, targetField);
                                    return;
                                }
                            } catch (e) {
                                console.warn('⚠️ Livewire method failed:', e);
                            }
                        }

                        console.error('❌ PageManageComponent bulunamadı');
                    } else {
                        console.error('❌ Livewire henüz yüklenmemiş');
                    }
                } catch (e) {
                    console.error('❌ receiveGeneratedContent error:', e);
                }
            };
            console.log('✅ Global receiveGeneratedContent function tanımlandı');
        }

        // SEO TAB JAVASCRIPT FUNCTIONS
        function updateCharCounter(input, language, type) {
            const value = input.value || '';
            const maxLength = input.getAttribute('maxlength') || 160;
            const counter = document.getElementById(`${type}_counter_${language}`);

            if (counter) {
                const remaining = maxLength - value.length;
                const small = counter.querySelector('small');
                if (small) {
                    small.textContent = `${value.length}/${maxLength}`;
                    small.style.color = remaining < 10 ? '#dc3545' : (remaining < 30 ? '#fd7e14' : '#6c757d');
                }
            }
        }

        function updatePriorityDisplay(rangeInput, language) {
            const value = parseInt(rangeInput.value);
            const badge = document.getElementById(`priority_badge_${language}`);

            if (badge) {
                const priorityValue = badge.querySelector('.priority-value');
                const priorityText = badge.querySelector('.priority-text');

                if (priorityValue && priorityText) {
                    priorityValue.textContent = value;

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

                    badge.className = badge.className.replace(/bg-(primary|secondary|success|danger|warning|info|light|dark)/g, '');
                    badge.classList.add(badgeClass);
                    priorityText.textContent = priorityLabel;
                }
            }
        }

        function toggleOgCustomFields(checkbox, language) {
            const customFields = document.getElementById(`og_custom_fields_${language}`);
            if (customFields) {
                // SÜPER HIZLI jQuery animasyon ile anında aç/kapat
                if (checkbox.checked) {
                    $(customFields).slideDown(200); // 200ms süper hızlı açılış
                } else {
                    $(customFields).slideUp(200); // 200ms süper hızlı kapanış
                }
            }
        }

        // Initialize social media switches based on content
        function initializeSocialMediaSwitches() {
            @foreach($availableLanguages as $lang)
                // {{ $lang }} dili için kontrol et
                const ogTitleInput_{{ $lang }} = document.querySelector(`[wire\\:model*="seoDataCache.{{ $lang }}.og_title"]`);
                const ogDescInput_{{ $lang }} = document.querySelector(`[wire\\:model*="seoDataCache.{{ $lang }}.og_description"]`);
                const switchElement_{{ $lang }} = document.getElementById('og_custom_{{ $lang }}');
                const customFields_{{ $lang }} = document.getElementById('og_custom_fields_{{ $lang }}');

                if (ogTitleInput_{{ $lang }} && ogDescInput_{{ $lang }} && switchElement_{{ $lang }} && customFields_{{ $lang }}) {
                    const hasOgTitle = ogTitleInput_{{ $lang }}.value && ogTitleInput_{{ $lang }}.value.trim().length > 0;
                    const hasOgDesc = ogDescInput_{{ $lang }}.value && ogDescInput_{{ $lang }}.value.trim().length > 0;

                    if (hasOgTitle || hasOgDesc) {
                        console.log('🎯 {{ strtoupper($lang) }} dili için sosyal medya alanları dolu, switch aktifleştiriliyor');
                        switchElement_{{ $lang }}.checked = true;
                        customFields_{{ $lang }}.style.display = 'block';

                        // Livewire model'i de güncelle
                        if (window.Livewire) {
                            switchElement_{{ $lang }}.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            @endforeach
        }

        // A1 PATTERN: applyAlternativeDirectly Function
        window.applyAlternativeDirectly = function(fieldTarget, value, element) {
            console.log('🎯 applyAlternativeDirectly:', { fieldTarget, value });

            const currentLang = window.currentLanguage || 'tr';
            let targetSelector = '';

            // Field target'a göre selector belirle
            switch(fieldTarget) {
                case 'seo_title':
                case 'title':
                case 'seoDataCache.tr.seo_title':
                case 'seoDataCache.en.seo_title':
                case 'seoDataCache.ar.seo_title':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.seo_title"]`;
                    break;
                case 'seo_description':
                case 'description':
                case 'seoDataCache.tr.seo_description':
                case 'seoDataCache.en.seo_description':
                case 'seoDataCache.ar.seo_description':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.seo_description"]`;
                    break;
                case 'og_title':
                case 'seoDataCache.tr.og_title':
                case 'seoDataCache.en.og_title':
                case 'seoDataCache.ar.og_title':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.og_title"]`;
                    break;
                case 'og_description':
                case 'seoDataCache.tr.og_description':
                case 'seoDataCache.en.og_description':
                case 'seoDataCache.ar.og_description':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.og_description"]`;
                    break;
            }

            if (targetSelector) {
                const targetField = document.querySelector(targetSelector);
                if (targetField) {
                    targetField.value = value;
                    targetField.dispatchEvent(new Event('input', { bubbles: true }));

                    // Visual feedback
                    if (element) {
                        // Diğer öğeleri passive yap
                        element.parentElement.querySelectorAll('.list-group-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        // Bu öğeyi active yap
                        element.classList.add('active');
                    }

                    console.log('✅ Field updated:', fieldTarget, value);
                } else {
                    console.warn('⚠️ Target field not found:', targetSelector);
                }
            } else {
                console.warn('⚠️ Unknown field target:', fieldTarget);
            }
        };

        // Initialize character counters when language changes
        function initializeSeoCounters(language) {
            // Title counter
            const titleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.seo_title"]`);
            if (titleInput) {
                updateCharCounter(titleInput, language, 'title');
            }

            // Description counter
            const descInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.seo_description"]`);
            if (descInput) {
                updateCharCounter(descInput, language, 'description');
            }

            // OG Title counter
            const ogTitleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_title"]`);
            if (ogTitleInput) {
                updateCharCounter(ogTitleInput, language, 'og_title');
            }

            // OG Description counter
            const ogDescInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_description"]`);
            if (ogDescInput) {
                updateCharCounter(ogDescInput, language, 'og_description');
            }
        }

        // DİL DEĞİŞİMİ EVENT LİSTENER
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refreshComponent', (data) => {
                if (!data || !data.source || data.source !== 'seo-analysis') {
                    console.log('🔄 Çeviri tamamlandı - component yenileniyor...', data);
                    Livewire.components.getByName('page-manage-component')[0].$refresh();
                }
            });

            // Dil değişimi event'i
            Livewire.on('language-changed', (language) => {
                console.log('🌍 Dil değişimi algılandı:', language);
                setTimeout(() => {
                    initializeSeoCounters(language);
                }, 100);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 Page Manage Component yüklendi');
            console.log('🌍 JS Current Language:', window.currentLanguage);
            console.log('🌍 Livewire Current Language:', '{{ $currentLanguage }}');
            console.log('📄 Current Page ID:', window.currentPageId);
            console.log('🔍 Available Languages:', @json($availableLanguages));

            // Monaco Editor başlatma
            // Monaco loader hazır olunca editörleri başlat
            if (window.monacoLoaderReady) {
                initializeMonacoEditors();
            } else {
                // Monaco loader henüz hazır değilse bekle
                window.initializeMonacoEditorsWhenReady = initializeMonacoEditors;
            }

            // 🚨 KRİTİK FİX: Sayfa yüklendiğinde doğru dil content'ini göster
            const livewireLanguage = '{{ $currentLanguage }}';
            if (livewireLanguage && window.switchLanguageContent) {
                console.log('🔧 İlk yükleme: Livewire dili ile content görünürlüğü düzenleniyor:', livewireLanguage);
                window.switchLanguageContent(livewireLanguage);
                window.currentLanguage = livewireLanguage;

                // SEO counters'ı da başlat
                setTimeout(() => {
                    initializeSeoCounters(livewireLanguage);
                    // Sosyal medya switch durumlarını kontrol et
                    initializeSocialMediaSwitches();
                }, 100);
            }
        });

        function initializeMonacoEditors() {
            // require kontrolü
            if (typeof require === 'undefined') {
                console.warn('⚠️ Monaco require henüz hazır değil, 500ms sonra tekrar denenecek');
                setTimeout(initializeMonacoEditors, 500);
                return;
            }

            // CSS Textarea değerini al
            const cssTextarea = document.getElementById('css-textarea');
            const jsTextarea = document.getElementById('js-textarea');

            try {
                // Monaco Loader ile başlat
                require.config({ paths: { 'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs' }});

            require(['vs/editor/editor.main'], function() {
                console.log('🎯 Monaco Editor yüklendi');

                // CSS Editor
                cssEditor = monaco.editor.create(document.getElementById('monaco-css-editor'), {
                    value: cssTextarea.value || '',
                    language: 'css',
                    theme: cssTheme,
                    automaticLayout: true,
                    minimap: { enabled: false },
                    scrollBeyondLastLine: false,
                    fontSize: 14,
                    formatOnPaste: true,
                    formatOnType: true,
                    folding: true
                });

                // CSS değişikliklerini textarea'ya aktar
                cssEditor.onDidChangeModelContent(() => {
                    cssTextarea.value = cssEditor.getValue();
                    cssTextarea.dispatchEvent(new Event('input'));
                });

                // JavaScript Editor
                jsEditor = monaco.editor.create(document.getElementById('monaco-js-editor'), {
                    value: jsTextarea.value || '',
                    language: 'javascript',
                    theme: jsTheme,
                    automaticLayout: true,
                    minimap: { enabled: false },
                    scrollBeyondLastLine: false,
                    fontSize: 14,
                    formatOnPaste: true,
                    formatOnType: true,
                    folding: true
                });

                // JS değişikliklerini textarea'ya aktar
                jsEditor.onDidChangeModelContent(() => {
                    jsTextarea.value = jsEditor.getValue();
                    jsTextarea.dispatchEvent(new Event('input'));
                });

                console.log('✅ Monaco Editors initialized');
            });
            } catch (error) {
                console.error('❌ Monaco Editor initialization failed:', error);
                // Fallback: 1 saniye sonra tekrar dene
                setTimeout(initializeMonacoEditors, 1000);
            }
        }

        // 🎯 CSS EDITOR TOOLBAR FUNCTIONS
        function formatCssCode() {
            if (cssEditor) {
                cssEditor.getAction('editor.action.formatDocument').run();
            }
        }

        function findInCss() {
            if (cssEditor) {
                cssEditor.getAction('actions.find').run();
            }
        }

        function toggleCssFold() {
            if (cssEditor) {
                cssFoldState = !cssFoldState;
                if (cssFoldState) {
                    cssEditor.getAction('editor.foldAll').run();
                } else {
                    cssEditor.getAction('editor.unfoldAll').run();
                }
            }
        }

        function toggleCssTheme() {
            if (cssEditor) {
                cssTheme = cssTheme === 'vs-dark' ? 'vs' : 'vs-dark';
                monaco.editor.setTheme(cssTheme);
            }
        }

        function toggleCssFullscreen() {
            const cssContainer = document.getElementById('monaco-css-editor').parentElement;
            const fullscreenIcon = document.getElementById('css-fullscreen-icon');

            if (!cssFullscreen) {
                // Tema rengine göre arka plan belirle
                const bgColor = cssTheme === 'vs-dark' ? '#1e1e1e' : '#ffffff';
                const textColor = cssTheme === 'vs-dark' ? '#ffffff' : '#000000';

                cssContainer.style.position = 'fixed';
                cssContainer.style.top = '0';
                cssContainer.style.left = '0';
                cssContainer.style.width = '100vw';
                cssContainer.style.height = '100vh';
                cssContainer.style.zIndex = '9999';
                cssContainer.style.backgroundColor = bgColor;
                cssContainer.style.color = textColor;
                cssContainer.style.padding = '20px';

                document.getElementById('monaco-css-editor').style.height = 'calc(100vh - 40px)';
                fullscreenIcon.className = 'fas fa-compress';
                cssFullscreen = true;
            } else {
                cssContainer.style.position = '';
                cssContainer.style.top = '';
                cssContainer.style.left = '';
                cssContainer.style.width = '';
                cssContainer.style.height = '';
                cssContainer.style.zIndex = '';
                cssContainer.style.backgroundColor = '';
                cssContainer.style.color = '';
                cssContainer.style.padding = '';

                document.getElementById('monaco-css-editor').style.height = '350px';
                fullscreenIcon.className = 'fas fa-expand';
                cssFullscreen = false;
            }

            if (cssEditor) {
                cssEditor.layout();
            }
        }

        // 🎯 JS EDITOR TOOLBAR FUNCTIONS
        function formatJsCode() {
            if (jsEditor) {
                jsEditor.getAction('editor.action.formatDocument').run();
            }
        }

        function findInJs() {
            if (jsEditor) {
                jsEditor.getAction('actions.find').run();
            }
        }

        function toggleJsFold() {
            if (jsEditor) {
                jsFoldState = !jsFoldState;
                if (jsFoldState) {
                    jsEditor.getAction('editor.foldAll').run();
                } else {
                    jsEditor.getAction('editor.unfoldAll').run();
                }
            }
        }

        function toggleJsTheme() {
            if (jsEditor) {
                jsTheme = jsTheme === 'vs-dark' ? 'vs' : 'vs-dark';
                monaco.editor.setTheme(jsTheme);
            }
        }

        function toggleJsFullscreen() {
            const jsContainer = document.getElementById('monaco-js-editor').parentElement;
            const fullscreenIcon = document.getElementById('js-fullscreen-icon');

            if (!jsFullscreen) {
                // Tema rengine göre arka plan belirle
                const bgColor = jsTheme === 'vs-dark' ? '#1e1e1e' : '#ffffff';
                const textColor = jsTheme === 'vs-dark' ? '#ffffff' : '#000000';

                jsContainer.style.position = 'fixed';
                jsContainer.style.top = '0';
                jsContainer.style.left = '0';
                jsContainer.style.width = '100vw';
                jsContainer.style.height = '100vh';
                jsContainer.style.zIndex = '9999';
                jsContainer.style.backgroundColor = bgColor;
                jsContainer.style.color = textColor;
                jsContainer.style.padding = '20px';

                document.getElementById('monaco-js-editor').style.height = 'calc(100vh - 40px)';
                fullscreenIcon.className = 'fas fa-compress';
                jsFullscreen = true;
            } else {
                jsContainer.style.position = '';
                jsContainer.style.top = '';
                jsContainer.style.left = '';
                jsContainer.style.width = '';
                jsContainer.style.height = '';
                jsContainer.style.zIndex = '';
                jsContainer.style.backgroundColor = '';
                jsContainer.style.color = '';
                jsContainer.style.padding = '';

                document.getElementById('monaco-js-editor').style.height = '350px';
                fullscreenIcon.className = 'fas fa-expand';
                jsFullscreen = false;
            }

            if (jsEditor) {
                jsEditor.layout();
            }
        }
    </script>
@endpush
</div>
