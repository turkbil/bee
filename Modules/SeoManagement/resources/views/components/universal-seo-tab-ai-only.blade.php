{{--
    TAMAMEN AI TABANLI SEO ANALIZ RAPORU
    Fallback hesaplama sistemi yok - sadece seo_settings tablosundan veri
--}}

@props([
    'model' => null,
    'availableLanguages' => [],
    'currentLanguage' => 'tr',
    'seoDataCache' => [],
    'pageId' => null,
    'savedAnalysisResults' => null,    // AI analiz sonuçları - ZORUNLU
    'savedOverallScore' => null,       // AI genel skoru - ZORUNLU
    'disabled' => false
])

@php
    // ========================================
    // SEO VERİLERİNİ SADECE VERİTABANINDAN ÇEK
    // ========================================

    // Eğer seo_settings veritabanında AI analiz sonuçları varsa kullan
    $hasAiAnalysis = isset($savedOverallScore) && $savedOverallScore && isset($savedAnalysisResults);

    if ($hasAiAnalysis) {
        \Log::info('✅ AI analiz sonuçları veritabanından yüklendi', [
            'overall_score' => $savedOverallScore,
            'page_id' => $pageId,
            'analysis_date' => $seoSettings->analysis_date ?? 'NULL'
        ]);

        // AI analiz verilerini hazırla
        $aiDetailedScores = $savedAnalysisResults['detailed_scores'] ?? [];
        $aiStrengths = $savedAnalysisResults['strengths'] ?? [];
        $aiImprovements = $savedAnalysisResults['improvements'] ?? [];
        $aiActionItems = $savedAnalysisResults['action_items'] ?? [];

        // AI skorlarını kullan
        $displayScore = $savedOverallScore;
        $displayStatus = $savedOverallScore >= 80 ? 'success' : ($savedOverallScore >= 60 ? 'warning' : 'danger');
        $displayMessage = $savedOverallScore >= 80 ? 'Mükemmel' : ($savedOverallScore >= 60 ? 'İyi' : 'Geliştirilebilir');

    } else {
        \Log::info('ℹ️ AI analizi bulunamadı - kullanıcı analiz başlatmalı', [
            'page_id' => $pageId,
            'has_score' => isset($savedOverallScore),
            'has_results' => isset($savedAnalysisResults)
        ]);
    }
@endphp

@foreach($availableLanguages as $lang)
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

    {{-- FORM ALANLARI BURADAKİ GİBİ DEVAM EDER --}}
    {{-- Temel SEO alanları, sosyal medya ayarları vs. --}}

</div>
@endforeach

{{-- AI-Only CSS --}}
<style>
/* AI odaklı tasarım */
.ai-score-badge {
    background: linear-gradient(45deg, #28a745, #20c997) !important;
    border: none !important;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3) !important;
}

.ai-score-badge::before {
    content: '🤖 ';
    margin-right: 4px;
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

/* Fallback skorları gizle */
.fallback-score-element {
    display: none !important;
}
</style>

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
                // Sayfayı yenile - AI sonuçları veritabanına kaydedildi
                window.location.reload();
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