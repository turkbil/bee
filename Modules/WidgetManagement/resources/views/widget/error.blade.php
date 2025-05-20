<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Widget Hata - WidgetManagement</title>
    
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
        
        .error-header {
            background-color: #d63939;
            color: white;
            padding: 0.75rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .error-container {
            max-width: 1200px;
            margin: 1.5rem auto;
            padding: 1rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .error-content {
            border: 1px solid #e6e7e9;
            border-radius: 4px;
            margin: 1rem 0;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 m-0">Widget Hata</h1>
            <div>
                <button class="btn btn-sm btn-outline-light" onclick="window.close()">
                    <i class="fas fa-times me-1"></i> Kapat
                </button>
            </div>
        </div>
    </div>
    
    <div class="error-container">
        <div class="alert alert-danger">
            <div class="d-flex">
                <div>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                </div>
                <div>
                    <strong>Hata!</strong><br>
                    {!! $message ?? 'Widget yüklenirken bir hata oluştu.' !!}
                </div>
            </div>
        </div>
        
        <div class="error-content">
            <div class="empty">
                <div class="empty-img text-center">
                    <i class="fas fa-exclamation-circle fa-5x text-danger mb-3"></i>
                </div>
                <p class="empty-title text-center">Widget görüntülenemiyor</p>
                <p class="empty-subtitle text-muted text-center">
                    Lütfen widget yapılandırmasını kontrol edin.
                </p>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-end align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Geri Dön
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>