

<?php $__env->startPush('pretitle'); ?>
Ayarlar
<?php $__env->stopPush(); ?>


<?php $__env->startPush('title'); ?>
Ayar Yönetimi
<?php $__env->stopPush(); ?>


<?php $__env->startPush('module-menu'); ?>
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    Ayar İşlemleri
                </button>
                <div class="dropdown-menu">
                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'view')): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.settingmanagement.index')); ?>">
                        Grup Listesi
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'update')): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.settingmanagement.group.manage')); ?>">
                        Yeni Grup Ekle
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->hasModulePermission('settingmanagement', 'view') || 
                        auth()->user()->hasModulePermission('settingmanagement', 'update')): ?>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Ayar İşlemleri</span>
                    </h6>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'update')): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.settingmanagement.manage')); ?>">
                        Yeni Ayar Ekle
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if(auth()->user()->hasModulePermission('settingmanagement', 'view') || 
                        auth()->user()->hasModulePermission('settingmanagement', 'update')): ?>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Tenant İşlemleri</span>
                    </h6>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    
                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'view')): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.settingmanagement.tenant.settings')); ?>">
                        Tenant Ayarları
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'view')): ?>
                    <a class="dropdown-item" href="<?php echo e(route('admin.settingmanagement.form-builder.index')); ?>">
                        Form Builder
                    </a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasmoduleaccess', 'settingmanagement', 'update')): ?>
            <a href="<?php echo e(route('admin.settingmanagement.manage')); ?>" class="btn btn-primary">
                Yeni Ayar Ekle
            </a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php $__env->stopPush(); ?><?php /**PATH C:\laragon\www\laravel\Modules/SettingManagement\resources/views/helper.blade.php ENDPATH**/ ?>