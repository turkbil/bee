<div class="seo-widget-container">
    @if($seoScore && isset($seoScore['percentage']))
    <div class="row mb-3">
        <div class="col-md-8 col-lg-6">
            <div class="seo-score-container">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">SEO Skoru</h6>
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
                       placeholder="SEO Başlığı">
                <label for="seo-title">
                    SEO Başlığı
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
                    <small>Arama motorlarında görünecek başlık</small>
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
                    Sayfa URL (Slug)
                    <small class="text-muted ms-2">- Otomatik oluşur</small>
                </label>
                <div class="form-text d-flex justify-content-between">
                    <small>Sayfanın URL adresinde görünecek kısım</small>
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
                          placeholder="SEO Açıklaması">{{ $seoData['seo_description'] ?? '' }}</textarea>
                <label for="seo-description">
                    SEO Açıklaması
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
                    <small>Arama sonuçlarında görünecek açıklama</small>
                </div>
            </div>
        </div>

        <!-- SEO Keywords -->
        <div class="col-md-6 mb-3">
            <label class="form-label">
                Anahtar Kelimeler
                <small class="text-muted">(En fazla {{ $seoLimits['seo_keywords_count'] }} adet)</small>
            </label>
            <div class="keyword-input-container">
                <div class="d-flex mb-2">
                    <input type="text" 
                           id="keyword-input"
                           class="form-control keyword-input me-2" 
                           placeholder="Anahtar kelime yazın ve Enter'a basın..."
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
                <label for="canonical-url">Canonical URL</label>
                <div class="form-text">
                    <small>Bu sayfanın asıl URL adresi (isteğe bağlı)</small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// SEO Widget için özel event binding
document.addEventListener('DOMContentLoaded', function() {
    // Global SEO manager varsa kullan
    if (window.pageManagement?.seoManager) {
        window.pageManagement.seoManager.updateAllCounters();
    }
    
    // SEO sistemlerini başlat (güvenli kontrol)
    if (typeof setupKeywordSystem === 'function') {
        setupKeywordSystem();
    } else {
        console.warn('⚠️ setupKeywordSystem fonksiyonu bulunamadı!');
    }
    
    if (typeof setupSeoSlugSystem === 'function') {
        setupSeoSlugSystem();
    } else {
        console.warn('⚠️ setupSeoSlugSystem fonksiyonu bulunamadı!');
    }
});

// SEO Widget slug sistemi
function setupSeoSlugSystem() {
    const slugInput = document.getElementById('page-slug');
    const slugStatus = document.getElementById('slug-status');
    const titleInput = document.querySelector('[wire\\:model*=".title"]');
    
    if (!slugInput) return;
    
    // Title'dan otomatik slug oluşturma
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            if (slugInput.value.trim() === '') {
                const slug = generateSeoSlug(this.value);
                slugInput.value = slug;
                slugInput.dispatchEvent(new Event('input', { bubbles: true }));
                checkSlugUniqueness(slug);
            }
        });
    }
    
    // Slug değişikliklerini dinle ve benzersizlik kontrolü
    let slugTimeout;
    slugInput.addEventListener('input', function() {
        clearTimeout(slugTimeout);
        const slug = this.value.trim();
        
        if (slug === '') {
            slugStatus.innerHTML = '';
            slugInput.classList.remove('is-valid', 'is-invalid');
            return;
        }
        
        // Slug format kontrolü
        const cleanSlug = generateSeoSlug(slug);
        if (slug !== cleanSlug) {
            this.value = cleanSlug;
            slugInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // 500ms bekle, sonra benzersizlik kontrolü yap
        slugTimeout = setTimeout(() => {
            checkSlugUniqueness(cleanSlug);
        }, 500);
    });
}

// Slug benzersizlik kontrolü
function checkSlugUniqueness(slug) {
    const slugStatus = document.getElementById('slug-status');
    const slugInput = document.getElementById('page-slug');
    
    if (!slug) return;
    
    // Loading durumu
    slugStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kontrol ediliyor...';
    slugInput.classList.remove('is-valid', 'is-invalid');
    
    // AJAX ile slug kontrolü
    fetch('/admin/check-slug', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            slug: slug,
            module: 'Page',
            exclude_id: window.currentPageId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.unique) {
            slugStatus.innerHTML = '<i class="fas fa-check"></i> Kullanılabilir';
            slugInput.classList.remove('is-invalid');
            slugInput.classList.add('is-valid');
        } else {
            slugStatus.innerHTML = '<i class="fas fa-times"></i> Bu slug zaten kullanılıyor';
            slugInput.classList.remove('is-valid');
            slugInput.classList.add('is-invalid');
        }
    })
    .catch(error => {
        console.error('Slug kontrolü hatası:', error);
        slugStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Kontrol edilemiyor';
    });
}

// SEO için Türkçe slug üretimi
function generateSeoSlug(text) {
    if (!text) return '';
    
    const turkishChars = {
        'Ç': 'C', 'ç': 'c', 'Ğ': 'G', 'ğ': 'g', 
        'I': 'I', 'ı': 'i', 'İ': 'I', 'i': 'i',
        'Ö': 'O', 'ö': 'o', 'Ş': 'S', 'ş': 's', 
        'Ü': 'U', 'ü': 'u'
    };
    
    return text
        .replace(/[ÇçĞğIıİiÖöŞşÜü]/g, match => turkishChars[match] || match)
        .toLowerCase()
        .replace(/[^a-z0-9\s\-]/g, '')
        .replace(/[\s\-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
</script>
@endpush