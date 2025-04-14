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
    
    <!-- Manual UI initialization script to ensure everything is properly setup -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Panel sekmeleri için tıklama olay dinleyicileri ekle
        document.querySelectorAll('.panel-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                // Aktif sekmeyi değiştir
                document.querySelectorAll('.panel-tab').forEach(function(t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');
                
                // İçerik göster/gizle
                const tabId = this.getAttribute('data-tab');
                document.querySelectorAll('.panel-tab-content').forEach(function(content) {
                    content.classList.remove('active');
                });
                document.querySelector(`.panel-tab-content[data-tab-content="${tabId}"]`).classList.add('active');
            });
        });
        
        // Cihaz butonları için olay dinleyicileri
        document.querySelectorAll('.device-btns button').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Aktif düğmeyi değiştir
                document.querySelectorAll('.device-btns button').forEach(function(b) {
                    b.classList.remove('active');
                });
                this.classList.add('active');
                
                // Cihaz değiştir
                const deviceId = this.getAttribute('id');
                if (deviceId === 'device-desktop') {
                    window.editor.DeviceManager.select('desktop');
                } else if (deviceId === 'device-tablet') {
                    window.editor.DeviceManager.select('tablet');
                } else if (deviceId === 'device-mobile') {
                    window.editor.DeviceManager.select('mobile');
                }
            });
        });
        
        // Tema değişikliği bildirimi
        if (typeof Livewire !== 'undefined') {
            Livewire.on('theme-changed', function(event) {
                const modal = document.getElementById('themeModal');
                if (modal && bootstrap.Modal.getInstance(modal)) {
                    bootstrap.Modal.getInstance(modal).hide();
                }
                
                // Bildirim göster
                if (typeof StudioUI !== 'undefined') {
                    StudioUI.showNotification('Tema başarıyla değiştirildi', 'success');
                }
            });
        }
    });
    </script>
    
    @livewireScripts
    
    @stack('scripts')
</body>
</html>