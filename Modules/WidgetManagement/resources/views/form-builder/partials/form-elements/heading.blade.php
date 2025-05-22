@php
    $content = isset($element['properties']['content']) ? $element['properties']['content'] : ($element['label'] ?? 'Başlık');
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
    $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    $color = isset($element['properties']['color']) ? $element['properties']['color'] : 'primary';
    $fontWeight = isset($element['properties']['font_weight']) ? $element['properties']['font_weight'] : 'bold';
    $divider = isset($element['properties']['divider']) && $element['properties']['divider'];
@endphp

<div class="col-{{ $width }} mb-3">
    <{{ $headingLevel }} class="text-{{ $align }} fw-{{ $fontWeight }} text-{{ $color }}">
        {!! $content !!}
    </{{ $headingLevel }}>
    
    @if($divider)
        <div class="mt-2" style="width: 50px; border-top: 3px solid var(--tblr-{{ $color }});"></div>
    @endif
</div>