<div wire:key="page-manage-component" wire:id="page-manage-component">
    @include('admin.partials.error_message')
    <form method="post" wire:submit.prevent="save">
        <div class="card">
            <x-tab-system 
                :tabs="$tabConfig" 
                :tab-completion="$tabCompletionStatus"
                storage-key="page_active_tab">
                
                {{-- Studio Integration --}}
                    @if($studioEnabled && $pageId)
                    <li class="nav-item ms-auto">
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}" 
                           target="_blank" 
                           class="nav-link bg-primary text-white rounded header-btn-uniform">
                            <i class="fas fa-wand-magic-sparkles me-2"></i>{{ __('admin.edit_with_studio') }}
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                    @else
                    <li class="nav-item ms-auto">
                    @endif
                        @php
                            // View Composer'dan gelen cache'li data kullan
                            $tenantLanguages = $cachedTenantLanguages ?? collect();
                            $currentLangName = $tenantLanguages->where('code', $currentLanguage)->first()?->native_name ?? strtoupper($currentLanguage);
                        @endphp
                        <div class="language-animation-container">
                            
                            <!-- TR EN AR Butonları (Normal durumda gizli) -->
                            <div class="language-buttons" id="languageButtons">
                                @foreach($tenantLanguages->where('is_active', true) as $lang)
                                    <button class="btn btn-link p-2 language-switch-btn {{ $currentLanguage === $lang->code ? 'text-primary' : 'text-muted' }}" 
                                            style="border: none; border-radius: 0; {{ $currentLanguage === $lang->code ? 'border-bottom: 2px solid var(--primary-color) !important;' : 'border-bottom: 2px solid transparent;' }}"
                                            data-language="{{ $lang->code }}"
                                            data-native-name="{{ $lang->native_name }}">
                                        {{ strtoupper($lang->code) }}
                                    </button>
                                @endforeach
                            </div>
                            
                            <!-- Language Badge (Normal durumda gözükür) -->
                            <div class="language-badge" id="languageBadge">
                                <div class="nav-link bg-primary text-white rounded header-btn-uniform">
                                    <i class="fas fa-language me-2"></i>{{ $currentLangName }}<i class="fas fa-chevron-down ms-2"></i>
                                </div>
                            </div>
                            
                        </div>
                    </li>
                
            </x-tab-system>
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <!-- Temel Bilgiler Tab -->
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        @foreach($availableLanguages as $lang)
                        @php
                            $langData = $multiLangInputs[$lang] ?? [];
                            $langName = $lang === 'tr' ? 'Türkçe' : ($lang === 'en' ? 'English' : 'العربية');
                        @endphp
                        
                        <div class="language-content" data-language="{{ $lang }}" style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">
                            <!-- Başlık alanı -->
                            <div class="form-floating mb-3">
                                <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                    class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                    placeholder="{{ __('page::admin.title_field') }}">
                                <label>
                                    {{ __('page::admin.title_field') }}
                                    @if($lang === session('site_default_language', 'tr')) <span class="required-star">★</span> @endif
                                </label>
                                @error('multiLangInputs.' . $lang . '.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- İçerik editörü -->
                            @include('page::admin.includes.content-editor', [
                                'lang' => $lang, 
                                'langName' => $langName, 
                                'langData' => $langData
                            ])
                        </div>
                        @endforeach
                        
                        {{-- Current Page ID for JavaScript - YENİ SAYFA İÇİN DE AKTIF --}}
                        <script>
                            window.currentPageId = {{ $pageId ?? 'null' }};
                            window.currentLanguage = '{{ $currentLanguage }}';
                            
                            // ULTRA PERFORMANCE: Tüm dillerin SEO verileri (ZERO API CALLS)
                            try {
                                @php
                                    // SEO Data Cache'den JavaScript için veri hazırla - HEM YENİ HEM ESKİ SAYFA
                                    $allLangSeoData = $this->seoDataCache ?? [];
                                    
                                    // Boş cache varsa her dil için boş veri oluştur (yeni sayfa için)
                                    if (empty($allLangSeoData) && !empty($this->availableLanguages)) {
                                        foreach($this->availableLanguages as $lang) {
                                            $allLangSeoData[$lang] = [
                                                'seo_title' => '',
                                                'seo_description' => '',
                                                'seo_keywords' => '',
                                                'canonical_url' => ''
                                            ];
                                        }
                                    }
                                    
                                    \Log::info('🔍 JavaScript SEO Data Debug', [
                                        'pageId' => $this->pageId,
                                        'seoDataCache_count' => count($allLangSeoData),
                                        'seoDataCache' => $allLangSeoData,
                                        'availableLanguages' => $this->availableLanguages,
                                        'isEmpty' => empty($allLangSeoData),
                                        'isNewPage' => !$this->pageId
                                    ]);
                                @endphp
                                window.allLanguagesSeoData = @json($allLangSeoData);
                                console.log('✅ SEO Data JSON başarıyla yüklendi:', window.allLanguagesSeoData);
                                console.log('🔍 Mevcut diller:', Object.keys(window.allLanguagesSeoData || {}));
                                console.log('🌍 Mevcut aktif dil:', window.currentLanguage);
                            } catch (error) {
                                console.error('❌ SEO Data JSON hatası:', error);
                                window.allLanguagesSeoData = {};
                            }
                            
                            // Global değişkenler - manage.js'te tanımlı
                            let currentLanguage = '{{ $currentLanguage }}';
                        </script>
                        
                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1" {{ (!isset($inputs['is_active']) || $inputs['is_active']) ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('page::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('page::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        @php
                            // 🚨 PERFORMANCE FIX: Global cache service kullan
                            $page = $pageId ? \Modules\Page\App\Services\PageCacheService::getPageWithSeo($pageId) : null;
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

                                    <!-- Slug (URL) -->
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   class="form-control" 
                                                   wire:model="multiLangInputs.{{ $lang }}.slug"
                                                   maxlength="255"
                                                   placeholder="sayfa-url-slug">
                                            <label>
                                                {{ __('admin.page_url_slug') }}
                                                <small class="text-muted ms-2">- {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}</small>
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
                    </div>

                    <!-- Code Tab -->
                    <div class="tab-pane fade" id="2" role="tabpanel">
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.css" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.css_code') }}"></textarea>
                            <label>{{ __('admin.css') }}</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.js" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.js_code') }}"></textarea>
                            <label>{{ __('admin.javascript') }}</label>
                        </div>
                    </div>
                    
                </div>
            </div>

            <x-form-footer route="admin.page" :model-id="$pageId" />

        </div>
        
        {{-- Helper dosyası --}}
        <div class="mt-2">
            @include('page::admin.helper')
        </div>
    </form>
    
</div>