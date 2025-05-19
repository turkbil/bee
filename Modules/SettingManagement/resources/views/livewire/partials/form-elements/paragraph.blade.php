<div class="mb-3">
    @php
        $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Paragraf metni';
        $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    @endphp
    
    <p class="text-{{ $align }}">{{ $content }}</p>
</div>