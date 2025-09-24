{{--
    TAMAMEN AI TABANLI SEO ANALIZ SISTEMI
    Fallback hesaplama sistemi kaldırıldı - sadece seo_settings tablosundan AI verileri
--}}

@props([
    'model' => null,
    'availableLanguages' => [],
    'currentLanguage' => 'tr',
    'seoDataCache' => [],
    'pageId' => null,
    'savedAnalysisResults' => null,
    'savedOverallScore' => null,
    'disabled' => false
])

@php
    // Eğer pageId geçilmişse Page modelini kullan, yoksa null
    $page = $pageId ? \App\Services\GlobalCacheService::getPageWithSeo($pageId) : null;
    $seoSettings = $page ? $page->seoSetting : null;

    // AI analiz sonuçları var mı kontrol et
    $hasAnalysisResults = $seoSettings && (
        $seoSettings->analysis_results ||
        $seoSettings->analysis_date ||
        $seoSettings->overall_score ||
        $seoSettings->strengths ||
        $seoSettings->improvements ||
        $seoSettings->action_items ||
        ($seoSettings->updated_at && $seoSettings->updated_at > now()->subHours(24))
    );
    $analysisResults = $hasAnalysisResults ? $seoSettings->analysis_results : null;

    // AI Önerileri kontrolü
    $hasAiRecommendations = false;
    $aiRecommendations = null;

    if ($seoSettings && !empty($seoSettings->ai_suggestions)) {
        $allAiSuggestions = $seoSettings->ai_suggestions;
        if (is_array($allAiSuggestions) && isset($allAiSuggestions[$currentLanguage])) {
            $hasAiRecommendations = true;
            $aiRecommendations = $allAiSuggestions[$currentLanguage];
        }
    }

    // ========================================
    // AI ONLY - SADECE VERİTABANI VERİLERİ
    // ========================================

    $hasAiAnalysis = isset($savedOverallScore) && $savedOverallScore && isset($savedAnalysisResults);

    if ($hasAiAnalysis) {
        // AI verilerini hazırla
        $aiDetailedScores = $savedAnalysisResults['detailed_scores'] ?? [];
        $aiStrengths = $savedAnalysisResults['strengths'] ?? [];
        $aiImprovements = $savedAnalysisResults['improvements'] ?? [];
        $aiActionItems = $savedAnalysisResults['action_items'] ?? [];

        \Log::info('✅ AI verileri yüklenliği doğrulandı', [
            'overall_score' => $savedOverallScore,
            'page_id' => $pageId,
            'detailed_scores_count' => count($aiDetailedScores)
        ]);
    } else {
        \Log::info('ℹ️ AI analizi bulunamadı - kullanıcı analiz başlatmalı', [
            'page_id' => $pageId
        ]);
    }
@endphp

<style>
.hover-card {
    transition: all 0.2s ease;
    cursor: pointer;
}

.hover-card:hover {
    background-color: rgba(0,0,0,0.03);
    border-color: rgba(0,0,0,0.2);
}

.ai-waiting-state {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    border-radius: 12px;
}

.ai-waiting-state .btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.ai-waiting-state .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
}
</style>

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

    {{-- AI SEO TOOLBAR --}}
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
                                <i class="fas fa-robot me-1"></i>
                                {{ $hasAiAnalysis ? 'AI Analizi Yenile' : 'AI SEO Analizi' }}
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
                            <small class="text-white">
                                <i class="fas fa-robot me-1"></i>
                                Yapay zeka ile SEO verilerinizi optimize edin
                            </small>
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
                            // AI önerilerini decode et
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

    {{-- 📊 AI SEO ANALİZ RAPORU --}}
    <div class="mt-4">
        <div class="row">
            <div class="col-12">
                <div class="bg-light border p-3 rounded-3 mb-3 position-relative">
                    <h3 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        AI SEO Analiz Raporu
                    </h3>
                    @if($hasAiAnalysis && isset($savedAnalysisResults['timestamp']))
                        <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                            {{ \Carbon\Carbon::parse($savedAnalysisResults['timestamp'])->diffForHumans() }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        @if($hasAiAnalysis)
            {{-- AI ANALIZ SONUÇLARI MEVCUT --}}

            {{-- AI Analiz Durumu --}}
            <div class="alert alert-success mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>
                        AI SEO Analiz Sonucu
                    </h5>
                    <span class="badge bg-success fs-6">
                        AI Skor: {{ $savedOverallScore }}/100
                    </span>
                </div>
                @if(isset($savedAnalysisResults['timestamp']))
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($savedAnalysisResults['timestamp'])->diffForHumans() }} AI tarafından analiz edildi
                    </small>
                @endif
            </div>

            @php
                $displayScore = $savedOverallScore;
                $displayStatus = $savedOverallScore >= 80 ? 'success' : ($savedOverallScore >= 60 ? 'warning' : 'danger');
                $displayMessage = $savedOverallScore >= 80 ? 'Mükemmel' : ($savedOverallScore >= 60 ? 'İyi' : 'Geliştirilebilir');
            @endphp

            {{-- GENEL AI SKOR ÖZETİ --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-{{ $displayStatus }} text-white mb-2">
                            {{ $displayScore }}
                        </div>
                        <h5>AI SEO Skoru</h5>
                        <p>{{ $displayMessage }}</p>
                        <small class="text-muted">
                            <i class="fas fa-robot me-1"></i>
                            AI ile analiz edildi
                        </small>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        @foreach(['title' => ['label' => 'Meta Title', 'icon' => 'heading'],
                                 'description' => ['label' => 'Meta Description', 'icon' => 'align-left'],
                                 'content' => ['label' => 'İçerik Kalitesi', 'icon' => 'file-alt'],
                                 'social' => ['label' => 'Sosyal Medya', 'icon' => 'share-alt']] as $key => $info)
                            @php
                                $score = $aiDetailedScores[$key]['score'] ?? 0;
                                $status = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
                            @endphp
                            <div class="col-md-3">
                                <div class="card border-{{ $status }} hover-card" data-score-type="{{ $key }}">
                                    <div class="card-body text-center p-3">
                                        <i class="fas fa-{{ $info['icon'] }} fa-2x mb-2"></i>
                                        <h6>{{ $info['label'] }}</h6>
                                        <div class="progress mb-1">
                                            <div class="progress-bar bg-{{ $status }}" style="width: {{ $score }}%"></div>
                                        </div>
                                        <div class="score-text">{{ $score }}/100</div>
                                        <small class="text-muted">AI Analizi</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- AI DETAYLI ANALİZ ACCORDION --}}
            <div class="accordion mt-4" id="aiSeoAnalysisAccordion">

                {{-- 1. AI GÜÇ ANALİZİ --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#aiStrengthsAnalysis">
                            <i class="fas fa-trophy me-2"></i>
                            Güçlü Yanlar (AI Analizi)
                            <span class="badge bg-success position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ count($aiStrengths) }} konu</span>
                        </button>
                    </h2>
                    <div id="aiStrengthsAnalysis" class="accordion-collapse collapse">
                        <div class="accordion-body pt-4">
                            @if(!empty($aiStrengths))
                                <div class="list-group list-group-flush">
                                    @foreach($aiStrengths as $strength)
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ is_string($strength) ? $strength : ($strength['text'] ?? $strength['description'] ?? 'AI analiz sonucu') }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    AI güçlü yanlar analizi bulunamadı.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. AI İYİLEŞTİRME ÖNERİLERİ --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#aiImprovementsAnalysis">
                            <i class="fas fa-chart-line me-2"></i>
                            İyileştirme Alanları (AI Analizi)
                            <span class="badge bg-warning position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ count($aiImprovements) }} öneri</span>
                        </button>
                    </h2>
                    <div id="aiImprovementsAnalysis" class="accordion-collapse collapse">
                        <div class="accordion-body pt-4">
                            @if(!empty($aiImprovements))
                                <div class="list-group list-group-flush">
                                    @foreach($aiImprovements as $improvement)
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <i class="fas fa-arrow-up text-warning me-2"></i>
                                            {{ is_string($improvement) ? $improvement : ($improvement['text'] ?? $improvement['description'] ?? 'AI iyileştirme önerisi') }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    AI iyileştirme önerileri bulunamadı.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. AI EYLEM PLANI --}}
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#aiActionPlan">
                            <i class="fas fa-tasks me-2"></i>
                            AI Eylem Planı
                            <span class="badge bg-primary position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%);">{{ count($aiActionItems) }} görev</span>
                        </button>
                    </h2>
                    <div id="aiActionPlan" class="accordion-collapse collapse">
                        <div class="accordion-body pt-4">
                            @if(!empty($aiActionItems))
                                <div class="list-group list-group-flush">
                                    @foreach($aiActionItems as $index => $action)
                                        @php
                                            $urgency = is_array($action) ? ($action['urgency'] ?? 'medium') : 'medium';
                                            $urgencyClass = $urgency === 'high' ? 'danger' : ($urgency === 'medium' ? 'warning' : 'info');
                                            $actionText = is_string($action) ? $action : ($action['task'] ?? $action['text'] ?? $action['description'] ?? 'AI eylem önerisi');
                                        @endphp
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex align-items-start">
                                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                <div class="flex-fill">
                                                    <strong>{{ $actionText }}</strong>
                                                    @if(is_array($action))
                                                        <br>
                                                        @if(isset($action['expected_impact']))
                                                            <small class="text-muted">Beklenen Etki: {{ $action['expected_impact'] }}</small>
                                                        @endif
                                                        @if(isset($action['urgency']))
                                                            <span class="badge bg-{{ $urgencyClass }} ms-2">{{ ucfirst($action['urgency']) }}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    AI eylem planı bulunamadı.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        @else
            {{-- AI ANALİZİ YOK - BAŞLATMA DAVET --}}
            <div class="card">
                <div class="card-body text-center py-5 ai-waiting-state">
                    <div class="empty">
                        <div class="empty-img">
                            <i class="fas fa-robot fa-5x text-muted mb-3"></i>
                        </div>
                        <p class="empty-title h3">AI SEO Analizi Bekliyor</p>
                        <p class="empty-subtitle text-muted">
                            Sayfanızın SEO performansını analiz etmek için AI motorunu başlatın.<br>
                            Kapsamlı analiz ve kişiselleştirilmiş öneriler alacaksınız.
                        </p>
                        <div class="empty-action">
                            <button type="button"
                                    class="btn btn-primary btn-lg ai-seo-comprehensive-btn"
                                    data-seo-feature="seo-comprehensive-audit"
                                    data-language="{{ $currentLanguage }}">
                                <i class="fas fa-robot me-2"></i>
                                AI SEO Analizi Başlat
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LOADING STATE --}}
            <div class="ai-analysis-loading" style="display: none;">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="spinner-border text-primary me-3" role="status"></div>
                            <div>
                                <h6 class="mb-1">AI SEO analizi yapılıyor...</h6>
                                <small class="text-muted">
                                    Sayfanız analiz ediliyor ve profesyonel öneriler hazırlanıyor.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ERROR STATE --}}
            <div class="ai-analysis-error" style="display: none;">
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-circle me-2"></i>AI Analizi Başarısız</h6>
                    <p class="mb-2">AI servisinde bir sorun oluştu. Lütfen tekrar deneyin.</p>
                    <button type="button" class="btn btn-outline-danger btn-sm ai-retry-analysis">
                        <i class="fas fa-redo me-1"></i>
                        Tekrar Dene
                    </button>
                </div>
            </div>
        @endif
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
                            {{-- Character counter --}}
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
                            {{-- Character counter --}}
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

                {{-- İçerik Türü ve Priority (Mevcut kodun geri kalanı) --}}
                {{-- ... diğer form alanları aynı kalacak ... --}}
            </div>
        </div>
    </div>

</div>
@endforeach

{{-- AI-Only JavaScript --}}
<script>
// AI Cache Manager - Sadece AI verileri
window.aiSeoManager = {

    checkAnalysisStatus: function() {
        const hasAnalysis = {{ $hasAiAnalysis ? 'true' : 'false' }};
        console.log('🤖 AI Analysis Status:', hasAnalysis);
        return hasAnalysis;
    },

    triggerAnalysis: function() {
        console.log('🚀 AI SEO analizi başlatılıyor...');

        // Loading state göster
        document.querySelector('.ai-waiting-state')?.classList.add('d-none');
        document.querySelector('.ai-analysis-loading')?.classList.remove('d-none');

        // AI API çağrısını yap
        this.performAiAnalysis();
    },

    performAiAnalysis: async function() {
        try {
            // AI API endpoint'e istek gönder
            const response = await fetch('/admin/seo/ai/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    page_id: {{ $pageId ?? 'null' }},
                    language: '{{ $currentLanguage }}',
                    feature_slug: 'comprehensive-seo-audit',
                    form_content: {
                        // Minimal form content for validation
                        page_id: {{ $pageId ?? 'null' }},
                        current_language: '{{ $currentLanguage }}'
                    }
                })
            });

            const result = await response.json();

            if (result.success) {
                console.log('✅ AI analizi başarılı', result);

                // Livewire component'e AI verilerini güncelleme komutu gönder
                if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                    window.Livewire.dispatch('aiAnalysisCompleted', {
                        analysisData: result.data
                    });
                }

                // Sayfayı yenile - AI sonuçları veritabanına kaydedildi
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                throw new Error(result.message || 'AI analizi başarısız');
            }

        } catch (error) {
            console.error('❌ AI analizi hatası:', error);
            this.showError(error.message);
        }
    },

    showError: function(message) {
        document.querySelector('.ai-analysis-loading')?.classList.add('d-none');
        const errorDiv = document.querySelector('.ai-analysis-error');
        if (errorDiv) {
            errorDiv.style.display = 'block';
            errorDiv.querySelector('p').textContent = message;
        }
    }
};

// AI butonlarına event listener ekle
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ai-seo-comprehensive-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            window.aiSeoManager.triggerAnalysis();
        });
    });

    document.querySelectorAll('.ai-retry-analysis').forEach(btn => {
        btn.addEventListener('click', function() {
            window.aiSeoManager.triggerAnalysis();
        });
    });
});
</script>