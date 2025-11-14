@php
    // Field element için setting_key zorunlu
    $settingKey = $element['setting_key'] ?? null;
    $width = $element['width'] ?? 12;

    // Setting'i bul
    $setting = null;
    if ($settingKey && isset($settings)) {
        $setting = $settings->firstWhere('key', $settingKey);
    }

    // Setting bulunamazsa error göster
    if (!$setting) {
        echo '<div class="col-' . $width . '"><div class="alert alert-danger">Setting not found: ' . ($settingKey ?? 'N/A') . '</div></div>';
        return;
    }
@endphp

<div class="col-{{ $width }}" wire:key="setting-{{ $setting->id }}">
    @switch($setting->type)
        @case('textarea')
            @php
                $rows = isset($setting->options['rows']) ? $setting->options['rows'] : 5;
            @endphp
            <div class="form-floating mb-3">
                <textarea wire:model.defer="values.{{ $setting->id }}"
                    id="setting-{{ $setting->id }}"
                    class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                    style="height: {{ $rows * 30 }}px"
                    placeholder="{{ $setting->label }}"></textarea>
                <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                @error('values.' . $setting->id)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
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
                    @if(isset($setting->help_text) && !empty($setting->help_text))
                        <div class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ $setting->help_text }}
                        </div>
                    @endif
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
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
            </div>
            @break

        @case('number')
            @php
                $min = isset($setting->options['min']) ? $setting->options['min'] : null;
                $max = isset($setting->options['max']) ? $setting->options['max'] : null;
            @endphp
            <div class="form-floating mb-3">
                <input type="number"
                    id="setting-{{ $setting->id }}"
                    wire:model.defer="values.{{ $setting->id }}"
                    class="form-control @error('values.' . $setting->id) is-invalid @enderror"
                    placeholder="{{ $setting->label }}"
                    @if($min !== null) min="{{ $min }}" @endif
                    @if($max !== null) max="{{ $max }}" @endif>
                <label for="setting-{{ $setting->id }}">{{ $setting->label }}</label>
                @error('values.' . $setting->id)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
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
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
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
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
            </div>
            @break

        @default
            {{-- Default: text input --}}
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
                @if(isset($setting->help_text) && !empty($setting->help_text))
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $setting->help_text }}
                    </div>
                @endif
            </div>
    @endswitch

    {{-- Reset to default button --}}
    @if(isset($originalValues[$setting->id]) && $originalValues[$setting->id] != $values[$setting->id])
        <div class="mt-2 text-end">
            <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault({{ $setting->id }})">
                <i class="fas fa-redo me-1"></i> {{ __('settingmanagement::admin.reset_to_default') }}
            </button>
        </div>
    @endif
</div>
