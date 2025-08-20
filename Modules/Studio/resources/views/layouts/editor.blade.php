<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($pageTitle) ? $pageTitle . ' - ' : '' }}Studio Editor</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Roboto+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    
    <link rel="stylesheet" href="{{ asset('admin-assets/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">

    
    <link rel="stylesheet" href="{{ asset('admin-assets/libs/bootstrap/dist/css/bootstrap.min.css') }}">

    
    <link rel="stylesheet" href="{{ asset('admin-assets/libs/monaco-custom/css/monaco-custom.css') }}">

    
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/grapes.min.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/grapes.min.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/core.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/core.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/layout.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/layout.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/panel.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/panel.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/toolbar.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/toolbar.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/forms.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/forms.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/canvas.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/canvas.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/components.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/components.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/layers.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/layers.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/colors.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/colors.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/devices.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/devices.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/modal.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/modal.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/toast.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/toast.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/context-menu.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/context-menu.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/style-manager.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/style-manager.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/responsive.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/responsive.css')) }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/utils.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/utils.css')) }}">

    
    <link rel="stylesheet"
        href="{{ asset('admin-assets/libs/studio/css/quick-editor.css') }}?v={{ filemtime(public_path('admin-assets/libs/studio/css/quick-editor.css')) }}">

    @livewireStyles
    
    <style>
        .studio-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.25rem;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            position: relative;
        }
        
        .header-left, .header-left-secondary {
            display: flex;
            align-items: center;
        }
        
        .header-left-secondary {
            position: absolute;
            right: 320px;
        }
        
        .header-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .studio-language-switch.active {
            background-color: #0d6efd;
            color: white;
        }
    </style>
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

        <div class="header-left-secondary">
            <!-- Studio Dil Seçici (Hem arayüz hem içerik) -->
            <div class="dropdown me-3">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="studioLanguageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-language me-1"></i>
                    @php
                        $currentLocale = request()->route('locale') ?? 'tr';
                    @endphp
                    <span id="current-studio-lang-label">{{ strtoupper($currentLocale) }}</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="studioLanguageDropdown">
                    @php
                        try {
                            $tenantLanguages = DB::table('tenant_languages')
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->select('code', 'name', 'native_name', 'flag_icon')
                                ->get();
                        } catch (\Exception $e) {
                            $tenantLanguages = collect([
                                (object) ['code' => 'tr', 'name' => 'Turkish', 'native_name' => 'Türkçe', 'flag_icon' => '🇹🇷']
                            ]);
                        }
                    @endphp
                    @foreach($tenantLanguages as $studioLang)
                        <li>
                            <a class="dropdown-item studio-language-switch {{ $currentLocale == $studioLang->code ? 'active' : '' }}" 
                               href="{{ route('admin.studio.editor', ['module' => request()->route('module') ?? 'page', 'id' => request()->route('id') ?? 1, 'locale' => $studioLang->code]) }}">
                                @if(!empty($studioLang->flag_icon))
                                    <span class="me-2">{{ $studioLang->flag_icon }}</span>
                                @endif
                                {{ $studioLang->native_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
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

    
    <script src="{{ asset('admin-assets/libs/jquery@3.7.1/jquery.min.js') }}"></script>

    
    <script src="{{ asset('admin-assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin-assets/libs/handlebars/handlebars.min.js') }}"></script>

    
    <script src="{{ asset('admin-assets/libs/monaco-custom/js/monaco-custom.js') }}"></script>

    
    <script
        src="{{ asset('admin-assets/libs/studio/grapes.min.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/grapes.min.js')) }}">
    </script>

    
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-config.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-config.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-loader.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-loader.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-utils-modal.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-utils-modal.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-utils-notification.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-utils-notification.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-fix.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-fix.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-utils.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-utils.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-html-parser.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-html-parser.js')) }}">
    </script>

    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-ui-tabs.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-ui-tabs.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-ui-panels.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-ui-panels.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-ui-devices.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-ui-devices.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-ui.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-ui.js')) }}">
    </script>

    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-blocks-category.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-blocks-category.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-blocks-manager.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-blocks-manager.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-blocks.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-blocks.js')) }}">
    </script>

    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-actions-save.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-actions-save.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-actions-export.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-actions-export.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-actions.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-actions.js')) }}">
    </script>

    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-widget-components.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-widget-components.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-widget-loader.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-widget-loader.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-widget-manager.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-widget-manager.js')) }}">
    </script>

    
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-quick-editor.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-quick-editor.js')) }}">
    </script>

    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-editor-setup.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-editor-setup.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/studio/partials/studio-core.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/partials/studio-core.js')) }}">
    </script>
    <script
        src="{{ asset('admin-assets/libs/js-cookie@3.0.5/js.cookie.min.js') }}?v={{ filemtime(public_path('admin-assets/libs/js-cookie@3.0.5/js.cookie.min.js')) }}">
    </script>
    <script src="{{ asset('admin-assets/libs/studio/app.js') }}?v={{ filemtime(public_path('admin-assets/libs/studio/app.js')) }}">
    </script>

    <!-- Studio Basitleştirilmiş Sistem - Sadece URL ile dil değiştirme -->
    <script>
        $(document).ready(function() {
            // Basit toast sistemi
            function showToast(message, type = 'info') {
                const colorClass = type === 'success' ? 'success' : 
                                 type === 'error' ? 'danger' : 
                                 type === 'warning' ? 'warning' : 'info';
                
                const toastHtml = `
                    <div class="toast align-items-center text-white bg-${colorClass} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">${message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                
                $('.toast-container').append(toastHtml);
                const $toast = $('.toast-container .toast:last');
                const toast = new bootstrap.Toast($toast[0], { delay: 3000 });
                toast.show();
                
                // Toast kaldırıldıktan sonra DOM'dan temizle
                $toast.on('hidden.bs.toast', function() {
                    $(this).remove();
                });
            }
            
            // Global olarak erişilebilir hale getir
            window.StudioLanguage = {
                showToast: showToast
            };
        });
    </script>

    @livewireScripts

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999;"></div>
</body>

</html>