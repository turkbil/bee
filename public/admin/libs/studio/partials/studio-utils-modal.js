/**
 * Studio Editor - Modal Modülü
 * Modal dialog gösterme işlevleri
 */

window.StudioModal = (function() {
    let monacoLoaded = false;
    let currentEditor = null;
    
    /**
     * Monaco Editor'ü yükle
     */
    function loadMonaco() {
        if (monacoLoaded) return Promise.resolve();
        
        return new Promise((resolve, reject) => {
            if (typeof require === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs/loader.min.js';
                script.onload = () => {
                    setupMonaco().then(resolve).catch(reject);
                };
                script.onerror = reject;
                document.head.appendChild(script);
            } else {
                setupMonaco().then(resolve).catch(reject);
            }
        });
    }
    
    /**
     * Monaco Editor'ü yapılandır
     */
    function setupMonaco() {
        return new Promise((resolve, reject) => {
            if (typeof require !== 'undefined') {
                require.config({ 
                    paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' }
                });

                require(['vs/editor/editor.main'], () => {
                    monacoLoaded = true;
                    resolve();
                });
            } else {
                reject(new Error('Require.js yüklenemedi'));
            }
        });
    }
    
    /**
     * Link ekleme modalı göster
     * @param {string} selectedText - Seçili metin
     * @param {string} currentUrl - Mevcut URL (düzenleme için)
     * @param {string} currentTarget - Mevcut target (düzenleme için)
     * @param {string} currentTitle - Mevcut title (düzenleme için)
     * @param {Function} callback - Link bilgileri ile çağrılacak fonksiyon
     */
    function showLinkModal(selectedText, currentUrl = '', currentTarget = '', currentTitle = '', callback) {
        // Mevcut modalı temizle
        const existingModal = document.getElementById("linkEditModal");
        if (existingModal) {
            existingModal.remove();
        }
        
        // Mevcut backdrop'ları temizle
        const backdropElements = document.querySelectorAll('.modal-backdrop');
        backdropElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "linkEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-link text-primary me-2"></i>Link Ekle
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Seçili Metin</label>
                            <input type="text" class="form-control" id="link-text" value="${selectedText}" readonly style="background-color: #f8f9fa;">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL *</label>
                            <input type="url" class="form-control" id="link-url" placeholder="https://example.com" value="${currentUrl}" required>
                            <div class="form-text">Başında http:// veya https:// olmalıdır</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Başlık (İsteğe Bağlı)</label>
                            <input type="text" class="form-control" id="link-title" placeholder="Link başlığı" value="${currentTitle}">
                            <div class="form-text">Mouse ile üzerine gelindiğinde gösterilecek metin</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Hedef</label>
                            <select class="form-select" id="link-target">
                                <option value="false" ${currentTarget === '' || currentTarget === 'false' ? 'selected' : ''}>Aynı Pencerede</option>
                                <option value="_blank" ${currentTarget === '_blank' ? 'selected' : ''}>Yeni Pencerede</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>İptal
                        </button>
                        <button type="button" class="btn btn-primary" id="saveLinkBtn">
                            <i class="fas fa-check me-1"></i>Link Ekle
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: false,
                focus: true
            });
            
            modalInstance.show();

            // URL input'a focus ver
            setTimeout(() => {
                const urlInput = document.getElementById("link-url");
                if (urlInput) {
                    urlInput.focus();
                }
            }, 500);

            // Kaydet butonu işlevi
            const saveLinkBtn = document.getElementById("saveLinkBtn");
            if (saveLinkBtn) {
                saveLinkBtn.addEventListener("click", function () {
                    const url = document.getElementById("link-url").value.trim();
                    const title = document.getElementById("link-title").value.trim();
                    const target = document.getElementById("link-target").value;
                    
                    if (!url) {
                        window.StudioNotification.warning('Lütfen geçerli bir URL girin');
                        document.getElementById("link-url").focus();
                        return;
                    }
                    
                    // URL doğrulaması
                    if (!isValidUrl(url)) {
                        window.StudioNotification.warning('Lütfen geçerli bir URL girin (http:// veya https:// ile başlamalı)');
                        document.getElementById("link-url").focus();
                        return;
                    }
                    
                    const linkData = {
                        url: url,
                        title: title,
                        target: target
                    };
                    
                    modalInstance.hide();
                    callback(linkData);
                });
            }

            // Enter tuşu ile kaydet
            modal.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    const saveLinkBtn = document.getElementById("saveLinkBtn");
                    if (saveLinkBtn) {
                        saveLinkBtn.click();
                    }
                }
            });

            modal.addEventListener("hidden.bs.modal", function () {
                modal.remove();
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    if (backdrop.parentNode) {
                        backdrop.parentNode.removeChild(backdrop);
                    }
                });
            });
        }
    }
    
    /**
     * URL doğrulaması
     * @param {string} url - Doğrulanacak URL
     * @returns {boolean} Geçerli URL mi
     */
    function isValidUrl(url) {
        try {
            const urlObj = new URL(url);
            return urlObj.protocol === 'http:' || urlObj.protocol === 'https:';
        } catch (e) {
            return false;
        }
    }
    
    /**
     * Kod düzenleme modalı göster (Monaco ile)
     * @param {string} title - Modal başlığı
     * @param {string} content - Düzenlenecek içerik
     * @param {Function} callback - Değişiklik kaydedildiğinde çağrılacak fonksiyon
     * @param {string} language - Dil (html, css, javascript)
     */
    function showEditModal(title, content, callback, language = 'html') {
        // Mevcut modalı temizle
        const existingModal = document.getElementById("codeEditModal");
        if (existingModal) {
            existingModal.remove();
        }
        
        // Mevcut backdrop'ları temizle
        const backdropElements = document.querySelectorAll('.modal-backdrop');
        backdropElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "codeEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-code text-primary me-2"></i>${title}
                        </h5>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="format-btn" title="Kodu Formatla">
                                <i class="fas fa-magic me-1"></i>Format
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="fullscreen-btn" title="Tam Ekran">
                                <i class="fas fa-expand me-1"></i>Tam Ekran
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>
                    </div>
                    <div class="modal-body p-0 d-flex flex-column">
                        <div class="editor-toolbar p-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-code me-1"></i>
                                    ${language.toUpperCase()}
                                </span>
                                <span class="badge bg-secondary me-2">
                                    <i class="fas fa-text-width me-1"></i>
                                    <span id="char-count">0</span> karakter
                                </span>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-list-ol me-1"></i>
                                    <span id="line-count">0</span> satır
                                </span>
                            </div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-3">
                                    <kbd>Ctrl+S</kbd> Kaydet • <kbd>Ctrl+F</kbd> Bul • <kbd>F11</kbd> Tam Ekran
                                </small>
                            </div>
                        </div>
                        <div id="monaco-editor-container" style="flex: 1; min-height: 60vh;"></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>İptal
                        </button>
                        <button type="button" class="btn btn-primary" id="saveCodeBtn">
                            <i class="fas fa-check me-1"></i>Uygula
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Monaco Editor'ü yükle ve başlat
        loadMonaco().then(() => {
            initMonacoEditor(content, language);
        }).catch(error => {
            console.error('Monaco Editor yüklenemedi:', error);
            // Fallback - textarea kullan
            fallbackToTextarea(content);
        });

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal, {
                backdrop: 'static',
                keyboard: false
            });
            modalInstance.show();

            document.getElementById("saveCodeBtn").addEventListener("click", function () {
                const newCode = currentEditor ? currentEditor.getValue() : document.getElementById("fallback-textarea")?.value || '';
                callback(newCode);
                modalInstance.hide();
            });

            modal.addEventListener("hidden.bs.modal", function () {
                if (currentEditor) {
                    currentEditor.dispose();
                    currentEditor = null;
                }
                modal.remove();
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    if (backdrop.parentNode) {
                        backdrop.parentNode.removeChild(backdrop);
                    }
                });
            });
        }
    }
    
    /**
     * Monaco Editor'ü başlat
     */
    function initMonacoEditor(content, language) {
        const container = document.getElementById('monaco-editor-container');
        if (!container) return;

        const editorSettings = {
            value: content,
            language: language,
            theme: 'vs-dark',
            fontSize: 14,
            lineHeight: 22,
            minimap: { enabled: true },
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
            autoIndent: 'advanced',
            renderWhitespace: 'selection',
            renderControlCharacters: true,
            renderFinalNewline: true,
            cursorBlinking: 'blink',
            cursorSmoothCaretAnimation: true,
            find: {
                seedSearchStringFromSelection: 'always',
                autoFindInSelection: 'never'
            },
            contextmenu: true,
            hover: { enabled: true }
        };

        try {
            currentEditor = monaco.editor.create(container, editorSettings);
            
            // Editör oluşturulduktan sonra otomatik formatla
            setTimeout(() => {
                if (currentEditor && (language === 'html' || language === 'css' || language === 'javascript')) {
                    try {
                        currentEditor.getAction('editor.action.formatDocument').run();
                        console.log('Kod otomatik formatlandı');
                    } catch (formatError) {
                        console.warn('Otomatik formatlama hatası:', formatError);
                    }
                }
            }, 200);
            
            // Satır ve karakter sayacını güncelle
            updateCounts();
            
            // İçerik değiştiğinde sayacı güncelle
            currentEditor.onDidChangeModelContent(() => {
                updateCounts();
            });
            
            // Keyboard shortcuts
            setupKeyboardShortcuts();
            
            // Buton olayları
            setupButtonEvents();
            
        } catch (error) {
            console.error('Monaco Editor oluşturma hatası:', error);
            fallbackToTextarea(content);
        }
    }
    
    /**
     * Sayaçları güncelle
     */
    function updateCounts() {
        if (!currentEditor) return;
        
        const text = currentEditor.getValue();
        const lines = text.split('\n').length;
        const chars = text.length;
        
        const lineCountEl = document.getElementById("line-count");
        const charCountEl = document.getElementById("char-count");
        
        if (lineCountEl) lineCountEl.textContent = lines;
        if (charCountEl) charCountEl.textContent = chars;
    }
    
    /**
     * Klavye kısayollarını ayarla
     */
    function setupKeyboardShortcuts() {
        if (!currentEditor) return;
        
        // Ctrl+S - Kaydet
        currentEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
            document.getElementById('saveCodeBtn')?.click();
        });
        
        // F11 - Tam ekran
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F11') {
                e.preventDefault();
                document.getElementById('fullscreen-btn')?.click();
            }
        });
    }
    
    /**
     * Buton olaylarını ayarla
     */
    function setupButtonEvents() {
        // Format butonu
        const formatBtn = document.getElementById('format-btn');
        if (formatBtn) {
            formatBtn.addEventListener('click', () => {
                if (currentEditor) {
                    currentEditor.getAction('editor.action.formatDocument').run();
                }
            });
        }
        
        // Tam ekran butonu
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', () => {
                const modal = document.getElementById('codeEditModal');
                if (modal) {
                    if (modal.classList.contains('fullscreen-mode')) {
                        modal.classList.remove('fullscreen-mode');
                        fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i>Tam Ekran';
                    } else {
                        modal.classList.add('fullscreen-mode');
                        fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i>Çıkış';
                    }
                    
                    setTimeout(() => {
                        if (currentEditor) {
                            currentEditor.layout();
                        }
                    }, 100);
                }
            });
        }
    }
    
    /**
     * Fallback textarea
     */
    function fallbackToTextarea(content) {
        const container = document.getElementById('monaco-editor-container');
        if (!container) return;
        
        container.innerHTML = `
            <textarea id="fallback-textarea" class="form-control font-monospace" style="width: 100%; height: 100%; border: none; resize: none;">${content}</textarea>
        `;
        
        const textarea = document.getElementById('fallback-textarea');
        if (textarea) {
            textarea.addEventListener('input', () => {
                const text = textarea.value;
                const lines = text.split('\n').length;
                const chars = text.length;
                
                const lineCountEl = document.getElementById("line-count");
                const charCountEl = document.getElementById("char-count");
                
                if (lineCountEl) lineCountEl.textContent = lines;
                if (charCountEl) charCountEl.textContent = chars;
            });
            
            // İlk sayımı yap
            textarea.dispatchEvent(new Event('input'));
        }
    }
    
    /**
     * Onay modalı göster
     * @param {string} title - Modal başlığı
     * @param {string} message - Modal mesajı
     * @param {Function} confirmCallback - Onay butonuna tıklandığında çağrılacak fonksiyon
     * @param {Function} cancelCallback - İptal butonuna tıklandığında çağrılacak fonksiyon
     */
    function showConfirmModal(title, message, confirmCallback, cancelCallback) {
        // Mevcut modalı temizle
        const existingModal = document.getElementById("confirmModal");
        if (existingModal) {
            existingModal.remove();
        }
        
        // Mevcut backdrop'ları temizle
        const backdropElements = document.querySelectorAll('.modal-backdrop');
        backdropElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        
        const modal = document.createElement("div");
        modal.className = "modal fade";
        modal.id = "confirmModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Onayla</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Bootstrap.Modal nesnesi mevcut mu kontrol et
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();

            document.getElementById("confirmBtn").addEventListener("click", function () {
                if (typeof confirmCallback === 'function') {
                    confirmCallback();
                }
                modalInstance.hide();
            });

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                });
            }

            modal.addEventListener("hidden.bs.modal", function () {
                modal.remove();
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    if (backdrop.parentNode) {
                        backdrop.parentNode.removeChild(backdrop);
                    }
                });
            });
        } else {
            // Fallback - basit modal gösterimi
            modal.style.display = "block";
            modal.style.backgroundColor = "rgba(0,0,0,0.5)";

            const confirmBtn = modal.querySelector("#confirmBtn");
            if (confirmBtn) {
                confirmBtn.addEventListener("click", function () {
                    if (typeof confirmCallback === 'function') {
                        confirmCallback();
                    }
                    document.body.removeChild(modal);
                });
            }

            const cancelBtn = modal.querySelector(".btn-secondary");
            if (cancelBtn) {
                cancelBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                    document.body.removeChild(modal);
                });
            }

            const closeBtn = modal.querySelector(".btn-close");
            if (closeBtn) {
                closeBtn.addEventListener("click", function () {
                    if (typeof cancelCallback === 'function') {
                        cancelCallback();
                    }
                    document.body.removeChild(modal);
                });
            }
        }
    }
    
    return {
        showEditModal: showEditModal,
        showConfirmModal: showConfirmModal,
        showLinkModal: showLinkModal
    };
})();