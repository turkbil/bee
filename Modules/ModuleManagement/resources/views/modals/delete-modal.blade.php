<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="delete-modal" tabindex="-1" role="dialog" aria-modal="true" style="display: block;">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <h3>{{ __('modulemanagement::admin.confirm_delete') }}</h3>
                    <div class="text-muted">"{{ $title }}" {{ __('modulemanagement::admin.about_to_delete') }}</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button class="btn w-100" wire:click="$set('showModal', false)">
                                    {{ __('modulemanagement::admin.cancel') }}
                                </button>
                            </div>
                            <div class="col">
                                <button class="btn btn-danger w-100" wire:click="delete" wire:loading.attr="disabled">
                                    {{ __('modulemanagement::admin.delete') }}
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
                if (e.keyCode === 32 && $('#delete-modal').is(':visible')) {
                    e.preventDefault();
                    $('#delete-modal .btn-danger').click();
                }
            });
        });
    </script>
</div>