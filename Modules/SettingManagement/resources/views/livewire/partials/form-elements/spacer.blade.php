<div class="col-12">
    @php
        $height = isset($element['properties']['height']) ? $element['properties']['height'] : 20;
    @endphp
    
    <div style="height: {{ $height }}px;"></div>
</div>