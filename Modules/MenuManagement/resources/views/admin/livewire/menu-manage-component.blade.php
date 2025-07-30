@include('menumanagement::admin.helper')

<div wire:key="menu-manage-component" wire:id="menu-manage-component">
    @include('admin.partials.error_message')

    <form method="post" wire:submit.prevent="save">
        <div class="card">
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="menu_active_tab">
                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>
            
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <!-- Temel Bilgiler Tab -->
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang->code] ?? [];
                                $langName = $lang->native_name ?? strtoupper($lang->code);
                            @endphp

                            <div class="language-content" data-language="{{ $lang->code }}"
                                style="display: {{ $currentLanguage === $lang->code ? 'block' : 'none' }};">

                                <!-- Menü Adı ve Konum -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang->code }}.name"
                                                class="form-control @error('multiLangInputs.' . $lang->code . '.name') is-invalid @enderror"
                                                placeholder="{{ __('menumanagement::admin.menu_name') }}">
                                            <label>
                                                {{ __('menumanagement::admin.menu_name') }}
                                                @if ($lang->code === session('site_default_language', 'tr'))
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang->code . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang->code }}.slug" maxlength="255"
                                                placeholder="menu-url-slug">
                                            <label>
                                                {{ __('admin.menu_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Açıklama -->
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <textarea class="form-control" wire:model="multiLangInputs.{{ $lang->code }}.description"
                                            placeholder="{{ __('menumanagement::admin.description') }}" style="height: 80px"></textarea>
                                        <label>{{ __('menumanagement::admin.description') }}</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Menü Konumu (sadece bir kere) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select wire:model="inputs.location" class="form-select @error('inputs.location') is-invalid @enderror">
                                        <option value="">{{ __('menumanagement::admin.select_location') }}</option>
                                        @foreach(config('menumanagement.menu_locations') as $key => $location)
                                            <option value="{{ $key }}">{{ __('menumanagement::admin.locations.' . $key) }}</option>
                                        @endforeach
                                    </select>
                                    <label>{{ __('menumanagement::admin.location') }} <span class="required-star">★</span></label>
                                    @error('inputs.location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" wire:model="inputs.sort_order" class="form-control" min="0" step="1">
                                    <label>{{ __('menumanagement::admin.sort_order') }}</label>
                                    <div class="form-text">
                                        <small class="text-muted">{{ __('menumanagement::admin.sort_order_help') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ayarlar -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1" {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('menumanagement::admin.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('menumanagement::admin.inactive') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="is_default" name="is_default" wire:model="inputs.is_default"
                                        value="1" {{ isset($inputs['is_default']) && $inputs['is_default'] ? 'checked' : '' }} />
                                    <div class="state p-warning p-on ms-2">
                                        <label>{{ __('menumanagement::admin.default_menu') }}</label>
                                    </div>
                                    <div class="state p-secondary p-off ms-2">
                                        <label>{{ __('menumanagement::admin.custom_menu') }}</label>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">{{ __('menumanagement::admin.default_menu_help') }}</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" id="cache_enabled" name="cache_enabled" wire:model="inputs.cache_enabled"
                                        value="1" {{ !isset($inputs['cache_enabled']) || $inputs['cache_enabled'] ? 'checked' : '' }} />
                                    <div class="state p-info p-on ms-2">
                                        <label>{{ __('menumanagement::admin.cache_enabled') }}</label>
                                    </div>
                                    <div class="state p-secondary p-off ms-2">
                                        <label>{{ __('menumanagement::admin.cache_disabled') }}</label>
                                    </div>
                                </div>
                                <div class="form-text">
                                    <small class="text-muted">{{ __('menumanagement::admin.cache_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Gelişmiş Ayarlar (Progressive Disclosure) -->
                        <div class="accordion accordion-flush mt-4" id="advancedSettings">
                            <div class="accordion-item border">
                                <h2 class="accordion-header" id="advancedHeading">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#advancedCollapse" aria-expanded="false">
                                        <i class="fas fa-cogs me-2"></i>
                                        {{ __('menumanagement::admin.advanced_settings') }}
                                    </button>
                                </h2>
                                <div id="advancedCollapse" class="accordion-collapse collapse" data-bs-parent="#advancedSettings">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" wire:model="inputs.max_depth" class="form-control" min="1" max="10">
                                                    <label>{{ __('menumanagement::admin.max_depth') }}</label>
                                                    <div class="form-text">
                                                        <small class="text-muted">{{ __('menumanagement::admin.max_depth_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="text" wire:model="inputs.css_class" class="form-control" maxlength="255">
                                                    <label>{{ __('menumanagement::admin.css_class') }}</label>
                                                    <div class="form-text">
                                                        <small class="text-muted">{{ __('menumanagement::admin.css_class_help') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <select wire:model="inputs.template" class="form-select">
                                                        <option value="default">{{ __('menumanagement::admin.template_default') }}</option>
                                                        <option value="horizontal">{{ __('menumanagement::admin.template_horizontal') }}</option>
                                                        <option value="vertical">{{ __('menumanagement::admin.template_vertical') }}</option>
                                                        <option value="dropdown">{{ __('menumanagement::admin.template_dropdown') }}</option>
                                                        <option value="mega">{{ __('menumanagement::admin.template_mega') }}</option>
                                                    </select>
                                                    <label>{{ __('menumanagement::admin.template') }}</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                    <input type="checkbox" id="show_icons" name="show_icons" wire:model="inputs.show_icons"
                                                        value="1" {{ isset($inputs['show_icons']) && $inputs['show_icons'] ? 'checked' : '' }} />
                                                    <div class="state p-info p-on ms-2">
                                                        <label>{{ __('menumanagement::admin.show_icons') }}</label>
                                                    </div>
                                                    <div class="state p-secondary p-off ms-2">
                                                        <label>{{ __('menumanagement::admin.hide_icons') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer text-end">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.menumanagement.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('admin.back') }}
                    </a>
                    <div>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-1"></i>{{ __('admin.save') }}
                        </button>
                        <button type="button" wire:click="saveAndContinue" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>{{ __('admin.save_and_continue') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.required-star {
    color: #dc3545;
    font-size: 0.8em;
}

.accordion-button:not(.collapsed) {
    background-color: var(--bs-light);
    color: var(--bs-dark);
}

.form-text small {
    font-size: 0.875em;
}

.pretty input:checked ~ .state.p-warning label:before {
    border-color: #ffc107;
    background-color: #ffc107;
}

.pretty input:checked ~ .state.p-info label:before {
    border-color: #0dcaf0;
    background-color: #0dcaf0;
}
</style>
@endpush

