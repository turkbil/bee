/**
 * Studio Editor - Eylemler Modülü
 * Kaydetme, dışa aktarma ve önizleme işlemleri
 */

window.StudioActions = (function() {
    // Cleanup tekrarlarını önlemek için flag
    let actionsSetup = false;
    
    /**
     * Tüm eylem butonlarını ayarlar
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupActions(editor, config) {
        if (actionsSetup) {
            console.log("Actions zaten ayarlandı, işlem atlanıyor");
            return;
        }
        actionsSetup = true;
        
        console.log("Setting up actions");
        
        // Daha önce eklenmiş olay dinleyicilerini temizle
        cleanup();
        
        // Kaydetme butonunu ayarla
        window.StudioSave.setupSaveButton(editor, config);
        
        // Önizleme butonunu ayarla
        window.StudioExport.setupPreviewButton(editor);
        
        // Diğer buton işlevlerini ayarla
        setupVisibilityButton(editor);
        setupCommandButtons(editor);
        
        // RTE sistemini özelleştir
        setupCustomRTE(editor);
        
        // Export ve Back butonları kaldırıldı
    }
    
    /**
     * Önceden eklenmiş olay dinleyicilerini temizle
     */
    function cleanup() {
        const buttons = ['save-btn', 'preview-btn', 'sw-visibility', 'cmd-clear', 'cmd-undo', 'cmd-redo', 'cmd-code-edit', 'cmd-css-edit'];
        
        buttons.forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button && !button.hasAttribute('data-cleaned')) {
                const newButton = button.cloneNode(true);
                newButton.setAttribute('data-cleaned', 'true');
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
            }
        });
    }
    
    /**
     * Görünürlük butonunu ayarlar (bileşen sınırlarını göster/gizle)
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupVisibilityButton(editor) {
        const swVisibility = document.getElementById("sw-visibility");
        if (swVisibility && !swVisibility.hasAttribute('data-visibility-setup')) {
            swVisibility.setAttribute('data-visibility-setup', 'true');
            console.log("Görünürlük butonu bulundu:", swVisibility);
            
            swVisibility.addEventListener("click", () => {
                console.log("Görünürlük butonuna tıklandı");
                try {
                    // State değişkenini takip etmek için
                    let currentState = swVisibility.classList.contains('active');
                    console.log("Mevcut durum (active):", currentState);
                    
                    // Canvas'ı kontrol et
                    console.log("Editor:", editor);
                    console.log("Canvas:", editor.Canvas);
                    
                    const frames = editor.Canvas.getFrames();
                    console.log("Frames:", frames);
                    
                    frames.forEach((frame, index) => {
                        console.log(`Frame ${index} işleniyor:`, frame);
                        const body = frame.view.getBody();
                        console.log(`Frame ${index} body:`, body);
                        
                        const allElements = body.querySelectorAll('*');
                        console.log(`Frame ${index} element sayısı:`, allElements.length);
                        
                        if (!currentState) {
                            console.log("Sınırları göster işlemi yapılıyor");
                            allElements.forEach(el => {
                                el.style.outline = '1px dashed rgba(170, 170, 170, 0.7)';
                            });
                        } else {
                            console.log("Sınırları gizle işlemi yapılıyor");
                            allElements.forEach(el => {
                                el.style.outline = '';
                            });
                        }
                    });
                    
                    // Buton durumunu güncelle
                    swVisibility.classList.toggle('active');
                    console.log("Buton durumu güncellendi:", swVisibility.classList.contains('active'));
                    
                    return swVisibility.classList.contains('active');
                } catch (error) {
                    console.error('Bileşen sınırlarını gösterme/gizleme hatası:', error);
                    return false;
                }
            });
        } else {
            // Hata mesajını kaldırdım, buton bulunamazsa sessizce geç
            console.log("Görünürlük butonu mevcut değil veya zaten ayarlanmış, atlanıyor");
        }
    }
    
    /**
     * Komut düğmelerini ayarlar (Temizle, Geri Al, İleri Al)
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCommandButtons(editor) {
        // İçerik temizle butonu
        const cmdClear = document.getElementById("cmd-clear");
        if (cmdClear && !cmdClear.hasAttribute('data-clear-setup')) {
            cmdClear.setAttribute('data-clear-setup', 'true');
            cmdClear.addEventListener("click", () => {
                if (confirm("İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.")) {
                    editor.DomComponents.clear();
                    editor.CssComposer.clear();
                }
            });
        }

        // Geri Al butonu
        const cmdUndo = document.getElementById("cmd-undo");
        if (cmdUndo && !cmdUndo.hasAttribute('data-undo-setup')) {
            cmdUndo.setAttribute('data-undo-setup', 'true');
            cmdUndo.addEventListener("click", () => {
                editor.UndoManager.undo();
            });
        }

        // Yinele butonu
        const cmdRedo = document.getElementById("cmd-redo");
        if (cmdRedo && !cmdRedo.hasAttribute('data-redo-setup')) {
            cmdRedo.setAttribute('data-redo-setup', 'true');
            cmdRedo.addEventListener("click", () => {
                editor.UndoManager.redo();
            });
        }
                
        // HTML kodu düzenleme - Monaco ile
        const cmdCodeEdit = document.getElementById("cmd-code-edit");
        if (cmdCodeEdit && !cmdCodeEdit.hasAttribute('data-code-setup')) {
            cmdCodeEdit.setAttribute('data-code-setup', 'true');
            cmdCodeEdit.addEventListener("click", () => {
                const htmlContent = editor.getHtml();
                window.StudioModal.showEditModal("HTML Düzenle", htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                }, 'html');
            });
        }

        // CSS kodu düzenleme - Monaco ile
        const cmdCssEdit = document.getElementById("cmd-css-edit");
        if (cmdCssEdit && !cmdCssEdit.hasAttribute('data-css-setup')) {
            cmdCssEdit.setAttribute('data-css-setup', 'true');
            cmdCssEdit.addEventListener("click", () => {
                // avoidProtected parametresi ile CSS içeriğini al
                const cssContent = editor.getCss({ avoidProtected: true });
                
                window.StudioModal.showEditModal("CSS Düzenle", cssContent, (newCss) => {
                    // CSS'i tamamen temizlemeden setle
                    editor.setStyle(newCss, { avoidProtected: true });
                }, 'css');
            });
        }
    }
    
    /**
     * Custom RTE sistemini kurulumlar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCustomRTE(editor) {
        let storedSelection = null;
        let storedRange = null;
        
        editor.on('rte:enable', (rte) => {
            console.log('RTE aktif edildi');
            
            const checkForLinkButtons = (attempts = 0) => {
                if (attempts > 15) {
                    console.log('Link butonları bulunamadı, maksimum deneme sayısına ulaşıldı');
                    return;
                }
                
                setTimeout(() => {
                    try {
                        // Ana belgede ara
                        let linkButtons = document.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                        let allRteElements = document.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                        
                        console.log(`Deneme ${attempts + 1} (Ana belge): RTE elementler: ${allRteElements.length}, Link butonları: ${linkButtons.length}`);
                        
                        // iframe'de de ara
                        const frameEl = editor.Canvas.getFrameEl();
                        if (frameEl) {
                            const frameDoc = frameEl.contentDocument || frameEl.contentWindow.document;
                            if (frameDoc) {
                                const iframeLinkButtons = frameDoc.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                                const iframeRteElements = frameDoc.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                                
                                console.log(`Deneme ${attempts + 1} (iframe): RTE elementler: ${iframeRteElements.length}, Link butonları: ${iframeLinkButtons.length}`);
                                
                                linkButtons = [...linkButtons, ...iframeLinkButtons];
                            }
                        }
                        
                        if (linkButtons.length === 0) {
                            console.log('Link butonları henüz yüklenmedi, tekrar deneniyor...');
                            checkForLinkButtons(attempts + 1);
                            return;
                        }
                        
                        // SVG içerik kontrolü ile link butonunu bul
                        const realLinkButtons = Array.from(linkButtons).filter(btn => {
                            const svg = btn.querySelector('svg');
                            if (svg) {
                                const path = svg.querySelector('path');
                                if (path) {
                                    const d = path.getAttribute('d');
                                    return d && d.includes('M3.9,12');
                                }
                            }
                            return btn.title === 'Link';
                        });
                        
                        console.log('Gerçek link butonları bulundu:', realLinkButtons.length);
                        
                        if (realLinkButtons.length === 0) {
                            console.log('Gerçek link butonları henüz yüklenmedi, tekrar deneniyor...');
                            checkForLinkButtons(attempts + 1);
                            return;
                        }
                        
                        realLinkButtons.forEach((linkButton, index) => {
                            if (!linkButton.hasAttribute('data-custom-link-setup')) {
                                linkButton.setAttribute('data-custom-link-setup', 'true');
                                
                                const handleLinkClick = (e) => {
                                    console.log('Custom link butonuna tıklandı!');
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                                    
                                    let selection, frameWindow;
                                    
                                    // iframe'den seçimi al
                                    if (frameEl) {
                                        frameWindow = frameEl.contentWindow;
                                        selection = frameWindow.getSelection();
                                    } else {
                                        selection = window.getSelection();
                                    }
                                    
                                    if (!selection || selection.toString().trim() === '') {
                                        window.StudioNotification.warning('Lütfen önce link yapmak istediğiniz metni seçin');
                                        return false;
                                    }
                                    
                                    const selectedText = selection.toString();
                                    console.log('Seçili metin:', selectedText);
                                    
                                    // Seçimi sakla
                                    if (selection.rangeCount > 0) {
                                        storedRange = selection.getRangeAt(0).cloneRange();
                                        storedSelection = selection;
                                    }
                                    
                                    let currentUrl = '';
                                    let currentTarget = '';
                                    let currentTitle = '';
                                    
                                    try {
                                        const range = selection.getRangeAt(0);
                                        let parentNode = range.commonAncestorContainer;
                                        
                                        if (parentNode.nodeType === Node.TEXT_NODE) {
                                            parentNode = parentNode.parentNode;
                                        }
                                        
                                        while (parentNode && parentNode.tagName !== 'A' && parentNode !== document.body) {
                                            parentNode = parentNode.parentNode;
                                        }
                                        
                                        if (parentNode && parentNode.tagName === 'A') {
                                            currentUrl = parentNode.getAttribute('href') || '';
                                            currentTarget = parentNode.getAttribute('target') || '';
                                            currentTitle = parentNode.getAttribute('title') || '';
                                        }
                                    } catch (err) {
                                        console.log('Mevcut link bilgisi alınırken hata:', err);
                                    }
                                    
                                    window.StudioModal.showLinkModal(selectedText, currentUrl, currentTarget, currentTitle, (linkData) => {
                                        if (!linkData.url) return;
                                        
                                        console.log('Link verisi alındı:', linkData);
                                        
                                        try {
                                            let targetDoc = document;
                                            if (frameWindow) {
                                                targetDoc = frameWindow.document;
                                            }
                                            
                                            // Saklanan seçimi geri yükle
                                            if (storedRange && storedSelection) {
                                                if (frameWindow) {
                                                    frameWindow.getSelection().removeAllRanges();
                                                    frameWindow.getSelection().addRange(storedRange);
                                                } else {
                                                    window.getSelection().removeAllRanges();
                                                    window.getSelection().addRange(storedRange);
                                                }
                                            }
                                            
                                            const currentSelection = frameWindow ? frameWindow.getSelection() : window.getSelection();
                                            
                                            if (!currentSelection.rangeCount) {
                                                window.StudioNotification.error('Seçim kayboldu, lütfen tekrar deneyin');
                                                return;
                                            }
                                            
                                            const range = currentSelection.getRangeAt(0);
                                            
                                            let existingLink = null;
                                            let parentNode = range.commonAncestorContainer;
                                            
                                            if (parentNode.nodeType === Node.TEXT_NODE) {
                                                parentNode = parentNode.parentNode;
                                            }
                                            
                                            while (parentNode && parentNode.tagName !== 'A' && parentNode !== targetDoc.body) {
                                                parentNode = parentNode.parentNode;
                                            }
                                            
                                            if (parentNode && parentNode.tagName === 'A') {
                                                existingLink = parentNode;
                                            }
                                            
                                            if (existingLink) {
                                                // Mevcut linki güncelle
                                                existingLink.href = linkData.url;
                                                if (linkData.target && linkData.target !== 'false') {
                                                    existingLink.target = linkData.target;
                                                } else {
                                                    existingLink.removeAttribute('target');
                                                }
                                                if (linkData.title) {
                                                    existingLink.title = linkData.title;
                                                } else {
                                                    existingLink.removeAttribute('title');
                                                }
                                            } else {
                                                // Yeni link oluştur
                                                const linkElement = targetDoc.createElement('a');
                                                linkElement.href = linkData.url;
                                                
                                                if (linkData.target && linkData.target !== 'false') {
                                                    linkElement.target = linkData.target;
                                                }
                                                
                                                if (linkData.title) {
                                                    linkElement.title = linkData.title;
                                                }
                                                
                                                // İçeriği güvenli şekilde sar
                                                try {
                                                    const contents = range.extractContents();
                                                    linkElement.appendChild(contents);
                                                    range.insertNode(linkElement);
                                                } catch (e) {
                                                    // Fallback
                                                    linkElement.textContent = selectedText;
                                                    range.deleteContents();
                                                    range.insertNode(linkElement);
                                                }
                                            }
                                            
                                            currentSelection.removeAllRanges();
                                            
                                            if (rte && typeof rte.updateContent === 'function') {
                                                rte.updateContent();
                                            }
                                            
                                            // Değişiklikleri editöre bildir
                                            setTimeout(() => {
                                                editor.trigger('change:canvasOffset');
                                            }, 100);
                                            
                                            window.StudioNotification.success('Link başarıyla eklendi');
                                        } catch (error) {
                                            console.error('Link ekleme hatası:', error);
                                            window.StudioNotification.error('Link eklenirken bir hata oluştu');
                                        }
                                        
                                        // Saklanan seçimi temizle
                                        storedSelection = null;
                                        storedRange = null;
                                    });
                                    
                                    return false;
                                };
                                
                                linkButton.addEventListener('click', handleLinkClick, true);
                                linkButton.addEventListener('mousedown', handleLinkClick, true);
                                linkButton.addEventListener('touchstart', handleLinkClick, true);
                                
                                console.log(`Link butonu ${index} için event listener eklendi`);
                            }
                        });
                    } catch (error) {
                        console.error('RTE link button setup hatası:', error);
                        checkForLinkButtons(attempts + 1);
                    }
                }, 200 + (attempts * 100));
            };
            
            checkForLinkButtons();
        });
        
        editor.on('rte:disable', () => {
            console.log('RTE devre dışı bırakıldı');
            // Saklanan seçimi temizle
            storedSelection = null;
            storedRange = null;
        });
    }
    
    return {
        setupActions: setupActions,
        cleanup: cleanup,
        setupVisibilityButton: setupVisibilityButton,
        setupCommandButtons: setupCommandButtons,
        setupCustomRTE: setupCustomRTE
    };
})();