<div>
    <div>
        <div class="gjs-wrapper">
            <div id="blocks-panel">
                <div id="blocks" class="blocks-container"></div>
            </div>
            
            <div id="gjs">{!! $content !!}</div>
            
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
                    plugins: [
                        'grapesjs-blocks-basic',
                        'grapesjs-preset-webpage',
                        'grapesjs-style-bg',
                        'grapesjs-plugin-export',
                        'grapesjs-plugin-forms',
                        'grapesjs-custom-code',
                        'grapesjs-touch',
                        'grapesjs-component-countdown',
                        'grapesjs-tabs',
                        'grapesjs-typed',
                        'grapesjs-lang-tr',
                        'grapesjs-widget-component'
                    ],
                    pluginsOpts: {
                        'grapesjs-preset-webpage': {
                            textCleanCanvas: 'Bu sayfayı temizlemek istediğinize emin misiniz?'
                        }
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
                    panels: {
                        defaults: [{
                            id: 'layers',
                            el: '.panel__right',
                            resizable: {
                                maxDim: 350,
                                minDim: 250,
                                tc: 0,
                                cl: 1,
                                cr: 0,
                                bc: 0,
                                keyHeight: 'height',
                                keyWidth: 'width',
                            },
                        }, {
                            id: 'panel-switcher',
                            el: '.panel__switcher',
                            buttons: [{
                                id: 'show-layers',
                                active: true,
                                label: 'Katmanlar',
                                command: 'show-layers',
                                togglable: false,
                            }, {
                                id: 'show-style',
                                active: true,
                                label: 'Stiller',
                                command: 'show-styles',
                                togglable: false,
                            }, {
                                id: 'show-traits',
                                active: true,
                                label: 'Özellikler',
                                command: 'show-traits',
                                togglable: false,
                            }],
                        }, {
                            id: 'panel-devices',
                            el: '.panel__devices',
                            buttons: [{
                                id: 'device-desktop',
                                label: 'Masaüstü',
                                command: 'set-device-desktop',
                                active: true,
                                togglable: false,
                            }, {
                                id: 'device-tablet',
                                label: 'Tablet',
                                command: 'set-device-tablet',
                                togglable: false,
                            }, {
                                id: 'device-mobile',
                                label: 'Mobil',
                                command: 'set-device-mobile',
                                togglable: false,
                            }],
                        }]
                    },
                    blockManager: {
                        appendTo: '#blocks',
                        blocks: [
                            {
                                id: 'section',
                                label: 'Bölüm',
                                category: 'basic',
                                content: '<section class="py-5"><div class="container"><h2>Bölüm Başlığı</h2></div></section>',
                                attributes: { class: 'fa fa-square-full' }
                            },
                            {
                                id: 'container',
                                label: 'Konteyner',
                                category: 'basic',
                                content: '<div class="container"></div>',
                                attributes: { class: 'fa fa-square' }
                            },
                            {
                                id: 'text',
                                label: 'Metin',
                                category: 'basic',
                                content: '<div data-gjs-type="text">Buraya metin girin</div>',
                                attributes: { class: 'fa fa-font' }
                            },
                            {
                                id: 'image',
                                label: 'Resim',
                                category: 'basic',
                                content: { type: 'image' },
                                attributes: { class: 'fa fa-image' }
                            },
                            {
                                id: 'video',
                                label: 'Video',
                                category: 'basic',
                                content: {
                                    type: 'video',
                                    src: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                                    style: {
                                        width: '100%',
                                        height: '300px'
                                    }
                                },
                                attributes: { class: 'fa fa-video' }
                            },
                            {
                                id: 'link',
                                label: 'Bağlantı',
                                category: 'basic',
                                content: '<a href="#">Bağlantı</a>',
                                attributes: { class: 'fa fa-link' }
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
                                id: 'list',
                                label: 'Liste',
                                category: 'typography',
                                content: '<ul><li>Liste öğesi 1</li><li>Liste öğesi 2</li><li>Liste öğesi 3</li></ul>',
                                attributes: { class: 'fa fa-list-ul' }
                            },
                            {
                                id: 'card',
                                label: 'Kart',
                                category: 'components',
                                content: '<div class="card"><div class="card-body"><h5 class="card-title">Kart Başlığı</h5><p class="card-text">Kart içeriği buraya gelir.</p><a href="#" class="btn btn-primary">Düğme</a></div></div>',
                                attributes: { class: 'fa fa-credit-card' }
                            },
                            {
                                id: 'button',
                                label: 'Düğme',
                                category: 'components',
                                content: '<button class="btn btn-primary">Düğme</button>',
                                attributes: { class: 'fa fa-square' }
                            },
                            {
                                id: 'row',
                                label: 'Satır',
                                category: 'layout',
                                content: '<div class="row"></div>',
                                attributes: { class: 'fa fa-columns' }
                            },
                            {
                                id: 'column',
                                label: 'Sütun',
                                category: 'layout',
                                content: '<div class="col"></div>',
                                attributes: { class: 'fa fa-column' }
                            },
                            {
                                id: 'row-2-col',
                                label: '2 Sütun',
                                category: 'layout',
                                content: '<div class="row"><div class="col-md-6"></div><div class="col-md-6"></div></div>',
                                attributes: { class: 'fa fa-columns' }
                            },
                            {
                                id: 'row-3-col',
                                label: '3 Sütun',
                                category: 'layout',
                                content: '<div class="row"><div class="col-md-4"></div><div class="col-md-4"></div><div class="col-md-4"></div></div>',
                                attributes: { class: 'fa fa-columns' }
                            },
                            {
                                id: 'row-4-col',
                                label: '4 Sütun',
                                category: 'layout',
                                content: '<div class="row"><div class="col-md-3"></div><div class="col-md-3"></div><div class="col-md-3"></div><div class="col-md-3"></div></div>',
                                attributes: { class: 'fa fa-columns' }
                            },
                            {
                                id: 'accordion',
                                label: 'Akordeon',
                                category: 'components',
                                content: `<div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Akordeon Öğesi #1
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <strong>Bu ilk akordeon öğesinin içeriği.</strong> İçeriği buraya ekleyin.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingTwo">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        Akordeon Öğesi #2
                                                    </button>
                                                </h2>
                                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <strong>Bu ikinci akordeon öğesinin içeriği.</strong> İçeriği buraya ekleyin.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`,
                                attributes: { class: 'fa fa-bars' }
                            },
                            {
                                id: 'alert',
                                label: 'Uyarı',
                                category: 'components',
                                content: '<div class="alert alert-primary" role="alert">Bu bir uyarı mesajıdır!</div>',
                                attributes: { class: 'fa fa-exclamation-circle' }
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
                
                // Widget kategorileri ekle
                editor.BlockManager.getCategories().add([
                    { id: 'basic', label: 'Temel' },
                    { id: 'typography', label: 'Tipografi' },
                    { id: 'layout', label: 'Düzen' },
                    { id: 'components', label: 'Bileşenler' },
                    { id: 'widget', label: 'Widgetlar' }
                ]);
                
                // Widgetları blok olarak ekle
                @foreach($widgets as $widget)
                    editor.BlockManager.add('widget-{{ $widget['id'] }}', {
                        label: '{{ $widget['name'] }}',
                        category: '{{ $widget['category'] ?? 'widget' }}',
                        content: {
                            type: 'widget',
                            widget_id: {{ $widget['id'] }},
                            content: {!! json_encode($widget['content_html'] ?? '<div class="widget-placeholder">Widget: ' . $widget['name'] . '</div>') !!}
                        },
                        attributes: { class: 'fa fa-puzzle-piece' }
                    });
                @endforeach
                
                // Custom CSS ve JS düzenleyicileri
                editor.Panels.addPanel({
                    id: 'panel-css-js',
                    visible: true,
                    buttons: [
                        {
                            id: 'open-css',
                            className: 'fa fa-css3',
                            command: 'open-css',
                            attributes: { title: 'CSS Düzenle' }
                        },
                        {
                            id: 'open-js',
                            className: 'fa fa-js',
                            command: 'open-js',
                            attributes: { title: 'JavaScript Düzenle' }
                        }
                    ]
                });
                
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
                            attributes: { class: 'gjs-css-editor' }
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
                
                // JavaScript Editör Modalı
                editor.Commands.add('open-js', {
                    run: function(editor, sender) {
                        const jsContent = document.getElementById('js-content').textContent;
                        
                        const modal = editor.Modal.open({
                            title: 'JavaScript Düzenle',
                            content: `<textarea id="js-editor" style="width: 100%; height: 400px; font-family: monospace;">${jsContent}</textarea>
                                    <div class="modal-footer mt-3">
                                        <button id="js-save" class="btn btn-primary">Kaydet</button>
                                        <button id="js-cancel" class="btn btn-secondary">İptal</button>
                                    </div>`,
                            attributes: { class: 'gjs-js-editor' }
                        });
                        
                        document.getElementById('js-save').addEventListener('click', function() {
                            const jsEditor = document.getElementById('js-editor');
                            document.getElementById('js-content').textContent = jsEditor.value;
                            editor.Modal.close();
                        });
                        
                        document.getElementById('js-cancel').addEventListener('click', function() {
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
            });
        </script>
    </div>
</div>