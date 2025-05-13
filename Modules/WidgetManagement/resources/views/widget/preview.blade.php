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

// CSS ve JS dosyalarını ayıkla
preg_match_all('/<link[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/i', $widget->content_html, $cssMatches);
$cssFiles = !empty($cssMatches[1]) ? $cssMatches[1] : [];

preg_match_all('/<script[^>]+src=[\'"]([^\'"]+)[\'"][^>]*><\/script>/i', $widget->content_html, $jsMatches);
$jsFiles = !empty($jsMatches[1]) ? $jsMatches[1] : [];

// Widget Service
$widgetService = app('widget.service');

// Widget HTML içeriğini render et
$renderedHtml = $widgetService->renderWidgetHtml($widget, $context, false);
@endphp

@include("themes.{$themeFolder}.layouts.header")

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
            @if($widget->type == 'module' && empty($widget->file_path))
                <div class="bg-yellow-100 border border-yellow-400 p-4 rounded-md text-yellow-800">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Bu modül bileşeni için HTML şablonu tanımlanmamış. Lütfen widget'ı düzenleyin ve bir HTML şablonu ekleyin.
                </div>
            @elseif($widget->type == 'file')
                @include('widgetmanagement::blocks.' . $widget->file_path, ['settings' => $context])
            @else
                {!! $renderedHtml !!}
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
    @php
    // Widget CSS içeriğini de işle
    $processedCss = $widget->content_css ?? '';
    
    if (!empty($processedCss)) {
        // CSS içindeki değişkenleri işle
        $processedCss = preg_replace_callback('/\{\{([^{}]+?)\}\}/m', function($matches) use ($context) {
            $key = trim($matches[1]);
            
            // {{widget.var}} formatında mı?
            if (strpos($key, 'widget.') === 0) {
                $widgetKey = str_replace('widget.', '', $key);
                if (isset($context['widget'][$widgetKey])) {
                    return $context['widget'][$widgetKey];
                } elseif (isset($context[$widgetKey])) {
                    return $context[$widgetKey];
                }
            }
            
            // Normal değişken mi?
            if (isset($context[$key])) {
                if (is_scalar($context[$key])) {
                    return $context[$key];
                }
            }
            
            return '';
        }, $processedCss);
    }
    @endphp
    
    {!! $processedCss !!}
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
<script>
    @php
    // Widget JS içeriğini de işle
    $processedJs = $widget->content_js ?? '';
    
    if (!empty($processedJs)) {
        // JS içindeki değişkenleri işle
        $processedJs = preg_replace_callback('/\{\{([^{}]+?)\}\}/m', function($matches) use ($context) {
            $key = trim($matches[1]);
            
            // {{widget.var}} formatında mı?
            if (strpos($key, 'widget.') === 0) {
                $widgetKey = str_replace('widget.', '', $key);
                if (isset($context['widget'][$widgetKey])) {
                    return $context['widget'][$widgetKey];
                } elseif (isset($context[$widgetKey])) {
                    return $context[$widgetKey];
                }
            }
            
            // Normal değişken mi?
            if (isset($context[$key])) {
                if (is_scalar($context[$key])) {
                    return $context[$key];
                }
            }
            
            return '';
        }, $processedJs);
    }
    @endphp
    
    {!! $processedJs !!}
</script>

@include("themes.{$themeFolder}.layouts.footer")