@php
    $content = $element['properties']['content'] ?? $element['content'] ?? 'Başlık Metni';
    $size = $element['properties']['size'] ?? $element['size'] ?? 'h3';
    $align = $element['properties']['align'] ?? $element['align'] ?? 'left';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-body text-center">
            <{{ $size }} class="text-{{ $align === 'center' ? 'center' : ($align === 'right' ? 'end' : 'start') }}">
                {{ $content }}
            </{{ $size }}>
        </div>
    </div>
</div>