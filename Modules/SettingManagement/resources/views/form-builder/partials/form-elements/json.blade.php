@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }

    // Temel alan özelliklerini al
    $fieldName = isset($element['name']) ? $element['name'] : (isset($element['properties']['name']) ? $element['properties']['name'] : 'json_' . uniqid());
    $fieldLabel = isset($element['label']) ? $element['label'] : (isset($element['properties']['label']) ? $element['properties']['label'] : 'JSON Veri');
    $isRequired = isset($element['required']) ? $element['required'] : (isset($element['properties']['required']) && $element['properties']['required']);
    $placeholder = isset($element['placeholder']) ? $element['placeholder'] : (isset($element['properties']['placeholder']) ? $element['properties']['placeholder'] : '{}');
    $helpText = isset($element['help_text']) ? $element['help_text'] : (isset($element['properties']['help_text']) ? $element['properties']['help_text'] : '');
    $rows = isset($element['rows']) ? $element['rows'] : (isset($element['properties']['rows']) ? $element['properties']['rows'] : 5);

    // Diğer özellikleri al
    $width = isset($element['width']) ? $element['width'] : (isset($element['properties']['width']) ? $element['properties']['width'] : 12);
    $defaultValue = isset($element['default']) ? $element['default'] : (isset($element['properties']['default_value']) ? $element['properties']['default_value'] : '{}');

    // values ve originalValues kontrolü
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }

    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }

    // Mevcut değeri belirle
    if(isset($values[$fieldName])) {
        $fieldValue = $values[$fieldName];
    } elseif(isset($settings) && is_object($settings)) {
        $cleanFieldName = str_replace('setting.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? $defaultValue;
    } else {
        $fieldValue = $defaultValue;
    }

    // JSON değerini pretty print formatında göster
    if(is_array($fieldValue) || is_object($fieldValue)) {
        $fieldValue = json_encode($fieldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // values için varsayılan değeri ayarla
    if (!isset($values[$fieldName])) {
        $values[$fieldName] = $fieldValue;
    }
@endphp

<div class="col-{{ $width }}">
    <div class="mb-3">
        <label for="{{ $fieldName }}" class="form-label">
            {{ $fieldLabel }}
            @if($isRequired)
                <span class="text-danger">*</span>
            @endif
        </label>
        <textarea
            id="{{ $fieldName }}"
            wire:model.defer="values.{{ $fieldName }}"
            class="form-control font-monospace @error('values.' . $fieldName) is-invalid @enderror"
            placeholder="{{ $placeholder }}"
            rows="{{ $rows }}"
            style="height: {{ (int)$rows * 30 + 30 }}px; font-size: 0.875rem;"
            @if($isRequired) required @endif>{{ $fieldValue }}</textarea>
        @error('values.' . $fieldName)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if($helpText)
            <div class="form-text mt-2">
                <i class="fas fa-info-circle me-1"></i>{{ $helpText }}
            </div>
        @else
            <div class="form-text mt-2">
                <i class="fas fa-info-circle me-1"></i>JSON formatında veri giriniz. Örnek: {"key": "value"}
            </div>
        @endif

        @if(isset($originalValues[$fieldName]) && isset($values[$fieldName]) && $originalValues[$fieldName] != $values[$fieldName])
            <div class="mt-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="resetToDefault('{{ $fieldName }}')">
                    <i class="fas fa-redo me-1"></i> Varsayılana Döndür
                </button>
            </div>
        @endif
    </div>
</div>
