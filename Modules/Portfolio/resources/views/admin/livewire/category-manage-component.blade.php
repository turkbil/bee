@php
    View::share('pretitle', __('portfolio::admin.category_management'));
    View::share('title', $categoryId ? __('portfolio::admin.edit_category') : __('portfolio::admin.new_category'));
@endphp

<div class="card">
    @include('portfolio::admin.helper-category')

    <div class="card-body">
        <form wire:submit.prevent="save">
            <!-- Universal Language Switcher -->
            <x-universal-language-switcher
                :currentLanguage="$currentLanguage"
                :availableLanguages="$availableLanguages"
                :languageNames="$languageNames"
                componentId="{{ $this->getId() }}"
            />

            <!-- Tab System -->
            <x-tab-system
                :tabs="$tabConfig"
                :activeTab="$activeTab"
                :completionStatus="$tabCompletionStatus"
                entityType="portfolio_category"
                :entityId="$categoryId"
            >
                <!-- Genel Bilgiler Tab -->
                <x-slot name="general">
                    <div class="row">
                        <!-- Kategori Adı -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label required">{{ __('portfolio::admin.category_name') }}</label>
                            <input type="text"
                                   wire:model.defer="multiLangInputs.{{ $currentLanguage }}.name"
                                   class="form-control @error('multiLangInputs.'.$currentLanguage.'.name') is-invalid @enderror"
                                   placeholder="{{ __('portfolio::admin.category_name_placeholder') }}">
                            @error('multiLangInputs.'.$currentLanguage.'.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('portfolio::admin.slug') }}</label>
                            <input type="text"
                                   wire:model.defer="multiLangInputs.{{ $currentLanguage }}.slug"
                                   class="form-control @error('multiLangInputs.'.$currentLanguage.'.slug') is-invalid @enderror"
                                   placeholder="{{ __('portfolio::admin.slug_placeholder') }}">
                            <small class="form-hint">{{ __('portfolio::admin.slug_hint') }}</small>
                            @error('multiLangInputs.'.$currentLanguage.'.slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Açıklama -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('portfolio::admin.description') }}</label>
                            <textarea
                                wire:model.defer="multiLangInputs.{{ $currentLanguage }}.description"
                                class="form-control @error('multiLangInputs.'.$currentLanguage.'.description') is-invalid @enderror"
                                rows="4"
                                placeholder="{{ __('portfolio::admin.description_placeholder') }}"></textarea>
                            @error('multiLangInputs.'.$currentLanguage.'.description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Sıralama -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('portfolio::admin.sort_order') }}</label>
                            <input type="number"
                                   wire:model.defer="inputs.sort_order"
                                   class="form-control @error('inputs.sort_order') is-invalid @enderror"
                                   placeholder="0">
                            <small class="form-hint">{{ __('portfolio::admin.sort_order_hint') }}</small>
                            @error('inputs.sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Durum -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.status') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model.defer="inputs.is_active"
                                       id="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ __('portfolio::admin.active_category') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </x-slot>

                <!-- SEO Tab -->
                <x-slot name="seo">
                    <livewire:seomanagement::universal-seo-tab-component
                        :entityType="'portfolio_category'"
                        :entityId="$categoryId"
                        :currentLanguage="$currentLanguage"
                        wire:key="seo-tab-{{ $categoryId }}"
                    />
                </x-slot>
            </x-tab-system>

            <!-- Kaydet Butonları -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.portfolio.category.index') }}" class="btn btn-outline-secondary">
                            {{ __('admin.cancel') }}
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" wire:click="save(false)" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">{{ __('admin.save') }}</span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    {{ __('admin.saving') }}
                                </span>
                            </button>
                            <button type="button" wire:click="save(true)" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">{{ __('admin.save_and_close') }}</span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    {{ __('admin.saving') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Form validation ve diğer JS işlemleri
        console.log('Portfolio Category Manage initialized');
    });
</script>
@endpush
