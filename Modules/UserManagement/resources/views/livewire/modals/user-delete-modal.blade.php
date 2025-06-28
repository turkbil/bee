<div>
    <!-- Delete Modal -->
    <div class="modal fade" id="userDeleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('usermanagement::admin.delete_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('usermanagement::admin.confirm_delete_user') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('usermanagement::admin.cancel') }}</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser">{{ __('usermanagement::admin.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>