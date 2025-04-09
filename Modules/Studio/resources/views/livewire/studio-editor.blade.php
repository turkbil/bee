<div>
    <div>
        <div class="gjs-wrapper">
            <div id="blocks-panel">
                <div id="blocks" class="blocks-container"></div>
            </div>
            
            <div id="gjs" class="gjs-editor-cont">{!! $content !!}</div>
            
            <div id="layer-panel">
                <div class="panel__top">
                    <div class="panel__switcher"></div>
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
                <div class="panel__right">
                    <div class="layers-container"></div>
                    <div class="styles-container"></div>
                    <div class="trait-container"></div>
                </div>
            </div>
        </div>
        
        <div id="hidden-styles" style="display: none;">
            <pre id="css-content">{!! htmlspecialchars($css) !!}</pre>
            <pre id="js-content">{!! htmlspecialchars($js) !!}</pre>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // CSS stilleri oluştur
                const inlineCss = `
                    .gjs-frame {
                        position: relative !important;
                        display: block !important;
                        visibility: visible !important;
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
                    .gjs-dashed .gjs-container {
                        min-height: 50px !important;
                    }
                    iframe {
                        display: block !important;
                        visibility: visible !important;
                    }
                `;
                
                // Inline stilleri ekle
                const styleEl = document.createElement('style');
                styleEl.textContent = inlineCss;
                document.head.appendChild(styleEl);
                
                // CSS ve JS içeriğini DOM'a programatik olarak ekliyoruz
                const cssElement = document.createElement('style');
                cssElement.id = 'gjs-custom-css';
                cssElement.textContent = document.getElementById('css-content').textContent;
                document.head.appendChild(cssElement);
                
                // GrapesJS editorünü başlat
                const editor = grapesjs.init({
                    container: '#gjs',
                    fromElement: true,
                    height: '100%',
                    width: 'auto',
                    storageManager: false,
                    allowScripts: 1,
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
                        sectors: [{
                            name: 'Genel',
                            open: false,
                            properties: ['display', 'flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-basis', 'flex-grow', 'flex-shrink', 'align-self']
                        }, {
                            name: 'Boyut',
                            open: false,
                            properties: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding']
                        }, {
                            name: 'Yazı',
                            open: false,
                            properties: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow']
                        }, {
                            name: 'Arkaplan',
                            open: false,
                            properties: ['background-color', 'background-image', 'background-repeat', 'background-position', 'background-attachment', 'background-size']
                        }, {
                            name: 'Kenarlık',
                            open: false,
                            properties: ['border-radius', 'border', 'border-width', 'border-style', 'border-color']
                        }, {
                            name: 'Ekstra',
                            open: false,
                            properties: ['opacity', 'box-shadow', 'transition', 'transform']
                        }]
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
                    },
                    selectorManager: {
                        componentFirst: true
                    },
                    traitManager: {
                        appendTo: '.trait-container'
                    }
                });
                
                // Görünürlük için gerekli ayarlar
                editor.on('load', () => {
                    console.log('Canvas yüklendi, içerik gösteriliyor');
                    
                    // Düzeltme için manuel müdahaleler
                    setTimeout(() => {
                        try {
                            // iframe CSS stillerini doğrudan değiştir
                            const iframe = document.querySelector('.gjs-frame');
                            if (iframe) {
                                // iframe görünürlüğünü kesinlikle zorla
                                iframe.style.height = 'calc(100vh - 150px)';
                                iframe.style.minHeight = '500px';
                                iframe.style.display = 'block';
                                iframe.style.visibility = 'visible';
                                iframe.style.opacity = '1';
                                iframe.style.position = 'relative';
                                
                                // iframe'in içindeki body elementine ulaşmayı dene
                                try {
                                    if (iframe.contentDocument && iframe.contentDocument.body) {
                                        iframe.contentDocument.body.style.display = 'block';
                                        iframe.contentDocument.body.style.visibility = 'visible';
                                        iframe.contentDocument.body.style.opacity = '1';
                                    }
                                } catch (e) {
                                    console.log('iframe içeriğine erişilemedi:', e);
                                }
                            }
                            
                            // Canvas container ve diğer GrapesJS elementleri
                            const canvasEl = document.querySelector('.gjs-cv-canvas');
                            if (canvasEl) {
                                canvasEl.style.position = 'relative';
                                canvasEl.style.top = '0';
                                canvasEl.style.left = '0';
                                canvasEl.style.width = '100%';
                                canvasEl.style.height = 'calc(100vh - 150px)';
                                canvasEl.style.minHeight = '500px';
                                canvasEl.style.visibility = 'visible';
                                canvasEl.style.display = 'block';
                                canvasEl.style.zIndex = '10';
                            }
                            
                            // Yeni CSS kurallarını enjekte et
                            const forceStyle = document.createElement('style');
                            forceStyle.innerHTML = `
                                .gjs-frame {
                                    display: block !important;
                                    visibility: visible !important;
                                    height: calc(100vh - 150px) !important;
                                    min-height: 500px !important;
                                    opacity: 1 !important;
                                    position: relative !important;
                                }
                                .gjs-cv-canvas {
                                    position: relative !important;
                                    top: 0 !important;
                                    left: 0 !important;
                                    width: 100% !important;
                                    height: calc(100vh - 150px) !important;
                                    min-height: 500px !important;
                                    visibility: visible !important;
                                    display: block !important;
                                    overflow: visible !important;
                                }
                                .gjs-editor, .gjs-editor-cont {
                                    min-height: 500px !important;
                                }
                                iframe {
                                    display: block !important;
                                    visibility: visible !important;
                                }
                            `;
                            document.head.appendChild(forceStyle);
                            
                            // Editörü yeniden yükle
                            editor.refresh();
                        } catch (error) {
                            console.error('Canvas stil ayarları uygulanırken hata oluştu:', error);
                        }
                    }, 300);
                });
                
                // İçeriği yükledikten hemen sonra ekstra bir kontrol
                editor.on('component:selected', () => {
                    const iframe = document.querySelector('.gjs-frame');
                    if (iframe && iframe.style.display !== 'block') {
                        iframe.style.display = 'block';
                        iframe.style.visibility = 'visible';
                    }
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
                            content: `<textarea id="css-editor" style="width: 100%; height: 400px; font-family: monospace;">${cssContent}</textarea>
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
                
                // Cihaz düğmeleri için komutlar
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
                
                // Kaydet düğmesi işlevi
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
                            // Tabler bildirim göster
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
                            
                            // 3 saniye sonra sayfaya yönlendir
                            setTimeout(() => {
                                if ('{{ $moduleType }}' === 'page') {
                                    window.location.href = '{{ route('admin.page.index') }}';
                                }
                            }, 2000);
                        } else {
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
                                    ${data.message || 'Kaydetme sırasında bir hata oluştu.'}
                                </div>
                            `;
                            
                            document.body.appendChild(notification);
                        }
                    })
                    .catch(error => {
                        console.error('Kaydetme hatası:', error);
                        
                        // Butonu normal durumuna getir
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
                
                // 1 saniye sonra bir kez daha görünürlük kontrolü yap
                setTimeout(() => {
                    const iframe = document.querySelector('.gjs-frame');
                    if (iframe) {
                        iframe.style.display = 'block';
                        iframe.style.visibility = 'visible';
                        iframe.style.opacity = '1';
                        iframe.style.height = 'calc(100vh - 150px)';
                        iframe.style.minHeight = '500px';
                    }
                    
                    // Editörü yenile
                    editor.refresh();
                    
                    // İçerik görünmüyorsa sayfayı yenile
                    setTimeout(() => {
                        const visibleContent = document.querySelector('.gjs-frame:not([style*="display: none"])');
                        if (!visibleContent) {
                            console.log('İçerik hala görünmüyor, sayfayı yeniliyorum...');
                            window.location.reload();
                        }
                    }, 500);
                }, 1000);
            });
        </script>
    </div>
</div>