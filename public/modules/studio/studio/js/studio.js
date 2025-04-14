/**
 * Studio Editor - Ana Modül
 * Tüm modülleri yükler ve başlatır
 */
(function() {
    'use strict';
    
    // Yükleme göstergesi
    let loader = null;
    
    // Tüm modüller yüklendi mi
    let modulesLoaded = {
        config: false,
        editor: false,
        events: false,
        blocks: false,
        ui: false,
        actions: false,
        helpers: false
    };
    
    /**
     * Başlangıç noktası - Document yüklendiğinde çalışır
     */
    function init() {
        console.log('Studio Editor başlatılıyor...');
        
        // Yükleme göstergesini oluştur
        createLoader();
        
        // Konfigürasyonu hazırla
        prepareConfig();
        
        // DOM yüklendiğinde editörü başlat
        document.addEventListener('DOMContentLoaded', function() {
            // Editör container'ı kontrol et
            const editorElement = document.getElementById('gjs');
            if (!editorElement) {
                console.warn('Editor container (#gjs) bulunamadı.');
                hideLoader();
                return;
            }
            
            // DOM olayını tetikle
            document.dispatchEvent(new Event('studio:ready'));
            
            // Editörü başlat
            startEditor();
        });
    }
    
    /**
     * Studio Editor için yapılandırma hazırlar
     */
    function prepareConfig() {
        // Studio Config modülünü kontrol et
        if (!window.StudioConfig) {
            console.error('StudioConfig modülü yüklenemedi.');
            hideLoader();
            showError('Editör başlatılırken bir hata oluştu: Yapılandırma modülü yüklenemedi.');
            return;
        }
        
        // Config modülünün yüklendiğini işaretle
        modulesLoaded.config = true;
        
        // Modül yükleme durumunu kontrol et
        checkAllModules();
    }
    
    /**
     * Editor başlatma
     */
    function startEditor() {
        try {
            // Editör elementini bul
            const editorElement = document.getElementById('gjs');
            
            // Yapılandırma parametrelerini al
            const config = {
                containerId: 'gjs',
                moduleType: editorElement.getAttribute('data-module-type'),
                moduleId: parseInt(editorElement.getAttribute('data-module-id'))
            };
            
            // HTML/CSS/JS içeriklerini gizli alanlardan al
            config.content = document.getElementById('html-content') ? document.getElementById('html-content').value : '';
            config.css = document.getElementById('css-content') ? document.getElementById('css-content').value : '';
            config.js = document.getElementById('js-content') ? document.getElementById('js-content').value : '';
            
            // Modül tipini ve ID'sini kontrol et
            if (!config.moduleType || !config.moduleId) {
                console.warn('Modül tipini veya ID eksik. Bu, içerik kaydetme işlemini engelleyebilir.');
            }
            
            // Diğer modülleri kontrol et
            if (!isAllModulesLoaded()) {
                console.error('Tüm gerekli modüller yüklenemedi.');
                hideLoader();
                showError('Editör başlatılırken bir hata oluştu: Gerekli modüller yüklenemedi.');
                return;
            }
            
            // App modülünü kullanarak editörü başlat
            if (window.StudioApp && typeof window.StudioApp.init === 'function') {
                console.log('StudioApp.init() çağrılıyor...');
                window.StudioApp.init(config);
            } else {
                console.error('StudioApp modülü yüklenemedi.');
                hideLoader();
                showError('Editör başlatılırken bir hata oluştu: Uygulama modülü yüklenemedi.');
            }
        } catch (error) {
            console.error('Editor başlatılırken hata oluştu:', error);
            hideLoader();
            showError('Editör başlatılırken bir hata oluştu: ' + error.message);
        }
    }
    
    /**
     * Modüllerin yüklenip yüklenmediğini kontrol eder
     * @returns {boolean} - Tüm modüller yüklendi mi?
     */
    function isAllModulesLoaded() {
        // Config ve Events modülleri zorunlu
        if (!modulesLoaded.config || !modulesLoaded.events) {
            return false;
        }
        
        // StudioEvents modülünü kontrol et
        if (window.StudioEvents) {
            modulesLoaded.events = true;
        }
        
        // StudioEditor modülünü kontrol et
        if (window.StudioEditor) {
            modulesLoaded.editor = true;
        }
        
        // StudioBlocks modülünü kontrol et
        if (window.StudioBlocks) {
            modulesLoaded.blocks = true;
        }
        
        // StudioUI modülünü kontrol et
        if (window.StudioUI) {
            modulesLoaded.ui = true;
        }
        
        // StudioActions modülünü kontrol et
        if (window.StudioActions) {
            modulesLoaded.actions = true;
        }
        
        // StudioHelpers modülünü kontrol et
        if (window.StudioHelpers) {
            modulesLoaded.helpers = true;
        }
        
        return modulesLoaded.config && 
               modulesLoaded.events && 
               modulesLoaded.editor && 
               modulesLoaded.blocks && 
               modulesLoaded.ui && 
               modulesLoaded.actions && 
               modulesLoaded.helpers;
    }
    
    /**
     * Tüm modüllerin yüklenme durumunu kontrol eder
     */
    function checkAllModules() {
        // StudioEvents modülünü kontrol et
        if (window.StudioEvents) {
            modulesLoaded.events = true;
        }
        
        // StudioEditor modülünü kontrol et
        if (window.StudioEditor) {
            modulesLoaded.editor = true;
        }
        
        // StudioBlocks modülünü kontrol et
        if (window.StudioBlocks) {
            modulesLoaded.blocks = true;
        }
        
        // StudioUI modülünü kontrol et
        if (window.StudioUI) {
            modulesLoaded.ui = true;
        }
        
        // StudioActions modülünü kontrol et
        if (window.StudioActions) {
            modulesLoaded.actions = true;
        }
        
        // StudioHelpers modülünü kontrol et
        if (window.StudioHelpers) {
            modulesLoaded.helpers = true;
        }
        
        // Eksik modülleri logla
        const missingModules = [];
        Object.keys(modulesLoaded).forEach(module => {
            if (!modulesLoaded[module]) {
                missingModules.push(module);
            }
        });
        
        if (missingModules.length > 0) {
            console.warn('Bazı modüller eksik:', missingModules.join(', '));
        } else {
            console.log('Tüm modüller başarıyla yüklendi!');
        }
    }
    
    /**
     * Yükleme göstergesini oluşturur
     */
    function createLoader() {
        // Mevcut loader'ı temizle
        if (loader) {
            document.body.removeChild(loader);
        }
        
        // Yeni loader oluştur
        loader = document.createElement('div');
        loader.className = 'studio-loader';
        loader.style.position = 'fixed';
        loader.style.top = '0';
        loader.style.left = '0';
        loader.style.width = '100%';
        loader.style.height = '100%';
        loader.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        loader.style.display = 'flex';
        loader.style.alignItems = 'center';
        loader.style.justifyContent = 'center';
        loader.style.zIndex = '10000';
        loader.style.transition = 'opacity 0.3s ease';
        
        loader.innerHTML = `
            <div class="studio-loader-content" style="text-align: center; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                <div style="margin-bottom: 15px;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
                <h3 style="margin-bottom: 10px;">Studio Editor Yükleniyor</h3>
                <p style="color: #6c757d;">Lütfen bekleyin...</p>
            </div>
        `;
        
        document.body.appendChild(loader);
    }
    
    /**
     * Yükleme göstergesini gizler
     */
    function hideLoader() {
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                if (loader && loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                    loader = null;
                }
            }, 300);
        }
    }
    
    /**
     * Hata mesajı gösterir
     * @param {string} message - Hata mesajı
     */
    function showError(message) {
        // Hata alanı oluştur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'studio-error alert alert-danger';
        errorDiv.style.margin = '20px';
        errorDiv.style.maxWidth = '800px';
        errorDiv.style.margin = '20px auto';
        
        errorDiv.innerHTML = `
            <div class="d-flex">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                </div>
                <div>
                    <h5>Editör Başlatma Hatası</h5>
                    <p>${message}</p>
                    <button class="btn btn-sm btn-danger" onclick="window.location.reload()">Sayfayı Yenile</button>
                </div>
            </div>
        `;
        
        // Editör container'ına ekle
        const editorElement = document.getElementById('gjs');
        if (editorElement) {
            // Container içeriğini temizle
            editorElement.innerHTML = '';
            
            // Hata mesajını ekle
            editorElement.appendChild(errorDiv);
        } else {
            // Container bulunamazsa body'ye ekle
            document.body.appendChild(errorDiv);
        }
    }
    
    // Başlat
    init();
    
    // Window objelerine açık (public) fonksiyonları ekle
    window.StudioCore = window.StudioCore || {};
    window.StudioCore.hideLoader = hideLoader;
    window.StudioCore.showError = showError;
})();