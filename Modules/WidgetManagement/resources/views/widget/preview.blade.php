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
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1.5rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .preview-metadata {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e6e7e9;
        }
        
        .preview-content {
            padding: 1.5rem;
            border: 1px solid #e6e7e9;
            border-radius: 4px;
            min-height: 300px;
        }
        
        .preview-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e6e7e9;
            font-size: 0.875rem;
            color: #626976;
        }
        
        /* Widget CSS */
        {!! $widget->content_css !!}
    </style>
</head>
<body>
    <div class="preview-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h3 m-0">Widget Önizleme</h1>
            <div>
                <button class="btn btn-outline-light btn-sm" onclick="window.close()">
                    <i class="fas fa-times me-1"></i> Kapat
                </button>
            </div>
        </div>
    </div>
    
    <div class="preview-container">
        <div class="preview-metadata">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <strong>Widget Adı:</strong> {{ $widget->name }}
                    </div>
                    <div class="mb-2">
                        <strong>Tip:</strong> {{ $widget->type }}
                    </div>
                    <div>
                        <strong>Açıklama:</strong> {{ $widget->description ?: 'Açıklama bulunmuyor' }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu bir önizlemedir. Widgetın gerçek ortamdaki görünümünden farklılık gösterebilir. Dinamik içerikler ve tenant özel ayarları bu önizlemede görünmeyebilir.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="preview-content">
            <!-- Widget HTML -->
            {!! $widget->content_html !!}
        </div>
        
        <div class="preview-footer text-end">
            <a href="{{ route('admin.widgetmanagement.manage', $widget->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i> Widget'ı Düzenle
            </a>
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
    
    <!-- Widget JavaScript -->
    <script>
        {!! $widget->content_js !!}
    </script>
</body>
</html>