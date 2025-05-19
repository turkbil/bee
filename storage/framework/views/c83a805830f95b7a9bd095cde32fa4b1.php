<?php echo $__env->make('settingmanagement::helper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="card">
    <div class="card-header">
        <div class="d-flex w-100 align-items-center justify-content-between">
            <h3 class="card-title m-0">
                <i class="fas fa-cogs me-2"></i>
                <?php echo e($group->name); ?> - Ayar Değerleri
            </h3>
            <a href="<?php echo e(route('admin.settingmanagement.items', $groupId)); ?>" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i> Ayarları Yönet
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info">
            <div class="d-flex">
                <div>
                    <i class="fas fa-info-circle me-2" style="margin-top: 3px"></i>
                </div>
                <div>
                    <h4 class="alert-title">Ayarları Toplu Düzenleme</h4>
                    <div class="text-muted">
                        Bu sayfa, <strong><?php echo e($group->name); ?></strong> grubu için tüm ayarları tek bir sayfa üzerinden 
                        değiştirmenizi sağlar. Değer değişikliklerini kaydetmek için sayfanın altındaki 
                        "Kaydet" düğmesini kullanın.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-3">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6" wire:key="setting-<?php echo e($setting->id); ?>">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title d-flex align-items-center">
                                <!--[if BLOCK]><![endif]--><?php switch($setting->type):
                                    case ('text'): ?>
                                        <i class="fas fa-font me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('textarea'): ?>
                                        <i class="fas fa-align-left me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('number'): ?>
                                        <i class="fas fa-hashtag me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('select'): ?>
                                        <i class="fas fa-list me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('checkbox'): ?>
                                        <i class="fas fa-check-square me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('file'): ?>
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('image'): ?>
                                        <i class="fas fa-image me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('image_multiple'): ?>
                                        <i class="fas fa-images me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('color'): ?>
                                        <i class="fas fa-palette me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('date'): ?>
                                        <i class="fas fa-calendar me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('email'): ?>
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('password'): ?>
                                        <i class="fas fa-key me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('tel'): ?>
                                        <i class="fas fa-phone me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('url'): ?>
                                        <i class="fas fa-globe me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php case ('time'): ?>
                                        <i class="fas fa-clock me-2 text-primary"></i>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <i class="fas fa-cog me-2 text-primary"></i>
                                <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php echo e($setting->label); ?>

                            </h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <!--[if BLOCK]><![endif]--><?php switch($setting->type):
                                case ('textarea'): ?>
                                    <textarea wire:model="values.<?php echo e($setting->id); ?>" class="form-control" rows="3" 
                                        placeholder="Değeri buraya giriniz..."></textarea>
                                    <?php break; ?>
                                
                                <?php case ('select'): ?>
                                    <!--[if BLOCK]><![endif]--><?php if(is_array($setting->options)): ?>
                                        <select wire:model="values.<?php echo e($setting->id); ?>" class="form-select">
                                            <option value="">Seçiniz</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $setting->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>"><?php echo e(is_string($label) ? $label : json_encode($label)); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php break; ?>
                                
                                <?php case ('checkbox'): ?>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" id="value-<?php echo e($setting->id); ?>" class="form-check-input" 
                                            wire:model="values.<?php echo e($setting->id); ?>"
                                            <?php if(isset($values[$setting->id]) && $values[$setting->id] == 1): ?> checked <?php endif; ?>>
                                        <label class="form-check-label" for="value-<?php echo e($setting->id); ?>">
                                            <?php echo e(isset($values[$setting->id]) && $values[$setting->id] == 1 ? 'Evet' : 'Hayır'); ?>

                                        </label>
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('file'): ?>
                                    <div class="form-group mb-3">
                                        <?php echo $__env->make('settingmanagement::livewire.partials.file-upload', [
                                            'fileKey' => $setting->id,
                                            'label' => 'Dosyayı sürükleyip bırakın veya tıklayın',
                                            'values' => $values
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('image'): ?>
                                    <div class="form-group mb-3">
                                        <?php echo $__env->make('settingmanagement::livewire.partials.image-upload', [
                                            'imageKey' => $setting->id,
                                            'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                            'values' => $values
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    <?php break; ?>
                                    
                                <?php case ('image_multiple'): ?>
                                    <div class="form-group mb-3">
                                        <!-- Mevcut Çoklu Resimler -->
                                        <?php
                                            $currentImages = isset($multipleImagesArrays[$setting->id]) ? $multipleImagesArrays[$setting->id] : [];
                                        ?>
                                        
                                        <?php echo $__env->make('settingmanagement::livewire.partials.existing-multiple-images', [
                                            'settingId' => $setting->id,
                                            'images' => $currentImages
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        
                                        <!-- Yükleme Alanı -->
                                        <div class="card mt-3">
                                            <div class="card-body p-3">
                                                <form wire:submit="updatedTempPhoto">
                                                    <div class="dropzone p-4" onclick="document.getElementById('file-upload-<?php echo e($setting->id); ?>').click()">
                                                        <input type="file" id="file-upload-<?php echo e($setting->id); ?>" class="d-none" 
                                                            wire:model="tempPhoto" accept="image/*" multiple
                                                            wire:click="setPhotoField('<?php echo e($setting->id); ?>')">
                                                            
                                                        <div class="text-center">
                                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                            <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                                            <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 2MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <!-- Geçici yüklenen görseller -->
                                        <!--[if BLOCK]><![endif]--><?php if(isset($temporaryMultipleImages[$setting->id]) && is_array($temporaryMultipleImages[$setting->id]) && count($temporaryMultipleImages[$setting->id]) > 0): ?>
                                            <div class="mt-3">
                                                <label class="form-label">Yeni Yüklenen Görseller</label>
                                                <div class="row g-2">
                                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $temporaryMultipleImages[$setting->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <!--[if BLOCK]><![endif]--><?php if($photo): ?>
                                                        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                                            <div class="position-relative">
                                                                <div class="position-absolute top-0 end-0 p-1">
                                                                    <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                                            wire:click="removeMultipleImageField(<?php echo e($setting->id); ?>, <?php echo e($index); ?>)">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="img-responsive img-responsive-1x1 rounded border" 
                                                                     style="background-image: url(<?php echo e($photo->temporaryUrl()); ?>)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <?php break; ?>

                                <?php case ('color'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Renk seçimi</label>
                                        <input type="color" class="form-control form-control-color" 
                                               value="<?php echo e($values[$setting->id] ?? '#ffffff'); ?>" 
                                               wire:model="values.<?php echo e($setting->id); ?>"
                                               title="Renk seçin">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('date'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                        <input type="date" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('time'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                        <input type="time" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('number'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="number" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('email'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <input type="email" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('password'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-key"></i>
                                        </span>
                                        <input type="password" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('tel'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-phone"></i>
                                        </span>
                                        <input type="tel" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php case ('url'): ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-globe"></i>
                                        </span>
                                        <input type="url" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                                    <?php break; ?>
                                
                                <?php default: ?>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="fas fa-font"></i>
                                        </span>
                                        <input type="text" wire:model="values.<?php echo e($setting->id); ?>" class="form-control">
                                    </div>
                            <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
                            
                            <!--[if BLOCK]><![endif]--><?php if(isset($originalValues[$setting->id]) && $originalValues[$setting->id] != $values[$setting->id]): ?>
                                <div class="mt-2 text-end">
                                    <span class="badge bg-yellow cursor-pointer" wire:click="resetToDefault(<?php echo e($setting->id); ?>)">
                                        <i class="fas fa-undo me-1"></i> Varsayılana Döndür
                                    </span>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
        
        <!--[if BLOCK]><![endif]--><?php if(count($changes) > 0): ?>
            <div class="alert alert-success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo e(count($changes)); ?> adet değişiklik yapıldı. Lütfen değişiklikleri kaydedin.
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" wire:click="save(false)">
                            <i class="fas fa-save me-2"></i> 
                            <span wire:loading.remove wire:target="save">Değişiklikleri Kaydet</span>
                            <span wire:loading wire:target="save">Kaydediliyor...</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <a href="<?php echo e(route('admin.settingmanagement.items', $groupId)); ?>" class="btn btn-link text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
        </a>

        <div class="d-flex gap-2">
            <button type="button" class="btn" wire:click="save(false)" wire:loading.attr="disabled"
                wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(false)">
                        <i class="fa-thin fa-plus me-2"></i> Kaydet ve Devam Et
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(false)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet ve Devam Et
                    </span>
                </span>
            </button>

            <button type="button" class="btn btn-primary ms-4" wire:click="save(true)"
                wire:loading.attr="disabled" wire:target="save">
                <span class="d-flex align-items-center">
                    <span class="ms-2" wire:loading.remove wire:target="save(true)">
                        <i class="fa-thin fa-floppy-disk me-2"></i> Kaydet
                    </span>
                    <span class="ms-2" wire:loading wire:target="save(true)">
                        <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> Kaydet
                    </span>
                </span>
            </button>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
<?php $__env->stopPush(); ?><?php /**PATH C:\laragon\www\laravel\Modules/SettingManagement\resources/views/livewire/values-component.blade.php ENDPATH**/ ?>