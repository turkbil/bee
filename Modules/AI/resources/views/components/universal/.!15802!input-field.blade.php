@php
/**
 * Universal AI Input Field Component - ENTERPRISE V3.0.0
 * 
 * Dynamically renders form inputs based on field configuration
 * Supports all input types with context-aware features
 * 
 * @var array $field - Field configuration array
 * @var array $context - Context data for smart defaults
 * @var string $feature - Feature slug for context
 * @var string $fieldIndex - Unique field identifier
 */

$fieldType = $field['type'] ?? 'text';
$fieldName = $field['name'] ?? 'field_' . ($fieldIndex ?? '0');
$fieldId = $field['id'] ?? 'field_' . str_replace('.', '_', $fieldName);
$fieldLabel = $field['label'] ?? ucfirst(str_replace('_', ' ', $fieldName));
$fieldPlaceholder = $field['placeholder'] ?? $fieldLabel;
$fieldValue = $field['value'] ?? ($field['default'] ?? '');
$fieldRequired = $field['required'] ?? false;
$fieldHelp = $field['help'] ?? '';
$fieldClass = $field['class'] ?? '';
$fieldAttributes = $field['attributes'] ?? [];
$fieldOptions = $field['options'] ?? [];
$fieldValidation = $field['validation'] ?? [];
$fieldConditional = $field['conditional'] ?? [];

// Context-aware smart defaults
if (!empty($context) && empty($fieldValue)) {
    $fieldValue = $field['context_value'] ?? '';
}

// Build attributes string
$attributesString = '';
foreach ($fieldAttributes as $attr => $value) {
    $attributesString .= $attr . '="' . htmlspecialchars($value) . '" ';
}
@endphp

<div class="form-field-wrapper" 
     data-field="{{ $fieldName }}" 
     data-type="{{ $fieldType }}"
     @if(!empty($fieldConditional))
         data-conditional="{{ json_encode($fieldConditional) }}"
         x-show="checkFieldCondition('{{ $fieldName }}', @js($fieldConditional))"
         x-transition
     @endif
>
    @switch($fieldType)
        @case('text')
        @case('email')  
        @case('url')
        @case('password')
        @case('number')
            <div class="form-floating mb-3">
                <input 
                    type="{{ $fieldType }}"
                    class="form-control {{ $fieldClass }} @error($fieldName) is-invalid @enderror"
                    id="{{ $fieldId }}"
                    name="{{ $fieldName }}"
                    value="{{ old($fieldName, $fieldValue) }}"
                    placeholder="{{ $fieldPlaceholder }}"
                    @if($fieldRequired) required @endif
                    {!! $attributesString !!}
                    x-data="inputField('{{ $fieldName }}', '{{ $fieldType }}')"
                    x-on:input="validateField"
                    x-on:blur="validateField"
                />
                <label for="{{ $fieldId }}" class="form-label">
                    {{ $fieldLabel }}
                    @if($fieldRequired)<span class="text-danger">*</span>@endif
                </label>
                @error($fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($fieldHelp)
                    <div class="form-text">{{ $fieldHelp }}</div>
                @endif
            </div>
            @break

        @case('textarea')
            <div class="mb-3">
                <label for="{{ $fieldId }}" class="form-label">
                    {{ $fieldLabel }}
                    @if($fieldRequired)<span class="text-danger">*</span>@endif
                </label>
                <textarea 
                    class="form-control {{ $fieldClass }} @error($fieldName) is-invalid @enderror"
                    id="{{ $fieldId }}"
                    name="{{ $fieldName }}"
                    placeholder="{{ $fieldPlaceholder }}"
                    rows="{{ $field['rows'] ?? 4 }}"
                    @if($fieldRequired) required @endif
                    {!! $attributesString !!}
                    x-data="textareaField('{{ $fieldName }}')"
                    x-on:input="updateCharCount; validateField"
                >{{ old($fieldName, $fieldValue) }}</textarea>
                @error($fieldName)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if(isset($field['max_length']))
                    <div class="form-text d-flex justify-content-between">
                        @if($fieldHelp)<span>{{ $fieldHelp }}</span>@endif
                        <span x-text="charCount + '/' + {{ $field['max_length'] }}"></span>
                    </div>
                @elseif($fieldHelp)
                    <div class="form-text">{{ $fieldHelp }}</div>
                @endif
            </div>
            @break

        @case('select')
            <div class="mb-3">
                <label for="{{ $fieldId }}" class="form-label">
                    {{ $fieldLabel }}
                    @if($fieldRequired)<span class="text-danger">*</span>@endif
                </label>
                <select 
                    class="form-select {{ $fieldClass }} @error($fieldName) is-invalid @enderror"
                    id="{{ $fieldId }}"
                    name="{{ $fieldName }}"
                    @if($fieldRequired) required @endif
                    {!! $attributesString !!}
                    x-data="selectField('{{ $fieldName }}')"
                    x-on:change="validateField"
                >
                    @if(!$fieldRequired || empty($fieldValue))
