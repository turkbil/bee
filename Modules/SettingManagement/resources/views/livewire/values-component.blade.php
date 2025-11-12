@php
    View::share('pretitle', 'Ayar Değerleri');
@endphp
@include('settingmanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <ul class="nav nav-tabs card-header-tabs flex-fill mb-0" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">{{ $group->name }} - {{ __('settingmanagement::admin.settings_tab_title') }}</a>
                    </li>
                </ul>

                <div class="ms-auto">
                    <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('settingmanagement::admin.back_button') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="tabs-1">
                        @if(isset($group->layout) && !empty($group->layout) && is_array($group->layout))
                            @if(isset($group->layout['elements']) && is_array($group->layout['elements']))
                                <div class="row g-3">
                                    @foreach($group->layout['elements'] as $element)
                                        @include('settingmanagement::form-builder.partials.form-elements.' . $element['type'], [
                                            'element' => $element,
                                            'values' => $values,
                                            'settings' => $settings,
                                            'temporaryImages' => $temporaryImages ?? [],
                                            'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                            'multipleImagesArrays' => $multipleImagesArrays ?? [],
                                            'originalValues' => $originalValues ?? []
                                        ])
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('settingmanagement::admin.form_structure_not_found') }}
                                </div>
                            @endif
                        @else
                            <div class="row g-3">
                                @foreach($settings as $setting)
                                    <div class="col-{{ $setting->width ?? 12 }}" wire:key="setting-{{ $setting->id }}">
                                        @switch($setting->type)
                                            @case('textarea')
                                                <div class="form-floating mb-3">
                                                    <textarea wire:model.defer="values.{{ $setting->id }}" 
                                                        id="setting-{{ $setting->id }}"
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror" 
                                                        style="height: 100px"
                                                        placeholder="{{ $setting->label }}"></textarea>
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('select')
                                                @if(is_array($setting->options))
                                                    @php
                                                        // Support both flat and nested choices format
                                                        $choices = isset($setting->options['choices']) ? $setting->options['choices'] : $setting->options;
                                                    @endphp
                                                    <div class="form-floating mb-3">
                                                        <select wire:model.defer="values.{{ $setting->id }}"
                                                            id="setting-{{ $setting->id }}"
                                                            class="form-select @error('values.' . $setting->id) is-invalid @enderror">
                                                            <option value="">{{ __('settingmanagement::admin.select_option') }}</option>
                                                            @foreach($choices as $key => $label)
                                                                <option value="{{ $key }}">{{ is_string($label) ? $label : json_encode($label) }}</option>
                                                            @endforeach
                                                        </select>
                                                        <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                        @error('values.' . $setting->id)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                @endif
                                                @break
                                            
                                            @case('checkbox')
                                                <div class="mb-3">
                                                    <div class="row">
                                                        <span class="col">{{ $setting->label }}</span>
                                                        <span class="col-auto">
                                                            <label class="form-check form-check-single form-switch">
                                                                <input type="checkbox" 
                                                                    id="setting-{{ $setting->id }}" 
                                                                    class="form-check-input @error('values.' . $setting->id) is-invalid @enderror" 
                                                                    wire:model.defer="values.{{ $setting->id }}"
                                                                    value="1">
                                                            </label>
                                                        </span>
                                                    </div>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('file')
                                            @case('image')
                                            @case('favicon')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $setting->label }}</label>

                                                    {{-- Universal MediaManagement Component --}}
                                                    @livewire('mediamanagement::universal-media', [
                                                        'modelId' => $setting->id,
                                                        'modelType' => 'setting',
                                                        'modelClass' => 'Modules\SettingManagement\App\Models\Setting',
                                                        'collections' => [$setting->getMediaCollectionName()],
                                                        'maxGalleryItems' => 1,
                                                        'sortable' => false,
                                                        'setFeaturedFromGallery' => false
                                                    ], key('setting-media-' . $setting->key . '-' . $setting->id))

                                                    @if(isset($setting->help_text) && !empty($setting->help_text))
                                                        <div class="form-text text-muted mt-2">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            {{ $setting->help_text }}
                                                        </div>
                                                    @endif
                                                </div>
                                                @break
                                                
                                            @case('image_multiple')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $setting->label }}</label>

                                                    {{-- ✅ UniversalMediaComponent kullan (SSL hatası yok!) --}}
                                                    @livewire('mediamanagement::universal-media', [
                                                        'modelId' => $setting->id,
                                                        'modelType' => 'setting',
                                                        'modelClass' => 'Modules\SettingManagement\App\Models\Setting',
                                                        'collections' => ['gallery'],
                                                        'maxGalleryItems' => 20,
                                                        'sortable' => true,
                                                        'setFeaturedFromGallery' => false
                                                    ], key('setting-gallery-' . $setting->id))

                                                    @if(isset($setting->help_text) && !empty($setting->help_text))
                                                        <div class="form-text text-muted mt-2">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            {{ $setting->help_text }}
                                                        </div>
                                                    @endif
                                                </div>
                                                @break

                                            @case('color')
                                                <div class="mb-3">
                                                    <label class="form-label">{{ $setting->label }}</label>
                                                    <input type="color" 
                                                        id="setting-{{ $setting->id }}"
                                                        class="form-control form-control-color @error('values.' . $setting->id) is-invalid @enderror" 
                                                        wire:model.defer="values.{{ $setting->id }}"
                                                        title="{{ __('settingmanagement::admin.choose_color') }}">
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('date')
                                                <div class="form-floating mb-3">
                                                    <input type="date" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">
                                                        <i class="far fa-calendar me-1"></i>
                                                        {{ $setting->label }}
                                                    </label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('time')
                                                <div class="form-floating mb-3">
                                                    <input type="time" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $setting->label }}
                                                    </label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('number')
                                                <div class="form-floating mb-3">
                                                    <input type="number" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('email')
                                                <div class="form-floating mb-3">
                                                    <input type="email" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('password')
                                                <div class="form-floating mb-3">
                                                    <input type="password" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('tel')
                                                <div class="form-floating mb-3">
                                                    <input type="tel" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @case('url')
                                                <div class="form-floating mb-3">
                                                    <input type="url" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                @break
                                            
                                            @default
                                                <div class="form-floating mb-3">
                                                    <input type="text" 
                                                        id="setting-{{ $setting->id }}"
                                                        wire:model.defer="values.{{ $setting->id }}" 
                                                        class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                                                        placeholder="{{ $setting->label }}">
                                                    <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                                                    @error('values.' . $setting->id)
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        @endswitch
                                        
                                        @if(isset($originalValues[$setting->id]) && $originalValues[$setting->id] != $values[$setting->id])
                                            <div class="mt-2 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault({{ $setting->id }})">
                                                    <i class="fas fa-redo me-1"></i> {{ __('settingmanagement::admin.reset_to_default') }}
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(count($changes) > 0)
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ __('settingmanagement::admin.changes_count', ['count' => count($changes)]) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.settingmanagement" :model-id="$group->id" />

        </div>
    </form>
</div>