/**
 * Studio Editor - Bloklar Modülü
 * Editor için özel blok tanımlamaları
 */
window.StudioBlocks = (function() {
    /**
     * Temel blok tanımlarını ekler
     * @param {Object} editor - GrapesJS editor örneği
     */
    function registerBlocks(editor) {
        // Reset mevcut kategoriler ve bloklar
        editor.BlockManager.getAll().reset();
        
        // Blok kategorileri tanımla - sadece bir kez
        editor.BlockManager.getCategories().reset();
        editor.BlockManager.getCategories().add([
            { id: "düzen", label: "Düzen Bileşenleri" },
            { id: "temel", label: "Temel Bileşenler" },
            { id: "medya", label: "Medya Bileşenleri" },
            { id: "bootstrap", label: "Bootstrap Bileşenleri" }
        ]);

        // 1 Sütunlu Bölüm
        editor.BlockManager.add("section-1col", {
            label: "1 Sütun",
            category: "düzen",
            attributes: { class: "fa fa-columns" },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Başlık Buraya</h2>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>`
        });

        // 2 Sütunlu Bölüm
        editor.BlockManager.add("section-2col", {
            label: "2 Sütun",
            category: "düzen",
            attributes: { class: "fa fa-columns" },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-6">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                    <div class="col-md-6">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek. Çift tıklayarak düzenleyebilirsiniz.</p>
                    </div>
                </div>
            </section>`
        });

        // 3 Sütunlu Bölüm
        editor.BlockManager.add("section-3col", {
            label: "3 Sütun",
            category: "düzen",
            attributes: { class: "fa fa-columns" },
            content: `<section class="container py-5">
                <div class="row">
                    <div class="col-md-4">
                        <h3>Başlık 1</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 2</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                    <div class="col-md-4">
                        <h3>Başlık 3</h3>
                        <p>İçerik buraya gelecek.</p>
                    </div>
                </div>
            </section>`
        });

        // Diğer blokları da ekle...
        // Header, Footer, Text, Link, Button, Image, Video, Card, vb.
        
        // Önemli: Tüm block-item etiketlerindeki data-block-id değerleri
        // burada tanımladığınız BlockManager ID'leriyle eşleşmelidir.
    }
    
    return {
        registerBlocks: registerBlocks
    };
})();