<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hazır Dosya Önizleme - {{ $widget->name }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    </style>
</head>
<body>
    <div class="preview-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">{{ $widget->name }} - Hazır Dosya Önizleme</h1>
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
                    <strong>Dosya Bilgileri:</strong><br>
                    <strong>Dosya Yolu:</strong> {{ $viewPath }}<br>
                    <strong>Açıklama:</strong> {{ $widget->description }}
                </div>
            </div>
        </div>
        
        <div class="preview-frame" id="preview-frame">
            <div class="preview-content">
                <!-- Hazır Dosya İçeriği -->
                @include('widgetmanagement::blocks.' . $widget->file_path, ['settings' => [
                    'title' => $widget->name,
                    'unique_id' => Str::random()
                ]])
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-blue me-2">Hazır Dosya</span>
                <span class="badge bg-green">{{ $widget->category->title ?? 'Kategori Yok' }}</span>
            </div>
            <a href="{{ route('admin.widgetmanagement.file.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Listeye Dön
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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
</body>
</html>