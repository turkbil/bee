<!-- resources/views/layouts/navigation.blade.php -->
<nav x-data="{ open: false }" style="background-color: #2d2d2d; border-bottom: 1px solid #404040;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px;">
        <div style="display: flex; justify-content: space-between; height: 64px;">
            <div style="display: flex; align-items: center;">
                <!-- Logo -->
                <a href="<?php echo e(route('dashboard')); ?>" style="display: block; margin-right: 32px;">
                    <?php if (isset($component)) { $__componentOriginal8892e718f3d0d7a916180885c6f012e7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8892e718f3d0d7a916180885c6f012e7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.application-logo','data' => ['style' => 'height: 36px; width: auto; fill: #e0e0e0;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('application-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['style' => 'height: 36px; width: auto; fill: #e0e0e0;']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $attributes = $__attributesOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__attributesOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8892e718f3d0d7a916180885c6f012e7)): ?>
<?php $component = $__componentOriginal8892e718f3d0d7a916180885c6f012e7; ?>
<?php unset($__componentOriginal8892e718f3d0d7a916180885c6f012e7); ?>
<?php endif; ?>
                </a>

                <!-- Navigation Links -->
                <div style="display: flex; gap: 24px;">
                    <a href="<?php echo e(route('dashboard')); ?>" 
                       style="color: <?php echo e(request()->routeIs('dashboard') ? '#ffffff' : '#a0a0a0'); ?>; text-decoration: none;">
                        <?php echo e(__('Dashboard')); ?>

                    </a>
                    <a href="<?php echo e(url('admin/dashboard')); ?>" 
                       style="color: <?php echo e(request()->is('admin/dashboard') ? '#ffffff' : '#a0a0a0'); ?>; text-decoration: none;">
                        <?php echo e(__('Yönetim Paneli')); ?>

                    </a>
                    <a href="<?php echo e(route('profile.edit')); ?>" 
                       style="color: <?php echo e(request()->routeIs('profile.edit') ? '#ffffff' : '#a0a0a0'); ?>; text-decoration: none;">
                        <?php echo e(__('Profile')); ?>

                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                style="background: none; border: none; color: #a0a0a0; cursor: pointer; padding: 0;">
                            <?php echo e(__('Log Out')); ?>

                        </button>
                    </form>
                </div>
            </div>

            <!-- User Info -->
            <div style="display: flex; align-items: center;">
                <div style="color: #e0e0e0;"><?php echo e(Auth::user()->name); ?></div>
            </div>

            <!-- Mobile menu button -->
            <div style="display: none;">
                <button @click="open = !open" 
                        style="padding: 8px; color: #a0a0a0; background: none; border: none; cursor: pointer;">
                    <svg style="height: 24px; width: 24px;" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" style="display: none;">
    </div>
</nav><?php /**PATH C:\laragon\www\laravel\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>