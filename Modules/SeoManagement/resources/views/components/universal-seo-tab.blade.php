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
    {{-- TEMEL SEO ALANLARI --}}
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-star me-2"></i>Temel SEO Ayarları ({{ strtoupper($lang) }})
                <small class="opacity-75 ms-2">Mutlaka doldurulması gerekenler</small>
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Meta Title --}}
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <input type="text" 
                               wire:model="seoDataCache.{{ $lang }}.seo_title"
                               class="form-control seo-no-enter @error('seoDataCache.' . $lang . '.seo_title') is-invalid @enderror"
                               placeholder="{{ __('page::admin.seo_title_placeholder') }}"
                               maxlength="60"
                               {{ $disabled ? 'disabled' : '' }}>
                        <label>
                            {{ __('page::admin.seo_title') }} ({{ strtoupper($lang) }})
                            <small class="text-muted ms-2">50-60 karakter</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>{{ __('page::admin.seo_title_help') }}
                            </small>
                        </div>
                        @error('seoDataCache.' . $lang . '.seo_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Meta Description --}}
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <textarea wire:model="seoDataCache.{{ $lang }}.seo_description"
                                  class="form-control seo-no-enter @error('seoDataCache.' . $lang . '.seo_description') is-invalid @enderror"
                                  placeholder="{{ __('page::admin.seo_description_placeholder') }}"
                                  style="height: 100px; resize: vertical;"
                                  maxlength="160"
                                  {{ $disabled ? 'disabled' : '' }}></textarea>
                        <label>
                            {{ __('page::admin.seo_description') }} ({{ strtoupper($lang) }})
                            <small class="text-muted ms-2">150-160 karakter</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>{{ __('page::admin.seo_description_help') }}
                            </small>
                        </div>
                        @error('seoDataCache.' . $lang . '.seo_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                {{-- Priority --}}
                <div class="col-md-6 mb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label mb-0">
                            <i class="fas fa-flag me-1"></i>
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
                            <i class="fas fa-info-circle me-1"></i>
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
        <i class="fas fa-share-alt me-2"></i>Sosyal Medya & Schema Ayarları
    </h6>

    {{-- OG IMAGE & CONTENT TYPE --}}
    <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-image me-2"></i>Sosyal Medya & İçerik Tipi
                <small class="opacity-75 ms-2">OG Image ve Schema.org ayarları</small>
            </h6>
        </div>
        <div class="card-body">
            @if($lang === ($availableLanguages[0] ?? 'tr'))
            <div class="row">
                {{-- OG Image Media Selector --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-image me-1"></i>Sosyal Medya Resmi
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
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                    
                    {{-- Media Selection Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm flex-fill"
                                onclick="document.getElementById('og_image_file_{{ $lang }}').click()"
                                {{ $disabled ? 'disabled' : '' }}>
                            <i class="fas fa-folder-open me-1"></i>
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
                            <i class="fas fa-info-circle me-1"></i>Facebook, LinkedIn, WhatsApp paylaşımlarında görünür
                        </small>
                    </div>
                </div>

                {{-- Universal Content Type (OG + Schema) --}}
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
                            <i class="fas fa-tags me-1"></i>İçerik Türü
                            <small class="text-muted ms-2">Sosyal medya + Schema.org</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Hem sosyal medya hem arama motorları için kullanılır
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
                                <i class="fas fa-edit me-1"></i>Özel İçerik Türü
                                <small class="text-muted ms-2">Manuel giriş</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>Schema.org'dan geçerli bir tür girin (Recipe, Book, Course...)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Bilgi:</strong> Sosyal medya ayarları tüm diller için ortaktır. Ana dil ({{ strtoupper($availableLanguages[0] ?? 'tr') }}) sekmesinden düzenleyebilirsiniz.
            </div>
            @endif
        </div>
    </div>

    {{-- TWITTER CARDS --}}
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fab fa-twitter me-2"></i>Twitter Cards
                <small class="opacity-75 ms-2">Twitter paylaşım ayarları</small>
            </h6>
        </div>
        <div class="card-body">
            @if($lang === ($availableLanguages[0] ?? 'tr'))
            <div class="row">
                {{-- Twitter Card Type --}}
                <div class="col-md-6 mb-3">
                    <div class="form-floating">
                        <select wire:model="seoDataCache.{{ $lang }}.twitter_card"
                                class="form-select"
                                {{ $disabled ? 'disabled' : '' }}>
                            <option value="summary">Summary (Küçük resim)</option>
                            <option value="summary_large_image">Summary Large Image (Büyük resim)</option>
                        </select>
                        <label>
                            <i class="fab fa-twitter me-1"></i>Twitter Card Türü
                            <small class="text-muted ms-2">Gösterim şekli</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Boş alanlar SEO verilerinden otomatik alınır
                            </small>
                        </div>
                    </div>
                </div>
                
                {{-- Twitter Override Toggle --}}
                <div class="col-md-6 mb-3">
                    <div class="mt-3">
                        <div class="pretty p-switch">
                            <input type="checkbox" 
                                   wire:model="seoDataCache.{{ $lang }}.twitter_custom_enabled"
                                   id="twitter_custom_{{ $lang }}"
                                   onchange="toggleTwitterCustomFields(this, '{{ $lang }}')"
                                   {{ $disabled ? 'disabled' : '' }}>
                            <div class="state">
                                <label for="twitter_custom_{{ $lang }}">
                                    <i class="fab fa-twitter me-1"></i>Özel Twitter içerikleri kullan
                                </label>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Kapalıysa SEO verilerini kullanır (70/200 karakter, 1024x512)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Twitter Custom Fields (Collapsible) --}}
            <div class="twitter-custom-fields" 
                 id="twitter_custom_fields_{{ $lang }}" 
                 style="display: none;">
                <hr class="my-3">
                <div class="row">
                    {{-- Twitter Title --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <input type="text" 
                                   wire:model="seoDataCache.{{ $lang }}.twitter_title"
                                   class="form-control seo-no-enter"
                                   placeholder="Twitter'da görünecek özel başlık"
                                   maxlength="70"
                                   {{ $disabled ? 'disabled' : '' }}>
                            <label>
                                <i class="fab fa-twitter me-1"></i>Twitter Başlığı
                                <small class="text-muted ms-2">Maksimum 70 karakter</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-hashtag me-1"></i>Twitter için optimize edilmiş başlık (hashtag kullanabilirsiniz)
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Twitter Description --}}
                    <div class="col-md-6 mb-3">
                        <div class="form-floating">
                            <textarea wire:model="seoDataCache.{{ $lang }}.twitter_description"
                                      class="form-control seo-no-enter"
                                      placeholder="Twitter'da görünecek özel açıklama"
                                      style="height: 100px; resize: vertical;"
                                      maxlength="200"
                                      {{ $disabled ? 'disabled' : '' }}></textarea>
                            <label>
                                <i class="fab fa-twitter me-1"></i>Twitter Açıklaması
                                <small class="text-muted ms-2">Maksimum 200 karakter</small>
                            </label>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-at me-1"></i>CTA ve mention kullanabilirsiniz (@username, #hashtag)
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Twitter Image Media Selector --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fab fa-twitter me-1"></i>Twitter Özel Resim
                            <small class="text-muted ms-2">1024x512 önerilen</small>
                        </label>
                        
                        {{-- Media Preview --}}
                        @if(!empty($seoDataCache[$lang]['twitter_image']))
                        <div class="media-preview-container mb-2 position-relative">
                            <img src="{{ $seoDataCache[$lang]['twitter_image'] }}" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 120px; width: auto;"
                                 alt="Twitter Image Preview">
                            <button type="button" 
                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                    wire:click="$set('seoDataCache.{{ $lang }}.twitter_image', '')"
                                    {{ $disabled ? 'disabled' : '' }}>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @endif
                        
                        {{-- Media Selection Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="button" 
                                    class="btn btn-outline-info btn-sm flex-fill"
                                    onclick="document.getElementById('twitter_image_file_{{ $lang }}').click()"
                                    {{ $disabled ? 'disabled' : '' }}>
                                <i class="fab fa-twitter me-1"></i>
                                {{ empty($seoDataCache[$lang]['twitter_image']) ? 'Twitter Resim Seç' : 'Twitter Resim Değiştir' }}
                            </button>
                            
                            <input type="url" 
                                   wire:model="seoDataCache.{{ $lang }}.twitter_image_url"
                                   class="form-control form-control-sm"
                                   placeholder="Veya URL girin"
                                   style="flex: 2;"
                                   {{ $disabled ? 'disabled' : '' }}>
                        </div>
                        
                        {{-- Hidden File Input --}}
                        <input type="file" 
                               id="twitter_image_file_{{ $lang }}"
                               wire:model="seoImageFiles.twitter_image"
                               class="d-none"
                               accept="image/jpeg,image/jpg,image/png,image/webp"
                               {{ $disabled ? 'disabled' : '' }}>
                        
                        {{-- Upload Progress --}}
                        <div class="progress mt-2" 
                             wire:loading 
                             wire:target="seoImageFiles.twitter_image"
                             style="height: 4px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                 style="width: 100%"></div>
                        </div>
                        
                        <div class="form-text mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Twitter için özel boyutlandırılmış resim (1024x512 px)
                            </small>
                        </div>
                    </div>
                    
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Bilgi:</strong> Twitter Cards ayarları tüm diller için ortaktır. Ana dil ({{ strtoupper($availableLanguages[0] ?? 'tr') }}) sekmesinden düzenleyebilirsiniz.
            </div>
            @endif
        </div>
    </div>

    {{-- İÇERİK BİLGİLERİ --}}
    <div class="card border-info mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-user-edit me-2"></i>İçerik Bilgileri
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
                            <i class="fas fa-user me-1"></i>Yazar Adı
                            <small class="text-muted ms-2">İçerik yazarı</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Bu içeriği yazan kişinin adı (schema.org author)
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
                            <i class="fas fa-link me-1"></i>Yazar Profil URL'si
                            <small class="text-muted ms-2">Yazarın profil sayfası</small>
                        </label>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Yazarın profil sayfası veya kişisel web sitesi
                            </small>
                        </div>
                    </div>
                </div>

            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Bilgi:</strong> İçerik bilgileri tüm diller için ortaktır. Ana dil ({{ strtoupper($availableLanguages[0] ?? 'tr') }}) sekmesinden düzenleyebilirsiniz.
            </div>
            @endif
        </div>
    </div>

    {{-- ROBOTS & AI CRAWLERS - OTOMATIK AKTIF --}}
    <div class="alert mb-4">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-robot fa-2x"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-2">
                    <i class="fas fa-check-circle me-2"></i>Robots ve AI Crawlers Otomatik Aktif
                    <span class="badge ms-2">{{ date('Y') }} Standartlari</span>
                </h6>
                <p class="mb-0 small">
                    <strong>Google Robots:</strong> index, follow, max-snippet:160, max-image-preview:large<br>
                    <strong>AI Crawlers:</strong> GPTBot, ClaudeBot, PerplexityBot, Google-Extended, BingBot<br>
                    <strong>Sonuc:</strong> Tum sayfalar otomatik olarak {{ date('Y') }} SEO standartlarinda optimize edildi
                </p>
            </div>
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
    
    // Twitter Custom Fields Toggle Function
    function toggleTwitterCustomFields(checkbox, language) {
        const customDiv = document.getElementById('twitter_custom_fields_' + language);
        const isEnabled = checkbox.checked;
        
        if (customDiv) {
            if (isEnabled) {
                customDiv.style.display = 'block';
                // Smooth animation
                customDiv.style.maxHeight = 'none';
                customDiv.style.overflow = 'visible';
            } else {
                customDiv.style.display = 'none';
                // Clear Twitter custom fields if disabled
                const twitterInputs = customDiv.querySelectorAll('input, textarea');
                twitterInputs.forEach(input => {
                    input.value = '';
                    // Livewire'a da bildir
                    input.dispatchEvent(new Event('input'));
                });
            }
        }
        
        console.log(`🐦 Twitter custom fields ${isEnabled ? 'enabled' : 'disabled'} for ${language}`);
    }
    
    // Sayfa yüklendiğinde mevcut değerleri kontrol et
    document.addEventListener('DOMContentLoaded', function() {
        const contentTypeSelects = document.querySelectorAll('select[wire\\:model*="content_type"]');
        contentTypeSelects.forEach(select => {
            const language = select.getAttribute('wire:model').match(/\.(\w+)\./)[1];
            if (select.value === 'custom') {
                toggleCustomContentType(select, language);
            }
        });
    });
    
    // Universal Content Type initialization listener
    document.addEventListener('livewire:navigated', function() {
        setTimeout(function() {
            const contentTypeSelects = document.querySelectorAll('select[wire\\\\:model*=\"content_type\"]');
            contentTypeSelects.forEach(select => {
                const wireModel = select.getAttribute('wire:model');
                if (wireModel) {
                    const language = wireModel.match(/\\.(\\w+)\\./)[1];
                    if (select.value === 'custom') {
                        toggleCustomContentType(select, language);
                    }
                }
            });
        }, 100);
    });
    
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
@endif