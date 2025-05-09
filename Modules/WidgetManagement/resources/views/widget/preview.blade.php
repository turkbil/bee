<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Widget Önizleme - {{ $widget->name }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Widget CSS Dosya Bağlantıları -->
    @if(isset($widget->css_files) && is_array($widget->css_files))
        @foreach($widget->css_files as $cssFile)
            @if(!empty($cssFile))
                <link rel="stylesheet" href="{{ $cssFile }}">
            @endif
        @endforeach
    @endif
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fb;
        }
        
        .preview-header {
            background-color: #206bc4;
            color: white;
            padding: 0.75rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .preview-content {
            border: 1px solid #e6e7e9;
            border-radius: 4px;
            min-height: 200px;
            margin: 1rem 0;
            padding: 1rem;
        }
        
        .device-switcher {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .preview-frame {
            transition: width 0.3s ease;
            margin: 0 auto;
            width: 100%;
        }
        
        .preview-frame.mobile {
            max-width: 375px;
        }
        
        .preview-frame.tablet {
            max-width: 768px;
        }
        
        /* Widget CSS */
        {!! $widget->content_css ?? '' !!}
    </style>
</head>
<body>
    <div class="preview-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">{{ $widget->name }} Önizleme</h1>
            <div>
                <button class="btn btn-sm btn-outline-light" onclick="window.close()">
                    <i class="fas fa-times me-1"></i> Kapat
                </button>
            </div>
        </div>
    </div>
    
    <div class="preview-container">
        <div class="device-switcher">
            <button class="btn btn-outline-primary active" onclick="setPreviewSize('desktop')">
                <i class="fas fa-desktop me-1"></i> Masaüstü
            </button>
            <button class="btn btn-outline-primary" onclick="setPreviewSize('tablet')">
                <i class="fas fa-tablet-alt me-1"></i> Tablet
            </button>
            <button class="btn btn-outline-primary" onclick="setPreviewSize('mobile')">
                <i class="fas fa-mobile-alt me-1"></i> Mobil
            </button>
        </div>
        
        <div class="alert alert-info">
            <div class="d-flex">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                </div>
                <div>
                    <strong>Önizleme Bilgileri:</strong><br>
                    <strong>Tür:</strong> {{ ucfirst($widget->type) }}<br>
                    <strong>Açıklama:</strong> {{ $widget->description }}
                </div>
            </div>
        </div>
        
        <div class="preview-frame" id="preview-frame">
            <div class="preview-content">
                @if($widget->type == 'module')
                    <!-- Modül için file-preview veya şablon yoksa uyarı -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Bu modül bileşeni için HTML şablonu tanımlanmamış. Lütfen widget'ı düzenleyin ve bir HTML şablonu ekleyin.
                    </div>
                @else
                    <!-- Handlebars ile widget içeriği derleniyor -->
                    <script id="widget-template" type="text/x-handlebars-template">
                        {!! $widget->content_html !!}
                    </script>
                    {{-- Context verisi controller'dan gelmektedir --}}
                    <div id="widget-rendered"></div>
                    <script src="{{ asset('admin/libs/handlebars/handlebars.min.js') }}?v={{ filemtime(public_path('admin/libs/handlebars/handlebars.min.js')) }}"></script>
                    <script>
                        (function() {
                            // Handlebars helper fonksiyonları tanımlama
                            Handlebars.registerHelper('eq', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'eq' ihtiyaç duyduğu parametreleri almadı");
                                return v1 === v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('ne', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'ne' ihtiyaç duyduğu parametreleri almadı");
                                return v1 !== v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('lt', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'lt' ihtiyaç duyduğu parametreleri almadı");
                                return v1 < v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('gt', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'gt' ihtiyaç duyduğu parametreleri almadı");
                                return v1 > v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('lte', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'lte' ihtiyaç duyduğu parametreleri almadı");
                                return v1 <= v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('gte', function(v1, v2, options) {
                                if (arguments.length < 3)
                                    throw new Error("Handlebars Helper 'gte' ihtiyaç duyduğu parametreleri almadı");
                                return v1 >= v2 ? options.fn(this) : options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('and', function() {
                                var options = arguments[arguments.length - 1];
                                for (var i = 0; i < arguments.length - 1; i++) {
                                    if (!arguments[i]) {
                                        return options.inverse(this);
                                    }
                                }
                                return options.fn(this);
                            });
                            
                            Handlebars.registerHelper('or', function() {
                                var options = arguments[arguments.length - 1];
                                for (var i = 0; i < arguments.length - 1; i++) {
                                    if (arguments[i]) {
                                        return options.fn(this);
                                    }
                                }
                                return options.inverse(this);
                            });
                            
                            Handlebars.registerHelper('truncate', function(str, len) {
                                if (!str || !len) {
                                    return str;
                                }
                                if (str.length > len) {
                                    return str.substring(0, len) + '...';
                                }
                                return str;
                            });
                            
                            Handlebars.registerHelper('formatDate', function(date, format) {
                                // Basit tarih biçimlendirme
                                if (!date) return '';
                                var d = new Date(date);
                                if (isNaN(d.getTime())) return date;
                                
                                var day = d.getDate().toString().padStart(2, '0');
                                var month = (d.getMonth() + 1).toString().padStart(2, '0');
                                var year = d.getFullYear();
                                
                                return day + '.' + month + '.' + year;
                            });
                            
                            Handlebars.registerHelper('json', function(context) {
                                return JSON.stringify(context);
                            });

                            const data = @json($context);
                            const template = Handlebars.compile(
                                document.getElementById('widget-template').innerHTML
                            );
                            document.getElementById('widget-rendered').innerHTML = template(data);
                        })();
                    </script>
                @endif
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-blue me-2">{{ ucfirst($widget->type) }}</span>
                
                @if($widget->has_items)
                <span class="badge bg-orange me-2">Dinamik İçerik</span>
                @endif
                
                <span class="badge bg-green">{{ $widget->category->title ?? 'Kategori Yok' }}</span>
            </div>
            
            @if($widget->type == 'module')
            <a href="{{ route('admin.widgetmanagement.modules') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Listeye Dön
            </a>
            @elseif(auth()->user()->hasRole('root'))
            <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Widget'ı Düzenle
            </a>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Widget JS Dosya Bağlantıları -->
    @if(isset($widget->js_files) && is_array($widget->js_files))
        @foreach($widget->js_files as $jsFile)
            @if(!empty($jsFile))
                <script src="{{ $jsFile }}"></script>
            @endif
        @endforeach
    @endif
    
    <!-- Önizleme Frame Boyutlandırma -->
    <script>
        function setPreviewSize(device) {
            const frame = document.getElementById('preview-frame');
            const buttons = document.querySelectorAll('.device-switcher button');
            
            // Aktif butonları temizle
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Aktif butonu işaretle
            event.target.closest('button').classList.add('active');
            
            // Frame boyutunu ayarla
            frame.className = 'preview-frame';
            if (device !== 'desktop') {
                frame.classList.add(device);
            }
        }
    </script>
    
    <!-- Widget JavaScript -->
    <script type="module">
        {!! $widget->content_js ?? '' !!}
    </script>
</body>
</html>