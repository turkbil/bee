<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($group->name ?? 'Form Builder'); ?> - Form Düzenleyici</title>
    
    <!-- Tabler CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/tabler.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('admin/css/tabler-vendors.min.css')); ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Form Builder CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/libs/form-builder/settingmanagement/css/form-builder.css')); ?>">
    
    <!-- Özel Stiller -->
    <?php echo $__env->yieldPushContent('styles'); ?>
    
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body>
    <?php echo e($slot); ?>

    
    <!-- JavaScript -->
    <script src="<?php echo e(asset('admin/js/tabler.min.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/sortable/sortable.min.js')); ?>"></script>
    
    <!-- Form Builder Ana Modüller -->
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-core.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-elements.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-templates.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-drag-drop.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-operations.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder-ui.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/libs/form-builder/settingmanagement/js/form-builder.js')); ?>"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html><?php /**PATH C:\laragon\www\laravel\Modules/SettingManagement\resources/views/layouts/form-builder.blade.php ENDPATH**/ ?>