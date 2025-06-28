<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="bulk-delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 9v2m0 4v.01"/>
                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                    </svg>
                    <h3>Toplu Silme Onayı</h3>
                    <div class="text-muted">{{ count($selectedItems) }} adet kayıt silinecek. Bu işlem geri alınamaz!</div>
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
                                <button class="btn btn-danger w-100" wire:click="bulkDelete" wire:loading.attr="disabled">
                                    <span wire:loading.remove>Sil</span>
                                    <span wire:loading>Siliniyor...</span>
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

    <script>
        document.addEventListener('livewire:initialized', () => {
            $(document).on('keydown', function(e) {
                if (e.keyCode === 32 && $('#bulk-delete-modal').is(':visible')) {
                    e.preventDefault();
                    $('#bulk-delete-modal .btn-danger').click();
                }
            });
        });
    </script>
</div>