@php
// Theme_id'ye göre theme'i bul
$theme = null;
if (function_exists('tenant') && tenant() && tenant()->theme_id) {
    $theme = \Modules\ThemeManagement\App\Models\Theme::find(tenant()->theme_id);
}

// Theme bulunamadıysa varsayılan olarak blank temasını kullan
if (!$theme) {
    $theme = \Modules\ThemeManagement\App\Models\Theme::where('is_default', true)->first();
    if (!$theme) {
        $theme = new \Modules\ThemeManagement\App\Models\Theme([
            'name' => 'blank',
            'folder_name' => 'blank'
        ]);
    }
}

$themeFolder = $theme->folder_name ?? 'blank';

// HTML içindeki CSS ve JS'leri çıkar
$contentHtml = $widget->content_html ?? '';
preg_match_all('/<link[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/i', $contentHtml, $cssMatches);
$cssFiles = !empty($cssMatches[1]) ? $cssMatches[1] : [];

preg_match_all('/<script[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $contentHtml, $jsMatches);
$jsFiles = !empty($jsMatches[1]) ? $jsMatches[1] : [];
@endphp

@include("themes.{$themeFolder}.layouts.header")

@php
ob_start();
@endphp

<div class="preview-header">
    <div class="container-fluid flex justify-between items-center">
        <h1 class="text-lg font-semibold m-0">{{ $widget->name }} Önizleme</h1>
        <div>
            <button class="px-3 py-1 text-sm border border-white text-white rounded hover:bg-white hover:text-blue-600 transition-colors" onclick="window.close()">
                <i class="fas fa-times me-1"></i> Kapat
            </button>
        </div>
    </div>
</div>

<div class="preview-container">
    <div class="device-switcher">
        <button class="px-3 py-2 bg-white border border-blue-500 text-blue-600 rounded-md hover:bg-blue-50 active" onclick="setPreviewSize('desktop')">
            <i class="fas fa-desktop me-1"></i> Masaüstü
        </button>
        <button class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50" onclick="setPreviewSize('tablet')">
            <i class="fas fa-tablet-alt me-1"></i> Tablet
        </button>
        <button class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-md hover:bg-gray-50" onclick="setPreviewSize('mobile')">
            <i class="fas fa-mobile-alt me-1"></i> Mobil
        </button>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 p-4 rounded-md mb-4 text-blue-800">
        <div class="flex">
            <div class="mr-2">
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
                <div class="bg-yellow-100 border border-yellow-400 p-4 rounded-md text-yellow-800">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu modül bileşeni için HTML şablonu tanımlanmamış. Lütfen widget'ı düzenleyin ve bir HTML şablonu ekleyin.
                </div>
            @else
                <script id="widget-template" type="text/x-handlebars-template">
                    {!! $widget->content_html !!}
                </script>
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
    
    <div class="mt-4 flex justify-between items-center">
        <div>
            <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-xs me-2">{{ ucfirst($widget->type) }}</span>
            
            @if($widget->has_items)
            <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-xs me-2">Dinamik İçerik</span>
            @endif
            
            <span class="bg-green-500 text-white px-2 py-1 rounded-full text-xs">{{ $widget->category->title ?? 'Kategori Yok' }}</span>
        </div>
        
        @if($widget->type == 'module')
        <a href="{{ route('admin.widgetmanagement.modules') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-arrow-left me-1"></i> Listeye Dön
        </a>
        @elseif(auth()->user()->hasRole('root'))
        <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            <i class="fas fa-edit me-1"></i> Widget'ı Düzenle
        </a>
        @endif
    </div>
</div>

@php
$content = ob_get_clean();
echo app('widget.resolver')->resolveWidgetContent($content);
@endphp

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

<style>
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

<!-- CSS Dosyaları -->
@foreach($cssFiles as $cssFile)
    @if(!empty($cssFile))
        <link rel="stylesheet" href="{{ $cssFile }}">
    @endif
@endforeach

<!-- JS Dosyaları -->
@foreach($jsFiles as $jsFile)
    @if(!empty($jsFile))
        <script src="{{ $jsFile }}"></script>
    @endif
@endforeach

<!-- Widget JavaScript -->
<script type="module">
    {!! $widget->content_js ?? '' !!}
</script>

@include("themes.{$themeFolder}.layouts.footer")