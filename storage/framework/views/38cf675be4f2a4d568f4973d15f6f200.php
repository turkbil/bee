<!-- resources/views/layouts/guest.blade.php -->
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        
        <!-- JavaScript için CSRF token'ı ayarla -->
        <script>
            window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>;
        </script>
    </head>
    <body style="margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; -webkit-font-smoothing: antialiased;">
        <?php echo e($slot); ?>

    </body>
</html><?php /**PATH C:\laragon\www\laravel\resources\views/layouts/guest.blade.php ENDPATH**/ ?>