<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' : '' }}Studio Editor</title>
    
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/tabler.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/tabler-vendors.min.css') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- GrapesJS CSS -->
    @studiocss
    
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
    <script src="{{ asset('assets/js/tabler.min.js') }}"></script>
    
    <!-- GrapesJS JS -->
    @studiojs
    
    @livewireScripts
</body>
</html>