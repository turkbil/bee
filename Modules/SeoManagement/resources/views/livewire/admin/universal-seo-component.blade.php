<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-search-plus me-2"></i>
                    Universal SEO Yönetimi
                    @if($model)
                        <small class="text-muted ms-2">{{ class_basename($model) }} #{{ $model->getKey() }}</small>
                    @endif
                </h3>
                
                @if($model)
                    <div class="d-flex gap-2">
                        <span class="badge bg-info">{{ ucfirst($modelType) }}</span>
                        <span class="badge bg-secondary">ID: {{ $modelId }}</span>
                    </div>
                @endif
            </div>

            <div class="card-body">
                @if(!$model)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Model bilgileri eksik. Lütfen model türü ve ID'sini belirtin.
                    </div>
                @else
                    <form wire:submit.prevent="save">
                        {{-- Tab System --}}
                        <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="universal_seo_active_tab">
                            {{-- Language Switcher --}}
                            <x-manage.language.switcher :current-language="$currentLanguage" />
                            
                            {{-- Basic SEO Tab --}}
                            <x-slot name="basic_seo">
                                <div class="row">
                                    {{-- Meta Title --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   wire:model.live="multiLangInputs.{{ $currentLanguage }}.title"
                                                   class="form-control @error('multiLangInputs.' . $currentLanguage . '.title') is-invalid @enderror"
                                                   placeholder="{{ __('admin.seo_title') }}"
                                                   maxlength="60">
                                            <label>
                                                {{ __('admin.seo_title') }} ({{ strtoupper($currentLanguage) }})
                                                <small class="text-muted">0-60 karakter</small>
                                            </label>
                                            @error('multiLangInputs.' . $currentLanguage . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Meta Description --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <textarea wire:model.live="multiLangInputs.{{ $currentLanguage }}.description"
                                                      class="form-control @error('multiLangInputs.' . $currentLanguage . '.description') is-invalid @enderror"
                                                      placeholder="{{ __('admin.seo_description') }}"
                                                      style="height: 100px"
                                                      maxlength="160"></textarea>
                                            <label>
                                                {{ __('admin.seo_description') }} ({{ strtoupper($currentLanguage) }})
                                                <small class="text-muted">0-160 karakter</small>
                                            </label>
                                            @error('multiLangInputs.' . $currentLanguage . '.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Keywords --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            {{ __('admin.seo_keywords') }} ({{ strtoupper($currentLanguage) }})
                                            <small class="text-muted">Virgül ile ayırın</small>
                                        </label>
                                        <input type="text" 
                                               wire:model.live="multiLangInputs.{{ $currentLanguage }}.keywords"
                                               class="form-control @error('multiLangInputs.' . $currentLanguage . '.keywords') is-invalid @enderror"
                                               placeholder="anahtar1, anahtar2, anahtar3">
                                        @error('multiLangInputs.' . $currentLanguage . '.keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Canonical URL --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="url" 
                                                   wire:model.live="seoData.canonical_url"
                                                   class="form-control @error('seoData.canonical_url') is-invalid @enderror"
                                                   placeholder="{{ __('admin.canonical_url') }}">
                                            <label>{{ __('admin.canonical_url') }}</label>
                                            @error('seoData.canonical_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Focus Keyword --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   wire:model.live="seoData.focus_keyword"
                                                   class="form-control @error('seoData.focus_keyword') is-invalid @enderror"
                                                   placeholder="Odak anahtar kelime">
                                            <label>Odak Anahtar Kelime</label>
                                            @error('seoData.focus_keyword')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Priority --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select wire:model.live="seoData.priority" class="form-select">
                                                <option value="low">Düşük</option>
                                                <option value="medium">Orta</option>
                                                <option value="high">Yüksek</option>
                                                <option value="critical">Kritik</option>
                                            </select>
                                            <label>SEO Önceliği</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- SEO Preview --}}
                                <div class="card bg-light mt-4">
                                    <div class="card-body">
                                        <h6 class="card-title text-muted mb-3">
                                            <i class="fas fa-search me-2"></i>SEO Önizleme
                                        </h6>
                                        <div class="seo-preview">
                                            <div class="preview-title text-primary fw-bold">
                                                {{ $multiLangInputs[$currentLanguage]['title'] ?? 'SEO Başlık' }}
                                            </div>
                                            <div class="preview-url text-success small">
                                                {{ $seoData['canonical_url'] ?? request()->url() }}
                                            </div>
                                            <div class="preview-description text-muted">
                                                {{ $multiLangInputs[$currentLanguage]['description'] ?? 'SEO açıklama metni burada görünecek...' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>

                            {{-- Social Media Tab --}}
                            <x-slot name="social_media">
                                <div class="row">
                                    {{-- Open Graph Title --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   wire:model.live="multiLangInputs.{{ $currentLanguage }}.og_title"
                                                   class="form-control @error('multiLangInputs.' . $currentLanguage . '.og_title') is-invalid @enderror"
                                                   placeholder="Open Graph Başlık"
                                                   maxlength="60">
                                            <label>
                                                Open Graph Başlık ({{ strtoupper($currentLanguage) }})
                                                <small class="text-muted">Boş bırakılırsa SEO başlığı kullanılır</small>
                                            </label>
                                            @error('multiLangInputs.' . $currentLanguage . '.og_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Open Graph Description --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <textarea wire:model.live="multiLangInputs.{{ $currentLanguage }}.og_description"
                                                      class="form-control @error('multiLangInputs.' . $currentLanguage . '.og_description') is-invalid @enderror"
                                                      placeholder="Open Graph Açıklama"
                                                      style="height: 100px"
                                                      maxlength="160"></textarea>
                                            <label>
                                                Open Graph Açıklama ({{ strtoupper($currentLanguage) }})
                                                <small class="text-muted">Boş bırakılırsa SEO açıklaması kullanılır</small>
                                            </label>
                                            @error('multiLangInputs.' . $currentLanguage . '.og_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Open Graph Image --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="url" 
                                                   wire:model.live="seoData.og_image"
                                                   class="form-control @error('seoData.og_image') is-invalid @enderror"
                                                   placeholder="https://example.com/image.jpg">
                                            <label>Open Graph Resim URL</label>
                                            @error('seoData.og_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Open Graph Type --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select wire:model.live="seoData.og_type" class="form-select">
                                                <option value="website">Website</option>
                                                <option value="article">Article</option>
                                                <option value="product">Product</option>
                                                <option value="profile">Profile</option>
                                            </select>
                                            <label>Open Graph Türü</label>
                                        </div>
                                    </div>

                                    {{-- Twitter Card --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <select wire:model.live="seoData.twitter_card" class="form-select">
                                                <option value="summary">Summary</option>
                                                <option value="summary_large_image">Summary Large Image</option>
                                                <option value="app">App</option>
                                                <option value="player">Player</option>
                                            </select>
                                            <label>Twitter Card Türü</label>
                                        </div>
                                    </div>

                                    {{-- Twitter Title --}}
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" 
                                                   wire:model.live="seoData.twitter_title"
                                                   class="form-control @error('seoData.twitter_title') is-invalid @enderror"
                                                   placeholder="Twitter Başlık"
                                                   maxlength="60">
                                            <label>Twitter Başlık</label>
                                            @error('seoData.twitter_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Twitter Description --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <textarea wire:model.live="seoData.twitter_description"
                                                      class="form-control @error('seoData.twitter_description') is-invalid @enderror"
                                                      placeholder="Twitter Açıklama"
                                                      style="height: 80px"
                                                      maxlength="160"></textarea>
                                            <label>Twitter Açıklama</label>
                                            @error('seoData.twitter_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Twitter Image --}}
                                    <div class="col-12 mb-3">
                                        <div class="form-floating">
                                            <input type="url" 
                                                   wire:model.live="seoData.twitter_image"
                                                   class="form-control @error('seoData.twitter_image') is-invalid @enderror"
                                                   placeholder="https://example.com/twitter-image.jpg">
                                            <label>Twitter Resim URL</label>
                                            @error('seoData.twitter_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </x-slot>

                            {{-- Advanced Tab --}}
                            <x-slot name="advanced">
                                <div class="row">
                                    {{-- Robots Meta --}}
                                    <div class="col-12 mb-4">
                                        <label class="form-label">Robots Meta</label>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:model.live="seoData.robots_meta.index" id="robotsIndex">
                                                    <label class="form-check-label" for="robotsIndex">Index</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:model.live="seoData.robots_meta.follow" id="robotsFollow">
                                                    <label class="form-check-label" for="robotsFollow">Follow</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:model.live="seoData.robots_meta.archive" id="robotsArchive">
                                                    <label class="form-check-label" for="robotsArchive">Archive</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:model.live="seoData.robots_meta.snippet" id="robotsSnippet">
                                                    <label class="form-check-label" for="robotsSnippet">Snippet</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Schema Markup --}}
                                    <div class="col-12 mb-3">
                                        <label class="form-label">
                                            Schema Markup (JSON-LD)
                                            <small class="text-muted">Geçerli JSON formatında yapılandırılmış veri</small>
                                        </label>
                                        <textarea wire:model.live="seoData.schema_markup"
                                                  class="form-control @error('seoData.schema_markup') is-invalid @enderror"
                                                  placeholder='{"@context": "https://schema.org", "@type": "Article", "headline": "Başlık"}'
                                                  style="height: 150px; font-family: monospace;"></textarea>
                                        @error('seoData.schema_markup')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Additional Keywords --}}
                                    <div class="col-12 mb-3">
                                        <label class="form-label">
                                            Ek Anahtar Kelimeler
                                            <small class="text-muted">LSI keywords, virgül ile ayırın</small>
                                        </label>
                                        <input type="text" 
                                               wire:model.live="seoData.additional_keywords"
                                               class="form-control @error('seoData.additional_keywords') is-invalid @enderror"
                                               placeholder="ek kelime1, ek kelime2, ek kelime3">
                                        @error('seoData.additional_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </x-slot>

                            {{-- AI Analysis Tab (Placeholder) --}}
                            <x-slot name="ai_analysis">
                                <div class="text-center py-5">
                                    <div class="mb-4">
                                        <i class="fas fa-robot fa-3x text-info mb-3"></i>
                                        <h4 class="text-info">AI SEO Analizi</h4>
                                        <p class="text-muted">Bu özellik yakında kullanıma açılacak!</p>
                                    </div>

                                    <div class="row g-3 justify-content-center">
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                                    <h6>SEO Skoru</h6>
                                                    <p class="small text-muted">Otomatik SEO skorlama</p>
                                                    <button class="btn btn-outline-info btn-sm" disabled>Yakında</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-lightbulb fa-2x text-info mb-2"></i>
                                                    <h6>Optimizasyon Önerileri</h6>
                                                    <p class="small text-muted">AI destekli iyileştirme tavsiyeleri</p>
                                                    <button class="btn btn-outline-info btn-sm" disabled>Yakında</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-magic fa-2x text-info mb-2"></i>
                                                    <h6>Otomatik İçerik</h6>
                                                    <p class="small text-muted">AI ile SEO metni oluşturma</p>
                                                    <button class="btn btn-outline-info btn-sm" disabled>Yakında</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                        </x-tab-system>

                        {{-- Form Actions --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="d-flex gap-2">
                                <span class="badge bg-info">{{ $currentLanguage }}</span>
                                <small class="text-muted">{{ count($availableLanguages) }} dil desteği</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                                    <i class="fas fa-arrow-left me-1"></i>Geri
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>