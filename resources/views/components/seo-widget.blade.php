<div class="seo-widget-container">
    @if($seoScore && isset($seoScore['percentage']))
    <div class="row mb-3">
        <div class="col-md-8 col-lg-6">
            <div class="seo-score-container">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">{{ __('admin.seo_score') }}</h6>
                    <span class="badge badge-outline">
                        {{ $seoScore['percentage'] }}%
                    </span>
                </div>
                <div class="progress">
                    <div class="progress-bar" 
                         style="width: {{ $seoScore['percentage'] }}%"></div>
                </div>
                
                @if($seoScore['checks'])
                <div class="seo-checks mt-2">
                    @foreach($seoScore['checks'] as $check => $details)
                    <small class="d-block">
                        <i class="fas fa-{{ $details['status'] === 'good' ? 'check' : ($details['status'] === 'warning' ? 'exclamation-triangle' : 'times') }}"></i>
                        {{ $details['message'] }}
                    </small>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- SEO Title -->
        <div class="col-md-6 mb-3">
            <div class="form-floating" style="position: relative;">
                <input type="text" 
                       class="form-control" 
                       id="seo-title" 
                       name="seo_title" 
                       wire:model.defer="seo_title"
                       maxlength="{{ $seoLimits['seo_title'] }}"
                       value="{{ $seoData['seo_title'] ?? '' }}"
                       placeholder="{{ __('admin.seo_title') }}">
                <label for="seo-title">
                    {{ __('admin.seo_title') }}
                    <span class="required-star">★</span>
                </label>
                <div style="position: absolute; right: 10px; top: 8px; z-index: 10;">
                    <small class="seo-character-counter me-2" 
                           data-counter-for="seo_title" 
                           data-limit="{{ $seoLimits['seo_title'] }}"
                           style="font-size: 0.7rem; font-weight: 300;">
                        {{ mb_strlen($seoData['seo_title'] ?? '') }}/{{ $seoLimits['seo_title'] }}
                    </small>
                    <div class="progress d-inline-block" style="width: 30px; height: 3px; vertical-align: middle;">
                        <div class="progress-bar seo-progress-bar" data-field="seo_title" style="width: {{ ($seoLimits['seo_title'] > 0) ? (mb_strlen($seoData['seo_title'] ?? '') / $seoLimits['seo_title'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="form-text">
                    <small><i class="fas fa-info-circle me-1"></i>{{ __('admin.seo_title_help') }}</small>
                </div>
            </div>
        </div>

        <!-- Slug (URL) -->
        <div class="col-md-6 mb-3">
            <div class="form-floating">
                <input type="text" 
                       class="form-control" 
                       id="page-slug" 
                       name="page_slug" 
                       wire:model.defer="multiLangInputs.{{ $currentLanguage }}.slug"
                       maxlength="255"
                       placeholder="sayfa-url-slug">
                <label for="page-slug">
                    {{ __('admin.page_url_slug') }}
                    <small class="text-muted ms-2">- {{ __('admin.slug_auto_generated') }}</small>
                </label>
                <div class="form-text d-flex justify-content-between">
                    <small><i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}</small>
                    <small class="slug-status" id="slug-status"></small>
                </div>
            </div>
        </div>

        <!-- SEO Description -->
        <div class="col-md-6 mb-3">
            <div class="form-floating" style="position: relative;">
                <textarea class="form-control" 
                          id="seo-description" 
                          name="seo_description" 
                          wire:model.defer="seo_description"
                          maxlength="{{ $seoLimits['seo_description'] }}"
                          style="height: 100px;"
                          placeholder="{{ __('admin.seo_description') }}">{{ $seoData['seo_description'] ?? '' }}</textarea>
                <label for="seo-description">
                    {{ __('admin.seo_description') }}
                    <span class="required-star">★</span>
                </label>
                <div style="position: absolute; right: 10px; top: 8px; z-index: 10;">
                    <small class="seo-character-counter me-2" 
                           data-counter-for="seo_description" 
                           data-limit="{{ $seoLimits['seo_description'] }}"
                           style="font-size: 0.7rem; font-weight: 300;">
                        {{ mb_strlen($seoData['seo_description'] ?? '') }}/{{ $seoLimits['seo_description'] }}
                    </small>
                    <div class="progress d-inline-block" style="width: 30px; height: 3px; vertical-align: middle;">
                        <div class="progress-bar seo-progress-bar" data-field="seo_description" style="width: {{ ($seoLimits['seo_description'] > 0) ? (mb_strlen($seoData['seo_description'] ?? '') / $seoLimits['seo_description'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="form-text">
                    <small><i class="fas fa-info-circle me-1"></i>{{ __('admin.seo_description_help') }}</small>
                </div>
            </div>
        </div>

        <!-- SEO Keywords -->
        <div class="col-md-6 mb-3">
            <label class="form-label">
                {{ __('admin.seo_keywords') }}
                <small class="text-muted">(En fazla {{ $seoLimits['seo_keywords_count'] }} adet)</small>
            </label>
            <div class="keyword-input-container">
                <div class="d-flex mb-2">
                    <input type="text" 
                           id="keyword-input"
                           class="form-control keyword-input me-2" 
                           placeholder="{{ __('admin.keywords_placeholder') }}"
                           autocomplete="off">
                    <button type="button" id="add-keyword" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div id="keyword-display" class="keyword-badges">
                    @if(!empty($seoData['seo_keywords']))
                        @foreach(array_filter(array_map('trim', explode(',', $seoData['seo_keywords']))) as $keyword)
                        <span class="badge badge-secondary me-1 mb-1" style="padding: 6px 8px;">
                            <span class="keyword-text">{{ $keyword }}</span>
                            <span class="keyword-remove" style="cursor: pointer; padding: 2px 4px; border-radius: 2px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">&times;</span>
                        </span>
                        @endforeach
                    @endif
                </div>
                <input type="hidden" 
                       id="seo-keywords-hidden"
                       name="seo_keywords" 
                       wire:model.defer="seo_keywords"
                       value="{{ $seoData['seo_keywords'] ?? '' }}">
            </div>
        </div>

        <!-- Canonical URL -->
        <div class="col-md-6 mb-3">
            <div class="form-floating">
                <input type="url" 
                       class="form-control" 
                       id="canonical-url" 
                       name="canonical_url" 
                       wire:model.defer="canonical_url"
                       maxlength="{{ $seoLimits['canonical_url'] }}"
                       value="{{ $seoData['canonical_url'] ?? '' }}"
                       placeholder="https://example.com/page">
                <label for="canonical-url">{{ __('admin.canonical_url') }}</label>
                <div class="form-text">
                    <small><i class="fas fa-info-circle me-1"></i>{{ __('admin.canonical_url_help') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="{{ asset('admin-assets/css/seo-tabs.css') }}" rel="stylesheet">
@endpush

{{-- SEO Tabs JS artık page-manage-component.blade.php'de yükleniyor --}}

{{-- DEVRE DIŞI: Multiple system conflict çözümü için - seo-tabs.js handles everything
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ SEO Widget basit sistem aktif');
    
    // Sadece Livewire sync için basit input listener'ları kur
    const seoInputs = ['seo-title', 'seo-description', 'seo-keywords-hidden', 'canonical-url'];
    
    seoInputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', function(e) {
                // Sadece Livewire'e bildir - basit sistem
                if (window.Livewire) {
                    const field = id.replace('seo-', 'seo_').replace('-', '_');
                    window.Livewire.dispatch('seo-field-updated', {
                        field: field,
                        value: e.target.value,
                        language: getCurrentLanguage()
                    });
                }
            });
        }
    });
    
    // Mevcut dili al - GLOBAL currentLanguage değişkeninden
    function getCurrentLanguage() {
        // Önce global currentLanguage değişkenini kontrol et
        if (window.currentLanguage) {
            console.log('🌍 Global currentLanguage kullanildi:', window.currentLanguage);
            return window.currentLanguage;
        }
        
        // Sonra aktif language butonunu kontrol et
        const activeLanguageBtn = document.querySelector('.language-switch-btn.text-primary');
        const buttonLang = activeLanguageBtn ? activeLanguageBtn.dataset.language : 'tr';
        console.log('🔘 Button dilinden alindi:', buttonLang);
        
        return buttonLang;
    }
});
</script>
--}}