@php
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $height = isset($element['properties']['height']) ? $element['properties']['height'] : 20;
    $spacerId = 'spacer-' . uniqid();
@endphp

<div class="col-{{ $width }}" id="{{ $spacerId }}">
    <div style="height: {{ $height }}px;"></div>
</div>