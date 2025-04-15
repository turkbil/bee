/**
 * Studio Editor - Bloklar Modülü
 * Blade şablonlarından yüklenen bloklar
 */
window.StudioBlocks = (function() {
    /**
     * Blade şablonlarından blokları kaydet
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        console.log("Server tarafından bloklar yükleniyor...");
        
        // Reset mevcut kategoriler ve bloklar
        editor.BlockManager.getAll().reset();
        editor.BlockManager.getCategories().reset();
        
        // Server'dan blokları al
        fetch('/admin/studio/api/blocks')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.blocks) {
                    // Kategorileri tanımla
                    const categories = {};
                    Object.keys(data.categories || {}).forEach(key => {
                        categories[key] = data.categories[key];
                        editor.BlockManager.getCategories().add({ id: key, label: data.categories[key] });
                    });
                    
                    // Blokları ekle
                    data.blocks.forEach(block => {
                        editor.BlockManager.add(block.id, {
                            label: block.label,
                            category: block.category,
                            attributes: { class: block.icon },
                            content: block.content
                        });
                    });
                    
                    console.log(`${data.blocks.length} adet blok başarıyla yüklendi`);
                } else {
                    console.error("Blok yüklenemedi:", data.message || "Server yanıt vermedi");
                }
            })
            .catch(error => {
                console.error("Bloklar yüklenirken hata oluştu:", error);
            });
    }
    
    return {
        registerBlocks: registerBlocks
    };
})();