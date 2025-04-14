/**
 * Studio Editor - Ana Uygulama Modülü
 * Tüm modülleri birleştirir ve uygulamayı başlatır
 */
window.StudioApp = (function() {
    // StudioHelpers mevcut değilse kendi deepMerge fonksiyonumuzu tanımlayalım
    if (!window.StudioHelpers) {
        window.StudioHelpers = {};
        
        /**
         * İki nesneyi derin birleştirir
         * @param {Object} target - Hedef nesne
         * @param {Object} source - Kaynak nesne
         * @returns {Object} - Birleştirilmiş nesne
         */
        window.StudioHelpers.deepMerge = function(target, source) {
            // Kaynak veya hedef nesne değilse, kaynağı döndür
            if (!target || typeof target !== 'object' || Array.isArray(target) ||
                !source || typeof source !== 'object' || Array.isArray(source)) {
                return source;
            }
            
            // Hedefin bir kopyasını al
            const output = Object.assign({}, target);
            
            // Kaynak anahtarlarını işle
            Object.keys(source).forEach(key => {
                if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                    // Hedefte anahtar yoksa, kaynaktan kopyala
                    if (!(key in target)) {
                        output[key] = source[key];
                    } else {
                        // Hedefte anahtar varsa, özyinelemeli birleştir
                        output[key] = window.StudioHelpers.deepMerge(target[key], source[key]);
                    }
                } else {
                    // Nesne değilse, kaynaktan atama yap
                    output[key] = source[key];
                }
            });
            
            return output;
        };
    }
    
    /**
     * Uygulamayı başlat
     * @param {Object} config - Yapılandırma parametreleri
     */
    function init(config = {}) {
        console.log('Studio Editor başlatılıyor...', config);
        
        // Yapılandırma değerlerini ayarla
        const defaultConfig = {
            containerId: 'gjs',
            moduleType: 'page',
            moduleId: 0,
            content: '',
            css: '',
            js: '',
            editorOptions: {}
        };
        
        // Özel yapılandırma ile varsayılanları birleştir
        const mergedConfig = window.StudioHelpers.deepMerge(defaultConfig, config);
        
        // Gerekli DOM elementlerini kontrol et
        const container = document.getElementById(mergedConfig.containerId);
        if (!container) {
            console.error(`Editor container (${mergedConfig.containerId}) bulunamadı!`);
            showSimpleError(`Editor container (${mergedConfig.containerId}) bulunamadı!`);
            return;
        }
        
        // HTML/CSS/JS içeriklerini al (container'dan veya yapılandırmadan)
        mergedConfig.content = mergedConfig.content || getContentFromElement('html-content');
        mergedConfig.css = mergedConfig.css || getContentFromElement('css-content');
        mergedConfig.js = mergedConfig.js || getContentFromElement('js-content');
        
        // Module tipi ve ID'yi container özelliklerinden al
        mergedConfig.moduleType = mergedConfig.moduleType || container.getAttribute('data-module-type');
        mergedConfig.moduleId = mergedConfig.moduleId || parseInt(container.getAttribute('data-module-id'));
        
        if (!mergedConfig.moduleType || !mergedConfig.moduleId) {
            console.warn('Module tipi veya ID bilgisi eksik! Bu durum içerik kaydetmeyi etkileyebilir.');
        }
        
        // Global yükleme bilgisi
        window.studioLoadedModules = {};
        
        // Basit başlatma - GrapesJS'i doğrudan başlat
        // StudioEditor veya diğer bağımlılıklar yoksa basit editör oluştur
        if (!window.StudioEditor || typeof window.StudioEditor.init !== 'function') {
            console.warn('StudioEditor modülü bulunamadı veya init fonksiyonu eksik. Basit editör oluşturuluyor...');
            initSimpleEditor(mergedConfig);
            return;
        }
        
        // GrapesJS editörünü başlat
        console.log('GrapesJS Editor başlatılıyor...');
        const editor = window.StudioEditor.init(mergedConfig);
        
        if (!editor) {
            console.error('GrapesJS editörü başlatılamadı!');
            showSimpleError('GrapesJS editörü başlatılamadı!');
            return;
        }
        
        // Editor örneğini oluştur
        window.studioEditor = editor;
        window.studioLoadedModules.editor = true;
        
        // Blokları ayarla
        editor.on('load', () => {
            // Blokları kaydet
            try {
                if (window.StudioBlocks && typeof window.StudioBlocks.registerBasicBlocks === 'function') {
                    window.StudioBlocks.registerBasicBlocks(editor);
                    window.studioLoadedModules.blocks = true;
                    console.log('Bloklar başarıyla kaydedildi.');
                } else {
                    console.warn('StudioBlocks modülü yüklenemedi! Bloklar kaydedilemeyecek.');
                }
            } catch (error) {
                console.error('Bloklar kaydedilirken hata:', error);
            }
            
            // Widget bloklarını yükle
            loadWidgetBlocks(editor, mergedConfig);
            
            // UI bileşenlerini ayarla
            try {
                if (window.StudioUI && typeof window.StudioUI.init === 'function') {
                    window.StudioUI.init(editor);
                    window.studioLoadedModules.ui = true;
                    console.log('UI modülü başarıyla başlatıldı.');
                } else {
                    console.warn('StudioUI modülü yüklenemedi! UI iyileştirmeleri yapılamayacak.');
                }
            } catch (error) {
                console.error('UI modülü başlatılırken hata:', error);
            }
            
            // Eylem butonlarını ayarla
            try {
                if (window.StudioActions && typeof window.StudioActions.setupActions === 'function') {
                    window.StudioActions.setupActions(editor, mergedConfig);
                    window.studioLoadedModules.actions = true;
                    console.log('Eylem butonları başarıyla ayarlandı.');
                } else {
                    console.warn('StudioActions modülü yüklenemedi! Eylem butonları etkinleştirilmeyecek.');
                }
            } catch (error) {
                console.error('Eylem butonları ayarlanırken hata:', error);
            }
            
            // Başlatma tamamlandı bilgisi
            console.log('Studio Editor başarıyla başlatıldı!');
            if (window.StudioEvents && typeof window.StudioEvents.trigger === 'function') {
                window.StudioEvents.trigger('app:ready', editor);
            }
        });
    }
    
    /**
     * Basit GrapesJS editörü başlat (diğer modüller olmadığında)
     * @param {Object} config - Yapılandırma 
     */
    function initSimpleEditor(config) {
        if (typeof grapesjs === 'undefined') {
            showSimpleError('GrapesJS kütüphanesi yüklenemedi!');
            return;
        }
        
        // GrapesJS temel yapılandırması
        const editorConfig = {
            container: '#' + config.containerId,
            height: '100%',
            width: 'auto',
            storageManager: false,
            components: config.content,
            style: config.css,
            panels: { defaults: [] }
        };
        
        // GrapesJS editörünü başlat
        const editor = grapesjs.init(editorConfig);
        
        // Editörü global olarak kaydet
        window.studioEditor = editor;
        
        // Yükleme mesajı
        console.log('Basit GrapesJS editörü başlatıldı (tam özellikler olmadan).');
        
        // En temel butonları ayarla
        setupBasicButtons(editor);
    }
    
    /**
     * En temel butonları ayarla 
     */
    function setupBasicButtons(editor) {
        // Kaydet butonu
        const saveBtn = document.getElementById('save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                // İçeriği hazırla
                const html = editor.getHtml();
                const css = editor.getCss();
                const jsEl = document.getElementById('js-content');
                const js = jsEl ? jsEl.value : '';
                
                // İçeriği form elementlerine aktar
                updateContentElements(html, css, js);
                
                // Kaydetme URL'si
                const container = document.getElementById('gjs');
                const moduleType = container.getAttribute('data-module-type');
                const moduleId = container.getAttribute('data-module-id');
                const saveUrl = `/admin/studio/save/${moduleType}/${moduleId}`;
                
                // CSRF token
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                // İçeriği kaydet
                fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content: html,
                        css: css,
                        js: js
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSimpleToast('Başarılı', data.message || 'İçerik başarıyla kaydedildi!', 'success');
                    } else {
                        showSimpleToast('Hata', data.message || 'İçerik kaydedilirken bir hata oluştu.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Kaydetme hatası:', error);
                    showSimpleToast('Hata', 'İçerik kaydedilirken bir hata oluştu.', 'error');
                });
            });
        }
        
        // Önizleme butonu
        const previewBtn = document.getElementById('preview-btn');
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                editor.runCommand('preview');
            });
        }
        
        // Görünürlük butonu
        const swVisibilityBtn = document.getElementById('sw-visibility');
        if (swVisibilityBtn) {
            swVisibilityBtn.addEventListener('click', function() {
                editor.runCommand('sw-visibility');
            });
        }
        
        // Geri al butonu
        const undoBtn = document.getElementById('cmd-undo');
        if (undoBtn) {
            undoBtn.addEventListener('click', function() {
                editor.UndoManager.undo();
            });
        }
        
        // Yinele butonu
        const redoBtn = document.getElementById('cmd-redo');
        if (redoBtn) {
            redoBtn.addEventListener('click', function() {
                editor.UndoManager.redo();
            });
        }
        
        // Temizle butonu
        const clearBtn = document.getElementById('cmd-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                if (confirm('İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
                    editor.DomComponents.clear();
                    editor.CssComposer.clear();
                }
            });
        }
        
        // HTML düzenleme butonu
        const codeEditBtn = document.getElementById('cmd-code-edit');
        if (codeEditBtn) {
            codeEditBtn.addEventListener('click', function() {
                const htmlContent = editor.getHtml();
                showCodeEditModal('HTML Düzenle', htmlContent, (newHtml) => {
                    editor.setComponents(newHtml);
                });
            });
        }
        
        // CSS düzenleme butonu
        const cssEditBtn = document.getElementById('cmd-css-edit');
        if (cssEditBtn) {
            cssEditBtn.addEventListener('click', function() {
                const cssContent = editor.getCss();
                showCodeEditModal('CSS Düzenle', cssContent, (newCss) => {
                    editor.setStyle(newCss);
                });
            });
        }
        
        // JS düzenleme butonu
        const jsEditBtn = document.getElementById('cmd-js-edit');
        if (jsEditBtn) {
            jsEditBtn.addEventListener('click', function() {
                const jsEl = document.getElementById('js-content');
                const jsContent = jsEl ? jsEl.value : '';
                showCodeEditModal('JavaScript Düzenle', jsContent, (newJs) => {
                    if (jsEl) jsEl.value = newJs;
                });
            });
        }
    }
    
    /**
     * Kod düzenleme modalı göster
     */
    function showCodeEditModal(title, content, callback) {
        // Bootstrap var mı kontrol et
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            // Modal oluştur
            const modalEl = document.createElement('div');
            modalEl.className = 'modal fade';
            modalEl.id = 'codeEditModal';
            modalEl.tabIndex = '-1';
            
            // Modal içeriği
            modalEl.innerHTML = `
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                        </div>
                        <div class="modal-body">
                            <textarea id="code-editor" class="form-control font-monospace" rows="20">${content}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" id="saveCodeBtn">Uygula</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Modalı ekle
            document.body.appendChild(modalEl);
            
            // Modal örneği oluştur
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            
            // Kaydet butonuna tıklama
            document.getElementById('saveCodeBtn').addEventListener('click', function() {
                const newCode = document.getElementById('code-editor').value;
                callback(newCode);
                modal.hide();
            });
            
            // Modal kapandığında temizle
            modalEl.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalEl);
            });
        } else {
            // Bootstrap yoksa basit prompt kullan
            const newCode = prompt(title, content);
            if (newCode !== null) {
                callback(newCode);
            }
        }
    }
    
    /**
     * Basit toast bildirimi göster
     */
    function showSimpleToast(title, message, type = 'success') {
        // Bootstrap var mı kontrol et
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            // Toast container oluştur
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(container);
            }
            
            // Toast elementini oluştur
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${
                type === 'success' ? 'success' : 'danger'
            } border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong>: ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Kapat"></button>
                </div>
            `;
            
            container.appendChild(toastEl);
            
            // Toast'u göster
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
            
            // Bir süre sonra kaldır
            setTimeout(() => {
                if (container.contains(toastEl)) {
                    container.removeChild(toastEl);
                }
            }, 3300);
        } else {
            // Bootstrap yoksa basit alert kullan
            alert(`${title}: ${message}`);
        }
    }
    
    /**
     * Basit hata mesajı göster
     */
    function showSimpleError(message) {
        // Editör container'ını bul
        const container = document.getElementById('gjs');
        if (!container) return;
        
        // Hata mesajı div'i oluştur
        const errorDiv = document.createElement('div');
        errorDiv.style.padding = '20px';
        errorDiv.style.margin = '20px';
        errorDiv.style.backgroundColor = '#fee2e2';
        errorDiv.style.color = '#991b1b';
        errorDiv.style.border = '1px solid #ef4444';
        errorDiv.style.borderRadius = '4px';
        
        errorDiv.innerHTML = `
            <h3 style="margin-top: 0;">Studio Editor Hatası</h3>
            <p>${message}</p>
            <p>Lütfen sayfayı yenileyin veya aşağıdaki sorunları kontrol edin:</p>
            <ul>
                <li>JavaScript dosyalarının doğru sırada yüklenmesi</li>
                <li>Gerekli tüm modüllerin (StudioHelpers, StudioEvents, vb.) yüklenmesi</li>
                <li>GrapesJS ve eklentilerinin düzgün yüklenmesi</li>
            </ul>
            <button onclick="window.location.reload()" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Sayfayı Yenile</button>
        `;
        
        // Mevcut içeriği temizle ve hata mesajını ekle
        container.innerHTML = '';
        container.appendChild(errorDiv);
    }
    
    /**
     * Element içeriğini al
     * @param {string} elementId - Element ID'si
     * @returns {string} - Element içeriği
     */
    function getContentFromElement(elementId) {
        const element = document.getElementById(elementId);
        return element ? element.value || element.innerHTML : '';
    }
    
    /**
     * Form elementlerini güncelle
     */
    function updateContentElements(html, css, js) {
        // HTML içerik elementini güncelle
        const htmlEl = document.getElementById('html-content');
        if (htmlEl) htmlEl.value = html;
        
        // CSS içerik elementini güncelle
        const cssEl = document.getElementById('css-content');
        if (cssEl) cssEl.value = css;
        
        // JS içerik elementini güncelle
        const jsEl = document.getElementById('js-content');
        if (jsEl) jsEl.value = js;
    }
    
    /**
     * Widget bloklarını yükle
     * @param {Object} editor - GrapesJS editör örneği
     * @param {Object} config - Yapılandırma parametreleri
     */
    function loadWidgetBlocks(editor, config) {
        // Widget bloklarını AJAX ile yükle
        if (!window.StudioBlocks || typeof window.StudioBlocks.registerWidgetBlocks !== 'function') {
            console.warn('Widget blokları yüklenemedi: StudioBlocks modülü eksik.');
            return;
        }
        
        const widgetApiUrl = '/admin/studio/api/widgets';
        
        fetch(widgetApiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.widgets && data.widgets.length > 0) {
                    // Widget bloklarını kaydet
                    window.StudioBlocks.registerWidgetBlocks(editor, data.widgets);
                    console.log(`${data.widgets.length} widget bloğu kaydedildi.`);
                } else {
                    console.log('Kayıtlı widget bulunamadı.');
                }
            })
            .catch(error => {
                console.error('Widget verileri yüklenirken hata:', error);
            });
    }
    
    return {
        init: init
    };
})();

// DOM yüklendikten sonra başlat
document.addEventListener('DOMContentLoaded', function() {
    // Editör container'ını kontrol et
    const editorContainer = document.getElementById('gjs');
    if (!editorContainer) {
        console.warn('Editor container (#gjs) bulunamadı. Studio Editor başlatılmadı.');
        return;
    }
    
    // Editör yapılandırması
    const config = {
        containerId: 'gjs',
        moduleType: editorContainer.getAttribute('data-module-type'),
        moduleId: parseInt(editorContainer.getAttribute('data-module-id'))
    };
    
    // Uygulamayı başlat
    window.StudioApp.init(config);
});