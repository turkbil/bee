/**
 * Studio Editor - Bağımsız Çalışan Script
 * Hiçbir harici modül gerektirmeden GrapesJS editörünü çalıştırır
 */
(function() {
    // DOM yüklendiğinde başlat
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Studio Standalone Editor başlatılıyor...');
        initStudioEditor();
    });
    
    /**
     * Studio Editor'ı Başlat
     */
    function initStudioEditor() {
        // GrapesJS yüklendi mi kontrol et
        if (typeof grapesjs === 'undefined') {
            showError('GrapesJS kütüphanesi yüklenemedi!');
            return;
        }
        
        // Editör container'ını kontrol et
        const editorContainer = document.getElementById('gjs');
        if (!editorContainer) {
            console.warn('Editor container (#gjs) bulunamadı.');
            return;
        }
        
        // Modül ve ID bilgilerini al
        const moduleType = editorContainer.getAttribute('data-module-type');
        const moduleId = parseInt(editorContainer.getAttribute('data-module-id'));
        
        // İçerikleri al
        const htmlContentEl = document.getElementById('html-content');
        const cssContentEl = document.getElementById('css-content');
        const jsContentEl = document.getElementById('js-content');
        
        const htmlContent = htmlContentEl ? htmlContentEl.value : '';
        const cssContent = cssContentEl ? cssContentEl.value : '';
        
        // GrapesJS yapılandırması
        const editorConfig = {
            // Temel ayarlar
            container: '#gjs',
            height: '100%',
            width: 'auto',
            storageManager: false,
            
            // İçerik
            components: htmlContent,
            style: cssContent,
            
            // Panel ayarları - Varsayılan panelleri devre dışı bırak
            panels: { defaults: [] },
            
            // Blok yöneticisi
            blockManager: {
                appendTo: '#blocks-container'
            },
            
            // Katman yöneticisi
            layerManager: {
                appendTo: '#layers-container'
            },
            
            // Stil yöneticisi
            styleManager: {
                appendTo: '#styles-container'
            },
            
            // Özellik yöneticisi
            traitManager: {
                appendTo: '#traits-container'
            },
            
            // Cihaz yöneticisi
            deviceManager: {
                devices: [
                    {
                        name: 'Desktop',
                        width: '',
                    },
                    {
                        name: 'Tablet',
                        width: '768px',
                        widthMedia: '992px',
                    },
                    {
                        name: 'Mobile',
                        width: '320px',
                        widthMedia: '576px',
                    }
                ]
            },
            
            // Canvas ayarları
            canvas: {
                styles: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css'
                ],
                scripts: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
                ]
            }
        };
        
        // GrapesJS eklentileri yüklü ise kullan
        const plugins = [];
        
        if (typeof 'grapesjs-blocks-basic' !== 'undefined') {
            plugins.push('grapesjs-blocks-basic');
        }
        
        if (typeof 'grapesjs-preset-webpage' !== 'undefined') {
            plugins.push('grapesjs-preset-webpage');
        }
        
        if (plugins.length > 0) {
            editorConfig.plugins = plugins;
        }
        
        // GrapesJS editörünü başlat
        console.log('GrapesJS editor başlatılıyor...', editorConfig);
        const editor = grapesjs.init(editorConfig);
        
        // Editor yüklendi
        editor.on('load', function() {
            console.log('GrapesJS editor yüklendi.');
            
            // Yükleme göstergesini gizle
            hideLoader();
            
            // Butonları ayarla
            setupButtons(editor, moduleType, moduleId);
            
            // Blok kategorilerini ekle
            setupBlockCategories(editor);
            
            // Panel sekmelerini ayarla
            setupPanelTabs();
            
            // Temel blokları ekle
            setupBasicBlocks(editor);
        });
        
        // Global erişim için kaydet
        window.studioEditor = editor;
    }
    
    /**
     * Butonları ayarla
     */
    function setupButtons(editor, moduleType, moduleId) {
        // Kaydet butonu
        const saveBtn = document.getElementById('save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                saveContent(editor, moduleType, moduleId);
            });
        }
        
        // Önizleme butonu
        const previewBtn = document.getElementById('preview-btn');
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                editor.runCommand('preview');
            });
        }
        
        // Bileşen sınırlarını göster/gizle
        const swVisibilityBtn = document.getElementById('sw-visibility');
        if (swVisibilityBtn) {
            swVisibilityBtn.addEventListener('click', function() {
                editor.runCommand('sw-visibility');
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
        
        // HTML düzenleme butonu
        const codeEditBtn = document.getElementById('cmd-code-edit');
        if (codeEditBtn) {
            codeEditBtn.addEventListener('click', function() {
                const htmlContent = editor.getHtml();
                showCodeEditModal('HTML Düzenle', htmlContent, function(newHtml) {
                    editor.setComponents(newHtml);
                });
            });
        }
        
        // CSS düzenleme butonu
        const cssEditBtn = document.getElementById('cmd-css-edit');
        if (cssEditBtn) {
            cssEditBtn.addEventListener('click', function() {
                const cssContent = editor.getCss();
                showCodeEditModal('CSS Düzenle', cssContent, function(newCss) {
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
                showCodeEditModal('JavaScript Düzenle', jsContent, function(newJs) {
                    if (jsEl) jsEl.value = newJs;
                });
            });
        }
        
        // Cihaz butonları
        setupDeviceButtons(editor);
    }
    
    /**
     * İçeriği kaydet
     */
    function saveContent(editor, moduleType, moduleId) {
        // Kaydetme öncesi bileşenleri güncelle
        const html = editor.getHtml();
        const css = editor.getCss();
        const jsEl = document.getElementById('js-content');
        const js = jsEl ? jsEl.value : '';
        
        // Gizli form elementlerini güncelle
        updateFormElements(html, css, js);
        
        // CSRF Token al
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            showToast('Hata', 'CSRF Token bulunamadı! Sayfayı yenileyin.', 'error');
            return;
        }
        
        // Kaydetme URL'si
        const saveUrl = `/admin/studio/save/${moduleType}/${moduleId}`;
        
        // Kaydet butonunu devre dışı bırak
        const saveBtn = document.getElementById('save-btn');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Kaydediliyor...';
        }
        
        // Fetch API ile kaydet
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
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
                showToast('Başarılı', data.message || 'İçerik başarıyla kaydedildi!', 'success');
            } else {
                showToast('Hata', data.message || 'İçerik kaydedilirken bir hata oluştu.', 'error');
            }
        })
        .catch(error => {
            console.error('Kaydetme hatası:', error);
            showToast('Hata', 'İçerik kaydedilirken bir hata oluştu.', 'error');
        })
        .finally(() => {
            // Kaydet butonunu normal haline getir
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Kaydet';
            }
        });
    }
    
    /**
     * Gizli form elementlerini güncelle
     */
    function updateFormElements(html, css, js) {
        const htmlContentEl = document.getElementById('html-content');
        const cssContentEl = document.getElementById('css-content');
        const jsContentEl = document.getElementById('js-content');
        
        if (htmlContentEl) htmlContentEl.value = html;
        if (cssContentEl) cssContentEl.value = css;
        if (jsContentEl) jsContentEl.value = js;
    }
    
    /**
     * Blok kategorilerini ayarla
     */
    function setupBlockCategories(editor) {
        // Kategorileri sıfırla
        editor.BlockManager.getCategories().reset();
        
        // Kategorileri ekle
        editor.BlockManager.getCategories().add([
            { id: 'düzen', label: 'Düzen Bileşenleri', open: true },
            { id: 'temel', label: 'Temel Bileşenler', open: true },
            { id: 'medya', label: 'Medya', open: false },
            { id: 'bootstrap', label: 'Bootstrap Bileşenleri', open: false },
            { id: 'formlar', label: 'Formlar', open: false }
        ]);
    }
    
    /**
     * Temel blokları ekle
     */
    function setupBasicBlocks(editor) {
        // Düzen blokları
        registerLayoutBlocks(editor);
        
        // Temel bloklar
        registerCoreBlocks(editor);
        
        // Medya blokları
        registerMediaBlocks(editor);
        
        // Bootstrap blokları
        registerBootstrapBlocks(editor);
        
        // Form blokları
        registerFormBlocks(editor);
    }
    
    /**
     * Cihaz butonlarını ayarla
     */
    function setupDeviceButtons(editor) {
        const desktopBtn = document.getElementById('device-desktop');
        const tabletBtn = document.getElementById('device-tablet');
        const mobileBtn = document.getElementById('device-mobile');
        
        function setActiveDeviceButton(btn) {
            // Tüm butonlardan active sınıfını kaldır
            [desktopBtn, tabletBtn, mobileBtn].forEach(b => {
                if (b) b.classList.remove('active');
            });
            
            // Tıklanan butona active sınıfı ekle
            if (btn) btn.classList.add('active');
        }
        
        if (desktopBtn) {
            desktopBtn.addEventListener('click', function() {
                editor.setDevice('Desktop');
                setActiveDeviceButton(this);
            });
            
            // Varsayılan olarak aktif
            desktopBtn.classList.add('active');
        }
        
        if (tabletBtn) {
            tabletBtn.addEventListener('click', function() {
                editor.setDevice('Tablet');
                setActiveDeviceButton(this);
            });
        }
        
        if (mobileBtn) {
            mobileBtn.addEventListener('click', function() {
                editor.setDevice('Mobile');
                setActiveDeviceButton(this);
            });
        }
    }
    
    /**
     * Panel sekmelerini (tabs) ayarla
     */
    function setupPanelTabs() {
        const tabs = document.querySelectorAll('.panel-tab');
        const tabContents = document.querySelectorAll('.panel-tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Tüm tablardan active sınıfını kaldır
                tabs.forEach(t => t.classList.remove('active'));
                // Bu taba active sınıfı ekle
                this.classList.add('active');
                
                // Tüm içeriklerden active sınıfını kaldır
                tabContents.forEach(content => content.classList.remove('active'));
                // İlgili içeriğe active sınıfı ekle
                const activeContent = document.querySelector(`.panel-tab-content[data-tab-content="${tabId}"]`);
                if (activeContent) {
                    activeContent.classList.add('active');
                }
            });
        });
        
        // İlk sekmeyi varsayılan olarak aktif yap
        if (tabs.length > 0 && tabContents.length > 0) {
            tabs[0].classList.add('active');
            tabContents[0].classList.add('active');
        }
    }
    
    /**
     * Kod düzenleme modalı
     */
    function showCodeEditModal(title, content, callback) {
        // Modal oluştur
        const modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = 'codeEditModal';
        modalEl.setAttribute('tabindex', '-1');
        modalEl.setAttribute('aria-labelledby', 'codeEditModalLabel');
        modalEl.setAttribute('aria-hidden', 'true');
        
        modalEl.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="codeEditModalLabel">${title}</h5>
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
        
        document.body.appendChild(modalEl);
        
        // Bootstrap ile modalı aç
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
            
            // Kaydet butonu işlevi
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
            // Bootstrap yoksa basit bir prompt kullan
            const newCode = prompt(title, content);
            if (newCode !== null) {
                callback(newCode);
            }
        }
    }
    
    /**
     * Toast bildirimi göster
     */
    function showToast(title, message, type = 'success') {
        // Toast container'ı oluştur/kontrol et
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
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
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        
        // Bootstrap Toast kullan
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        } else {
            // Bootstrap yoksa manuel göster ve gizle
            setTimeout(() => {
                toastEl.style.opacity = '0';
                setTimeout(() => {
                    if (toastContainer.contains(toastEl)) {
                        toastContainer.removeChild(toastEl);
                    }
                }, 300);
            }, 3000);
        }
    }
    
    /**
     * Hata mesajı göster
     */
    function showError(message) {
        // Container'ı bul
        const container = document.getElementById('gjs');
        if (!container) return;
        
        // Hata mesajı div'i
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
                <li>JavaScript dosyalarının doğru yüklendiğinden emin olun</li>
                <li>GrapesJS kütüphanesinin düzgün yüklendiğini kontrol edin</li>
                <li>Browser konsolunda hata mesajlarını inceleyin</li>
            </ul>
            <button onclick="window.location.reload()" style="padding: 6px 12px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Sayfayı Yenile</button>
        `;
        
        // Mevcut içeriği temizle ve hata mesajını ekle
        container.innerHTML = '';
        container.appendChild(errorDiv);
    }
    
    /**
     * Yükleme göstergesini gizle
     */
    function hideLoader() {
        const loader = document.getElementById('studio-loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                if (loader.parentNode) {
                    loader.parentNode.removeChild(loader);
                }
            }, 300);
        }
    }
    
    /**
     * Düzen blokları
     */
    function registerLayoutBlocks(editor) {
        // 1 Sütunlu Bölüm
        editor.BlockManager.add('section-1col', {
            label: '1 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-square' },
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
        editor.BlockManager.add('section-2col', {
            label: '2 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-columns' },
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
        editor.BlockManager.add('section-3col', {
            label: '3 Sütun',
            category: 'düzen',
            attributes: { class: 'fa fa-grip-horizontal' },
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
        
        // Hero Bölüm
        editor.BlockManager.add('hero-section', {
            label: 'Hero Bölüm',
            category: 'düzen',
            attributes: { class: 'fa fa-star' },
            content: `<section class="py-5 text-center container">
                <div class="row py-lg-5">
                    <div class="col-lg-8 col-md-10 mx-auto">
                        <h1 class="fw-bold">Ana Başlık</h1>
                        <p class="lead text-muted">Buraya kısa bir açıklama yazabilirsiniz. Misyon, vizyon veya hizmetleriniz hakkında kısa bir tanıtım metni ekleyebilirsiniz.</p>
                        <div class="mt-4">
                            <a href="#" class="btn btn-primary my-2 me-2">Daha Fazla</a>
                            <a href="#" class="btn btn-secondary my-2">İletişim</a>
                        </div>
                    </div>
                </div>
            </section>`
        });
    }
    
    /**
     * Temel bloklar
     */
    function registerCoreBlocks(editor) {
        // Başlık
        editor.BlockManager.add('header-block', {
            label: 'Başlık',
            category: 'temel',
            attributes: { class: 'fa fa-heading' },
            content: {
                type: 'text',
                tagName: 'h2',
                content: 'Başlık Buraya',
                style: { padding: '10px' }
            }
        });
        
        // Paragraf
        editor.BlockManager.add('paragraph', {
            label: 'Paragraf',
            category: 'temel',
            attributes: { class: 'fa fa-paragraph' },
            content: {
                type: 'text',
                tagName: 'p',
                content: 'Buraya metin içeriği gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                style: { padding: '10px' }
            }
        });
        
        // Düğme
        editor.BlockManager.add('button', {
            label: 'Düğme',
            category: 'temel',
            attributes: { class: 'fa fa-square' },
            content: {
                type: 'link',
                tagName: 'a',
                content: 'Tıkla',
                classes: ['btn', 'btn-primary'],
                attributes: { href: '#' },
                style: { margin: '10px 0' }
            }
        });
        
        // Link
        editor.BlockManager.add('link', {
            label: 'Link',
            category: 'temel',
            attributes: { class: 'fa fa-link' },
            content: {
                type: 'link',
                content: 'Link Metni',
                attributes: { href: '#' }
            }
        });
        
        // Divider
        editor.BlockManager.add('divider', {
            label: 'Ayırıcı',
            category: 'temel',
            attributes: { class: 'fa fa-minus' },
            content: {
                type: 'divider',
                tagName: 'hr',
                style: { margin: '15px 0' }
            }
        });
    }
    
    /**
     * Medya blokları
     */
    function registerMediaBlocks(editor) {
        // Resim
        editor.BlockManager.add('image', {
            label: 'Resim',
            category: 'medya',
            attributes: { class: 'fa fa-image' },
            content: {
                type: 'image',
                style: { padding: '10px', 'max-width': '100%' },
                classes: ['img-fluid'],
                attributes: { src: 'https://via.placeholder.com/800x400', alt: 'Görsel açıklaması' }
            }
        });
        
        // Video
        editor.BlockManager.add('video', {
            label: 'Video',
            category: 'medya',
            attributes: { class: 'fa fa-video' },
            content: `<div class="ratio ratio-16x9 my-3">
                <iframe src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" 
                    title="Video başlığı" allowfullscreen></iframe>
            </div>`
        });
        
        // Resim + Metin
        editor.BlockManager.add('image-text', {
            label: 'Resim & Metin',
            category: 'medya',
            attributes: { class: 'fa fa-newspaper' },
            content: `<div class="row my-4 align-items-center">
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/600x400" class="img-fluid rounded" alt="Görsel açıklaması">
                </div>
                <div class="col-md-6">
                    <h3>Başlık Buraya</h3>
                    <p>Buraya açıklama metni gelecek. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <a href="#" class="btn btn-primary">Detaylar</a>
                </div>
            </div>`
        });
    }
    
    /**
     * Bootstrap blokları
     */
    function registerBootstrapBlocks(editor) {
        // Kart
        editor.BlockManager.add('card', {
            label: 'Kart',
            category: 'bootstrap',
            attributes: { class: 'fa fa-credit-card' },
            content: `<div class="card">
                <img src="https://via.placeholder.com/800x400" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Kart başlığı</h5>
                    <p class="card-text">Kart içeriği buraya gelecek. Kısa bir açıklama metni yazabilirsiniz.</p>
                    <a href="#" class="btn btn-primary">Detaylar</a>
                </div>
            </div>`
        });
        
        // Uyarı
        editor.BlockManager.add('alert', {
            label: 'Uyarı',
            category: 'bootstrap',
            attributes: { class: 'fa fa-exclamation-triangle' },
            content: `<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Dikkat!</strong> Önemli bir bildirim için bu uyarı bloğunu kullanabilirsiniz.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`
        });
    }
    
    /**
     * Form blokları
     */
    function registerFormBlocks(editor) {
        // İletişim Formu
        editor.BlockManager.add('contact-form', {
            label: 'İletişim Formu',
            category: 'formlar',
            attributes: { class: 'fa fa-envelope' },
            content: `<form class="my-4">
                <div class="mb-3">
                    <label for="name" class="form-label">Adınız Soyadınız</label>
                    <input type="text" class="form-control" id="name" placeholder="Adınız Soyadınız">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email adresiniz</label>
                    <input type="email" class="form-control" id="email" placeholder="ornek@domain.com">
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Mesajınız</label>
                    <textarea class="form-control" id="message" rows="5"></textarea>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary" type="submit">Gönder</button>
                </div>
            </form>`
        });
    }
})();