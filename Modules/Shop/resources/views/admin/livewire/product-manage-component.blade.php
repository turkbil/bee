@php
    $title = $productId ? __('shop::admin.edit_product') : __('shop::admin.new_product');
    View::share('pretitle', $title);
@endphp

@include('shop::admin.helper')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">{{ $title }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.products.index') }}" class="btn btn-outline-secondary">
                {{ __('admin.back') }}
            </a>
            <button class="btn btn-primary" wire:click="save">
                <i class="ti ti-device-floppy"></i> {{ __('admin.save') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach ($availableLanguages as $locale)
                            <li class="nav-item" role="presentation">
                                <a href="#"
                                   class="nav-link @if ($currentLanguage === $locale) active @endif"
                                   wire:click.prevent="switchLanguage('{{ $locale }}')">
                                    {{ strtoupper($locale) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="border rounded-bottom p-3">
                        <div class="mb-3">
                            <label class="form-label">{{ __('shop::admin.product_title') }}</label>
                            <input type="text"
                                   class="form-control @error(\"multiLangInputs.{$currentLanguage}.title\") is-invalid @enderror"
                                   wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.title">
                            @error("multiLangInputs.{$currentLanguage}.title")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('shop::admin.short_description') }}</label>
                            <textarea class="form-control" rows="3"
                                      wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.short_description"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('shop::admin.long_description') }}</label>
                            <textarea class="form-control" rows="6"
                                      wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.long_description"></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label">{{ __('shop::admin.slug') }}</label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control"
                                       wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.slug">
                                <button class="btn btn-outline-secondary"
                                        wire:click.prevent="generateSlugFor('{{ $currentLanguage }}')">
                                    {{ __('shop::admin.generate_slug') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">{{ __('shop::admin.seo_data') }}</label>
                    <textarea class="form-control"
                              rows="4"
                              wire:model.defer="seoData.custom_meta"
                              placeholder="{{ __('shop::admin.seo_placeholder') }}"></textarea>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="mb-4">
                    <label class="form-label">{{ __('shop::admin.category') }}</label>
                    <select wire:model="inputs.category_id"
                            class="form-select @error('inputs.category_id') is-invalid @enderror">
                        <option value="">{{ __('shop::admin.select_category') }}</option>
        @foreach ($categories as $category)
            <option value="{{ $category->category_id }}">
                {{ $category->getTranslated('title', $currentLanguage) ?? \Illuminate\Support\Arr::first($category->title) }}
            </option>
        @endforeach
                    </select>
                    @error('inputs.category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">{{ __('shop::admin.brand') }}</label>
                    <select wire:model="inputs.brand_id" class="form-select">
                        <option value="">{{ __('shop::admin.select_brand') }}</option>
        @foreach ($brands as $brand)
            <option value="{{ $brand->brand_id }}">
                {{ $brand->getTranslated('title', $currentLanguage) ?? \Illuminate\Support\Arr::first($brand->title) }}
            </option>
        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('shop::admin.sku') }}</label>
                    <input type="text"
                           class="form-control @error('inputs.sku') is-invalid @enderror"
                           wire:model.lazy="inputs.sku">
                    @error('inputs.sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.product_type') }}</label>
                        <select wire:model="inputs.product_type" class="form-select">
                            @foreach (\Modules\Shop\App\Enums\ProductType::cases() as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.condition') }}</label>
                        <select wire:model="inputs.condition" class="form-select">
                            @foreach (\Modules\Shop\App\Enums\ProductCondition::cases() as $condition)
                                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.base_price') }}</label>
                        <input type="number" step="0.01" class="form-control"
                               wire:model.lazy="inputs.base_price">
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.compare_at_price') }}</label>
                        <input type="number" step="0.01" class="form-control"
                               wire:model.lazy="inputs.compare_at_price">
                    </div>
                </div>

                <div class="row g-3 mt-0">
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.currency') }}</label>
                        <input type="text"
                               class="form-control @error('inputs.currency') is-invalid @enderror"
                               wire:model.lazy="inputs.currency"
                               maxlength="3">
                        @error('inputs.currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label">{{ __('shop::admin.sort_order') }}</label>
                        <input type="number" class="form-control"
                               wire:model.lazy="inputs.sort_order">
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" wire:model="inputs.price_on_request" id="porCheckbox">
                    <label class="form-check-label" for="porCheckbox">
                        {{ __('shop::admin.price_on_request') }}
                    </label>
                </div>

                <div class="form-switch mt-3">
                    <input class="form-check-input" type="checkbox" wire:model="inputs.is_active" id="isActiveSwitch">
                    <label class="form-check-label" for="isActiveSwitch">{{ __('shop::admin.active') }}</label>
                </div>
            </div>
        </div>

        {{-- MEDYA YÖNETİMİ --}}
        <div class="mt-4">
            <h4 class="mb-3">{{ __('shop::admin.media_management') }}</h4>
            <livewire:mediamanagement::universal-media
                wire:id="product-media-component"
                :model-id="$productId"
                model-type="shop_product"
                model-class="Modules\Shop\App\Models\ShopProduct"
                :collections="['featured_image', 'gallery']"
                :sortable="true"
                :set-featured-from-gallery="true"
                :key="'universal-media-' . ($productId ?? 'new')"
            />
        </div>
    </div>
</div>
