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
    
    <form wire:submit.prevent="save" id="widget-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="code-editor-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html-pane" type="button" role="tab" aria-controls="html-pane" aria-selected="true">
                                    <i class="fab fa-html5 me-2"></i>
                                    HTML
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="css-tab" data-bs-toggle="tab" data-bs-target="#css-pane" type="button" role="tab" aria-controls="css-pane" aria-selected="false">
                                    <i class="fab fa-css3-alt me-2"></i>
                                    CSS
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="js-tab" data-bs-toggle="tab" data-bs-target="#js-pane" type="button" role="tab" aria-controls="js-pane" aria-selected="false">
                                    <i class="fab fa-js-square me-2"></i>
                                    JavaScript
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-pane" type="button" role="tab" aria-controls="files-pane" aria-selected="false">
                                    <i class="fas fa-file-code me-2"></i>
                                    Harici Dosyalar
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="code-editor-content">
                            <div class="tab-pane fade show active" id="html-pane" role="tabpanel" aria-labelledby="html-tab">
                                <div class="mb-3" wire:ignore>
                                    <div id="html-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_html" 
                                        id="html-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="css-pane" role="tabpanel" aria-labelledby="css-tab">
                                <div class="mb-3" wire:ignore>
                                    <div id="css-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_css" 
                                        id="css-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="js-pane" role="tabpanel" aria-labelledby="js-tab">
                                <div class="mb-3" wire:ignore>
                                    <div id="js-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea 
                                        wire:model.defer="widget.content_js" 
                                        id="js-textarea" 
                                        style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="files-pane" role="tabpanel" aria-labelledby="files-tab">
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
<script>
let htmlEditor, cssEditor, jsEditor;
let editorsInitialized = false;
let monacoLoaded = false;
let widgetData = @json($widget);

const widgetCodeEditor = {
    initialized: false,
    editors: {},
    
    init: function() {
        if (this.initialized) return;
        
        this.loadMonaco();
        this.setupTabs();
        this.setupFormSubmit();
        this.initialized = true;
    },
    
    loadMonaco: function() {
        if (monacoLoaded) {
            this.createEditors();
            return;
        }
        
        if (typeof require === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js';
            script.onload = () => {
                this.setupMonaco();
            };
            document.head.appendChild(script);
        } else {
            this.setupMonaco();
        }
    },
    
    setupMonaco: function() {
        if (typeof require !== 'undefined') {
            require.config({ 
                paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } 
            });

            require(['vs/editor/editor.main'], () => {
                monacoLoaded = true;
                this.createEditors();
            });
        }
    },
    
    createEditors: function() {
        if (editorsInitialized || typeof monaco === 'undefined') return;
        
        const htmlEl = document.getElementById('html-editor');
        const cssEl = document.getElementById('css-editor');
        const jsEl = document.getElementById('js-editor');
        
        if (!htmlEl || !cssEl || !jsEl) {
            setTimeout(() => this.createEditors(), 100);
            return;
        }
        
        try {
            htmlEditor = monaco.editor.create(htmlEl, {
                value: widgetData.content_html || '',
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

            cssEditor = monaco.editor.create(cssEl, {
                value: widgetData.content_css || '',
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

            jsEditor = monaco.editor.create(jsEl, {
                value: widgetData.content_js || '',
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

            this.setupEditorEvents();
            editorsInitialized = true;
            
        } catch (error) {
            console.error('Monaco editor oluşturma hatası:', error);
        }
    },
    
    setupEditorEvents: function() {
        if (!editorsInitialized) return;
        
        let updateTimeout;
        
        const debouncedUpdate = (type) => {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                this.syncEditorToTextarea(type);
                this.updateWidgetData(type);
            }, 300);
        };
        
        htmlEditor.onDidChangeModelContent(() => debouncedUpdate('html'));
        cssEditor.onDidChangeModelContent(() => debouncedUpdate('css'));
        jsEditor.onDidChangeModelContent(() => debouncedUpdate('js'));
    },
    
    setupTabs: function() {
        const tabButtons = document.querySelectorAll('#code-editor-tabs button[data-bs-toggle="tab"]');
        
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', () => {
                setTimeout(() => {
                    this.resizeEditors();
                }, 100);
            });
        });
    },
    
    setupFormSubmit: function() {
        const form = document.getElementById('widget-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                this.updateBeforeSubmit();
            });
        }
    },
    
    syncEditorToTextarea: function(type) {
        if (!editorsInitialized) return;
        
        try {
            const editor = type === 'html' ? htmlEditor : type === 'css' ? cssEditor : jsEditor;
            const textarea = document.getElementById(`${type}-textarea`);
            const value = editor.getValue();
            
            if (textarea) {
                textarea.value = value;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
        } catch (error) {
            console.error('Editor senkronizasyon hatası:', error);
        }
    },
    
    updateWidgetData: function(type) {
        if (!editorsInitialized) return;
        
        try {
            const editor = type === 'html' ? htmlEditor : type === 'css' ? cssEditor : jsEditor;
            const fieldName = `content_${type}`;
            widgetData[fieldName] = editor.getValue();
        } catch (error) {
            console.error('Widget verisi güncelleme hatası:', error);
        }
    },
    
    resizeEditors: function() {
        if (!editorsInitialized) return;
        
        setTimeout(() => {
            try {
                if (htmlEditor) htmlEditor.layout();
                if (cssEditor) cssEditor.layout();
                if (jsEditor) jsEditor.layout();
            } catch (error) {
                console.error('Editor layout hatası:', error);
            }
        }, 50);
    },
    
    formatAllCode: function() {
        if (!editorsInitialized) return;
        
        try {
            if (htmlEditor) htmlEditor.getAction('editor.action.formatDocument').run();
            if (cssEditor) cssEditor.getAction('editor.action.formatDocument').run();
            if (jsEditor) jsEditor.getAction('editor.action.formatDocument').run();
        } catch (error) {
            console.error('Kod formatlama hatası:', error);
        }
    },
    
    updateBeforeSubmit: function() {
        if (!editorsInitialized) return;
        
        this.syncEditorToTextarea('html');
        this.syncEditorToTextarea('css');
        this.syncEditorToTextarea('js');
    },
    
    updateEditorValues: function(newData) {
        if (!editorsInitialized || !newData) return;
        
        try {
            widgetData = newData;
            
            if (htmlEditor && htmlEditor.getValue() !== (newData.content_html || '')) {
                htmlEditor.setValue(newData.content_html || '');
            }
            if (cssEditor && cssEditor.getValue() !== (newData.content_css || '')) {
                cssEditor.setValue(newData.content_css || '');
            }
            if (jsEditor && jsEditor.getValue() !== (newData.content_js || '')) {
                jsEditor.setValue(newData.content_js || '');
            }
        } catch (error) {
            console.error('Editor değer güncelleme hatası:', error);
        }
    }
};

window.formatAllCode = function() {
    widgetCodeEditor.formatAllCode();
};

window.updateWidgetEditors = function(newData) {
    widgetCodeEditor.updateEditorValues(newData);
};

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        widgetCodeEditor.init();
    }, 200);
});

window.addEventListener('resize', function() {
    widgetCodeEditor.resizeEditors();
});

document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        widgetCodeEditor.init();
    }, 300);
});
</script>
@endpush