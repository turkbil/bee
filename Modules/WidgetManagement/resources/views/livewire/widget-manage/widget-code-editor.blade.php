@include('widgetmanagement::helper')
@php
// Bu şablon WidgetCodeEditorComponent tarafından kullanılıyor
@endphp

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
                            <a href="{{ route('admin.widgetmanagement.manage', $widgetId) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Geri Dön
                            </a>
                            <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'settings_schema']) }}" 
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-sliders-h me-2"></i>
                                Özelleştirme Ayarları
                            </a>
                            
                            @if($widget['has_items'])
                            <a href="{{ route('admin.widgetmanagement.form-builder.edit', ['widgetId' => $widgetId, 'schemaType' => 'item_schema']) }}" 
                               class="btn btn-outline-success" target="_blank">
                                <i class="fas fa-layer-group me-2"></i>
                                İçerik Yapısı
                            </a>
                            @endif
                            
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
    
    @php
        $variables = $this->getAvailableVariables();
    @endphp
    
    @if(!empty($variables))
    <div class="row mb-4">
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
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-info-circle text-blue me-3 mt-1"></i>
                    </div>
                    <div>
                        <h4 class="alert-title">Handlebars Şablon Değişkenleri</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Değişkenler:</strong><br>
                                <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; değişken_adı &#125;&#125;</code>
                            </div>
                            <div class="col-md-4">
                                <strong>Dinamik içerikler:</strong><br>
                                <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; #each items &#125;&#125;...&#123;&#123; /each &#125;&#125;</code>
                            </div>
                            <div class="col-md-4">
                                <strong>Koşullu içerik:</strong><br>
                                <code class="bg-dark text-light px-2 py-1 rounded">&#123;&#123; #if değişken &#125;&#125;...&#123;&#123; /if &#125;&#125;</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
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
                                <div class="editor-container" id="html-editor-container">
                                    <textarea id="html-editor" wire:model="widget.content_html" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-css">
                                <div class="editor-container" id="css-editor-container">
                                    <textarea id="css-editor" wire:model="widget.content_css" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="tabs-js">
                                <div class="editor-container" id="js-editor-container">
                                    <textarea id="js-editor" wire:model="widget.content_js" style="display: none;"></textarea>
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
                                                        wire:model="widget.css_files.{{ $index }}" 
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
                                                        wire:model="widget.js_files.{{ $index }}" 
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
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/monokai.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.css">
<style>
.editor-container {
    height: 600px;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}

.CodeMirror {
    height: 600px !important;
    font-size: 14px;
    line-height: 1.5;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}

.CodeMirror-focused {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.CodeMirror-scroll {
    height: auto;
    min-height: 600px;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
}

.tab-content {
    padding-top: 1rem;
}

.card-header-tabs {
    margin-bottom: -1px;
}

.card-header-tabs .nav-link {
    border-bottom: 1px solid transparent;
}

.card-header-tabs .nav-link.active {
    border-bottom-color: #fff;
}

.container-fluid {
    max-width: none;
    padding: 1rem 2rem;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/foldgutter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/brace-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/fold/xml-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/edit/matchtags.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/addon/selection/active-line.min.js"></script>

<script>
document.addEventListener('livewire:initialized', function() {
    let htmlEditor, cssEditor, jsEditor;
    let editorsInitialized = false;
    
    function initializeEditors() {
        if (editorsInitialized) return;
        
        const htmlTextarea = document.getElementById('html-editor');
        const cssTextarea = document.getElementById('css-editor');
        const jsTextarea = document.getElementById('js-editor');
        
        if (htmlTextarea) {
            htmlEditor = CodeMirror.fromTextArea(htmlTextarea, {
                mode: 'htmlmixed',
                theme: 'monokai',
                lineNumbers: true,
                indentUnit: 2,
                tabSize: 2,
                lineWrapping: true,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                matchTags: true,
                styleActiveLine: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-/": "toggleComment",
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });
            
            htmlEditor.on('change', function() {
                @this.set('widget.content_html', htmlEditor.getValue());
            });
        }
        
        if (cssTextarea) {
            cssEditor = CodeMirror.fromTextArea(cssTextarea, {
                mode: 'css',
                theme: 'monokai',
                lineNumbers: true,
                indentUnit: 2,
                tabSize: 2,
                lineWrapping: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                styleActiveLine: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-/": "toggleComment",
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });
            
            cssEditor.on('change', function() {
                @this.set('widget.content_css', cssEditor.getValue());
            });
        }
        
        if (jsTextarea) {
            jsEditor = CodeMirror.fromTextArea(jsTextarea, {
                mode: 'javascript',
                theme: 'monokai',
                lineNumbers: true,
                indentUnit: 2,
                tabSize: 2,
                lineWrapping: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                styleActiveLine: true,
                foldGutter: true,
                gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
                extraKeys: {
                    "Ctrl-Space": "autocomplete",
                    "Ctrl-/": "toggleComment",
                    "F11": function(cm) {
                        cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    },
                    "Esc": function(cm) {
                        if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    }
                }
            });
            
            jsEditor.on('change', function() {
                @this.set('widget.content_js', jsEditor.getValue());
            });
        }
        
        editorsInitialized = true;
    }
    
    document.addEventListener('shown.bs.tab', function (event) {
        setTimeout(function() {
            if (htmlEditor) htmlEditor.refresh();
            if (cssEditor) cssEditor.refresh();
            if (jsEditor) jsEditor.refresh();
        }, 100);
    });
    
    Livewire.on('refreshEditors', function() {
        setTimeout(function() {
            if (htmlEditor) {
                htmlEditor.setValue(@this.get('widget.content_html') || '');
                htmlEditor.refresh();
            }
            if (cssEditor) {
                cssEditor.setValue(@this.get('widget.content_css') || '');
                cssEditor.refresh();
            }
            if (jsEditor) {
                jsEditor.setValue(@this.get('widget.content_js') || '');
                jsEditor.refresh();
            }
        }, 100);
    });
    
    setTimeout(function() {
        initializeEditors();
    }, 500);
});
</script>
@endpush