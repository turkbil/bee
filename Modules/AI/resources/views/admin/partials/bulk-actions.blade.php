{{-- Bulk Actions Panel --}}
@if(count($selectedItems) > 0)
<div class="alert alert-primary d-flex align-items-center justify-content-between" role="alert">
    <div>
        <strong>{{ count($selectedItems) }}</strong> öğe seçildi
    </div>
    <div class="btn-group">
        <button type="button" wire:click="bulkToggleActive(true)" class="btn btn-sm btn-success">
            Seçilenleri Aktif Yap
        </button>
        <button type="button" wire:click="bulkToggleActive(false)" class="btn btn-sm btn-warning">
            Seçilenleri Pasif Yap
        </button>
        <button type="button" wire:click="$dispatch('showBulkDeleteModal', {module: '{{ $moduleType }}', count: {{ count($selectedItems) }}})" class="btn btn-sm btn-danger">
            Seçilenleri Sil
        </button>
    </div>
</div>
@endif