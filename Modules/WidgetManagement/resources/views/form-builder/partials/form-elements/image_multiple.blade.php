@php
    $fieldName = $element['name'] ?? '';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? [];
        $model = 'formData';
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? [];
        $model = 'settings';
    } else {
        $fieldValue = [];
        $model = null;
    }
    
    if (!is_array($fieldValue)) {
        $fieldValue = [];
    }

    // Livewire için benzersiz bir ID oluştur
    $settingId = isset($element['id']) ? $element['id'] : (isset($element['name']) ? md5($element['name']) : uniqid());
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
                @if(isset($formData))
                    <div class="row">
                        @if(!empty($fieldValue))
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mevcut Görseller</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            @foreach($fieldValue as $index => $image)
                                                <div class="col-6 col-md-4 col-lg-3 mb-3">
                                                    @include('widgetmanagement::form-builder.partials.multiple-image-upload', [
                                                        'settingId' => $settingId,
                                                        'index' => $index,
                                                        'model' => $model,
                                                        'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                                        'values' => ['image' => cdn($image)],
                                                        'isExisting' => true,
                                                        'fieldName' => $fieldName
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h4 class="card-title">Yeni Görsel Ekle</h4>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                wire:click="addMultipleImageField('{{ $fieldName }}')">
                                            <i class="fas fa-plus me-1"></i> Yeni Görsel Ekle
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        @if(isset($multipleImageFields[$fieldName]) && count($multipleImageFields[$fieldName]) > 0)
                                            @foreach($multipleImageFields[$fieldName] as $index => $field)
                                                <div class="col-12 col-md-6 col-lg-4 mb-3">
                                                    @include('widgetmanagement::form-builder.partials.multiple-image-upload', [
                                                        'settingId' => $settingId,
                                                        'index' => $index,
                                                        'model' => $model,
                                                        'label' => 'Görseli sürükleyip bırakın veya tıklayın',
                                                        'isRequired' => $isRequired
                                                    ])
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Henüz yükleme alanı eklenmemiş. "Yeni Görsel Ekle" butonuna tıklayarak görsel yükleme alanı ekleyebilirsiniz.
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        @if(!empty($fieldValue))
                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Mevcut Görseller</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            @foreach($fieldValue as $index => $image)
                                                <div class="col-6 col-md-4 col-lg-3">
                                                    <div class="position-relative" style="height: 150px;">
                                                        <img src="{{ cdn($image) }}" 
                                                            alt="Resim {{ $index + 1 }}" 
                                                            class="img-fluid rounded h-100 w-100 object-fit-cover">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <input type="file" 
                                wire:model="temporaryUpload.{{ str_replace('widget.', '', $fieldName) }}" 
                                class="form-control @error('temporaryUpload.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror"
                                accept="image/*"
                                multiple
                                @if($isRequired && empty($fieldValue)) required @endif>
                            @error('temporaryUpload.' . str_replace('widget.', '', $fieldName))
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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