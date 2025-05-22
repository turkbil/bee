@php
    $style = $element['properties']['style'] ?? $element['style'] ?? 'solid';
    $color = $element['properties']['color'] ?? $element['color'] ?? '#e5e7eb';
    $thickness = $element['properties']['thickness'] ?? $element['thickness'] ?? '1px';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-body d-flex align-items-center">
            <hr class="flex-fill" style="border: none; height: {{ $thickness }}; background-color: {{ $color }}; border-top: {{ $thickness }} {{ $style }} {{ $color }};">
        </div>
    </div>
</div>