@if($showDeleteModal)
<div class="modal modal-blur fade show" id="delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <h3>Silmek istediğinize emin misiniz?</h3>
                <div class="text-muted">"{{ $deleteTitle }}" kaydını silmek üzeresiniz.</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button class="btn w-100" wire:click="$set('showDeleteModal', false)">
                                İptal
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn btn-danger w-100" wire:click="delete" wire:loading.attr="disabled">
                                <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Sil
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
// Silme modali sonrası sayfa yenileme önlemi
document.addEventListener('livewire:initialized', () => {
    @this.on('itemDeleted', () => {
        setTimeout(() => {
            const modalBackdrop = document.querySelector('.modal-backdrop');
            if (modalBackdrop) {
                modalBackdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 300);
    });
});
</script>