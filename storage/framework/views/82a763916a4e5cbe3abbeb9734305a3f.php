<?php echo $__env->make('settingmanagement::helper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="input-icon flex-grow-1">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Grup ara...">
                </div>
                <a href="<?php echo e(route('admin.settingmanagement.group.manage')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Grup Ekle
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row row-cards">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card bg-muted-lt">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary-lt me-2">
                                        <i class="<?php echo e($group->icon ?? 'fas fa-folder'); ?> <?php echo e(!$group->is_active ? 'text-danger' : ''); ?>"></i>
                                    </div>
                                    <div>
                                        <h3 class="card-title mb-0 d-flex align-items-center">
                                            <?php echo e($group->name); ?>

                                            <!--[if BLOCK]><![endif]--><?php if(!$group->is_active): ?>
                                            <span class="badge bg-danger text-white ms-2">Pasif</span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </h3>
                                        <!--[if BLOCK]><![endif]--><?php if($group->description): ?>
                                        <small class="text-muted"><?php echo e(Str::limit($group->description, 50)); ?></small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div class="ms-auto">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="<?php echo e(route('admin.settingmanagement.group.manage', $group->id)); ?>"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Düzenle
                                                </a>
                                                <a href="<?php echo e(route('admin.settingmanagement.group.manage', ['parent_id' => $group->id])); ?>"
                                                    class="dropdown-item">
                                                    <i class="fas fa-plus me-2"></i> Alt Grup Ekle
                                                </a>
                                                <button wire:click="toggleActive(<?php echo e($group->id); ?>)"
                                                    class="dropdown-item">
                                                    <i class="fas fa-<?php echo e($group->is_active ? 'ban' : 'check'); ?> me-2"></i>
                                                    <?php echo e($group->is_active ? 'Pasif Yap' : 'Aktif Yap'); ?>

                                                </button>
                                                <!--[if BLOCK]><![endif]--><?php if($group->children->isEmpty()): ?>
                                                <button wire:click="delete(<?php echo e($group->id); ?>)"
                                                    wire:confirm="Bu grubu silmek istediğinize emin misiniz?"
                                                    class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> Sil
                                                </button>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--[if BLOCK]><![endif]--><?php if($group->children->isNotEmpty()): ?>
                        <div class="list-group list-group-flush">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $group->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="<?php echo e($child->icon ?? 'fas fa-circle'); ?> <?php echo e(!$child->is_active ? 'text-danger' : ''); ?>"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill">
                                                <div class="font-weight-medium d-flex align-items-center"> 
                                                    <a href="<?php echo e(route('admin.settingmanagement.values', $child->id)); ?>"
                                                        class="text-reset">
                                                        <?php echo e($child->name); ?>

                                                    </a>
                                                    <!--[if BLOCK]><![endif]--><?php if(!$child->is_active): ?>
                                                    <span class="badge bg-danger text-white ms-2">Pasif</span>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <!--[if BLOCK]><![endif]--><?php if($child->description): ?>
                                                <div class="text-muted small"><?php echo e(Str::limit($child->description, 40)); ?>

                                                </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="badge bg-primary text-white text-center align-middle d-flex align-items-center justify-content-center" style="min-width: 2.5rem; padding: 0.35rem 0.5rem;">
                                                    <?php echo e($child->settings->count()); ?>

                                                </span>
                                                <div class="dropdown ms-2">
                                                    <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="<?php echo e(route('admin.settingmanagement.items', $child->id)); ?>" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> Ayarları Yapılandır
                                                        </a>
                                                        <a href="<?php echo e(route('admin.settingmanagement.group.manage', $child->id)); ?>" class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> Düzenle
                                                        </a>
                                                        <a href="<?php echo e(route('admin.settingmanagement.form-builder.edit', $child->id)); ?>" class="dropdown-item">
                                                            <i class="fas fa-magic me-2"></i> Form Builder
                                                        </a>
                                                        <button wire:click="toggleActive(<?php echo e($child->id); ?>)" class="dropdown-item">
                                                            <i class="fas fa-<?php echo e($child->is_active ? 'ban' : 'check'); ?> me-2"></i>
                                                            <?php echo e($child->is_active ? 'Pasif Yap' : 'Aktif Yap'); ?>

                                                        </button>
                                                        <!--[if BLOCK]><![endif]--><?php if($child->children->isEmpty()): ?>
                                                        <button wire:click="delete(<?php echo e($child->id); ?>)" wire:confirm="Bu alt grubu silmek istediğinize emin misiniz?" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Sil
                                                        </button>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="text-muted"><?php echo e($group->children->count()); ?> alt grup</div>
                                </div>
                                <div class="ms-auto">
                                    <a href="<?php echo e(route('admin.settingmanagement.group.manage', ['parent_id' => $group->id])); ?>"
                                        class="btn btn-link btn-sm">
                                        Alt Grup Ekle
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fas fa-layer-group fa-3x text-muted"></i>
                        </div>
                        <p class="empty-title">Henüz grup eklenmemiş</p>
                        <p class="empty-subtitle text-muted">
                            Yeni gruplar ekleyerek ayarlarınızı düzenlemeye başlayabilirsiniz.
                        </p>
                        <div class="empty-action">
                            <a href="<?php echo e(route('admin.settingmanagement.group.manage')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Yeni Grup Ekle
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\laravel\Modules/SettingManagement\resources/views/livewire/group-list-component.blade.php ENDPATH**/ ?>