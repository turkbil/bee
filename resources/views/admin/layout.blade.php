<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ config('app.name') }} - @yield('title')</title>
    <!-- Sistem teması kontrolü - Sayfa yüklenmeden çalışır -->
    <script>
        (function() {
            var darkMode = "<?php echo isset($_COOKIE['dark']) ? $_COOKIE['dark'] : 'auto'; ?>";
            if(darkMode === 'auto') {
                // Sistem karanlık modunu kontrol et
                if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    document.documentElement.classList.add('dark');
                    document.documentElement.classList.remove('light');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    document.documentElement.classList.add('light');
                    document.documentElement.classList.remove('dark');
                }
            } else if(darkMode === '1') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.setAttribute('data-bs-theme', 'light');
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <!-- Google Fontları -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    @livewireStyles
    <link rel="stylesheet" href="{{ asset('admin/css/tabler.min.css') }}?v={{ filemtime(public_path('admin/css/tabler.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/css/theme-font-size.css') }}?v={{ filemtime(public_path('admin/css/theme-font-size.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/css/tabler-vendors.min.css') }}?v={{ filemtime(public_path('admin/css/tabler-vendors.min.css')) }}">
    @if (Str::contains(Request::url(), ['create', 'edit', 'manage', 'form']))
    @else
    @endif
    <link rel="stylesheet" href="{{ asset('admin/css/plugins.css') }}?v={{ filemtime(public_path('admin/css/plugins.css')) }}" />
    <link rel="stylesheet" href="{{ asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/libs/choices/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/choices-custom.css') }}?v={{ filemtime(public_path('admin/css/choices-custom.css')) }}">
    <link rel="stylesheet" href="{{ asset('admin/css/main.css') }}?v={{ filemtime(public_path('admin/css/main.css')) }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/theme-simple.css') }}?v={{ filemtime(public_path('admin/css/theme-simple.css')) }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/theme-font-size.css') }}?v={{ filemtime(public_path('admin/css/theme-font-size.css')) }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}?v={{ filemtime(public_path('admin/css/responsive.css')) }}" />
    @stack('styles') @stack('css')
    <style>
        :root {
            --primary-color: <?php echo isset($_COOKIE['siteColor']) ? $_COOKIE['siteColor']: '#066fd1';
            ?>;
            --primary-text-color: <?php echo isset($_COOKIE['siteTextColor']) ? $_COOKIE['siteTextColor']: '#ffffff';
            ?>;
            --tblr-font-family: <?php echo isset($_COOKIE['themeFont']) ? $_COOKIE['themeFont'] : 'Inter, Poppins, Roboto, system-ui, -apple-system, \'Segoe UI\', \'Helvetica Neue\', Arial, \'Noto Sans\', sans-serif'; ?>;
            --tblr-border-radius: <?php echo isset($_COOKIE['themeRadius']) ? $_COOKIE['themeRadius'] : '0.25rem'; ?>;
        }
        body {
            font-family: var(--tblr-font-family);
        }
    </style>
</head>
<body<?php
    $darkMode = isset($_COOKIE['dark']) ? $_COOKIE['dark'] : 'auto';
    $tableCompact = isset($_COOKIE['tableCompact']) ? $_COOKIE['tableCompact'] : '0';
    $themeFontSize = isset($_COOKIE['themeFontSize']) ? $_COOKIE['themeFontSize'] : 'small';
    
    // Sistem teması kontrolü için PHP tarafında
    $isDark = false;
    if ($darkMode == '1') {
        $isDark = true;
    } elseif ($darkMode == 'auto' && isset($_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'])) {
        // HTTP başlığından tarayıcı tercihini kontrol et (modern tarayıcılar için)
        $isDark = $_SERVER['HTTP_SEC_CH_PREFERS_COLOR_SCHEME'] === 'dark';
    }
    
    if ($isDark) {
        echo ' class="dark' . ($tableCompact == '1' ? ' table-compact' : '') . ' font-size-' . $themeFontSize . '" data-bs-theme="dark"';
    } else if ($darkMode == '0') {
        echo ' class="light' . ($tableCompact == '1' ? ' table-compact' : '') . ' font-size-' . $themeFontSize . '" data-bs-theme="light"';
    } else {
        // auto mode - başlangıçta nötr, JS ile kontrol edilecek
        echo ' class="' . (($tableCompact == '1') ? 'table-compact ' : '') . 'font-size-' . $themeFontSize . '"';
    }
?>>

<div class="page">
    @include('admin.components.navigation')
    
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                  @stack('pretitle')
                </div>
                <h2 class="page-title">
                  @stack('title')
                </h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  @stack('module-menu')
                </div>
              </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container">
                @hasSection('content')
                @yield('content') {{-- Blade sayfaları için --}}
                @else
                {{ $slot ?? '' }} {{-- Livewire bileşenleri için --}}
                @endif
            </div>
        </div>

        <footer class="footer footer-transparent d-print-none">
            <div class="container">
                <div class="row text-center align-items-center flex-row-reverse">
                    <div class="col-auto ms-auto">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary" rel="noopener">
                                    Dokümantasyon
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary" rel="noopener">
                                    Lisans
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary" rel="noopener">
                                    Kaynak Kodu
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary" rel="noopener">
                                    <i class="fa-thin fa-heart text-pink"></i>
                                    Sevgiyle kodlandı.
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                        <ul class="list-inline list-inline-dots mb-0">
                            <li class="list-inline-item">
                                Telif Hakkı &copy; {{ date('Y') }}
                                <a href="https://turkbilisim.com.tr" class="link-secondary" target="_blank"
                                    rel="noopener">
                                    Türk Bilişim
                                </a>.
                                Tüm hakları saklıdır.
                            </li>
                            <li class="list-inline-item">
                                <a href="#" class="link-secondary" rel="noopener">
                                    v1.0.0
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="{{ asset('admin/js/plugins.js') }}?v={{ filemtime(public_path('admin/js/plugins.js')) }}"></script>
<script src="{{ asset('admin/js/tabler.min.js') }}" defer></script>
<script src="{{ asset('admin/libs/litepicker/dist/litepicker.js') }}" defer></script>
<script src="{{ asset('admin/libs/fslightbox/index.js') }}" defer></script>
<script src="{{ asset('admin/libs/choices/choices.min.js') }}" defer></script>
<script src="{{ asset('admin/js/main.js') }}?v={{ filemtime(public_path('admin/js/main.js')) }}"></script>
<script src="{{ asset('admin/js/theme-simple.js') }}?v={{ filemtime(public_path('admin/js/theme-simple.js')) }}"></script>
<script src="{{ asset('admin/js/toast.js') }}?v={{ filemtime(public_path('admin/js/toast.js')) }}" defer></script>
@livewireScripts
@stack('scripts') @stack('js')

@if(session('toast'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastData = @json(session('toast'));
        if (toastData && toastData.title && toastData.message) {
            showToast(toastData.title, toastData.message, toastData.type || 'success');
        }
    });
</script>
@endif

@if (request()->routeIs('admin.*.manage*'))
    <x-head.tinymce-config />
@endif
</body>
</html>