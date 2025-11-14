@php
    // Alert özellikleri
    $variant = $element['variant'] ?? 'info'; // info, warning, danger, success
    $content = $element['content'] ?? 'Alert içeriği';
    $width = $element['width'] ?? 12;
    $dismissible = $element['dismissible'] ?? false;

    // Icon mapping
    $iconMap = [
        'info' => 'fas fa-info-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'danger' => 'fas fa-times-circle',
        'success' => 'fas fa-check-circle',
    ];
    $icon = $iconMap[$variant] ?? 'fas fa-info-circle';
@endphp

<div class="col-{{ $width }}">
    <div class="alert alert-{{ $variant }} @if($dismissible) alert-dismissible fade show @endif mb-3" role="alert">
        <div class="d-flex">
            <div>
                <i class="{{ $icon }} me-2"></i>
            </div>
            <div class="flex-fill">
                {!! $content !!}
            </div>
        </div>
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    </div>
</div>
