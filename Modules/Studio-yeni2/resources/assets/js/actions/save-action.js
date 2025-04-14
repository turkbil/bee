/**
 * Studio Editor - Save Action
 * İçerik kaydetme işlemlerini yönetir
 */
const StudioSaveAction = (function() {
    // İsteğin çalışıp çalışmadığını izleyen flag
    let isSaveInProgress = false;
    
    /**
     * Kaydetme düğmesini yapılandırır
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function setupSaveButton(editor, config) {
        const saveBtn = document.getElementById('save-btn');
        if (!saveBtn) {
            console.error('Save button (#save-btn) not found.');
            return;
        }
        
        // Kaydetme işlemini yapacak fonksiyon
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Zaten bir istek çalışıyorsa engelle
            if (isSaveInProgress) {
                console.log('Save operation already in progress, ignoring this click');
                return;
            }
            
            // İşlem başladı flag'ini ayarla
            isSaveInProgress = true;
            console.log('Save button clicked');
            
            // Butonu geçici olarak devre dışı bırak
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
            
            try {
                // İçeriği hazırla
                const result = saveContent(editor, config);
                
                // Sonucu işle (Promise döndürür)
                result.then(response => {
                    if (response.success) {
                        StudioUtils.showNotification('Başarılı', response.message || 'İçerik başarıyla kaydedildi!');
                        // Sayfaya yönlendirme olabilir veya başka işlemler
                    } else {
                        StudioUtils.showNotification('Hata', response.message || 'Kayıt işlemi başarısız.', 'error');
                    }
                }).catch(error => {
                    console.error('Kaydetme hatası:', error);
                    StudioUtils.showNotification('Hata', error.message || 'Sunucuya bağlanırken bir hata oluştu.', 'error');
                }).finally(() => {
                    // Butonu normal haline getir
                    this.disabled = false;
                    this.innerHTML = originalText;
                    
                    // İşlem bittikten sonra kilidi kaldır
                    setTimeout(() => {
                        isSaveInProgress = false;
                        console.log('Save operation lock released');
                    }, 1000); // 1 saniye beklet, hızlı çift tıklamaları önlemek için
                });
            } catch (error) {
                console.error('Save operation error:', error);
                this.disabled = false;
                this.innerHTML = originalText;
                isSaveInProgress = false; // Hata durumunda kilidi kaldır
                StudioUtils.showNotification('Hata', 'İçerik kaydedilirken bir sorun oluştu: ' + error.message, 'error');
            }
        });
    }
    
    /**
     * İçeriği kaydetme işlemini gerçekleştirir
     * @param {Object} editor - GrapesJS editor örneği
     * @param {Object} config - Yapılandırma parametreleri
     * @returns {Promise} - Kaydetme işlemi sonucu
     */
    function saveContent(editor, config) {
        // İçeriği hazırla
        let htmlContent = StudioCore.prepareContentForSave(editor);
        let cssContent = StudioCore.prepareCssForSave(editor);
        let jsContent = document.getElementById('js-content')?.value || '';
        
        // İçerik doğrulama
        if (!validateContent(htmlContent)) {
            return Promise.reject(new Error('Geçersiz HTML içeriği.'));
        }

        // Kaydetme URL'si
        const saveUrl = `/admin/studio/save/${config.moduleType}/${config.moduleId}`;
        
        // CSRF token'ı al
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // İstek verilerini hazırla
        const requestData = {
            content: htmlContent,
            css: cssContent,
            js: jsContent,
            // Tema ve şablon bilgileri de eklenebilir
            theme: config.theme,
            header_template: config.headerTemplate,
            footer_template: config.footerTemplate,
            settings: config.settings || {}
        };
        
        // Fetch API ile POST isteği
        return fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error('Doğrulama hatası: ' + Object.values(data.errors).flat().join(' '));
                    });
                }
                throw new Error('Sunucu hatası: ' + response.status);
            }
            return response.json();
        });
    }
    
    /**
     * İçerik doğrulama kontrolü
     * @param {string} html - HTML içeriği
     * @returns {boolean} - İçerik geçerli mi
     */
    function validateContent(html) {
        if (!html || html.length < 10) {
            StudioUtils.showNotification('Uyarı', 'İçerik çok kısa veya boş!', 'warning');
            return false;
        }
        
        // Temel doğrulama kontrolü - gerekirse daha karmaşık kontroller eklenebilir
        return true;
    }
    
    // Dışa aktarılan API
    return {
        setupSaveButton,
        saveContent,
        validateContent
    };
})();

// Global olarak kullanılabilir yap
window.StudioSaveAction = StudioSaveAction;