@php
    $columns = $element['columns'] ?? [];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-body">
            <div class="row g-3">
                @foreach($columns as $column)
                    <div class="col-md-{{ $column['width'] ?? 6 }}">
                        <div class="border rounded p-3" style="min-height: 100px; background-color: #f8f9fa;">
                            @if(isset($column['elements']) && is_array($column['elements']))
                                @foreach($column['elements'] as $columnElement)
                                    @if(isset($formData))
                                        @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                            'element' => $columnElement,
                                            'formData' => $formData ?? [],
                                            'temporaryImages' => $temporaryImages ?? [],
                                            'photos' => $photos ?? []
                                        ])
                                    @else
                                        @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                            'element' => $columnElement,
                                            'settings' => $settings ?? [],
                                            'temporaryUpload' => $temporaryUpload ?? []
                                        ])
                                    @endif
                                @endforeach
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-plus mb-2"></i>
                                    <p class="small">Bu sütuna form elemanları eklenecek</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>