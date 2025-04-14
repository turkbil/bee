<div class="studio-toolbar">
    <div class="toolbar-left">
        {{ $left ?? '' }}
    </div>
    
    <div class="toolbar-center">
        {{ $center ?? '' }}
    </div>
    
    <div class="toolbar-right">
        {{ $right ?? '' }}
    </div>
</div>

<style>
    .studio-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 16px;
        background-color: #fff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .toolbar-left, .toolbar-center, .toolbar-right {
        display: flex;
        align-items: center;
    }
    
    .toolbar-left {
        flex: 1;
    }
    
    .toolbar-center {
        flex: 1;
        justify-content: center;
    }
    
    .toolbar-right {
        flex: 1;
        justify-content: flex-end;
    }
    
    .toolbar-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 36px;
        padding: 0 12px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        color: #475569;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-right: 4px;
    }
    
    .toolbar-btn:hover {
        background-color: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155;
    }
    
    .toolbar-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    
    .toolbar-btn.active {
        background-color: #e0e7ff;
        border-color: #818cf8;
        color: #4f46e5;
    }
    
    .toolbar-btn i {
        margin-right: 8px;
    }
    
    .toolbar-btn.btn-icon {
        width: 36px;
        padding: 0;
    }
    
    .toolbar-btn.btn-icon i {
        margin-right: 0;
    }
    
    .toolbar-divider {
        width: 1px;
        height: 24px;
        background-color: #e2e8f0;
        margin: 0 8px;
    }
</style>