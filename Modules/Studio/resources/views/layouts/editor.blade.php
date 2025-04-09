<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' : '' }}Studio Editor</title>
    
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/tabler-vendors.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">
    
    <!-- GrapesJS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.8/dist/css/grapes.min.css">
    
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            height: 50px;
            flex-shrink: 0;
            background-color: #206bc4;
            padding: 0 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }
        
        .navbar-left {
            display: flex;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 18px;
            font-weight: bold;
            margin-right: 15px;
            color: white;
        }
        
        .navbar-right {
            display: flex;
            align-items: center;
        }
        
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            position: relative;
        }
    </style>
    
    @livewireStyles
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <div class="navbar-brand">
                Studio Editor - Sayfa Düzenleyici
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-sm btn-dark">
                <i class="fa-solid fa-arrow-left me-1"></i> Geri
            </a>
        </div>
        
        <div class="navbar-right">
            <button class="btn btn-sm btn-warning me-2" id="preview-btn">
                <i class="fa-solid fa-eye me-1"></i> Önizleme
            </button>
            <button class="btn btn-sm btn-success" id="save-btn">
                <i class="fa-solid fa-save me-1"></i> Kaydet
            </button>
        </div>
    </div>
    
    <div class="page-wrapper">
        {{ $slot }}
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Tabler JS -->
    <script src="{{ asset('admin/js/tabler.min.js') }}"></script>
    
    <!-- GrapesJS ve Eklentileri -->
    <script src="https://unpkg.com/grapesjs@0.21.8/dist/grapes.min.js"></script>
    <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
    <script src="https://unpkg.com/grapesjs-style-bg@1.0.5"></script>
    <script src="https://unpkg.com/grapesjs-plugin-export@1.0.11"></script>
    <script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5"></script>
    <script src="https://unpkg.com/grapesjs-custom-code@1.0.1"></script>
    <script src="https://unpkg.com/grapesjs-touch@0.1.1"></script>
    
    @livewireScripts
</body>
</html>