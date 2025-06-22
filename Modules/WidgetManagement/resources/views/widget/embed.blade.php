@php
// CSS ve JS dosyalarını ayıkla
$cssFiles = [];
$jsFiles = [];

if (!empty($widget->content_html)) {
    preg_match_all('/<link[^>]+href=[\'"]([^\'"]+)[\'"][^>]*>/i', $widget->content_html, $cssMatches);
    if (!empty($cssMatches[1])) {
        $cssFiles = $cssMatches[1];
    }

    preg_match_all('/<script[^>]+src=[\'"]([^\'"]+)[\'"][^>]*><\/script>/i', $widget->content_html, $jsMatches);
    if (!empty($jsMatches[1])) {
        $jsFiles = $jsMatches[1];
    }
}

// Widget Service
$widgetService = app('widget.service');

// Widget HTML içeriğini render et
$renderedHtml = $widgetService->renderWidgetHtml($widget, $context, false);

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

<div id="widget-embed-content-{{ $tenantWidgetId }}" class="dark:bg-gray-800 dark:text-white">
    <div id="widget-content-{{ $tenantWidgetId }}">
        @if($widget->type == 'file')
            @include('widgetmanagement::blocks.' . $widget->file_path, ['settings' => $context])
        @else
            {!! $renderedHtml !!}
        @endif
    </div>
</div>

@if(!empty($processedCss))
<style>
    {!! $processedCss !!}
    
    /* Gece modu için ek stiller */
    .dark #widget-embed-content-{{ $tenantWidgetId }} {
        background-color: #1f2937;
        color: #f3f4f6;
    }
    
    .dark #widget-content-{{ $tenantWidgetId }} {
        background-color: #1f2937;
        color: #f3f4f6;
    }
</style>
@endif


@foreach($cssFiles as $cssFile)
    @if(!empty($cssFile))
        <link rel="stylesheet" href="{{ $cssFile }}">
    @endif
@endforeach


@foreach($jsFiles as $jsFile)
    @if(!empty($jsFile))
        <script src="{{ $jsFile }}"></script>
    @endif
@endforeach

@if(!empty($processedJs))
<script>
    (function() {
        try {
            // Tema moduyla ilgili Event Listener ekle
            document.addEventListener('themeChanged', function(e) {
                const widgetContainer = document.getElementById('widget-embed-content-{{ $tenantWidgetId }}');
                if (e.detail.mode === 'dark') {
                    widgetContainer.classList.add('dark-mode');
                } else {
                    widgetContainer.classList.remove('dark-mode');
                }
            });
            
            {!! $processedJs !!}
        } catch (error) {
            console.error('Widget JS hatası:', error);
        }
    })();
</script>
@endif