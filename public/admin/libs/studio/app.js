/**
 * Studio Editor Uygulama Başlatıcı
 * Tüm modülleri yükler ve uygulamayı başlatır
 */
document.addEventListener('DOMContentLoaded', function() {
    // Editor element'ini bul
    const editorElement = document.getElementById('gjs');
    if (!editorElement) {
        console.log('Studio Editor başlatılamıyor: #gjs elementi bulunamadı!');
        return;
    }
    
    // Konfigürasyon oluştur
    const config = {
        elementId: 'gjs',
        module: editorElement.getAttribute('data-module-type') || 'page',
        moduleId: parseInt(editorElement.getAttribute('data-module-id') || '0'),
        content: document.getElementById('html-content') ? document.getElementById('html-content').value : '',
        css: document.getElementById('css-content') ? document.getElementById('css-content').value : '',
    };
    
    // Sadece bir kez başlatıldığından emin ol
    if (window._studioEditorInitialized) {
        console.warn('Studio Editor zaten başlatılmış, tekrar başlatma işlemi atlanıyor.');
        return;
    }
    window._studioEditorInitialized = true;

    if (!config || !config.moduleId || config.moduleId <= 0) {
        console.error('Geçersiz konfigürasyon veya modül ID:', config);
        window._studioEditorInitialized = false; // Hata durumunda bayrağı geri al
        return;
    }
    
    // Global değişkende sakla
    window.studioEditorConfig = config;
    
    // Editor başlat
    if (typeof window.initStudioEditor === 'function') {
        try {
            const editor = window.initStudioEditor(config);
            
            // Editor yükleme olayını dinle
            editor.on('load', function() {
                console.log('Editor yükleme olayı tetiklendi');
                
                // Blokları kaydet
                if (window.StudioBlocks && typeof window.StudioBlocks.registerBlocks === 'function') {
                    window.StudioBlocks.registerBlocks(editor);
                    
                    // Kategorileri DOM'a ekle
                    setTimeout(function() {
                        updateBlocksInCategories(editor);
                    }, 500);
                }
                
                // Butonları ayarla
                if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
                    window.StudioActions.setupActions(editor, config);
                }
                
                // Panel sekmelerini ayarla
                setupTabs();
            });
            
            // Global erişim için kaydet
            window.studioEditor = editor;
        } catch (error) {
            console.error('Studio Editor başlatılırken hata:', error);
        }
    } else {
        console.error('Studio Editor başlatılamıyor: initStudioEditor fonksiyonu bulunamadı!');
    }
});

/**
 * Sol panel sekmelerini ayarla
 */
function setupTabs() {
    const tabs = document.querySelectorAll(".panel-tab");
    const tabContents = document.querySelectorAll(".panel-tab-content");

    tabs.forEach((tab) => {
        // Eski event listener'ları temizle
        const newTab = tab.cloneNode(true);
        if (tab.parentNode) {
            tab.parentNode.replaceChild(newTab, tab);
        }
        
        newTab.addEventListener("click", function () {
            const tabName = this.getAttribute("data-tab");

            // Aktif tab değiştir
            tabs.forEach((t) => t.classList.remove("active"));
            this.classList.add("active");

            // İçeriği değiştir
            tabContents.forEach((content) => {
                if (content.getAttribute("data-tab-content") === tabName) {
                    content.classList.add("active");
                } else {
                    content.classList.remove("active");
                }
            });
        });
    });
}

/**
 * Editördeki blokları kategori elementlerine ekler
 */
function updateBlocksInCategories(editor) {
    if (!editor) {
        console.error('Editor örneği bulunamadı');
        return;
    }
    
    // Tüm blokları al
    const blocks = editor.BlockManager.getAll();
    
    // Her bir kategori için blokları işle
    const categories = document.querySelectorAll('.block-category');
    
    categories.forEach(category => {
        const categoryId = category.querySelector('.block-category-header')?.textContent?.trim().toLowerCase();
        if (!categoryId) return;
        
        // Kategori adını belirle
        let catName = '';
        if (categoryId.includes('düzen')) catName = 'layout';
        else if (categoryId.includes('içerik')) catName = 'content';
        else if (categoryId.includes('form')) catName = 'form';
        else if (categoryId.includes('medya')) catName = 'media';
        else if (categoryId.includes('widget')) catName = 'widget';
        
        // Kategori içerik alanını temizle
        const blockItems = category.querySelector('.block-items');
        if (blockItems) {
            blockItems.innerHTML = '';
            
            // Bu kategoriye ait blokları ekle
            blocks.filter(block => block.get('category') === catName).forEach(block => {
                const blockEl = document.createElement('div');
                blockEl.className = 'block-item';
                blockEl.setAttribute('data-block-id', block.get('id'));
                
                // İçeriği oluştur
                blockEl.innerHTML = `
                    <div class="block-item-icon">
                        <i class="${block.getAttributes().class || 'fa fa-cube'}"></i>
                    </div>
                    <div class="block-item-label">${block.get('label')}</div>
                `;
                
                // Drag-drop işlevini ekle
                blockEl.setAttribute('draggable', 'true');
                blockEl.addEventListener('dragstart', (e) => {
                    editor.runCommand('select-comp', { event: e });
                    blockEl.classList.add('dragging');
                });
                
                blockEl.addEventListener('dragend', () => {
                    blockEl.classList.remove('dragging');
                });
                
                blockEl.addEventListener('click', () => {
                    editor.BlockManager.add(block.get('id'));
                });
                
                blockItems.appendChild(blockEl);
            });
        }
    });
}