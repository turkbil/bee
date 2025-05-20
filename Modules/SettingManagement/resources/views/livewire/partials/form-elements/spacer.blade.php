<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-arrows-alt-v me-2 text-primary"></i>
                    Boşluk Elementi
                </h3>
            </div>
        </div>
        <div class="card-body">
            @php
                $height = isset($element['properties']['height']) ? $element['properties']['height'] : 20;
            @endphp
            
            <div style="height: {{ $height }}px;"></div>
        </div>
    </div>
</div>