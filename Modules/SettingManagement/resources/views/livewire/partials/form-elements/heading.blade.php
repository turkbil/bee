<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-heading me-2 text-primary"></i>
                    Başlık Elementi
                </h3>
            </div>
        </div>
        <div class="card-body">
            @php
                $headingLevel = isset($element['properties']['size']) ? $element['properties']['size'] : 'h3';
                $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Başlık';
                $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
            @endphp
            
            <{{ $headingLevel }} class="text-{{ $align }} mb-0">{{ $content }}</{{ $headingLevel }}>
        </div>
    </div>
</div>