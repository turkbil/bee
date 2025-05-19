<div x-data="{ 
    isDropping: false,
    handleDrop(event) {
        event.preventDefault();
        if (event.dataTransfer.files.length > 0) {
            window.Livewire.find('<?php echo e($_instance->getId()); ?>').upload('temporaryImages.<?php echo e($fileKey); ?>', event.dataTransfer.files[0]);
        }
        this.isDropping = false;
    }
}" 
x-on:dragover.prevent="isDropping = true" 
x-on:dragleave.prevent="isDropping = false"
x-on:drop="handleDrop($event)">
<div class="row align-items-center g-3">
    <div class="col-12 col-md-9">
        <div class="card" :class="{ 'border-primary': isDropping }">
            <div class="card-body">
                <div class="dropzone" onclick="document.getElementById('fileInput_file_<?php echo e($fileKey); ?>').click()">
                    <div class="dropzone-files"></div>
                    <div class="d-flex flex-column align-items-center justify-content-center p-4">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                        <div class="text-muted">
                            <span x-show="!isDropping"><?php echo e($label ?? 'Dosyayı sürükleyip bırakın veya tıklayın'); ?></span>
                            <span x-show="isDropping" class="text-primary">Bırakın!</span>
                        </div>
                    </div>
                    <input type="file" id="fileInput_file_<?php echo e($fileKey); ?>"
                        wire:model="temporaryImages.<?php echo e($fileKey); ?>" class="d-none"
                        accept="*/*" />
                </div>

                <!-- Progress Bar Alanı -->
                <div class="progress-container" style="height: 10px;">
                    <div class="progress progress-sm mt-2" wire:loading
                        wire:target="temporaryImages.<?php echo e($fileKey); ?>">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            style="width: 100%"></div>
                    </div>
                </div>

                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['temporaryImages.' . $fileKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="text-danger small mt-2"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body p-3">
                <!--[if BLOCK]><![endif]--><?php if(isset($temporaryImages[$fileKey])): ?>
                <div class="position-relative" style="height: 100px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="removeImage('<?php echo e($fileKey); ?>')" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                            <p class="mb-0 mt-2"><?php echo e($temporaryImages[$fileKey]->getClientOriginalName()); ?></p>
                        </div>
                    </div>
                </div>
                <?php elseif(!empty($values[$fileKey])): ?>
                <div class="position-relative" style="height: 100px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="deleteMedia(<?php echo e($fileKey); ?>)" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                            <p class="mb-0 mt-2"><?php echo e(basename($values[$fileKey])); ?></p>
                            <a href="<?php echo e(cdn($values[$fileKey])); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-eye me-1"></i> Görüntüle
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                    <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
</div><?php /**PATH C:\laragon\www\laravel\Modules/SettingManagement\resources/views/livewire/partials/file-upload.blade.php ENDPATH**/ ?>