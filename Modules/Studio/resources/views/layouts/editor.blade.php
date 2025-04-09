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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GrapesJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs@0.21.8/dist/css/grapes.min.css">
    <link rel="stylesheet" href="{{ Module::asset('studio:css/studio-editor.css') }}">
    
    @livewireStyles
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        
        .editor-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .editor-header {
            height: 50px;
            background: #343a40;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 1rem;
            justify-content: space-between;
            z-index: 1000;
        }
        
        .editor-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-right: 2rem;
        }
        
        .gjs-wrapper {
            display: flex;
            flex: 1;
            position: relative;
            overflow: hidden;
        }
        
        #gjs {
            flex: 1;
            height: calc(100vh - 50px);
            overflow: visible !important;
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Editor içerik alanı için kritik stiller */
        .gjs-cv-canvas {
            position: relative !important;
            width: 100% !important;
            height: 100% !important;
            top: 0 !important;
            left: 0 !important;
            z-index: 10 !important;
            visibility: visible !important;
            display: block !important;
            overflow: visible !important;
        }
        
        .gjs-frame-wrapper {
            min-height: 400px !important;
        }
        
        .gjs-frame {
            z-index: 1 !important;
            position: relative !important;
            height: 100% !important;
            visibility: visible !important;
            display: block !important;
            min-height: 400px !important;
        }
        
        #blocks-panel {
            width: 250px;
            background: #f8f9fa;
            height: calc(100vh - 50px);
            overflow-y: auto;
            border-right: 1px solid #ddd;
            z-index: 100;
        }
        
        #layer-panel {
            width: 250px;
            background: #f8f9fa;
            height: calc(100vh - 50px);
            overflow-y: auto;
            border-left: 1px solid #ddd;
            z-index: 100;
        }
        
        .gjs-block {
            width: auto;
            height: auto;
            min-height: auto;
            padding: 5px;
        }
        
        .panel__right {
            overflow-y: auto;
            height: calc(100vh - 100px);
        }
        
        .panel__top {
            padding: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .blocks-container {
            padding: 10px 5px;
        }
        
        .trait-container, .styles-container, .layers-container {
            height: 100%;
            overflow-y: auto;
        }
        
        .widget-placeholder {
            padding: 10px;
            background-color: #f3f4f6;
            border: 1px dashed #ccc;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-radius: 4px;
        }
        
        /* Fix iframe visibility issues */
        iframe {
            display: block !important;
            visibility: visible !important;
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <div class="editor-header">
            <div class="d-flex align-items-center">
                <div class="editor-title">{{ $pageTitle ?? 'Studio Editor' }}</div>
                <div class="btn-group">
                    <button id="studio-back" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </button>
                </div>
            </div>
            
            <div class="panel__basic-actions">
                <button id="studio-save" class="btn btn-success">
                    <i class="fas fa-save"></i> Kaydet
                </button>
            </div>
        </div>
        
        {{ $slot }}
    </div>
    
    <!-- Tabler JS -->
    <script src="{{ asset('admin/js/tabler.min.js') }}"></script>
    
    <!-- GrapesJS sadece ana kütüphane -->
    <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.21.8/dist/grapes.min.js"></script>
    
    <!-- Diğer JS dosyaları çıkarıldı -->
    <script src="{{ Module::asset('studio:js/studio.js') }}"></script>
    
    @livewireScripts
</body>
</html>