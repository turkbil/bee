<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="tenant-default-locale" content="{{ get_tenant_default_locale() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>{{ \App\Helpers\AdminTitleHelper::generateTitle() }}</title>
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
    @livewireStyles
    <link rel="stylesheet" href="/admin-assets/css/tabler.min.css?v={{ time() }}">
    <link rel="stylesheet" href="/admin-assets/css/theme-font-size.css?v={{ time() }}">
    <link rel="stylesheet" href="/admin-assets/css/tabler-vendors.min.css?v={{ time() }}">

    {{-- Alternate Language Links for SEO (Admin Panel) --}}
    @php
        use App\Helpers\CanonicalHelper;

        // Admin panelde model yok, sadece route bazlı alternate link'ler
        $alternateLinks = CanonicalHelper::generateAlternateLinks();
        echo CanonicalHelper::generateAlternateMetaTags($alternateLinks);
    @endphp

    <!-- CORE SYSTEM STYLES - DO NOT REMOVE OR MODIFY -->
    <!-- Bu CSS tüm temalarda ve admin panelde zorunludur / This CSS is mandatory everywhere -->
    <link rel="stylesheet" href="{{ asset('css/core-system.css') }}?v=1.0.0">
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

    {{-- Global AI Widget System CSS - Auto-load if AI module is active --}}
    @php
        $aiModuleActive = false;
        try {
            $modules = app('modules')->allEnabled();
            $aiModuleActive = is_array($modules) ? in_array('AI', array_keys($modules)) : $modules->has('AI');
        } catch(\Exception $e) {
            // Module service not available
        }
    @endphp
    @if($aiModuleActive)
    <link rel="stylesheet" href="/admin-assets/css/ai-widget.css?v={{ time() }}" />
    @endif
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


    <style>
      @import url("https://rsms.me/inter/inter.css");

      /* Tüm badge'lerin text rengini beyaz yap */
      .badge {
          color: white !important;
      }

      /* Badge renkleri için arkaplan rengi atamaları */
      .badge:not([class*="bg-"]) {
          background-color: var(--tblr-primary) !important;
      }

      /* AI recommendations gereksiz butonları gizle */
      .ai-select-all-recommendations,
      .ai-apply-selected-recommendations {
          display: none !important;
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
        display: none;
        transition: opacity 0.2s ease;
        background: transparent;
    ">
        <div id="loading-progress" class="progress-bar" style="
            background-color: var(--tblr-primary, #066fd1);
            width: 0%;
            transition: width 0.3s ease;
        "></div>
    </div>

    {{-- AI Credit Warning System --}}
    @livewire('ai::admin.credit-warning-component')

    @include('admin.components.navigation')

    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                  {{ \App\Helpers\AdminTitleHelper::generatePretitle() }}
                </div>
                <h2 class="page-title">
                  {{ \App\Helpers\AdminTitleHelper::generatePageTitle() }}
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

{{-- 🤖 AI Widget System artık readme/ai-assistant klasöründe --}}

{{-- 🌍 Global AI Translation Modal - Available in all modules --}}
@include('admin.partials.global-ai-translation-modal')

{{-- 🚀 Global AI Content Generation Modal - Available in all modules --}}
@include('admin.partials.global-ai-content-modal')

{{-- jQuery Library --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="/admin-assets/js/plugins.js?v={{ time() }}"></script>
<script src="/admin-assets/js/multi-modal-manager.js?v={{ time() }}"></script>
<script src="/admin-assets/js/tabler.min.js"></script>
<script src="/admin-assets/libs/litepicker/dist/litepicker.js" defer></script>
<script src="/admin-assets/libs/fslightbox/index.js" defer></script>
<script src="/admin-assets/libs/choices/choices.min.js" defer></script>
<script src="/admin-assets/libs/apexcharts/dist/apexcharts.min.js"></script>
<script src="/admin-assets/js/translations.js?v={{ time() }}"></script>
<script src="/admin-assets/js/theme.js?v={{ time() }}"></script>
<script src="/admin-assets/js/main.js?v={{ time() }}"></script>
{{-- SEO functionality moved to main.js and manage.js --}}
<script src="/admin-assets/js/toast.js?v={{ time() }}" defer></script>

{{-- 🌍 AI Translation Modal JavaScript --}}
<script src="/assets/js/simple-translation-modal.js?v={{ time() }}"></script>

<!-- Global Loading Bar Script - Tabler Native -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.getElementById('global-loading-bar');
    const progressBar = document.getElementById('loading-progress');

    // Tabler Turbo benzeri loading bar sistemi
    const loader = {
        show: function() {
            loadingBar.style.display = 'block';
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
                setTimeout(() => {
                    loadingBar.style.display = 'none';
                    progressBar.style.width = '0%';
                }, 400);
            }, 200);
        }
    };

    // Loading bar gösterme - MODAL SAFE
    function showLoadingBar() {
        // 🚫 MODAL AÇIKKEN LOADING BAR GÖSTERME
        const activeModals = document.querySelectorAll('.modal.show, .modal[style*="display: block"]');
        if (activeModals.length > 0) {
            // Modal açık: loading bar iptal edildi
            return;
        }

        loader.show();
        setTimeout(() => loader.setValue(0.9), 200);
        
        // AUTO-HIDE: 2 saniye sonra otomatik gizle (modal safe)
        setTimeout(() => {
            // Loading bar otomatik gizleniyor (2 saniye)
            hideLoadingBar();
        }, 2000);
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

                // Bootstrap toggle'lar için loading bar gösterme
                if (this.getAttribute('data-bs-toggle')) return;
                if (this.getAttribute('data-bs-target')) return;
                if (this.getAttribute('data-bs-dismiss')) return;

                // Modal içindeki linkler için loading bar gösterme
                if (this.closest('.modal')) {
                    // Modal içi link: loading bar iptal edildi
                    return;
                }

                showLoadingBar();
            });
        });

        // Wire:click elementleri için de loading bar (escape edilen selector)
        const wireElements = document.querySelectorAll('[wire\\:click]');
        wireElements.forEach(element => {
            if (element.dataset.wireLoadingAttached) return;
            element.dataset.wireLoadingAttached = 'true';

            element.addEventListener('click', function(e) {
                // Modal içindeki wire:click için loading bar gösterme
                if (this.closest('.modal')) {
                    // Modal içi wire:click: loading bar iptal edildi
                    return;
                }
                showLoadingBar();
            });
        });
    }

    // Sayfa yüklendiğinde loading bar'ı %100 yap ve gizle
    window.addEventListener('load', hideLoadingBar);
    
    // ULTRA AGGRESSIVE FIX: 2 saniye sonra zorla gizle
    setTimeout(() => {
        // Loading bar zorla gizleniyor
        if (loadingBar) {
            loadingBar.style.display = 'none';
            loadingBar.style.opacity = '0';
            progressBar.style.width = '0%';
            // Loading bar zorla gizlendi
        }
    }, 2000);
    
    // BACKUP FIX: 1 saniye sonra da kontrol et
    setTimeout(() => {
        if (loadingBar && (loadingBar.style.display !== 'none' || loadingBar.style.opacity !== '0')) {
            // Loading bar hala görünür, zorla gizleniyor
            loadingBar.style.display = 'none';
            loadingBar.style.opacity = '0';
            progressBar.style.width = '0%';
        }
    }, 1000);

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

{{-- Global AI Widget System JS - Auto-load if AI module is active --}}
@if($aiModuleActive ?? false)
<script src="{{ asset('admin-assets/js/ai-widget.js') }}?v={{ time() }}"></script>
{{-- Global AI Translation System JS --}}
<script src="{{ asset('assets/js/ai-translation-system-v2.js') }}?v={{ time() }}"></script>

@endif

{{-- Global AI Content Generation System JS - ALWAYS LOAD (Global System) --}}
<script src="{{ asset('assets/js/ai-content-system.js') }}?v={{ time() }}"></script>

{{-- Global AI Content System Initialization --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global AI Content System'i başlat
    if (window.AIContentGenerationSystem) {
        window.aiContentSystem = new window.AIContentGenerationSystem({
            module: 'global', // Global kullanım
            baseUrl: '/admin'
        });

        console.log('🚀 Global AI Content System başlatıldı');
    } else {
        console.error('❌ AIContentGenerationSystem class not found');
    }
});
</script>

<!-- CORE SYSTEM SCRIPTS - DO NOT REMOVE OR MODIFY -->
<!-- Bu script tüm temalarda ve admin panelde zorunludur / This script is mandatory everywhere -->
<script src="{{ asset('js/core-system.js') }}?v=1.0.0"></script>

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
                // Session toast duplicate prevented: currentMessage
                return;
            }

            showToast(toastData.title, toastData.message, toastData.type || 'success');
        }
    });
</script>
@endif

@if (request()->routeIs('admin.*.manage*') || request()->routeIs('admin.menumanagement.index'))
    <x-head.hugerte-config />

    {{-- Tenant default language for manage.js --}}
    <script>
        window.tenantDefaultLanguage = '{{ get_tenant_default_locale() }}';
    </script>


    {{-- Load Manage.js for manage pages --}}
    <script src="/admin-assets/js/manage.js?v={{ time() }}"></script>

    {{-- Language Dropdown with Bootstrap Initialize --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns with overflow fix
            const allDropdownTriggers = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            allDropdownTriggers.forEach(trigger => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                    const dropdown = new bootstrap.Dropdown(trigger, {
                        boundary: 'viewport',
                        popperConfig: {
                            strategy: 'fixed',
                            modifiers: [
                                {
                                    name: 'computeStyles',
                                    options: {
                                        adaptive: false
                                    }
                                },
                                {
                                    name: 'offset',
                                    options: {
                                        offset: [0, 5]
                                    }
                                }
                            ]
                        }
                    });
                    
                    trigger.addEventListener('show.bs.dropdown', function(e) {
                        const dropdownMenu = this.nextElementSibling;
                        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                            dropdownMenu.style.position = 'fixed';
                            dropdownMenu.style.zIndex = '99999';
                            
                            const rect = this.getBoundingClientRect();
                            dropdownMenu.style.top = (rect.bottom + 5) + 'px';
                            dropdownMenu.style.right = '20px';
                            dropdownMenu.style.left = 'auto';
                            dropdownMenu.style.transform = 'none';
                        }
                    });
                }
            });
            
        });
    </script>
@endif

{{-- 🌍 ENTERPRISE STREAMING TRANSLATION SYSTEM --}}
<script src="{{ asset('admin-assets/js/streaming-translation.js') }}"></script>
<script>
    // 🎆 Enterprise-level streaming translation integration
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Enterprise Streaming Translation System Ready!');
        
        // ✅ Check if streaming translation is available
        if (typeof streamingTranslation !== 'undefined') {
            // Real-time connection status indicator
            streamingTranslation.on('connection_established', function() {
                console.log('✅ WebSocket connection established - Real-time translation ready');
            });
            
            streamingTranslation.on('connection_failed', function() {
                console.warn('⚠️ WebSocket connection failed - Falling back to polling mode');
            });
        } else {
            console.log('📝 Streaming translation not available - Using standard translation mode');
        }
        
        // Global keyboard shortcuts for power users
        document.addEventListener('keydown', function(e) {
            // Ctrl+Shift+T: Quick translation modal
            if (e.ctrlKey && e.shiftKey && e.key === 'T') {
                e.preventDefault();
                const activeElement = document.activeElement;
                if (activeElement && activeElement.dataset.pageId) {
                    const language = prompt('Enter target language code (e.g., tr, en, de):');
                    if (language && typeof streamingTranslation !== 'undefined') {
                        streamingTranslation.startStreamingTranslation(
                            activeElement.dataset.pageId,
                            language,
                            { priorityMode: 'high' }
                        );
                    }
                }
            }
        });
    });
</script>

<!-- AI Content Builder JS -->
<script src="{{ asset('assets/js/ai-content-builder.js') }}"></script>

</body>
</html>
