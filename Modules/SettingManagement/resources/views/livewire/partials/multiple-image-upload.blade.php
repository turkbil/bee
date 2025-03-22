<div x-data="{ 
    isDropping: false,
    handleDrop(event) {
        event.preventDefault();
        if (event.dataTransfer.files.length > 0) {
            @this.upload('temporaryMultipleImages.{{ $settingId }}.{{ $index }}', event.dataTransfer.files[0]);
        }
        this.isDropping = false;
    }
}" 
x-on:dragover.prevent="isDropping = true" 
x-on:dragleave.prevent="isDropping = false"
x-on:drop="handleDrop($event)"
class="mb-3">
<div class="row align-items-center g-3">
    <div class="col-12 col-md-9">
        <div class="card" :class="{ 'border-primary': isDropping }">
            <div class="card-body">
                <div class="dropzone" onclick="document.getElementById('fileInput_multiple_{{ $settingId }}_{{ $index }}').click()">
                    <div class="dropzone-files"></div>
                    <div class="d-flex flex-column align-items-center justify-content-center p-4">
                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                        <div class="text-muted">
                            <span x-show="!isDropping">{{ $label ?? 'Görseli sürükleyip bırakın veya tıklayın' }}</span>
                            <span x-show="isDropping" class="text-primary">Bırakın!</span>
                        </div>
                    </div>
                    <input type="file" id="fileInput_multiple_{{ $settingId }}_{{ $index }}"
                        wire:model="temporaryMultipleImages.{{ $settingId }}.{{ $index }}" class="d-none"
                        accept="image/jpeg,image/png,image/webp,image/gif" />
                </div>

                <!-- Progress Bar Alanı -->
                <div class="progress-container" style="height: 10px;">
                    <div class="progress progress-sm mt-2" wire:loading
                        wire:target="temporaryMultipleImages.{{ $settingId }}.{{ $index }}">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            style="width: 100%"></div>
                    </div>
                </div>

                @error('temporaryMultipleImages.' . $settingId . '.' . $index)
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body p-3">
                @if (isset($temporaryMultipleImages[$settingId][$index]))
                <div class="position-relative" style="height: 156px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="removeMultipleImageField({{ $settingId }}, {{ $index }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <img src="{{ $temporaryMultipleImages[$settingId][$index]->temporaryUrl() }}"
                        class="img-fluid rounded h-100 w-100 object-fit-cover" alt="Yüklenen Fotoğraf">
                </div>
                @else
                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 156px;">
                    <i class="fa-solid fa-image-slash fa-2x"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>