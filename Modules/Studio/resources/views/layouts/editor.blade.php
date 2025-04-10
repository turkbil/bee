<!-- Modules/Studio/resources/views/layouts/editor.blade.php -->
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
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/grapes.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/studio-editor.css') }}">
    
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
        
        .editor-toolbar {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        
        .toolbar-group {
            display: flex;
            margin-right: 8px;
        }
        
        .toolbar-divider {
            width: 1px;
            height: 24px;
            background-color: #dee2e6;
            margin: 0 8px;
        }
        
        .toolbar-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #495057;
            margin-right: 4px;
        }
        
        .toolbar-btn:hover {
            background-color: #f1f3f5;
        }
        
        .toolbar-btn.active {
            background-color: #e9ecef;
            color: #206bc4;
        }
        
        .device-btns {
            margin-left: auto;
        }
        
        /* Editor Ana Stilleri */
        .editor-main {
            display: flex;
            height: calc(100vh - 136px); /* Navbar + toolbar + debug bar yüksekliği */
            overflow: hidden;
        }
        
        /* Sol Panel */
        .panel__left {
            width: 260px;
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        
        .blocks-search {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .blocks-container {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        /* Orta Panel (Canvas) */
        .editor-canvas {
            flex: 1;
            position: relative;
            background-color: #f5f5f5;
            overflow: hidden;
        }
        
        #gjs {
            height: 100%;
            width: 100%;
        }
        
        /* Sağ Panel */
        .panel__right {
            width: 260px;
            background-color: #fff;
            border-left: 1px solid #dee2e6;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        
        .panel-tabs {
            display: flex;
            border-bottom: 1px solid #dee2e6;
        }
        
        .panel-tab {
            padding: 10px 15px;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
            border-bottom: 2px solid transparent;
        }
        
        .panel-tab.active {
            color: #206bc4;
            border-bottom-color: #206bc4;
        }
        
        .panel-tab-content {
            display: none;
            flex: 1;
            overflow-y: auto;
            height: calc(100% - 42px);
        }
        
        .panel-tab-content.active {
            display: block;
        }
        
        .debug-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background-color: #2c3e50;
            color: white;
            z-index: 1000;
        }
        
        /* GrapesJS stil geçersiz kılmaları */
        .gjs-cv-canvas {
            width: 100% !important;
            height: 100% !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        .gjs-block {
            width: auto !important;
            height: auto !important;
            min-height: 40px !important;
            padding: 10px !important;
            text-align: center !important;
            font-size: 12px !important;
            border: 1px solid #ddd !important;
            border-radius: 5px !important;
            margin: 10px 5px !important;
            background-color: #fff !important;
            transition: all 0.2s ease !important;
        }
        
        .gjs-block:hover {
            box-shadow: 0 3px 6px rgba(0,0,0,0.1) !important;
            transform: translateY(-2px) !important;
            border-color: #0d6efd !important;
        }
        
        .gjs-block-category {
            padding: 10px 5px !important;
        }
        
        .gjs-one-bg {
            background-color: #ffffff !important;
        }
        
        .gjs-two-color {
            color: #383838 !important;
        }
        
        .gjs-four-color {
            color: #0d6efd !important;
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
        <div class="editor-toolbar">
            <div class="toolbar-group">
                <button class="toolbar-btn" id="sw-visibility" title="Bileşen sınırlarını göster/gizle">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="toolbar-btn" id="cmd-clear" title="İçeriği temizle">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            
            <div class="toolbar-divider"></div>
            
            <div class="toolbar-group">
                <button class="toolbar-btn" id="cmd-undo" title="Geri al">
                    <i class="fas fa-undo"></i>
                </button>
                <button class="toolbar-btn" id="cmd-redo" title="Yinele">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
            
            <div class="toolbar-divider"></div>
            
            <div class="toolbar-group">
                <button class="toolbar-btn" id="cmd-code-edit" title="HTML Düzenle">
                    <i class="fas fa-code"></i>
                </button>
                <button class="toolbar-btn" id="cmd-css-edit" title="CSS Düzenle">
                    <i class="fas fa-paint-brush"></i>
                </button>
                <button class="toolbar-btn" id="cmd-js-edit" title="JS Düzenle">
                    <i class="fas fa-file-code"></i>
                </button>
            </div>
            
            <div class="toolbar-divider"></div>
            
            <div class="toolbar-group">
                <button class="toolbar-btn" id="export-btn" title="Dışa Aktar">
                    <i class="fas fa-download"></i>
                </button>
            </div>
            
            <div class="toolbar-group device-btns">
                <button class="toolbar-btn active" id="device-desktop" title="Masaüstü">
                    <i class="fas fa-desktop"></i>
                </button>
                <button class="toolbar-btn" id="device-tablet" title="Tablet">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button class="toolbar-btn" id="device-mobile" title="Mobil">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>
        </div>
        
        {{ $slot }}
    </div>
    
    <!-- jQuery -->
    <script src="{{ asset('admin/libs/jquery@3.7.1/jquery.min.js') }}"></script>
    
    <!-- Tabler JS -->
    <script src="{{ asset('admin/js/tabler.min.js') }}"></script>
    
    <!-- GrapesJS ve Eklentileri -->
    <script src="{{ asset('admin/libs/studio/grapes.min.js') }}"></script>

    <!-- Özel Studio JS -->
    <script src="{{ asset('admin/libs/studio/studio.js') }}"></script>
        
    @livewireScripts
</body>
</html>