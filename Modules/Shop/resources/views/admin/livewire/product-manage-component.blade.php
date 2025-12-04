<div>
    @php
        View::share(
            'pretitle',
            $productId ? __('shop::admin.edit_product_pretitle') : __('shop::admin.new_product_pretitle'),
        );
    @endphp

    @include('shop::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="shop_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $productId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'shop', 'id' => $productId]) }}"
                            target="_blank" class="btn btn-outline-primary"
                            style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i
                                class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('shop::admin.studio.editor') }}
                        </a>
                    </li>
                @endif

                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>

            <div class="card-body">
                <div class="tab-content" id="contentTabContent">

                    <!-- ÜRÜN BİLGİLERİ TAB - Card Grid Yapısı -->
                    <div class="tab-pane show active" id="0" role="tabpanel">

                        <div class="row g-4">

                            {{-- ÜRÜN BİLGİLERİ CARD --}}
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-box me-2"></i>Ürün Bilgileri
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($availableLanguages as $lang)
                                            @php
                                                $langData = $multiLangInputs[$lang] ?? [];
                                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                                            @endphp

                                            <div class="language-content" data-language="{{ $lang }}"
                                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                                <!-- Başlık - EN ÜSTTE -->
                                                <div class="mb-3">
                                                    <div class="form-floating">
                                                        <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                            class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                            placeholder="{{ __('shop::admin.title_field') }}">
                                                        <label>
                                                            {{ __('shop::admin.title_field') }}
                                                            @if ($lang === get_tenant_default_locale())
                                                                <span class="required-star">★</span>
                                                            @endif
                                                        </label>
                                                        @error('multiLangInputs.' . $lang . '.title')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Kategori ve Marka (Sadece ilk dilde) -->
                                                @if ($lang === get_tenant_default_locale())
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <div class="form-floating">
                                                                <select wire:model="inputs.category_id"
                                                                    class="form-control @error('inputs.category_id') is-invalid @enderror"
                                                                    id="category_select">
                                                                    <option value="">{{ __('shop::admin.select_category') }}</option>
                                                                    @foreach ($this->activeCategories as $category)
                                                                        <option value="{{ $category->category_id }}">
                                                                            {{ $category->getTranslated('title', app()->getLocale()) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <label for="category_select">{{ __('shop::admin.category') }}</label>
                                                                @error('inputs.category_id')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="form-floating">
                                                                <select wire:model="inputs.brand_id"
                                                                    class="form-control @error('inputs.brand_id') is-invalid @enderror"
                                                                    id="brand_select">
                                                                    <option value="">{{ __('shop::admin.select_brand') }}</option>
                                                                    @foreach ($this->activeBrands as $brand)
                                                                        <option value="{{ $brand->brand_id }}">
                                                                            {{ $brand->getTranslated('title', app()->getLocale()) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <label for="brand_select">{{ __('shop::admin.brand') }}</label>
                                                                @error('inputs.brand_id')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Slug -->
                                                <div class="mb-3">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                            wire:model="multiLangInputs.{{ $lang }}.slug"
                                                            id="slug_{{ $lang }}" maxlength="255"
                                                            placeholder="urun-url-slug">
                                                        <label for="slug_{{ $lang }}">
                                                            {{ __('admin.product_url_slug') }}
                                                            <small class="text-muted ms-2">- {{ __('admin.slug_auto_generated') }}</small>
                                                        </label>
                                                        <div class="form-text">
                                                            <small class="text-muted">{{ __('admin.slug_help') }}</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Kısa Açıklama -->
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        {{ __('shop::admin.short_description') }} ({{ $langName }})
                                                    </label>
                                                    <textarea wire:model="multiLangInputs.{{ $lang }}.short_description" class="form-control" rows="3"
                                                        maxlength="500" placeholder="{{ __('shop::admin.short_description_placeholder') }}"></textarea>
                                                </div>

                                                <!-- SKU, Ürün Tipi, Durum (Sadece ilk dilde) -->
                                                @if ($lang === get_tenant_default_locale())
                                                    <div class="mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" wire:model="inputs.sku"
                                                                class="form-control @error('inputs.sku') is-invalid @enderror"
                                                                id="sku_input" placeholder="SKU">
                                                            <label for="sku_input">
                                                                {{ __('shop::admin.sku') }}
                                                                <span class="required-star">★</span>
                                                            </label>
                                                            @error('inputs.sku')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    {{-- 🔴 YENİ: Model Numarası --}}
                                                    <div class="mb-3">
                                                        <div class="form-floating">
                                                            <input type="text" wire:model="inputs.model_number"
                                                                class="form-control @error('inputs.model_number') is-invalid @enderror"
                                                                id="model_number_input" placeholder="Model Numarası">
                                                            <label for="model_number_input">Model Numarası</label>
                                                            @error('inputs.model_number')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-text">
                                                            <small class="text-muted">Üretici model kodu (örn: XF-2000-LT)</small>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <div class="form-floating">
                                                                <select wire:model="inputs.product_type" class="form-control"
                                                                    id="product_type_select">
                                                                    <option value="physical">{{ __('shop::admin.physical') }}</option>
                                                                    <option value="digital">{{ __('shop::admin.digital') }}</option>
                                                                    <option value="service">{{ __('shop::admin.service') }}</option>
                                                                </select>
                                                                <label for="product_type_select">{{ __('shop::admin.product_type') }}</label>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="form-floating">
                                                                <select wire:model="inputs.condition" class="form-control"
                                                                    id="condition_select">
                                                                    <option value="new">{{ __('shop::admin.new') }}</option>
                                                                    <option value="used">{{ __('shop::admin.used') }}</option>
                                                                    <option value="refurbished">{{ __('shop::admin.refurbished') }}</option>
                                                                </select>
                                                                <label for="condition_select">{{ __('shop::admin.condition') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- 🔴 YENİ: Öne Çıkan & Çok Satan --}}
                                                    <div class="row mb-3">
                                                        <div class="col-6">
                                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                                <input type="checkbox" id="is_featured" wire:model="inputs.is_featured" value="1" />

                                                                <div class="state p-success p-on ms-2">
                                                                    <label>⭐ Öne Çıkan Ürün</label>
                                                                </div>
                                                                <div class="state p-danger p-off ms-2">
                                                                    <label>Öne Çıkan Değil</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                                <input type="checkbox" id="is_bestseller" wire:model="inputs.is_bestseller" value="1" />

                                                                <div class="state p-success p-on ms-2">
                                                                    <label>🔥 Çok Satan</label>
                                                                </div>
                                                                <div class="state p-danger p-off ms-2">
                                                                    <label>Normal Satış</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Anasayfada Göster -->
                                                    <div class="mb-3">
                                                        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                            <input type="checkbox" id="show_on_homepage_top" wire:model="inputs.show_on_homepage" value="1" />

                                                            <div class="state p-success p-on ms-2">
                                                                <label>Anasayfada Göster</label>
                                                            </div>
                                                            <div class="state p-danger p-off ms-2">
                                                                <label>Anasayfada Gösterme</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Aktif/Pasif + Yayın Tarihi (en altta, yan yana) -->
                                                    <div class="row mb-0">
                                                        <div class="col-6">
                                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                                                    value="1"
                                                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                                                <div class="state p-success p-on ms-2">
                                                                    <label>{{ __('shop::admin.active') }}</label>
                                                                </div>
                                                                <div class="state p-danger p-off ms-2">
                                                                    <label>{{ __('shop::admin.inactive') }}</label>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-6">
                                                            <div class="form-floating">
                                                                <input type="datetime-local" wire:model="inputs.published_at"
                                                                    class="form-control @error('inputs.published_at') is-invalid @enderror"
                                                                    id="published_at_input">
                                                                <label for="published_at_input"><small>📅 Yayın Tarihi (opsiyonel)</small></label>
                                                                @error('inputs.published_at')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- FİYATLANDIRMA (KDV) CARD --}}
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary-lt">
                                        <h3 class="card-title">
                                            <i class="fas fa-dollar-sign me-2"></i>Fiyatlandırma (KDV)
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <!-- KDV Hariç + KDV Dahil -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" step="0.01" wire:model.live="inputs.base_price"
                                                        class="form-control @error('inputs.base_price') is-invalid @enderror"
                                                        id="base_price_input" placeholder="0.00">
                                                    <label for="base_price_input">KDV Hariç (₺)</label>
                                                    @error('inputs.base_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" step="0.01" wire:model.live="price_with_tax"
                                                        class="form-control"
                                                        id="price_with_tax_input" placeholder="0.00">
                                                    <label for="price_with_tax_input">KDV Dahil (₺)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- KDV Oranı -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" wire:model.live="tax_rate"
                                                    class="form-control"
                                                    id="tax_rate_input" placeholder="20.00">
                                                <label for="tax_rate_input">KDV Oranı (%)</label>
                                            </div>
                                        </div>

                                        <!-- Liste Fiyatı (Üstü Çizili) - KDV Dahil -->
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" step="0.01"
                                                    wire:model="inputs.compare_at_price" class="form-control"
                                                    id="compare_price_input" placeholder="0.00">
                                                <label for="compare_price_input">Liste Fiyatı (Üstü Çizili) - KDV Dahil</label>
                                            </div>
                                            <div class="form-text">
                                                <small class="text-muted">İndirim varsa piyasa fiyatını girin (üstü çizili gösterilir)</small>
                                            </div>
                                        </div>

                                        {{-- 🔴 YENİ: Maliyet Fiyatı --}}
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" step="0.01"
                                                    wire:model="inputs.cost_price" class="form-control"
                                                    id="cost_price_input" placeholder="0.00">
                                                <label for="cost_price_input">💰 Maliyet Fiyatı - KDV Hariç (₺)</label>
                                            </div>
                                            <div class="form-text">
                                                <small class="text-muted">Kar marjı hesaplaması için (sadece admin görür)</small>
                                            </div>
                                        </div>

                                        <!-- Para Birimi + Fiyat Gösterimi -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <select wire:model.live="inputs.currency_id"
                                                        class="form-control @error('inputs.currency_id') is-invalid @enderror"
                                                        id="currency_select">
                                                        <option value="">-- {{ __('admin.select') }} --</option>
                                                        @foreach($this->activeCurrencies as $currency)
                                                            <option value="{{ $currency->currency_id }}" {{ $currency->code === 'TRY' ? 'selected' : '' }}>
                                                                {{ $currency->code }} ({{ $currency->symbol }})
                                                                @if($currency->is_default)
                                                                    ★
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label for="currency_select">{{ __('shop::admin.currency') }}</label>
                                                    @error('inputs.currency_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <select wire:model="inputs.price_display_mode"
                                                        class="form-control @error('inputs.price_display_mode') is-invalid @enderror"
                                                        id="price_display_mode_select">
                                                        <option value="show">Fiyatı Göster</option>
                                                        <option value="hide">Fiyatı Gizle</option>
                                                        <option value="request">Fiyat Sorunuz</option>
                                                    </select>
                                                    <label for="price_display_mode_select">Fiyat Gösterimi</label>
                                                    @error('inputs.price_display_mode')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        {{-- STOK YÖNETİMİ - Fiyatlandırma Kartı İçinde --}}
                                        <h4 class="mb-3"><i class="fas fa-warehouse me-2"></i>Stok Yönetimi</h4>

                                        <!-- Stok Takibi -->
                                        <div class="mb-3">
                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                <input type="checkbox" id="stock_tracking" wire:model="inputs.stock_tracking" value="1" />

                                                <div class="state p-success p-on ms-2">
                                                    <label>Stok Takibi Aktif</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>Stok Takibi Kapalı</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 🔴 YENİ: Backorder --}}
                                        <div class="mb-3">
                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                <input type="checkbox" id="allow_backorder" wire:model="inputs.allow_backorder" value="1" />

                                                <div class="state p-success p-on ms-2">
                                                    <label>✅ Stokta Yokken Sipariş Alınabilir</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>❌ Stokta Yokken Sipariş Alınamaz</label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 🔴 YENİ: Temin Süresi --}}
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" wire:model="inputs.lead_time_days" class="form-control"
                                                    id="lead_time_days_input" placeholder="0">
                                                <label for="lead_time_days_input">⏱️ Temin Süresi (Gün)</label>
                                            </div>
                                            <div class="form-text">
                                                <small class="text-muted">Ürün stokta yoksa kaç günde temin edilir?</small>
                                            </div>
                                        </div>

                                        <!-- Mevcut Stok + Minimum Stok -->
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" wire:model="inputs.current_stock" class="form-control"
                                                        id="current_stock_input" placeholder="0">
                                                    <label for="current_stock_input">Mevcut Stok</label>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-floating">
                                                    <input type="number" wire:model="inputs.min_stock" class="form-control"
                                                        id="min_stock_input" placeholder="0">
                                                    <label for="min_stock_input">Minimum Stok</label>
                                                </div>
                                                <div class="form-text">
                                                    <small class="text-muted">Uyarı seviyesi</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Barkod -->
                                        <div class="mb-0">
                                            <div class="form-floating">
                                                <input type="text" wire:model="inputs.barcode" class="form-control"
                                                    id="barcode_input" placeholder="Barkod">
                                                <label for="barcode_input">Barkod</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MEDYA YÖNETİMİ CARD --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-images me-2"></i>Medya Yönetimi
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <livewire:mediamanagement::universal-media wire:id="product-media-component"
                                            :model-id="$productId" model-type="shop_product"
                                            model-class="Modules\Shop\App\Models\ShopProduct" :collections="['hero', 'gallery']" :sortable="true"
                                            :set-featured-from-gallery="true" :key="'universal-media-' . ($productId ?? 'new')" />

                                        <hr class="my-4">

                                        {{-- 🔴 YENİ: Video URL --}}
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="url" wire:model="inputs.video_url" class="form-control"
                                                    id="video_url_input" placeholder="https://youtube.com/...">
                                                <label for="video_url_input">🎬 Video URL (YouTube/Vimeo)</label>
                                            </div>
                                            <div class="form-text">
                                                <small class="text-muted">Ürün tanıtım videosu linki</small>
                                            </div>
                                        </div>

                                        {{-- 🔴 YENİ: PDF Kılavuzu --}}
                                        <div class="mb-0">
                                            <label class="form-label">📄 Kullanım Kılavuzu (PDF)</label>
                                            <input type="file" wire:model="manual_pdf" class="form-control" accept=".pdf">
                                            @if(isset($inputs['manual_pdf_url']) && $inputs['manual_pdf_url'])
                                                <div class="mt-2">
                                                    <a href="{{ $inputs['manual_pdf_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-file-pdf me-1"></i>Mevcut Kılavuzu Görüntüle
                                                    </a>
                                                </div>
                                            @endif
                                            @error('manual_pdf')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- İÇERİK CARD --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-file-alt me-2"></i>Ürün İçeriği
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach ($availableLanguages as $lang)
                                            @php
                                                $langData = $multiLangInputs[$lang] ?? [];
                                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                                            @endphp

                                            <div class="language-content" data-language="{{ $lang }}"
                                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                                {{-- İçerik editörü --}}
                                                @include('admin.components.content-editor', [
                                                    'lang' => $lang,
                                                    'langName' => $langName,
                                                    'langData' => $langData,
                                                    'fieldName' => 'body',
                                                    'label' => __('shop::admin.content'),
                                                    'placeholder' => __('shop::admin.content_placeholder'),
                                                ])
                                            </div>
                                        @endforeach

                                        <hr class="my-4">

                                        {{-- 🔴 YENİ: Etiketler (Tags) --}}
                                        <div class="mb-0">
                                            <label class="form-label">🏷️ Etiketler (Tags)</label>
                                            <input type="text" wire:model="inputs.tags" class="form-control"
                                                id="tags_input" placeholder="elektrikli, forklift, lityum, 2 ton">
                                            <div class="form-text">
                                                <small class="text-muted">Virgülle ayırın. SEO ve arama için kullanılır.</small>
                                            </div>
                                            @if(isset($inputs['tags']) && is_array($inputs['tags']))
                                                <div class="mt-2">
                                                    @foreach($inputs['tags'] as $tag)
                                                        <span class="badge bg-primary me-1">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- BADGE YÖNETİMİ CARD --}}
                            <div class="col-12">
                                @include('shop::admin.partials.badge-manager')
                            </div>

                            {{-- 🔴 YENİ: FİZİKSEL ÖZELLİKLER CARD --}}
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-weight-hanging me-2"></i>Fiziksel Özellikler
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Ağırlık --}}
                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" wire:model="inputs.weight" class="form-control"
                                                    id="weight_input" placeholder="0.00">
                                                <label for="weight_input">⚖️ Ağırlık (kg)</label>
                                            </div>
                                        </div>

                                        {{-- Boyutlar --}}
                                        <div class="row mb-3">
                                            <div class="col-4">
                                                <div class="form-floating">
                                                    <input type="number" step="0.01" wire:model="inputs.dimensions.length" class="form-control"
                                                        id="length_input" placeholder="0">
                                                    <label for="length_input">Uzunluk (cm)</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-floating">
                                                    <input type="number" step="0.01" wire:model="inputs.dimensions.width" class="form-control"
                                                        id="width_input" placeholder="0">
                                                    <label for="width_input">Genişlik (cm)</label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-floating">
                                                    <input type="number" step="0.01" wire:model="inputs.dimensions.height" class="form-control"
                                                        id="height_input" placeholder="0">
                                                    <label for="height_input">Yükseklik (cm)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <small>Kargo hesaplaması ve ürün karşılaştırma için kullanılır</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 🔴 YENİ: TEKNİK ÖZELLİKLER CARD --}}
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-cogs me-2"></i>Teknik Özellikler
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Alpine.js ile dinamik key-value listesi --}}
                                        <div x-data="technicalSpecsManager()">
                                            <template x-if="!specs || specs.length === 0">
                                                <div class="text-center text-muted py-3">
                                                    <i class="fas fa-cog fa-2x mb-2"></i>
                                                    <p>Henüz teknik özellik eklenmemiş</p>
                                                </div>
                                            </template>

                                            <template x-for="(spec, index) in specs" :key="index">
                                                <div class="row mb-2">
                                                    <div class="col-5">
                                                        <input type="text" class="form-control" placeholder="Özellik Adı"
                                                            x-model="spec.key">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" class="form-control" placeholder="Değer"
                                                            x-model="spec.value">
                                                    </div>
                                                    <div class="col-1">
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            @click="removeSpec(index)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <button type="button" class="btn btn-sm btn-primary mt-2" @click="addSpec()">
                                                <i class="fas fa-plus me-1"></i>Özellik Ekle
                                            </button>
                                        </div>

                                        <div class="alert alert-info mt-3 mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <small><strong>Örnek:</strong> Kapasite → 2.0 Ton, Motor → 24V Elektrik</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 🔴 YENİ: GARANTİ & KARGO CARD --}}
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-shield-alt me-2"></i>Garanti & Kargo
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Garanti Bölümü --}}
                                        <h5 class="mb-3"><i class="fas fa-award me-2"></i>Garanti Bilgileri</h5>

                                        <div class="mb-3">
                                            <div class="form-floating">
                                                <input type="number" wire:model="inputs.warranty_info.period" class="form-control"
                                                    id="warranty_period_input" placeholder="0">
                                                <label for="warranty_period_input">Garanti Süresi (Ay)</label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Garanti Detayları</label>
                                            <textarea wire:model="inputs.warranty_info.details" class="form-control" rows="3"
                                                placeholder="Garanti kapsamı ve şartları..."></textarea>
                                        </div>

                                        <hr class="my-4">

                                        {{-- Kargo Bölümü --}}
                                        <h5 class="mb-3"><i class="fas fa-truck me-2"></i>Kargo Bilgileri</h5>

                                        <div class="mb-3">
                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                <input type="checkbox" id="free_shipping" wire:model="inputs.shipping_info.free_shipping" value="1" />

                                                <div class="state p-success p-on ms-2">
                                                    <label>🚚 Ücretsiz Kargo</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>Ücretli Kargo</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <div class="form-floating">
                                                <select wire:model="inputs.shipping_info.size_limit" class="form-control">
                                                    <option value="">Sınırlama Yok</option>
                                                    <option value="small">Küçük (< 5kg)</option>
                                                    <option value="medium">Orta (5-20kg)</option>
                                                    <option value="large">Büyük (20-100kg)</option>
                                                    <option value="xlarge">Çok Büyük (> 100kg)</option>
                                                </select>
                                                <label>Kargo Boyut Limiti</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$productId" model-type="shop_product"
                            model-class="Modules\Shop\App\Models\ShopProduct" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.shop.products" :model-id="$productId" />

        </div>

        {{-- JSON İÇERİK YÖNETİMİ --}}
        @if ($productId)
            @include('shop::admin.partials.json-manager')
        @endif

    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $productId ?? 'null' }};
            window.currentModuleName = 'shop';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔥 TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('restore-active-tab', () => {
                    console.log('🔄 Tab restore tetiklendi (validation error)');

                    // forceTabRestore fonksiyonu tab-system.blade.php'de tanımlı
                    if (typeof window.forceTabRestore === 'function') {
                        setTimeout(() => {
                            window.forceTabRestore();
                        }, 100);
                    } else {
                        console.warn('⚠️ forceTabRestore fonksiyonu bulunamadı');
                    }
                });

                // 🔄 BROWSER REDIRECT - Event işlendikten sonra yönlendir
                Livewire.on('browser', (event) => {
                    console.log('🔄 Browser event:', event);

                    if (event.action === 'redirect') {
                        const delay = event.delay || 0;
                        console.log(`🔄 Redirecting to ${event.url} after ${delay}ms`);

                        setTimeout(() => {
                            window.location.href = event.url;
                        }, delay);
                    }
                });
            });
        </script>

        {{-- 🌍 UNIVERSAL SYSTEMS --}}
        @include('languagemanagement::admin.components.universal-language-scripts', [
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => $availableLanguages,
        ])

        @include('seomanagement::admin.components.universal-seo-scripts', [
            'availableLanguages' => $availableLanguages,
        ])

        @include('ai::admin.components.universal-ai-content-scripts')

        {{-- 🔧 TEKNİK ÖZELLİKLER MANAGER --}}
        <script>
        // 🔧 Teknik Özellikler Manager
        function technicalSpecsManager() {
            return {
                specs: @entangle('inputs.technical_specs') || [],

                addSpec() {
                    if (!this.specs) this.specs = [];
                    this.specs.push({ key: '', value: '' });
                },

                removeSpec(index) {
                    if (confirm('Bu özelliği silmek istediğinize emin misiniz?')) {
                        this.specs.splice(index, 1);
                    }
                }
            }
        }
        </script>
    @endpush
</div>
