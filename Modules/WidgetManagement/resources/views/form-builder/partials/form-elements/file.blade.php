@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    
    $fieldValue = $formData[$fieldName] ?? '';
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-file me-2 text-primary"></i>
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                <div x-data="{ 
                    isDropping: false,
                    handleDrop(event) {
                        event.preventDefault();
                        if (event.dataTransfer.files.length > 0) {
                            @this.upload('temporaryImages.{{ $fieldName }}', event.dataTransfer.files[0]);
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
                                <div class="dropzone" onclick="document.getElementById('fileInput_file_{{ $fieldName }}').click()">
                                    <div class="dropzone-files"></div>
                                    <div class="d-flex flex-column align-items-center justify-content-center p-4">
                                        <i class="fa-solid fa-cloud-arrow-up fa-2x mb-2 text-muted"></i>
                                        <div class="text-muted">
                                            <span x-show="!isDropping">Dosyayı sürükleyip bırakın veya tıklayın</span>
                                            <span x-show="isDropping" class="text-primary">Bırakın!</span>
                                        </div>
                                    </div>
                                    <input type="file" id="fileInput_file_{{ $fieldName }}"
                                        wire:model="temporaryImages.{{ $fieldName }}" class="d-none"
                                        accept="*/*" />
                                </div>

                                <div class="progress-container" style="height: 10px;">
                                    <div class="progress progress-sm mt-2" wire:loading
                                        wire:target="temporaryImages.{{ $fieldName }}">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            style="width: 100%"></div>
                                    </div>
                                </div>

                                @error('temporaryImages.' . $fieldName)
                                <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card">
                            <div class="card-body p-3">
                                @if (isset($temporaryImages[$fieldName]))
                                <div class="position-relative" style="height: 100px;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                        wire:click="removeImage('{{ $fieldName }}')" wire:loading.attr="disabled">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                                            <p class="mb-0 mt-2">{{ $temporaryImages[$fieldName]->getClientOriginalName() }}</p>
                                        </div>
                                    </div>
                                </div>
                                @elseif (!empty($formData[$fieldName]))
                                <div class="position-relative" style="height: 100px;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                        wire:click="deleteMedia('{{ $fieldName }}')" wire:loading.attr="disabled">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div class="text-center">
                                            <i class="fa-solid fa-file fa-3x text-muted"></i>
                                            <p class="mb-0 mt-2">{{ basename($formData[$fieldName]) }}</p>
                                            <a href="{{ cdn($formData[$fieldName]) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-eye me-1"></i> Görüntüle
                                            </a>
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
                
                @if($helpText)
                    <div class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ $helpText }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>