<div class="modal fade" id="modal-tenant-manage" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $tenantId ? 'Tenant Güncelleme' : 'Yeni Tenant Ekleme' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit.prevent="saveTenant">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tenant Adı</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yetkili Adı Soyadı</label>
                        <input type="text" class="form-control" wire:model="fullname">
                        @error('fullname') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Adresi</label>
                        <input type="email" class="form-control" wire:model="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon Numarası</label>
                        <input type="text" class="form-control" wire:model="phone">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <div class="pretty p-icon p-toggle p-plain">
                            <input type="checkbox" id="is_active" name="is_active" wire:model="is_active"
                                value="1" />
                            <div class="state p-on">
                                <i class="icon fa-regular fa-square-check"></i>
                                <label>Aktif / Online</label>
                            </div>
                            <div class="state p-off">
                                <i class="icon fa-regular fa-square"></i>
                                <label>Aktif Değil / Offline</label>
                            </div>
                        </div>
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