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
                        <div class="d-flex justify-content-between align-items-center">
                            <ul class="nav nav-tabs card-header-tabs" id="code-editor-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="html-tab" data-bs-toggle="tab" data-bs-target="#html-pane" type="button" role="tab">
                                        <i class="fab fa-html5 me-2"></i>
                                        HTML
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="css-tab" data-bs-toggle="tab" data-bs-target="#css-pane" type="button" role="tab">
                                        <i class="fab fa-css3-alt me-2"></i>
                                        CSS
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="js-tab" data-bs-toggle="tab" data-bs-target="#js-pane" type="button" role="tab">
                                        <i class="fab fa-js-square me-2"></i>
                                        JavaScript
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-pane" type="button" role="tab">
                                        <i class="fas fa-file-code me-2"></i>
                                        Harici Dosyalar
                                    </button>
                                </li>
                            </ul>
                            <div class="ms-auto">
                                <nav class="nav nav-segmented nav-sm" role="tablist">
                                    <button type="button" class="nav-link" onclick="editorActions.formatCode()" title="Kodu Formatla" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                    <button type="button" class="nav-link" onclick="editorActions.toggleTheme()" title="Tema Değiştir" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-palette"></i>
                                    </button>
                                    <button type="button" class="nav-link" onclick="editorActions.decreaseFontSize()" title="Yazı Tipi Küçült" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-search-minus"></i>
                                    </button>
                                    <button type="button" class="nav-link" onclick="editorActions.increaseFontSize()" title="Yazı Tipi Büyüt" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-search-plus"></i>
                                    </button>
                                    <button type="button" class="nav-link" onclick="editorActions.toggleFullscreen()" title="Tam Ekran" data-bs-toggle="tooltip" data-bs-placement="top">
                                        <i class="fas fa-expand" id="fullscreen-icon"></i>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="code-editor-content">
                            <div class="tab-pane fade show active" id="html-pane" role="tabpanel">
                                <div class="mb-3" wire:ignore>
                                    <div id="html-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_html" id="html-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="css-pane" role="tabpanel">
                                <div class="mb-3" wire:ignore>
                                    <div id="css-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_css" id="css-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="js-pane" role="tabpanel">
                                <div class="mb-3" wire:ignore>
                                    <div id="js-editor" style="height: 400px; width: 100%;"></div>
                                    <textarea wire:model.defer="widget.content_js" id="js-textarea" style="display: none;"></textarea>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="files-pane" role="tabpanel">
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
                    <div class="card-footer" id="editor-footer">
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
            </div>
        </div>
    </form>

    @php
        $variables = $this->getAvailableVariables();
    @endphp
    
    @if(!empty($variables))
    <div class="row my-4" id="variables-panel">
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

.fullscreen-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: #1e1e1e;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.fullscreen-overlay .card {
    flex: 1;
    margin: 0;
    border: none;
    background: #1e1e1e;
    height: 100vh;
}

.fullscreen-overlay .card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
}

.fullscreen-overlay .tab-content {
    flex: 1;
    height: 100%;
}

.fullscreen-overlay .tab-pane {
    height: 100%;
}

.fullscreen-overlay .tab-pane > div {
    height: 100%;
}

.fullscreen-overlay #html-editor,
.fullscreen-overlay #css-editor,
.fullscreen-overlay #js-editor {
    height: 100% !important;
}

@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/emmet/2.4.7/emmet.min.js"></script>
<script>
let htmlEditor, cssEditor, jsEditor;
let editorsInitialized = false;
let monacoLoaded = false;
let widgetData = @json($widget);
let currentTheme = 'vs-dark';
let currentFontSize = 14;
let fullscreenEnabled = false;

const editorSettings = {
    fontSize: currentFontSize,
    lineHeight: currentFontSize + 8,
    theme: currentTheme,
    minimap: { enabled: false },
    automaticLayout: true,
    scrollBeyondLastLine: false,
    formatOnPaste: true,
    formatOnType: true,
    wordWrap: 'on',
    folding: true,
    foldingStrategy: 'indentation',
    showFoldingControls: 'always',
    suggest: {
        insertMode: 'replace',
        filterGraceful: true
    },
    quickSuggestions: {
        other: true,
        comments: true,
        strings: true
    },
    acceptSuggestionOnCommitCharacter: true,
    acceptSuggestionOnEnter: 'on',
    accessibilitySupport: 'auto',
    autoIndent: 'advanced',
    renderWhitespace: 'selection',
    renderControlCharacters: true,
    renderFinalNewline: true,
    rulers: [80, 120],
    cursorBlinking: 'blink',
    cursorSmoothCaretAnimation: true,
    find: {
        seedSearchStringFromSelection: 'always',
        autoFindInSelection: 'never'
    }
};

const editorActions = {
    formatCode: function() {
        const activeEditor = this.getActiveEditor();
        if (activeEditor) {
            activeEditor.getAction('editor.action.formatDocument').run();
        }
    },
    
    toggleTheme: function() {
        const themes = ['vs-dark', 'vs', 'hc-black'];
        const currentIndex = themes.indexOf(currentTheme);
        currentTheme = themes[(currentIndex + 1) % themes.length];
        
        if (monacoLoaded) {
            monaco.editor.setTheme(currentTheme);
        }
    },
    
    increaseFontSize: function() {
        currentFontSize = Math.min(currentFontSize + 2, 24);
        const newLineHeight = currentFontSize + 12;
        this.updateAllEditors({ 
            fontSize: currentFontSize,
            lineHeight: newLineHeight
        });
    },
    
    decreaseFontSize: function() {
        currentFontSize = Math.max(currentFontSize - 2, 10);
        const newLineHeight = currentFontSize + 8;
        this.updateAllEditors({ 
            fontSize: currentFontSize,
            lineHeight: newLineHeight
        });
    },
    
    toggleFullscreen: function() {
        const editorCard = document.getElementById('editor-card');
        const body = document.body;
        const fullscreenIcon = document.getElementById('fullscreen-icon');
        
        if (!fullscreenEnabled) {
            editorCard.classList.add('fullscreen-overlay');
            body.style.overflow = 'hidden';
            fullscreenEnabled = true;
            fullscreenIcon.className = 'fas fa-compress';
        } else {
            editorCard.classList.remove('fullscreen-overlay');
            body.style.overflow = 'auto';
            fullscreenEnabled = false;
            fullscreenIcon.className = 'fas fa-expand';
        }
        
        setTimeout(() => {
            this.resizeAllEditors();
        }, 100);
    },
    
    getActiveEditor: function() {
        const activeTab = document.querySelector('#code-editor-tabs button.nav-link.active');
        if (!activeTab) return null;
        
        const tabId = activeTab.id;
        if (tabId === 'html-tab') return htmlEditor;
        if (tabId === 'css-tab') return cssEditor;
        if (tabId === 'js-tab') return jsEditor;
        return null;
    },
    
    updateAllEditors: function(options) {
        if (!editorsInitialized) return;
        
        [htmlEditor, cssEditor, jsEditor].forEach(editor => {
            if (editor) {
                editor.updateOptions(options);
            }
        });
    },
    
    resizeAllEditors: function() {
        if (!editorsInitialized) return;
        
        setTimeout(() => {
            [htmlEditor, cssEditor, jsEditor].forEach(editor => {
                if (editor) {
                    editor.layout();
                }
            });
        }, 50);
    }
};

const widgetCodeEditor = {
    initialized: false,
    
    init: function() {
        if (this.initialized) return;
        
        this.loadMonaco();
        this.setupTabs();
        this.setupFormSubmit();
        this.setupKeyboardShortcuts();
        this.setupTooltips();
        this.initialized = true;
    },
    
    setupTooltips: function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
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
                this.setupEmmet();
                this.createEditors();
            });
        }
    },
    
    setupEmmet: function() {
        if (typeof emmet !== 'undefined' && typeof monaco !== 'undefined') {
            monaco.languages.registerCompletionItemProvider('html', {
                provideCompletionItems: function(model, position) {
                    const textUntilPosition = model.getValueInRange({
                        startLineNumber: 1,
                        startColumn: 1,
                        endLineNumber: position.lineNumber,
                        endColumn: position.column
                    });
                    
                    const match = textUntilPosition.match(/[\w:.#\[\]@-]*$/);
                    if (!match) return { suggestions: [] };
                    
                    const word = match[0];
                    if (word.length < 2) return { suggestions: [] };
                    
                    try {
                        const expandedHtml = emmet.expand(word);
                        if (expandedHtml && expandedHtml !== word) {
                            return {
                                suggestions: [{
                                    label: word,
                                    kind: monaco.languages.CompletionItemKind.Snippet,
                                    insertText: expandedHtml,
                                    documentation: 'Emmet abbreviation',
                                    range: {
                                        startLineNumber: position.lineNumber,
                                        startColumn: position.column - word.length,
                                        endLineNumber: position.lineNumber,
                                        endColumn: position.column
                                    }
                                }]
                            };
                        }
                    } catch (e) {
                        return { suggestions: [] };
                    }
                    
                    return { suggestions: [] };
                }
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
                ...editorSettings,
                value: widgetData.content_html || '',
                language: 'html'
            });

            cssEditor = monaco.editor.create(cssEl, {
                ...editorSettings,
                value: widgetData.content_css || '',
                language: 'css'
            });

            jsEditor = monaco.editor.create(jsEl, {
                ...editorSettings,
                value: widgetData.content_js || '',
                language: 'javascript'
            });

            this.setupEditorEvents();
            this.setupErrorMarkers();
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
    
    setupErrorMarkers: function() {
        if (!editorsInitialized) return;
        
        const validateCSS = (cssCode) => {
            const errors = [];
            const lines = cssCode.split('\n');
            
            lines.forEach((line, index) => {
                if (line.trim() && !line.includes('{') && !line.includes('}') && line.includes(':')) {
                    if (!line.trim().endsWith(';') && !line.trim().endsWith('{')) {
                        errors.push({
                            startLineNumber: index + 1,
                            startColumn: 1,
                            endLineNumber: index + 1,
                            endColumn: line.length + 1,
                            message: 'Noktalı virgül eksik olabilir',
                            severity: monaco.MarkerSeverity.Warning
                        });
                    }
                }
            });
            
            return errors;
        };
        
        const validateJS = (jsCode) => {
            const errors = [];
            try {
                new Function(jsCode);
            } catch (e) {
                const match = e.message.match(/line (\d+)/);
                const lineNumber = match ? parseInt(match[1]) : 1;
                
                errors.push({
                    startLineNumber: lineNumber,
                    startColumn: 1,
                    endLineNumber: lineNumber,
                    endColumn: 100,
                    message: e.message,
                    severity: monaco.MarkerSeverity.Error
                });
            }
            
            return errors;
        };
        
        cssEditor.onDidChangeModelContent(() => {
            const cssCode = cssEditor.getValue();
            const errors = validateCSS(cssCode);
            monaco.editor.setModelMarkers(cssEditor.getModel(), 'css-validator', errors);
        });
        
        jsEditor.onDidChangeModelContent(() => {
            const jsCode = jsEditor.getValue();
            const errors = validateJS(jsCode);
            monaco.editor.setModelMarkers(jsEditor.getModel(), 'js-validator', errors);
        });
    },
    
    setupKeyboardShortcuts: function() {
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 's':
                        e.preventDefault();
                        document.getElementById('widget-form').dispatchEvent(new Event('submit'));
                        break;
                    case 'f':
                        e.preventDefault();
                        if (e.shiftKey) {
                            editorActions.formatCode();
                        }
                        break;
                    case 'Enter':
                        if (e.shiftKey) {
                            e.preventDefault();
                            editorActions.toggleFullscreen();
                        }
                        break;
                }
            }
            
            if (e.key === 'F11') {
                e.preventDefault();
                editorActions.toggleFullscreen();
            }
        });
    },
    
    setupTabs: function() {
        const tabButtons = document.querySelectorAll('#code-editor-tabs button[data-bs-toggle="tab"]');
        
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', () => {
                setTimeout(() => {
                    editorActions.resizeAllEditors();
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

window.updateWidgetEditors = function(newData) {
    widgetCodeEditor.updateEditorValues(newData);
};

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        widgetCodeEditor.init();
    }, 200);
});

window.addEventListener('resize', function() {
    editorActions.resizeAllEditors();
});

document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        widgetCodeEditor.init();
    }, 300);
});
</script>
@endpush