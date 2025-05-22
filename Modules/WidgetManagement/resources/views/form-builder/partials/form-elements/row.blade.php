@php
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $gutter = isset($element['properties']['gutter']) ? $element['properties']['gutter'] : 3;
    $rowId = 'row-' . uniqid();
    $columns = isset($element['columns']) && is_array($element['columns']) ? $element['columns'] : [];
@endphp

<div class="col-{{ $width }} mb-3" id="{{ $rowId }}">
    <div class="row g-{{ $gutter }}">
        @foreach($columns as $index => $column)
            @php
                $colWidth = $column['width'] ?? 12;
                $colWidthMd = isset($column['width_md']) ? $column['width_md'] : null;
                $colId = 'col-' . $rowId . '-' . $index;
            @endphp
            
            <div id="{{ $colId }}" class="col-{{ $colWidth }} {{ $colWidthMd ? 'col-md-' . $colWidthMd : '' }}">
                @if(isset($column['elements']) && is_array($column['elements']))
                    @foreach($column['elements'] as $columnElement)
                        @if(isset($formData))
                            @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                'element' => $columnElement,
                                'formData' => $formData ?? []
                            ])
                        @else
                            @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                'element' => $columnElement,
                                'settings' => $settings ?? []
                            ])
                        @endif
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>