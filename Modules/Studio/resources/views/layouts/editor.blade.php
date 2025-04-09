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
    <link rel="stylesheet" href="https://unpkg.com/grapesjs@0.21.8/dist/css/grapes.min.css">
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
        }
        
        #blocks-panel {
            width: 250px;
            background: #f8f9fa;
            height: calc(100vh - 50px);
            overflow-y: auto;
            border-right: 1px solid #ddd;
        }
        
        #layer-panel {
            width: 250px;
            background: #f8f9fa;
            height: calc(100vh - 50px);
            overflow-y: auto;
            border-left: 1px solid #ddd;
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
    
    <!-- GrapesJS JS -->
    <script src="https://unpkg.com/grapesjs@0.21.8/dist/grapes.min.js"></script>
    <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1/dist/grapesjs-blocks-basic.min.js"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2/dist/grapesjs-preset-webpage.min.js"></script>
    <script src="https://unpkg.com/grapesjs-style-bg@1.0.5/dist/grapesjs-style-bg.min.js"></script>
    <script src="https://unpkg.com/grapesjs-plugin-export@1.0.11/dist/grapesjs-plugin-export.min.js"></script>
    <script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5/dist/grapesjs-plugin-forms.min.js"></script>
    <script src="https://unpkg.com/grapesjs-custom-code@1.0.1/dist/grapesjs-custom-code.min.js"></script>
    <script src="https://unpkg.com/grapesjs-touch@0.1.1/dist/grapesjs-touch.min.js"></script>
    <script src="https://unpkg.com/grapesjs-component-countdown@1.0.1/dist/grapesjs-component-countdown.min.js"></script>
    <script src="https://unpkg.com/grapesjs-tabs@1.0.6/dist/grapesjs-tabs.min.js"></script>
    <script src="https://unpkg.com/grapesjs-typed@1.0.5/dist/grapesjs-typed.min.js"></script>
    <script src="{{ Module::asset('studio:js/studio.js') }}"></script>
    
    @livewireScripts
</body>
</html>