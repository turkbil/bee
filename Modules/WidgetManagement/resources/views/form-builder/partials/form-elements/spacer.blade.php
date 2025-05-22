@php
    $fieldType = $element['type'] ?? 'spacer';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $height = isset($element['properties']['height']) ? $element['properties']['height'] : 20;
    $visible = isset($element['properties']['visible_md']) && $element['properties']['visible_md'] ? 'd-none d-md-block' : '';
    $responsiveHeight = isset($element['properties']['responsive_height']) && is_array($element['properties']['responsive_height']) ? $element['properties']['responsive_height'] : [];
    $heightSm = $responsiveHeight['sm'] ?? $height;
    $heightMd = $responsiveHeight['md'] ?? $height;
    $heightLg = $responsiveHeight['lg'] ?? $height;
    $heightXl = $responsiveHeight['xl'] ?? $height;
    $backgroundColor = isset($element['properties']['background_color']) ? $element['properties']['background_color'] : 'transparent';
    $spacerId = 'spacer-' . uniqid();
@endphp

<div class="col-{{ $width }} {{ $visible }}" id="{{ $spacerId }}">
    <div style="height: {{ $height }}px; background-color: {{ $backgroundColor }};"></div>
</div>

@if(!empty($responsiveHeight))
<style>
    @media (min-width: 576px) {
        #{{ $spacerId }} > div {
            height: {{ $heightSm }}px !important;
        }
    }
    @media (min-width: 768px) {
        #{{ $spacerId }} > div {
            height: {{ $heightMd }}px !important;
        }
    }
    @media (min-width: 992px) {
        #{{ $spacerId }} > div {
            height: {{ $heightLg }}px !important;
        }
    }
    @media (min-width: 1200px) {
        #{{ $spacerId }} > div {
            height: {{ $heightXl }}px !important;
        }
    }
</style>
@endif