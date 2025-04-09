<div>
    <div>
        <div class="gjs-wrapper">
            <div id="blocks-panel">
                <div id="blocks" class="blocks-container"></div>
            </div>
            
            <div id="gjs" class="gjs-editor-cont">{!! $content !!}</div>
            
            <div id="layer-panel">
                <div class="panel__top">
                    <div class="panel__devices">
                        <button id="studio-device-desktop" class="editor-device-button active">
                            <i class="fas fa-desktop"></i>
                        </button>
                        <button id="studio-device-tablet" class="editor-device-button">
                            <i class="fas fa-tablet-alt"></i>
                        </button>
                        <button id="studio-device-mobile" class="editor-device-button">
                            <i class="fas fa-mobile-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="panel-tabs">
                    <div class="panel-tabs-header">
                        <button class="panel-tab-btn active" data-tab="layers">Katmanlar</button>
                        <button class="panel-tab-btn" data-tab="styles">Stiller</button>
                        <button class="panel-tab-btn" data-tab="traits">Özellikler</button>
                    </div>
                    <div class="panel-tab-content">
                        <div id="layers-container" class="tab-panel active"></div>
                        <div id="styles-container" class="tab-panel"></div>
                        <div id="traits-container" class="tab-panel"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="hidden-styles" style="display: none;">
            <pre id="css-content">{!! htmlspecialchars($css) !!}</pre>
            <pre id="js-content">{!! htmlspecialchars($js) !!}</pre>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // CSS stilleri ekle
                const inlineCss = `
                    /* Temel Stiller */
                    .gjs-frame {
                        display: block !important;
                        visibility: visible !important;
                        position: relative !important;
                    }
                    .gjs-cv-canvas {
                        position: relative !important;
                        top: 0 !important;
                        width: 100% !important;
                        height: 100% !important;
                        visibility: visible !important;
                        display: block !important;
                        overflow: visible !important;
                    }
                    
                    /* Panel Tab Sistemi */
                    .panel-tabs {
                        display: flex;
                        flex-direction: column;
                        height: calc(100vh - 90px);
                        overflow: hidden;
                    }
                    .panel-tabs-header {
                        display: flex;
                        background: #f5f5f5;
                        border-bottom: 1px solid #ddd;
                    }
                    .panel-tab-btn {
                        flex: 1;
                        padding: 10px;
                        background: none;
                        border: none;
                        cursor: pointer;
                        font-size: 14px;
                        color: #333;
                        border-bottom: 2px solid transparent;
                    }
                    .panel-tab-btn.active {
                        background: #fff;
                        border-bottom: 2px solid #3b97e3;
                        font-weight: 500;
                    }
                    .panel-tab-content {
                        flex: 1;
                        overflow: hidden;
                        position: relative;
                    }
                    .tab-panel {
                        display: none;
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        overflow-y: auto;
                        background: #333;
                        padding: 0;
                    }
                    .tab-panel.active {
                        display: block;
                    }
                    
                    /* Blok paneli düzenlemesi */
                    #blocks-panel {
                        width: 250px;
                        background: #f8f9fa;
                        height: calc(100vh - 50px);
                        overflow-y: auto;
                        border-right: 1px solid #ddd;
                    }
                    
                    /* Sağ panel düzenlemesi */
                    #layer-panel {
                        width: 300px;
                        background: #f8f9fa;
                        height: calc(100vh - 50px);
                        overflow: hidden;
                        border-left: 1px solid #ddd;
                        display: flex;
                        flex-direction: column;
                    }
                    
                    /* Panel içi stiller */
                    .gjs-sm-sector {
                        margin-bottom: 10px;
                        background-color: #333;
                        border-bottom: 1px solid #444;
                    }
                    .gjs-sm-sector-title {
                        font-weight: 500;
                        color: #eee;
                    }
                    .gjs-sm-property {
                        padding: 8px 5px;
                        background-color: #333;
                    }
                    .gjs-field {
                        background-color: #444;
                        border-radius: 3px;
                        margin-top: 5px;
                    }
                    .gjs-field input, .gjs-field select, .gjs-field textarea {
                        color: #eee;
                    }
                    .gjs-sm-label {
                        color: #ddd;
                    }
                    
                    /* Katmanlar paneli stilleri */
                    .gjs-layer {
                        background-color: #333;
                        border-bottom: 1px solid #444;
                        color: #eee;
                    }
                    .gjs-layer-title {
                        color: #eee;
                    }
                    .gjs-layer.gjs-selected {
                        background-color: #444;
                    }
                    
                    /* Cihaz butonu stilleri */
                    .panel__devices {
                        padding: 10px;
                        display: flex;
                        justify-content: flex-end;
                        background-color: #333;
                    }
                    .editor-device-button {
                        padding: 5px 10px;
                        margin: 0 3px;
                        background: transparent;
                        color: #ddd;
                        border: 1px solid #555;
                        border-radius: 3px;
                        cursor: pointer;
                    }
                    .editor-device-button.active {
                        background: #444;
                        color: #fff;
                        border-color: #777;
                    }
                `;
                
                // Inline stilleri ekle
                const styleEl = document.createElement('style');
                styleEl.textContent = inlineCss;
                document.head.appendChild(styleEl);
                
                // CSS ve JS içeriğini DOM'a ekle
                const cssElement = document.createElement('style');
                cssElement.id = 'gjs-custom-css';
                cssElement.textContent = document.getElementById('css-content').textContent;
                document.head.appendChild(cssElement);
                
                // Panel sekme geçişleri için işlev
                function setupTabs() {
                    const tabButtons = document.querySelectorAll('.panel-tab-btn');
                    tabButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            // Tüm sekmeleri deaktif et
                            document.querySelectorAll('.panel-tab-btn').forEach(btn => btn.classList.remove('active'));
                            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
                            
                            // Tıklanan sekmeyi aktif et
                            this.classList.add('active');
                            const tabName = this.getAttribute('data-tab');
                            document.getElementById(tabName + '-container').classList.add('active');
                        });
                    });
                }
                
                // GrapesJS editörünü başlat
                const editor = grapesjs.init({
                    container: '#gjs',
                    fromElement: true,
                    height: '100%',
                    width: 'auto',
                    storageManager: false,
                    allowScripts: 1,
                    panels: {
                        defaults: []
                    },
                    canvas: {
                        styles: [
                            '{{ asset('admin/css/tabler.min.css') }}',
                            '{{ asset('admin/css/tabler-vendors.min.css') }}'
                        ],
                        scripts: [
                            '{{ asset('admin/js/tabler.min.js') }}'
                        ],
                    },
                    deviceManager: {
                        devices: [
                            {
                                id: 'desktop',
                                name: 'Masaüstü',
                                width: '',
                            },
                            {
                                id: 'tablet',
                                name: 'Tablet',
                                width: '768px',
                                widthMedia: '992px',
                            },
                            {
                                id: 'mobile',
                                name: 'Mobil',
                                width: '320px',
                                widthMedia: '576px',
                            }
                        ]
                    },
                    assetManager: {
                        assets: [],
                        upload: '{{ route('admin.studio.api.assets.upload') }}',
                        uploadName: 'files',
                        autoAdd: true,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        params: {
                            _token: '{{ csrf_token() }}'
                        }
                    },
                    styleManager: {
                        appendTo: '#styles-container',
                        sectors: [{
                            name: 'Genel',
                            open: true,
                            properties: [
                                'display', 
                                'flex-direction', 
                                'flex-wrap', 
                                'justify-content', 
                                'align-items', 
                                'align-content', 
                                'order', 
                                'flex-basis', 
                                'flex-grow', 
                                'flex-shrink', 
                                'align-self'
                            ]
                        }, {
                            name: 'Boyut',
                            open: false,
                            properties: [
                                'width', 
                                'height', 
                                'max-width', 
                                'min-height', 
                                'margin', 
                                'padding'
                            ]
                        }, {
                            name: 'Yazı',
                            open: false,
                            properties: [
                                'font-family', 
                                'font-size', 
                                'font-weight', 
                                'letter-spacing', 
                                'color', 
                                'line-height', 
                                'text-align', 
                                'text-decoration', 
                                'text-shadow'
                            ]
                        }, {
                            name: 'Arkaplan',
                            open: false,
                            properties: [
                                'background-color', 
                                'background-image', 
                                'background-repeat', 
                                'background-position', 
                                'background-attachment', 
                                'background-size'
                            ]
                        }, {
                            name: 'Kenarlık',
                            open: false,
                            properties: [
                                'border-radius', 
                                'border', 
                                'border-width', 
                                'border-style', 
                                'border-color'
                            ]
                        }, {
                            name: 'Ekstra',
                            open: false,
                            properties: [
                                'opacity', 
                                'box-shadow', 
                                'transition', 
                                'transform'
                            ]
                        }]
                    },
                    layerManager: {
                        appendTo: '#layers-container'
                    },
                    traitManager: {
                        appendTo: '#traits-container'
                    },
                    blockManager: {
                        appendTo: '#blocks',
                        blocks: [
                            {
                                id: 'section',
                                label: 'Bölüm',
                                category: 'basic',
                                content: '<section class="py-5"><div class="container"><h2>Bölüm Başlığı</h2><p>Buraya içeriğinizi ekleyin</p></div></section>',
                                attributes: { class: 'fa fa-square-full' }
                            },
                            {
                                id: 'container',
                                label: 'Konteyner',
                                category: 'basic',
                                content: '<div class="container"><p>Konteyner içeriği</p></div>',
                                attributes: { class: 'fa fa-square' }
                            },
                            {
                                id: 'text',
                                label: 'Metin',
                                category: 'basic',
                                content: '<div>Buraya metin girin</div>',
                                attributes: { class: 'fa fa-font' }
                            },
                            {
                                id: 'image',
                                label: 'Resim',
                                category: 'basic',
                                content: '<img src="https://via.placeholder.com/350x150" alt="Resim" class="img-fluid">',
                                attributes: { class: 'fa fa-image' }
                            },
                            {
                                id: 'heading',
                                label: 'Başlık',
                                category: 'typography',
                                content: '<h1>Başlık</h1>',
                                attributes: { class: 'fa fa-heading' }
                            },
                            {
                                id: 'paragraph',
                                label: 'Paragraf',
                                category: 'typography',
                                content: '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam condimentum ex nec enim fermentum, eget sollicitudin quam efficitur.</p>',
                                attributes: { class: 'fa fa-paragraph' }
                            },
                            {
                                id: 'button',
                                label: 'Düğme',
                                category: 'components',
                                content: '<button class="btn btn-primary">Düğme</button>',
                                attributes: { class: 'fa fa-square' }
                            },
                            {
                                id: 'row-2-col',
                                label: '2 Sütun',
                                category: 'layout',
                                content: '<div class="row"><div class="col-md-6"><p>Birinci sütun</p></div><div class="col-md-6"><p>İkinci sütun</p></div></div>',
                                attributes: { class: 'fa fa-columns' }
                            }
                        ]
                    }
                });
                
                // Tab sistemini aktifleştir
                setupTabs();
                
                // Görünürlük düzeltmeleri
                editor.on('load', () => {
                    console.log('Editor yüklendi');
                    
                    // İframe stil düzeltmeleri
                    setTimeout(() => {
                        // İframe görünürlük düzeltmesi
                        const iframe = document.querySelector('.gjs-frame');
                        if (iframe) {
                            iframe.style.height = 'calc(100vh - 150px)';
                            iframe.style.minHeight = '500px';
                            iframe.style.display = 'block';
                            iframe.style.visibility = 'visible';
                            iframe.style.position = 'relative';
                            iframe.style.zIndex = '2';
                        }
                        
                        // Canvas düzeltmeleri
                        const canvasEl = document.querySelector('.gjs-cv-canvas');
                        if (canvasEl) {
                            canvasEl.style.height = 'calc(100vh - 150px)';
                            canvasEl.style.visibility = 'visible';
                            canvasEl.style.display = 'block';
                            canvasEl.style.zIndex = '1';
                        }
                        
                        // Editör refresh
                        editor.refresh();
                    }, 500);
                    
                    // Ek stil düzeltmeleri
                    const forceStyle = document.createElement('style');
                    forceStyle.innerHTML = `
                        .gjs-one-bg { background-color: #333 !important; }
                        .gjs-two-color { color: #eee !important; }
                        .gjs-field { background-color: #444 !important; }
                        .gjs-field input, .gjs-field select { color: #eee !important; }
                        .gjs-sm-sector { background-color: #333 !important; border: none !important; }
                        .gjs-sm-property { background-color: #333 !important; }
                        .gjs-sm-sector-title { color: #eee !important; }
                        .gjs-sm-label { color: #ddd !important; }
                        .gjs-mdl-dialog { background-color: #333 !important; color: #eee !important; }
                        .gjs-block { border: 1px solid #555 !important; }
                        .gjs-block:hover { box-shadow: 0 0 0 2px #3b97e3 !important; }
                    `;
                    document.head.appendChild(forceStyle);
                });
                
                // Widget kategorileri ekle
                editor.BlockManager.getCategories().add([
                    { id: 'basic', label: 'Temel' },
                    { id: 'typography', label: 'Tipografi' },
                    { id: 'layout', label: 'Düzen' },
                    { id: 'components', label: 'Bileşenler' }
                ]);
                
                // CSS Editör Modalı
                editor.Commands.add('open-css', {
                    run: function(editor, sender) {
                        const cssContent = document.getElementById('css-content').textContent;
                        
                        const modal = editor.Modal.open({
                            title: 'CSS Düzenle',
                            content: `<textarea id="css-editor" style="width: 100%; height: 400px; font-family: monospace; background: #333; color: #eee; border: 1px solid #555;">${cssContent}</textarea>
                                    <div class="modal-footer mt-3">
                                        <button id="css-save" class="btn btn-primary">Kaydet</button>
                                        <button id="css-cancel" class="btn btn-secondary">İptal</button>
                                    </div>`,
                        });
                        
                        document.getElementById('css-save').addEventListener('click', function() {
                            const cssEditor = document.getElementById('css-editor');
                            document.getElementById('css-content').textContent = cssEditor.value;
                            
                            const styleEl = document.getElementById('gjs-custom-css');
                            styleEl.textContent = cssEditor.value;
                            
                            editor.Modal.close();
                        });
                        
                        document.getElementById('css-cancel').addEventListener('click', function() {
                            editor.Modal.close();
                        });
                    }
                });
                
                // Cihaz düğmeleri
                document.getElementById('studio-device-desktop').addEventListener('click', function() {
                    editor.setDevice('desktop');
                    this.classList.add('active');
                    document.getElementById('studio-device-tablet').classList.remove('active');
                    document.getElementById('studio-device-mobile').classList.remove('active');
                });
                
                document.getElementById('studio-device-tablet').addEventListener('click', function() {
                    editor.setDevice('tablet');
                    this.classList.add('active');
                    document.getElementById('studio-device-desktop').classList.remove('active');
                    document.getElementById('studio-device-mobile').classList.remove('active');
                });
                
                document.getElementById('studio-device-mobile').addEventListener('click', function() {
                    editor.setDevice('mobile');
                    this.classList.add('active');
                    document.getElementById('studio-device-desktop').classList.remove('active');
                    document.getElementById('studio-device-tablet').classList.remove('active');
                });
                
                // Kaydet düğmesi
                document.getElementById('studio-save').addEventListener('click', function() {
                    // Kaydediliyor bildirimi göster
                    const saveBtn = this;
                    const originalText = saveBtn.innerHTML;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Kaydediliyor...';
                    saveBtn.disabled = true;
                    
                    // HTML içeriğini al
                    const htmlContent = editor.getHtml();
                    
                    // CSS içeriğini al
                    const cssContent = document.getElementById('css-content').textContent;
                    
                    // JS içeriğini al
                    const jsContent = document.getElementById('js-content').textContent;
                    
                    // AJAX ile kaydet
                    fetch('{{ route('admin.studio.save', ['module' => $moduleType, 'id' => $moduleId]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            content: htmlContent,
                            css: cssContent,
                            js: jsContent
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Butonu normal durumuna getir
                        saveBtn.innerHTML = originalText;
                        saveBtn.disabled = false;
                        
                        if (data.success) {
                            // Başarı bildirimi
                            const notification = document.createElement('div');
                            notification.className = 'toast show';
                            notification.setAttribute('role', 'alert');
                            notification.setAttribute('aria-live', 'assertive');
                            notification.setAttribute('aria-atomic', 'true');
                            notification.setAttribute('data-bs-autohide', 'true');
                            notification.setAttribute('data-bs-delay', '3000');
                            notification.style.position = 'fixed';
                            notification.style.top = '20px';
                            notification.style.right = '20px';
                            notification.style.minWidth = '300px';
                            notification.style.zIndex = '9999';
                            
                            notification.innerHTML = `
                                <div class="toast-header bg-success text-white">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong class="me-auto">Başarılı</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    İçerik başarıyla kaydedildi.
                                </div>
                            `;
                            
                            document.body.appendChild(notification);
                            
                            // 2 saniye sonra yönlendir
                            setTimeout(() => {
                                if ('{{ $moduleType }}' === 'page') {
                                    window.location.href = '{{ route('admin.page.index') }}';
                                }
                            }, 2000);
                        } else {
                            // Hata bildirimi
                            const notification = document.createElement('div');
                            notification.className = 'toast show';
                            notification.setAttribute('role', 'alert');
                            notification.setAttribute('aria-live', 'assertive');
                            notification.setAttribute('aria-atomic', 'true');
                            notification.style.position = 'fixed';
                            notification.style.top = '20px';
                            notification.style.right = '20px';
                            notification.style.minWidth = '300px';
                            notification.style.zIndex = '9999';
                            
                            notification.innerHTML = `
                                <div class="toast-header bg-danger text-white">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong class="me-auto">Hata</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                <div class="toast-body">
                                    ${data.message || 'Kaydetme sırasında bir hata oluştu.'}
                                </div>
                            `;
                            
                            document.body.appendChild(notification);
                        }
                    })
                    .catch(error => {
                        console.error('Kaydetme hatası:', error);
                        
                        // Butonu normale döndür
                        saveBtn.innerHTML = originalText;
                        saveBtn.disabled = false;
                        
                        // Hata bildirimi göster
                        const notification = document.createElement('div');
                        notification.className = 'toast show';
                        notification.setAttribute('role', 'alert');
                        notification.setAttribute('aria-live', 'assertive');
                        notification.setAttribute('aria-atomic', 'true');
                        notification.style.position = 'fixed';
                        notification.style.top = '20px';
                        notification.style.right = '20px';
                        notification.style.minWidth = '300px';
                        notification.style.zIndex = '9999';
                        
                        notification.innerHTML = `
                            <div class="toast-header bg-danger text-white">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong class="me-auto">Hata</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Kaydetme sırasında bir hata oluştu. Lütfen tekrar deneyin.
                            </div>
                        `;
                        
                        document.body.appendChild(notification);
                    });
                });
                
                // Geri düğmesi
                document.getElementById('studio-back').addEventListener('click', function() {
                    if (editor.getDirtyCount() > 0) {
                        if (confirm('Değişikliklerinizi kaydetmeden çıkmak istediğinize emin misiniz?')) {
                            window.location.href = '{{ route('admin.page.index') }}';
                        }
                    } else {
                        window.location.href = '{{ route('admin.page.index') }}';
                    }
                });
            });
        </script>
    </div>
</div>