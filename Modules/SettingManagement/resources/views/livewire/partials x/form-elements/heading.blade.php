<div class="col-12 mb-4">
    @php
        $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
        $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Başlık';
        $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    @endphp
    
    <{{ $headingLevel }} class="text-{{ $align }} fw-bold text-primary">{{ $content }}</{{ $headingLevel }}>
</div>