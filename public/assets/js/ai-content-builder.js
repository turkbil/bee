/**
 * AI Content Builder JavaScript
 * Tema analizi ve içerik üretimi için client-side yardımcı fonksiyonlar
 */

(function() {
    'use strict';

    // AI Content Builder namespace
    window.AIContentBuilder = {

        /**
         * Sayfanın mevcut tema renklerini analiz et
         */
        analyzePageTheme: function() {
            const theme = {
                colors: {},
                fonts: {},
                spacing: {}
            };

            // Root element stillerini al
            const rootStyles = getComputedStyle(document.documentElement);

            // CSS değişkenlerini tara
            const cssVars = Array.from(document.styleSheets)
                .flatMap(sheet => {
                    try {
                        return Array.from(sheet.cssRules || []);
                    } catch (e) {
                        return [];
                    }
                })
                .filter(rule => rule.type === CSSRule.STYLE_RULE)
                .flatMap(rule => Array.from(rule.style))
                .filter(prop => prop.startsWith('--'));

            // Renkleri çıkar
            cssVars.forEach(varName => {
                const value = rootStyles.getPropertyValue(varName);
                if (value && this.isColor(value)) {
                    theme.colors[varName] = value.trim();
                }
            });

            // Primary element'lerden renkleri al
            const primaryElements = [
                '.btn-primary',
                '.bg-primary',
                '.text-primary',
                '[class*="primary"]'
            ];

            primaryElements.forEach(selector => {
                try {
                    const el = document.querySelector(selector);
                    if (el) {
                        const styles = getComputedStyle(el);
                        if (!theme.colors.primary) {
                            theme.colors.primary = styles.backgroundColor || styles.color;
                        }
                    }
                } catch (e) {}
            });

            // Font family'leri tespit et
            const bodyFont = getComputedStyle(document.body).fontFamily;
            theme.fonts.body = bodyFont;

            const headings = document.querySelector('h1, h2, h3');
            if (headings) {
                theme.fonts.headings = getComputedStyle(headings).fontFamily;
            }

            // Container genişliğini bul
            const container = document.querySelector('.container, .max-w-7xl, [class*="container"]');
            if (container) {
                theme.spacing.containerWidth = getComputedStyle(container).maxWidth;
            }

            return theme;
        },

        /**
         * Renk değeri kontrolü
         */
        isColor: function(value) {
            return /^#[0-9A-F]{3,8}$/i.test(value) ||
                   /^rgb/.test(value) ||
                   /^hsl/.test(value) ||
                   ['red', 'blue', 'green', 'yellow', 'black', 'white', 'gray'].some(color => value.includes(color));
        },

        /**
         * İçeriği editöre ekle
         */
        insertToEditor: function(editorId, content) {
            // TinyMCE
            if (typeof tinymce !== 'undefined') {
                const editor = tinymce.get(editorId);
                if (editor) {
                    editor.insertContent(content);
                    return true;
                }
            }

            // CKEditor
            if (typeof CKEDITOR !== 'undefined') {
                const editor = CKEDITOR.instances[editorId];
                if (editor) {
                    editor.insertHtml(content);
                    return true;
                }
            }

            // Summernote
            if (typeof $ !== 'undefined' && $.fn.summernote) {
                const $editor = $('#' + editorId);
                if ($editor.length) {
                    const currentContent = $editor.summernote('code');
                    $editor.summernote('code', currentContent + content);
                    return true;
                }
            }

            // Fallback: textarea
            const textarea = document.getElementById(editorId);
            if (textarea) {
                const cursorPos = textarea.selectionStart;
                const textBefore = textarea.value.substring(0, cursorPos);
                const textAfter = textarea.value.substring(cursorPos);
                textarea.value = textBefore + content + textAfter;
                textarea.focus();
                textarea.setSelectionRange(cursorPos + content.length, cursorPos + content.length);
                return true;
            }

            return false;
        },

        /**
         * Content Builder panelini aç
         */
        openPanel: function(params = {}) {
            // Tema analizini yap
            const themeData = this.analyzePageTheme();

            // Livewire event'i tetikle
            if (window.Livewire) {
                window.Livewire.dispatch('openContentBuilder', {
                    ...params,
                    themeData: themeData
                });
            }
        },

        /**
         * İçerik önizleme
         */
        previewContent: function(content) {
            const modal = document.createElement('div');
            modal.className = 'ai-content-preview-modal';
            modal.innerHTML = `
                <div class="modal-backdrop"></div>
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>İçerik Önizleme</h3>
                        <button class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="preview-container">
                            ${content}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary insert-btn">Editöre Ekle</button>
                        <button class="btn btn-secondary close-btn">Kapat</button>
                    </div>
                </div>
            `;

            // Stil ekle
            if (!document.getElementById('ai-content-preview-styles')) {
                const styles = document.createElement('style');
                styles.id = 'ai-content-preview-styles';
                styles.textContent = `
                    .ai-content-preview-modal {
                        position: fixed;
                        inset: 0;
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .ai-content-preview-modal .modal-backdrop {
                        position: absolute;
                        inset: 0;
                        background: rgba(0, 0, 0, 0.5);
                    }
                    .ai-content-preview-modal .modal-content {
                        position: relative;
                        background: white;
                        border-radius: 8px;
                        width: 90%;
                        max-width: 800px;
                        max-height: 80vh;
                        display: flex;
                        flex-direction: column;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                    }
                    .ai-content-preview-modal .modal-header {
                        padding: 1rem 1.5rem;
                        border-bottom: 1px solid #e5e7eb;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                    }
                    .ai-content-preview-modal .modal-body {
                        flex: 1;
                        overflow-y: auto;
                        padding: 1.5rem;
                    }
                    .ai-content-preview-modal .modal-footer {
                        padding: 1rem 1.5rem;
                        border-top: 1px solid #e5e7eb;
                        display: flex;
                        gap: 0.5rem;
                        justify-content: flex-end;
                    }
                    .ai-content-preview-modal .close-btn {
                        background: none;
                        border: none;
                        font-size: 1.5rem;
                        cursor: pointer;
                        color: #6b7280;
                    }
                    .ai-content-preview-modal .btn {
                        padding: 0.5rem 1rem;
                        border-radius: 0.375rem;
                        border: none;
                        cursor: pointer;
                        font-size: 0.875rem;
                        font-weight: 500;
                    }
                    .ai-content-preview-modal .btn-primary {
                        background: #3b82f6;
                        color: white;
                    }
                    .ai-content-preview-modal .btn-secondary {
                        background: #e5e7eb;
                        color: #374151;
                    }
                `;
                document.head.appendChild(styles);
            }

            document.body.appendChild(modal);

            // Event listeners
            modal.querySelectorAll('.close-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.remove();
                });
            });

            modal.querySelector('.modal-backdrop').addEventListener('click', () => {
                modal.remove();
            });

            return modal;
        },

        /**
         * Tema renklerini hex formatına çevir
         */
        rgbToHex: function(rgb) {
            if (!rgb || !rgb.startsWith('rgb')) return rgb;

            const values = rgb.match(/\d+/g);
            if (!values || values.length < 3) return rgb;

            const r = parseInt(values[0]);
            const g = parseInt(values[1]);
            const b = parseInt(values[2]);

            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        },

        /**
         * Framework tespiti
         */
        detectFramework: function() {
            // Tailwind kontrolü
            if (document.documentElement.className.includes('tailwind') ||
                document.querySelector('[class*="text-"], [class*="bg-"], [class*="p-"], [class*="m-"]')) {
                return 'tailwind';
            }

            // Bootstrap kontrolü
            if (document.querySelector('[class*="col-"], [class*="btn-primary"], .container-fluid')) {
                return 'bootstrap';
            }

            return 'custom';
        },

        /**
         * Init
         */
        init: function() {
            // Global event listeners
            document.addEventListener('DOMContentLoaded', () => {
                // AI Content Builder butonlarını dinle
                document.querySelectorAll('[data-ai-content-builder]').forEach(button => {
                    button.addEventListener('click', (e) => {
                        e.preventDefault();
                        const params = button.dataset.aiContentBuilder ?
                            JSON.parse(button.dataset.aiContentBuilder) : {};
                        this.openPanel(params);
                    });
                });
            });

            // Livewire v3 event listeners
            if (window.Livewire) {
                // Livewire v3 syntax
                document.addEventListener('livewire:init', () => {
                    window.Livewire.on('aiContentGenerated', (data) => {
                        if (data && data.content && data.targetField) {
                            this.insertToEditor(data.targetField, data.content);
                        }
                    });

                    window.Livewire.on('showContentPreview', (data) => {
                        if (data && data.content) {
                            this.previewContent(data.content);
                        }
                    });
                });
            }
        }
    };

    // Initialize
    AIContentBuilder.init();

})();