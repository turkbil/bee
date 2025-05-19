<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo e(config('app.name')); ?> - <?php echo $__env->yieldContent('title'); ?></title>
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
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <link rel="stylesheet" href="<?php echo e(asset('admin/css/tabler.min.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/tabler.min.css'))); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/theme-font-size.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/theme-font-size.css'))); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/tabler-vendors.min.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/tabler-vendors.min.css'))); ?>">
    <?php if(Str::contains(Request::url(), ['create', 'edit', 'manage', 'form'])): ?>
    <?php else: ?>
    <?php endif; ?>
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/plugins.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/plugins.css'))); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/libs/fontawesome-pro@6.7.1/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/main.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/main.css'))); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/theme-builder.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/theme-builder.css'))); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/theme-font-size.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/theme-font-size.css'))); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/responsive.css')); ?>?v=<?php echo e(filemtime(public_path('admin/css/responsive.css'))); ?>" />
    <?php echo $__env->yieldPushContent('styles'); ?> <?php echo $__env->yieldPushContent('css'); ?>
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
    <?php echo $__env->make('admin.components.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <div class="page-pretitle">
                  <?php echo $__env->yieldPushContent('pretitle'); ?>
                </div>
                <h2 class="page-title">
                  <?php echo $__env->yieldPushContent('title'); ?>
                </h2>
              </div>
              <!-- Page title actions -->
              <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                  <?php echo $__env->yieldPushContent('module-menu'); ?>
                </div>
              </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container">
                <?php if (! empty(trim($__env->yieldContent('content')))): ?>
                <?php echo $__env->yieldContent('content'); ?> 
                <?php else: ?>
                <?php echo e($slot ?? ''); ?> 
                <?php endif; ?>
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
                                Telif Hakkı &copy; <?php echo e(date('Y')); ?>

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

<script src="<?php echo e(asset('admin/js/plugins.js')); ?>?v=<?php echo e(filemtime(public_path('admin/js/plugins.js'))); ?>"></script>
<script src="<?php echo e(asset('admin/js/tabler.min.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/libs/litepicker/dist/litepicker.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/libs/fslightbox/index.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/libs/tom-select/dist/js/tom-select.complete.min.js')); ?>" defer></script>
<script src="<?php echo e(asset('admin/libs/tom-select/dist/js/plugins/restore_on_backspace.js')); ?>"></script>
<script src="<?php echo e(asset('admin/js/main.js')); ?>?v=<?php echo e(filemtime(public_path('admin/js/main.js'))); ?>"></script>
<script src="<?php echo e(asset('admin/js/theme.js')); ?>?v=<?php echo e(filemtime(public_path('admin/js/theme.js'))); ?>"></script>
<script src="<?php echo e(asset('admin/js/toast.js')); ?>?v=<?php echo e(filemtime(public_path('admin/js/toast.js'))); ?>" defer></script>
<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

<?php echo $__env->yieldPushContent('scripts'); ?> <?php echo $__env->yieldPushContent('js'); ?>

<?php if(session('toast')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toastData = <?php echo json_encode(session('toast'), 15, 512) ?>;
        if (toastData && toastData.title && toastData.message) {
            showToast(toastData.title, toastData.message, toastData.type || 'success');
        }
    });
</script>
<?php endif; ?>

<?php if(request()->routeIs('admin.*.manage*')): ?>
    <?php if (isset($component)) { $__componentOriginal64ae506d51b70549a46b57e3bed1687c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal64ae506d51b70549a46b57e3bed1687c = $attributes; } ?>
<?php $component = App\View\Components\Head\TinymceConfig::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('head.tinymce-config'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Head\TinymceConfig::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal64ae506d51b70549a46b57e3bed1687c)): ?>
<?php $attributes = $__attributesOriginal64ae506d51b70549a46b57e3bed1687c; ?>
<?php unset($__attributesOriginal64ae506d51b70549a46b57e3bed1687c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal64ae506d51b70549a46b57e3bed1687c)): ?>
<?php $component = $__componentOriginal64ae506d51b70549a46b57e3bed1687c; ?>
<?php unset($__componentOriginal64ae506d51b70549a46b57e3bed1687c); ?>
<?php endif; ?>
<?php endif; ?>
</body>
</html><?php /**PATH C:\laragon\www\laravel\resources\views/admin/layout.blade.php ENDPATH**/ ?>