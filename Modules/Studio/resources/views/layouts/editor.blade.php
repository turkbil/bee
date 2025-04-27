<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' : '' }}Studio Editor</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('admin/libs/bootstrap/dist/css/bootstrap.min.css') }}">
    
    <!-- GrapesJS CSS -->
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/grapes.min.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/grapes.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/core.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/core.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/layout.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/layout.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/panel.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/panel.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/toolbar.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/toolbar.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/forms.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/forms.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/canvas.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/canvas.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/components.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/components.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/layers.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/layers.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/colors.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/colors.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/devices.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/devices.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/modal.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/modal.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/toast.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/toast.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/context-menu.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/context-menu.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/style-manager.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/style-manager.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/responsive.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/responsive.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/utils.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/utils.css')) }}">

    @livewireStyles
</head>
<body class="studio-editor-body">
    <div class="studio-header">
        <div class="header-left">
            <div class="btn-group btn-group-sm me-4">
                <button id="device-desktop" class="btn btn-light btn-sm active" title="Masaüstü">
                    <i class="fas fa-desktop"></i>
                </button>
                <button id="device-tablet" class="btn btn-light btn-sm" title="Tablet">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button id="device-mobile" class="btn btn-light btn-sm" title="Mobil">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>

            <div class="btn-group btn-group-sm me-4">
                <button id="sw-visibility" class="btn btn-light btn-sm" title="Bileşen sınırlarını göster/gizle">
                    <i class="fas fa-border-all"></i>
                </button>
                
                <button id="cmd-clear" class="btn btn-light btn-sm" title="İçeriği temizle">
                    <i class="fas fa-trash-alt"></i>
                </button>
                
                <button id="cmd-undo" class="btn btn-light btn-sm" title="Geri al">
                    <i class="fas fa-undo"></i>
                </button>
                
                <button id="cmd-redo" class="btn btn-light btn-sm" title="Yinele">
                    <i class="fas fa-redo"></i>
                </button>
            </div>

        </div>
        
        <div class="header-center">
            <div class="studio-brand">
                Studio <i class="fa-solid fa-wand-magic-sparkles mx-2"></i> Editor
            </div>
        </div>
        
        <div class="header-right">

            
            <button id="cmd-code-edit" class="btn btn-light btn-sm" title="HTML Düzenle">
                <i class="fas fa-code me-1"></i>
                <span>HTML</span>
            </button>
            
            <button id="cmd-css-edit" class="btn btn-light btn-sm" title="CSS Düzenle">
                <i class="fas fa-paint-brush me-1"></i>
                <span>CSS</span>
            </button>

            <button id="preview-btn" class="btn btn-light btn-sm me-2" title="Önizleme">
                <i class="fa-solid fa-eye me-1"></i>
                <span>Önizleme</span>
            </button>
            
            <button id="save-btn" class="btn btn-primary btn-sm" title="Kaydet">
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
    
    <!-- Bootstrap JS -->
    <script src="{{ asset('admin/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        
    <!-- GrapesJS Core -->
    <script src="{{ asset('admin/libs/studio/grapes.min.js') }}?v={{ filemtime(public_path('admin/libs/studio/grapes.min.js')) }}"></script>
    
    <!-- Studio Modülleri -->
    <script src="{{ asset('admin/libs/studio/partials/studio-config.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-config.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-loader.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-loader.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-utils-modal.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-utils-modal.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-utils-notification.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-utils-notification.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-fix.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-fix.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-utils.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-utils.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-html-parser.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-html-parser.js')) }}"></script>

    <script src="{{ asset('admin/libs/studio/partials/studio-ui-tabs.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-ui-tabs.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-ui-panels.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-ui-panels.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-ui-devices.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-ui-devices.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-ui.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-ui.js')) }}"></script>

    <script src="{{ asset('admin/libs/studio/partials/studio-blocks-category.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-blocks-category.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-blocks-manager.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-blocks-manager.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-blocks.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-blocks.js')) }}"></script>

    <script src="{{ asset('admin/libs/studio/partials/studio-actions-save.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-actions-save.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-actions-export.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-actions-export.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-actions.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-actions.js')) }}"></script>

    <script src="{{ asset('admin/libs/studio/partials/studio-widget-components.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-widget-components.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-widget-loader.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-widget-loader.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-widget-manager.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-widget-manager.js')) }}"></script>

    <script src="{{ asset('admin/libs/studio/partials/studio-editor-setup.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-editor-setup.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-core.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-core.js')) }}"></script>
    <script src="{{ asset('admin/libs/js-cookie@3.0.5/js.cookie.min.js') }}?v={{ filemtime(public_path('admin/libs/js-cookie@3.0.5/js.cookie.min.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/app.js') }}?v={{ filemtime(public_path('admin/libs/studio/app.js')) }}"></script>

    @livewireScripts
    
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;"></div>
</body>
</html>