<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="modal-prompt-edit" tabindex="-1" role="dialog" aria-modal="true"
        style="display: block; padding-right: 15px;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $promptId ? 'Prompt Düzenle' : 'Yeni Prompt Ekle' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Prompt Adı</label>
                        <input type="text" class="form-control @error('prompt.name') is-invalid @enderror"
                            wire:model="prompt.name" placeholder="Prompt adını girin">
                        @error('prompt.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prompt İçeriği</label>
                        <textarea class="form-control @error('prompt.content') is-invalid @enderror"
                            wire:model="prompt.content" rows="6" placeholder="Prompt içeriğini girin"></textarea>
                        @error('prompt.content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="prompt.is_default">
                            <span class="form-check-label">Varsayılan Prompt</span>
                        </label>
                        <div class="text-muted small">
                            Bu seçenek işaretlenirse, diğer varsayılan promptlar devre dışı bırakılır.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" wire:click="closeModal">İptal</button>
                    <button type="button" class="btn btn-primary" wire:click="save">
                        {{ $promptId ? 'Güncelle' : 'Ekle' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>