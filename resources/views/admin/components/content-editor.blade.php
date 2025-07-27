{{-- Global Content Editor Component with HugeRTE --}}
{{-- 
    Global kullanım için tasarlanmış content editor component
    Tüm modüllerde kullanılabilir
    
    Parametreler:
    - $lang: Dil kodu (tr, en, vs.)
    - $langName: Dil adı (Türkçe, English, vs.)
    - $fieldName: Field adı (body, content, description, vs.) - varsayılan: 'body'
    - $modelPath: Model path (multiLangInputs, content, vs.) - varsayılan: 'multiLangInputs'
    - $label: Label metni - varsayılan: 'İçerik'
    - $placeholder: Placeholder metni - varsayılan: 'İçeriği buraya yazın'
    - $required: Zorunlu field mi? - varsayılan: true (sadece default dil için)
    - $height: Editor yüksekliği - varsayılan: '500px'
    - $langData: Mevcut dil verisi array'i
--}}

@php
    // Varsayılan değerler
    $fieldName = $fieldName ?? 'body';
    $modelPath = $modelPath ?? 'multiLangInputs';
    $label = $label ?? 'İçerik';
    $placeholder = $placeholder ?? 'İçeriği buraya yazın';
    $height = $height ?? '500px';
    $required = $required ?? true;
    $defaultLang = session('site_default_language', 'tr');
    $isRequired = $required && ($lang === $defaultLang);
    
    // Unique ID oluştur
    $editorId = "editor_{$fieldName}_{$lang}_" . uniqid();
    $hiddenId = "hidden_{$fieldName}_{$lang}_" . uniqid();
    $wireModel = "{$modelPath}.{$lang}.{$fieldName}";
@endphp

<div class="mb-3">
    <label class="form-label">
        {{ $label }}
        @if($isRequired) 
            <span class="required-star">★</span>
        @endif
    </label>
    
    <div wire:ignore>
        <textarea 
            id="{{ $editorId }}" 
            class="form-control hugerte-editor"
            data-wire-model="{{ $wireModel }}"
            style="min-height: {{ $height }};"
            placeholder="{{ $placeholder }}...">{{ $langData[$fieldName] ?? '' }}</textarea>
    </div>
    
    {{-- Hidden input for Livewire synchronization --}}
    <input type="hidden" wire:model.defer="{{ $wireModel }}" id="{{ $hiddenId }}">
</div>

{{-- Auto-initialization script for HugeRTE --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // HugeRTE için otomatik initialization
    const editorElement = document.getElementById('{{ $editorId }}');
    if (editorElement && typeof window.initializeHugeRTE === 'function') {
        window.initializeHugeRTE('{{ $editorId }}', '{{ $hiddenId }}');
    } else {
        // Fallback: Basit initialization
        console.log('HugeRTE initialization for {{ $editorId }}');
    }
});
</script>
@endpush