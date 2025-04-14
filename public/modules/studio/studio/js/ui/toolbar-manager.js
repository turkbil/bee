/**
 * Studio Toolbar Manager
 * Araç çubuğu işlevselliğini yöneten modül
 */
const StudioToolbarManager = (function() {
    let editor = null;
    let config = {};
    
    /**
     * Araç çubuğunu ayarla
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Araç çubuğu seçenekleri
     */
    function setupToolbar(editorInstance, options = {}) {
        editor = editorInstance;
        
        // Varsayılan yapılandırma
        config = {
            buttonSelectors: {
                save: '#save-btn',
                preview: '#preview-btn',
                export: '#export-btn',
                undo: '#cmd-undo',
                redo: '#cmd-redo',
                clear: '#cmd-clear',
                visibility: '#sw-visibility',
                desktop: '#device-desktop',
                tablet: '#device-tablet',
                mobile: '#device-mobile',
                codeEdit: '#cmd-code-edit',
                cssEdit: '#cmd-css-edit',
                jsEdit: '#cmd-js-edit'
            },
            ...options
        };
        
        // Butonları başlat
        initButtons();
        
        console.log('Araç çubuğu yöneticisi başlatıldı');
    }
    
    /**
     * Butonları yapılandır
     */
    function initButtons() {
        // Kaydet butonu
        const saveBtn = document.querySelector(config.buttonSelectors.save);
        if (saveBtn) {
            // Kaydet butonu işlevi StudioSaveAction tarafından yönetilir
            console.log('Kaydet butonu bulundu');
        }
        
        // Önizleme butonu
        const previewBtn = document.querySelector(config.buttonSelectors.preview);
        if (previewBtn) {
            // Önizleme butonu işlevi StudioPreviewAction tarafından yönetilir
            console.log('Önizleme butonu bulundu');
        }
        
        // Dışa aktar butonu
        const exportBtn = document.querySelector(config.buttonSelectors.export);
        if (exportBtn) {
            // Dışa aktar butonu işlevi StudioExportAction tarafından yönetilir
            console.log('Dışa aktar butonu bulundu');
        }
        
        // Geri al butonu
        const undoBtn = document.querySelector(config.buttonSelectors.undo);
        if (undoBtn) {
            undoBtn.addEventListener('click', function() {
                editor.Commands.run('core:undo');
            });
        }
        
        // Yinele butonu
        const redoBtn = document.querySelector(config.buttonSelectors.redo);
        if (redoBtn) {
            redoBtn.addEventListener('click', function() {
                editor.Commands.run('core:redo');
            });
        }
        
        // Temizle butonu
        const clearBtn = document.querySelector(config.buttonSelectors.clear);
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (confirm('İçeriği temizlemek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
                    editor.Commands.run('core:canvas-clear');
                }
            });
        }
        
        // Görünürlük butonu
        const visibilityBtn = document.querySelector(config.buttonSelectors.visibility);
        if (visibilityBtn) {
            visibilityBtn.addEventListener('click', function() {
                const result = editor.Commands.run('sw-visibility');
                visibilityBtn.classList.toggle('active', result);
            });
        }
        
        // Cihaz butonları
        setupDeviceButtons();
        
        // Kod düzenleme butonları
        setupCodeEditButtons();
        
        console.log('Araç çubuğu butonları başlatıldı');
    }
    
    /**
     * Cihaz butonlarını ayarla
     */
    function setupDeviceButtons() {
        const deviceManager = editor.DeviceManager;
        const deviceButtons = {
            desktop: document.querySelector(config.buttonSelectors.desktop),
            tablet: document.querySelector(config.buttonSelectors.tablet),
            mobile: document.querySelector(config.buttonSelectors.mobile)
        };
        
        // Tüm butonları aktifliğini kaldır
        function clearActiveDeviceButtons() {
            Object.values(deviceButtons).forEach(btn => {
                if (btn) {
                    btn.classList.remove('active');
                }
            });
        }
        
        // Masaüstü butonu
        if (deviceButtons.desktop) {
            deviceButtons.desktop.addEventListener('click', function() {
                clearActiveDeviceButtons();
                this.classList.add('active');
                deviceManager.select('desktop');
                handleDeviceSwitch('desktop');
            });
        }
        
        // Tablet butonu
        if (deviceButtons.tablet) {
            deviceButtons.tablet.addEventListener('click', function() {
                clearActiveDeviceButtons();
                this.classList.add('active');
                deviceManager.select('tablet');
                handleDeviceSwitch('tablet');
            });
        }
        
        // Mobil butonu
        if (deviceButtons.mobile) {
            deviceButtons.mobile.addEventListener('click', function() {
                clearActiveDeviceButtons();
                this.classList.add('active');
                deviceManager.select('mobile');
                handleDeviceSwitch('mobile');
            });
        }
    }
    
    /**
     * Cihaz değişikliklerini yönet
     * @param {string} device Cihaz türü
     */
    function handleDeviceSwitch(device) {
        // Canvas genişliğini güncelle
        const canvas = editor.Canvas;
        const canvasEl = canvas.getElement();
        
        // Cihaz değişimi olayını tetikle
        const event = new CustomEvent('studio:device-changed', { 
            detail: { 
                device
            } 
        });
        document.dispatchEvent(event);
    }
    
    /**
     * Kod düzenleme butonlarını ayarla
     */
    function setupCodeEditButtons() {
        // HTML düzenleme butonu
        const codeEditBtn = document.querySelector(config.buttonSelectors.codeEdit);
        if (codeEditBtn) {
            codeEditBtn.addEventListener('click', function() {
                openCodeEditor('html');
            });
        }
        
        // CSS düzenleme butonu
        const cssEditBtn = document.querySelector(config.buttonSelectors.cssEdit);
        if (cssEditBtn) {
            cssEditBtn.addEventListener('click', function() {
                openCodeEditor('css');
            });
        }
        
        // JS düzenleme butonu
        const jsEditBtn = document.querySelector(config.buttonSelectors.jsEdit);
        if (jsEditBtn) {
            jsEditBtn.addEventListener('click', function() {
                openCodeEditor('js');
            });
        }
    }
    
    /**
     * Kod düzenleyiciyi aç
     * @param {string} type Kod türü (html, css, js)
     */
    function openCodeEditor(type) {
        let code = '';
        let title = '';
        let mode = '';
        
        switch (type) {
            case 'html':
                code = editor.getHtml();
                title = 'HTML Düzenleyici';
                mode = 'html';
                break;
            case 'css':
                code = editor.getCss();
                title = 'CSS Düzenleyici';
                mode = 'css';
                break;
            case 'js':
                const jsContentEl = document.getElementById('js-content');
                code = jsContentEl ? jsContentEl.value : '';
                title = 'JavaScript Düzenleyici';
                mode = 'javascript';
                break;
            default:
                return;
        }
        
        // Kod düzenleyici modalını oluştur
        const modalId = 'code-editor-modal';
        
        // Mevcut modalı kaldır
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }
        
        // Modal HTML yapısı
        const modalHTML = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}-label" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="${modalId}-label">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body p-0">
                        <textarea id="code-editor-textarea" class="form-control" style="height: 70vh; font-family: monospace;">${code}</textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="code-editor-save">Uygula</button>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        // Modal'ı body'ye ekle
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Modal örneğini al
        const modalElement = document.getElementById(modalId);
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static'
        });
        
        // Modal'ı göster
        modal.show();
        
        // CodeMirror varsa yükle
        const textarea = document.getElementById('code-editor-textarea');
        let codeMirrorInstance = null;
        
        if (window.CodeMirror) {
            codeMirrorInstance = CodeMirror.fromTextArea(textarea, {
                mode: mode,
                theme: 'default',
                lineNumbers: true,
                lineWrapping: true,
                autoCloseBrackets: true,
                autoCloseTags: true,
                matchBrackets: true,
                matchTags: { bothTags: true },
                extraKeys: { 'Ctrl-Space': 'autocomplete' },
                indentUnit: 2,
                tabSize: 2,
                scrollbarStyle: 'native'
            });
        }
        
        // Kaydet butonuna tıklama olayı ekle
        const saveButton = document.getElementById('code-editor-save');
        saveButton.addEventListener('click', function() {
            const updatedCode = codeMirrorInstance ? codeMirrorInstance.getValue() : textarea.value;
            
            switch (type) {
                case 'html':
                    editor.setComponents(updatedCode);
                    break;
                case 'css':
                    editor.setStyle(updatedCode);
                    break;
                case 'js':
                    const jsContentEl = document.getElementById('js-content');
                    if (jsContentEl) {
                        jsContentEl.value = updatedCode;
                    }
                    break;
            }
            
            // Modalı kapat
            modal.hide();
            
            // Kod değişikliği olayını tetikle
            const event = new CustomEvent('studio:code-updated', { 
                detail: { 
                    type, 
                    code: updatedCode
                } 
            });
            document.dispatchEvent(event);
        });
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        setupToolbar: setupToolbar,
        initButtons: initButtons,
        handleDeviceSwitch: handleDeviceSwitch
    };
})();

// Global olarak kullanılabilir yap
window.StudioToolbarManager = StudioToolbarManager;