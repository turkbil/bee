{{-- Page Content Editor with HugeRTE --}}
<div class="mb-3" wire:ignore>
    <label class="form-label">
        <i class="fas fa-edit me-2 text-primary"></i>
        {{ __('page::admin.content') }} ({{ $langName }})
        @if($lang === session('site_default_language', 'tr')) 
            <span class="badge bg-primary ms-2">{{ __('admin.required') }}</span>
        @endif
    </label>
    
    <textarea 
        id="editor_{{ $lang }}" 
        wire:model.defer="multiLangInputs.{{ $lang }}.body"
        class="form-control"
        style="min-height: 500px;"
        placeholder="{{ __('page::admin.content_placeholder') }} ({{ $langName }})...">{{ $langData['body'] ?? '' }}</textarea>
</div>

