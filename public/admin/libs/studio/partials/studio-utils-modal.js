/**
 * Studio Editor - Modal Modülü
 * Modal dialog gösterme işlevleri
 * ✅ Link ekleme özelliği GrapesJS Modal API ile çalışıyor
 */

window.StudioModal = (function() {
    let monacoLoaded = false;
    let currentEditor = null;
    let currentTheme = localStorage.getItem('studio_monaco_theme') || 'vs-dark';
    
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
     * ✅ Link ekleme modalı göster (GrapesJS Modal API kullanarak)
     * @param {string} selectedText - Seçili metin
     * @param {string} currentUrl - Mevcut URL (düzenleme için)
     * @param {string} currentTarget - Mevcut target (düzenleme için)
     * @param {string} currentTitle - Mevcut title (düzenleme için)
     * @param {Function} callback - Link bilgileri ile çağrılacak fonksiyon
     * @param {Object} editor - GrapesJS editor örneği
     */
    function showLinkModal(selectedText, currentUrl = '', currentTarget = '', currentTitle = '', callback, editor) {
        if (!editor || !editor.Modal) {
            console.error('GrapesJS editor veya Modal API bulunamadı');
            return;
        }
        
        // Modal içeriği oluştur
        const modalContent = document.createElement('div');
        modalContent.className = 'link-modal-content';
        modalContent.style.padding = '20px';
        modalContent.style.minWidth = '400px';
        
        modalContent.innerHTML = `
            <div class="mb-3">
                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600;">Seçili Metin</label>
                <input type="text" class="form-control" id="link-text" value="${selectedText}" readonly 
                       style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 8px 12px; border-radius: 4px; width: 100%;">
            </div>
            
            <div class="mb-3">
                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600;">URL *</label>
                <input type="url" class="form-control" id="link-url" placeholder="https://example.com" value="${currentUrl}" required
                       style="border: 1px solid #dee2e6; padding: 8px 12px; border-radius: 4px; width: 100%;">
                <div class="form-text" style="font-size: 12px; color: #6c757d; margin-top: 4px;">Başında http:// veya https:// olmalıdır</div>
            </div>
            
            <div class="mb-3">
                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600;">Başlık (İsteğe Bağlı)</label>
                <input type="text" class="form-control" id="link-title" placeholder="Link başlığı" value="${currentTitle}"
                       style="border: 1px solid #dee2e6; padding: 8px 12px; border-radius: 4px; width: 100%;">
                <div class="form-text" style="font-size: 12px; color: #6c757d; margin-top: 4px;">Mouse ile üzerine gelindiğinde gösterilecek metin</div>
            </div>
            
            <div class="mb-4">
                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600;">Hedef</label>
                <select class="form-select" id="link-target" style="border: 1px solid #dee2e6; padding: 8px 12px; border-radius: 4px; width: 100%;">
                    <option value="false" ${currentTarget === '' || currentTarget === 'false' ? 'selected' : ''}>Aynı Pencerede</option>
                    <option value="_blank" ${currentTarget === '_blank' ? 'selected' : ''}>Yeni Pencerede</option>
                </select>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #dee2e6; padding-top: 15px;">
                <button type="button" class="btn btn-secondary" id="cancelLinkBtn" style="padding: 8px 16px; border: 1px solid #6c757d; background: #6c757d; color: white; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-times me-1"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="saveLinkBtn" style="padding: 8px 16px; border: 1px solid #0d6efd; background: #0d6efd; color: white; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-check me-1"></i>Link Ekle
                </button>
            </div>
        `;

        // GrapesJS Modal'ı aç
        editor.Modal.open({
            title: '<i class="fas fa-link" style="color: #0d6efd; margin-right: 8px;"></i>Link Ekle',
            content: modalContent,
            attributes: { 
                class: 'studio-link-modal',
                'data-modal-type': 'link-editor'
            }
        });

        // Focus URL input'una
        setTimeout(() => {
            const urlInput = modalContent.querySelector("#link-url");
            if (urlInput) {
                urlInput.focus();
                urlInput.select();
            }
        }, 100);

        // Event listener'ları ekle
        const saveLinkBtn = modalContent.querySelector("#saveLinkBtn");
        const cancelLinkBtn = modalContent.querySelector("#cancelLinkBtn");

        function closeModal() {
            editor.Modal.close();
        }

        function saveLink() {
            const url = modalContent.querySelector("#link-url").value.trim();
            const title = modalContent.querySelector("#link-title").value.trim();
            const target = modalContent.querySelector("#link-target").value;
            
            if (!url) {
                if (window.StudioNotification) {
                    window.StudioNotification.warning('Lütfen geçerli bir URL girin');
                }
                modalContent.querySelector("#link-url").focus();
                return;
            }
            
            if (!isValidUrl(url)) {
                if (window.StudioNotification) {
                    window.StudioNotification.warning('Lütfen geçerli bir URL girin (http:// veya https:// ile başlamalı)');
                }
                modalContent.querySelector("#link-url").focus();
                return;
            }
            
            const linkData = {
                url: url,
                title: title,
                target: target
            };
            
            closeModal();
            
            // Kısa bir delay ile callback'i çağır
            setTimeout(() => {
                callback(linkData);
            }, 50);
        }

        if (saveLinkBtn) {
            saveLinkBtn.addEventListener("click", saveLink);
        }

        if (cancelLinkBtn) {
            cancelLinkBtn.addEventListener("click", closeModal);
        }

        // Enter tuşu ile kaydetme
        modalContent.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveLink();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeModal();
            }
        });

        // Modal kapandığında callback ile temizlik
        editor.Modal.onceClose(() => {
            console.log('Link modal kapatıldı');
        });
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
     */
    function showEditModal(title, content, callback, language = 'html') {
        // Mevcut Bootstrap modal sistemini koruyoruz - çalışıyor
        const modal = document.createElement("div");
        modal.className = "modal fade show";
        modal.id = "codeEditModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.style.display = "block";
        modal.style.zIndex = "99999";
        
        modal.innerHTML = `
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-code text-primary me-2"></i>${title}
                        </h5>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="format-btn" title="Kodu Formatla">
                                <i class="fas fa-magic me-1"></i>Format
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="theme-btn" title="Tema: ${getThemeName(currentTheme)}">
                                <i class="fas fa-palette me-1"></i>${getThemeName(currentTheme)}
                            </button>
                            <button type="button" class="btn-close" aria-label="Kapat"></button>
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
                                <span class="badge bg-secondary me-2">
                                    <i class="fas fa-list-ol me-1"></i>
                                    <span id="line-count">0</span> satır
                                </span>
                                <span class="badge" style="background-color: ${getThemeColor(currentTheme)}; color: white;">
                                    <i class="fas fa-palette me-1"></i>
                                    <span id="theme-name">${getThemeName(currentTheme)}</span>
                                </span>
                            </div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-3">
                                    <kbd>Ctrl+S</kbd> Kaydet • <kbd>Ctrl+F</kbd> Bul • <kbd>Ctrl+T</kbd> Tema
                                </small>
                            </div>
                        </div>
                        <div id="monaco-editor-container" style="flex: 1; min-height: 60vh;"></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" id="cancelCodeBtn">
                            <i class="fas fa-times me-1"></i>İptal
                        </button>
                        <button type="button" class="btn btn-primary" id="saveCodeBtn">
                            <i class="fas fa-check me-1"></i>Uygula
                        </button>
                    </div>
                </div>
            </div>
        `;

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.style.zIndex = '99998';

        document.body.appendChild(backdrop);
        document.body.appendChild(modal);
        document.body.classList.add('modal-open');

        loadMonaco().then(() => {
            initMonacoEditor(content, language);
        }).catch(error => {
            console.error('Monaco Editor yüklenemedi:', error);
            fallbackToTextarea(content);
        });

        function closeModal() {
            if (currentEditor) {
                currentEditor.dispose();
                currentEditor = null;
            }
            // Bootstrap modal temizliği
            const existingModals = document.querySelectorAll('.modal');
            existingModals.forEach(modal => {
                if (modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            });
            
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            });
            
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
        }

        document.getElementById("saveCodeBtn").addEventListener("click", function () {
            const newCode = currentEditor ? currentEditor.getValue() : document.getElementById("fallback-textarea")?.value || '';
            closeModal();
            callback(newCode);
        });

        document.getElementById("cancelCodeBtn").addEventListener("click", closeModal);
        modal.querySelector(".btn-close").addEventListener("click", closeModal);
        backdrop.addEventListener('click', closeModal);
    }
    
    /**
     * Tema adını al
     * @param {string} theme - Tema kodu
     * @returns {string} - Tema adı
     */
    function getThemeName(theme) {
        const themeNames = {
            'vs-dark': 'Koyu',
            'vs': 'Açık',
            'hc-black': 'Yüksek Kontrast'
        };
        return themeNames[theme] || 'Bilinmeyen';
    }
    
    /**
     * Tema rengini al
     * @param {string} theme - Tema kodu
     * @returns {string} - Tema rengi
     */
    function getThemeColor(theme) {
        const themeColors = {
            'vs-dark': '#1e1e1e',
            'vs': '#ffffff',
            'hc-black': '#000000'
        };
        return themeColors[theme] || '#6c757d';
    }
    
    /**
     * Tema değiştir
     */
    function toggleTheme() {
        const themes = ['vs-dark', 'vs', 'hc-black'];
        const currentIndex = themes.indexOf(currentTheme);
        currentTheme = themes[(currentIndex + 1) % themes.length];
        
        // Temayı localStorage'a kaydet
        localStorage.setItem('studio_monaco_theme', currentTheme);
        
        if (monacoLoaded && currentEditor) {
            monaco.editor.setTheme(currentTheme);
        }
        
        // Tema adını güncelle
        const themeNameEl = document.getElementById('theme-name');
        if (themeNameEl) {
            themeNameEl.textContent = getThemeName(currentTheme);
        }
        
        // Tema badge rengini güncelle
        const themeBadge = themeNameEl?.parentElement;
        if (themeBadge) {
            themeBadge.style.backgroundColor = getThemeColor(currentTheme);
            themeBadge.style.color = currentTheme === 'vs' ? '#000000' : '#ffffff';
        }
        
        // Tema buton metnini güncelle
        const themeBtn = document.getElementById('theme-btn');
        if (themeBtn) {
            themeBtn.title = `Tema: ${getThemeName(currentTheme)}`;
            themeBtn.innerHTML = `<i class="fas fa-palette me-1"></i>${getThemeName(currentTheme)}`;
        }
    }
    
    // Monaco Editor helper functions (mevcut sistem korunuyor)
    function initMonacoEditor(content, language) {
        const container = document.getElementById('monaco-editor-container');
        if (!container) return;

        const editorSettings = {
            value: content,
            language: language,
            theme: currentTheme,
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
            
            updateCounts();
            
            currentEditor.onDidChangeModelContent(() => {
                updateCounts();
            });
            
            setupKeyboardShortcuts();
            setupButtonEvents();
            
        } catch (error) {
            console.error('Monaco Editor oluşturma hatası:', error);
            fallbackToTextarea(content);
        }
    }
    
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
    
    function setupKeyboardShortcuts() {
        if (!currentEditor) return;
        
        currentEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => {
            document.getElementById('saveCodeBtn')?.click();
        });
        
        currentEditor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyT, () => {
            toggleTheme();
        });
    }
    
    function setupButtonEvents() {
        const formatBtn = document.getElementById('format-btn');
        if (formatBtn) {
            formatBtn.addEventListener('click', () => {
                if (currentEditor) {
                    currentEditor.getAction('editor.action.formatDocument').run();
                }
            });
        }
        
        const themeBtn = document.getElementById('theme-btn');
        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                toggleTheme();
            });
        }
    }
    
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
            
            textarea.dispatchEvent(new Event('input'));
        }
    }
    
    /**
     * Onay modalı göster - Mevcut sistem korunuyor
     */
    function showConfirmModal(title, message, confirmCallback, cancelCallback) {
        // Bootstrap modal sistemini koruyoruz
        const modal = document.createElement("div");
        modal.className = "modal fade show";
        modal.id = "confirmModal";
        modal.setAttribute("tabindex", "-1");
        modal.setAttribute("aria-modal", "true");
        modal.setAttribute("role", "dialog");
        modal.style.display = "block";
        modal.style.zIndex = "99999";
        
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" aria-label="Kapat"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelConfirmBtn">İptal</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Onayla</button>
                    </div>
                </div>
            </div>
        `;

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.style.zIndex = '99998';

        document.body.appendChild(backdrop);
        document.body.appendChild(modal);
        document.body.classList.add('modal-open');

        function closeModal() {
            const existingModals = document.querySelectorAll('.modal');
            existingModals.forEach(modal => {
                if (modal.parentNode) {
                    modal.parentNode.removeChild(modal);
                }
            });
            
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            });
            
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
        }

        document.getElementById("confirmBtn").addEventListener("click", function () {
            if (typeof confirmCallback === 'function') {
                confirmCallback();
            }
            closeModal();
        });

        document.getElementById("cancelConfirmBtn").addEventListener("click", function () {
            if (typeof cancelCallback === 'function') {
                cancelCallback();
            }
            closeModal();
        });

        modal.querySelector(".btn-close").addEventListener("click", function () {
            if (typeof cancelCallback === 'function') {
                cancelCallback();
            }
            closeModal();
        });

        backdrop.addEventListener('click', function () {
            if (typeof cancelCallback === 'function') {
                cancelCallback();
            }
            closeModal();
        });
    }
    
    return {
        showEditModal: showEditModal,
        showConfirmModal: showConfirmModal,
        showLinkModal: showLinkModal
    };
})();