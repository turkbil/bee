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
                                <a href="#" class="nav-link" title="Kod Önerileri" onclick="editorActions.toggleSuggestions(); return false;">
                                    <i class="fas fa-lightbulb"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link" title="Kodu Formatla" onclick="editorActions.formatCode(); return false;">
                                    <i class="fas fa-magic"></i>
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