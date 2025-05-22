@php
    $fieldType = $element['type'] ?? 'row';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $marginBottom = isset($element['properties']['margin_bottom']) ? $element['properties']['margin_bottom'] : 4;
    $gutter = isset($element['properties']['gutter']) ? $element['properties']['gutter'] : 3;
    $background = isset($element['properties']['background']) ? $element['properties']['background'] : '';
    $padding = isset($element['properties']['padding']) ? $element['properties']['padding'] : 0;
    $border = isset($element['properties']['border']) && $element['properties']['border'] ? 'border rounded' : '';
    $shadow = isset($element['properties']['shadow']) ? 'shadow-' . $element['properties']['shadow'] : '';
    $equalHeight = isset($element['properties']['equal_height']) && $element['properties']['equal_height'] ? 'h-100' : '';
    $verticalAlign = isset($element['properties']['vertical_align']) ? 'align-items-' . $element['properties']['vertical_align'] : '';
    $horizontalAlign = isset($element['properties']['horizontal_align']) ? 'justify-content-' . $element['properties']['horizontal_align'] : '';
    $rowId = 'row-' . uniqid();
    
    $columns = isset($element['columns']) && is_array($element['columns']) ? $element['columns'] : [];
@endphp

<div class="col-{{ $width }} mb-{{ $marginBottom }}" id="{{ $rowId }}">
    <div class="row g-{{ $gutter }} {{ $border }} {{ $background ? 'bg-' . $background : '' }} {{ $padding ? 'p-' . $padding : '' }} {{ $shadow }} {{ $verticalAlign }} {{ $horizontalAlign }}">
        @foreach($columns as $index => $column)
            @php
                $colWidth = $column['width'] ?? 12;
                $colWidthSm = isset($column['width_sm']) ? $column['width_sm'] : null;
                $colWidthMd = isset($column['width_md']) ? $column['width_md'] : null;
                $colWidthLg = isset($column['width_lg']) ? $column['width_lg'] : null;
                $colWidthXl = isset($column['width_xl']) ? $column['width_xl'] : null;
                $colBackground = isset($column['background']) ? 'bg-' . $column['background'] : '';
                $colPadding = isset($column['padding']) ? 'p-' . $column['padding'] : '';
                $colBorder = isset($column['border']) && $column['border'] ? 'border rounded' : '';
                $colOrder = isset($column['order']) ? 'order-' . $column['order'] : '';
                $colOrderMd = isset($column['order_md']) ? 'order-md-' . $column['order_md'] : '';
                $colHidden = isset($column['hidden']) && $column['hidden'] ? 'd-none' : '';
                $colVisibleMd = isset($column['visible_md']) && $column['visible_md'] ? 'd-none d-md-block' : '';
                $colId = 'col-' . $rowId . '-' . $index;
            @endphp
            
            <div id="{{ $colId }}" class="col-{{ $colWidth }} 
                    {{ $colWidthSm ? 'col-sm-' . $colWidthSm : '' }} 
                    {{ $colWidthMd ? 'col-md-' . $colWidthMd : '' }} 
                    {{ $colWidthLg ? 'col-lg-' . $colWidthLg : '' }} 
                    {{ $colWidthXl ? 'col-xl-' . $colWidthXl : '' }} 
                    {{ $colBackground }} {{ $colPadding }} {{ $colBorder }} {{ $colOrder }} {{ $colOrderMd }} {{ $colHidden }} {{ $colVisibleMd }} {{ $equalHeight }}">
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