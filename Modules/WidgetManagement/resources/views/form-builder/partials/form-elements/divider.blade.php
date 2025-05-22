@php
    $fieldType = $element['type'] ?? 'divider';
    $fieldLabel = $element['label'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $thickness = isset($element['properties']['thickness']) ? $element['properties']['thickness'] : 1;
    $style = isset($element['properties']['style']) ? $element['properties']['style'] : 'solid';
    $color = isset($element['properties']['color']) ? $element['properties']['color'] : 'var(--tblr-border-color)';
@endphp

<div class="col-{{ $width }} mb-4">
    @if($fieldLabel)
        <div class="d-flex align-items-center mb-2">
            <span class="text-muted fs-5">{{ $fieldLabel }}</span>
            <hr class="flex-grow-1 ms-2" style="border-top: {{ $thickness }}px {{ $style }} {{ $color }};">
        </div>
    @else
        <hr style="border-top: {{ $thickness }}px {{ $style }} {{ $color }};">
    @endif
</div>