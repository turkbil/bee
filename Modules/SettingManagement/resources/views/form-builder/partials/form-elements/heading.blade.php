<div class="col-12">
    @php
        $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
        $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Başlık';
        $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
    @endphp
    
    @switch($headingLevel)
        @case('h1')
            <{{ $headingLevel }} class="page-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h2')
            <{{ $headingLevel }} class="section-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h3')
            <{{ $headingLevel }} class="card-title text-{{ $align }} mb-0">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h4')
            <{{ $headingLevel }} class="section-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @case('h5')
            <{{ $headingLevel }} class="modal-title text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
            @break
        @default
            <{{ $headingLevel }} class="text-{{ $align }}">{{ $content }}</{{ $headingLevel }}>
    @endswitch
</div>