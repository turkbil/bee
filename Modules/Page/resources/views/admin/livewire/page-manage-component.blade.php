<div wire:key="page-manage-component">
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
                           class="nav-link px-3 py-2 bg-primary text-white rounded">
                            <i class="fas fa-wand-magic-sparkles me-2"></i>Studio ile Düzenle
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                    @else
                    <li class="nav-item ms-auto">
                    @endif
                        @php
                            // View Composer'dan gelen cache'li data kullan
                            $tenantLanguages = $cachedTenantLanguages ?? collect();
                        @endphp
                        <div class="d-flex gap-3">
                            @foreach($tenantLanguages->where('is_active', true) as $lang)
                                <button class="btn btn-link p-2 language-switch-btn {{ $currentLanguage === $lang->code ? 'text-primary' : 'text-muted' }}" 
                                        style="border: none; border-radius: 0; {{ $currentLanguage === $lang->code ? 'border-bottom: 2px solid var(--primary-color) !important;' : 'border-bottom: 2px solid transparent;' }}"
                                        data-language="{{ $lang->code }}">
                                    {{ strtoupper($lang->code) }}
                                </button>
                            @endforeach
                        </div>
                    </li>
                
            </x-tab-system>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Tab 1: Basic Info -->
                    <div class="tab-pane fade show active" id="tabs-1">
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
                                    placeholder="{{ __('page::admin.title_field') }} ({{ strtoupper($lang) }})">
                                <label>
                                    {{ __('page::admin.title_field') }} ({{ $langName }})
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
                        
                        {{-- Current Page ID for JavaScript --}}
                        @if($pageId)
                        <script>
                            window.currentPageId = {{ $pageId }};
                        </script>
                        @endif
                        

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
                    
                    <!-- Tab 2: SEO - Global Widget -->
                    <div class="tab-pane fade" id="tabs-2">
                        @if($pageId)
                            @php
                                $page = \Modules\Page\App\Models\Page::find($pageId);
                                $seoSettings = $page ? $page->seoSetting : null;
                                
                                // Mevcut dilin SEO verilerini al
                                $currentSeoData = [
                                    'seo_title' => '',
                                    'seo_description' => '',
                                    'seo_keywords' => '',
                                    'canonical_url' => ''
                                ];
                                
                                if ($seoSettings) {
                                    $titles = $seoSettings->titles ?? [];
                                    $descriptions = $seoSettings->descriptions ?? [];
                                    $keywords = $seoSettings->keywords ?? [];
                                    
                                    $currentSeoData = [
                                        'seo_title' => $titles[$currentLanguage] ?? '',
                                        'seo_description' => $descriptions[$currentLanguage] ?? '',
                                        'seo_keywords' => is_array($keywords[$currentLanguage] ?? []) ? implode(', ', $keywords[$currentLanguage]) : '',
                                        'canonical_url' => $seoSettings->canonical_url ?? ''
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
                            
                            <!-- Global SEO Widget Kullanımı -->
                            <x-seo-widget 
                                :seo-data="$currentSeoData" 
                                :seo-limits="$seoLimits" 
                                :language="$currentLanguage" 
                                :current-language="$currentLanguage"
                                :show-score="true" />
                                
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                SEO ayarları sayfayı kaydettikten sonra mevcut olacaktır.
                            </div>
                        @endif
                    </div>

                    <!-- Tab 3: Code Area -->
                    <div class="tab-pane fade" id="tabs-3">
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