/**
 * Studio Editor - HTML Parser Modülü
 * HTML içeriğini parse eden ve düzeltmeler yapan yardımcı fonksiyonlar
 */
window.StudioHtmlParser = (function() {
    console.log('StudioHtmlParser modülü yükleniyor...');
    
    /**
     * HTML içeriğini temizler ve düzeltmeler yapar
     * @param {string} htmlString - HTML içeriği
     * @returns {string} - Düzeltilmiş HTML
     */
    function parseAndFixHtml(htmlString) {
        if (!htmlString || typeof htmlString !== 'string') {
            console.warn('Geçersiz HTML içeriği: boş veya string değil');
            return getDefaultContent();
        }
        
        // HTML içeriğini temizle
        let cleanHtml = htmlString.trim();
        
        // Sadece <body></body> gibi boş bir yapı mı kontrol et
        if (cleanHtml === '<body></body>' || 
            cleanHtml === '<body> </body>' ||
            cleanHtml.length < 20) {
            console.warn('Boş veya çok kısa HTML içeriği tespit edildi');
            return getDefaultContent();
        }
        
        // Body içeriğini al
        let bodyContent = cleanHtml;
        const bodyMatchRegex = /<body[^>]*>([\s\S]*?)<\/body>/;
        const bodyMatch = cleanHtml.match(bodyMatchRegex);
        
        if (bodyMatch && bodyMatch[1]) {
            bodyContent = bodyMatch[1].trim();
            console.log("Body içinde içerik bulundu:", bodyContent.substring(0, 50) + '...');
        } else {
            console.log("Body etiketi bulunamadı, tüm içerik kullanılacak");
        }
        
        // Eğer içerik hala boşsa, varsayılan içerik ver
        if (!bodyContent || bodyContent.length < 10) {
            console.warn('İçerik body etiketinden çıkarıldıktan sonra boş veya çok kısa');
            return getDefaultContent();
        }
        
        return bodyContent;
    }
    
    /**
     * HTML içeriği kaydetmeye hazırla
     * @param {Object} editor - GrapesJS editor instance
     * @returns {string} - Düzeltilmiş ve kaydetmeye hazır HTML
     */
    function prepareContentForSave(editor) {
        let finalContent = '';
        
        try {
            // Önce editor getHtml metodunu deneyelim
            if (editor && typeof editor.getHtml === 'function') {
                finalContent = editor.getHtml();
                
                // İçerik kontrolü
                if (!finalContent || finalContent === '<body></body>' || finalContent.length < 20) {
                    console.warn('Editor getHtml() boş içerik döndürdü, alternatif yöntemler deneniyor...');
                    
                    // Alternatif 1: Komponentleri toHTML ile dönüştür
                    try {
                        if (editor.getComponents && editor.getComponents().length > 0) {
                            const components = editor.getComponents();
                            let componentsHtml = '';
                            
                            components.each(component => {
                                componentsHtml += component.toHTML();
                            });
                            
                            if (componentsHtml && componentsHtml.length > 20) {
                                finalContent = componentsHtml;
                                console.log('Komponent HTML içeriği oluşturuldu:', finalContent.substring(0, 50) + '...');
                            }
                        }
                    } catch (err) {
                        console.error('Komponent toHTML hatası:', err);
                    }
                    
                    // Alternatif 2: Canvas iframe içeriğine doğrudan eriş
                    if (!finalContent || finalContent === '<body></body>' || finalContent.length < 20) {
                        try {
                            const iframe = document.querySelector('#gjs iframe');
                            if (iframe && iframe.contentDocument && iframe.contentDocument.body) {
                                finalContent = iframe.contentDocument.body.innerHTML;
                                console.log('Canvas iframe içeriği alındı:', finalContent.substring(0, 50) + '...');
                            }
                        } catch (err) {
                            console.error('Canvas iframe erişim hatası:', err);
                        }
                    }
                }
            }
            
            // Eğer hala içerik yoksa veya geçersizse
            if (!finalContent || finalContent === '<body></body>' || finalContent.length < 20) {
                console.warn('Hiçbir yöntem geçerli içerik sağlamadı, varsayılan içerik dönülüyor.');
                finalContent = getDefaultContent();
            }
            
            // Son bir güvenlik kontrolü ve temizleme
            if (finalContent.includes('<script')) {
                console.warn('İçerikte <script> etiketleri tespit edildi, temizleniyor...');
                finalContent = finalContent.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
            }
            
            return finalContent;
        } catch (e) {
            console.error('İçerik hazırlama sırasında hata:', e);
            return getDefaultContent();
        }
    }
    
    /**
     * Varsayılan içerik
     * @returns {string} - Varsayılan HTML içeriği
     */
    function getDefaultContent() {
        return `
        <div class="container py-4">
            <div class="row">
                <div class="col-12">
                    <h1 class="mb-4">Yeni Sayfa</h1>
                    <p class="lead">Bu sayfayı düzenlemek için sol taraftaki bileşenleri kullanabilirsiniz.</p>
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i> Studio Editor ile görsel düzenleme yapabilirsiniz.
                        Düzenlemelerinizi kaydetmek için sağ üstteki Kaydet butonunu kullanın.
                    </div>
                </div>
            </div>
        </div>`;
    }
    
    return {
        parseAndFixHtml: parseAndFixHtml,
        prepareContentForSave: prepareContentForSave,
        getDefaultContent: getDefaultContent
    };
})();