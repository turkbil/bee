/**
 * Studio Save Action
 * İçeriği kaydetme işlemlerini yöneten modül
 */
const StudioSaveAction = (function() {
    let editor = null;
    let config = {};
    let saveButton = null;
    let isSaving = false;

    /**
     * Kaydetme eylemlerini ayarla
     * @param {Object} editorInstance GrapesJS editor örneği
     * @param {Object} options Yapılandırma seçenekleri
     */
    function init(editorInstance, options = {}) {
        editor = editorInstance;
        config = {
            saveButtonId: 'save-btn',
            savingClass: 'is-saving',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
            saveEndpoint: '/admin/studio/save',
            autoSaveInterval: 0, // Otomatik kaydetme için milisaniye (0: devre dışı)
            ...options
        };

        // Kaydet butonunu ayarla
        setupSaveButton();

        // Komut kaydet
        editor.Commands.add('save-content', {
            run: () => saveContent()
        });

        // Otomatik kaydetme
        if (config.autoSaveInterval > 0) {
            setInterval(() => {
                // İçerik değiştiğinde otomatik kaydet
                if (editor.getDirtyCount() > 0 && !isSaving) {
                    saveContent(true);
                }
            }, config.autoSaveInterval);
        }

        console.log('Save Action başlatıldı');
    }

    /**
     * Kaydet butonunu ayarla
     */
    function setupSaveButton() {
        saveButton = document.getElementById(config.saveButtonId);
        
        if (saveButton) {
            saveButton.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!isSaving) {
                    saveContent();
                }
            });
            
            console.log('Kaydet butonu hazırlandı');
        } else {
            console.warn('Kaydet butonu bulunamadı:', config.saveButtonId);
        }
    }

    /**
     * İçeriği kaydet
     * @param {boolean} isAuto Otomatik kaydetme mi?
     */
    function saveContent(isAuto = false) {
        if (isSaving) return;
        
        isSaving = true;
        
        // Kaydetme durumunu göster
        if (saveButton) {
            saveButton.classList.add(config.savingClass);
            saveButton.querySelector('span').textContent = 'Kaydediliyor...';
            saveButton.disabled = true;
        }
        
        // İçeriği hazırla
        const html = StudioCore.prepareContentForSave(editor);
        const css = StudioCore.prepareCssForSave(editor);
        
        // JS içeriğini textarea'dan al
        const jsContent = document.getElementById('js-content')?.value || '';
        
        // Tema ve şablon bilgilerini al (varsa)
        const themeSettings = {};
        if (typeof StudioThemeManager !== 'undefined') {
            Object.assign(themeSettings, StudioThemeManager.getActiveThemeSettings());
        }
        
        // Modül bilgilerini al
        const studioConfig = Studio.getConfig();
        const moduleType = studioConfig.moduleType || 'page';
        const moduleId = studioConfig.moduleId || 0;
        
        if (!moduleId) {
            showError('Modül ID bulunamadı!');
            resetSaveButton();
            return;
        }
        
        // İçeriği doğrula
        if (!validateContent(html)) {
            showError('İçerik doğrulama hatası. Lütfen HTML içeriğini kontrol edin.');
            resetSaveButton();
            return;
        }
        
        // Kaydetme URL'i
        const saveUrl = `${config.saveEndpoint}/${moduleType}/${moduleId}`;
        
        // Form verilerini oluştur
        const formData = new FormData();
        formData.append('content', html);
        formData.append('css', css);
        formData.append('js', jsContent);
        
        // Tema ayarlarını ekle
        if (themeSettings.theme) formData.append('theme', themeSettings.theme);
        if (themeSettings.header_template) formData.append('header_template', themeSettings.header_template);
        if (themeSettings.footer_template) formData.append('footer_template', themeSettings.footer_template);
        if (themeSettings.settings) formData.append('settings', JSON.stringify(themeSettings.settings));
        
        // Fetch API ile gönder
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': config.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Başarıyla kaydedildi
                showSuccess(isAuto ? 'İçerik otomatik kaydedildi.' : 'İçerik başarıyla kaydedildi.');
                
                // Formu temizle
                editor.setDirtyCount(0);
                
                // Olay gönder
                const event = new CustomEvent('studio:content-saved', { 
                    detail: { 
                        moduleType, 
                        moduleId, 
                        isAuto 
                    } 
                });
                document.dispatchEvent(event);
            } else {
                // Hata
                showError(data.message || 'Kaydetme işlemi başarısız oldu.');
            }
        })
        .catch(error => {
            console.error('Kaydetme hatası:', error);
            showError('Bağlantı hatası. Lütfen internet bağlantınızı kontrol edin.');
        })
        .finally(() => {
            resetSaveButton();
        });
    }
    
    /**
     * Kaydet butonunu sıfırla
     */
    function resetSaveButton() {
        isSaving = false;
        
        if (saveButton) {
            saveButton.classList.remove(config.savingClass);
            saveButton.querySelector('span').textContent = 'Kaydet';
            saveButton.disabled = false;
        }
    }
    
    /**
     * İçeriği doğrula
     * @param {string} content HTML içeriği
     * @returns {boolean} Doğrulama sonucu
     */
    function validateContent(content) {
        // Basit doğrulama: Boş içerik kontrolü
        if (!content || content.trim() === '') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Başarı mesajı göster
     * @param {string} message Mesaj
     */
    function showSuccess(message) {
        if (typeof StudioUI !== 'undefined' && StudioUI.showNotification) {
            StudioUI.showNotification(message, 'success');
        } else {
            console.log(message);
        }
    }
    
    /**
     * Hata mesajı göster
     * @param {string} message Mesaj
     */
    function showError(message) {
        if (typeof StudioUI !== 'undefined' && StudioUI.showNotification) {
            StudioUI.showNotification(message, 'error');
        } else {
            console.error(message);
        }
    }
    
    // Dışa aktarılan fonksiyonlar
    return {
        init: init,
        saveContent: saveContent
    };
})();

// Global olarak kullanılabilir yap
window.StudioSaveAction = StudioSaveAction;