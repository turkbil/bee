<div>
    @if($showModal)
    <div class="modal modal-blur fade show" id="prompt-edit-modal" tabindex="-1" role="dialog" aria-modal="true"
        style="display: block; padding-right: 15px;">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? __('ai::admin.edit') . ' ' . __('ai::admin.prompt') : __('ai::admin.new_prompt') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" wire:model="prompt.name"
                            class="form-control @error('prompt.name') is-invalid @enderror" id="prompt_name"
                            placeholder="{{ __('ai::admin.prompt_name') }}">
                        <label for="prompt_name">{{ __('ai::admin.prompt_name') }}</label>
                        @error('prompt.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('ai::admin.prompt_content') }}</label>
                        <textarea wire:model="prompt.content"
                            class="form-control @error('prompt.content') is-invalid @enderror" id="prompt_content"
                            rows="8" placeholder="{{ __('ai::admin.system_prompt_content') }}"></textarea>
                        @error('prompt.content')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input wire:model="prompt.is_default" class="form-check-input" type="checkbox"
                                    id="prompt_is_default">
                                <label class="form-check-label" for="prompt_is_default">{{ __('ai::admin.default_prompt') }}</label>
                            </div>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.default_prompt_info') }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input wire:model="prompt.is_common" class="form-check-input" type="checkbox"
                                    id="prompt_is_common">
                                <label class="form-check-label" for="prompt_is_common">{{ __('ai::admin.common_features_prompt') }}</label>
                            </div>
                            <div class="form-text mt-2 ms-2">
                                <i class="fas fa-info-circle me-1"></i>{{ __('ai::admin.common_prompt_info') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" wire:click="closeModal">
                        {{ __('ai::admin.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary ms-auto" wire:click="save">
                        <i class="fas fa-save me-2"></i> {{ $isEditing ? __('ai::admin.update') : __('ai::admin.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>