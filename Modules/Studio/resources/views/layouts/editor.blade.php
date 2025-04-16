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
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/studio-grapes-overrides.css') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">

    @livewireStyles
</head>
<body class="studio-editor-body">
    <div class="studio-header">
        <div class="header-left">
            <a href="{{ url()->previous() }}" class="btn btn-back me-2" id="btn-back" title="Geri">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            
            <div class="editor-toolbar btn-group me-2">
                <button id="sw-visibility" class="btn btn-tool btn-icon" title="Bileşen sınırlarını göster/gizle">
                    <i class="fas fa-border-all"></i>
                </button>
                
                <button id="cmd-clear" class="btn btn-tool btn-icon" title="İçeriği temizle">
                    <i class="fas fa-trash-alt"></i>
                </button>
                
                <button id="cmd-undo" class="btn btn-tool btn-icon" title="Geri al">
                    <i class="fas fa-undo"></i>
                </button>
                
                <button id="cmd-redo" class="btn btn-tool btn-icon" title="Yinele">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>
        
        <div class="header-center">
            <div class="studio-brand">
                Studio <i class="fa-solid fa-wand-magic-sparkles mx-2 text-primary"></i> Editor
            </div>
        </div>
        
        <div class="header-right">
            <div class="device-btns btn-group me-2">
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
            
            <div class="code-btns btn-group me-2">
                <button id="cmd-code-edit" class="btn btn-tool" title="HTML Düzenle">
                    <i class="fas fa-code me-1"></i>
                    <span>HTML</span>
                </button>
                
                <button id="cmd-css-edit" class="btn btn-tool" title="CSS Düzenle">
                    <i class="fas fa-paint-brush me-1"></i>
                    <span>CSS</span>
                </button>
            </div>
            
            <button id="export-btn" class="btn btn-tool me-2" title="Dışa Aktar">
                <i class="fas fa-download me-1"></i>
                <span>Dışa Aktar</span>
            </button>
            
            <button id="preview-btn" class="btn btn-view me-2" title="Önizleme">
                <i class="fa-solid fa-eye me-1"></i>
                <span>Önizleme</span>
            </button>
            
            <button id="save-btn" class="btn btn-save" title="Kaydet">
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

    <!-- GrapesJS Core -->
    <script src="{{ asset('admin/libs/studio/grapes.min.js') }}"></script>
    
    <!-- Studio Modülleri -->
    <script src="{{ asset('admin/libs/studio/partials/studio-fix.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-utils.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-html-parser.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-blocks.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-ui.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-actions.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-core.js') }}"></script>
    <script src="{{ asset('admin/libs/js-cookie@3.0.5/js.cookie.min.js') }}"></script>
    <script src="{{ asset('admin/libs/studio/app.js') }}"></script>
    
    @livewireScripts
    
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>
</body>
</html>