@php
    $fieldName = $element['name'] ?? '';
    $fieldType = $element['type'] ?? 'image_multiple';
    $fieldLabel = $element['label'] ?? '';
    $isRequired = isset($element['required']) && $element['required'];
    $placeholder = $element['placeholder'] ?? 'Görselleri sürükleyip bırakın veya tıklayın';
    $helpText = $element['help_text'] ?? '';
    $isSystem = isset($element['system']) && $element['system'];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $maxImages = isset($element['properties']['max_images']) ? $element['properties']['max_images'] : null;
    $minImages = isset($element['properties']['min_images']) ? $element['properties']['min_images'] : null;
    
    if(isset($formData)) {
        $fieldValue = $formData[$fieldName] ?? [];
    } elseif(isset($settings)) {
        $cleanFieldName = str_replace('widget.', '', $fieldName);
        $fieldValue = $settings[$cleanFieldName] ?? [];
    } else {
        $fieldValue = [];
    }
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
                    <div class="@error('formData.' . $fieldName) is-invalid @enderror">
                        <!-- Mevcut Çoklu Resimler -->
                        @php
                            $currentImages = isset($formData[$fieldName]) && is_array($formData[$fieldName]) ? $formData[$fieldName] : [];
                        @endphp
                        
                        @include('widgetmanagement::form-builder.partials.existing-multiple-images', [
                            'imageKey' => $fieldName,
                            'model' => 'formData',
                            'images' => $currentImages,
                            'isRequired' => $isRequired,
                            'minImages' => $minImages,
                            'maxImages' => $maxImages
                        ])
                        
                        <!-- Yükleme Alanı -->
                        @if(!$maxImages || count($currentImages) < $maxImages)
                            @include('widgetmanagement::form-builder.partials.image-upload-multiple', [
                                'imageKey' => $fieldName,
                                'model' => 'formData',
                                'label' => $placeholder,
                                'currentCount' => count($currentImages),
                                'maxCount' => $maxImages
                            ])
                        @endif
                    </div>
                    @error('formData.' . $fieldName)
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                @else
                    <div class="@error('settings.' . str_replace('widget.', '', $fieldName)) is-invalid @enderror">
                        <!-- Mevcut Çoklu Resimler -->
                        @php
                            $cleanFieldName = str_replace('widget.', '', $fieldName);
                            $currentImages = isset($settings[$cleanFieldName]) && is_array($settings[$cleanFieldName]) ? $settings[$cleanFieldName] : [];
                        @endphp
                        
                        @include('widgetmanagement::form-builder.partials.existing-multiple-images', [
                            'imageKey' => $cleanFieldName,
                            'model' => 'settings',
                            'images' => $currentImages,
                            'isRequired' => $isRequired,
                            'minImages' => $minImages,
                            'maxImages' => $maxImages
                        ])
                        
                        <!-- Yükleme Alanı -->
                        @if(!$maxImages || count($currentImages) < $maxImages)
                            @include('widgetmanagement::form-builder.partials.image-upload-multiple', [
                                'imageKey' => $cleanFieldName,
                                'model' => 'settings',
                                'label' => $placeholder,
                                'currentCount' => count($currentImages),
                                'maxCount' => $maxImages
                            ])
                        @endif
                    </div>
                    @error('settings.' . str_replace('widget.', '', $fieldName))
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
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