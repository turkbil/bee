@php
    $fieldType = $element['type'] ?? 'heading';
    $fieldLabel = $element['label'] ?? 'Başlık';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
    $content = isset($element['properties']['content']) ? $element['properties']['content'] : $fieldLabel;
    $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    $color = isset($element['properties']['color']) ? $element['properties']['color'] : 'primary';
    $margin = isset($element['properties']['margin']) ? $element['properties']['margin'] : 4;
    $marginTop = isset($element['properties']['margin_top']) ? $element['properties']['margin_top'] : 0;
    $marginBottom = isset($element['properties']['margin_bottom']) ? $element['properties']['margin_bottom'] : $margin;
    $fontWeight = isset($element['properties']['font_weight']) ? $element['properties']['font_weight'] : 'bold';
    $fontSize = isset($element['properties']['font_size']) ? $element['properties']['font_size'] : '';
    $fontFamily = isset($element['properties']['font_family']) ? "font-family: '{$element['properties']['font_family']}'" : '';
    $letterSpacing = isset($element['properties']['letter_spacing']) ? "letter-spacing: {$element['properties']['letter_spacing']}px" : '';
    $lineHeight = isset($element['properties']['line_height']) ? "line-height: {$element['properties']['line_height']}" : '';
    $textTransform = isset($element['properties']['text_transform']) ? "text-transform: {$element['properties']['text_transform']}" : '';
    $textDecoration = isset($element['properties']['text_decoration']) ? "text-decoration: {$element['properties']['text_decoration']}" : '';
    $shadow = isset($element['properties']['text_shadow']) && $element['properties']['text_shadow'] ? "text-shadow: 1px 1px 3px rgba(0,0,0,0.3)" : '';
    $uppercase = isset($element['properties']['uppercase']) && $element['properties']['uppercase'] ? 'text-uppercase' : '';
    $id = isset($element['properties']['id']) ? $element['properties']['id'] : 'heading-' . uniqid();
    $cssClass = isset($element['properties']['class']) ? $element['properties']['class'] : '';
    $divider = isset($element['properties']['divider']) && $element['properties']['divider'];
    $dividerWidth = isset($element['properties']['divider_width']) ? $element['properties']['divider_width'] : 50;
    $dividerColor = isset($element['properties']['divider_color']) ? $element['properties']['divider_color'] : $color;
    $dividerThickness = isset($element['properties']['divider_thickness']) ? $element['properties']['divider_thickness'] : 3;
    $dividerStyle = isset($element['properties']['divider_style']) ? $element['properties']['divider_style'] : 'solid';
    $dividerPosition = isset($element['properties']['divider_position']) ? $element['properties']['divider_position'] : 'bottom';
    $dividerSpacing = isset($element['properties']['divider_spacing']) ? $element['properties']['divider_spacing'] : 10;
@endphp

<div class="col-{{ $width }} mb-{{ $marginBottom }} mt-{{ $marginTop }}">
    <{{ $headingLevel }} id="{{ $id }}" class="text-{{ $align }} fw-{{ $fontWeight }} text-{{ $color }} {{ $fontSize }} {{ $uppercase }} {{ $cssClass }}" style="{{ $fontFamily }}; {{ $letterSpacing }}; {{ $lineHeight }}; {{ $textTransform }}; {{ $textDecoration }}; {{ $shadow }}">
        {!! $content !!}
        
        @if($divider && $dividerPosition == 'right')
            <span style="display: inline-block; vertical-align: middle; margin-left: {{ $dividerSpacing }}px; width: {{ $dividerWidth }}px; border-top: {{ $dividerThickness }}px {{ $dividerStyle }} var(--tblr-{{ $dividerColor }});"></span>
        @endif
    </{{ $headingLevel }}>
    
    @if($divider && $dividerPosition == 'bottom')
        <div class="mt-{{ $dividerSpacing / 5 }}" style="width: {{ $dividerWidth }}px; border-top: {{ $dividerThickness }}px {{ $dividerStyle }} var(--tblr-{{ $dividerColor }});"></div>
    @endif
</div>