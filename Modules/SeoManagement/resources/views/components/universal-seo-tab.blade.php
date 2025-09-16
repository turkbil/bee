{{-- 
    A1'deki Page manage SEO tab'ından AYNEN kopyalanan universal component
    Kullanım: <x-seomanagement::universal-seo-tab :model="$model" :available-languages="$availableLanguages" :current-language="$currentLanguage" :seo-data-cache="$seoDataCache" />
--}}

@props([
    'model' => null,
    'availableLanguages' => [],
    'currentLanguage' => 'tr',
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
                            <small class="text-white opacity-75">AI ile SEO verilerinizi optimize edin</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- AI SEO RECOMMENDATIONS SECTION --}}
    @if(!$disabled)
    <div class="ai-seo-recommendations-section" id="aiSeoRecommendationsSection_{{ $lang }}" style="display: {{ ($hasAiRecommendations && $currentLanguage === $lang) ? 'block' : 'none' }};">
        <div class="card border-success mt-3">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-center align-items-center">
                    <h3 class="card-title mb-0 text-center">
                        <i class="fas fa-magic me-2"></i>
                        AI SEO Önerileri
                    </h3>
                </div>
            </div>
            <div class="card-body">
                {{-- LOADING STATE --}}
                <div class="ai-recommendations-loading" style="display: none;">
                    <div class="d-flex align-items-center justify-content-center py-4">
                        <div class="spinner-border text-success me-3" role="status"></div>
                        <div>
                            <h6 class="mb-1">AI öneriler üretiliyor...</h6>
                            <small class="text-muted">
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

                        {{-- HEADER ACTIONS --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">
                                    <span class="ai-recommendations-count">{{ count($recommendationsData) }}</span>
                                    özelleştirilmiş öneri yüklendi
                                    <span class="badge bg-info ms-2">Kaydedilmiş</span>
                                </h6>
                                <small class="text-muted">
                                    Öneriler otomatik olarak uygulandı. Yeni öneriler için "AI Önerileri" butonunu kullanın.
                                </small>
                            </div>
                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-outline-warning btn-sm"
                                        onclick="if(confirm('Mevcut öneriler silinecek ve yeni öneriler oluşturulacak. Emin misiniz?')) {
                                            window.forceRegenerateRecommendations = true;
                                            document.querySelector('.ai-seo-recommendations-btn[data-language=&quot;{{ $lang }}&quot;]').click();
                                        }">
                                    <i class="fas fa-refresh me-1"></i>
                                    Yeniden Oluştur
                                </button>
                            </div>
                        </div>

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
                        {{-- HEADER ACTIONS --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="mb-1">
                                    <span class="ai-recommendations-count">4</span>
                                    özelleştirilmiş öneri üretildi
                                </h6>
                                <small class="text-muted">
                                    Her öneriyi tek tek seçebilir veya tümünü uygulayabilirsiniz
                                </small>
                            </div>
                            <div class="btn-group">
                                <button type="button"
                                        class="btn btn-outline-success btn-sm ai-select-all-recommendations">
                                    <i class="fas fa-check-double me-1"></i>
                                    Tümünü Seç
                                </button>
                                <button type="button"
                                        class="btn btn-success btn-sm ai-apply-selected-recommendations"
                                        disabled>
                                    <i class="fas fa-magic me-1"></i>
                                    Seçilenleri Uygula
                                </button>
                            </div>
                        </div>

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
    </div>
    @endif

    {{-- KAYDEDILMIŞ ANALIZ SONUÇLARI --}}
    @if($hasAnalysisResults)
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Kapsamlı SEO Analizi
                <small class="ms-2 opacity-75">{{ $seoSettings->analysis_date ? \Carbon\Carbon::parse($seoSettings->analysis_date)->diffForHumans() : 'Yakın zamanda' }}</small>
            </h3>
            @if(!$disabled)
            <button type="button" 
                    class="btn btn-outline-danger btn-sm"
                    wire:click="clearSeoAnalysis"
                    onclick="return confirm('SEO analizi verileri silinecek. Emin misiniz?')"
                    title="SEO analizi verilerini sıfırla">
                <i class="fas fa-trash-alt me-1"></i>
                Verileri Sıfırla
            </button>
            @endif
        </div>
        <div class="card-body">
            @php
                $analysisData = $analysisResults;
                $overallScore = $analysisData['overall_score'] ?? $seoSettings->overall_score ?? null;
                $detailedScores = $analysisData['detailed_scores'] ?? null;
            @endphp
            @if($overallScore)
            <!-- GENEL SKOR -->
            <div class="row mb-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl {{ $overallScore >= 80 ? 'bg-success' : ($overallScore >= 60 ? 'bg-warning' : 'bg-danger') }} text-white" style="font-size: 1.5rem; font-weight: bold;">
                        {{ $overallScore }}
                    </div>
                </div>
                <div class="col">
                    <h4>Genel SEO Skoru</h4>
                    <p class="text-secondary">{{ $overallScore >= 80 ? 'Mükemmel' : ($overallScore >= 60 ? 'İyi' : 'Geliştirilebilir') }}</p>
                </div>
            </div>
            
            <!-- SKOR DETAYLARI -->
            @if($detailedScores)
            <div class="row g-3 mb-4">
                @foreach($detailedScores as $category => $details)
                    @if(isset($details['score']))
                    @php $score = $details['score']; @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-sm">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">{{ strtoupper(str_replace('_', ' ', $category)) }}</div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-{{ $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger') }}" style="width: {{ $score }}%"></div>
                                        </div>
                                    </div>
                                    <div class="ms-2 text-{{ $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger') }}">{{ $score }}/100</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
            @endif
            
            @php
                // VERİLERİ HEM JSON'DAN HEM DB ALANLARDAN OKU (FALLBACK)
                $strengths = $analysisData['strengths'] ?? ($seoSettings->strengths ?? null);
                $improvements = $analysisData['improvements'] ?? ($seoSettings->improvements ?? null);
                $actionItems = $analysisData['action_items'] ?? ($seoSettings->action_items ?? null);
                
                // HEP TAM LİSTE GÖSTER - KESME YOK
                $displayLimit = null;
            @endphp
            <!-- OLUMLU YANLAR -->
            @if($strengths)
            <div class="mb-4">
                <h5 class="text-success"><i class="fas fa-check-circle me-2"></i>Güçlü Yanlar</h5>
                <div class="list-group list-group-flush">
                    @if(is_array($strengths))
                    @foreach($strengths as $strength)
                    <div class="list-group-item border-0 px-0 py-2">
                        <i class="fas fa-plus-circle text-success me-2"></i>{{ is_array($strength) ? ($strength['text'] ?? $strength['title'] ?? $strength['description'] ?? json_encode($strength)) : $strength }}
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
            
            <!-- İYİLEŞTİRME ÖNERİLERİ -->
            @if($improvements)
            <div class="mb-4">
                <h5 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>İyileştirme Alanları</h5>
                <div class="list-group list-group-flush">
                    @if(is_array($improvements))
                    @foreach($improvements as $improvement)
                    <div class="list-group-item border-0 px-0 py-2">
                        <i class="fas fa-arrow-up text-warning me-2"></i>{{ is_array($improvement) ? ($improvement['text'] ?? $improvement['title'] ?? $improvement['description'] ?? json_encode($improvement)) : $improvement }}
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
            
            <!-- EYLEM ÖNERİLERİ -->
            @if($actionItems)
            <div>
                <h5 class="text-primary"><i class="fas fa-tasks me-2"></i>Öncelikli Eylemler</h5>
                <div class="list-group list-group-flush">
                    @if(is_array($actionItems))
                    @foreach($actionItems as $index => $item)
                    <div class="list-group-item border-0 px-0 py-2">
                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                        <strong>{{ is_array($item) ? ($item['task'] ?? $item['text'] ?? $item['title'] ?? $item['description'] ?? 'Eylem tanımı bulunamadı') : $item }}</strong>
                        @if(is_array($item) && isset($item['urgency']))
                        <span class="badge bg-danger ms-2">{{ $item['urgency'] }}</span>
                        @endif
                        @if(is_array($item) && isset($item['area']))
                        <br><small class="text-muted">Alan: {{ $item['area'] }}</small>
                        @endif
                        @if(is_array($item) && isset($item['expected_impact']))
                        <small class="text-muted"> • Etki: {{ $item['expected_impact'] }}</small>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- TABLER STYLE SEO ACCORDION --}}
    @if($hasAnalysisResults)
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-line me-2"></i>
                Detaylı SEO Analizi
            </h3>
        </div>
        <div class="card-body">            
            <!-- ACCORDION ANALIZ SEKSIYONLARI -->
            <div class="accordion" id="seoAnalysisAccordion">
                
                <!-- 1. TITLE ANALIZI -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#titleAnalysis" aria-expanded="false">
                            <i class="fas fa-heading me-2"></i>
                            Meta Title Analizi
                            @php
                                $titles = $seoSettings->titles ?? [];
                                $currentMetaTitle = $titles[$lang] ?? '';
                                $displayTitle = !empty($currentMetaTitle) ? $currentMetaTitle : '';
                                $titleLength = strlen($displayTitle);
                                $isEmpty = empty($displayTitle);
                                
                                // Title score hesapla
                                $titleScore = 0;
                                if (!$isEmpty) {
                                    $titleScore += 40; // Var olması için 40 puan
                                    if ($titleLength >= 30 && $titleLength <= 60) {
                                        $titleScore += 40; // İdeal uzunluk için 40 puan
                                    } elseif ($titleLength >= 20) {
                                        $titleScore += 20; // Kabul edilebilir uzunluk için 20 puan
                                    }
                                    if ($titleLength >= 40 && $titleLength <= 55) {
                                        $titleScore += 20; // Perfect uzunluk için bonus 20 puan
                                    }
                                }
                                $titleBadgeClass = $titleScore >= 80 ? 'success' : ($titleScore >= 50 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $titleBadgeClass }} ms-2">{{ $titleScore }}/100</span>
                        </button>
                    </h2>
                    <div id="titleAnalysis" class="accordion-collapse collapse" data-bs-parent="#seoAnalysisAccordion">
                        <div class="accordion-body">
                            @php
                                // JSON titles verilerinden meta title al
                                $titles = $seoSettings->titles ?? [];
                                $currentMetaTitle = $titles[$lang] ?? '';
                                $pageTitle = '';
                                
                                // Page title varsa JSON body'den al
                                if(is_array($model->body ?? null)) {
                                    $bodyContent = collect($model->body)->get($lang, '');
                                } else {
                                    $bodyContent = $model->body ?? '';
                                }
                                
                                // H1 tag'ından page title çıkar
                                if (preg_match('/<h1[^>]*>(.*?)<\/h1>/i', $bodyContent, $matches)) {
                                    $pageTitle = strip_tags($matches[1]);
                                }
                                
                                // Eğer H1 yoksa model title kullan
                                if (empty($pageTitle)) {
                                    if(is_array($model->title ?? null)) {
                                        $pageTitle = collect($model->title)->get($lang, '');
                                    } else {
                                        $pageTitle = $model->title ?? '';
                                    }
                                }
                                
                                $displayTitle = !empty($currentMetaTitle) ? $currentMetaTitle : $pageTitle;
                                $titleLength = strlen($displayTitle);
                                $isFallback = empty($currentMetaTitle) && !empty($pageTitle);
                                $isEmpty = empty($displayTitle);
                            @endphp
                            <div class="alert alert-{{ $isEmpty ? 'danger' : ($isFallback ? 'warning' : 'success') }}">
                                <h6><i class="fas fa-info-circle"></i> Mevcut Durum:</h6>
                                <ul class="mb-2">
                                    <li><strong>Meta Title:</strong> 
                                        @if($isEmpty)
                                            <code class="text-danger">Boş - Meta title yok</code> ❌
                                        @elseif($isFallback)
                                            <code class="text-warning">Otomatik - Sayfa başlığından alındı</code> ⚠️
                                        @else
                                            <code class="text-success">Özel meta title mevcut</code> ✅
                                        @endif
                                    </li>
                                    <li><strong>Kullanılan Başlık:</strong> <code>"{{ $displayTitle }}"</code></li>
                                    <li><strong>Kaynak:</strong> 
                                        <span class="badge bg-{{ $isEmpty ? 'danger' : ($isFallback ? 'warning' : 'success') }}">
                                            @if($isEmpty) Boş @elseif($isFallback) Sayfa Başlığı @else Meta Title @endif
                                        </span>
                                    </li>
                                    <li><strong>Uzunluk:</strong> {{ $titleLength }} karakter
                                        @if($titleLength > 60)
                                            <span class="text-danger">(Çok uzun - kesilecek)</span>
                                        @elseif($titleLength < 30)
                                            <span class="text-warning">(Kısa - geliştirilebilir)</span>
                                        @else
                                            <span class="text-success">(İdeal uzunluk)</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> Acil Öneriler:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>🎯 Özel Meta Title Yazın:</strong>
                                        <div class="bg-light p-2 rounded mt-1">
                                            <code>"Profesyonel Web Tasarım Hizmetleri | Türk Bilişim"</code>
                                            <small class="text-muted d-block">55 karakter - ideal!</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>📏 Uzunluk Kuralları:</strong>
                                        <ul class="small mb-0 mt-1">
                                            <li>✅ 50-60 karakter ideal</li>
                                            <li>⚠️ 70+ karakter kesilir</li>
                                            <li>❌ 30- karakter çok kısa</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-light border">
                                <strong>🚀 Hızlı Düzeltme:</strong>
                                <p class="mb-3">Meta title alanına aşağıdakilerden birini kullan:</p>
                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm text-start d-flex align-items-center gap-2" 
                                            onclick="fillSeoTitle('{{ $lang }}', 'Profesyonel Web Tasarım ve Dijital Çözümler | Türk Bilişim')">
                                        <i class="fas fa-copy"></i>
                                        <span>"Profesyonel Web Tasarım ve Dijital Çözümler | Türk Bilişim"</span>
                                        <small class="text-muted ms-auto">55 karakter</small>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm text-start d-flex align-items-center gap-2" 
                                            onclick="fillSeoTitle('{{ $lang }}', 'Web Tasarım, E-Ticaret ve SEO Hizmetleri | Türk Bilişim')">
                                        <i class="fas fa-copy"></i>
                                        <span>"Web Tasarım, E-Ticaret ve SEO Hizmetleri | Türk Bilişim"</span>
                                        <small class="text-muted ms-auto">52 karakter</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DESCRIPTION ANALIZI -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#descAnalysis" aria-expanded="false">
                            <i class="fas fa-align-left me-2"></i>
                            Meta Description Analizi
                            @php
                                $descriptions = $seoSettings->descriptions ?? [];
                                $currentMetaDescription = $descriptions[$lang] ?? '';
                                $descLength = strlen($currentMetaDescription);
                                $hasDescription = !empty($currentMetaDescription);
                                
                                // Description score hesapla
                                $descScore = 0;
                                if ($hasDescription) {
                                    $descScore += 50; // Var olması için 50 puan
                                    if ($descLength >= 120 && $descLength <= 160) {
                                        $descScore += 50; // İdeal uzunluk için 50 puan
                                    } elseif ($descLength >= 80 && $descLength < 180) {
                                        $descScore += 30; // Kabul edilebilir uzunluk için 30 puan
                                    } elseif ($descLength >= 50) {
                                        $descScore += 10; // En az bir şey yazılmış için 10 puan
                                    }
                                }
                                $descBadgeClass = $descScore >= 80 ? 'success' : ($descScore >= 50 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $descBadgeClass }} ms-2">{{ $descScore }}/100</span>
                        </button>
                    </h2>
                    <div id="descAnalysis" class="accordion-collapse collapse" data-bs-parent="#seoAnalysisAccordion">
                        <div class="accordion-body">
                            @php
                                // JSON descriptions verilerinden meta description al
                                $descriptions = $seoSettings->descriptions ?? [];
                                $currentMetaDescription = $descriptions[$lang] ?? '';
                                $descLength = strlen($currentMetaDescription);
                                $hasDescription = !empty($currentMetaDescription);
                            @endphp
                            <div class="alert alert-{{ $hasDescription ? 'success' : 'warning' }}">
                                @if($hasDescription)
                                    <h6><i class="fas fa-check"></i> Mevcut Durum:</h6>
                                    <ul class="mb-2">
                                        <li><strong>Meta Açıklama:</strong> <code class="text-success">Mevcut</code> ✅</li>
                                        <li><strong>İçerik:</strong> "{{ Str::limit($currentMetaDescription, 100) }}"</li>
                                        <li><strong>Uzunluk:</strong> {{ $descLength }} karakter
                                            @if($descLength > 160)
                                                <span class="text-danger">(Çok uzun - kesilecek)</span>
                                            @elseif($descLength < 120)
                                                <span class="text-warning">(Kısa - daha detaylı olabilir)</span>
                                            @else
                                                <span class="text-success">(İdeal uzunluk)</span>
                                            @endif
                                        </li>
                                    </ul>
                                    @if($descLength >= 120 && $descLength <= 160)
                                    <div class="alert alert-success p-2">
                                        <strong>Güçlü Yanlar:</strong>
                                        <ul class="mb-0 small">
                                            <li>✅ Meta açıklama mevcut ve içerik sunuyor</li>
                                            <li>✅ Uzunluk uygun ({{ $descLength }} karakter)</li>
                                        </ul>
                                    </div>
                                    @endif
                                @else
                                    <h6><i class="fas fa-exclamation-triangle"></i> Eksik Meta Açıklama:</h6>
                                    <ul class="mb-2">
                                        <li><strong>Meta Description:</strong> <code class="text-danger">Boş - Meta açıklama yok</code> ❌</li>
                                        <li><strong>SEO Etkisi:</strong> Arama sonuçlarında görünüm azalır</li>
                                        <li><strong>Önerilen Uzunluk:</strong> 120-160 karakter arası</li>
                                    </ul>
                                @endif
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-arrow-up"></i> İyileştirme Önerileri:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>🎯 Call-to-Action ekleyin:</strong>
                                        <p class="small">"Hemen başlayın", "Ücretsiz teklif alın"</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>🔑 Ana anahtar kelime:</strong>
                                        <p class="small">Açıklamada 1-2 kez geçsin</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. SOCIAL MEDIA ANALIZI -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#socialAnalysis" aria-expanded="false">
                            <i class="fab fa-facebook-f me-2"></i>
                            Sosyal Medya Analizi
                            <span class="badge bg-light text-dark ms-2">60/100</span>
                        </button>
                    </h2>
                    <div id="socialAnalysis" class="accordion-collapse collapse" data-bs-parent="#seoAnalysisAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-list-check"></i> OG Tag Durumu:</h6>
                                    <ul class="list-unstyled">
                                        <li>{{ empty($seoDataCache[$lang]['og_title']) ? '❌' : '✅' }} <code>og:title</code> - {{ empty($seoDataCache[$lang]['og_title']) ? 'eksik' : 'mevcut' }}</li>
                                        <li>{{ empty($seoDataCache[$lang]['og_description']) ? '❌' : '✅' }} <code>og:description</code> - {{ empty($seoDataCache[$lang]['og_description']) ? 'eksik' : 'mevcut' }}</li>
                                        <li>{{ empty($seoDataCache[$lang]['og_image']) ? '❌' : '✅' }} <code>og:image</code> - {{ empty($seoDataCache[$lang]['og_image']) ? 'eksik' : 'mevcut' }}</li>
                                        <li>✅ <code>og:url</code> - mevcut</li>
                                        <li>{{ empty($seoDataCache[$lang]['content_type']) ? '❌' : '✅' }} <code>og:type</code> - {{ empty($seoDataCache[$lang]['content_type']) ? 'eksik' : $seoDataCache[$lang]['content_type'] }}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-rocket"></i> Acil Eylemler:</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <span class="text-muted" style="cursor: pointer;" onclick="scrollToElement('og_image_file_{{ $lang }}')">
                                            🖼️ 1200x630px görsel ekle
                                        </span>
                                        <span class="text-muted" style="cursor: pointer;" onclick="scrollToElement('og_custom_{{ $lang }}')">
                                            📝 OG title & description yaz
                                        </span>
                                        <span class="text-muted" style="cursor: pointer;">
                                            🐦 Twitter Card ekle
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. CONTENT ANALIZI -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentAnalysis" aria-expanded="false">
                            <i class="fas fa-file-alt me-2"></i>
                            İçerik Kalitesi Analizi
                            <span class="badge bg-light text-dark ms-2">70/100</span>
                        </button>
                    </h2>
                    <div id="contentAnalysis" class="accordion-collapse collapse" data-bs-parent="#seoAnalysisAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>📊 İçerik İstatistikleri:</h6>
                                    @php
                                        $bodyContent = '';
                                        if(is_array($model->body ?? null)) {
                                            $bodyContent = collect($model->body)->get($lang, '');
                                        } else {
                                            $bodyContent = $model->body ?? '';
                                        }
                                        $wordCount = str_word_count(strip_tags($bodyContent));
                                        $charCount = strlen(strip_tags($bodyContent));
                                    @endphp
                                    <ul class="list-unstyled">
                                        <li>📝 Kelime sayısı: <strong>{{ $wordCount }} kelime</strong> 
                                            <span class="badge bg-light text-dark">
                                                {{ $wordCount >= 300 ? 'İyi' : ($wordCount >= 150 ? 'Orta' : 'Az') }}
                                            </span>
                                        </li>
                                        <li>📏 Karakter: {{ $charCount }}</li>
                                        <li>🏷️ H1 etiketi: <span class="text-{{ strpos($bodyContent, '<h1') !== false ? 'success' : 'danger' }}">{{ strpos($bodyContent, '<h1') !== false ? '✅ Mevcut' : '❌ Yok' }}</span></li>
                                        <li>🏷️ H2 etiketi: <span class="text-{{ strpos($bodyContent, '<h2') !== false ? 'success' : 'danger' }}">{{ strpos($bodyContent, '<h2') !== false ? '✅ Mevcut' : '❌ Yok' }}</span></li>
                                        <li>🔗 İç linkler: {{ substr_count($bodyContent, '<a') }} adet</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>🎯 İyileştirmeler:</h6>
                                    <div class="alert alert-warning p-2">
                                        <strong>Acil:</strong>
                                        <ul class="small mb-0">
                                            @if($wordCount < 300)
                                            <li>En az 300 kelime yazın (şu an: {{ $wordCount }})</li>
                                            @endif
                                            @if(strpos($bodyContent, '<h1') === false)
                                            <li>H1 başlığı ekleyin</li>
                                            @endif
                                            @if(strpos($bodyContent, '<h2') === false)
                                            <li>2-3 H2 alt başlık kullanın</li>
                                            @endif
                                            <li>Paragrafları bölerek düzenleyin</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. TEKNIK SEO ANALIZI -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technicalAnalysis" aria-expanded="false">
                            <i class="fas fa-cogs me-2"></i>
                            Teknik SEO Analizi
                            <span class="badge bg-light text-dark ms-2">50/100</span>
                        </button>
                    </h2>
                    <div id="technicalAnalysis" class="accordion-collapse collapse" data-bs-parent="#seoAnalysisAccordion">
                        <div class="accordion-body">
                            <div class="alert alert-light border">
                                <h6>🚨 Kritik Eksiklikler:</h6>
                                <ul class="mb-2">
                                    <li>{{ empty($seoDataCache[$lang]['schema_markup']) ? '❌' : '✅' }} Schema markup {{ empty($seoDataCache[$lang]['schema_markup']) ? 'yok' : 'mevcut' }}</li>
                                    <li>❌ Alt etiketleri kontrol edilmeli</li>
                                    <li>{{ empty($seoDataCache[$lang]['canonical_url']) ? '❌' : '✅' }} Canonical URL {{ empty($seoDataCache[$lang]['canonical_url']) ? 'belirlenmemiş' : 'mevcut' }}</li>
                                    <li>🔍 XML sitemap durumu kontrol edilmeli</li>
                                </ul>
                                <span class="text-muted">
                                    🔧 Manuel düzeltme gerekli
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- ÖZET EYLEM PLANI -->
            <div class="mt-4">
                <div class="alert alert-primary">
                    <h5><i class="fas fa-bullseye"></i> Öncelikli Eylem Planı</h5>
                    <ol class="mb-0">
                        <li><strong>Meta title {{ empty($seoDataCache[$lang]['seo_title']) ? 'ekle' : 'iyileştir' }}</strong> <span class="badge bg-light text-dark">KRİTİK</span></li>
                        <li><strong>İçeriği {{ $wordCount < 300 ? ($wordCount . ' kelimeden 300+\'e çıkar') : 'yapılandır' }}</strong> <span class="badge bg-light text-dark">{{ $wordCount < 300 ? 'KRİTİK' : 'YÜKSEK' }}</span></li>
                        <li><strong>{{ strpos($bodyContent, '<h1') === false ? 'H1 başlığı koy' : 'H1 başlığını optimize et' }}</strong> <span class="badge bg-light text-dark">KRİTİK</span></li>
                        <li><strong>OG image {{ empty($seoDataCache[$lang]['og_image']) ? 'ekle' : 'optimize et' }}</strong> <span class="badge bg-light text-dark">YÜKSEK</span></li>
                        <li><strong>Schema markup ekle</strong> <span class="badge bg-light text-dark">ORTA</span></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TEMEL SEO ALANLARI --}}
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                Temel SEO Ayarları ({{ strtoupper($lang) }})
                <small class="opacity-75 ms-2">Mutlaka doldurulması gerekenler</small>
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
                                {{ __('page::admin.seo_title') }} ({{ strtoupper($lang) }})
                                <span class="character-counter float-end" id="title_counter_{{ $lang }}">
                                    <small class="text-muted">0/60</small>
                                </span>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
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
                                {{ __('page::admin.seo_description') }} ({{ strtoupper($lang) }})
                                <span class="character-counter float-end" id="description_counter_{{ $lang }}">
                                    <small class="text-muted">0/160</small>
                                </span>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
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
                            <small class="text-muted ms-2">Schema.org + OpenGraph</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
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
                                <small class="text-muted ms-2">Manuel giriş</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
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
                        <span class="text-muted small fw-bold">1</span>
                        <span class="text-muted small">Düşük</span>
                        <input type="range" 
                               wire:model="seoDataCache.{{ $lang }}.priority_score"
                               class="form-range flex-grow-1 mx-2"
                               min="1" 
                               max="10" 
                               step="1"
                               value="{{ $seoDataCache[$lang]['priority_score'] ?? 5 }}"
                               oninput="onManualPriorityChange(this, '{{ $lang }}')"
                               {{ $disabled ? 'disabled' : '' }}>
                        <span class="text-muted small">Kritik</span>
                        <span class="text-muted small fw-bold">10</span>
                    </div>
                    <div class="form-text mt-2 priority-examples">
                        <small class="text-muted">
                            
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
    <h6 class="text-muted mb-3">
        Sosyal Medya & Schema Ayarları
    </h6>

    {{-- SOSYAL MEDYA AYARLARI --}}
    <div class="card border-success mb-4" style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem !important; transition: border-radius 0.15s;">
        <div class="card-header bg-success text-white" style="--tblr-success: #28a745 !important; --tblr-success-rgb: 40, 167, 69 !important; border-radius: 0.25rem 0.25rem 0px 0px !important;">
            <h6 class="mb-0">
                Sosyal Medya Paylaşım Ayarları
                <small class="opacity-75 ms-2">Facebook, LinkedIn, WhatsApp için</small>
            </h6>
        </div>
        <div class="card-body" style="border-radius: 0px 0px 0.25rem 0.25rem !important;">
            @if($lang === ($availableLanguages[0] ?? 'tr'))
            <div class="row">
                {{-- Sosyal Medya Görseli --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Sosyal Medya Resmi
                        <small class="text-muted ms-2">1200x630 önerilen</small>
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
                        <small class="text-muted">
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
                            <small class="text-muted">
                                Kapalıysa yukarıdaki SEO verilerini kullanır (otomatik sistem)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                
                <strong>Bilgi:</strong> Sosyal medya ayarları tüm diller için ortaktır. Ana dil ({{ strtoupper($availableLanguages[0] ?? 'tr') }}) sekmesinden düzenleyebilirsiniz.
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
                        <div class="form-floating" style="border-radius: 0.25rem !important; overflow: hidden !important;">
                            <input type="text" 
                                   wire:model="seoDataCache.{{ $lang }}.og_title"
                                   class="form-control seo-no-enter"
                                   placeholder="Facebook/LinkedIn'de görünecek özel başlık"
                                   maxlength="60"
                                   style="border-radius: 0.25rem !important;"
                                   {{ $disabled ? 'disabled' : '' }}>
                            <label>
                                Sosyal Medya Başlığı
                                <small class="text-muted ms-2">Maksimum 60 karakter</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
                                    Sosyal medya paylaşımlarında görünecek başlık
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- OG Description --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating" style="border-radius: 0.25rem !important; overflow: hidden !important;">
                            <textarea wire:model="seoDataCache.{{ $lang }}.og_description"
                                      class="form-control seo-no-enter"
                                      placeholder="Facebook/LinkedIn'de görünecek özel açıklama"
                                      style="height: 100px; resize: vertical; border-radius: 0.25rem !important;"
                                      maxlength="155"
                                      {{ $disabled ? 'disabled' : '' }}></textarea>
                            <label>
                                Sosyal Medya Açıklaması
                                <small class="text-muted ms-2">Maksimum 155 karakter</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
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
                <small class="opacity-75 ms-2">Yazar ve içerik metadata</small>
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
                            <small class="text-muted ms-2">İçerik yazarı</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
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
                            <small class="text-muted ms-2">Yazarın profil sayfası</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                Yazarın profil sayfası veya kişisel web sitesi
                            </small>
                        </div>
                    </div>
                </div>

            </div>
            @else
            <div class="alert alert-info">
                
                <strong>Bilgi:</strong> İçerik bilgileri tüm diller için ortaktır. Ana dil ({{ strtoupper($availableLanguages[0] ?? 'tr') }}) sekmesinden düzenleyebilirsiniz.
            </div>
            @endif
        </div>
    </div>


</div>
@endforeach

@if(!$disabled)
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
                    small.className = 'text-muted';
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