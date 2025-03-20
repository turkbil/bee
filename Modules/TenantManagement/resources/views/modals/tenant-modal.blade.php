<div class="modal fade" id="modal-tenant-manage" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $tenantId ? 'Tenant Güncelleme' : 'Yeni Tenant Ekleme' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="saveTenant">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Tenant Adı</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yetkili Adı Soyadı</label>
                        <input type="text" class="form-control @error('fullname') is-invalid @enderror" wire:model="fullname">
                        @error('fullname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Adresi</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon Numarası</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Durum</label>
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="is_active">
                            <span class="form-check-label">{{ $is_active ? 'Aktif / Online' : 'Pasif / Offline' }}</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">İptal</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                                    <span wire:loading wire:target="saveTenant" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Kaydet
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>