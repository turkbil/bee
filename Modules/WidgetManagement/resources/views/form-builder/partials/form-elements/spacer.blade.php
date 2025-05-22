@php
    $height = $element['properties']['height'] ?? $element['height'] ?? '2rem';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-body text-center text-muted">
            <div style="height: {{ $height }}; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrows-alt-v me-2"></i>
                Boşluk ({{ $height }})
            </div>
        </div>
    </div>
</div>