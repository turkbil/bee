<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="delete-tenant-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="closeModal"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h3>Tenant'ı Silmek İstediğinize Emin Misiniz?</h3>
                    <div class="text-muted">"{{ $tenantTitle }}" tenant'ını silmek üzeresiniz. Bu işlem geri alınamaz ve bu tenant'a ait tüm veriler silinecektir.</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button class="btn w-100" wire:click="closeModal">
                                    İptal
                                </button>
                            </div>
                            <div class="col">
                                <button class="btn btn-danger w-100" wire:click="delete" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="delete">
                                        <i class="fas fa-trash me-2"></i>Sil
                                    </span>
                                    <span wire:loading wire:target="delete">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Siliniyor...
                                    </span>
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