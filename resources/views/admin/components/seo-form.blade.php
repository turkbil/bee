{{-- Modern AI-Powered SEO Form Component --}}
<div class="modern-seo-container">
    @if($model)
        @php
            $seoSettings = $model->seoSetting ?? null;
            $seoScore = $seoSettings->seo_score ?? 0;
        @endphp
        
        {{-- SEO Score Header --}}
        <div class="seo-score-header mb-4">
            <div class="card gradient-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="seo-score-display">
                                <h3 class="text-white mb-2">
                                    <i class="fas fa-robot me-2"></i>AI SEO Skoru
                                </h3>
                                <div class="score-circle">
                                    <div class="progress-ring">
                                        <svg class="progress-ring-svg" width="120" height="120">
                                            <circle class="progress-ring-circle-bg" stroke="#ffffff33" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
                                            <circle class="progress-ring-circle" stroke="#00d4aa" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"
                                                    style="stroke-dasharray: 327; stroke-dashoffset: {{ 327 - (327 * ($seoScore / 100)) }};"/>
                                        </svg>
                                        <div class="score-text">
                                            <span class="score-number">{{ $seoScore }}</span>
                                            <span class="score-label">/ 100</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ai-actions">
                                <h5 class="text-white mb-3">
                                    <i class="fas fa-magic me-2"></i>AI Asistan
                                </h5>
                                <div class="d-grid gap-2">
                                    <button type="button" 
                                            wire:click="analyzeSeo"
                                            wire:loading.attr="disabled"
                                            class="btn btn-ai-primary">
                                        <i class="fas fa-search me-2"></i>
                                        <span wire:loading.remove wire:target="analyzeSeo">Hızlı Analiz</span>
                                        <span wire:loading wire:target="analyzeSeo">
                                            <span class="spinner-border spinner-border-sm me-2"></span>Analiz Ediliyor...
                                        </span>
                                    </button>
                                    
                                    <button type="button" 
                                            wire:click="generateSeoSuggestions"
                                            wire:loading.attr="disabled"
                                            class="btn btn-ai-secondary">
                                        <i class="fas fa-lightbulb me-2"></i>
                                        <span wire:loading.remove wire:target="generateSeoSuggestions">AI Önerileri</span>
                                        <span wire:loading wire:target="generateSeoSuggestions">
                                            <span class="spinner-border spinner-border-sm me-2"></span>Üretiliyor...
                                        </span>
                                    </button>
                                    
                                    <button type="button" 
                                            wire:click="autoOptimizeSeo"
                                            wire:loading.attr="disabled"
                                            class="btn btn-ai-premium">
                                        <i class="fas fa-wand-magic-sparkles me-2"></i>
                                        <span wire:loading.remove wire:target="autoOptimizeSeo">Otomatik Optimize</span>
                                        <span wire:loading wire:target="autoOptimizeSeo">
                                            <span class="spinner-border spinner-border-sm me-2"></span>Optimize Ediliyor...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- AI Analysis Results --}}
        @if(isset($aiAnalysis) && $aiAnalysis)
        <div class="ai-analysis-results mb-4">
            @if(app()->environment('local'))
                <div class="alert alert-info small mb-2">
                    <strong>Debug:</strong> AI Analysis Keys: {{ implode(', ', array_keys($aiAnalysis)) }}
                </div>
            @endif
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>AI Analiz Sonuçları
                        <span class="badge badge-outline ms-2">{{ $aiAnalysis['overall_score'] ?? 0 }}/100</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($aiAnalysis['priority_actions']) && is_array($aiAnalysis['priority_actions']))
                        <div class="priority-actions mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Öncelikli İyileştirmeler
                                </h6>
                                <span class="badge badge-outline rounded-pill">{{ count($aiAnalysis['priority_actions']) }} öneri</span>
                            </div>
                            <div class="action-list">
                                @foreach($aiAnalysis['priority_actions'] as $index => $action)
                                    <div class="action-item enhanced">
                                        <div class="action-badge gradient">{{ $index + 1 }}</div>
                                        <div class="action-content">
                                            <div class="action-text">{{ is_string($action) ? $action : 'Geçersiz öneri' }}</div>
                                            <div class="action-meta">
                                                <small class="text-muted">
                                                    <i class="fas fa-lightbulb me-1"></i>AI Önerisi
                                                </small>
                                            </div>
                                        </div>
                                        <div class="action-status">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <div class="row">
                        @if(isset($aiAnalysis['suggested_title']) && is_string($aiAnalysis['suggested_title']))
                            <div class="col-md-6 mb-3">
                                <div class="suggestion-card modern">
                                    <div class="suggestion-header">
                                        <h6 class="suggestion-title">
                                            <i class="fas fa-heading me-2"></i>Önerilen Başlık
                                        </h6>
                                        <span class="suggestion-badge">AI</span>
                                    </div>
                                    <p class="suggestion-text">{{ $aiAnalysis['suggested_title'] }}</p>
                                    <div class="suggestion-actions">
                                        <button type="button" 
                                                wire:click="applySuggestion('title', {{ json_encode($aiAnalysis['suggested_title']) }})"
                                                class="btn btn-outline btn-sm">
                                            <i class="fas fa-check me-1"></i>Uygula
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(isset($aiAnalysis['suggested_description']) && is_string($aiAnalysis['suggested_description']))
                            <div class="col-md-6 mb-3">
                                <div class="suggestion-card modern">
                                    <div class="suggestion-header">
                                        <h6 class="suggestion-title">
                                            <i class="fas fa-align-left me-2"></i>Önerilen Açıklama
                                        </h6>
                                        <span class="suggestion-badge">AI</span>
                                    </div>
                                    <p class="suggestion-text">{{ $aiAnalysis['suggested_description'] }}</p>
                                    <div class="suggestion-actions">
                                        <button type="button" 
                                                wire:click="applySuggestion('description', {{ json_encode($aiAnalysis['suggested_description']) }})"
                                                class="btn btn-outline btn-sm">
                                            <i class="fas fa-check me-1"></i>Uygula
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SEO Fields - Single Language (uses page management language switcher) --}}
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            <small>SEO ayarları şu anda seçili dil için düzenleniyor. Dil değiştirmek için sağ üstteki dil butonlarını kullanın.</small>
        </div>
            
        {{-- SEO Form Fields in 2-Column Layout --}}
        <div class="seo-form-content">
            <div class="row">
                {{-- Left Column --}}
                <div class="col-md-6">
                    {{-- Title Field with Character Counter --}}
                    <div class="form-group mb-4">
                        <div class="form-floating input-with-counter">
                            <input type="text" 
                                   wire:model.defer="seoData.title"
                                   class="form-control @error('seoData.title') is-invalid @enderror"
                                   placeholder="SEO optimizasyonlu başlık yazın..."
                                   id="seo-title"
                                   maxlength="60">
                            <label for="seo-title">
                                <i class="fas fa-heading me-2"></i>SEO Başlık
                            </label>
                            <div class="character-counter">
                                <span class="counter-text" id="title-counter">
                                    {{ strlen($seoData['title'] ?? '') }}/60
                                </span>
                                <div class="counter-bar">
                                    <div class="counter-progress" id="title-progress"
                                         style="width: {{ min(100, (strlen($seoData['title'] ?? '') / 60) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @error('seoData.title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ideal: 50-60 karakter. Arama motorlarında görünecek başlık.
                        </small>
                    </div>

                    {{-- Description Field with Character Counter --}}
                    <div class="form-group mb-4">
                        <div class="form-floating input-with-counter">
                            <textarea wire:model.defer="seoData.description" 
                                      class="form-control @error('seoData.description') is-invalid @enderror" 
                                      id="seo-description"
                                      rows="3"
                                      style="height: 100px;"
                                      placeholder="Çekici ve bilgilendirici açıklama yazın..."
                                      maxlength="160"></textarea>
                            <label for="seo-description">
                                <i class="fas fa-align-left me-2"></i>Meta Açıklama
                            </label>
                            <div class="character-counter">
                                <span class="counter-text" id="description-counter">
                                    {{ strlen($seoData['description'] ?? '') }}/160
                                </span>
                                <div class="counter-bar">
                                    <div class="counter-progress" id="description-progress"
                                         style="width: {{ min(100, (strlen($seoData['description'] ?? '') / 160) * 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                        @error('seoData.description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ideal: 120-160 karakter. Arama sonuçlarında görünecek açıklama.
                        </small>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="col-md-6">
                    {{-- Slug Field --}}
                    <div class="form-group mb-4">
                        <div class="form-floating">
                            <input type="text" 
                                   wire:model.defer="slugData.{{ $currentLanguage }}"
                                   class="form-control"
                                   id="seo-slug"
                                   placeholder="sayfa-url-adresi">
                            <label for="seo-slug">
                                <i class="fas fa-link me-2"></i>Slug (URL)
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Boş bırakılırsa başlıktan otomatik oluşturulur.
                        </small>
                    </div>

                    {{-- Keywords Field --}}
                    <div class="form-group mb-4">
                        <label class="form-label">
                            <i class="fas fa-tags me-2"></i>Anahtar Kelimeler
                        </label>
                        <div class="input-group mb-3">
                            <input type="text" 
                                   id="new-keyword-input"
                                   class="form-control" 
                                   placeholder="Anahtar kelime yazın ve Enter'a basın...">
                            <button type="button" 
                                    id="add-keyword-btn"
                                    class="btn btn-outline-secondary border">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-2" id="keywords-display">
                            {{-- Keywords display area --}}
                            
                            @php
                                $seoDataSafe = $seoData ?? [];
                                $keywordsRaw = $seoDataSafe['keywords'] ?? [];
                                $processedKeywords = is_array($keywordsRaw) 
                                    ? $keywordsRaw 
                                    : (is_string($keywordsRaw) 
                                        ? json_decode($keywordsRaw, true) ?? []
                                        : []);
                            @endphp
                            
                            @if(!empty($processedKeywords) && is_array($processedKeywords))
                                @foreach($processedKeywords as $keyword)
                                    <span class="badge badge-outline d-inline-flex align-items-center border" style="font-size: 1rem; padding: 8px 12px;" data-keyword="{{ $keyword }}">
                                        {{ $keyword }}
                                        <button type="button" class="btn-close ms-2 remove-keyword" 
                                                style="font-size: 0.7em; padding: 4px;" 
                                                aria-label="Kaldır"></button>
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Henüz anahtar kelime eklenmemiş
                                </span>
                            @endif
                        </div>
                        <!-- Hidden input for Livewire sync -->
                        <input type="hidden" wire:model.defer="seoData.keywords" id="keywords-hidden">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            3-7 anahtar kelime ideal. Kelime yazıp Enter'a basın.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Advanced SEO Settings --}}
        <div class="advanced-seo mt-4">
            <h5 class="mb-4 fw-bold">
                <i class="fas fa-cogs me-2"></i>Gelişmiş SEO Ayarları
            </h5>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="text" 
                               wire:model="seoData.focus_keyword"
                               class="form-control"
                               id="focus-keyword"
                               placeholder="Odaklanılacak ana kelime">
                        <label for="focus-keyword">
                            <i class="fas fa-bullseye me-2"></i>Ana Anahtar Kelime
                        </label>
                    </div>
                    <small class="form-text text-muted">SEO analizi için temel kelime</small>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-floating">
                        <input type="url" 
                               wire:model="seoData.canonical_url"
                               class="form-control"
                               id="canonical-url"
                               placeholder="https://example.com/sayfa">
                        <label for="canonical-url">
                            <i class="fas fa-link me-2"></i>Canonical URL
                        </label>
                    </div>
                    <small class="form-text text-muted">Boş bırakılırsa otomatik oluşturulur</small>
                </div>
            </div>
            
            {{-- Robots Settings --}}
            <h6 class="fw-bold mb-3">
                <i class="fas fa-robot me-2"></i>Arama Motoru Ayarları
            </h6>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth">
                        <input type="checkbox" 
                               wire:model="seoData.robots_index"
                               id="robots_index">
                        <div class="state p-success p-on">
                            <label for="robots_index">
                                İndekslensin
                            </label>
                        </div>
                        <div class="state p-danger p-off">
                            <label for="robots_index">
                                İndekslenmesin
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth">
                        <input type="checkbox" 
                               wire:model="seoData.robots_follow"
                               id="robots_follow">
                        <div class="state p-success p-on">
                            <label for="robots_follow">
                                Linkler Takip Edilsin
                            </label>
                        </div>
                        <div class="state p-danger p-off">
                            <label for="robots_follow">
                                Linkler Takip Edilmesin
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth">
                        <input type="checkbox" 
                               wire:model="seoData.robots_archive"
                               id="robots_archive">
                        <div class="state p-success p-on">
                            <label for="robots_archive">
                                Arşivlenebilir
                            </label>
                        </div>
                        <div class="state p-danger p-off">
                            <label for="robots_archive">
                                Arşivlenemesin
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="pretty p-default p-curve p-toggle p-smooth">
                        <input type="checkbox" 
                               wire:model="seoData.auto_optimize"
                               id="auto_optimize">
                        <div class="state p-success p-on">
                            <label for="auto_optimize">
                                Otomatik Optimize
                            </label>
                        </div>
                        <div class="state p-danger p-off">
                            <label for="auto_optimize">
                                Manuel Optimize
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Robots Settings Explanation --}}
            <div class="alert alert-info border-0 mt-3">
                <div class="d-flex">
                    <i class="fas fa-info-circle me-3 mt-1"></i>
                    <div>
                        <h6 class="mb-2">Bu ayarlar ne işe yarar?</h6>
                        <ul class="mb-0 small">
                            <li><strong>İndekslensin:</strong> Sayfanızın Google'da görünmesini istiyorsanız açık bırakın</li>
                            <li><strong>Linkler Takip Edilsin:</strong> Sayfanızdaki linklerin SEO değeri taşımasını istiyorsanız açık bırakın</li>
                            <li><strong>Arşivlenebilir:</strong> Sayfanızın Google Cache'de saklanmasını istiyorsanız açık bırakın</li>
                            <li><strong>Otomatik Optimize:</strong> AI'ın SEO ayarlarınızı otomatik iyileştirmesini istiyorsanız açık bırakın</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- SEO Save Button kaldırıldı - parent form ile entegre --}}
        </div>

    @else
        <div class="alert alert-warning border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">Model Bulunamadı</h5>
                    <p class="mb-0">SEO ayarları görüntülenemiyor. Lütfen önce içeriği kaydedin.</p>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Modern CSS Styles --}}
    <style>
.modern-seo-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.gradient-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
}

.seo-score-display {
    position: relative;
}

.score-circle {
    position: relative;
    display: inline-block;
}

.progress-ring-svg {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dashoffset 1s ease-in-out;
}

.score-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
}

.score-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.score-label {
    font-size: 0.9rem;
    opacity: 0.8;
}

.btn-ai-primary {
    background: linear-gradient(45deg, #00d4aa, #00c4f0);
    border: none;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.btn-ai-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 170, 0.3);
    color: white;
}

.btn-ai-secondary {
    background: linear-gradient(45deg, #ffd200, #ff8c00);
    border: none;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.btn-ai-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 140, 0, 0.3);
    color: white;
}

.btn-ai-premium {
    background: linear-gradient(45deg, #667eea, #764ba2);
    border: none;
    color: white;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.btn-ai-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(118, 75, 162, 0.3);
    color: white;
}

.custom-tabs .nav-link {
    border: none;
    border-radius: 12px 12px 0 0;
    padding: 12px 20px;
    margin-right: 4px;
    background: #f8f9fa;
    color: #6c757d;
    font-weight: 600;
    transition: all 0.3s ease;
}

.custom-tabs .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.input-with-counter {
    position: relative;
}

.character-counter {
    position: absolute;
    right: 12px;
    top: 8px;
    transform: none;
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 2;
}

/* Textarea için counter position ayarı */
.form-floating textarea ~ .character-counter {
    top: 8px;
    transform: none;
}

.character-counter .counter-text {
    font-size: 0.75rem;
    color: #6c757d;
    font-weight: normal;
    min-width: 40px;
}

.counter-bar {
    width: 40px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.counter-progress {
    height: 100%;
    background: linear-gradient(90deg, #00d4aa, #667eea);
    border-radius: 2px;
    transition: width 0.3s ease;
}

.keywords-container {
    border-radius: 12px;
    padding: 16px;
}

.keyword-input-wrapper {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.keyword-tag {
    background: linear-gradient(45deg, #667eea, #764ba2);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.keyword-tag:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.keyword-remove {
    background: none;
    border: none;
    color: white;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    transition: all 0.2s ease;
}

.keyword-remove:hover {
    background: rgba(255, 255, 255, 0.2);
}

.action-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    border-radius: 16px;
    border-left: 4px solid var(--bs-warning);
    background: rgba(255, 193, 7, 0.05);
    border: 1px solid rgba(255, 193, 7, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.15);
    border-color: rgba(255, 193, 7, 0.3);
}

.action-item.enhanced {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.08) 0%, rgba(0, 212, 170, 0.08) 100%);
}

.action-badge {
    background: linear-gradient(135deg, #ffc107, #ff8c00);
    color: #fff;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.action-badge.gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.action-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.action-text {
    font-weight: 500;
    color: #2c3e50;
    line-height: 1.4;
    font-size: 0.95rem;
}

.action-meta {
    display: flex;
    align-items: center;
    gap: 8px;
}

.action-status {
    opacity: 0.6;
    transition: opacity 0.3s ease;
}

.action-item:hover .action-status {
    opacity: 1;
}

.suggestion-card {
    border-radius: 16px;
    padding: 20px;
    height: 100%;
    border: 1px solid rgba(0, 123, 255, 0.1);
    background: rgba(0, 123, 255, 0.02);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.suggestion-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 123, 255, 0.1);
    border-color: rgba(0, 123, 255, 0.2);
}

.suggestion-card.modern {
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.03) 0%, rgba(40, 167, 69, 0.03) 100%);
}

.suggestion-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.suggestion-title {
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
    flex: 1;
    color: #495057;
}

.suggestion-badge {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.suggestion-badge.success {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.suggestion-text {
    color: #495057;
    line-height: 1.5;
    margin-bottom: 16px;
    font-size: 0.9rem;
    padding: 12px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.suggestion-actions {
    text-align: right;
}

.modern-btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.modern-btn:hover {
    transform: translateY(-1px);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.card {
    border-radius: 16px;
}

.card-header {
    border-radius: 16px 16px 0 0 !important;
    border-bottom: 1px solid var(--bs-border-color);
}

@media (max-width: 768px) {
    .score-circle {
        transform: scale(0.8);
    }
    
    .ai-actions .d-grid {
        gap: 8px !important;
    }
    
    .custom-tabs .nav-link {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
}
    </style>

    {{-- JavaScript for Enhanced Interactions --}}
    <script>
document.addEventListener('livewire:initialized', function() {
    // Choices.js initialization for keywords
    // Keywords management with jQuery for better performance
    function initializeKeywords() {
        // Eğer zaten initialize edilmişse tekrar çalıştırma
        if (window.keywordsInitialized) {
            // Keywords already initialized
            return;
        }
        
        // Initialize keywords system
        window.keywordsInitialized = true;
        
        let currentKeywords = {!! json_encode(
            isset($seoData) && is_array($seoData) && isset($seoData['keywords']) 
                ? (is_array($seoData['keywords']) 
                    ? array_values($seoData['keywords']) 
                    : (is_string($seoData['keywords']) 
                        ? array_values(json_decode($seoData['keywords'], true) ?? [])
                        : []))
                : []
        ) !!};
        
        // Keywords data loaded from server
        
        function updateKeywordsDisplay() {
            const keywordsDisplay = document.getElementById('keywords-display');
            const hiddenInput = document.getElementById('keywords-hidden');
            
            // Update keywords display elements
            
            // Update display
            keywordsDisplay.innerHTML = '';
            currentKeywords.forEach(keyword => {
                const badge = document.createElement('span');
                badge.className = 'badge badge-outline d-inline-flex align-items-center border';
                badge.style.fontSize = '1rem';
                badge.style.padding = '8px 12px';
                badge.setAttribute('data-keyword', keyword);
                badge.innerHTML = `
                    ${keyword}
                    <button type="button" class="btn-close ms-2 remove-keyword" 
                            style="font-size: 0.7em; padding: 4px;" 
                            aria-label="Kaldır"></button>
                `;
                keywordsDisplay.appendChild(badge);
            });
            
            // Update hidden input for Livewire sync
            hiddenInput.value = JSON.stringify(currentKeywords);
            hiddenInput.dispatchEvent(new Event('input'));
            
            // @this.set() kaldırıldı - sonsuz döngü yaratıyordu
            // Hidden input sync yeterli
            
            // Keywords display updated successfully
        }
        
        function addKeyword() {
            const input = document.getElementById('new-keyword-input');
            const keyword = input.value.trim();
            
            if (keyword && !currentKeywords.includes(keyword) && currentKeywords.length < 10) {
                currentKeywords.push(keyword);
                input.value = '';
                updateKeywordsDisplay();
                
                // Keyword added successfully
            } else {
                // Keyword could not be added (duplicate or limit reached)
            }
        }
        
        // Add keyword button click
        document.getElementById('add-keyword-btn').addEventListener('click', addKeyword);
        
        // Add keyword on Enter
        document.getElementById('new-keyword-input').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                console.log('⌨️ Enter tuşuna basıldı, keyword ekleniyor...');
                addKeyword();
            }
        });
        
        // Remove keyword click (event delegation)
        document.getElementById('keywords-display').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-keyword')) {
                const badge = e.target.closest('.badge');
                const keyword = badge.getAttribute('data-keyword');
                const index = currentKeywords.indexOf(keyword);
                
                if (index > -1) {
                    currentKeywords.splice(index, 1);
                    updateKeywordsDisplay();
                }
            }
        });
        
        // Initial display update
        updateKeywordsDisplay();
    }
    
    // jQuery Character counting with color coding
    function setupCharacterCounting() {
        // SEO Title field
        const titleInput = document.getElementById('seo-title');
        const titleCounter = document.getElementById('title-counter');
        const titleProgress = document.getElementById('title-progress');
        
        if (titleInput && titleCounter && titleProgress) {
            function updateTitleCount() {
                const maxLength = 60;
                const currentLength = titleInput.value.length;
                const percentage = (currentLength / maxLength) * 100;
                
                titleCounter.textContent = `${currentLength}/${maxLength}`;
                titleProgress.style.width = `${Math.min(100, percentage)}%`;
                
                // Color coding for title
                if (currentLength < 30) {
                    titleProgress.style.background = 'linear-gradient(90deg, #ffc107, #fd7e14)';
                    titleCounter.style.color = '#ffc107';
                } else if (currentLength <= 60) {
                    titleProgress.style.background = 'linear-gradient(90deg, #00d4aa, #667eea)';
                    titleCounter.style.color = '#00d4aa';
                } else {
                    titleProgress.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
                    titleCounter.style.color = '#dc3545';
                }
            }
            
            titleInput.addEventListener('input', updateTitleCount);
            titleInput.addEventListener('keyup', updateTitleCount);
            updateTitleCount();
        }
        
        // SEO Description field
        const descInput = document.getElementById('seo-description');
        const descCounter = document.getElementById('description-counter');
        const descProgress = document.getElementById('description-progress');
        
        if (descInput && descCounter && descProgress) {
            function updateDescCount() {
                const maxLength = 160;
                const currentLength = descInput.value.length;
                const percentage = (currentLength / maxLength) * 100;
                
                descCounter.textContent = `${currentLength}/${maxLength}`;
                descProgress.style.width = `${Math.min(100, percentage)}%`;
                
                // Color coding for description
                if (currentLength < 120) {
                    descProgress.style.background = 'linear-gradient(90deg, #ffc107, #fd7e14)';
                    descCounter.style.color = '#ffc107';
                } else if (currentLength <= 160) {
                    descProgress.style.background = 'linear-gradient(90deg, #00d4aa, #667eea)';
                    descCounter.style.color = '#00d4aa';
                } else {
                    descProgress.style.background = 'linear-gradient(90deg, #dc3545, #c82333)';
                    descCounter.style.color = '#dc3545';
                }
            }
            
            descInput.addEventListener('input', updateDescCount);
            descInput.addEventListener('keyup', updateDescCount);
            updateDescCount();
        }
    }
    
    setupCharacterCounting();
    initializeKeywords();
    
    // Listen for language switches from page management
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('language-switch-btn')) {
            const language = e.target.getAttribute('data-language');
            // Language switch button clicked
            
            if (language) {
                // Call SEO component language switch
                // Notify SEO component about language change
                @this.call('switchLanguage', language).then(function(result) {
                    // SEO language switch completed successfully
                    // Keywords automatically updated, no re-init needed
                }).catch(function(error) {
                    // SEO language switch failed - handle error silently
                });
            }
        }
    });
    
    // Keywords are now handled directly by Livewire - no event listeners needed
    
    // Livewire morph.updated hook tamamen kaldırıldı
    // Çünkü @this.set() ile sonsuz döngü yaratıyordu
});
    </script>
</div>