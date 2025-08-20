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
        {{-- HugeRTE Editor Textarea --}}
        <textarea 
            id="{{ $editorId }}" 
            class="form-control hugerte-editor"
            style="min-height: {{ $height }};"
            placeholder="{{ $placeholder }}...">{{ $langData[$fieldName] ?? '' }}</textarea>
        
        {{-- Hidden input for Livewire sync --}}
        <input type="hidden" 
               id="hidden_{{ $fieldName }}_{{ $lang }}" 
               wire:model.defer="{{ $wireModel }}">
    </div>
</div>

{{-- HugeRTE Auto-initialization Script - Tabler Official Implementation --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // HugeRTE editor'ün yüklenmesini bekle
    function initHugeRTEEditor() {
        if (typeof hugerte !== 'undefined') {
            let options = {
                selector: '#{{ $editorId }}',
                height: 400,
                menubar: false,
                statusbar: false,
                license_key: 'gpl',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }',
                setup: function (editor) {
                    editor.on('change keyup input', function () {
                        const content = editor.getContent();
                        const hiddenInput = document.getElementById('hidden_{{ $fieldName }}_{{ $lang }}');
                        if (hiddenInput) {
                            hiddenInput.value = content;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                }
            };
            
            // ULTRA ENHANCED: Dark mode detection for page load persistence - multiple fallbacks
            function detectComponentDarkMode() {
                try {
                    // 1. localStorage kontrolü (primary)
                    const tablerTheme = localStorage.getItem('tablerTheme');
                    if (tablerTheme === 'dark') return true;
                    if (tablerTheme === 'light') return false;
                    
                    // 2. DOM element kontrolü (secondary)
                    if (document.body) {
                        const bodyTheme = document.body.getAttribute('data-bs-theme');
                        if (bodyTheme === 'dark') return true;
                        if (bodyTheme === 'light') return false;
                    }
                    
                    // 3. HTML element kontrolü (tertiary)  
                    if (document.documentElement) {
                        const htmlTheme = document.documentElement.getAttribute('data-bs-theme');
                        if (htmlTheme === 'dark') return true;
                        if (htmlTheme === 'light') return false;
                    }
                    
                    // 4. CSS class kontrolü (fallback)
                    if (document.body?.classList.contains('theme-dark')) return true;
                    if (document.documentElement?.classList.contains('theme-dark')) return true;
                    
                    // 5. System preference (ultimate fallback)
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        return true;
                    }
                    
                    return false;
                } catch (e) {
                    console.warn('Component dark mode detection failed:', e);
                    return false;
                }
            }
            
            if (detectComponentDarkMode()) {
                options.skin = 'oxide-dark';
                options.content_css = 'dark';
                // Dark mode için özel CSS override - complete styles
                options.content_style = 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; -webkit-font-smoothing: antialiased; }' + `
                  body { 
                    background-color: #1a1a1a !important; 
                    color: #e3e3e3 !important; 
                  }
                  p { color: #e3e3e3 !important; }
                  h1, h2, h3, h4, h5, h6 { color: #ffffff !important; }
                  a { color: #4dabf7 !important; }
                  blockquote { 
                    border-left: 4px solid #495057 !important; 
                    background-color: #212529 !important; 
                    color: #e3e3e3 !important; 
                  }
                  pre, code { 
                    background-color: #212529 !important; 
                    color: #f8f9fa !important; 
                    border: 1px solid #495057 !important; 
                  }
                  table { border-color: #495057 !important; }
                  td, th { 
                    border-color: #495057 !important; 
                    color: #e3e3e3 !important; 
                  }
                  th { background-color: #343a40 !important; }
                  hr { border-color: #495057 !important; }
                `;
            }
            
            hugerte.init(options);
        } else {
            // HugeRTE henüz yüklenmemişse, kısa süre sonra tekrar dene
            setTimeout(initHugeRTEEditor, 100);
        }
    }
    
    // Editor'ü başlat
    initHugeRTEEditor();
});
</script>
@endpush