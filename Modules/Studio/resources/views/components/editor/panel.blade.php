@props(['title', 'icon' => null])

<div {{ $attributes->merge(['class' => 'studio-panel']) }}>
    <div class="studio-panel-header">
        @if($icon)
            <i class="{{ $icon }} panel-icon"></i>
        @endif
        <h3 class="panel-title">{{ $title }}</h3>
        <div class="panel-actions">
            {{ $actions ?? '' }}
        </div>
    </div>
    <div class="studio-panel-body">
        {{ $slot }}
    </div>
</div>