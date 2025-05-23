@php
    // Element dizisinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    // Kart başlığını ve içeriğini doğrudan al (properties içinde olmayabilir)
    $cardTitle = isset($element['title']) ? $element['title'] : (isset($element['label']) ? $element['label'] : 'Kart');
    $cardContent = isset($element['content']) ? $element['content'] : null;
    
    // Değişkenlerin var olduğunu kontrol edelim
    if (!isset($values) || !is_array($values)) {
        $values = [];
    }
    
    if (!isset($settings) || !is_object($settings) && !is_array($settings)) {
        $settings = is_object($settings) ? $settings : collect([]);
    }
    
    if (!isset($originalValues) || !is_array($originalValues)) {
        $originalValues = [];
    }
    
    if (!isset($formData) || !is_array($formData)) {
        $formData = [];
    }
    
    if (!isset($temporaryImages) || !is_array($temporaryImages)) {
        $temporaryImages = [];
    }
    
    if (!isset($temporaryMultipleImages) || !is_array($temporaryMultipleImages)) {
        $temporaryMultipleImages = [];
    }
    
    if (!isset($multipleImagesArrays) || !is_array($multipleImagesArrays)) {
        $multipleImagesArrays = [];
    }
@endphp

<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-square me-2 text-primary"></i>
                    {{ $cardTitle }}
                </h3>
            </div>
        </div>
        
        <div class="card-body">
            @if($cardContent)
                <p>{{ $cardContent }}</p>
            @endif
            
            @if(isset($element['elements']) && is_array($element['elements']))
                <div class="row">
                    @foreach($element['elements'] as $cardElement)
                        @php
                            // Element tipini güvenli bir şekilde kontrol et
                            $elementType = isset($cardElement['type']) ? $cardElement['type'] : 'text';
                            // View'un var olup olmadığını kontrol et
                            $viewPath = 'widgetmanagement::form-builder.partials.form-elements.' . $elementType;
                        @endphp
                        
                        @if(view()->exists($viewPath))
                            @include($viewPath, [
                                'element' => $cardElement,
                                'values' => $values,
                                'settings' => $settings,
                                'originalValues' => $originalValues,
                                'temporaryImages' => $temporaryImages,
                                'temporaryMultipleImages' => $temporaryMultipleImages,
                                'multipleImagesArrays' => $multipleImagesArrays,
                                'formData' => $formData,
                                'originalData' => $originalValues
                            ])
                        @else
                            <div class="alert alert-warning">
                                '{{ $elementType }}' türü için görünüm bulunamadı.
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>