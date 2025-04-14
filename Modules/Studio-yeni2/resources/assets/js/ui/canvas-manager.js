/**
 * Studio Editor - Canvas Manager
 * Çalışma alanı (canvas) işlevselliğini yönetir
 */
const StudioCanvasManager = (function() {
    /**
     * Çalışma alanını kurar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCanvas(editor) {
        if (!editor) {
            console.error('Editor örneği geçersiz');
            return;
        }
        
        console.log('Çalışma alanı kuruluyor...');
        
        handleDropEvents(editor);
        setupComponentSelection(editor);
        setupCanvasEvents(editor);
    }
    
    /**
     * Sürükle-bırak olaylarını yönetir
     * @param {Object} editor - GrapesJS editor örneği
     */
    function handleDropEvents(editor) {
        // Canvas elementini al
        const canvasElement = document.querySelector('.editor-canvas');
        if (!canvasElement) {
            console.error('Canvas elementi bulunamadı.');
            return;
        }
        
        // Sürükleme olaylarını dinle
        canvasElement.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drop-target');
        });
        
        canvasElement.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drop-target');
        });
        
        canvasElement.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drop-target');
            
            // GrapesJS kendi drop olayını yönetir, burada ekstra işlem yapmaya gerek yok
            console.log('Drop olayı GrapesJS tarafından yönetiliyor.');
        });
        
        console.log('Canvas sürükle-bırak olayları kuruldu.');
    }
    
    /**
     * Bileşen seçimi işlevselliğini kurar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupComponentSelection(editor) {
        // Seçili bileşen değiştiğinde
        editor.on('component:selected', function(component) {
            if (!component) return;
            
            // Stil panelini seçili bileşene odakla
            const styleTab = document.querySelector('.panel-tab[data-tab="styles"]');
            if (styleTab) {
                styleTab.click();
            }
            
            console.log('Bileşen seçildi:', component.getName() || component.get('tagName'));
        });
        
        // Tüm seçim kaldırıldığında
        editor.on('component:deselected', function() {
            console.log('Bileşen seçimi kaldırıldı.');
        });
        
        console.log('Bileşen seçimi işlevselliği kuruldu.');
    }
    
    /**
     * Canvas olaylarını kurar
     * @param {Object} editor - GrapesJS editor örneği
     */
    function setupCanvasEvents(editor) {
        // Çift tıklama - içeriği düzenleme
        editor.on('canvas:dblclick', function(event) {
            console.log('Canvas çift tıklama algılandı:', event);
        });
        
        // Fare hareketi - hover efektleri
        editor.on('component:mouseover', function(component) {
            if (!component) return;
            
            // Hover etrafında stil
            const el = component.getEl();
            if (el) {
                el.classList.add('gjs-hovered');
            }
        });
        
        editor.on('component:mouseout', function(component) {
            if (!component) return;
            
            // Hover stilini kaldır
            const el = component.getEl();
            if (el) {
                el.classList.remove('gjs-hovered');
            }
        });
        
        console.log('Canvas olayları kuruldu.');
    }
    
    /**
     * Canvas boyutunu ayarlar
     * @param {Object} editor - GrapesJS editor örneği
     * @param {string} size - Boyut ('desktop', 'tablet', 'mobile')
     */
    function setCanvasSize(editor, size) {
        if (!editor) return;
        
        switch (size) {
            case 'desktop':
                editor.setDevice('Masaüstü');
                break;
            case 'tablet':
                editor.setDevice('Tablet');
                break;
            case 'mobile':
                editor.setDevice('Mobil');
                break;
            default:
                editor.setDevice('Masaüstü');
        }
        
        console.log(`Canvas boyutu "${size}" olarak ayarlandı.`);
    }
    
    // Dışa aktarılan API
    return {
        setupCanvas,
        handleDropEvents,
        setupComponentSelection,
        setupCanvasEvents,
        setCanvasSize
    };
})();

// Global olarak kullanılabilir yap
window.StudioCanvasManager = StudioCanvasManager;