{{-- resources/views/admin/layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('admin.partials.head') {{-- CSS, JS, meta bilgileri burada yer alacak --}}
</head>
<body<?php echo (isset($_COOKIE['dark']) && $_COOKIE['dark'] == '1') ? ' class=" dark" data-bs-theme="dark"' : ' class="light" data-bs-theme="light"'; ?>>
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
                                @yield('pretitle', 'Modül') {{-- Dinamik PreTitle --}}
                            </div>
                            <h2 class="page-title">
                                @yield('title', 'Başlık') {{-- Dinamik Sayfa Başlığı --}}
                            </h2>
                        </div>
                        <!-- Modül Menüsü -->
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                @yield('module-menu') {{-- Dinamik Modül Menüsü --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- İçerik Bölümü --}}
            <div class="page-body">
                <div class="container-xl">
                    @yield('content') {{-- Sayfanın içeriği --}}
                </div>
            </div>
            {{-- Sayfanın alt kısmı --}}
            @include('admin.partials.footer')
        </div>
    </div>
    @include('admin.partials.scripts')
    </body>

</html>
