<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-columns me-2 text-primary"></i>
                    {{ $element['properties']['label'] ?? 'Satır Düzeni' }}
                </h3>
            </div>
        </div>
        <div class="card-body">
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
    </div>
</div>