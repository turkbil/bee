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
            return;
        }
        actionsSetup = true;
        
        
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
            
            swVisibility.addEventListener("click", () => {
                try {
                    let currentState = swVisibility.classList.contains('active');
                    
                    
                    const frames = editor.Canvas.getFrames();
                    
                    frames.forEach((frame, index) => {
                        const body = frame.view.getBody();
                        
                        const allElements = body.querySelectorAll('*');
                        
                        if (!currentState) {
                            allElements.forEach(el => {
                                el.style.outline = '1px dashed rgba(170, 170, 170, 0.7)';
                            });
                        } else {
                            allElements.forEach(el => {
                                el.style.outline = '';
                            });
                        }
                    });
                    
                    swVisibility.classList.toggle('active');
                    
                    return swVisibility.classList.contains('active');
                } catch (error) {
                    console.error('Bileşen sınırlarını gösterme/gizleme hatası:', error);
                    return false;
                }
            });
        } else {
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
        
        // RTE toolbar customization
        editor.on('rte:enable', (rte, view) => {
            currentRTE = rte;
            currentEditedComponent = view.model;
            
            const frameEl = editor.Canvas.getFrameEl();
            if (frameEl) {
                frameWindow = frameEl.contentWindow;
                frameDocument = frameEl.contentDocument || frameWindow.document;
            }
            
            // Wrap for style butonunu gizle
            setTimeout(() => {
                const wrapButtons = document.querySelectorAll('.gjs-rte-action[title="Wrap for style"]');
                wrapButtons.forEach(btn => {
                    btn.style.display = 'none';
                    btn.remove();
                });
                
                // iframe içindeki wrap butonlarını da gizle
                if (frameDocument) {
                    const frameWrapButtons = frameDocument.querySelectorAll('.gjs-rte-action[title="Wrap for style"]');
                    frameWrapButtons.forEach(btn => {
                        btn.style.display = 'none';
                        btn.remove();
                    });
                }
            }, 100);
            
            const checkForLinkButtons = (attempts = 0) => {
                if (attempts > 15) {
                    return;
                }
                
                setTimeout(() => {
                    try {
                        let linkButtons = document.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                        let allRteElements = document.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                        
                        
                        if (frameEl && frameDocument) {
                            const iframeLinkButtons = frameDocument.querySelectorAll('.gjs-rte-action[title="Link"], .gjs-rte-action');
                            const iframeRteElements = frameDocument.querySelectorAll('.gjs-rte-action, .gjs-rte-toolbar, [class*="gjs-rte"]');
                            
                            
                            linkButtons = [...linkButtons, ...iframeLinkButtons];
                        }
                        
                        if (linkButtons.length === 0) {
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
                        
                        
                        if (realLinkButtons.length === 0) {
                            checkForLinkButtons(attempts + 1);
                            return;
                        }
                        
                        realLinkButtons.forEach((linkButton, index) => {
                            if (!linkButton.hasAttribute('data-custom-link-setup')) {
                                linkButton.setAttribute('data-custom-link-setup', 'true');
                                
                                const handleLinkClick = (e) => {
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
                                    }
                                    
                                    window.StudioModal.showLinkModal(
                                        selectedText, 
                                        currentUrl, 
                                        currentTarget, 
                                        currentTitle, 
                                        (linkData) => {
                                            if (!linkData.url) return;
                                            
                                            
                                            try {
                                                if (frameDocument && selectionInfo) {
                                                    
                                                    const newSelection = frameWindow.getSelection();
                                                    newSelection.removeAllRanges();
                                                    newSelection.addRange(selectionInfo.range);
                                                    
                                                    const linkElement = frameDocument.createElement('a');
                                                    linkElement.href = linkData.url;
                                                    
                                                    if (linkData.target && linkData.target !== 'false') {
                                                        linkElement.target = linkData.target;
                                                    }
                                                    
                                                    if (linkData.title) {
                                                        linkElement.title = linkData.title;
                                                    }
                                                    
                                                    linkElement.textContent = selectedText;
                                                    
                                                    const range = selectionInfo.range;
                                                    range.deleteContents();
                                                    range.insertNode(linkElement);
                                                    
                                                    newSelection.removeAllRanges();
                                                    
                                                    
                                                    if (currentRTE && typeof currentRTE.disable === 'function') {
                                                        currentRTE.disable();
                                                    } else {
                                                        editor.RichTextEditor.disable();
                                                    }
                                                    
                                                    const bodyHtml = frameDocument.body.innerHTML;
                                                    
                                                    editor.setComponents(bodyHtml);
                                                    
                                                    
                                                    const finalHtml = editor.getHtml();
                                                    
                                                    if (finalHtml.includes('href=')) {
                                                        if (window.StudioNotification) {
                                                            window.StudioNotification.success('Link başarıyla eklendi');
                                                        }
                                                    } else {
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
                                        editor
                                    );
                                    
                                    return false;
                                };
                                
                                linkButton.addEventListener('click', handleLinkClick, true);
                                linkButton.addEventListener('mousedown', handleLinkClick, true);
                                linkButton.addEventListener('touchstart', handleLinkClick, true);
                                
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