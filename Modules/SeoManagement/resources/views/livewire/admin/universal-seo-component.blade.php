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
                        <span class="badge bg-dark">ID: {{ $modelId }}</span>
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
                                {{-- TEMEL SEO ALANLARI --}}
                                <div class="card border-primary mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-star me-2"></i>Temel SEO Ayarları
                                            <small class="opacity-75 ms-2">Mutlaka doldurulması gerekenler</small>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Meta Title --}}
                                            <div class="col-12 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" 
                                                           wire:model.live="multiLangInputs.{{ $currentLanguage }}.title"
                                                           class="form-control seo-no-enter @error('multiLangInputs.' . $currentLanguage . '.title') is-invalid @enderror"
                                                           placeholder="Google'da gözükecek başlık"
                                                           maxlength="60">
                                                    <label>
                                                        <i class="fas fa-heading me-1"></i>
                                                        Meta Title ({{ strtoupper($currentLanguage) }})
                                                        <small class="text-muted">50-60 karakter</small>
                                                    </label>
                                                    <div class="form-text">
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Google arama sonuçlarında gözüken başlık. Tıklanmak isteyecek şekilde yazın.
                                                        </small>
                                                    </div>
                                                    @error('multiLangInputs.' . $currentLanguage . '.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Meta Description --}}
                                            <div class="col-12 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" 
                                                           wire:model.live="multiLangInputs.{{ $currentLanguage }}.description"
                                                           class="form-control seo-no-enter @error('multiLangInputs.' . $currentLanguage . '.description') is-invalid @enderror"
                                                           placeholder="Google'da başlığın altında gözükecek açıklama"
                                                           maxlength="160">
                                                    <label>
                                                        <i class="fas fa-align-left me-1"></i>
                                                        Meta Description ({{ strtoupper($currentLanguage) }})
                                                        <small class="text-muted">150-160 karakter</small>
                                                    </label>
                                                    <div class="form-text">
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Google'da başlığın altında gözüken açıklama. İnsanı tıklamaya teşvik etmeli.
                                                        </small>
                                                    </div>
                                                    @error('multiLangInputs.' . $currentLanguage . '.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Keywords --}}
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-tags me-1"></i>
                                                    Keywords ({{ strtoupper($currentLanguage) }})
                                                    <small class="text-muted">Virgül ile ayırın</small>
                                                </label>
                                                <input type="text" 
                                                       wire:model.live="multiLangInputs.{{ $currentLanguage }}.keywords"
                                                       class="form-control seo-no-enter @error('multiLangInputs.' . $currentLanguage . '.keywords') is-invalid @enderror"
                                                       placeholder="anahtar1, anahtar2, anahtar3">
                                                <div class="form-text">
                                                    <small class="text-info">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        5-10 kelime yeterli. Sayfanızın hangi kelimelerle bulunacağını belirler.
                                                    </small>
                                                </div>
                                                @error('multiLangInputs.' . $currentLanguage . '.keywords')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- Focus Keyword --}}
                                            <div class="col-md-6 mb-3">
                                                <div class="form-floating">
                                                    <input type="text" 
                                                           wire:model.live="seoData.focus_keyword"
                                                           class="form-control seo-no-enter @error('seoData.focus_keyword') is-invalid @enderror"
                                                           placeholder="Ana odaklanılan kelime">
                                                    <label>
                                                        <i class="fas fa-bullseye me-1"></i>
                                                        Focus Keyword (Odak Kelime)
                                                    </label>
                                                    <div class="form-text">
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Sayfanın ana odaklandığı tek kelime. Bu kelimeyi sayfada 3-5 kez geçirin.
                                                        </small>
                                                    </div>
                                                    @error('seoData.focus_keyword')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Priority --}}
                                            <div class="col-md-6 mb-0">
                                                <div class="form-floating">
                                                    <select wire:model.live="seoData.priority" class="form-select">
                                                        <option value="low">📗 Düşük (Blog yazıları, arşiv)</option>
                                                        <option value="medium">📘 Orta (Ürün, kategori sayfaları)</option>
                                                        <option value="high">📙 Yüksek (Ana sayfa, önemli kategoriler)</option>
                                                        <option value="critical">📕 Kritik (Kampanya, en önemli sayfalar)</option>
                                                    </select>
                                                    <label>
                                                        <i class="fas fa-flag me-1"></i>
                                                        SEO Önceliği
                                                    </label>
                                                    <div class="form-text">
                                                        <small class="text-info">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Hangi sayfaların daha önemli olduğunu belirler. Kritik sayfalar önce optimize edilir.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- DIVIDER --}}
                                <div class="text-center my-4">
                                    <div class="border-top border-3 border-secondary position-relative">
                                        <span class="bg-body px-4 position-absolute top-50 start-50 translate-middle text-secondary fw-bold">
                                            <i class="fas fa-cogs me-2"></i>GELİŞMİŞ SEO AYARLARI<i class="fas fa-cogs ms-2"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mt-3">
                                        Aşağıdaki alanlar isteğe bağlıdır. Daha detaylı SEO kontrolü için kullanın.
                                    </small>
                                </div>

                                {{-- GELİŞMİŞ SEO ALANLARI --}}
                                <div class="row">
                                    {{-- Social Media Card --}}
                                    <div class="col-12 mb-4">
                                        <div class="card border-info">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">
                                                    <i class="fab fa-facebook me-2"></i>Sosyal Medya Paylaşımı
                                                    <small class="opacity-75 ms-2">Facebook, WhatsApp, LinkedIn</small>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    {{-- Open Graph Title --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" 
                                                                   wire:model.live="multiLangInputs.{{ $currentLanguage }}.og_title"
                                                                   class="form-control seo-no-enter @error('multiLangInputs.' . $currentLanguage . '.og_title') is-invalid @enderror"
                                                                   placeholder="Sosyal medyada gözükecek başlık"
                                                                   maxlength="60">
                                                            <label>
                                                                Open Graph Başlık ({{ strtoupper($currentLanguage) }})
                                                            </label>
                                                            <div class="form-text">
                                                                <small class="text-muted">Boş bırakılırsa SEO başlığı kullanılır</small>
                                                            </div>
                                                            @error('multiLangInputs.' . $currentLanguage . '.og_title')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Open Graph Description --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" 
                                                                   wire:model.live="multiLangInputs.{{ $currentLanguage }}.og_description"
                                                                   class="form-control seo-no-enter @error('multiLangInputs.' . $currentLanguage . '.og_description') is-invalid @enderror"
                                                                   placeholder="Sosyal medyada gözükecek açıklama"
                                                                   maxlength="160">
                                                            <label>
                                                                Open Graph Açıklama ({{ strtoupper($currentLanguage) }})
                                                            </label>
                                                            <div class="form-text">
                                                                <small class="text-muted">Boş bırakılırsa SEO açıklaması kullanılır</small>
                                                            </div>
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
                                                                   class="form-control seo-no-enter @error('seoData.og_image') is-invalid @enderror"
                                                                   placeholder="https://site.com/resim.jpg">
                                                            <label>
                                                                <i class="fas fa-image me-1"></i>
                                                                Open Graph Resim URL
                                                            </label>
                                                            <div class="form-text">
                                                                <small class="text-info">Sosyal medyada paylaşılınca gözükecek resim</small>
                                                            </div>
                                                            @error('seoData.og_image')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Open Graph Type --}}
                                                    <div class="col-md-6 mb-0">
                                                        <div class="form-floating">
                                                            <select wire:model.live="seoData.og_type" class="form-select">
                                                                <option value="website">🌐 Website (Genel site sayfaları)</option>
                                                                <option value="article">📰 Article (Blog yazıları, makaleler)</option>
                                                                <option value="product">🛍️ Product (Ürün sayfaları)</option>
                                                                <option value="profile">👤 Profile (Profil sayfaları)</option>
                                                            </select>
                                                            <label>Open Graph Türü</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Twitter Card --}}
                                    <div class="col-12 mb-4">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="fab fa-twitter me-2"></i>Twitter Cards
                                                    <small class="opacity-75 ms-2">Twitter'a özel ayarlar</small>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    {{-- Twitter Card Type --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-floating">
                                                            <select wire:model.live="seoData.twitter_card" class="form-select">
                                                                <option value="summary">📝 Summary (Küçük kart)</option>
                                                                <option value="summary_large_image">🖼️ Large Image (Büyük resimli kart)</option>
                                                                <option value="app">📱 App (Uygulama kartı)</option>
                                                                <option value="player">▶️ Player (Video/ses oynatıcı)</option>
                                                            </select>
                                                            <label>Twitter Card Türü</label>
                                                        </div>
                                                    </div>

                                                    {{-- Twitter Title --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" 
                                                                   wire:model.live="seoData.twitter_title"
                                                                   class="form-control seo-no-enter @error('seoData.twitter_title') is-invalid @enderror"
                                                                   placeholder="Twitter'da gözükecek başlık"
                                                                   maxlength="60">
                                                            <label>Twitter Başlık</label>
                                                            @error('seoData.twitter_title')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Twitter Description --}}
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" 
                                                                   wire:model.live="seoData.twitter_description"
                                                                   class="form-control seo-no-enter @error('seoData.twitter_description') is-invalid @enderror"
                                                                   placeholder="Twitter'da gözükecek açıklama"
                                                                   maxlength="160">
                                                            <label>Twitter Açıklama</label>
                                                            @error('seoData.twitter_description')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Twitter Image --}}
                                                    <div class="col-md-6 mb-0">
                                                        <div class="form-floating">
                                                            <input type="url" 
                                                                   wire:model.live="seoData.twitter_image"
                                                                   class="form-control seo-no-enter @error('seoData.twitter_image') is-invalid @enderror"
                                                                   placeholder="https://site.com/twitter-resim.jpg">
                                                            <label>
                                                                <i class="fas fa-image me-1"></i>
                                                                Twitter Resim URL
                                                            </label>
                                                            @error('seoData.twitter_image')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Technical SEO Card --}}
                                    <div class="col-12 mb-3">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-cogs me-2"></i>Teknik SEO
                                                    <small class="opacity-75 ms-2">İleri seviye ayarlar</small>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    {{-- Canonical URL --}}
                                                    <div class="col-12 mb-3">
                                                        <div class="form-floating">
                                                            <input type="url" 
                                                                   wire:model.live="seoData.canonical_url"
                                                                   class="form-control seo-no-enter @error('seoData.canonical_url') is-invalid @enderror"
                                                                   placeholder="https://site.com/asil-sayfa-adresi">
                                                            <label>
                                                                <i class="fas fa-link me-1"></i>
                                                                Canonical URL (Asıl Adres)
                                                            </label>
                                                            <div class="form-text">
                                                                <small class="text-info">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    Google'a "bu sayfanın asıl adresi budur" der. Aynı içerik birden fazla adreste varsa kullanın.
                                                                </small>
                                                            </div>
                                                            @error('seoData.canonical_url')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- Additional Keywords --}}
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">
                                                            <i class="fas fa-plus-circle me-1"></i>
                                                            Additional Keywords (Ek Anahtar Kelimeler)
                                                            <small class="text-muted">Virgül ile ayırın</small>
                                                        </label>
                                                        <input type="text" 
                                                               wire:model.live="seoData.additional_keywords"
                                                               class="form-control seo-no-enter @error('seoData.additional_keywords') is-invalid @enderror"
                                                               placeholder="ek kelime1, ek kelime2, ilgili terim">
                                                        <div class="form-text">
                                                            <small class="text-info">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                Ana kelimenin yanında ilgili kelimeler. LSI (ilgili) kelimeler, doğal geçirin.
                                                            </small>
                                                        </div>
                                                        @error('seoData.additional_keywords')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- Auto Optimize Switch --}}
                                                    <div class="col-12 mb-0">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   wire:model.live="seoData.auto_optimize" id="autoOptimize">
                                                            <label class="form-check-label" for="autoOptimize">
                                                                <i class="fas fa-magic me-1"></i>
                                                                <strong>Auto Optimize (Otomatik Optimizasyon)</strong>
                                                            </label>
                                                            <div class="form-text">
                                                                <small class="text-info">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    Açık olursa: AI sistemi sayfayı otomatik iyileştirir ve öneriler verir.<br>
                                                                    Kapalı olursa: Manuel olarak siz yönetirsiniz.
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
                                                {{ request()->url() }}
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
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">
                                                Schema Markup (JSON-LD)
                                                <small class="text-muted">Geçerli JSON formatında yapılandırılmış veri</small>
                                            </label>
                                            @if($model)
                                                <button type="button" 
                                                        wire:click="generateAutoSchema" 
                                                        class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-magic me-1"></i>Otomatik Oluştur
                                                </button>
                                            @endif
                                        </div>
                                        <textarea wire:model.live="seoData.schema_markup"
                                                  class="form-control @error('seoData.schema_markup') is-invalid @enderror"
                                                  placeholder='{"@context": "https://schema.org", "@type": "Article", "headline": "Başlık"}'
                                                  style="height: 150px; font-family: monospace;"></textarea>
                                        @error('seoData.schema_markup')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if(!empty($seoData['schema_markup']))
                                            <div class="form-text">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Schema markup başarıyla yüklendi
                                                </small>
                                            </div>
                                        @endif
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
                                        <h4 class="text-info">SEO Özelliği</h4>
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