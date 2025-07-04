{{-- Bulk Actions Panel --}}
@if(count($selectedItems) > 0)
<div class="alert alert-primary d-flex align-items-center justify-content-between" role="alert">
    <div>
        <strong>{{ count($selectedItems) }}</strong> {{ __('ai::admin.selected_items') }}
    </div>
    <div class="btn-group">
        <button type="button" wire:click="bulkToggleActive(true)" class="btn btn-sm btn-success">
            {{ __('ai::admin.bulk_activate') }}
        </button>
        <button type="button" wire:click="bulkToggleActive(false)" class="btn btn-sm btn-warning">
            {{ __('ai::admin.bulk_deactivate') }}
        </button>
        <button type="button" wire:click="$dispatch('showBulkDeleteModal', {module: '{{ $moduleType }}', count: {{ count($selectedItems) }}})" class="btn btn-sm btn-danger">
            {{ __('ai::admin.bulk_delete') }}
        </button>
    </div>
</div>
@endif