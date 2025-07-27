@props([
    'availableLanguages' => [],
    'currentLanguage' => 'tr',
    'seoDataCache' => [],
    'pageId' => null
])

@php
    // Eğer pageId geçilmişse Page modelini kullan, yoksa null
    $page = $pageId ? \App\Services\GlobalCacheService::getPageWithSeo($pageId) : null;
    $seoSettings = $page ? $page->seoSetting : null;
@endphp

@foreach($availableLanguages as $lang)
@php
    // Bu dilin SEO verilerini al - cache'den
    $langSeoData = [
        'seo_title' => $seoDataCache[$lang]['seo_title'] ?? '',
        'seo_description' => $seoDataCache[$lang]['seo_description'] ?? '',
        'seo_keywords' => $seoDataCache[$lang]['seo_keywords'] ?? '',
        'canonical_url' => $seoDataCache[$lang]['canonical_url'] ?? ''
    ];
    
    // Var olan sayfa ise o dilin verilerini veritabanından al
    if ($seoSettings) {
        $titles = $seoSettings->titles ?? [];
        $descriptions = $seoSettings->descriptions ?? [];
        $keywords = $seoSettings->keywords ?? [];
        
        // Keywords güvenli işleme
        $keywordData = $keywords[$lang] ?? [];
        $keywordString = '';
        if (is_array($keywordData)) {
            $keywordString = implode(', ', $keywordData);
        } elseif (is_string($keywordData)) {
            $keywordString = $keywordData;
        }
        
        $canonicalUrls = $seoSettings->canonical_url ?? [];
        $canonicalUrl = is_array($canonicalUrls) ? ($canonicalUrls[$lang] ?? '') : ($canonicalUrls ?? '');
        
        $langSeoData = [
            'seo_title' => $titles[$lang] ?? '',
            'seo_description' => $descriptions[$lang] ?? '',
            'seo_keywords' => $keywordString,
            'canonical_url' => $canonicalUrl
        ];
    }
    
    // SEO limitleri
    $seoLimits = [
        'seo_title' => 60,
        'seo_description' => 160,
        'seo_keywords_count' => 10,
        'canonical_url' => 255
    ];
@endphp

<div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
    <!-- SEO Widget - Bu dil için - Orijinal tasarım -->
    <div class="seo-widget-container">
        <div class="row">
            <!-- SEO Title -->
            <div class="col-md-6 mb-3">
                <div class="form-floating" style="position: relative;">
                    <input type="text" 
                           class="form-control" 
                           wire:model="seoDataCache.{{ $lang }}.seo_title"
                           maxlength="60"
                           placeholder="{{ __('admin.seo_title') }}">
                    <label>
                        {{ __('admin.seo_title') }}
                    </label>
                    <div style="position: absolute; right: 10px; top: 8px; z-index: 10;">
                        <small class="me-2" style="font-size: 0.7rem; font-weight: 300;">
                            <span class="char-count-{{ $lang }}-title">0</span>/60
                        </small>
                        <div class="progress d-inline-block" style="width: 30px; height: 3px; vertical-align: middle;">
                            <div class="progress-bar progress-{{ $lang }}-title" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="form-text">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ __('admin.seo_title_help') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- SEO Description - Solda -->
            <div class="col-md-6 mb-3">
                <div class="form-floating" style="position: relative;">
                    <textarea class="form-control"
                              wire:model="seoDataCache.{{ $lang }}.seo_description"
                              maxlength="160"
                              rows="3"
                              style="height: 80px;"
                              placeholder="{{ __('admin.seo_description') }}"></textarea>
                    <label>
                        {{ __('admin.seo_description') }}
                    </label>
                    <div style="position: absolute; right: 10px; top: 8px; z-index: 10;">
                        <small class="me-2" style="font-size: 0.7rem; font-weight: 300;">
                            <span class="char-count-{{ $lang }}-desc">0</span>/160
                        </small>
                        <div class="progress d-inline-block" style="width: 30px; height: 3px; vertical-align: middle;">
                            <div class="progress-bar progress-{{ $lang }}-desc" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="form-text">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ __('admin.seo_description_help') }}</small>
                    </div>
                </div>
            </div>

            <!-- SEO Keywords - Sağda -->
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="text" 
                           class="form-control" 
                           wire:model="seoDataCache.{{ $lang }}.seo_keywords"
                           placeholder="{{ __('admin.keywords_placeholder') }}">
                    <label>
                        {{ __('admin.seo_keywords') }}
                        <small class="text-muted">{{ __('admin.keywords_separator_note') }}</small>
                    </label>
                    <div class="form-text">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ __('admin.seo_keywords_help') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canonical URL - Her dil için ayrı -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-floating">
                    <input type="url" 
                           class="form-control"
                           wire:model="seoDataCache.{{ $lang }}.canonical_url"
                           placeholder="Canonical URL">
                    <label>{{ __('admin.canonical_url') }}</label>
                    <div class="form-text">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ __('admin.canonical_url_help') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

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
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'canonical_url' => ''
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
    
    @php $seoJsInitialized = true; @endphp
    @endif
</script>