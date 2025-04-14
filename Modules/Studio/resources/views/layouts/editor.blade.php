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
    
    <!-- Studio CSS -->
    @studiocss
    <!-- Edit stillerini ekleyelim -->
    <style>
        /* Bileşen düzenleme için ek stiller */
        .gjs-selected {
            outline: 2px solid #3b82f6 !important;
            outline-offset: 2px !important;
        }
        
        .gjs-toolbar {
            background-color: #3b82f6 !important;
            border-radius: 4px !important;
        }
        
        /* Canvas dropzone için stiller */
        .gjs-droppable-active {
            outline: 2px dashed #3b82f6 !important;
            outline-offset: 5px !important;
            background-color: rgba(59, 130, 246, 0.05) !important;
        }
        
        .panel-tab.active {
            color: #3b82f6;
            border-bottom: 2px solid #3b82f6;
            background-color: #fff;
        }
        
        /* Akordiyon stiller */
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
            border-color: #3b82f6;
        }
        
        /* Bileşen blokları için stiller */
        .block-item {
            cursor: grab;
            transition: all 0.2s ease;
        }
        
        .block-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .block-item.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }
        .block-item.dragging {
            opacity: 0.5;
            cursor: move;
        }

        .gjs-droppable-active {
            outline: 2px dashed #4CAF50;
            outline-offset: -2px;
        }
    </style>
    <style>
        /* Acil durum stilleri */
        .studio-header {
            height: 60px;
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            z-index: 100;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }
        
        .page-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding-top: 60px;
        }
        
        .editor-main {
            display: flex;
            height: calc(100vh - 60px);
            overflow: hidden;
        }
        
        .panel__left {
            width: 280px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .panel-tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f8f9fa;
        }
        
        .panel-tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
            border-bottom: 2px solid transparent;
        }
        
        .panel-tab:hover {
            color: #3b82f6;
            background-color: #e2e8f0;
        }
        
        .panel-tab.active {
            color: #3b82f6;
            border-bottom: 2px solid #3b82f6;
            background-color: #fff;
        }
        
        .panel-tab-content {
            flex: 1;
            overflow-y: auto;
            display: none;
            padding: 15px;
        }
        
        .panel-tab-content.active {
            display: block;
        }
        
        .editor-canvas {
            flex: 1;
            position: relative;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        
        #gjs {
            height: 100%;
            width: 100%;
        }
        
        /* Araç çubuğu düğmeleri */
        .btn-tool {
            color: #475569;
            background-color: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
            margin-right: 5px;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
        }
        
        .btn-tool:hover {
            background-color: #f1f5f9;
        }
        
        .btn-tool.active {
            background-color: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }
        
        .device-btns {
            display: flex;
            align-items: center;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .device-btns button {
            background: transparent;
            border: none;
            padding: 6px 10px;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .device-btns button:not(:last-child) {
            border-right: 1px solid #e2e8f0;
        }
        
        .device-btns button:hover {
            background-color: #f1f5f9;
        }
        
        .device-btns button.active {
            background-color: #3b82f6;
            color: #ffffff;
        }
        </style>
    
    @livewireStyles
</head>
<body>
    <div class="studio-header">
        <div class="header-left">
            <a href="{{ url()->previous() }}" class="btn btn-back me-2">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            
            <button id="sw-visibility" class="btn btn-tool btn-icon me-1" title="Bileşen sınırlarını göster/gizle">
                <i class="fas fa-eye"></i>
            </button>
            
            <button id="cmd-clear" class="btn btn-tool btn-icon me-1" title="İçeriği temizle">
                <i class="fas fa-trash-alt"></i>
            </button>
            
            <button id="cmd-undo" class="btn btn-tool btn-icon me-1" title="Geri al">
                <i class="fas fa-undo"></i>
            </button>
            
            <button id="cmd-redo" class="btn btn-tool btn-icon me-1" title="Yinele">
                <i class="fas fa-redo"></i>
            </button>
        </div>
        
        <div class="header-center">
            <div class="studio-brand">
                Studio <i class="fa-solid fa-wand-magic-sparkles mx-2"></i>
                Editor
            </div>
        </div>
        
        <div class="header-right">
            <div class="device-btns me-2">
                <button id="device-desktop" class="active" title="Masaüstü">
                    <i class="fas fa-desktop"></i>
                </button>
                <button id="device-tablet" title="Tablet">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button id="device-mobile" title="Mobil">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>
            
            <button id="cmd-code-edit" class="btn btn-tool me-1" title="HTML Düzenle">
                <i class="fas fa-code"></i>
                <span>HTML</span>
            </button>
            
            <button id="cmd-css-edit" class="btn btn-tool me-1" title="CSS Düzenle">
                <i class="fas fa-paint-brush"></i>
                <span>CSS</span>
            </button>
            
            <button id="cmd-js-edit" class="btn btn-tool me-1" title="JavaScript Düzenle">
                <i class="fas fa-file-code"></i>
                <span>JS</span>
            </button>
            
            <button type="button" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#themeModal">
                <i class="fas fa-palette me-1"></i>
                <span>Tema</span>
            </button>
            
            <button id="preview-btn" class="btn btn-info me-1" title="Önizleme">
                <i class="fa-solid fa-eye me-1"></i>
                <span>Önizleme</span>
            </button>
            
            <button id="save-btn" class="btn btn-success" title="Kaydet">
                <i class="fa-solid fa-save me-1"></i>
                <span>Kaydet</span>
            </button>
        </div>
    </div>
    
    <div class="page-wrapper">        
        {{ $slot }}
    </div>
    
    <!-- jQuery -->
    <script src="{{ asset('admin/libs/jquery@3.7.1/jquery.min.js') }}"></script>
        
    <!-- Tabler JS -->
    <script src="{{ asset('admin/js/tabler.min.js') }}"></script>

    <!-- Studio JS -->
    @studiojs
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seçili bileşen olduğunda traits panelini aktifleştir
            document.addEventListener('studio:component-selected', function(event) {
                const traitsTab = document.querySelector('.panel-tab[data-tab="traits"]');
                if (traitsTab) {
                    setTimeout(() => traitsTab.click(), 100);
                }
            });
            
            // Blokların sürükle-bırak davranışını iyileştir
            document.addEventListener('studio:editor-ready', function() {
                const blockItems = document.querySelectorAll('.block-item');
                blockItems.forEach(item => {
                    item.setAttribute('draggable', 'true');
                    
                    item.addEventListener('dragstart', function(e) {
                        const blockId = this.getAttribute('data-block-id');
                        e.dataTransfer.setData('text/plain', blockId);
                        e.dataTransfer.effectAllowed = 'copy';
                        this.classList.add('dragging');
                    });
                    
                    item.addEventListener('dragend', function() {
                        this.classList.remove('dragging');
                    });
                });
            });
        });
    </script>
    
    @livewireScripts
    
    @stack('scripts')
</body>
</html>