@props(['title', 'subtitle' => null, 'icon' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if($title || $icon)
    <div class="card-header">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <h5 class="card-title">{{ $title }}</h5>
        @if($subtitle)
            <h6 class="card-subtitle text-muted">{{ $subtitle }}</h6>
        @endif
    </div>
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
    @if($footer)
    <div class="card-footer">
        {{ $footer }}
    </div>
    @endif
</div>