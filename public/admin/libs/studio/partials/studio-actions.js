/**
 * Studio Editor - Eylemler Modülü
 * Kaydetme, dışa aktarma ve önizleme işlemleri
 */

window.StudioActions = (function() {
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
        
        cleanup();
        
        window.StudioSave.setupSaveButton(editor, config);
        window.StudioExport.setupPreviewButton(editor);
        
        setupVisibilityButton(editor);
        setupCommandButtons(editor);
        setupCustomRTE(editor);
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
                    let currentState = swVisibility.classList.contains('active');
                    console.log("Mevcut durum (active):", currentState);
                    
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
                    
                    swVisibility.classList.toggle('active');
                    console.log("Buton durumu güncellendi:", swVisibility.classList.contains('active'));
                    
                    return swVisibility.classList.contains('active');
                } catch (error) {
                    console.error('Bileşen sınırlarını gösterme/gizleme hatası:', error);
                    return false;
                }
            });
        } else {
            console.log("Görünürlük butonu mevcut değil veya zaten ayarlanmış, atlanıyor");
        }
    }
    
    /**
     * Komut düğmelerini ayarlar (Temizle, Geri Al, İleri Al)
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCommandButtons(editor) {
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

        const cmdUndo = document.getElementById("cmd-undo");
        if (cmdUndo && !cmdUndo.hasAttribute('data-undo-setup')) {
            cmdUndo.setAttribute('data-undo-setup', 'true');
            cmdUndo.addEventListener("click", () => {
                editor.UndoManager.undo();
            });
        }

        const cmdRedo = document.getElementById("cmd-redo");
        if (cmdRedo && !cmdRedo.hasAttribute('data-redo-setup')) {
            cmdRedo.setAttribute('data-redo-setup', 'true');
            cmdRedo.addEventListener("click", () => {
                editor.UndoManager.redo();
            });
        }
                
        const cmdCodeEdit = document.getElementById("cmd-code-edit");
        if (cmdCodeEdit && !cmdCodeEdit.hasAttribute('data-code-setup')) {
            cmdCodeEdit.setAttribute('data-code-setup', 'true');
            cmdCodeEdit.addEventListener("click", () => {
                // HTML içeriğini temizlenmiş halde göster
                const rawHtml = editor.getHtml();
                const cleanedHtml = window.StudioSave.cleanHtml(rawHtml);
                
                window.StudioModal.showEditModal("HTML Düzenle", cleanedHtml, (newHtml) => {
                    editor.setComponents(newHtml);
                }, 'html');
            });
        }

        const cmdCssEdit = document.getElementById("cmd-css-edit");
        if (cmdCssEdit && !cmdCssEdit.hasAttribute('data-css-setup')) {
            cmdCssEdit.setAttribute('data-css-setup', 'true');
            cmdCssEdit.addEventListener("click", () => {
                const cssContent = editor.getCss({ avoidProtected: true });
                
                window.StudioModal.showEditModal("CSS Düzenle", cssContent, (newCss) => {
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
        let frameWindow = null;
        let frameDocument = null;
        let currentRTE = null;
        let currentEditedComponent = null;
        
        editor.on('rte:enable', (rte, view) => {
            console.log('RTE aktif edildi');
            currentRTE = rte;
            currentEditedComponent = view.model;
            
            const frameEl = editor.Canvas.getFrameEl();
            if (frameEl) {
                frameWindow = frameEl.contentWindow;
                frameDocument = frameEl.contentDocument || frameWindow.document;
            }
            
            const checkForLinkButtons = (attempts = 0) => {
                if (attempts > 15) {
                    console.log('Link butonları bulunamadı, maksimum deneme sayısına ulaşıldı');
                    return;
                }
                
                setTimeout(() => {
                    try {
                        let linkButtons = document.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                        let allRteElements = document.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                        
                        console.log(`Deneme ${attempts + 1} (Ana belge): RTE elementler: ${allRteElements.length}, Link butonları: ${linkButtons.length}`);
                        
                        if (frameEl && frameDocument) {
                            const iframeLinkButtons = frameDocument.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                            const iframeRteElements = frameDocument.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                            
                            console.log(`Deneme ${attempts + 1} (iframe): RTE elementler: ${iframeRteElements.length}, Link butonları: ${iframeLinkButtons.length}`);
                            
                            linkButtons = [...linkButtons, ...iframeLinkButtons];
                        }
                        
                        if (linkButtons.length === 0) {
                            console.log('Link butonları henüz yüklenmedi, tekrar deneniyor...');
                            checkForLinkButtons(attempts + 1);
                            return;
                        }
                        
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
                                    
                                    let selection;
                                    let targetDoc = document;
                                    
                                    if (frameWindow && frameDocument) {
                                        selection = frameWindow.getSelection();
                                        targetDoc = frameDocument;
                                    } else {
                                        selection = window.getSelection();
                                    }
                                    
                                    if (!selection || selection.toString().trim() === '') {
                                        if (window.StudioNotification) {
                                            window.StudioNotification.warning('Lütfen önce link yapmak istediğiniz metni seçin');
                                        }
                                        return false;
                                    }
                                    
                                    const selectedText = selection.toString();
                                    console.log('Seçili metin:', selectedText);
                                    
                                    // Selection bilgilerini kaydet
                                    let selectionInfo = null;
                                    if (selection.rangeCount > 0) {
                                        const range = selection.getRangeAt(0);
                                        selectionInfo = {
                                            range: range.cloneRange(),
                                            text: selectedText,
                                            startContainer: range.startContainer,
                                            startOffset: range.startOffset,
                                            endContainer: range.endContainer,
                                            endOffset: range.endOffset
                                        };
                                    }
                                    
                                    // Mevcut link bilgilerini kontrol et
                                    let currentUrl = '';
                                    let currentTarget = '';
                                    let currentTitle = '';
                                    
                                    try {
                                        const range = selection.getRangeAt(0);
                                        let parentNode = range.commonAncestorContainer;
                                        
                                        if (parentNode.nodeType === Node.TEXT_NODE) {
                                            parentNode = parentNode.parentNode;
                                        }
                                        
                                        while (parentNode && parentNode.tagName !== 'A' && parentNode !== targetDoc.body) {
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
                                    
                                    // GrapesJS Modal kullanarak link modalını aç
                                    window.StudioModal.showLinkModal(
                                        selectedText, 
                                        currentUrl, 
                                        currentTarget, 
                                        currentTitle, 
                                        (linkData) => {
                                            if (!linkData.url) return;
                                            
                                            console.log('Link verisi alındı:', linkData);
                                            
                                            try {
                                                // Manuel olarak link oluştur
                                                if (frameDocument && selectionInfo) {
                                                    console.log('Manuel link oluşturuluyor...');
                                                    
                                                    // Selection'ı yeniden oluştur
                                                    const newSelection = frameWindow.getSelection();
                                                    newSelection.removeAllRanges();
                                                    newSelection.addRange(selectionInfo.range);
                                                    
                                                    // Link elementini oluştur
                                                    const linkElement = frameDocument.createElement('a');
                                                    linkElement.href = linkData.url;
                                                    linkElement.setAttribute('data-gjs-type', 'link');
                                                    
                                                    if (linkData.target && linkData.target !== 'false') {
                                                        linkElement.target = linkData.target;
                                                    }
                                                    
                                                    if (linkData.title) {
                                                        linkElement.title = linkData.title;
                                                    }
                                                    
                                                    linkElement.textContent = selectedText;
                                                    
                                                    // Range içeriğini link ile değiştir
                                                    const range = selectionInfo.range;
                                                    range.deleteContents();
                                                    range.insertNode(linkElement);
                                                    
                                                    // Selection'ı temizle
                                                    newSelection.removeAllRanges();
                                                    
                                                    console.log('Link DOM\'a eklendi:', linkElement);
                                                    
                                                    // CRITICAL: DOM'dan tüm HTML'i al ve editörü yeniden parse et
                                                    const bodyHtml = frameDocument.body.innerHTML;
                                                    console.log('Frame body HTML:', bodyHtml);
                                                    
                                                    // RTE'yi kapat
                                                    if (currentRTE && typeof currentRTE.disable === 'function') {
                                                        currentRTE.disable();
                                                    } else {
                                                        editor.RichTextEditor.disable();
                                                    }
                                                    
                                                    // Editörü tamamen yeniden parse et
                                                    console.log('Editör yeniden parse ediliyor...');
                                                    editor.setComponents(bodyHtml);
                                                    
                                                    console.log('Editör parse edildi, doğrulama yapılıyor...');
                                                    
                                                    // Doğrulama
                                                    const finalHtml = editor.getHtml();
                                                    console.log('Final HTML:', finalHtml);
                                                    
                                                    if (finalHtml.includes('href=')) {
                                                        console.log('✓ Link başarıyla model\'e kaydedildi');
                                                        if (window.StudioNotification) {
                                                            window.StudioNotification.success('Link başarıyla eklendi');
                                                        }
                                                    } else {
                                                        console.log('✗ Link hala model\'de yok');
                                                        if (window.StudioNotification) {
                                                            window.StudioNotification.error('Link eklenemedi');
                                                        }
                                                    }
                                                }
                                            } catch (error) {
                                                console.error('Link ekleme işlemi hatası:', error);
                                                if (window.StudioNotification) {
                                                    window.StudioNotification.error('Link eklenirken bir hata oluştu: ' + error.message);
                                                }
                                            }
                                        },
                                        editor // Editor'ü parametre olarak geç
                                    );
                                    
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
            currentRTE = null;
            currentEditedComponent = null;
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