@include('widgetmanagement::helper')

<div class="container-fluid">
    @include('admin.partials.error_message')
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 me-4">{{ $widget['name'] }} - Kod Editörü</h3>
                            <span class="badge fs-6 px-3 py-2">{{ ucfirst($widget['type']) }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-dark" onclick="formatAllCode()">
                                <i class="fas fa-magic me-2"></i>
                                Kodu Düzenle
                            </button>
                            <a href="{{ route('admin.widgetmanagement.manage', $widgetId) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Geri Dön
                            </a>
                            <a href="{{ route('admin.widgetmanagement.preview', $widgetId) }}" 
                               class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-eye me-2"></i>
                                Önizleme
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-html" class="nav-link active" data-bs-toggle="tab">
                                    <i class="fab fa-html5 me-2"></i>
                                    HTML
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-css" class="nav-link" data-bs-toggle="tab">
                                    <i class="fab fa-css3-alt me-2"></i>
                                    CSS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-js" class="nav-link" data-bs-toggle="tab">
                                    <i class="fab fa-js-square me-2"></i>
                                    JavaScript
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-files" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-file-code me-2"></i>
                                    Harici Dosyalar
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-html">
                                <div class="mb-3">
                                    <div id="html-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_html" 
                                        id="html-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-css">
                                <div class="mb-3">
                                    <div id="css-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_css" 
                                        id="css-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-js">
                                <div class="mb-3">
                                    <div id="js-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_js" 
                                        id="js-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-files">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">
                                                        <i class="fab fa-css3-alt me-2"></i>
                                                        CSS Dosyaları
                                                    </h4>
                                                    <button type="button" class="btn btn-sm btn-primary" wire:click="addCssFile">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if(empty($widget['css_files']) || count($widget['css_files']) === 0)
                                                <div class="text-center py-3 text-muted">
                                                    <i class="fab fa-css3-alt fa-2x mb-2"></i>
                                                    <p class="mb-0">Henüz CSS dosyası eklenmedi.</p>
                                                </div>
                                                @else
                                                @foreach($widget['css_files'] as $index => $cssFile)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-link"></i>
                                                    </span>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        wire:model.defer="widget.css_files.{{ $index }}" 
                                                        placeholder="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
                                                    <button type="button" class="btn btn-outline-danger" wire:click="removeCssFile({{ $index }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">
                                                        <i class="fab fa-js-square me-2"></i>
                                                        JavaScript Dosyaları
                                                    </h4>
                                                    <button type="button" class="btn btn-sm btn-primary" wire:click="addJsFile">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @if(empty($widget['js_files']) || count($widget['js_files']) === 0)
                                                <div class="text-center py-3 text-muted">
                                                    <i class="fab fa-js-square fa-2x mb-2"></i>
                                                    <p class="mb-0">Henüz JavaScript dosyası eklenmedi.</p>
                                                </div>
                                                @else
                                                @foreach($widget['js_files'] as $index => $jsFile)
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-link"></i>
                                                    </span>
                                                    <input type="text" 
                                                        class="form-control" 
                                                        wire:model.defer="widget.js_files.{{ $index }}" 
                                                        placeholder="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
                                                    <button type="button" class="btn btn-outline-danger" wire:click="removeJsFile({{ $index }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.widgetmanagement.manage', $widgetId) }}" class="btn btn-link text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>
                                Geri Dön
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save me-2"></i>
                                    Kodu Kaydet
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Kaydediliyor...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @php
        $variables = $this->getAvailableVariables();
    @endphp
    
    @if(!empty($variables))
    <div class="row my-4">
        @if(isset($variables['settings']))
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>
                        Özelleştirme Değişkenleri
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            @foreach($variables['settings'] as $var)
                            <tr>
                                <td>
                                    <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                </td>
                                <td class="text-muted">{{ $var['label'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if(isset($variables['items']))
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        İçerik Değişkenleri
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; #each items &#125;&#125;</code>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            @foreach($variables['items'] as $var)
                            <tr>
                                <td>
                                    <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                </td>
                                <td class="text-muted">{{ $var['label'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="mt-2">
                        <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; /each &#125;&#125;</code>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

@push('styles')
<style>
.container-fluid {
    max-width: none;
    padding: 1rem 2rem;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
}

.tab-content {
    padding-top: 1rem;
}

.monaco-editor-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js"></script>
<script>
let htmlEditor, cssEditor, jsEditor;

require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });

require(['vs/editor/editor.main'], function () {
    htmlEditor = monaco.editor.create(document.getElementById('html-editor'), {
        value: @json($widget['content_html']),
        language: 'html',
        theme: 'vs-dark',
        automaticLayout: true,
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        formatOnPaste: true,
        formatOnType: true,
        fontSize: 14,
        lineHeight: 20,
        wordWrap: 'on'
    });

    cssEditor = monaco.editor.create(document.getElementById('css-editor'), {
        value: @json($widget['content_css']),
        language: 'css',
        theme: 'vs-dark',
        automaticLayout: true,
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        formatOnPaste: true,
        formatOnType: true,
        fontSize: 14,
        lineHeight: 20,
        wordWrap: 'on'
    });

    jsEditor = monaco.editor.create(document.getElementById('js-editor'), {
        value: @json($widget['content_js']),
        language: 'javascript',
        theme: 'vs-dark',
        automaticLayout: true,
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        formatOnPaste: true,
        formatOnType: true,
        fontSize: 14,
        lineHeight: 20,
        wordWrap: 'on'
    });

    window.formatAllCode = function() {
        htmlEditor.getAction('editor.action.formatDocument').run();
        cssEditor.getAction('editor.action.formatDocument').run();
        jsEditor.getAction('editor.action.formatDocument').run();
    };

    function syncEditorToTextarea() {
        document.getElementById('html-textarea').value = htmlEditor.getValue();
        document.getElementById('css-textarea').value = cssEditor.getValue();
        document.getElementById('js-textarea').value = jsEditor.getValue();
        
        @this.set('widget.content_html', htmlEditor.getValue());
        @this.set('widget.content_css', cssEditor.getValue());  
        @this.set('widget.content_js', jsEditor.getValue());
    }

    htmlEditor.onDidChangeModelContent(function() {
        syncEditorToTextarea();
    });

    cssEditor.onDidChangeModelContent(function() {
        syncEditorToTextarea();
    });

    jsEditor.onDidChangeModelContent(function() {
        syncEditorToTextarea();
    });

    document.addEventListener('livewire:initialized', function() {
        Livewire.hook('element.updating', function(el, component) {
            syncEditorToTextarea();
        });
    });

    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
    tabLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', function() {
            setTimeout(() => {
                if (htmlEditor) htmlEditor.layout();
                if (cssEditor) cssEditor.layout();
                if (jsEditor) jsEditor.layout();
            }, 100);
        });
    });
});
</script>
@endpush