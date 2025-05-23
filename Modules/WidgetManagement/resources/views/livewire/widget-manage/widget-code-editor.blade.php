@include('widgetmanagement::helper')

<div>
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
                <div class="card" id="editor-card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#html-pane" class="nav-link active" data-bs-toggle="tab">
                                    <i class="fab fa-html5 me-2"></i>
                                    HTML
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#css-pane" class="nav-link" data-bs-toggle="tab">
                                    <i class="fab fa-css3-alt me-2"></i>
                                    CSS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#js-pane" class="nav-link" data-bs-toggle="tab">
                                    <i class="fab fa-js-square me-2"></i>
                                    JavaScript
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#files-pane" class="nav-link" data-bs-toggle="tab">
                                    <i class="fas fa-file-code me-2"></i>
                                    Harici Dosyalar
                                </a>
                            </li>
                            <li class="nav-item ms-auto">
                                <a href="#" class="nav-link" title="Kodu Formatla" onclick="editorActions.formatCode(); return false;">
                                    <i class="fas fa-indent"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Bul/Değiştir" onclick="editorActions.openFind(); return false;">
                                    <i class="fas fa-search"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Tümünü Katla" onclick="editorActions.toggleFoldAll(); return false;">
                                    <i class="fas fa-compress-alt"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Tema Değiştir" onclick="editorActions.toggleTheme(); return false;">
                                    <i class="fas fa-palette"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Tam Ekran" onclick="editorActions.toggleFullscreen(); return false;">
                                    <i class="fas fa-expand" id="fullscreen-icon"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="html-pane">
                                <div class="mb-3" wire:ignore>
                                    <div id="html-editor" style="height: 600px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_html" id="html-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="css-pane">
                                <div class="mb-3" wire:ignore>
                                    <div id="css-editor" style="height: 600px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_css" id="css-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="js-pane">
                                <div class="mb-3" wire:ignore>
                                    <div id="js-editor" style="height: 600px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_js" id="js-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="files-pane">
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
                </div>
            </div>
        </div>
        
        @php
            $variables = $this->getAvailableVariables();
        @endphp
        
        <div class="row my-4">
            @if(isset($variables['settings']) || isset($variables['items']))
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-code me-2"></i>
                            Handlebars Değişkenleri
                        </h4>
                    </div>
                    <div class="card-body">
                        @if(isset($variables['settings']))
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-sliders-h me-1"></i>
                            Özelleştirme Değişkenleri
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm mb-0 variable-table">
                                @foreach($variables['settings'] as $var)
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; {{ $var['name'] }} &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">{{ $var['label'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        @endif
                        
                        @if(isset($variables['items']))
                        <h6 class="text-success mb-2">
                            <i class="fas fa-layer-group me-1"></i>
                            İçerik Değişkenleri
                        </h6>
                        <div class="mb-2">
                            <code class="copyable-code" data-copy="&#123;&#123; #each items &#125;&#125;">&#123;&#123; #each items &#125;&#125;</code>
                        </div>
                        <div class="table-responsive mb-2">
                            <table class="table table-sm mb-0 variable-table">
                                @foreach($variables['items'] as $var)
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; {{ $var['name'] }} &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; {{ $var['name'] }} &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">{{ $var['label'] }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="mb-3">
                            <code class="copyable-code" data-copy="&#123;&#123; /each &#125;&#125;">&#123;&#123; /each &#125;&#125;</code>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-magic me-2"></i>
                            Handlebars Yardımcıları
                        </h4>
                    </div>
                    <div class="card-body">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-question-circle me-1"></i>
                            Koşul Yapıları
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm mb-0 variable-table">
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; #if koşul &#125;&#125;...&#123;&#123; /if &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; #if koşul &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Koşullu görüntüleme</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; #unless koşul &#125;&#125;...&#123;&#123; /unless &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; #unless koşul &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Tersi koşul</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; else &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; else &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Alternatif</td>
                                </tr>
                            </table>
                        </div>
                        
                        <h6 class="text-info mb-2">
                            <i class="fas fa-repeat me-1"></i>
                            Döngü Yapıları
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm mb-0 variable-table">
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; #each liste &#125;&#125;...&#123;&#123; /each &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; #each liste &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Liste döngüsü</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; @index &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; @index &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Döngü indeksi</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; @first &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; @first &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">İlk eleman</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; @last &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; @last &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Son eleman</td>
                                </tr>
                            </table>
                        </div>
                        
                        <h6 class="text-secondary mb-2">
                            <i class="fas fa-object-group me-1"></i>
                            Diğer Yardımcılar
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 variable-table">
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123; #with nesne &#125;&#125;...&#123;&#123; /with &#125;&#125;">
                                        <code class="copyable-code">&#123;&#123; #with nesne &#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Nesne bağlamı</td>
                                </tr>
                                <tr>
                                    <td class="variable-code" data-copy="&#123;&#123;!-- yorum --&#125;&#125;">
                                        <code class="copyable-code">&#123;&#123;!-- yorum --&#125;&#125;</code>
                                    </td>
                                    <td class="text-muted" style="width: 40%">Yorum satırı</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-footer">
                <div class="d-flex justify-content-end align-items-center">
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
    </form>

</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/libs/monaco-custom/css/monaco-custom.css') }}">
<style>
.copyable-code {
    cursor: pointer;
    transition: all 0.2s ease;
    background-color: #2d2d30 !important;
    color: #cccccc !important;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    position: relative;
}

.copyable-code:hover {
    background-color: #094771 !important;
    color: #ffffff !important;
}

.variable-table tr {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.variable-table tr:hover {
    background-color: rgba(9, 71, 113, 0.1);
}

.copy-feedback {
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    z-index: 1000;
    pointer-events: none;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin/libs/monaco-custom/js/monaco-custom.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        if (typeof MonacoCustomEditor !== 'undefined') {
            MonacoCustomEditor.init(@json($widget), @json($this->getAvailableVariables()));
        }
    }, 200);

    // Kopyalama işlevi
    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            return new Promise((resolve, reject) => {
                if (document.execCommand('copy')) {
                    textArea.remove();
                    resolve();
                } else {
                    textArea.remove();
                    reject();
                }
            });
        }
    }

    function showCopyFeedback(element) {
        const feedback = document.createElement('div');
        feedback.className = 'copy-feedback';
        feedback.textContent = 'Kopyalandı!';
        element.style.position = 'relative';
        element.appendChild(feedback);
        
        setTimeout(() => {
            if (feedback.parentNode) {
                feedback.parentNode.removeChild(feedback);
            }
        }, 1500);
    }

    // Tıklanabilir kod elemanları
    document.addEventListener('click', function(e) {
        const copyableCode = e.target.closest('.copyable-code');
        const variableRow = e.target.closest('.variable-code');
        
        if (copyableCode) {
            e.preventDefault();
            const copyText = copyableCode.getAttribute('data-copy') || copyableCode.textContent;
            const cleanText = copyText.replace(/&#123;/g, '{').replace(/&#125;/g, '}');
            
            copyToClipboard(cleanText).then(() => {
                showCopyFeedback(copyableCode);
            }).catch(() => {
                console.error('Kopyalama başarısız');
            });
        } else if (variableRow) {
            e.preventDefault();
            const copyText = variableRow.getAttribute('data-copy');
            const cleanText = copyText.replace(/&#123;/g, '{').replace(/&#125;/g, '}');
            
            copyToClipboard(cleanText).then(() => {
                showCopyFeedback(variableRow);
            }).catch(() => {
                console.error('Kopyalama başarısız');
            });
        }
    });
});

window.updateWidgetEditors = function(newData) {
    if (typeof MonacoCustomEditor !== 'undefined') {
        MonacoCustomEditor.updateEditorValues(newData);
    }
};

document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        if (typeof MonacoCustomEditor !== 'undefined') {
            MonacoCustomEditor.init(@json($widget), @json($this->getAvailableVariables()));
        }
    }, 300);
});
</script>
@endpush