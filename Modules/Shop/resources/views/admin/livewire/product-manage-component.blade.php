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

                    <!-- ÜRÜN BİLGİLERİ TAB - NO FADE for instant switching -->
                    <div class="tab-pane show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating mb-3 mb-md-0">
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

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug"
                                                id="slug_{{ $lang }}" maxlength="255"
                                                placeholder="urun-url-slug">
                                            <label for="slug_{{ $lang }}">
                                                {{ __('admin.product_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    {{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kategori ve Marka Seçimi (Sadece ilk dilde göster) -->
                                @if ($lang === get_tenant_default_locale())
                                    <div class="row mb-4">
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating">
                                                <select wire:model="inputs.category_id"
                                                    class="form-control @error('inputs.category_id') is-invalid @enderror"
                                                    id="category_select">
                                                    <option value="">{{ __('shop::admin.select_category') }}
                                                    </option>
                                                    @foreach ($this->activeCategories as $category)
                                                        <option value="{{ $category->category_id }}">
                                                            {{ $category->getTranslated('title', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label for="category_select">
                                                    {{ __('shop::admin.category') }}
                                                </label>
                                                @error('inputs.category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-floating">
                                                <select wire:model="inputs.brand_id"
                                                    class="form-control @error('inputs.brand_id') is-invalid @enderror"
                                                    id="brand_select">
                                                    <option value="">{{ __('shop::admin.select_brand') }}
                                                    </option>
                                                    @foreach ($this->activeBrands as $brand)
                                                        <option value="{{ $brand->brand_id }}">
                                                            {{ $brand->getTranslated('title', app()->getLocale()) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <label for="brand_select">
                                                    {{ __('shop::admin.brand') }}
                                                </label>
                                                @error('inputs.brand_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- SKU ve Ürün Özellikleri -->
                                    <div class="row mb-4">
                                        <div class="col-12 col-md-4">
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

                                        <div class="col-12 col-md-4">
                                            <div class="form-floating">
                                                <select wire:model="inputs.product_type" class="form-control"
                                                    id="product_type_select">
                                                    <option value="physical">{{ __('shop::admin.physical') }}</option>
                                                    <option value="digital">{{ __('shop::admin.digital') }}</option>
                                                    <option value="service">{{ __('shop::admin.service') }}</option>
                                                </select>
                                                <label for="product_type_select">
                                                    {{ __('shop::admin.product_type') }}
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="form-floating">
                                                <select wire:model="inputs.condition" class="form-control"
                                                    id="condition_select">
                                                    <option value="new">{{ __('shop::admin.new') }}</option>
                                                    <option value="used">{{ __('shop::admin.used') }}</option>
                                                    <option value="refurbished">{{ __('shop::admin.refurbished') }}
                                                    </option>
                                                </select>
                                                <label for="condition_select">
                                                    {{ __('shop::admin.condition') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fiyat Bilgileri -->
                                    <div class="row mb-4">
                                        <div class="col-12 col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01" wire:model="inputs.base_price"
                                                    class="form-control @error('inputs.base_price') is-invalid @enderror"
                                                    id="base_price_input" placeholder="0.00">
                                                <label for="base_price_input">
                                                    {{ __('shop::admin.base_price') }}
                                                </label>
                                                @error('inputs.base_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="form-floating">
                                                <input type="number" step="0.01"
                                                    wire:model="inputs.compare_at_price" class="form-control"
                                                    id="compare_price_input" placeholder="0.00">
                                                <label for="compare_price_input">
                                                    {{ __('shop::admin.compare_at_price') }}
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-4">
                                            <div class="form-floating">
                                                <input type="text" wire:model="inputs.currency"
                                                    class="form-control @error('inputs.currency') is-invalid @enderror"
                                                    id="currency_input" maxlength="3" placeholder="TRY">
                                                <label for="currency_input">
                                                    {{ __('shop::admin.currency') }}
                                                </label>
                                                @error('inputs.currency')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price on Request Checkbox -->
                                    <div class="mb-3">
                                        <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                            <input type="checkbox" id="price_on_request" name="price_on_request"
                                                wire:model="inputs.price_on_request" value="1" />

                                            <div class="state p-success p-on ms-2">
                                                <label>{{ __('shop::admin.price_on_request') }}</label>
                                            </div>
                                            <div class="state p-danger p-off ms-2">
                                                <label>{{ __('shop::admin.price_show') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ --}}
                        <div class="mb-4">
                            <livewire:mediamanagement::universal-media wire:id="product-media-component"
                                :model-id="$productId" model-type="shop_product"
                                model-class="Modules\Shop\App\Models\ShopProduct" :collections="['featured_image', 'gallery']" :sortable="true"
                                :set-featured-from-gallery="true" :key="'universal-media-' . ($productId ?? 'new')" />
                        </div>

                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                {{-- Kısa Açıklama --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ __('shop::admin.short_description') }} ({{ $langName }})
                                    </label>
                                    <textarea wire:model="multiLangInputs.{{ $lang }}.short_description" class="form-control" rows="3"
                                        maxlength="500" placeholder="{{ __('shop::admin.short_description_placeholder') }}"></textarea>
                                </div>

                                {{-- İçerik editörü - AI button artık global component'te --}}
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

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3 mt-4">
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
    @endpush
</div>
