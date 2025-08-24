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
    
    // Kaydedilmiş analiz sonuçları var mı kontrol et (yeni alanda)
    $hasAnalysisResults = $seoSettings && $seoSettings->analysis_results;
    $analysisResults = $hasAnalysisResults ? $seoSettings->analysis_results : null;
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
        <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" 
                                    class="btn ai-seo-comprehensive-btn" 
                                    data-seo-feature="seo-comprehensive-audit"
                                    data-language="{{ $lang }}"
                                    style="background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 500; box-shadow: 0 2px 8px rgba(238, 90, 36, 0.3); transition: all 0.3s ease;">
                                <i class="fas fa-chart-bar me-1"></i>
                                {{ $hasAnalysisResults ? 'Verileri Yenile' : 'SEO Analizi' }}
                            </button>
                            <button type="button" 
                                    class="btn seo-generator-btn"
                                    data-action="generate-seo"
                                    data-language="{{ $lang }}"
                                    style="background: linear-gradient(45deg, #4ecdc4, #44a08d); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-weight: 500; box-shadow: 0 2px 8px rgba(68, 160, 141, 0.3); transition: all 0.3s ease;">
                                <i class="fas fa-magic me-1"></i>
                                SEO Oluştur
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

    {{-- KAYDEDILMIŞ ANALIZ SONUÇLARI --}}
    @if($hasAnalysisResults)
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line me-2"></i>
                Kapsamlı SEO Analizi
                <small class="ms-2 opacity-75">{{ $seoSettings->analysis_date ? \Carbon\Carbon::parse($seoSettings->analysis_date)->diffForHumans() : 'Yakın zamanda' }}</small>
            </h3>
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
                        
                        {{-- AI Meta Title Button --}}
                        @if(!$disabled)
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm ai-seo-btn position-absolute"
                                style="top: 8px; right: 8px; z-index: 5;"
                                data-feature="seo-meta-title-generator"
                                data-target="seo_title"
                                data-language="{{ $lang }}"
                                title="AI ile Meta Title üret">
                            <i class="ti ti-sparkles" style="font-size: 12px;"></i>
                        </button>
                        @endif
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
                        
                        {{-- AI Meta Description Button --}}
                        @if(!$disabled)
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm ai-seo-btn position-absolute"
                                style="top: 8px; right: 8px; z-index: 5;"
                                data-feature="seo-meta-description-generator"
                                data-target="seo_description"
                                data-language="{{ $lang }}"
                                title="AI ile Meta Description üret">
                            <i class="ti ti-file-text" style="font-size: 12px;"></i>
                        </button>
                        @endif
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
                        <span class="badge bg-primary priority-badge" style="position: relative;">
                            <span class="priority-value">{{ $seoDataCache[$lang]['priority_score'] ?? 5 }}</span>/10 - <span class="priority-text">
                                @php
                                    $priorityValue = $seoDataCache[$lang]['priority_score'] ?? 5;
                                    if ($priorityValue >= 1 && $priorityValue <= 3) {
                                        echo 'Düşük';
                                    } elseif ($priorityValue >= 4 && $priorityValue <= 6) {
                                        echo 'Orta';
                                    } elseif ($priorityValue >= 7 && $priorityValue <= 8) {
                                        echo 'Yüksek';
                                    } else {
                                        echo 'Kritik';
                                    }
                                @endphp
                            </span>
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
                            <input type="checkbox" 
                                   wire:model="seoDataCache.{{ $lang }}.og_custom_enabled"
                                   id="og_custom_{{ $lang }}"
                                   onchange="toggleOgCustomFields(this, '{{ $lang }}')"
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
                 style="display: none; max-height: none; overflow: visible;">
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
    window.currentPageId = {{ $pageId ?? 'null' }};
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
        
        // Update priority text based on value
        let priorityLabel = '';
        
        if (value >= 1 && value <= 3) {
            priorityLabel = 'Düşük';
        } else if (value >= 4 && value <= 6) {
            priorityLabel = 'Orta';
        } else if (value >= 7 && value <= 8) {
            priorityLabel = 'Yüksek';
        } else if (value >= 9 && value <= 10) {
            priorityLabel = 'Kritik';
        }
        
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
    
</script>

{{-- AI SEO Integration JavaScript --}}
<script src="{{ asset('assets/js/ai-seo-integration.js') }}"></script>
@endif