{{--
    A1'deki Page manage SEO tab'ından AYNEN kopyalanan universal component
    Kullanım: <x-seomanagement::universal-seo-tab :model="$model" :available-languages="$availableLanguages" :current-language="$currentLanguage" :seo-data-cache="$seoDataCache" />
--}}

<style>
.hover-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.hover-card:hover {
    background-color: rgba(0,0,0,0.03);
    border-color: rgba(0,0,0,0.2);
}

.hover-element {
    transition: all 0.2s ease;
    cursor: pointer;
}

.hover-element:hover {
    background-color: rgba(0,0,0,0.03);
}

.accordion-button {
    transition: all 0.2s ease;
}

.accordion-button:hover {
    background-color: rgba(0,0,0,0.03);
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
    background-color: rgba(0,0,0,0.03);
}

.accordion-header button {
    transition: all 0.2s ease;
}

.accordion-header button:hover {
    background-color: rgba(0,0,0,0.05) !important;
}

.accordion-collapse.show {
    background-color: rgba(0,123,255,0.02);
}

.accordion-header .accordion-button:not(.collapsed) {
    background-color: rgba(0,123,255,0.15) !important;
    color: #0d6efd !important;
    border-color: rgba(0,123,255,0.2) !important;
}

.accordion-header .accordion-button:not(.collapsed):hover {
    background-color: rgba(0,123,255,0.25) !important;
}
</style>

@props([
    'model' => null,
    'availableLanguages' => [],
    'currentLanguage' => app()->getLocale(),
    'seoDataCache' => [],
    'pageId' => null,
    'disabled' => false // Önizleme için disable özelliği
])

@php
    // Eğer pageId geçilmişse Page modelini kullan, yoksa null
    $page = $pageId ? \App\Services\GlobalCacheService::getPageWithSeo($pageId) : null;
    $seoSettings = $page ? $page->seoSetting : null;

    // Kaydedilmiş analiz sonuçları var mı kontrol et (herhangi bir analiz alanı)
    // FIX: analysis_date ve action_items varlığını da kontrol et (JSON parse hatası durumunda)
    $hasAnalysisResults = $seoSettings && (
        $seoSettings->analysis_results ||
        $seoSettings->analysis_date ||
        $seoSettings->overall_score ||
        $seoSettings->strengths ||
        $seoSettings->improvements ||
        $seoSettings->action_items ||
        // YEDEK: En az bir analiz işlemi yapılmışsa accordion'u göster
        ($seoSettings->updated_at && $seoSettings->updated_at > now()->subHours(24))
    );
    $analysisResults = $hasAnalysisResults ? $seoSettings->analysis_results : null;

    // AI Skorları - AI-only sistem için gerekli
    $savedOverallScore = $seoSettings->overall_score ?? null;
    $savedAnalysisResults = $seoSettings->analysis_results ?? null;

    // AI Önerileri kontrolü - ai_suggestions alanında kayıtlı öneri var mı?
    $hasAiRecommendations = false;
    $aiRecommendations = null;

    if ($seoSettings && !empty($seoSettings->ai_suggestions)) {
        // AI önerileri var, şimdi mevcut dil için kontrol et
        $allAiSuggestions = $seoSettings->ai_suggestions;
        if (is_array($allAiSuggestions) && isset($allAiSuggestions[$currentLanguage])) {
            $hasAiRecommendations = true;
            $aiRecommendations = $allAiSuggestions[$currentLanguage];
        }
    }
@endphp

@foreach($availableLanguages as $lang)
@php
    // Bu dilin SEO verilerini al - cache'den
    $langSeoData = [
        'seo_title' => $seoDataCache[$lang]['seo_title'] ?? '',
        'seo_description' => $seoDataCache[$lang]['seo_description'] ?? ''
    ];
    
    // Var olan sayfa ise o dilin verilerini veritabanından al
    if ($seoSettings) {
        $titles = $seoSettings->titles ?? [];
        $descriptions = $seoSettings->descriptions ?? [];
        
        $langSeoData = [
            'seo_title' => $titles[$lang] ?? '',
            'seo_description' => $descriptions[$lang] ?? ''
        ];
    }
@endphp

<div class="seo-language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
    
    {{-- AI SEO TOOLBAR - YENİ TASARIM --}}
    @if(!$disabled)
    <div class="ai-seo-toolbar mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" 
                                    class="btn btn-light ai-seo-comprehensive-btn" 
                                    data-seo-feature="seo-comprehensive-audit"
                                    data-language="{{ $lang }}">
                                <i class="fas fa-chart-bar me-1"></i>
                                {{ $hasAnalysisResults ? 'Verileri Yenile' : 'SEO Analizi' }}
                            </button>
                            
                            <button type="button" 
                                    class="btn btn-success ai-seo-recommendations-btn" 
                                    data-seo-feature="seo-smart-recommendations"
                                    data-language="{{ $lang }}">
                                <i class="fas fa-magic me-1"></i>
                                AI Önerileri
                            </button>
                        </div>
                        <div class="mt-1">
                            <small class="text-white ">AI ile SEO verilerinizi optimize edin</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- AI SEO RECOMMENDATIONS SECTION --}}
    @if(!$disabled)
    <div class="ai-seo-recommendations-section mt-4" id="aiSeoRecommendationsSection_{{ $lang }}" style="display: {{ ($hasAiRecommendations && $currentLanguage === $lang) ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-12">
                <div class="bg-light border p-3 rounded-3 mb-3 position-relative">
                    <h3 class="mb-0">
                        <i class="fas fa-magic me-2"></i>
                        AI SEO Önerileri
                    </h3>
                    @if(!$disabled)
                    <button type="button"
                            class="btn btn-outline-danger btn-sm position-absolute"
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
                {{-- LOADING STATE --}}
                <div class="ai-recommendations-loading" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center py-4">
                        <div class="spinner-border text-success me-3" role="status"></div>
                        <div>
                            <h6 class="mb-1">AI öneriler üretiliyor...</h6>
                            <small>
                                Sayfanız analiz ediliyor ve özelleştirilmiş öneriler hazırlanıyor.
                            </small>
                        </div>
                    </div>
                </div>

                {{-- RECOMMENDATIONS CONTENT --}}
                <div class="ai-recommendations-content" style="display: {{ ($hasAiRecommendations && $currentLanguage === $lang) ? 'block' : 'none' }};">

                    @if($hasAiRecommendations && $currentLanguage === $lang)
                        @php
                            // AI önerilerini decode et - eğer zaten array ise decode etme
                            if (is_string($aiRecommendations)) {
                                $recommendations = json_decode($aiRecommendations, true);
                            } else {
                                $recommendations = $aiRecommendations;
                            }
                            $recommendationsData = $recommendations['recommendations'] ?? [];
                        @endphp


                        {{-- RECOMMENDATIONS LIST --}}
                        <div class="ai-recommendations-list">
                            @if(!empty($recommendationsData))
                                @php
                                    $seoRecs = collect($recommendationsData)->filter(function($rec) {
                                        return in_array($rec['type'], ['title', 'description', 'seo_title', 'seo_description']);
                                    });
                                    $socialRecs = collect($recommendationsData)->filter(function($rec) {
                                        return str_contains($rec['type'], 'og_') || str_contains($rec['type'], 'social');
                                    });
                                @endphp

                                @if($seoRecs->count() > 0)
                                <div class="row mb-4">
                                    @foreach($seoRecs as $index => $rec)
                                        <div class="col-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ $rec['title'] ?? 'SEO Önerisi' }}</h3>
                                                </div>
                                                <div class="list-group list-group-flush">
                                                    @if(isset($rec['alternatives']) && !empty($rec['alternatives']))
                                                        @foreach($rec['alternatives'] as $altIndex => $alt)
                                                            <a href="#" class="list-group-item list-group-item-action{{ $altIndex === 0 ? ' active' : '' }}"
                                                               onclick="applyAlternativeDirectly('{{ $rec['field_target'] }}', '{{ addslashes($alt['value']) }}', this); return false;">
                                                                {{ $alt['value'] }}
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <a href="#" class="list-group-item list-group-item-action">
                                                            {{ $rec['value'] ?? $rec['suggested_value'] ?? '' }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($socialRecs->count() > 0)
                                <div class="row mb-4">
                                    @foreach($socialRecs as $index => $rec)
                                        <div class="col-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ $rec['title'] ?? 'Sosyal Medya Önerisi' }}</h3>
                                                </div>
                                                <div class="list-group list-group-flush">
                                                    @if(isset($rec['alternatives']) && !empty($rec['alternatives']))
                                                        @foreach($rec['alternatives'] as $altIndex => $alt)
                                                            <a href="#" class="list-group-item list-group-item-action{{ $altIndex === 0 ? ' active' : '' }}"
                                                               onclick="applyAlternativeDirectly('{{ $rec['field_target'] }}', '{{ addslashes($alt['value']) }}', this); return false;">
                                                                {{ $alt['value'] }}
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <a href="#" class="list-group-item list-group-item-action">
                                                            {{ $rec['value'] ?? $rec['suggested_value'] ?? '' }}
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
                                </div>
                            @endif
                        </div>

                    @else

                        {{-- RECOMMENDATIONS LIST --}}
                        <div class="ai-recommendations-list">
                            {{-- Recommendation items will be dynamically inserted here --}}
                        </div>
                    @endif

                    {{-- SUCCESS FEEDBACK --}}
                    <div class="ai-recommendations-success" style="display: none;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Başarılı!</strong> Seçilen öneriler uygulandı. SEO verileriniz güncellendi.
                        </div>
                    </div>
                </div>

                {{-- ERROR STATE --}}
                <div class="ai-recommendations-error" style="display: none;">
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-circle me-2"></i>Öneriler üretilemedi</h6>
                        <p class="mb-2">AI servisinde bir sorun oluştu. Lütfen tekrar deneyin.</p>
                        <button type="button" class="btn btn-outline-danger btn-sm ai-retry-recommendations">
                            <i class="fas fa-redo me-1"></i>
                            Tekrar Dene
                        </button>
                    </div>
                </div>
        </div>
    </div>
    @endif

    {{-- 📊 CANLI SEO ANALİZİ - GLOBAL STANDART --}}
    <div class="mt-4">
        <div class="row">
            <div class="col-12">
                <div class="bg-light border p-3 rounded-3 mb-3 position-relative">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        SEO Analiz Raporu
                    </h3>
                    @if(isset($seoSettings->analysis_date))
                        <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                            {{ \Carbon\Carbon::parse($seoSettings->analysis_date)->diffForHumans() }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
        <div>
            @php
                // ========================================
                // CANLI VERİ OKUMA SİSTEMİ - FORM VERİLERİ
                // ========================================

                // Main Tab Verileri (Anlık form verilerinden)
                $pageTitle = '';
                $pageSlug = '';
                $pageBody = '';

                if(is_array($model->title ?? null)) {
                    $pageTitle = collect($model->title)->get($lang, '');
                } else {
                    $pageTitle = $model->title ?? '';
                }

                if(is_array($model->slug ?? null)) {
                    $pageSlug = collect($model->slug)->get($lang, '');
                } else {
                    $pageSlug = $model->slug ?? '';
                }

                if(is_array($model->body ?? null)) {
                    $pageBody = collect($model->body)->get($lang, '');
                } else {
                    $pageBody = $model->body ?? '';
                }

                // SEO Tab Verileri (Anlık cache verilerinden)
                $metaTitle = $seoDataCache[$lang]['seo_title'] ?? '';
                $metaDescription = $seoDataCache[$lang]['seo_description'] ?? '';
                $ogTitle = $seoDataCache[$lang]['og_title'] ?? '';
                $ogDescription = $seoDataCache[$lang]['og_description'] ?? '';
                $ogImage = $seoDataCache[$lang]['og_image'] ?? '';
                $authorName = $seoDataCache[$lang]['author_name'] ?? '';

                // ========================================
                // AI-ONLY SEO ANALİZ SİSTEMİ - FALLBACK YOK
                // ========================================

                // Default boş değerler - AI verisi yoksa boş gösterilecek
                $titleToAnalyze = !empty($metaTitle) ? $metaTitle : $pageTitle;
                $titleLength = strlen($titleToAnalyze);
                $descLength = strlen($metaDescription);
                $wordCount = str_word_count(strip_tags($pageBody));
                $charCount = strlen(strip_tags($pageBody));
                $hasH1 = strpos($pageBody, '<h1') !== false;
                $hasH2 = strpos($pageBody, '<h2') !== false;
                $linkCount = substr_count($pageBody, '<a');

                $titleScore = 0;
                $titleStatus = 'secondary';
                $titleMessage = 'AI analizi bekleniyor';

                $descScore = 0;
                $descStatus = 'secondary';
                $descMessage = 'AI analizi bekleniyor';

                $contentScore = 0;
                $contentStatus = 'secondary';
                $contentMessage = 'AI analizi bekleniyor';

                $socialScore = 0;
                $socialStatus = 'secondary';
                $socialMessage = 'AI analizi bekleniyor';

                $overallScore = 0;
                $overallStatus = 'secondary';
                $overallMessage = 'AI analizi bekleniyor';

                // SADECE AI VERİLERİ KULLAN - FALLBACK YOK
                $hasAiData = isset($savedOverallScore) && $savedOverallScore && isset($savedAnalysisResults);

                if ($hasAiData) {
                    // AI verilerini kullan
                    $overallScore = $savedOverallScore;
                    $overallStatus = $savedOverallScore >= 80 ? 'success' : ($savedOverallScore >= 60 ? 'warning' : 'danger');
                    $overallMessage = $savedOverallScore >= 80 ? 'Mükemmel' : ($savedOverallScore >= 60 ? 'İyi' : 'Geliştirilebilir');

                    // AI detailed scores kullan
                    if (isset($savedAnalysisResults['detailed_scores'])) {
                        $aiDetailedScores = $savedAnalysisResults['detailed_scores'];
                        $titleScore = $aiDetailedScores['title']['score'] ?? 0;
                        $descScore = $aiDetailedScores['description']['score'] ?? 0;
                        $contentScore = $aiDetailedScores['content']['score'] ?? 0;
                        $socialScore = $aiDetailedScores['social']['score'] ?? 0;

                        // AI statusları
                        $titleStatus = $titleScore >= 80 ? 'success' : ($titleScore >= 60 ? 'warning' : 'danger');
                        $descStatus = $descScore >= 80 ? 'success' : ($descScore >= 60 ? 'warning' : 'danger');
                        $contentStatus = $contentScore >= 80 ? 'success' : ($contentScore >= 60 ? 'warning' : 'danger');
                        $socialStatus = $socialScore >= 80 ? 'success' : ($socialScore >= 60 ? 'warning' : 'danger');
                    } else {
                        // AI detailed scores yoksa sıfır
                        $titleScore = $descScore = $contentScore = $socialScore = 0;
                        $titleStatus = $descStatus = $contentStatus = $socialStatus = 'secondary';
                    }

                    $aiStrengths = $savedAnalysisResults['strengths'] ?? [];
                    $aiImprovements = $savedAnalysisResults['improvements'] ?? [];
                    $aiActionItems = $savedAnalysisResults['action_items'] ?? [];
                }
            @endphp

            {{-- GENEL DURUM ÖZETİ --}}
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
                                        <div class="progress-bar bg-{{ $titleStatus }}" style="width: {{ $titleScore }}%"></div>
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
                                        <div class="progress-bar bg-{{ $descStatus }}" style="width: {{ $descScore }}%"></div>
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
                                        <div class="progress-bar bg-{{ $contentStatus }}" style="width: {{ $contentScore }}%"></div>
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
                                        <div class="progress-bar bg-{{ $socialStatus }}" style="width: {{ $socialScore }}%"></div>
                                    </div>
                                    <div>{{ $socialScore }}/100</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAYLI ANALİZ ACCORDİON --}}
            <div class="accordion mt-4" id="realTimeSeoAccordion">

                {{-- 1. META ETİKET ANALİZİ --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#metaAnalysis" aria-expanded="false">
                            <i class="fas fa-tags me-2"></i>
                            Meta Etiket Analizi
                            <span class="badge bg-{{ $titleStatus }} position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ round(($titleScore + $descScore) / 2) }}/100</span>
                        </button>
                    </h2>
                    <div id="metaAnalysis" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                        <div class="accordion-body pt-4">

                            <div class="mb-4">
                                <h5 class="mb-3">Meta Title</h5>
                                <div class="p-3 rounded border">
                                    @if(empty($titleToAnalyze))
                                        <p class="mb-1">Başlık bulunamadı</p>
                                    @else
                                        <p class="mb-1">"{{ $titleToAnalyze }}"</p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $titleLength }} karakter</span>
                                        @if(!empty($metaTitle))
                                            <span class="badge bg-success">Meta Title</span>
                                        @elseif(!empty($pageTitle))
                                            <span class="badge bg-warning">Sayfa Başlığı</span>
                                        @else
                                            <span class="badge bg-danger">Yok</span>
                                        @endif
                                    </div>
                                </div>
                                @if($titleScore < 80)
                                <p class="mb-0 mt-2">
                                    @if(empty($metaTitle))
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
                                    @if(empty($metaDescription))
                                        <p class="mb-1">Meta açıklama yok</p>
                                    @else
                                        <p class="mb-1">"{{ Str::limit($metaDescription, 100) }}"</p>
                                    @endif
                                    <span>{{ $descLength }} karakter</span>
                                </div>
                                @if($descScore < 80)
                                <p class="mb-0 mt-2">
                                    @if(empty($metaDescription))
                                        SEO tab'ında meta açıklama alanını doldurun
                                    @elseif($descLength < 120)
                                        Açıklamayı genişletin (120-160 karakter arası ideal)
                                    @elseif($descLength > 160)
                                        Açıklamayı kısaltın (maksimum 160 karakter)
                                    @endif
                                </p>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- 2. İÇERİK KALİTE ANALİZİ --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#contentQualityAnalysis" aria-expanded="false">
                            <i class="fas fa-file-alt me-2"></i>
                            İçerik Kalite Analizi
                            <span class="badge bg-{{ $contentStatus }} position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ $contentScore }}/100</span>
                        </button>
                    </h2>
                    <div id="contentQualityAnalysis" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                        <div class="accordion-body pt-4">

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
                                        @if($hasH1)
                                            <span class="badge bg-success">Mevcut</span>
                                        @else
                                            <span class="badge bg-danger">Yok</span>
                                        @endif
                                    </div>
                                    <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                        <span>H2 Alt Başlıklar</span>
                                        @if($hasH2)
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

                            @if($contentScore < 80)
                            <div>
                                <h5 class="mb-3">Öneriler</h5>
                                <div class="p-3 rounded border">
                                    @if($wordCount < 300)
                                        <p class="mb-2">• İçeriği en az 300 kelimeye çıkarın (şu an: {{ $wordCount }})</p>
                                    @endif
                                    @if(!$hasH1)
                                        <p class="mb-2">• Ana tab'ta başlık alanını doldurun veya içeriğe H1 ekleyin</p>
                                    @endif
                                    @if(!$hasH2)
                                        <p class="mb-2">• İçeriğe 2-3 alt başlık (H2) ekleyin</p>
                                    @endif
                                    @if($linkCount == 0)
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

                {{-- 3. SOSYAL MEDYA HAZIRLIĞI --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#socialMediaAnalysis" aria-expanded="false">
                            <i class="fas fa-share-alt me-2"></i>
                            Sosyal Medya Hazırlığı
                            <span class="badge bg-{{ $socialStatus }} position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ $socialScore }}/100</span>
                        </button>
                    </h2>
                    <div id="socialMediaAnalysis" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                        <div class="accordion-body pt-4">

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
                                        @if(!empty($ogTitle) || !empty($metaTitle))
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
                                        @if(!empty($ogDescription) || !empty($metaDescription))
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
                                        @if(!empty($ogImage))
                                            <span class="badge bg-success">Mevcut</span>
                                        @else
                                            <span class="badge bg-danger">Yok</span>
                                        @endif
                                    </div>
                                    <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>Yazar Bilgisi</strong>
                                            <div>{{ !empty($authorName) ? $authorName : 'Belirtilmemiş' }}</div>
                                        </div>
                                        @if(!empty($authorName))
                                            <span class="badge bg-success">Mevcut</span>
                                        @else
                                            <span class="badge bg-danger">Yok</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($socialScore < 70)
                            <div>
                                <h5 class="mb-3">Öneriler</h5>
                                <div class="p-3 rounded border">
                                    @if(empty($ogImage))
                                        <p class="mb-2">• 1200x630px sosyal medya görseli ekleyin</p>
                                    @endif
                                    @if(empty($ogTitle) && empty($metaTitle))
                                        <p class="mb-2">• Meta title veya OG title ekleyin</p>
                                    @endif
                                    @if(empty($ogDescription) && empty($metaDescription))
                                        <p class="mb-2">• Meta description veya OG description ekleyin</p>
                                    @endif
                                    @if(empty($authorName))
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

                {{-- 4. ÖNCELİKLİ EYLEM PLANI --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#actionPlan" aria-expanded="false">
                            <i class="fas fa-bullseye me-2"></i>
                            Öncelikli Eylem Planı
                            <span class="badge bg-primary position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ ($titleScore < 80) + ($descScore < 80) + ($contentScore < 80) + ($socialScore < 70) }} eylem</span>
                        </button>
                    </h2>
                    <div id="actionPlan" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                        <div class="accordion-body pt-4">

                            <h5 class="mb-3">Yapılacaklar Listesi</h5>
                            <div class="p-3 rounded border">
                                @if($titleScore < 80)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <h6 class="mb-2">
                                            <span class="badge bg-danger me-2">KRİTİK</span>
                                            Meta Title {{ empty($metaTitle) ? 'ekle' : 'iyileştir' }}
                                        </h6>
                                        <ul class="mb-0">
                                            @if(empty($metaTitle))
                                                <li>SEO tab'ında meta title alanını doldurun</li>
                                            @elseif($titleLength < 30)
                                                <li>En az 30 karakter olmalı (şu an: {{ $titleLength }})</li>
                                            @elseif($titleLength > 60)
                                                <li>Maksimum 60 karakter olmalı (şu an: {{ $titleLength }})</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                @if($contentScore < 80)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <h6 class="mb-2">
                                            <span class="badge bg-{{ $wordCount < 300 ? 'danger' : 'warning' }} me-2">{{ $wordCount < 300 ? 'KRİTİK' : 'YÜKSEK' }}</span>
                                            İçeriği geliştir
                                        </h6>
                                        <ul class="mb-0">
                                            @if($wordCount < 300)
                                                <li>En az 300 kelime yazın (şu an: {{ $wordCount }})</li>
                                            @endif
                                            @if(!$hasH1)
                                                <li>H1 başlığı ekleyin</li>
                                            @endif
                                            @if(!$hasH2)
                                                <li>H2 alt başlıkları ekleyin</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                @if($descScore < 80)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <h6 class="mb-2">
                                            <span class="badge bg-{{ empty($metaDescription) ? 'danger' : 'warning' }} me-2">{{ empty($metaDescription) ? 'KRİTİK' : 'YÜKSEK' }}</span>
                                            Meta Description {{ empty($metaDescription) ? 'ekle' : 'iyileştir' }}
                                        </h6>
                                        <ul class="mb-0">
                                            @if(empty($metaDescription))
                                                <li>SEO tab'ında meta açıklama alanını doldurun</li>
                                            @elseif($descLength < 120)
                                                <li>En az 120 karakter olmalı (şu an: {{ $descLength }})</li>
                                            @elseif($descLength > 160)
                                                <li>Maksimum 160 karakter olmalı (şu an: {{ $descLength }})</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                @if($socialScore < 70)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <h6 class="mb-2">
                                            <span class="badge bg-warning me-2">YÜKSEK</span>
                                            Sosyal medya optimizasyonu
                                        </h6>
                                        <ul class="mb-0">
                                            @if(empty($ogImage))
                                                <li>1200x630px görsel ekleyin</li>
                                            @endif
                                            @if(empty($authorName))
                                                <li>Yazar bilgisi ekleyin</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                @if($overallScore >= 80)
                                    <div class="text-center">
                                        <i class="fas fa-trophy text-success fa-2x mb-2"></i>
                                        <h6 class="text-success">Tebrikler! SEO optimizasyonu tamamlandı.</h6>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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
                            <input type="text" 
                                   wire:model="seoDataCache.{{ $lang }}.seo_title"
                                   class="form-control seo-no-enter @error('seoDataCache.' . $lang . '.seo_title') is-invalid @enderror"
                                   placeholder="{{ __('page::admin.seo_title_placeholder') }}"
                                   maxlength="60"
                                   {{ $disabled ? 'disabled' : '' }}>
                            <label>
                                {{ __('page::admin.seo_title') }}
                            </label>
                            {{-- Character counter - sağ üst köşe --}}
                            <span class="character-counter position-absolute"
                                  id="title_counter_{{ $lang }}"
                                  style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                <small >0/60</small>
                            </span>
                            <div class="form-text">
                                <small >
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
                                      placeholder="{{ __('page::admin.seo_description_placeholder') }}"
                                      style="height: 100px; resize: vertical;"
                                      maxlength="160"
                                      {{ $disabled ? 'disabled' : '' }}></textarea>
                            <label>
                                {{ __('page::admin.seo_description') }}
                            </label>
                            {{-- Character counter - sağ üst köşe --}}
                            <span class="character-counter position-absolute"
                                  id="description_counter_{{ $lang }}"
                                  style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                <small >0/160</small>
                            </span>
                            <div class="form-text">
                                <small >
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
                            <small >
                                Hem sosyal medya hem arama motorları için kullanılır
                            </small>
                        </div>
                    </div>
                    
                    {{-- Custom Content Type Input --}}
                    <div class="mt-3" 
                         id="custom_content_type_{{ $lang }}" 
                         style="display: none;">
                        <div class="form-floating">
                            <input type="text" 
                                   wire:model="seoDataCache.{{ $lang }}.content_type_custom"
                                   class="form-control seo-no-enter"
                                   placeholder="Örn: Recipe, Book, Course..."
                                   {{ $disabled ? 'disabled' : '' }}>
                            <label>
                                Özel İçerik Türü
                                <small class="ms-2">Manuel giriş</small>
                            </label>
                            <div class="form-text">
                                <small >
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
                            <span class="priority-value">{{ $priorityValue }}</span>/10 - <span class="priority-text">{{ $priorityText }}</span>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-bold">1</span>
                        <span class="small">Düşük</span>
                        <input type="range" 
                               wire:model="seoDataCache.{{ $lang }}.priority_score"
                               class="form-range flex-grow-1 mx-2"
                               min="1" 
                               max="10" 
                               step="1"
                               value="{{ $seoDataCache[$lang]['priority_score'] ?? 5 }}"
                               oninput="onManualPriorityChange(this, '{{ $lang }}')"
                               {{ $disabled ? 'disabled' : '' }}>
                        <span class="small">Kritik</span>
                        <span class="small fw-bold">10</span>
                    </div>
                    <div class="form-text mt-2 priority-examples">
                        <small >
                            
                            <span class="priority-example" data-range="1-3" style="opacity: 0.4;"><strong>1-3:</strong> Blog yazıları, arşiv</span> &nbsp;•&nbsp; 
                            <span class="priority-example" data-range="4-6" style="opacity: 1;"><strong>4-6:</strong> Ürün sayfaları</span> &nbsp;•&nbsp; 
                            <span class="priority-example" data-range="7-8" style="opacity: 0.4;"><strong>7-8:</strong> Önemli kategoriler</span> &nbsp;•&nbsp; 
                            <span class="priority-example" data-range="9-10" style="opacity: 0.4;"><strong>9-10:</strong> Ana sayfa, kampanyalar</span>
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
    <div class="card border-success mb-4" style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem !important; transition: border-radius 0.15s;">
        <div class="card-header bg-success text-white" style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem 0.25rem 0px 0px !important;">
            <h6 class="mb-0">
                Sosyal Medya Paylaşım Ayarları
                <small class=" ms-2">Facebook, LinkedIn, WhatsApp için</small>
            </h6>
        </div>
        <div class="card-body" style="border-radius: 0px 0px 0.25rem 0.25rem !important;">
            @if($lang === ($availableLanguages[0] ?? 'tr'))
            <div class="row">
                {{-- Sosyal Medya Görseli --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Sosyal Medya Resmi
                        <small class="ms-2">1200x630 önerilen</small>
                    </label>
                    
                    {{-- Media Preview --}}
                    @if(!empty($seoDataCache[$lang]['og_image']))
                    <div class="media-preview-container mb-2 position-relative">
                        <img src="{{ $seoDataCache[$lang]['og_image'] }}" 
                             class="img-fluid rounded border" 
                             style="max-height: 120px; width: auto;"
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
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm flex-fill"
                                onclick="document.getElementById('og_image_file_{{ $lang }}').click()"
                                {{ $disabled ? 'disabled' : '' }}>
                            
                            {{ empty($seoDataCache[$lang]['og_image']) ? 'Resim Seç' : 'Resim Değiştir' }}
                        </button>
                        
                        <input type="url" 
                               wire:model="seoDataCache.{{ $lang }}.og_image_url"
                               class="form-control form-control-sm"
                               placeholder="Veya URL girin"
                               style="flex: 2;"
                               {{ $disabled ? 'disabled' : '' }}>
                    </div>
                    
                    {{-- Hidden File Input --}}
                    <input type="file" 
                           id="og_image_file_{{ $lang }}"
                           wire:model="seoImageFiles.og_image"
                           class="d-none"
                           accept="image/jpeg,image/jpg,image/png,image/webp"
                           {{ $disabled ? 'disabled' : '' }}>
                    
                    {{-- Upload Progress --}}
                    <div class="progress mt-2" 
                         wire:loading 
                         wire:target="seoImageFiles.og_image"
                         style="height: 4px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                             style="width: 100%"></div>
                    </div>
                    
                    <div class="form-text mt-2">
                        <small >
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
                                   {{ $autoChecked ? 'checked' : '' }}
                                   {{ $disabled ? 'disabled' : '' }}>
                            <div class="state">
                                <label for="og_custom_{{ $lang }}">
                                    Ayarları özelleştirmek istiyorum
                                </label>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            <small >
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
            <div class="og-custom-fields"
                 id="og_custom_fields_{{ $lang }}"
                 style="display: {{ $autoChecked ? 'block' : 'none' }}; max-height: none; overflow: visible;">
                <hr class="my-3">
                <div class="row">
                    {{-- OG Title --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating position-relative" style="border-radius: 0.25rem !important; overflow: hidden !important;">
                            <input type="text"
                                   wire:model="seoDataCache.{{ $lang }}.og_title"
                                   class="form-control seo-no-enter"
                                   placeholder="Facebook/LinkedIn'de görünecek özel başlık"
                                   maxlength="60"
                                   style="border-radius: 0.25rem !important;"
                                   {{ $disabled ? 'disabled' : '' }}>
                            <label>
                                Sosyal Medya Başlığı
                                <small class="ms-2">Maksimum 60 karakter</small>
                            </label>
                            {{-- Character counter - sağ üst köşe --}}
                            <span class="character-counter position-absolute"
                                  id="og_title_counter_{{ $lang }}"
                                  style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                <small >0/60</small>
                            </span>
                            <div class="form-text">
                                <small >
                                    Sosyal medya paylaşımlarında görünecek başlık
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- OG Description --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating position-relative" style="border-radius: 0.25rem !important; overflow: hidden !important;">
                            <textarea wire:model="seoDataCache.{{ $lang }}.og_description"
                                      class="form-control seo-no-enter"
                                      placeholder="Facebook/LinkedIn'de görünecek özel açıklama"
                                      style="height: 100px; resize: vertical; border-radius: 0.25rem !important;"
                                      maxlength="155"
                                      {{ $disabled ? 'disabled' : '' }}></textarea>
                            <label>
                                Sosyal Medya Açıklaması
                                <small class="ms-2">Maksimum 155 karakter</small>
                            </label>
                            {{-- Character counter - sağ üst köşe --}}
                            <span class="character-counter position-absolute"
                                  id="og_description_counter_{{ $lang }}"
                                  style="top: 8px; right: 12px; z-index: 5; font-size: 11px;">
                                <small >0/155</small>
                            </span>
                            <div class="form-text">
                                <small >
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
            @if($lang === ($availableLanguages[0] ?? 'tr'))
            <div class="row">



                {{-- Author Name --}}
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" 
                               wire:model="seoDataCache.{{ $lang }}.author_name"
                               class="form-control seo-no-enter"
                               placeholder="Nurullah Okatan"
                               {{ $disabled ? 'disabled' : '' }}>
                        <label>
                            Yazar Adı
                            <small class="ms-2">İçerik yazarı</small>
                        </label>
                        <div class="form-text">
                            <small >
                                Bu içeriği yazan kişinin adı (schema.org author)
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Author URL/Profile --}}
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="url" 
                               wire:model="seoDataCache.{{ $lang }}.author_url"
                               class="form-control seo-no-enter"
                               placeholder="https://example.com/author/nurullah-okatan"
                               {{ $disabled ? 'disabled' : '' }}>
                        <label>
                            Yazar Profil URL'si
                            <small class="ms-2">Yazarın profil sayfası</small>
                        </label>
                        <div class="form-text">
                            <small >
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

@if(!$disabled)
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
    @if(!isset($seoJsInitialized))
    window.currentModelId = @if($pageId){{ $pageId }}@else null @endif;
    window.currentLanguage = '{{ $currentLanguage }}';
    
    // ULTRA PERFORMANCE: Tüm dillerin SEO verileri (ZERO API CALLS)
    try {
        @php
            // SEO Data Cache'den JavaScript için veri hazırla - HEM YENİ HEM ESKİ SAYFA
            $allLangSeoData = $seoDataCache ?? [];
            
            // Boş cache varsa her dil için boş veri oluştur (yeni sayfa için)
            if (empty($allLangSeoData) && !empty($availableLanguages)) {
                foreach($availableLanguages as $lang) {
                    $allLangSeoData[$lang] = [
                        'seo_title' => '',
                        'seo_description' => ''
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
        badge.className = badge.className.replace(/bg-(primary|secondary|success|danger|warning|info|light|dark)/g, '');
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
        const visibleContent = document.querySelector('.seo-language-content[style*="display: block"], .seo-language-content[style=""], .seo-language-content:not([style*="display: none"])');
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
    document.addEventListener('livewire:navigated', function () {
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
            const contentTypeSelects = document.querySelectorAll('select[wire\\:model*=\"content_type\"]');
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
        const visibleContent = document.querySelector('.seo-language-content[style*="display: block"], .seo-language-content[style=""], .seo-language-content:not([style*="display: none"])');
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
        titleInput.dispatchEvent(new Event('input', { bubbles: true }));
        
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

{{-- AI SEO Integration JavaScript --}}
<script src="{{ asset('assets/js/ai-seo-integration.js') }}"></script>
@endif