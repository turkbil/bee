/**
 * Studio Quick Editor - Rich Text Editor Modülü
 * Canvas ile eşzamanlı çalışan mini rich text editor
 */

window.StudioQuickEditor = (function() {
    let editorInstance = null;
    let currentComponent = null;
    let isUpdatingFromCanvas = false;
    let isUpdatingFromEditor = false;
    let syncTimeout = null;
    
    /**
     * Quick Editor'ı başlat
     * @param {Object} editor - GrapesJS editor örneği
     */
    function initQuickEditor(editor) {
        editorInstance = editor;
        
        // Editor event'lerini dinle
        setupEditorEvents(editor);
        
        // Quick editor arayüzünü oluştur
        createQuickEditorUI();
        
        console.log('Studio Quick Editor başlatıldı');
    }
    
    /**
     * GrapesJS editor event'lerini ayarla
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupEditorEvents(editor) {
        // Component seçildiğinde
        editor.on('component:selected', function(component) {
            console.log('QE: Component seçildi:', component.get('type'), component.get('tagName'));
            handleComponentSelection(component);
        });
        
        // Component seçimi iptal edildiğinde
        editor.on('component:deselected', function() {
            console.log('QE: Component seçimi iptal edildi');
            hideQuickEditor();
        });
        
        // Component içeriği değiştiğinde
        editor.on('component:update:content', function(component) {
            console.log('QE: Component içeriği güncellendi:', component.get('content'));
            if (component === currentComponent && !isUpdatingFromEditor) {
                console.log('QE: Canvas\'tan Quick Editor güncellenecek');
                updateQuickEditorFromCanvas(component);
            }
        });
        
        // Canvas tıklama eventi
        editor.on('canvas:click', function() {
            setTimeout(() => {
                const selected = editor.getSelected();
                if (selected && isTextComponent(selected)) {
                    console.log('QE: Canvas tıklama - text component seçildi');
                    handleComponentSelection(selected);
                } else {
                    console.log('QE: Canvas tıklama - text component değil veya seçim yok');
                    hideQuickEditor();
                }
            }, 50);
        });
        
        // RTE olaylarını dinle
        editor.on('rte:enable', function(rte, view) {
            console.log('QE: RTE aktif edildi - component:', view.model.get('type'));
        });
        
        editor.on('rte:disable', function() {
            console.log('QE: RTE devre dışı bırakıldı');
        });
    }
    
    /**
     * Quick editor arayüzünü oluştur
     */
    function createQuickEditorUI() {
        const container = document.getElementById('text-editor-container');
        if (!container) {
            console.error('Text editor container bulunamadı');
            return;
        }
        
        container.innerHTML = `
            <div class="quick-editor-container" id="quick-editor" style="display: none;">
                <div class="quick-editor-toolbar">
                    <div class="toolbar-group">
                        <button class="quick-editor-btn" id="qe-bold" title="Kalın (Ctrl+B)" data-command="bold">
                            <i class="fas fa-bold"></i>
                        </button>
                        <button class="quick-editor-btn" id="qe-italic" title="İtalik (Ctrl+I)" data-command="italic">
                            <i class="fas fa-italic"></i>
                        </button>
                        <button class="quick-editor-btn" id="qe-underline" title="Altı Çizili (Ctrl+U)" data-command="underline">
                            <i class="fas fa-underline"></i>
                        </button>
                    </div>
                    <div class="toolbar-divider"></div>
                    <div class="toolbar-group">
                        <button class="quick-editor-btn" id="qe-link" title="Link Ekle (Ctrl+K)" data-command="link">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
                <div class="quick-editor-content">
                    <textarea class="quick-editor-textarea" id="qe-textarea" placeholder="Metin içeriğini düzenleyin..."></textarea>
                </div>
                <div class="quick-editor-status">
                    <div class="status-left">
                        <span class="quick-editor-char-count">0 karakter</span>
                        <span>•</span>
                        <span id="qe-element-type">-</span>
                    </div>
                    <div class="status-right">
                        <span class="quick-editor-sync-indicator" id="qe-sync-indicator"></span>
                        <span>Senkron</span>
                    </div>
                </div>
            </div>
        `;
        
        // Event listener'ları ekle
        setupQuickEditorEvents();
    }
    
    /**
     * Quick editor event'lerini ayarla
     */
    function setupQuickEditorEvents() {
        const textarea = document.getElementById('qe-textarea');
        const boldBtn = document.getElementById('qe-bold');
        const italicBtn = document.getElementById('qe-italic');
        const underlineBtn = document.getElementById('qe-underline');
        const linkBtn = document.getElementById('qe-link');
        
        if (!textarea) return;
        
        // Textarea input eventi
        textarea.addEventListener('input', function() {
            console.log('QE: Textarea input eventi tetiklendi');
            updateCharCount();
            
            if (!isUpdatingFromCanvas) {
                console.log('QE: Canvas güncellemesi başlatılıyor...');
                isUpdatingFromEditor = true;
                updateCanvasFromQuickEditor();
                
                clearTimeout(syncTimeout);
                syncTimeout = setTimeout(() => {
                    isUpdatingFromEditor = false;
                    setSyncStatus('synced');
                    console.log('QE: Güncelleme kilidini açıldı');
                }, 500);
                
                setSyncStatus('syncing');
            }
        });
        
        // Textarea select eventi
        textarea.addEventListener('select', function() {
            updateToolbarState();
        });
        
        // Textarea keyup eventi
        textarea.addEventListener('keyup', function() {
            updateToolbarState();
        });
        
        // Toolbar butonları
        if (boldBtn) {
            boldBtn.addEventListener('click', () => executeCommand('bold'));
        }
        
        if (italicBtn) {
            italicBtn.addEventListener('click', () => executeCommand('italic'));
        }
        
        if (underlineBtn) {
            underlineBtn.addEventListener('click', () => executeCommand('underline'));
        }
        
        if (linkBtn) {
            linkBtn.addEventListener('click', () => executeCommand('link'));
        }
        
        // Keyboard shortcuts
        textarea.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch(e.key.toLowerCase()) {
                    case 'b':
                        e.preventDefault();
                        executeCommand('bold');
                        break;
                    case 'i':
                        e.preventDefault();
                        executeCommand('italic');
                        break;
                    case 'u':
                        e.preventDefault();
                        executeCommand('underline');
                        break;
                    case 'k':
                        e.preventDefault();
                        executeCommand('link');
                        break;
                }
            }
        });
    }
    
    /**
     * Component seçimini işle
     * @param {Object} component - Seçilen component
     */
    function handleComponentSelection(component) {
        if (!isTextComponent(component)) {
            console.log('QE: Seçilen component text değil, quick editor gizleniyor');
            hideQuickEditor();
            return;
        }
        
        console.log('QE: Text component seçildi, quick editor gösteriliyor');
        currentComponent = component;
        showQuickEditor(component);
        updateQuickEditorFromCanvas(component);
    }
    
    /**
     * Component'in text komponenti olup olmadığını kontrol et
     * @param {Object} component - Kontrol edilecek component
     * @returns {boolean}
     */
    function isTextComponent(component) {
        if (!component) return false;
        
        const type = component.get('type');
        const tagName = component.get('tagName');
        const view = component.view;
        
        console.log('QE: Component kontrolü - type:', type, 'tagName:', tagName);
        
        // Direkt text tiplerini kontrol et
        if (type === 'text' || type === 'textnode') {
            console.log('QE: Text komponenti tespit edildi (type)');
            return true;
        }
        
        // Element view kontrolü
        if (view && view.el) {
            const element = view.el;
            const children = element.children;
            const textContent = getCleanTextContent(element);
            
            // Alt elementleri varsa container olarak kabul et
            if (children.length > 0) {
                let hasOnlyTextNodes = true;
                for (let child of children) {
                    if (child.nodeType !== Node.TEXT_NODE && 
                        !['A', 'STRONG', 'EM', 'B', 'I', 'U', 'SPAN'].includes(child.tagName)) {
                        hasOnlyTextNodes = false;
                        break;
                    }
                }
                
                if (hasOnlyTextNodes && textContent.trim()) {
                    console.log('QE: Text komponenti tespit edildi (inline elements)');
                    return true;
                }
                
                return false;
            }
            
            // Alt element yoksa ve text content varsa text component
            if (textContent.trim()) {
                const textTags = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'a', 'li', 'td', 'th', 'label', 'button', 'div'];
                if (tagName && textTags.includes(tagName.toLowerCase())) {
                    console.log('QE: Text komponenti tespit edildi (text tag)');
                    return true;
                }
            }
        }
        
        console.log('QE: Text komponenti değil');
        return false;
    }
    
    /**
     * Element'ten temiz text content al
     * @param {HTMLElement} element - DOM element
     * @returns {string} Temizlenmiş text
     */
    function getCleanTextContent(element) {
        if (!element) return '';
        return element.textContent || element.innerText || '';
    }
    
    /**
     * Quick editor'ı göster
     * @param {Object} component - Seçilen component
     */
    function showQuickEditor(component) {
        const container = document.getElementById('quick-editor');
        if (!container) return;
        
        console.log('QE: Quick editor gösteriliyor');
        container.style.display = 'block';
        
        // Element tipini göster
        const elementType = document.getElementById('qe-element-type');
        if (elementType) {
            const tagName = component.get('tagName') || 'div';
            elementType.textContent = tagName.toUpperCase();
        }
        
        // Yapılandır sekmesini aktifleştir
        if (window.StudioTabs && window.StudioTabs.activateTab) {
            window.StudioTabs.activateTab('configure', 'right');
        }
        
        setSyncStatus('synced');
    }
    
    /**
     * Quick editor'ı gizle
     */
    function hideQuickEditor() {
        const container = document.getElementById('quick-editor');
        if (container) {
            console.log('QE: Quick editor gizleniyor');
            container.style.display = 'none';
        }
        
        currentComponent = null;
        clearTimeout(syncTimeout);
    }
    
    /**
     * Canvas'tan quick editor'ı güncelle
     * @param {Object} component - Güncellenecek component
     */
    function updateQuickEditorFromCanvas(component) {
        if (isUpdatingFromEditor) {
            console.log('QE: Editor güncellemesi devam ediyor, canvas güncellemesi atlanıyor');
            return;
        }
        
        console.log('QE: Canvas\'tan quick editor güncelleniyor...');
        isUpdatingFromCanvas = true;
        
        const textarea = document.getElementById('qe-textarea');
        if (!textarea) return;
        
        // Component içeriğini al
        let content = component.get('content') || '';
        console.log('QE: Component içeriği alındı:', content);
        
        // HTML formatını textarea için düzenle
        if (content) {
            // HTML'i görsel format için düzenle
            content = content
                .replace(/<strong>/g, '**')
                .replace(/<\/strong>/g, '**')
                .replace(/<b>/g, '**')
                .replace(/<\/b>/g, '**')
                .replace(/<em>/g, '*')
                .replace(/<\/em>/g, '*')
                .replace(/<i>/g, '*')
                .replace(/<\/i>/g, '*')
                .replace(/<u>/g, '_')
                .replace(/<\/u>/g, '_')
                .replace(/<a\s+href="([^"]*)"[^>]*>/g, '[$1](')
                .replace(/<\/a>/g, ')');
            
            // Diğer HTML etiketlerini temizle
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            content = tempDiv.textContent || tempDiv.innerText || '';
        }
        
        // Eğer content boşsa, element text content'ini al
        if (!content || content.trim() === '') {
            const view = component.view;
            if (view && view.el) {
                content = getCleanTextContent(view.el);
            }
        }
        
        console.log('QE: Textarea\'ya yazılacak içerik:', content);
        textarea.value = content;
        updateCharCount();
        updateToolbarState();
        
        setTimeout(() => {
            isUpdatingFromCanvas = false;
            console.log('QE: Canvas güncelleme kilidi açıldı');
        }, 100);
    }
    
    /**
     * Quick editor'dan canvas'ı güncelle - Canvas RTE sistemi ile tam uyumlu
     */
    function updateCanvasFromQuickEditor() {
        if (!currentComponent || isUpdatingFromCanvas) {
            console.log('QE: Canvas güncellemesi iptal edildi - component yok veya canvas güncellemesi devam ediyor');
            return;
        }
        
        const textarea = document.getElementById('qe-textarea');
        if (!textarea) {
            console.log('QE: Textarea bulunamadı');
            return;
        }
        
        let content = textarea.value;
        console.log('QE: Canvas\'a gönderilecek textarea içeriği:', content);
        
        // Markdown benzeri formatları HTML'e çevir
        content = content
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/_(.*?)_/g, '<u>$1</u>')
            .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2">$1</a>');
        
        // Newline'ları <br> ile değiştir
        content = content.replace(/\n/g, '<br>');
        
        console.log('QE: HTML formatına çevrilmiş içerik:', content);
        
        // Canvas RTE sistemiyle tam uyumlu güncelleme - studio-actions.js sistemiyle aynı
        try {
            console.log('QE: Canvas güncelleme işlemi başlıyor...');
            
            // 1. Component'i seç
            editorInstance.select(currentComponent);
            console.log('QE: Component seçildi');
            
            // 2. Frame bilgilerini al
            const frameEl = editorInstance.Canvas.getFrameEl();
            if (!frameEl) {
                console.error('QE: Canvas frame bulunamadı');
                return;
            }
            
            const frameWindow = frameEl.contentWindow;
            const frameDocument = frameEl.contentDocument || frameWindow.document;
            console.log('QE: Frame bilgileri alındı');
            
            // 3. RTE'yi element üzerinde aktifleştir
            console.log('QE: RTE aktifleştiriliyor...');
            editorInstance.RichTextEditor.enable(currentComponent.view.el);
            
            // 4. Element içeriğini doğrudan güncelle
            const element = currentComponent.view.el;
            if (element) {
                console.log('QE: Element içeriği güncelleniyor...');
                element.innerHTML = content;
                
                // 5. Component model'ini güncelle
                currentComponent.set('content', content);
                console.log('QE: Component model güncellendi');
            }
            
            // 6. RTE'yi kapat
            console.log('QE: RTE devre dışı bırakılıyor...');
            editorInstance.RichTextEditor.disable();
            
            // 7. KRITIK: Canvas sisteminde olduğu gibi DOM'dan HTML al ve yeniden parse et
            setTimeout(() => {
                console.log('QE: DOM\'dan HTML alınıyor ve yeniden parse ediliyor...');
                const bodyHtml = frameDocument.body.innerHTML;
                console.log('QE: Frame body HTML:', bodyHtml.substring(0, 200) + '...');
                
                // 8. Editörü tamamen yeniden parse et (Canvas sistemi ile aynı)
                editorInstance.setComponents(bodyHtml);
                console.log('QE: Editor yeniden parse edildi');
                
                // 9. Doğrulama - HTML çıktısını kontrol et
                setTimeout(() => {
                    const finalHtml = editorInstance.getHtml();
                    console.log('QE: Final HTML:', finalHtml.substring(0, 200) + '...');
                    
                    if (finalHtml.includes('href=')) {
                        console.log('QE: ✓ Link başarıyla model\'e kaydedildi');
                        if (window.StudioNotification) {
                            window.StudioNotification.success('İçerik başarıyla güncellendi');
                        }
                    } else if (content.includes('[') && content.includes('](')) {
                        console.log('QE: ✗ Link model\'e kaydedilmedi');
                        if (window.StudioNotification) {
                            window.StudioNotification.warning('Link kaydedilemedi, tekrar deneyin');
                        }
                    }
                }, 100);
            }, 50);
            
        } catch (error) {
            console.error('QE: Canvas güncelleme hatası:', error);
            if (window.StudioNotification) {
                window.StudioNotification.error('Canvas güncellenirken hata: ' + error.message);
            }
        }
        
        // Hidden input'ı da güncelle
        setTimeout(() => {
            console.log('QE: Hidden input güncelleniyor...');
            const htmlEl = document.getElementById('html-content');
            if (htmlEl && editorInstance) {
                const rawHtml = editorInstance.getHtml();
                const cleanedHtml = window.StudioSave ? window.StudioSave.cleanHtml(rawHtml) : rawHtml;
                htmlEl.value = cleanedHtml;
                console.log('QE: Hidden input güncellendi, uzunluk:', cleanedHtml.length);
            }
        }, 300);
    }
    
    /**
     * Toolbar komutunu çalıştır
     * @param {string} command - Çalıştırılacak komut
     */
    function executeCommand(command) {
        const textarea = document.getElementById('qe-textarea');
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        
        console.log('QE: Komut çalıştırılıyor:', command, 'Seçili metin:', selectedText);
        
        if (!selectedText && command !== 'link') {
            if (window.StudioNotification) {
                window.StudioNotification.warning('Lütfen önce bir metin seçin');
            }
            return;
        }
        
        let newText = '';
        let newStart = start;
        let newEnd = end;
        
        switch(command) {
            case 'bold':
                if (selectedText.startsWith('**') && selectedText.endsWith('**')) {
                    // Bold'u kaldır
                    newText = selectedText.slice(2, -2);
                    newEnd = start + newText.length;
                } else {
                    // Bold ekle
                    newText = `**${selectedText}**`;
                    newStart = start + 2;
                    newEnd = end + 2;
                }
                break;
                
            case 'italic':
                if (selectedText.startsWith('*') && selectedText.endsWith('*') && 
                    !selectedText.startsWith('**')) {
                    // Italic'i kaldır
                    newText = selectedText.slice(1, -1);
                    newEnd = start + newText.length;
                } else {
                    // Italic ekle
                    newText = `*${selectedText}*`;
                    newStart = start + 1;
                    newEnd = end + 1;
                }
                break;
                
            case 'underline':
                if (selectedText.startsWith('_') && selectedText.endsWith('_')) {
                    // Underline'ı kaldır
                    newText = selectedText.slice(1, -1);
                    newEnd = start + newText.length;
                } else {
                    // Underline ekle
                    newText = `_${selectedText}_`;
                    newStart = start + 1;
                    newEnd = end + 1;
                }
                break;
                
            case 'link':
                executeLink(selectedText, start, end);
                return;
        }
        
        // Metni güncelle
        const beforeText = textarea.value.substring(0, start);
        const afterText = textarea.value.substring(end);
        textarea.value = beforeText + newText + afterText;
        
        // Selection'ı güncelle
        textarea.setSelectionRange(newStart, newEnd);
        textarea.focus();
        
        // Canvas'ı güncelle
        updateCanvasFromQuickEditor();
        updateToolbarState();
    }
    
    /**
     * Link komutunu çalıştır - Canvas sistemi ile tam aynı modal
     * @param {string} selectedText - Seçili metin
     * @param {number} start - Başlangıç pozisyonu
     * @param {number} end - Bitiş pozisyonu
     */
    function executeLink(selectedText, start, end) {
        if (!selectedText || selectedText.trim() === '') {
            if (window.StudioNotification) {
                window.StudioNotification.warning('Lütfen önce link yapmak istediğiniz metni seçin');
            }
            return;
        }
        
        console.log('QE: Link ekleme işlemi başlıyor - Seçili metin:', selectedText);
        
        // Mevcut link kontrolü
        let currentUrl = '';
        let currentTitle = '';
        const linkMatch = selectedText.match(/\[(.*?)\]\((.*?)\)/);
        if (linkMatch) {
            currentUrl = linkMatch[2];
            selectedText = linkMatch[1];
            console.log('QE: Mevcut link tespit edildi:', currentUrl);
        }
        
        // Canvas ile tam aynı modal sistemi kullan
        if (window.StudioModal && window.StudioModal.showLinkModal && editorInstance) {
            console.log('QE: Link modal açılıyor...');
            window.StudioModal.showLinkModal(
                selectedText,
                currentUrl,
                '_blank',
                currentTitle,
                function(linkData) {
                    console.log('QE: Link modal\'dan veri alındı:', linkData);
                    
                    const textarea = document.getElementById('qe-textarea');
                    if (!textarea) return;
                    
                    // Textarea'da link formatını uygula
                    const newText = `[${selectedText}](${linkData.url})`;
                    const beforeText = textarea.value.substring(0, start);
                    const afterText = textarea.value.substring(end);
                    
                    textarea.value = beforeText + newText + afterText;
                    console.log('QE: Textarea güncellendi, yeni içerik:', textarea.value);
                    
                    // Selection'ı güncelle
                    const newEnd = start + newText.length;
                    textarea.setSelectionRange(start, newEnd);
                    textarea.focus();
                    
                    // Canvas'ı RTE sistemi ile güncelle - TAM UYUMLU
                    console.log('QE: Canvas link güncellemesi başlıyor...');
                    setTimeout(() => {
                        try {
                            if (!currentComponent) {
                                console.error('QE: Current component bulunamadı');
                                return;
                            }
                            
                            // Canvas RTE sisteminin birebir aynısı
                            console.log('QE: Component seçiliyor...');
                            editorInstance.select(currentComponent);
                            
                            console.log('QE: RTE etkinleştiriliyor...');
                            editorInstance.RichTextEditor.enable(currentComponent.view.el);
                            
                            // Textarea'dan tüm içeriği al ve HTML'e çevir
                            let fullContent = textarea.value;
                            console.log('QE: Tam içerik alındı:', fullContent);
                            
                            fullContent = fullContent
                                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                                .replace(/_(.*?)_/g, '<u>$1</u>')
                                .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" data-gjs-type="link">$1</a>')
                                .replace(/\n/g, '<br>');
                            
                            console.log('QE: HTML formatına çevrildi:', fullContent);
                            
                            // Component içeriğini güncelle
                            console.log('QE: Component view element güncelliyor...');
                            currentComponent.view.el.innerHTML = fullContent;
                            currentComponent.set('content', fullContent);
                            
                            console.log('QE: RTE devre dışı bırakılıyor...');
                            editorInstance.RichTextEditor.disable();
                            
                            // Frame'den HTML'i al ve yeniden parse et - Canvas sistemi ile aynı
                            setTimeout(() => {
                                console.log('QE: Frame\'den HTML alınıyor...');
                                const frameEl = editorInstance.Canvas.getFrameEl();
                                if (frameEl) {
                                    const frameDocument = frameEl.contentDocument || frameEl.contentWindow.document;
                                    const bodyHtml = frameDocument.body.innerHTML;
                                    
                                    console.log('QE: Frame body HTML alındı, parse ediliyor...');
                                    console.log('QE: Body HTML (ilk 200 karakter):', bodyHtml.substring(0, 200));
                                    
                                    // Canvas'taki link sistemi ile aynı - dökümanasyondaki gibi
                                    editorInstance.setComponents(bodyHtml);
                                    
                                    console.log('QE: Editor yeniden parse edildi');
                                    
                                    // Doğrulama
                                    setTimeout(() => {
                                        const finalHtml = editorInstance.getHtml();
                                        console.log('QE: Final HTML doğrulaması:', finalHtml.substring(0, 200));
                                        
                                        if (finalHtml.includes('href=')) {
                                            console.log('QE: ✓ Link başarıyla model\'e kaydedildi');
                                            if (window.StudioNotification) {
                                                window.StudioNotification.success('Link başarıyla eklendi');
                                            }
                                        } else {
                                            console.log('QE: ✗ Link model\'e kaydedilmedi');
                                            if (window.StudioNotification) {
                                                window.StudioNotification.error('Link kaydedilemedi');
                                            }
                                        }
                                    }, 100);
                                }
                            }, 100);
                            
                        } catch (error) {
                            console.error('QE: Link ekleme hatası:', error);
                            if (window.StudioNotification) {
                                window.StudioNotification.error('Link eklenirken hata: ' + error.message);
                            }
                        }
                    }, 50);
                    
                    updateToolbarState();
                },
                editorInstance
            );
        } else {
            console.error('QE: StudioModal.showLinkModal bulunamadı');
        }
    }
    
    /**
     * Toolbar durumunu güncelle
     */
    function updateToolbarState() {
        const textarea = document.getElementById('qe-textarea');
        if (!textarea) return;
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);
        
        // Butonların aktif durumunu güncelle
        const boldBtn = document.getElementById('qe-bold');
        const italicBtn = document.getElementById('qe-italic');
        const underlineBtn = document.getElementById('qe-underline');
        const linkBtn = document.getElementById('qe-link');
        
        if (boldBtn) {
            boldBtn.classList.toggle('active', 
                selectedText.startsWith('**') && selectedText.endsWith('**'));
        }
        
        if (italicBtn) {
            italicBtn.classList.toggle('active', 
                selectedText.startsWith('*') && selectedText.endsWith('*') && 
                !selectedText.startsWith('**'));
        }
        
        if (underlineBtn) {
            underlineBtn.classList.toggle('active', 
                selectedText.startsWith('_') && selectedText.endsWith('_'));
        }
        
        if (linkBtn) {
            linkBtn.classList.toggle('active', 
                /\[.*?\]\(.*?\)/.test(selectedText));
        }
    }
    
    /**
     * Karakter sayısını güncelle
     */
    function updateCharCount() {
        const textarea = document.getElementById('qe-textarea');
        const charCountEl = document.querySelector('.quick-editor-char-count');
        
        if (textarea && charCountEl) {
            const count = textarea.value.length;
            charCountEl.textContent = `${count} karakter`;
        }
    }
    
    /**
     * Senkronizasyon durumunu ayarla
     * @param {string} status - Durum: 'synced', 'syncing', 'error'
     */
    function setSyncStatus(status) {
        const indicator = document.getElementById('qe-sync-indicator');
        if (!indicator) return;
        
        indicator.className = 'quick-editor-sync-indicator';
        indicator.classList.add(status);
    }
    
    return {
        initQuickEditor: initQuickEditor,
        showQuickEditor: showQuickEditor,
        hideQuickEditor: hideQuickEditor,
        isTextComponent: isTextComponent,
        executeCommand: executeCommand
    };
})();