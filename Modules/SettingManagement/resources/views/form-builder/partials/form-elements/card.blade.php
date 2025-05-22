<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-square me-2 text-primary"></i>
                    {{ $element['properties']['title'] ?? 'Kart' }}
                </h3>
            </div>
        </div>
        
        <div class="card-body">
            @if(isset($element['properties']['content']))
                <p>{{ $element['properties']['content'] }}</p>
            @endif
            
            @if(isset($element['elements']) && is_array($element['elements']))
                <div class="row">
                    @foreach($element['elements'] as $cardElement)
                        @include('settingmanagement::form-builder.partials.form-elements.' . $cardElement['type'], [
                            'element' => $cardElement,
                            'values' => $values,
                            'settings' => $settings,
                            'originalValues' => $originalValues ?? [],
                            'temporaryImages' => $temporaryImages ?? [],
                            'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                            'multipleImagesArrays' => $multipleImagesArrays ?? []
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>