<div class="col-12 mb-4">
    <div class="d-flex align-items-center mb-3">
        <i class="fas fa-columns me-2 text-primary"></i>
        <h4 class="m-0 fw-bold text-primary">
            {{ $element['properties']['label'] ?? 'Satır Düzeni' }}
        </h4>
    </div>
    <div class="row g-3">
        @if(isset($element['columns']) && is_array($element['columns']))
            @foreach($element['columns'] as $column)
                <div class="col-12 col-md-{{ $column['width'] ?? 12 }}">
                    @if(isset($column['elements']) && is_array($column['elements']))
                        @foreach($column['elements'] as $columnElement)
                            @include('settingmanagement::livewire.partials.form-elements.' . $columnElement['type'], [
                                'element' => $columnElement,
                                'values' => $values,
                                'settings' => $settings,
                                'originalValues' => $originalValues ?? [],
                                'temporaryImages' => $temporaryImages ?? [],
                                'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                'multipleImagesArrays' => $multipleImagesArrays ?? []
                            ])
                        @endforeach
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>