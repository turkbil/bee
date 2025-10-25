@include('shop::admin.helper')

@php
    $title = $brandId ? __('shop::admin.edit_brand') : __('shop::admin.new_brand');
    View::share('pretitle', $title);
@endphp

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">{{ $title }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.shop.brands.index') }}" class="btn btn-outline-secondary">
                {{ __('admin.back') }}
            </a>
            <button class="btn btn-primary" wire:click="save">
                <i class="fas fa-save"></i> {{ __('admin.save') }}
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-7">
                <ul class="nav nav-tabs" role="tablist">
                    @foreach ($availableLanguages as $locale)
                        <li class="nav-item">
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
                        <label class="form-label">{{ __('shop::admin.brand_name') }}</label>
                        <input type="text"
                               class="form-control @error(\"multiLangInputs.{$currentLanguage}.title\") is-invalid @enderror"
                               wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.title">
                        @error("multiLangInputs.{$currentLanguage}.title")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('shop::admin.brand_description') }}</label>
                        <textarea class="form-control" rows="5"
                                  wire:model.lazy="multiLangInputs.{{ $currentLanguage }}.description"></textarea>
                    </div>

                    <div>
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

            <div class="col-lg-5">
                {{-- Brand Logo Media Upload --}}
                <div class="mb-3">
                    <label class="form-label">{{ __('shop::admin.brand_logo') }}</label>
                    <livewire:mediamanagement::universal-media
                        wire:id="brand-media-component"
                        :model-id="$brandId"
                        model-type="shop_brand"
                        model-class="Modules\Shop\App\Models\ShopBrand"
                        :collections="['brand_logo']"
                        :sortable="false"
                        :key="'universal-media-' . ($brandId ?? 'new')"
                    />
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('shop::admin.logo_url') }} <small class="text-muted">({{ __('shop::admin.optional_fallback') }})</small></label>
                    <input type="url" class="form-control" wire:model.lazy="inputs.logo_url">
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('shop::admin.website_url') }}</label>
                    <input type="url" class="form-control" wire:model.lazy="inputs.website_url">
                </div>

               <div class="row g-3">
                    <div class="col-4">
                        <label class="form-label">{{ __('shop::admin.country') }}</label>
                        <input type="text" maxlength="2" class="form-control"
                               wire:model.lazy="inputs.country_code">
                    </div>
                    <div class="col-4">
                        <label class="form-label">{{ __('shop::admin.founded_year') }}</label>
                        <input type="number" class="form-control"
                               wire:model.lazy="inputs.founded_year">
                    </div>
                    <div class="col-4">
                        <label class="form-label">{{ __('shop::admin.sort_order') }}</label>
                        <input type="number" class="form-control"
                               wire:model.lazy="inputs.sort_order">
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">{{ __('shop::admin.headquarters') }}</label>
                    <input type="text" class="form-control"
                           wire:model.lazy="inputs.headquarters">
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" wire:model="inputs.is_featured" id="featuredSwitch">
                    <label class="form-check-label" for="featuredSwitch">{{ __('shop::admin.featured') }}</label>
                </div>

                <div class="form-switch mt-3">
                    <input class="form-check-input" type="checkbox" wire:model="inputs.is_active" id="isActiveSwitch">
                    <label class="form-check-label" for="isActiveSwitch">{{ __('shop::admin.active') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>
