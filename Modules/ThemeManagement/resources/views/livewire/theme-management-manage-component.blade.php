@include('thememanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('thememanagement::admin.theme_information') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Temel Bilgiler -->
                    <div class="col-12">
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.name"
                                class="form-control @error('inputs.name') is-invalid @enderror"
                                placeholder="Tema kodu">
                            <label>{{ __('thememanagement::admin.theme_code') }}</label>
                            @error('inputs.name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">{{ __('thememanagement::admin.theme_code_hint') }}</small>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="inputs.title"
                                class="form-control @error('inputs.title') is-invalid @enderror"
                                placeholder="Tema başlığı">
                            <label>{{ __('thememanagement::admin.theme_title') }}</label>
                            @error('inputs.title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">{{ __('thememanagement::admin.theme_title_hint') }}</small>
                        </div>
                        
                        <!-- Gizli alan olarak folder_name'i name ile aynı yap -->
                        <input type="hidden" wire:model="inputs.folder_name" value="{{ $inputs['name'] }}">
                        
                        <!-- Aktif/Varsayılan Durum -->
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth">
                                    <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                        value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('thememanagement::admin.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('thememanagement::admin.not_active') }}</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="pretty p-default p-curve p-toggle p-smooth">
                                    <input type="checkbox" id="is_default" name="is_default" wire:model="inputs.is_default"
                                        value="1" {{ $inputs['is_default'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('thememanagement::admin.default_theme') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('thememanagement::admin.not_default') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tema Görseli -->
                        <div class="mb-4">
                            <h4 class="form-label">{{ __('thememanagement::admin.theme_preview_image') }}</h4>
                            @include('thememanagement::livewire.partials.image-upload', [
                                'imageKey' => 'thumbnail',
                                'label' => __('thememanagement::admin.drop_or_click_image')
                            ])
                        </div>
                        
                        <!-- Tema Açıklaması -->
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.description" class="form-control" data-bs-toggle="autosize" rows="5" 
                                placeholder="{{ __('thememanagement::admin.theme_description_placeholder') }}"></textarea>
                            <label>{{ __('thememanagement::admin.theme_description') }}</label>
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.thememanagement" :model-id="$themeId" />

        </div>
    </form>
</div>