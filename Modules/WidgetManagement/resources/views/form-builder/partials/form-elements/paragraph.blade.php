@php
    $fieldLabel = $element['label'] ?? '';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Paragraf metni';
    $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    $color = isset($element['properties']['color']) ? $element['properties']['color'] : 'muted';
    $isSystem = isset($element['system']) && $element['system'];
    $isFirstLarge = isset($element['properties']['first_letter_large']) && $element['properties']['first_letter_large'];
    $id = 'paragraph-' . uniqid();
@endphp

<div class="col-{{ $width }} mb-3">
    @if($fieldLabel)
        <h5 class="mb-2">
            {{ $fieldLabel }}
            @if($isSystem)
                <span class="badge bg-orange ms-1">Sistem</span>
            @endif
        </h5>
    @endif
    
    <p id="{{ $id }}" class="text-{{ $align }} text-{{ $color }}">
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