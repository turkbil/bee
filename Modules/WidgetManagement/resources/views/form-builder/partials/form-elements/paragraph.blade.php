@php
    $content = $element['properties']['content'] ?? $element['content'] ?? 'Paragraf metni burada yer alacak.';
    $align = $element['properties']['align'] ?? $element['align'] ?? 'left';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-body">
            <p class="text-{{ $align === 'center' ? 'center' : ($align === 'right' ? 'end' : 'start') }} mb-0">
                {{ $content }}
            </p>
        </div>
    </div>
</div>