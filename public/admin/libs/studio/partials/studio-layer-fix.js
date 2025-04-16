/**
 * Studio Layer Fix - Katmanlar paneli iyileştirmeleri
 */

(function() {
    // DOMContentLoaded olayını bekle
    document.addEventListener('DOMContentLoaded', initLayerFixes);
    
    // Editor yüklendiğinde de çalıştır
    document.addEventListener('editor:loaded', initLayerFixes);
    
    /**
     * Katmanlar paneli iyileştirmelerini başlat
     */
    function initLayerFixes() {
        // İlk iyileştirmeleri yap
        setTimeout(enhanceLayerPanel, 1000);
        
        // Her 2 saniyede bir kontrol et (dinamik güncellemeler için)
        setInterval(enhanceLayerPanel, 2000);
        
        // Editör olaylarını dinle
        if (window.studioEditor) {
            window.studioEditor.on('component:add component:remove component:update', function() {
                setTimeout(enhanceLayerPanel, 500);
            });
        }
    }
    
    /**
     * Katmanlar panelini iyileştir
     */
    function enhanceLayerPanel() {
        const layerContainer = document.getElementById('layers-container');
        if (!layerContainer) return;
        
        // Tüm katmanlar
        const allLayers = layerContainer.querySelectorAll('.gjs-layer');
        
        allLayers.forEach(layer => {
            // Katman ikonları ve görünürlük kontrolü
            const titleContainer = layer.querySelector('.gjs-layer-title-c');
            if (!titleContainer) return;
            
            // Önceden işaretlenmemişse işaretle
            if (!layer.hasAttribute('data-enhanced')) {
                // Caret ikonu ekle
                const hasChildren = layer.querySelector('.gjs-layer-children');
                
                if (hasChildren && hasChildren.children.length > 0) {
                    // Mevcut caret kontrolü
                    let caret = titleContainer.querySelector('.gjs-layer-caret');
                    
                    if (!caret) {
                        caret = document.createElement('span');
                        caret.className = 'gjs-layer-caret';
                        titleContainer.insertBefore(caret, titleContainer.firstChild);
                    }
                    
                    // Katman açılıp kapanma durumunu ayarla
                    if (layer.classList.contains('gjs-open')) {
                        hasChildren.style.display = 'block';
                    } else {
                        hasChildren.style.display = 'none';
                    }
                    
                    // Tıklama olayı ekle
                    titleContainer.addEventListener('click', function(e) {
                        // Görünürlük butonuna tıklandıysa işleme
                        if (e.target.closest('.gjs-layer-vis')) return;
                        
                        // Toggle açma/kapama
                        layer.classList.toggle('gjs-open');
                        
                        if (layer.classList.contains('gjs-open')) {
                            hasChildren.style.display = 'block';
                        } else {
                            hasChildren.style.display = 'none';
                        }
                    });
                }
                
                // İşaretleme
                layer.setAttribute('data-enhanced', 'true');
            }
            
            // Body ve üst seviye elementleri özel stille göster
            if (layer.parentNode === layerContainer) {
                layer.classList.add('top-level-layer');
            }
        });
    }
})();