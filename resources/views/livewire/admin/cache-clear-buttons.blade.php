@if($mobile)
<!-- Mobile Cache Clear Button -->
<a href="#" wire:click="clearCache"
   class="d-flex flex-column align-items-center justify-content-center text-center p-2 border rounded mobile-quick-action">
    <span class="cache-icon-container">
        <!-- Loading state -->
        <div wire:loading wire:target="clearCache">
            <i class="fa-solid fa-spinner fa-spin mb-1 text-info" style="font-size: 18px;"></i>
        </div>
        <!-- Normal state -->
        <div wire:loading.remove wire:target="clearCache">
            @if($type === 'all')
                <i class="fa-solid fa-trash-can mb-1 text-danger" style="font-size: 18px;"></i>
            @else
                <i class="fa-solid fa-broom mb-1 text-primary" style="font-size: 18px;"></i>
            @endif
        </div>
    </span>
    <small class="fw-bold">
        @if($type === 'all')
            Sistem Cache
        @else
            Cache Temizle
        @endif
    </small>
</a>
@else
<!-- Desktop Cache Clear Button -->
<a href="#" wire:click="clearCache"
   class="d-flex flex-column align-items-center justify-content-center text-center py-3 px-2 quick-action-item">
    <span class="cache-icon-container d-flex justify-content-center">
        <!-- Loading state -->
        <div wire:loading wire:target="clearCache">
            <i class="fa-solid fa-spinner fa-spin mb-2" style="font-size: 28px;"></i>
        </div>
        <!-- Normal state -->
        <div wire:loading.remove wire:target="clearCache">
            @if($type === 'all')
                <i class="fa-solid fa-trash-can mb-2" style="font-size: 28px;"></i>
            @else
                <i class="fa-solid fa-broom mb-2" style="font-size: 28px;"></i>
            @endif
        </div>
    </span>
    <span class="nav-link-title">
        @if($type === 'all')
            Sistem Cache
        @else
            Cache Temizle
        @endif
    </span>
</a>
@endif