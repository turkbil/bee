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
                <div class="position-relative" style="height: 156px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="removeImage('{{ $fileKey }}')" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    @php
                        $fileName = $temporaryImages[$fileKey]->getClientOriginalName();
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    
                    @if ($isImage)
                        <img src="{{ $temporaryImages[$fileKey]->temporaryUrl() }}"
                            class="img-fluid rounded h-100 w-100 object-fit-cover" alt="{{ $fileName }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fa-solid fa-file fa-3x text-muted"></i>
                                <p class="mb-0 mt-2 small">{{ $fileName }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="position-absolute bottom-0 start-0 end-0 bg-warning text-dark text-center py-1 rounded-bottom">
                        <small><i class="fa-solid fa-clock me-1"></i>Kaydetmeyi bekliyor</small>
                    </div>
                </div>
                @elseif (!empty($values[$fileKey]))
                <div class="position-relative" style="height: 156px;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                        wire:click="deleteMedia({{ $fileKey }})" wire:loading.attr="disabled">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    @php
                        $fileName = basename($values[$fileKey]);
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    
                    @if ($isImage)
                        <img src="{{ cdn($values[$fileKey]) }}"
                            class="img-fluid rounded h-100 w-100 object-fit-cover" alt="{{ $fileName }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-center">
                                <i class="fa-solid fa-file fa-3x text-muted"></i>
                                <p class="mb-0 mt-2 small">{{ $fileName }}</p>
                                <a href="{{ cdn($values[$fileKey]) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-eye me-1"></i> Görüntüle
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                @else
                <div class="d-flex align-items-center justify-content-center text-muted" style="height: 156px;">
                    <i class="fa-solid fa-file-circle-xmark fa-2x"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>