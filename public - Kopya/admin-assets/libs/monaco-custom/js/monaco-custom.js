const MonacoCustomEditor = (function() {
    let htmlEditor, cssEditor, jsEditor;
    let editorsInitialized = false;
    let monacoLoaded = false;
    let widgetData = {};
    let currentTheme = 'vs-dark';
    let fullscreenEnabled = false;
    let foldAllEnabled = false;

    const editorSettings = {
        fontSize: 14,
        lineHeight: 22,
        theme: 'vs-dark',
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
        rulers: [],
        cursorBlinking: 'blink',
        cursorSmoothCaretAnimation: true,
        find: {
            seedSearchStringFromSelection: 'always',
            autoFindInSelection: 'never'
        },
        selectionHighlight: false,
        occurrencesHighlight: false,
        renderLineHighlight: 'none',
        contextmenu: false,
        hover: { enabled: false }
    };

    const editorActions = {
        formatCode: function() {
            const activeEditor = this.getActiveEditor();
            if (!activeEditor) return;

            try {
                const activeTab = document.querySelector('.nav-tabs a.nav-link.active');
                const href = activeTab ? activeTab.getAttribute('href') : '';
                
                if (href === '#html-pane') {
                    activeEditor.getAction('editor.action.formatDocument').run();
                }
            } catch (error) {
                console.warn('Format hatası:', error);
            }
        },

        autoFormat: function(editor, language) {
            if (!editor) return;
            
            setTimeout(() => {
                try {
                    if (language === 'html') {
                        editor.getAction('editor.action.formatDocument').run();
                    }
                } catch (error) {
                    console.warn('Otomatik format hatası:', error);
                }
            }, 100);
        },

        openFind: function() {
            const activeEditor = this.getActiveEditor();
            if (activeEditor) {
                activeEditor.getAction('actions.find').run();
            }
        },

        toggleFoldAll: function() {
            const activeEditor = this.getActiveEditor();
            if (!activeEditor) return;

            if (foldAllEnabled) {
                activeEditor.getAction('editor.unfoldAll').run();
                foldAllEnabled = false;
            } else {
                activeEditor.getAction('editor.foldAll').run();
                foldAllEnabled = true;
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
            const activeTab = document.querySelector('.nav-tabs a.nav-link.active');
            if (!activeTab) return null;
            
            const href = activeTab.getAttribute('href');
            if (href === '#html-pane') return htmlEditor;
            if (href === '#css-pane') return cssEditor;
            if (href === '#js-pane') return jsEditor;
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
        
        init: function(initialWidgetData, availableVariables) {
            if (this.initialized) return;
            
            widgetData = initialWidgetData || {};
            
            this.loadMonaco();
            this.setupTabs();
            this.setupFormSubmit();
            this.setupKeyboardShortcuts();
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
                    this.setupEmmet();
                    this.setupContextMenu();
                    this.createEditors();
                });
            }
        },
        
        setupContextMenu: function() {
            if (typeof monaco !== 'undefined') {
                document.addEventListener('contextmenu', function(e) {
                    const target = e.target.closest('.monaco-editor');
                    if (target) {
                        e.preventDefault();
                        
                        const existingMenu = document.querySelector('.custom-context-menu');
                        if (existingMenu) {
                            document.body.removeChild(existingMenu);
                        }
                        
                        const contextMenu = document.createElement('div');
                        contextMenu.className = 'custom-context-menu';
                        contextMenu.style.top = e.clientY + 'px';
                        contextMenu.style.left = e.clientX + 'px';
                        
                        const menuItems = [
                            { text: 'Kes', action: 'cut' },
                            { text: 'Kopyala', action: 'copy' },
                            { text: 'Yapıştır', action: 'paste' },
                            { text: 'Tümünü Seç', action: 'selectAll' }
                        ];
                        
                        menuItems.forEach(item => {
                            const menuItem = document.createElement('div');
                            menuItem.className = 'custom-context-menu-item';
                            menuItem.textContent = item.text;
                            
                            menuItem.addEventListener('click', function() {
                                const activeEditor = editorActions.getActiveEditor();
                                if (activeEditor) {
                                    switch(item.action) {
                                        case 'cut':
                                            activeEditor.getAction('editor.action.clipboardCutAction').run();
                                            break;
                                        case 'copy':
                                            activeEditor.getAction('editor.action.clipboardCopyAction').run();
                                            break;
                                        case 'paste':
                                            activeEditor.getAction('editor.action.clipboardPasteAction').run();
                                            break;
                                        case 'selectAll':
                                            activeEditor.getAction('editor.action.selectAll').run();
                                            break;
                                    }
                                }
                                document.body.removeChild(contextMenu);
                            });
                            
                            contextMenu.appendChild(menuItem);
                        });
                        
                        document.body.appendChild(contextMenu);
                        
                        const closeMenu = function(event) {
                            if (!contextMenu.contains(event.target)) {
                                if (document.body.contains(contextMenu)) {
                                    document.body.removeChild(contextMenu);
                                }
                                document.removeEventListener('click', closeMenu);
                            }
                        };
                        
                        setTimeout(() => {
                            document.addEventListener('click', closeMenu);
                        }, 100);
                    }
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
                                        documentation: 'Emmet kısayolu',
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
                this.autoFormatHtmlEditor();
                editorsInitialized = true;
                
            } catch (error) {
                console.error('Monaco editor oluşturma hatası:', error);
            }
        },

        autoFormatHtmlEditor: function() {
            setTimeout(() => {
                if (htmlEditor && widgetData.content_html) {
                    editorActions.autoFormat(htmlEditor, 'html');
                }
            }, 500);
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
                        if (!line.trim().endsWith(';') && !line.trim().endsWith('{') && !line.includes('{{')) {
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
                    const cleanedCode = jsCode.replace(/\{\{[^}]*\}\}/g, '""');
                    new Function(cleanedCode);
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
                            this.formatBeforeSubmit();
                            const form = document.getElementById('widget-form');
                            if (form) form.dispatchEvent(new Event('submit'));
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
            const tabButtons = document.querySelectorAll('.nav-tabs a[data-bs-toggle="tab"]');
            
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
                    this.formatBeforeSubmit();
                    this.updateBeforeSubmit();
                });
            }
        },

        formatBeforeSubmit: function() {
            if (!editorsInitialized) return;
            
            try {
                if (htmlEditor) editorActions.autoFormat(htmlEditor, 'html');
            } catch (error) {
                console.warn('Format before submit hatası:', error);
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
                    editorActions.autoFormat(htmlEditor, 'html');
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

    window.editorActions = editorActions;

    window.addEventListener('resize', function() {
        editorActions.resizeAllEditors();
    });

    return {
        init: widgetCodeEditor.init.bind(widgetCodeEditor),
        updateEditorValues: widgetCodeEditor.updateEditorValues.bind(widgetCodeEditor)
    };
})();