<div class="mb-3">
    <div class="card">
        @if(isset($element['properties']['title']))
            <div class="card-header">
                <h3 class="card-title">{{ $element['properties']['title'] }}</h3>
            </div>
        @endif
        
        <div class="card-body">
            @if(isset($element['properties']['content']))
                <p>{{ $element['properties']['content'] }}</p>
            @endif
            
            @if(isset($element['elements']) && is_array($element['elements']))
                @foreach($element['elements'] as $cardElement)
                    @include('settingmanagement::livewire.partials.form-elements.' . $cardElement['type'], [
                        'element' => $cardElement,
                        'values' => $values,
                        'settings' => $settings,
                        'temporaryImages' => $temporaryImages ?? [],
                        'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                        'multipleImagesArrays' => $multipleImagesArrays ?? [],
                    ])
                @endforeach
            @endif
        </div>
    </div>
</div>