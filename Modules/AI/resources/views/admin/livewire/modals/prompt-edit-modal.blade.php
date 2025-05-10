<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="prompt-edit-modal" tabindex="-1" role="dialog" aria-modal="true"
        style="display: block; padding-right: 15px;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? 'Prompt Düzenle' : 'Yeni Prompt Ekle' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="prompt.name"
                            class="form-control @error('prompt.name') is-invalid @enderror" id="prompt_name"
                            placeholder="Prompt adı">
                        <label for="prompt_name">Prompt Adı</label>
                        @error('prompt.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prompt İçeriği</label>
                        <textarea wire:model="prompt.content"
                            class="form-control @error('prompt.content') is-invalid @enderror" id="prompt_content"
                            rows="8" placeholder="Sistem prompt içeriği"></textarea>
                        @error('prompt.content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input wire:model="prompt.is_default" class="form-check-input" type="checkbox"
                                    id="prompt_is_default">
                                <label class="form-check-label" for="prompt_is_default">Varsayılan prompt</label>
                            </div>
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-1"></i>
                                Varsayılan prompt, özel bir prompt seçilmediğinde kullanılır.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input wire:model="prompt.is_common" class="form-check-input" type="checkbox"
                                    id="prompt_is_common">
                                <label class="form-check-label" for="prompt_is_common">Ortak özellikler promptu</label>
                            </div>
                            <div class="form-text">
                                <i class="fa-thin fa-circle-info me-1"></i>
                                Ortak özellikler promptu, tüm konuşmalarda kullanılır.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" wire:click="closeModal">
                        İptal
                    </button>
                    <button type="button" class="btn btn-primary ms-auto" wire:click="save">
                        <i class="fas fa-save me-2"></i> {{ $isEditing ? 'Güncelle' : 'Kaydet' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>