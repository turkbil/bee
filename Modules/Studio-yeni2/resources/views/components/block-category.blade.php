@props(['title' => '', 'icon' => null, 'collapsed' => false, 'id' => null])

<div {{ $attributes->merge(['class' => 'block-category ' . ($collapsed ? 'collapsed' : ''), 'id' => $id]) }}>
    <div class="block-category-header">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $title }}
        <i class="toggle-icon fa fa-chevron-down"></i>
    </div>
    
    <div class="block-items" style="{{ $collapsed ? 'display: none;' : '' }}">
        {{ $slot }}
    </div>
</div>

<style>
    .block-category {
        margin-bottom: 10px;
        border-radius: 8px;
        background-color: #fff;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .block-category-header {
        padding: 12px 15px;
        font-weight: 500;
        color: #475569;
        background-color: #f8f9fa;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
    }
    
    .block-category-header:hover {
        background-color: #f0f2f8;
        color: #206bc4;
    }
    
    .block-category-header i:first-child {
        margin-right: 8px;
        color: #206bc4;
        font-size: 14px;
    }
    
    .toggle-icon {
        transition: transform 0.2s ease;
        color: #94a3b8;
        font-size: 12px;
    }
    
    .block-category.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }
    
    .block-items {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        padding: 12px;
        background-color: #fff;
    }
</style>