<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-paragraph me-2 text-primary"></i>
                    Paragraf Elementi
                </h3>
            </div>
        </div>
        <div class="card-body">
            @php
                $content = isset($element['properties']['content']) ? $element['properties']['content'] : 'Paragraf metni';
                $align = isset($element['properties']['align']) ? $element['properties']['align'] : 'left';
            @endphp
            
            <p class="text-{{ $align }} mb-0">{{ $content }}</p>
        </div>
    </div>
</div>