{{-- resources/views/admin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="{{ asset('admin/css/tabler.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/css/tabler-vendors.min.css') }}" />
    @if (Str::contains(Request::url(), ['create', 'edit', 'manage', 'form']))
    @else
    <!-- <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.css"> -->
    @endif
    <link rel="stylesheet"
        href="{{ asset('admin/css/plugins.css') }}?v={{ filemtime(public_path('admin/css/plugins.css')) }}" />
    <link rel="stylesheet" href="{{ asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('admin/css/main.css') }}?v={{ filemtime(public_path('admin/css/main.css')) }}" />
    @livewireStyles
    @stack('css')
    <style>
        :root {
            --primary-color: <?php echo isset($_COOKIE['siteColor']) ? $_COOKIE['siteColor']: '#066fd1';
            ?>;
            --primary-text-color: <?php echo isset($_COOKIE['siteTextColor']) ? $_COOKIE['siteTextColor']: '#ffffff';
            ?>;
        }
    </style>
</head>
<body<?php echo (isset($_COOKIE['dark']) && $_COOKIE['dark']=='1' ) ? ' class="dark' . (!isset($_COOKIE['tableCompact'])
    || $_COOKIE['tableCompact']=='1' ? ' table-compact' : '' ) . '" data-bs-theme="dark"' : ' class="light' .
    (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact']=='1' ? ' table-compact' : '' )
    . '" data-bs-theme="light"' ; ?>>
    <div class="page">
        {{-- Sayfanın üst kısmı --}}
        @include('admin.partials.header') {{-- Üst header (logo, kullanıcı menüsü vs.) --}}
        {{-- Dinamik Breadcrumb ve Modül Menüsü --}}
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <!-- PreTitle ve Sayfa Başlığı -->
                            <div class="page-pretitle">
                                @stack('pretitle') {{-- Dinamik PreTitle --}}
                            </div>
                            <h2 class="page-title">
                                @stack('title') {{-- Dinamik Sayfa Başlığı --}}
                            </h2>
                        </div>
                        <!-- Modül Menüsü -->
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                @stack('module-menu') {{-- Dinamik Modül Menüsü --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- İçerik Bölümü --}}
            <div class="page-body">
                <div class="container-xl">
                    @hasSection('content')
                    @yield('content') {{-- Blade sayfaları için --}}
                    @else
                    {{ $slot ?? '' }} {{-- Livewire bileşenleri için --}}
                    @endif
                </div>
            </div>
            {{-- Sayfanın alt kısmı --}}
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item"><a href="https://tabler.io/docs" target="_blank"
                                        class="link-secondary" rel="noopener">Documentation</a></li>
                                <li class="list-inline-item"><a href="./license.html" class="link-secondary">License</a>
                                </li>
                                <li class="list-inline-item"><a href="https://github.com/tabler/tabler" target="_blank"
                                        class="link-secondary" rel="noopener">Source code</a></li>
                                <li class="list-inline-item">
                                    <a href="https://github.com/sponsors/codecalm" target="_blank"
                                        class="link-secondary" rel="noopener">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/heart -->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon text-pink icon-filled icon-inline" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                        </svg>
                                        Sponsor
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    Copyright &copy; 2023
                                    <a href="." class="link-secondary">Tabler</a>.
                                    All rights reserved.
                                </li>
                                <li class="list-inline-item">
                                    <a href="./changelog.html" class="link-secondary" rel="noopener">
                                        v1.0.0-beta19
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
    <script src="{{ asset('admin/js/main.js') }}?v={{ filemtime(public_path('admin/js/main.js')) }}"></script>
    <script src="{{ asset('admin/js/toast.js') }}?v={{ filemtime(public_path('admin/js/toast.js')) }}"></script>
    {{-- Modüle özel silme modalı --}}
    @if(request()->is('admin/user*'))
    <livewire:modals.user-delete-modal />
    @else
    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
    @endif

    @livewireScripts
    @stack('js')

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

    <script>
        // Tablo duzeni kodu
        function toggleTableMode(isCompact) {
            document.cookie = `tableCompact=${isCompact ? '1' : '0'}; max-age=${60*60*24*30}; path=/`;
            const body = document.body;
            const darkClass = body.classList.contains('dark') ? 'dark' : 'light';
            body.className = `${darkClass}${isCompact ? ' table-compact' : ''}`;
        }

        // Site rengi secimi
        const selectedColor = document.getElementById('selectedColor');
        const colorPickerDropdown = document.getElementById('colorPickerDropdown');
        
        // Başlangıçta seçili rengi ayarla
        window.addEventListener('load', () => {
            const cookieColor = getCookie('siteColor') || '#2196F3';
            selectedColor.style.backgroundColor = cookieColor;
            changeColor(cookieColor);
            
            // Renk kutularının arkaplan rengini ayarla
            document.querySelectorAll('.color-option').forEach(option => {
                option.style.backgroundColor = option.dataset.color;
            });

            // Dışarı tıklamada dropdown'ı kapat
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.color-picker-container')) {
                    colorPickerDropdown.style.display = 'none';
                }
            });
        });

        function toggleColorPicker() {
            colorPickerDropdown.style.display = colorPickerDropdown.style.display === 'none' ? 'flex' : 'none';
        }

        function changeColor(color) {
            selectedColor.style.backgroundColor = color;
            colorPickerDropdown.style.display = 'none';

            // Rengi cookie'ye kaydet
            document.cookie = `siteColor=${color}; max-age=${60*60*24*30}; path=/`;
            
            // Rengin parlaklığını hesapla
            const r = parseInt(color.substr(1,2), 16);
            const g = parseInt(color.substr(3,2), 16);
            const b = parseInt(color.substr(5,2), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            
            // Parlaklığa göre text rengini belirle
            const textColor = brightness > 128 ? 'rgb(24, 36, 51)' : '#ffffff';
            
            // Text rengini cookie'ye kaydet
            document.cookie = `siteTextColor=${textColor}; max-age=${60*60*24*30}; path=/`;
            
            // CSS değişkenlerini güncelle
            document.documentElement.style.setProperty('--primary-color', color);
            document.documentElement.style.setProperty('--primary-text-color', textColor);
            document.documentElement.style.setProperty('--tblr-primary', color); 
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

    </script>

    </body>

</html>