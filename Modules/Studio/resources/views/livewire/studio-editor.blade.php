<div class="gjs-wrapper">
    <div id="gjs">{!! $content !!}</div>
    
    <div id="blocks-panel"></div>
    <div id="layer-panel"></div>
    
    <div id="hidden-styles" style="display: none;">
        <style id="gjs-custom-css">
            {!! $css !!}
        </style>
        <script id="gjs-custom-js" type="text/javascript">
            {!! $js !!}
        </script>
    </div>
    
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            // GrapesJS editorünü başlat
            const editor = grapesjs.init({
                container: '#gjs',
                fromElement: true,
                height: '100%',
                width: 'auto',
                storageManager: false,
                panels: {
                    defaults: [
                        {
                            id: 'basic-actions',
                            el: '.panel__basic-actions',
                            buttons: [
                                { id: 'save', className: 'btn-save', label: 'Kaydet' }
                            ],
                        },
                        {
                            id: 'layers',
                            el: '#layer-panel',
                            resizable: {
                                maxDim: 350,
                                minDim: 200,
                            },
                        }
                    ]
                },
                blockManager: {
                    appendTo: '#blocks-panel',
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
                        }
                    ]
                }
            });
            
            // Widgetları blok olarak ekle
            @foreach($widgets as $widget)
                editor.BlockManager.add('widget-{{ $widget['id'] }}', {
                    label: '{{ $widget['name'] }}',
                    category: 'widget',
                    content: '<div class="widget" data-widget-id="{{ $widget['id'] }}">Widget: {{ $widget['name'] }}</div>',
                    attributes: { class: 'fa fa-puzzle-piece' }
                });
            @endforeach
            
            // Kaydet butonu işlevi
            editor.Commands.add('save-command', {
                run: function(editor, sender) {
                    const htmlContent = editor.getHtml();
                    const cssContent = document.getElementById('gjs-custom-css').innerHTML;
                    const jsContent = document.getElementById('gjs-custom-js').innerHTML;
                    
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
                        if (data.success) {
                            alert('İçerik başarıyla kaydedildi.');
                            window.location.href = '{{ route('admin.page.index') }}';
                        } else {
                            alert('Kaydetme sırasında bir hata oluştu: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Kaydetme hatası:', error);
                        alert('Kaydetme sırasında bir hata oluştu. Lütfen tekrar deneyin.');
                    });
                }
            });
            
            editor.Panels.getButton('basic-actions', 'save').set('command', 'save-command');
            
            // Kaydet butonu için DOM elementi
            document.getElementById('studio-save').addEventListener('click', function() {
                editor.runCommand('save-command');
            });
            
            // Geri dönüş butonu
            document.getElementById('studio-back').addEventListener('click', function() {
                if (confirm('Değişikliklerinizi kaydetmeden çıkmak istediğinize emin misiniz?')) {
                    window.location.href = '{{ route('admin.page.index') }}';
                }
            });
        });
    </script>
</div>