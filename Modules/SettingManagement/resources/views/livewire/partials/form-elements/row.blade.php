<div class="row mb-3">
    @if(isset($element['columns']) && is_array($element['columns']))
        @foreach($element['columns'] as $column)
            <div class="col-{{ $column['width'] ?? 12 }}">
                @if(isset($column['elements']) && is_array($column['elements']))
                    @foreach($column['elements'] as $columnElement)
                        @include('settingmanagement::livewire.partials.form-elements.' . $columnElement['type'], [
                            'element' => $columnElement,
                            'values' => $values,
                            'settings' => $settings,
                            'temporaryImages' => $temporaryImages ?? [],
                            'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                            'multipleImagesArrays' => $multipleImagesArrays ?? [],
                        ])
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif
</div>