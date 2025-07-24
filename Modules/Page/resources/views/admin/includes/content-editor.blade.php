{{-- Page Content Editor with HugeRTE --}}
<div class="mb-3">
    <label class="form-label">
        {{ __('page::admin.content') }} ({{ $langName }})
        @if($lang === session('site_default_language', 'tr')) 
            <span class="required-star">★</span>
        @endif
    </label>
    
    <div wire:ignore>
        <textarea 
            id="editor_{{ $lang }}" 
            class="form-control hugerte-editor"
            data-wire-model="multiLangInputs.{{ $lang }}.body"
            style="min-height: 500px;"
            placeholder="{{ __('page::admin.content_placeholder') }} ({{ $langName }})...">{{ $langData['body'] ?? '' }}</textarea>
    </div>
    
    {{-- Hidden input for Livewire synchronization --}}
    <input type="hidden" wire:model.defer="multiLangInputs.{{ $lang }}.body" id="hidden_body_{{ $lang }}">
</div>

