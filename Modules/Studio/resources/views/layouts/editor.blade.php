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
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/grapes.min.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/grapes.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/studio-editor.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/studio-editor.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/studio-editor-ui.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/studio-editor-ui.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/studio/css/studio-grapes-overrides.css') }}?v={{ filemtime(public_path('admin/libs/studio/css/studio-grapes-overrides.css')) }}">

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
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                Studio Editor
            </div>
        </div>
        
        <div class="header-right">
            <div class="device-btns">
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
            
            <button id="export-btn" class="btn btn-tool me-2" title="Dışa Aktar">
                <i class="fas fa-download"></i>
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
    <script src="{{ asset('admin/libs/jquery@3.7.1/jquery.min.js') }}?v={{ filemtime(public_path('admin/libs/jquery@3.7.1/jquery.min.js')) }}"></script>
        
    <!-- Tabler JS -->
    <script src="{{ asset('admin/js/tabler.min.js') }}?v={{ filemtime(public_path('admin/js/tabler.min.js')) }}"></script>

    <!-- GrapesJS ve Eklentileri -->
    <script src="{{ asset('admin/libs/studio/grapes.min.js') }}?v={{ filemtime(public_path('admin/libs/studio/grapes.min.js')) }}"></script>

    <!-- Studio Düzeltme Modülü - ÖNCE YÜKLENMELİ -->
    <script src="{{ asset('admin/libs/studio/partials/studio-fix.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-fix.js')) }}"></script>

    <!-- Studio Modüler JS Dosyaları -->
    <script src="{{ asset('admin/libs/studio/partials/studio-utils.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-utils.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-plugins.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-plugins.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-plugins-loader.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-plugins-loader.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-core.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-core.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-blocks.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-blocks.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-ui.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-ui.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-actions.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-actions.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/partials/studio-init.js') }}?v={{ filemtime(public_path('admin/libs/studio/partials/studio-init.js')) }}"></script>

    <!-- Özel Studio JS -->
    <script src="{{ asset('admin/libs/studio/studio.js') }}?v={{ filemtime(public_path('admin/libs/studio/studio.js')) }}"></script>
    <script src="{{ asset('admin/libs/studio/app.js') }}?v={{ filemtime(public_path('admin/libs/studio/app.js')) }}"></script>

    <!-- Editor Yapılandırması - Doğrudan hidden input kullanıyoruz JSON ayrıştırma sorunları nedeniyle -->
    <div style="display:none;">
        <textarea id="html-content">{{ $content ?? '' }}</textarea>
        <textarea id="css-content">{{ $css ?? '' }}</textarea>
        <textarea id="js-content">{{ $js ?? '' }}</textarea>
    </div>
        
    @livewireScripts
</body>
</html>