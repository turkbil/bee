@if($bulkActionsEnabled)
<div class="position-fixed bottom-0 start-50 translate-middle-x mb-4" style="z-index: 1000;">
    <div class="card shadow-lg border-0 rounded-lg " style="backdrop-filter: blur(12px); background: var(--tblr-bg-surface);"><span class="badge bg-red badge-notification badge-blink"></span>
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                <span class="text-muted small">{{ count($selectedItems) }} {{ __('page::admin.items_selected') }}</span>
                <button type="button" class="btn btn-sm btn-outline-success px-3 py-1 hover-btn" wire:click="bulkToggleActive(true)">
                    <i class="fas fa-check me-2"></i>
                    <span>{{ __('page::admin.activate') }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning px-3 py-1 hover-btn" wire:click="bulkToggleActive(false)">
                    <i class="fas fa-times me-2"></i>
                    <span>{{ __('page::admin.deactivate') }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 hover-btn" wire:click="confirmBulkDelete">
                    <i class="fas fa-trash me-2"></i>
                    <span>{{ __('admin.delete') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif