<?php $__env->startSection('content'); ?>
    <div class="container">
        <h1><?php echo e($page->title); ?></h1>

        <div>
            <?php echo parse_widget_shortcodes($page->body); ?>
        </div>

        <div>
            <a href="<?php echo e(route('pages.index')); ?>">← Tüm Sayfalar</a>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('themes.blank.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\laravel\Modules/Page\resources/views/front/show.blade.php ENDPATH**/ ?>