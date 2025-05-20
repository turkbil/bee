<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="confirm-action-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <h3>{{ $title }}</h3>
                    <div class="text-muted">{{ $message }}</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button class="btn w-100" wire:click="$set('showModal', false)">
                                    İptal
                                </button>
                            </div>
                            <div class="col">
                                <button class="btn btn-danger w-100" wire:click="confirm" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Onayla</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>İşleniyor</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>