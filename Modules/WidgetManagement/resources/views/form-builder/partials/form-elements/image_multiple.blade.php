@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    
    $fieldValue = $formData[$fieldName] ?? [];
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-images me-2 text-primary"></i>
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group w-100">
                @if(!empty($formData[$fieldName]) && is_array($formData[$fieldName]))
                <div class="mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Mevcut Resimler</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                @foreach ($formData[$fieldName] as $imageIndex => $imagePath)
                                <div class="col-md-4 col-lg-3">
                                    <div class="card">
                                        <div class="img-responsive img-responsive-1x1 card-img-top" style="background-image: url('{{ cdn($imagePath) }}')"></div>
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ cdn($imagePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        wire:click="removeExistingMultipleImage('{{ $fieldName }}', {{ $imageIndex }})"
                                                        wire:confirm="Bu resmi silmek istediğinize emin misiniz?">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="card mt-3">
                    <div class="card-body p-3">
                        <div class="dropzone p-4" onclick="document.getElementById('file-upload-{{ $fieldName }}').click()">
                            <input type="file" id="file-upload-{{ $fieldName }}" class="d-none" 
                                wire:model="tempPhoto" accept="image/*" multiple
                                wire:click="setPhotoField('{{ $fieldName }}')">
                                
                            <div class="text-center">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Görselleri sürükleyip bırakın veya tıklayın</h4>
                                <p class="text-muted small">PNG, JPG, WEBP, GIF - Maks 2MB - <strong>Toplu seçim yapabilirsiniz</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(isset($photos[$fieldName]) && is_array($photos[$fieldName]) && count($photos[$fieldName]) > 0)
                    <div class="mt-3">
                        <label class="form-label">Yeni Yüklenen Görseller</label>
                        <div class="row g-2">
                            @foreach($photos[$fieldName] as $index => $photo)
                                @if($photo)
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                    <div class="position-relative">
                                        <div class="position-absolute top-0 end-0 p-1">
                                            <button type="button" class="btn btn-danger btn-icon btn-sm"
                                                    wire:click="removePhoto('{{ $fieldName }}', {{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="img-responsive img-responsive-1x1 rounded border" 
                                            style="background-image: url({{ $photo->temporaryUrl() }})">
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
                
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