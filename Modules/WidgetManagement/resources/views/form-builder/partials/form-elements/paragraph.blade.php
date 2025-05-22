@php
    $fieldType = $element['type'] ?? 'paragraph';
    $fieldLabel = $element['label'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Paragraf metni';
    $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    $color = isset($element['properties']['color']) ? $element['properties']['color'] : 'muted';
    $margin = isset($element['properties']['margin']) ? $element['properties']['margin'] : 4;
    $marginTop = isset($element['properties']['margin_top']) ? $element['properties']['margin_top'] : 0;
    $marginBottom = isset($element['properties']['margin_bottom']) ? $element['properties']['margin_bottom'] : $margin;
    $isSystem = isset($element['system']) && $element['system'];
    $fontWeight = isset($element['properties']['font_weight']) ? $element['properties']['font_weight'] : 'normal';
    $fontSize = isset($element['properties']['font_size']) ? $element['properties']['font_size'] : '';
    $fontFamily = isset($element['properties']['font_family']) ? "font-family: '{$element['properties']['font_family']}'" : '';
    $letterSpacing = isset($element['properties']['letter_spacing']) ? "letter-spacing: {$element['properties']['letter_spacing']}px" : '';
    $lineHeight = isset($element['properties']['line_height']) ? "line-height: {$element['properties']['line_height']}" : '';
    $textTransform = isset($element['properties']['text_transform']) ? "text-transform: {$element['properties']['text_transform']}" : '';
    $textDecoration = isset($element['properties']['text_decoration']) ? "text-decoration: {$element['properties']['text_decoration']}" : '';
    $id = isset($element['properties']['id']) ? $element['properties']['id'] : 'paragraph-' . uniqid();
    $cssClass = isset($element['properties']['class']) ? $element['properties']['class'] : '';
    $isLeadText = isset($element['properties']['lead']) && $element['properties']['lead'] ? 'lead' : '';
    $isFirstLarge = isset($element['properties']['first_letter_large']) && $element['properties']['first_letter_large'];
    $backgroundColor = isset($element['properties']['background_color']) ? $element['properties']['background_color'] : '';
    $padding = isset($element['properties']['padding']) ? $element['properties']['padding'] : '';
    $border = isset($element['properties']['border']) ? $element['properties']['border'] : '';
    $roundedCorners = isset($element['properties']['rounded']) && $element['properties']['rounded'] ? 'rounded' : '';
    $shadow = isset($element['properties']['shadow']) && $element['properties']['shadow'] ? 'shadow' : '';
@endphp

<div class="col-{{ $width }} mb-{{ $marginBottom }} mt-{{ $marginTop }}">
    @if($fieldLabel)
        <h5 class="mb-2">
            {{ $fieldLabel }}
            @if($isSystem)
                <span class="badge bg-orange ms-1">Sistem</span>
            @endif
        </h5>
    @endif
    
    <p id="{{ $id }}" class="text-{{ $align }} text-{{ $color }} fw-{{ $fontWeight }} {{ $isLeadText }} {{ $cssClass }} {{ $backgroundColor ? 'bg-' . $backgroundColor : '' }} {{ $padding ? 'p-' . $padding : '' }} {{ $border ? 'border border-' . $border : '' }} {{ $roundedCorners }} {{ $shadow }}" style="{{ $fontFamily }}; {{ $letterSpacing }}; {{ $lineHeight }}; {{ $textTransform }}; {{ $textDecoration }}">
        @if($isFirstLarge)
            <span class="first-letter">{{ mb_substr($content, 0, 1) }}</span>{!! mb_substr($content, 1) !!}
        @else
            {!! $content !!}
        @endif
    </p>
</div>

@if($isFirstLarge)
<style>
    #{{ $id }} .first-letter {
        float: left;
        font-size: 3em;
        line-height: 0.8;
        margin-right: 0.2em;
        font-weight: bold;
        color: var(--tblr-{{ $color }});
    }
</style>
@endif