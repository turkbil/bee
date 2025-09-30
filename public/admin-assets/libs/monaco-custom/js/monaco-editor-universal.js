/**
 * MONACO EDITOR UNIVERSAL JAVASCRIPT
 * Tüm manage sayfaları için ortak Monaco Editor sistemi
 * Pattern: A1 CMS Universal System
 *
 * Kullanım:
 * 1. data-monaco-editor="css" veya data-monaco-editor="js" attribute'u ile işaretle
 * 2. data-monaco-target="css-field-name" ile Livewire model'i belirt
 * 3. Otomatik başlatılır ve senkronize edilir
 */

(function() {
    'use strict';

    // 🔒 SINGLETON PATTERN - Sadece bir kere yükle
    if (window.MonacoEditorUniversal) {
        console.log('ℹ️ Monaco Editor Universal zaten yüklü - atlanıyor');
        return;
    }

    class MonacoEditorUniversal {
        constructor() {
            this.editors = new Map();
            this.loaderInitialized = false;
            this.monacoReady = false;
            this.initQueue = [];

            console.log('🎯 Monaco Editor Universal System başlatılıyor...');
            this.loadMonacoLoader();
        }

        /**
         * Monaco Loader'ı yükle (AMD conflict prevention)
         */
        loadMonacoLoader() {
            if (this.loaderInitialized) {
                console.log('ℹ️ Monaco loader zaten yüklü');
                return;
            }

            // AMD sistemini geçici olarak devre dışı bırak
            const originalDefine = window.define;
            const originalRequire = window.require;

            const monacoScript = document.createElement('script');
            monacoScript.src = 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js';
            monacoScript.onload = () => {
                this.loaderInitialized = true;
                console.log('✅ Monaco loader yüklendi');
                this.configureMonaco();
            };
            monacoScript.onerror = () => {
                console.error('❌ Monaco loader yüklenemedi');
            };
            document.head.appendChild(monacoScript);
        }

        /**
         * Monaco'yu yapılandır ve editörleri başlat
         */
        configureMonaco() {
            if (typeof require === 'undefined') {
                console.warn('⚠️ Monaco require henüz hazır değil, 500ms sonra tekrar denenecek');
                setTimeout(() => this.configureMonaco(), 500);
                return;
            }

            require.config({
                paths: {
                    'vs': 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs'
                }
            });

            require(['vs/editor/editor.main'], () => {
                this.monacoReady = true;
                console.log('✅ Monaco Editor hazır');
                this.processInitQueue();
            });
        }

        /**
         * Bekleyen editörleri başlat
         */
        processInitQueue() {
            while (this.initQueue.length > 0) {
                const { element, options } = this.initQueue.shift();
                this.createEditor(element, options);
            }
        }

        /**
         * Monaco Editor oluştur
         */
        createEditor(element, options = {}) {
            if (!this.monacoReady) {
                console.log('⏳ Monaco henüz hazır değil, kuyruga ekleniyor...');
                this.initQueue.push({ element, options });
                return null;
            }

            const editorType = element.dataset.monacoEditor || 'css';
            const targetField = element.dataset.monacoTarget || editorType;
            const editorId = `monaco-${targetField}-${Date.now()}`;

            // Livewire sync için textarea
            const textarea = document.querySelector(`[wire\\:model*="${targetField}"]`) ||
                           document.getElementById(`${targetField}-textarea`);

            if (!textarea) {
                console.warn(`⚠️ Livewire textarea bulunamadı: ${targetField}`);
            }

            const language = editorType === 'css' ? 'css' : 'javascript';
            const initialValue = textarea ? textarea.value : '';

            const editor = monaco.editor.create(element, {
                value: initialValue,
                language: language,
                theme: 'vs-dark',
                automaticLayout: true,
                minimap: { enabled: false },
                scrollBeyondLastLine: false,
                fontSize: 14,
                formatOnPaste: true,
                formatOnType: true,
                folding: true,
                ...options
            });

            // Textarea ile senkronize et
            if (textarea) {
                editor.onDidChangeModelContent(() => {
                    textarea.value = editor.getValue();
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                });
            }

            // Editor'ı kaydet
            this.editors.set(editorId, {
                editor,
                element,
                textarea,
                type: editorType,
                theme: 'vs-dark',
                fullscreen: false
            });

            console.log(`✅ Monaco Editor oluşturuldu: ${editorId} (${language})`);
            return { id: editorId, editor };
        }

        /**
         * Toolbar butonlarını ekle
         */
        attachToolbar(editorId, toolbarElement) {
            const editorData = this.editors.get(editorId);
            if (!editorData) return;

            const { editor, type } = editorData;

            // Format button
            const formatBtn = toolbarElement.querySelector('[data-action="format"]');
            if (formatBtn) {
                formatBtn.onclick = () => {
                    editor.getAction('editor.action.formatDocument').run();
                };
            }

            // Find button
            const findBtn = toolbarElement.querySelector('[data-action="find"]');
            if (findBtn) {
                findBtn.onclick = () => {
                    editor.getAction('actions.find').run();
                };
            }

            // Fold button
            const foldBtn = toolbarElement.querySelector('[data-action="fold"]');
            if (foldBtn) {
                let folded = false;
                foldBtn.onclick = () => {
                    if (folded) {
                        editor.getAction('editor.unfoldAll').run();
                    } else {
                        editor.getAction('editor.foldAll').run();
                    }
                    folded = !folded;
                };
            }

            // Theme toggle button
            const themeBtn = toolbarElement.querySelector('[data-action="theme"]');
            if (themeBtn) {
                themeBtn.onclick = () => {
                    editorData.theme = editorData.theme === 'vs-dark' ? 'vs' : 'vs-dark';
                    monaco.editor.setTheme(editorData.theme);
                };
            }

            // Fullscreen button
            const fullscreenBtn = toolbarElement.querySelector('[data-action="fullscreen"]');
            if (fullscreenBtn) {
                fullscreenBtn.onclick = () => {
                    this.toggleFullscreen(editorId);
                };
            }
        }

        /**
         * Tam ekran modu
         */
        toggleFullscreen(editorId) {
            const editorData = this.editors.get(editorId);
            if (!editorData) return;

            const { element, editor } = editorData;
            const container = element.closest('.monaco-editor-container');

            if (!editorData.fullscreen) {
                // Fullscreen aç
                container.classList.add('monaco-fullscreen');
                const bgColor = editorData.theme === 'vs-dark' ? '#1e1e1e' : '#ffffff';
                container.style.backgroundColor = bgColor;
                element.style.height = 'calc(100vh - 100px)';
                editorData.fullscreen = true;
            } else {
                // Fullscreen kapat
                container.classList.remove('monaco-fullscreen');
                container.style.backgroundColor = '';
                element.style.height = '350px';
                editorData.fullscreen = false;
            }

            editor.layout();
        }

        /**
         * Editor'ı al
         */
        getEditor(editorId) {
            const editorData = this.editors.get(editorId);
            return editorData ? editorData.editor : null;
        }

        /**
         * Tüm editörleri otomatik başlat
         */
        autoInitialize() {
            const elements = document.querySelectorAll('[data-monaco-editor]');
            elements.forEach(element => {
                if (element.dataset.monacoInitialized) return;
                element.dataset.monacoInitialized = 'true';

                const result = this.createEditor(element);
                if (result) {
                    // Toolbar varsa bağla
                    const container = element.closest('.monaco-editor-container');
                    const toolbar = container?.querySelector('.monaco-toolbar');
                    if (toolbar) {
                        this.attachToolbar(result.id, toolbar);
                    }
                }
            });
        }

        /**
         * Livewire morph sonrası editörleri kontrol et
         */
        checkAfterMorph() {
            this.editors.forEach((editorData, editorId) => {
                const { element, editor, textarea } = editorData;

                // Element hala DOM'da mı?
                if (!document.body.contains(element)) {
                    console.log(`🗑️ Editor DOM'dan kaldırıldı: ${editorId}`);
                    editor.dispose();
                    this.editors.delete(editorId);
                    return;
                }

                // Textarea değeri değişti mi?
                if (textarea && textarea.value !== editor.getValue()) {
                    console.log(`🔄 Textarea değişti, Monaco güncelleniyor: ${editorId}`);
                    editor.setValue(textarea.value);
                }
            });
        }
    }

    // Global instance
    window.MonacoEditorUniversal = new MonacoEditorUniversal();

    // DOM ready olduğunda otomatik başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.MonacoEditorUniversal.autoInitialize();
        });
    } else {
        window.MonacoEditorUniversal.autoInitialize();
    }

    // Livewire hook'ları
    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.updated', () => {
            window.MonacoEditorUniversal.checkAfterMorph();
            window.MonacoEditorUniversal.autoInitialize();
        });
    });

    console.log('✅ Monaco Editor Universal System hazır!');

})();