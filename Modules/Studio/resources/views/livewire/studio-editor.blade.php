<div><div>
    <div style="display:none">
        <textarea id="css-content">{!! $css !!}</textarea>
        <textarea id="js-content">{!! $js !!}</textarea>
    </div>
    
    <div class="editor-main">
        <!-- Sol Panel: Bloklar -->
        <div class="panel__left">
            <div class="blocks-search">
                <input type="text" id="blocks-search" class="form-control form-control-sm" placeholder="Bileşen ara...">
            </div>
            <div id="blocks-container" class="blocks-container"></div>
        </div>
        
        <!-- Orta Panel: Canvas -->
        <div class="editor-canvas">
            <div id="gjs"></div>
        </div>
        
        <!-- Sağ Panel: Özellikler -->
        <div class="panel__right">
            <div class="panel-tabs">
                <div class="panel-tab active" data-tab="styles">Stiller</div>
                <div class="panel-tab" data-tab="traits">Özellikler</div>
                <div class="panel-tab" data-tab="layers">Katmanlar</div>
            </div>
            
            <div class="panel-tab-content active" data-tab-content="styles">
                <div id="styles-container" class="styles-container"></div>
            </div>
            <div class="panel-tab-content" data-tab-content="traits">
                <div id="traits-container" class="traits-container"></div>
            </div>
            <div class="panel-tab-content" data-tab-content="layers">
                <div id="layers-container" class="layers-container"></div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // GrapesJS Editor yapılandırması
            const editor = grapesjs.init({
                container: '#gjs',
                fromElement: false,
                height: '100%',
                width: '100%',
                storageManager: false,
                panels: { defaults: [] },
                blockManager: {
                    appendTo: '#blocks-container',
                    blocks: [
                        {
                            id: 'section',
                            label: 'Bölüm',
                            category: 'Temel',
                            content: '<section class="section"><div class="container"><h2>Bölüm Başlığı</h2><p>İçeriğiniz burada yer alacak.</p></div></section>',
                            attributes: { class: 'fa fa-puzzle-piece' }
                        },
                        {
                            id: 'text',
                            label: 'Metin',
                            category: 'Temel',
                            content: '<div data-gjs-type="text">Metin içeriği buraya gelecek.</div>',
                            attributes: { class: 'fa fa-text-width' }
                        },
                        {
                            id: 'heading',
                            label: 'Başlık',
                            category: 'Temel',
                            content: '<h2>Başlık</h2>',
                            attributes: { class: 'fa fa-heading' }
                        },
                        {
                            id: 'image',
                            label: 'Görsel',
                            category: 'Temel',
                            select: true,
                            content: { type: 'image' },
                            attributes: { class: 'fa fa-image' }
                        },
                        {
                            id: 'video',
                            label: 'Video',
                            category: 'Temel',
                            select: true,
                            content: { type: 'video' },
                            attributes: { class: 'fa fa-film' }
                        },
                        {
                            id: 'link',
                            label: 'Bağlantı',
                            category: 'Temel',
                            content: { type: 'link', content: 'Bağlantı' },
                            attributes: { class: 'fa fa-link' }
                        },
                        {
                            id: 'button',
                            label: 'Düğme',
                            category: 'Temel',
                            content: '<button class="btn btn-primary">Tıkla</button>',
                            attributes: { class: 'fa fa-square' }
                        },
                        {
                            id: 'container',
                            label: 'Konteyner',
                            category: 'Düzen',
                            content: '<div class="container"></div>',
                            attributes: { class: 'fa fa-square-o' }
                        },
                        {
                            id: 'row',
                            label: 'Satır',
                            category: 'Düzen',
                            content: '<div class="row"></div>',
                            attributes: { class: 'fa fa-ellipsis-h' }
                        },
                        {
                            id: 'column',
                            label: 'Sütun',
                            category: 'Düzen',
                            content: '<div class="col"></div>',
                            attributes: { class: 'fa fa-ellipsis-v' }
                        },
                        {
                            id: 'column-3',
                            label: '3 Sütun',
                            category: 'Düzen',
                            content: '<div class="row"><div class="col-md-4">Sütun 1</div><div class="col-md-4">Sütun 2</div><div class="col-md-4">Sütun 3</div></div>',
                            attributes: { class: 'fa fa-columns' }
                        },
                        {
                            id: 'form',
                            label: 'Form',
                            category: 'Form',
                            content: '<form><div class="mb-3"><label class="form-label">Ad Soyad</label><input type="text" class="form-control" placeholder="Adınız Soyadınız"></div><div class="mb-3"><label class="form-label">E-posta</label><input type="email" class="form-control" placeholder="E-posta adresiniz"></div><div class="mb-3"><label class="form-label">Mesaj</label><textarea class="form-control" rows="3"></textarea></div><button type="submit" class="btn btn-primary">Gönder</button></form>',
                            attributes: { class: 'fa fa-wpforms' }
                        }
                    ]
                },
                styleManager: {
                    appendTo: '#styles-container'
                },
                layerManager: {
                    appendTo: '#layers-container'
                },
                traitManager: {
                    appendTo: '#traits-container'
                },
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
                            widthMedia: '480px',
                        }
                    ]
                },
                canvas: {
                    scripts: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js'
                    ],
                    styles: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css',
                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
                    ]
                },
                plugins: [
                    'grapesjs-blocks-basic',
                    'grapesjs-preset-webpage',
                    'grapesjs-style-bg',
                ],
                pluginsOpts: {
                    'grapesjs-preset-webpage': {
                        textCleanCanvas: 'İçeriği temizlemek istediğinize emin misiniz?',
                        showStylesOnChange: true,
                        importPlaceholder: 'HTML/CSS kodu yapıştırın',
                        modalImportTitle: 'Kod İçe Aktar',
                        modalImportLabel: 'HTML veya CSS kodunu yapıştırın',
                        modalImportContent: ''
                    }
                }
            });
            
            // Önceden oluşturulmuş içeriği yükle
            editor.setComponents(`{!! str_replace(['\\', '`', '"'], ['\\\\', '\\`', '\\"'], $content) !!}`);
            editor.setStyle(`{!! str_replace(['\\', '`', '"'], ['\\\\', '\\`', '\\"'], $css) !!}`);
            
            // Arama fonksiyonu
            const searchInput = document.getElementById('blocks-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    const blocks = document.querySelectorAll('.gjs-block');
                    
                    blocks.forEach(block => {
                        const label = block.querySelector('.gjs-block-label');
                        if (label) {
                            const text = label.textContent.toLowerCase();
                            if (text.includes(query)) {
                                block.style.display = '';
                            } else {
                                block.style.display = 'none';
                            }
                        }
                    });
                });
            }
            
            // Tab değiştirme fonksiyonu
            const tabs = document.querySelectorAll('.panel-tab');
            const tabContents = document.querySelectorAll('.panel-tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Aktif tab değiştir
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // İçeriği değiştir
                    tabContents.forEach(content => {
                        if (content.getAttribute('data-tab-content') === tabName) {
                            content.classList.add('active');
                        } else {
                            content.classList.remove('active');
                        }
                    });
                });
            });
            
            // Kaydetme
            document.getElementById('save-btn').addEventListener('click', function() {
                const htmlContent = editor.getHtml();
                const cssContent = editor.getCss();
                const jsContent = document.getElementById('js-content').value;
                
                fetch('{{ route("admin.studio.save", ["module" => $moduleType, "id" => $moduleId]) }}', {
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
                    if (data.success) {
                        alert('İçerik başarıyla kaydedildi.');
                    } else {
                        alert('Hata: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Kaydetme hatası:', error);
                    alert('İçerik kaydedilirken bir hata oluştu.');
                });
            });

            // Önizleme butonu
            document.getElementById('preview-btn').addEventListener('click', function() {
                editor.runCommand('preview');
            });
            
            console.log('Editor yüklendi');
        });
    </script>
    
    <style>
        /* Editor Ana Stiller */
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .editor-main {
            position: absolute;
            top: 50px; /* Header yüksekliği */
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        
        /* Sol Panel */
        .panel__left {
            width: 260px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        
        .blocks-search {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .blocks-container {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        /* Orta Panel (Canvas) */
        .editor-canvas {
            flex: 1;
            position: relative;
            background-color: #f5f5f5;
            overflow: hidden;
        }
        
        #gjs {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }
        
        /* Sağ Panel */
        .panel__right {
            width: 260px;
            background-color: #fff;
            border-left: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        
        .panel-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
        }
        
        .panel-tab {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
            border-bottom: 2px solid transparent;
        }
        
        .panel-tab.active {
            color: #206bc4;
            border-bottom-color: #206bc4;
        }
        
        .panel-tab-content {
            display: none;
            flex: 1;
            overflow-y: auto;
            height: calc(100% - 42px);
        }
        
        .panel-tab-content.active {
            display: block;
        }
        
        .styles-container,
        .traits-container,
        .layers-container {
            height: 100%;
            padding: 10px;
            overflow-y: auto;
        }
        
        /* GrapesJS Stil Geçersiz Kılmaları */
        .gjs-block {
            width: auto !important;
            height: auto !important;
            min-height: 40px !important;
            padding: 10px !important;
            text-align: center !important;
            font-size: 12px !important;
            border: 1px solid #ddd !important;
            border-radius: 5px !important;
            margin: 10px 5px !important;
            background-color: #fff !important;
            transition: all 0.2s ease !important;
        }
        
        .gjs-block:hover {
            box-shadow: 0 3px 6px rgba(0,0,0,0.1) !important;
            transform: translateY(-2px) !important;
            border-color: #0d6efd !important;
        }
        
        .gjs-block-category {
            padding: 10px 5px !important;
        }
        
        .gjs-category-title {
            font-weight: 600 !important;
            color: #495057 !important;
            font-size: 14px !important;
            padding: 10px 5px !important;
        }
        
        .gjs-one-bg {
            background-color: #ffffff !important;
        }
        
        .gjs-two-color {
            color: #383838 !important;
        }
        
        .gjs-four-color {
            color: #0d6efd !important;
        }
        
        .gjs-four-color-h:hover {
            color: #0a58ca !important;
        }
        
        .gjs-frame-wrapper {
            height: 100% !important;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .panel__left, .panel__right {
                width: 230px;
            }
        }
        
        @media (max-width: 768px) {
            .editor-main {
                flex-direction: column;
            }
            
            .panel__left, .panel__right {
                width: 100%;
                height: 200px;
            }
            
            .panel__left {
                border-right: none;
                border-bottom: 1px solid #dee2e6;
            }
            
            .panel__right {
                border-left: none;
                border-top: 1px solid #dee2e6;
            }
        }
    </style>
</div>