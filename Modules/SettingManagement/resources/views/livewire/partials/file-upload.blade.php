<div x-data="{ 
    isDropping: false,
    handleDrop(event) {
        event.preventDefault();
        if (event.dataTransfer.files.length > 0) {
            @this.upload('temporaryImages.{{ $fileKey }}', event.dataTransfer.files[0]);
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
                <div class="dropzone" onclick="document.getElementById('fileInput_file_{{ $fileKey }}').click()">
                    <div class="dropzone-files"></div>
                    <div class="d-flex flex-column align-items-center justify-content-center p-4">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                        <div class="text-muted">
                            <span x-show="!isDropping">{{ $label ?? 'Dosyayı sürükleyip bırakın veya tıklayın' }}</span>
                            <span x-show="isDropping" class="text-primary">Bırakın!</span>
                        </div>
                    </div>
                    <input type="file" id="fileInput_file_{{ $fileKey }}"
                        wire:model="temporaryImages.{{ $fileKey }}" class="d-none"
                        accept="*/*" />
                </div>

                <!-- Progress Bar Alanı -->
                <div class="progress-container" style="height: 10px;">
                    <div class="progress progress-sm mt-2" wire:loading
                        wire:target="temporaryImages.{{ $fileKey }}">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            style="width: 100%"></div>
                    </div>
                </div>

                @error('temporaryImages.' . $fileKey)
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body p-3">
                @if (isset($temporaryImages[$fileKey]))
                <div class="position-relative" style="height: 100px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="deleteMedia({{ $fileKey }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                            <p class="mb-0 mt-2">{{ $temporaryImages[$fileKey]->getClientOriginalName() }}</p>
                        </div>
                    </div>
                </div>
                @elseif (isset($setting) && method_exists($setting, 'getFirstMedia') && $setting->getFirstMedia('files'))
                <div class="position-relative" style="height: 100px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="deleteMedia({{ $fileKey }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                            <p class="mb-0 mt-2">{{ $setting->getFirstMedia('files')->file_name }}</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 100px;">
                    <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>