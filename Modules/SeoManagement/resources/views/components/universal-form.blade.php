{{-- 
    Universal SEO Form Component
    Herhangi bir model için kullanılabilir SEO yönetim formu
    
    Kullanım:
    <x-seo-management::universal-form 
        :model="$anyModel"
        :available-languages="$availableLanguages" 
        :current-language="$currentLanguage" 
        :seo-data-cache="$seoDataCache" />
--}}

@props([
    'model' => null,
    'modelType' => null,
    'modelId' => null,
    'availableLanguages' => [],
    'currentLanguage' => 'tr',
    'seoDataCache' => []
])

@php
    // Model detection - flexible approach
    if ($model) {
        $currentModel = $model;
        $modelClass = get_class($model);
        $modelKey = $model->getKey();
    } elseif ($modelType && $modelId) {
        $modelClass = $modelType;
        $currentModel = $modelClass::find($modelId);
        $modelKey = $modelId;
    } else {
        $currentModel = null;
        $modelClass = null;
        $modelKey = null;
    }
    
    // SEO settings al
    $seoSettings = $currentModel?->seoSetting;
@endphp

{{-- SEO Form Container --}}
<div class="seo-universal-form" data-model-type="{{ $modelClass }}" data-model-id="{{ $modelKey }}">
    
    {{-- Language Tabs için SEO Content --}}
    @foreach($availableLanguages as $lang)
        @php
            // Bu dilin SEO verilerini al - cache'den veya database'den
            $langSeoData = [
                'seo_title' => $seoDataCache[$lang]['seo_title'] ?? '',
                'seo_description' => $seoDataCache[$lang]['seo_description'] ?? '',
                'seo_keywords' => $seoDataCache[$lang]['seo_keywords'] ?? ''
            ];
            
            // Var olan model ise veritabanından al
            if ($seoSettings) {
                $titles = $seoSettings->titles ?? [];
                $descriptions = $seoSettings->descriptions ?? [];
                $keywords = $seoSettings->keywords ?? [];
                
                // Override cache with database values
                $langSeoData['seo_title'] = $titles[$lang] ?? $langSeoData['seo_title'];
                $langSeoData['seo_description'] = $descriptions[$lang] ?? $langSeoData['seo_description'];
                
                // Keywords güvenli işleme
                $keywordData = $keywords[$lang] ?? [];
                if (is_string($keywordData)) {
                    $keywordArray = explode(',', $keywordData);
                    $langSeoData['seo_keywords'] = array_map('trim', $keywordArray);
                } elseif (is_array($keywordData)) {
                    $langSeoData['seo_keywords'] = $keywordData;
                }
            }
        @endphp
        
        {{-- Language specific SEO content --}}
        <div class="language-content" data-language="{{ $lang }}" 
             style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
            
            {{-- SEO Başlık --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-floating">
                        <input type="text" 
                               wire:model="seoDataCache.{{ $lang }}.seo_title"
                               class="form-control @error('seoDataCache.' . $lang . '.seo_title') is-invalid @enderror"
                               placeholder="{{ __('admin.seo_title') }}"
                               maxlength="60">
                        <label>
                            {{ __('admin.seo_title') }} ({{ strtoupper($lang) }})
                            <small class="text-muted">0-60 karakter</small>
                        </label>
                        @error('seoDataCache.' . $lang . '.seo_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            {{-- SEO Açıklama --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-floating">
                        <textarea wire:model="seoDataCache.{{ $lang }}.seo_description"
                                  class="form-control @error('seoDataCache.' . $lang . '.seo_description') is-invalid @enderror"
                                  placeholder="{{ __('admin.seo_description') }}"
                                  style="height: 100px"
                                  maxlength="160"></textarea>
                        <label>
                            {{ __('admin.seo_description') }} ({{ strtoupper($lang) }})
                            <small class="text-muted">0-160 karakter</small>
                        </label>
                        @error('seoDataCache.' . $lang . '.seo_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            {{-- SEO Anahtar Kelimeler --}}
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        {{ __('admin.seo_keywords') }} ({{ strtoupper($lang) }})
                        <small class="text-muted">{{ __('admin.keywords_separator_note') }}</small>
                    </label>
                    <input type="text" 
                           wire:model="seoDataCache.{{ $lang }}.seo_keywords"
                           class="form-control @error('seoDataCache.' . $lang . '.seo_keywords') is-invalid @enderror"
                           placeholder="{{ __('admin.keywords_placeholder') }}">
                    @error('seoDataCache.' . $lang . '.seo_keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
        </div>
    @endforeach
    
    
    {{-- SEO Preview Card --}}
    <div class="card bg-light mt-4">
        <div class="card-body">
            <h6 class="card-title text-muted mb-3">
                <i class="fas fa-search me-2"></i>{{ __('admin.seo_preview') }}
            </h6>
            <div class="seo-preview">
                <div class="preview-title text-primary fw-bold">
                    {{ $seoDataCache[$currentLanguage]['seo_title'] ?? 'SEO Başlık' }}
                </div>
                <div class="preview-url text-success small">
                    {{ request()->url() }}
                </div>
                <div class="preview-description text-muted">
                    {{ $seoDataCache[$currentLanguage]['seo_description'] ?? 'SEO açıklama metni burada görünecek...' }}
                </div>
            </div>
        </div>
    </div>
    
    {{-- AI Features Placeholder (İleride) --}}
    <div class="mt-4 p-3 bg-info bg-opacity-10 rounded">
        <h6 class="text-info mb-2">
            <i class="fas fa-robot me-2"></i>AI SEO Özellikleri (Yakında)
        </h6>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-info btn-sm" disabled>
                <i class="fas fa-magic me-1"></i>AI SEO Oluştur
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" disabled>
                <i class="fas fa-chart-line me-1"></i>SEO Skoru
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" disabled>
                <i class="fas fa-lightbulb me-1"></i>Optimizasyon Önerileri
            </button>
        </div>
    </div>
    
</div>

{{-- JavaScript for SEO form --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // SEO karakter sayacları
    const seoInputs = document.querySelectorAll('.seo-universal-form input, .seo-universal-form textarea');
    
    seoInputs.forEach(input => {
        if (input.hasAttribute('maxlength')) {
            const maxLength = input.getAttribute('maxlength');
            const label = input.nextElementSibling.querySelector('small');
            
            if (label) {
                const updateCounter = () => {
                    const currentLength = input.value.length;
                    label.textContent = `${currentLength}/${maxLength} karakter`;
                    
                    // Color coding
                    if (currentLength > maxLength * 0.9) {
                        label.className = 'text-danger';
                    } else if (currentLength > maxLength * 0.7) {
                        label.className = 'text-warning';
                    } else {
                        label.className = 'text-muted';
                    }
                };
                
                input.addEventListener('input', updateCounter);
                updateCounter(); // Initial call
            }
        }
    });
});
</script>