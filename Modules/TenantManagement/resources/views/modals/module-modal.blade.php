<div class="modal fade" id="modal-module-management" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modül Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <livewire:tenant-module-component :tenantId="$tenantId ?? null" />
            </div>
        </div>
    </div>
</div>