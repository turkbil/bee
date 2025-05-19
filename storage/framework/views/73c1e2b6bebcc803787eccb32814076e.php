<?php
// Tenant ID'yi al
$tenantId = null;
$isCentral = false;

// Helper fonksiyonları kontrol et ve kullan
if (function_exists('tenant_id')) {
$tenantId = tenant_id();
} elseif (app()->has('tenancy') && app('tenancy')->initialized) {
$tenantId = tenant()->getTenantKey();
}

if (function_exists('is_central')) {
$isCentral = is_central();
} elseif (app()->has('tenancy')) {
$isCentral = !app('tenancy')->initialized;
}

// Tenant domainde mysql bağlantısı kullanması için
if (!$isCentral && app()->has('tenancy') && app('tenancy')->initialized) {
config(['database.connections.tenant.driver' => 'mysql']);
DB::purge('tenant');
}

// Modül servisini çağır
$moduleService = app(App\Services\ModuleService::class);

// Modülleri her zaman taze şekilde al (önbellek yok)
$modules = $moduleService->getModulesByTenant($tenantId);

// Modülleri tipine göre grupla
$groupedModules = $moduleService->groupModulesByType($modules);

// Aktif segment/tip bilgisini al
$activeType = request()->segment(2);

// Settings helper ile site başlığı ve logo bilgilerini al
$siteTitle = settings('site_title', config('app.name'));
?>

<header class="navbar navbar-expand-md d-print-none">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="<?php echo e(route('admin.dashboard')); ?>">
                <?php echo e($siteTitle); ?>

                <?php if(!$isCentral && $tenantId): ?>
                <?php
                $tenant = \App\Models\Tenant::find($tenantId);
                $tenantName = $tenant ? $tenant->name ?? $tenant->id : '';
                ?>
                <span class="small text-muted ms-2">(<?php echo e($tenantName); ?>)</span>
                <?php endif; ?>
            </a>
        </h1>

        <div class="navbar-nav flex-row order-md-last">
            <!-- Tema Ayarları Butonu -->
            <div class="nav-item me-2">
                <a href="#" class="d-flex lh-1 text-reset p-0" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasTheme">
                    <span class="me-2">
                        <i class="fa-solid fa-brush" style="font-size: 18px"></i>
                    </span>
                </a>
            </div>

            <div class="d-none d-md-flex">
                <!-- Karanlık mod switch'i -->
                <!-- Karanlık mod switch'i - orijinal tasarım korundu -->
                <div class="theme-mode mt-0 pt-2 me-2" data-theme="light">
                    <input type="checkbox" id="switch" class="dark-switch">
                    <div class="app">
                        <div class="switch-content">
                            <div class="switch-label"></div>
                            <label for="switch">
                                <div class="toggle"></div>
                                <div class="names">
                                    <p class="light"><i class="fa-solid fa-moon"></i></p>
                                    <p class="dark"><i class="fa-solid fa-sun"></i></p>
                                    <p class="auto"><i class="fa-solid fa-circle-half-stroke"></i></p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex p-0 px-1" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm"><i class="fa-solid fa-user"></i> </span>
                    <div class="d-none d-xl-block ps-2">
                        <div><?php echo e(Auth::user()->name); ?></div>
                        <div class="mt-1 small text-secondary">
                            <?php
                            $user = Auth::user();
                            $roleName = 'Kullanıcı';

                            if ($user->hasRole('root')) {
                            $roleName = 'Root';
                            } elseif ($user->hasRole('admin')) {
                            $roleName = 'Admin';
                            } elseif ($user->hasRole('editor')) {
                            $roleName = 'Editör';
                            }
                            ?>
                            <?php echo e($roleName); ?>

                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="<?php echo e(route('admin.usermanagement.user.activity.logs', ['id' => auth()->id()])); ?>"
                        class="dropdown-item">Aktivitelerim</a>
                    <a href="<?php echo e(route('admin.usermanagement.manage', ['id' => auth()->id()])); ?>"
                        class="dropdown-item">Profilim</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="dropdown-item">Çıkış Yap</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <?php if($groupedModules->has('content') && $groupedModules['content']->count() > 0): ?>
                    <li class="nav-item <?php echo e($activeType == 'content' ? 'active' : ''); ?> dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-content" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-graduate"></i>
                            </span>
                            <span class="nav-link-title">İçerik</span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <?php $__currentLoopData = $groupedModules['content']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a class="dropdown-item"
                                        href="<?php echo e(route('admin.' . strtolower($module->name) . '.index')); ?>">
                                        <?php echo e($module->display_name); ?>

                                    </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if($groupedModules->has('widget') && $groupedModules['widget']->count() > 0): ?>
                    <li class="nav-item <?php echo e($activeType == 'widget' ? 'active' : ''); ?> dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-widget" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-chef"></i>
                            </span>
                            <span class="nav-link-title">Bileşen</span>
                        </a>
                        <div class="dropdown-menu">
                            <?php $__currentLoopData = $groupedModules['widget']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dropdown-item"
                                href="<?php echo e(route('admin.' . strtolower($module->name) . '.index')); ?>">
                                <?php echo e($module->display_name); ?>

                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if($groupedModules->has('management') && $groupedModules['management']->count() > 0): ?>
                    <li class="nav-item <?php echo e($activeType == 'management' ? 'active' : ''); ?> dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-management" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-police-tie"></i>
                            </span>
                            <span class="nav-link-title">Yönetim</span>
                        </a>
                        <div class="dropdown-menu">
                            <?php $__currentLoopData = $groupedModules['management']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dropdown-item"
                                href="<?php echo e(route('admin.' . strtolower($module->name) . '.index')); ?>">
                                <?php echo e($module->display_name); ?>

                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if($groupedModules->has('system') && $groupedModules['system']->count() > 0): ?>
                    <li class="nav-item <?php echo e($activeType == 'system' ? 'active' : ''); ?> dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-system" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <i class="fa-solid fa-user-doctor"></i>
                            </span>
                            <span class="nav-link-title">Sistem</span>
                        </a>
                        <div class="dropdown-menu">
                            <?php $__currentLoopData = $groupedModules['system']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a class="dropdown-item"
                                href="<?php echo e(route('admin.' . strtolower($module->name) . '.index')); ?>">
                                <?php echo e($module->display_name); ?>

                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>

<?php echo $__env->make('admin.components.theme-builder', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\laravel\resources\views/admin/components/navigation.blade.php ENDPATH**/ ?>