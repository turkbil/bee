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
        // Disable source map requests and errors in development
        (function() {
            // Suppress console errors for source maps
            var originalError = console.error;
            console.error = function() {
                var message = String(arguments[0] || '');
                if (message.toLowerCase().includes('source map') || 
                    message.includes('bootstrap.esm.js.map') ||
                    message.includes('chart.umd.min.js.map') ||
                    message.includes('ENOENT') ||
                    message.includes('node_modules')) {
                    return; // Suppress these errors
                }
                originalError.apply(console, arguments);
            };
            
            // Disable sourcemap requests globally
            if (window.SourceMap) {
                window.SourceMap = undefined;
            }
            
            // Override fetch to block source map requests
            var originalFetch = window.fetch;
            window.fetch = function(url, options) {
                if (typeof url === 'string' && url.includes('.map')) {
                    return Promise.reject(new Error('Source map blocked'));
                }
                return originalFetch.apply(this, arguments);
            };
        })();
        
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
    <link rel="stylesheet" href="/admin-assets/css/tabler.min.css?v={{ time() }}">
    <link rel="stylesheet" href="/admin-assets/css/theme-font-size.css?v={{ time() }}">
    <link rel="stylesheet" href="/admin-assets/css/tabler-vendors.min.css?v={{ time() }}">
    @if (Str::contains(Request::url(), ['create', 'edit', 'manage', 'form']))
    @else
    @endif
    <link rel="stylesheet" href="/admin-assets/css/plugins.css?v={{ time() }}" />
    <link rel="stylesheet" href="/admin-assets/libs/fontawesome-pro@6.7.1/css/all.min.css">
    <link rel="stylesheet" href="/admin-assets/libs/choices/choices.min.css">
    <link rel="stylesheet" href="/admin-assets/css/choices-custom.css?v={{ time() }}">
    <link rel="stylesheet" href="/admin-assets/css/main.css?v={{ time() }}" />
    <link rel="stylesheet" href="/admin-assets/css/main-theme-builder.css?v={{ time() }}" />
    <link rel="stylesheet" href="/admin-assets/css/responsive.css?v={{ time() }}" />
    <link rel="stylesheet" href="/admin-assets/css/ai-response-templates.css?v={{ time() }}" />
    @stack('styles') @stack('css')
    
    {{-- Module Menu Icon Symmetry CSS --}}
    <style>
        .icon-menu {
            display: inline-block;
            width: 20px;
            height: 20px;
            text-align: center;
            margin-right: 8px;
            font-size: 14px;
            line-height: 20px;
            vertical-align: middle;
        }
        
        .dropdown-item .icon-menu,
        .dropdown-module-item .icon-menu,
        .btn .icon-menu {
            flex-shrink: 0;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
        }
        
        .dropdown-module-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        
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
    <!-- Global Loading Bar - Tabler Style -->
    <div id="global-loading-bar" class="progress" style="
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        z-index: 99999;
        opacity: 0;
        transition: opacity 0.1s ease;
        background: transparent;
    ">
        <div id="loading-progress" class="progress-bar" style="
            background-color: var(--tblr-primary, #066fd1);
            width: 0%;
            transition: width 0.2s ease;
        "></div>
    </div>
    
    @include('admin.components.navigation')
    
    <div class="page-wrapper">
        @hasSection('pretitle')
        <div class="page-header d-print-none">
            <div class="container">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                  @yield('pretitle')
                </div>
                <h2 class="page-title">
                  @yield('title')
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
        @endif

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

<script src="/admin-assets/js/plugins.js?v={{ time() }}"></script>
<script src="/admin-assets/js/tabler.min.js"></script>
<script src="/admin-assets/libs/litepicker/dist/litepicker.js" defer></script>
<script src="/admin-assets/libs/fslightbox/index.js" defer></script>
<script src="/admin-assets/libs/choices/choices.min.js" defer></script>
<script src="/admin-assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="/admin-assets/js/translations.js?v={{ time() }}"></script>
<script src="/admin-assets/js/theme.js?v={{ time() }}"></script>
<script src="/admin-assets/js/main.js?v={{ time() }}"></script>
<script src="/admin-assets/js/toast.js?v={{ time() }}" defer></script>

<!-- Global Loading Bar Script - Tabler Native -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.getElementById('global-loading-bar');
    const progressBar = document.getElementById('loading-progress');
    
    // Tabler Turbo benzeri loading bar sistemi
    const loader = {
        show: function() {
            loadingBar.style.opacity = '1';
            progressBar.style.width = '10%';
        },
        
        setValue: function(value) {
            progressBar.style.width = (value * 100) + '%';
        },
        
        hide: function() {
            this.setValue(1);
            setTimeout(() => {
                loadingBar.style.opacity = '0';
                progressBar.style.width = '0%';
            }, 100);
        }
    };
    
    // Loading bar gösterme
    function showLoadingBar() {
        loader.show();
        // Yavaş yavaş %90'a kadar çıkar
        setTimeout(() => loader.setValue(0.9), 200);
    }
    
    // Loading bar gizleme
    function hideLoadingBar() {
        loader.hide();
    }
    
    // Admin linkleri ve AJAX işlemleri için loading bar
    function attachLoadingToLinks() {
        const links = document.querySelectorAll('a[href]:not([href^="#"]):not([href^="javascript:"]):not([href^="mailto:"]):not([href^="tel:"]):not([data-no-loading])');
        
        links.forEach(link => {
            // Zaten event listener eklenmiş mi kontrol et
            if (link.dataset.loadingAttached) return;
            link.dataset.loadingAttached = 'true';
            
            link.addEventListener('click', function(e) {
                // External linkler için loading bar gösterme
                if (this.hostname !== window.location.hostname) return;
                
                // Dropdown toggle'lar için loading bar gösterme
                if (this.getAttribute('data-bs-toggle')) return;
                
                // Modal toggle'lar için loading bar gösterme
                if (this.getAttribute('data-bs-target')) return;
                
                showLoadingBar();
            });
        });
        
        // Wire:click elementleri için de loading bar (escape edilen selector)
        const wireElements = document.querySelectorAll('[wire\\:click]');
        wireElements.forEach(element => {
            if (element.dataset.wireLoadingAttached) return;
            element.dataset.wireLoadingAttached = 'true';
            
            element.addEventListener('click', function(e) {
                showLoadingBar();
            });
        });
    }
    
    // Sayfa yüklendiğinde loading bar'ı %100 yap ve gizle
    window.addEventListener('load', hideLoadingBar);
    
    // Linkleri yakala
    attachLoadingToLinks();
    
    // Livewire için loading bar
    if (typeof Livewire !== 'undefined') {
        Livewire.hook('message.sent', () => {
            showLoadingBar();
        });
        
        Livewire.hook('message.processed', () => {
            hideLoadingBar();
        });
    }
    
    // Sayfa değişikliklerinde linkleri yeniden yakala (AJAX sonrası)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                // Yeni eklenen linkler için loading bar ekle
                setTimeout(attachLoadingToLinks, 100);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Form submit'leri için loading bar
    document.addEventListener('submit', function(e) {
        if (e.target.tagName === 'FORM') {
            showLoadingBar();
        }
    });
    
    // Browser back/forward için loading bar
    window.addEventListener('beforeunload', function() {
        showLoadingBar();
    });
});
</script>
@livewireScripts

<!-- Navigation Loading States -->
<script src="{{ asset('admin-assets/js/navigation-loading.js') }}"></script>

@stack('scripts') @stack('js')

@if(session('toast'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastData = @json(session('toast'));
        if (toastData && toastData.title && toastData.message) {
            // 🚫 SESSION TOAST DUPLICATE CONTROL
            const currentTime = Date.now();
            const currentMessage = toastData.title + toastData.message;
            
            // Global duplicate control değişkenlerini kontrol et
            if (typeof lastToastTime !== 'undefined' && 
                currentTime - lastToastTime < 1000 && 
                typeof lastToastMessage !== 'undefined' && 
                lastToastMessage === currentMessage) {
                console.log('🚫 Session toast duplicate prevented:', currentMessage);
                return;
            }
            
            showToast(toastData.title, toastData.message, toastData.type || 'success');
        }
    });
</script>
@endif

@if (request()->routeIs('admin.*.manage*'))
    <x-head.hugerte-config />
    
    {{-- Global Multi-Language Form Management for All Modules --}}
    <script>
    $(document).ready(function() {
        // Initialize tab manager with dynamic key based on current route
        const routeName = '{{ request()->route()->getName() }}';
        const tabKey = routeName.replace('.', '') + 'ActiveTab';
        TabManager.init(tabKey);
        
        // Check if page has multi-language support
        if ($('.language-content').length > 0 || $('.language-switch-btn').length > 0) {
            // Initialize multi-language form switcher
            MultiLangFormSwitcher.init();
            
            // HugeRTE is automatically initialized via hugerte-config.blade.php
        } else {
            // Single language - TinyMCE is automatically initialized via tinymce-config.blade.php
        }
        
        // Language switcher for underline style buttons (.language-switch-btn) - only if exists
        if ($('.language-switch-btn').length > 0) {
            $('.language-switch-btn').on('click', function(e) {
            e.preventDefault();
            const selectedLang = $(this).data('language');
            
            // Remove active from all siblings
            $('.language-switch-btn').each(function() {
                $(this).removeClass('text-primary').addClass('text-muted');
                $(this).css('border-bottom', '2px solid transparent');
            });
            
            // Add active to clicked button
            $(this).removeClass('text-muted').addClass('text-primary');
            
            // Get primary color from theme builder variable
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color') ||
                                 getComputedStyle(document.documentElement).getPropertyValue('--tblr-primary') ||
                                 '#066fd1';
            
            $(this).css('border-bottom', `2px solid ${primaryColor}`);
            
            // Switch language content
            MultiLangFormSwitcher.switchLanguage(selectedLang);
        });
        }
    });

    // Re-initialize on Livewire updates
    document.addEventListener('livewire:updated', function() {
        // HugeRTE handles its own reinitialization via hugerte-config.blade.php
        
        // Re-bind language switcher events - only if exists
        if ($('.language-switch-btn').length > 0) {
            $('.language-switch-btn').off('click').on('click', function(e) {
            e.preventDefault();
            const selectedLang = $(this).data('language');
            
            // Remove active from all siblings
            $('.language-switch-btn').each(function() {
                $(this).removeClass('text-primary').addClass('text-muted');
                $(this).css('border-bottom', '2px solid transparent');
            });
            
            // Add active to clicked button
            $(this).removeClass('text-muted').addClass('text-primary');
            
            // Get primary color from theme builder variable
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color') ||
                                 getComputedStyle(document.documentElement).getPropertyValue('--tblr-primary') ||
                                 '#066fd1';
            
            $(this).css('border-bottom', `2px solid ${primaryColor}`);
            
            // Switch language content
            MultiLangFormSwitcher.switchLanguage(selectedLang);
        });
        }
    });
    </script>
@endif
</body>
</html>