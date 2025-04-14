@props(['title' => '', 'icon' => null, 'open' => true, 'id' => null])

<div {{ $attributes->merge(['class' => 'studio-panel', 'id' => $id]) }}>
    <div class="studio-panel-header" @if(!$open) data-bs-toggle="collapse" data-bs-target="#{{ $id }}-content" @endif>
        <h3 class="studio-panel-title">
            @if($icon)
                <i class="fa {{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h3>
        
        @if(!$open)
        <div class="studio-panel-toggle">
            <i class="fa fa-chevron-down"></i>
        </div>
        @endif
    </div>
    
    <div class="studio-panel-content @if(!$open) collapse @endif" id="{{ $id }}-content">
        {{ $slot }}
    </div>
</div>

<style>
    .studio-panel {
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        background-color: #fff;
        overflow: hidden;
    }
    
    .studio-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
    }
    
    .studio-panel-title {
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
    }
    
    .studio-panel-content {
        padding: 1rem;
    }
    
    .studio-panel-toggle i {
        transition: transform 0.2s;
    }
    
    .studio-panel-header[aria-expanded="true"] .studio-panel-toggle i {
        transform: rotate(180deg);
    }
</style>