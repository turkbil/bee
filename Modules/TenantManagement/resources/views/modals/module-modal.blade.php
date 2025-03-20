<div class="modal fade" id="modal-module-management" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modül Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($tenantId)
                    <livewire:tenantmanagement::tenant-module-component :tenant-id="$tenantId" :wire:key="'module-tenant-'.$tenantId" />
                @else
                    <div class="alert alert-warning">
                        Lütfen önce bir tenant seçin.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>