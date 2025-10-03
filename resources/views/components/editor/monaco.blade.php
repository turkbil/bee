{{--
    MONACO EDITOR COMPONENT
    Universal Monaco Code Editor

    Kullanım:
    <x-editor.monaco
        type="css"
        label="CSS"
        wire-model="inputs.css"
        :value="$inputs['css'] ?? ''"
    />

    Parametreler:
    @param string $type - Editor tipi: 'css' veya 'js'
    @param string $label - Editor label'ı (CSS, JavaScript, vs.)
    @param string $wireModel - Livewire model path
    @param string $value - Başlangıç değeri
    @param string $height - Editor yüksekliği (varsayılan: 350px)
--}}

@php
    $type = $type ?? 'css';
    $label = $label ?? ucfirst($type);
    $wireModel = $wireModel ?? "inputs.{$type}";
    $value = $value ?? '';
    $height = $height ?? '350px';
    $textareaId = "{$type}-textarea";
@endphp

<div class="monaco-editor-container mb-4" wire:ignore>
    <div class="monaco-toolbar">
        <label class="monaco-toolbar-label">{{ $label }}</label>
        <div class="monaco-toolbar-actions">
            <button type="button" class="monaco-toolbar-btn" data-action="format" title="Format">
                <i class="fas fa-magic"></i> Format
            </button>
            <button type="button" class="monaco-toolbar-btn" data-action="find" title="Ara">
                <i class="fas fa-search"></i> Ara
            </button>
            <button type="button" class="monaco-toolbar-btn" data-action="fold" title="Katla/Aç">
                <i class="fas fa-compress-alt"></i> Katla
            </button>
            <button type="button" class="monaco-toolbar-btn" data-action="theme" title="Tema">
                <i class="fas fa-palette"></i> Tema
            </button>
        </div>
    </div>
    <div class="monaco-editor-wrapper"
         data-monaco-editor="{{ $type }}"
         data-monaco-target="{{ $type }}"
         style="height: {{ $height }};"></div>
    <textarea wire:model="{{ $wireModel }}"
              id="{{ $textareaId }}"
              class="monaco-hidden-textarea">{{ $value }}</textarea>
</div>