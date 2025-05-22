@php
    // Element ve properties dizilerinin var olduğunu kontrol edelim
    if (!isset($element) || !is_array($element)) {
        $element = [];
    }
    
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    $cardTitle = isset($element['properties']['title']) ? $element['properties']['title'] : 'Kart';
    $cardContent = isset($element['properties']['content']) ? $element['properties']['content'] : null;
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
                                'values' => isset($values) ? $values : [],
                                'settings' => isset($settings) ? $settings : [],
                                'originalValues' => $originalValues ?? [],
                                'temporaryImages' => $temporaryImages ?? [],
                                'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                'multipleImagesArrays' => $multipleImagesArrays ?? [],
                                'formData' => $formData ?? []
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