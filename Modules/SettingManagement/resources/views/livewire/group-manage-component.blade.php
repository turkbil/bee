@include('settingmanagement::helper')
<form wire:submit="save">
    <div class="card">
        <div class="card-body">
            <div class="row">
                
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-folder me-2"></i>
                                {{ t('settingmanagement::general.group_info') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input type="text" wire:model="inputs.name"
                                            class="form-control @error('inputs.name') is-invalid @enderror"
                                            placeholder="{{ t('settingmanagement::general.group_name_placeholder') }}">
                                        <label>{{ t('settingmanagement::general.group_name') }} *</label>
                                        @error('inputs.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <select wire:model.live="inputs.parent_id" id="parent-group-select"
                                            class="form-select @error('inputs.parent_id') is-invalid @enderror"
                                            data-choices
                                            data-choices-search="{{ count($parentGroups) > 6 ? 'true' : 'false' }}"
                                            data-choices-placeholder="{{ t('settingmanagement::general.parent_group_placeholder') }}">
                                            <option value="">{{ t('settingmanagement::general.parent_group_placeholder') }}</option>
                                            @foreach($parentGroups as $group)
                                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                        <label>{{ t('settingmanagement::general.parent_group') }}</label>
                                        @error('inputs.parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                
                                <div id="prefix-field" class="col-md-12" style="display: {{ !empty($inputs['parent_id']) ? 'block' : 'none' }}">
                                    <div class="form-floating">
                                        <input type="text" wire:model="inputs.prefix"
                                            class="form-control @error('inputs.prefix') is-invalid @enderror"
                                            placeholder="{{ t('settingmanagement::general.prefix_placeholder') }}">
                                        <label>{{ t('settingmanagement::general.prefix') }}</label>
                                        @error('inputs.prefix')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text mt-2 ms-2"><i class="fas fa-info-circle me-1"></i>{{ t('settingmanagement::general.prefix_help') }}</div>
                                </div>

                                
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea wire:model="inputs.description"
                                            class="form-control @error('inputs.description') is-invalid @enderror" rows="3" data-bs-toggle="autosize"
                                            placeholder="{{ t('settingmanagement::general.description_placeholder') }}"></textarea>
                                        <label>{{ t('settingmanagement::general.description') }}</label>
                                        @error('inputs.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-settings me-2"></i>
                                {{ t('settingmanagement::general.settings') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            
                            <div class="form-group">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:model="inputs.is_active"
                                        value="1" {{ $inputs['is_active'] ? 'checked' : '' }} />
                                    <div class="state p-success p-on ms-2">
                                        <label>{{ t('settingmanagement::general.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ t('settingmanagement::general.inactive') }}</label>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="form-group mt-3">
                                <label class="form-label">{{ t('settingmanagement::general.icon') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="{{ $inputs['icon'] ?: 'fas fa-folder' }}"></i>
                                    </span>
                                    <input type="text" wire:model="inputs.icon"
                                        class="form-control @error('inputs.icon') is-invalid @enderror"
                                        placeholder="{{ t('settingmanagement::general.icon_placeholder') }}">
                                </div>
                                <div class="form-text mt-2 ms-2"><i class="fas fa-info-circle me-1"></i>{{ t('settingmanagement::general.icon_help') }}</div>
                                @error('inputs.icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card-footer d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-link text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i>{{ t('settingmanagement::general.cancel') }}
            </a>

            <div class="d-flex gap-2">
                <button type="button" class="btn" wire:click="save(false)" wire:loading.attr="disabled"
                    wire:target="save">
                    <span class="d-flex align-items-center">
                        <span class="ms-2" wire:loading.remove wire:target="save(false)">
                            <i class="fa-thin fa-plus me-2"></i> {{ t('settingmanagement::general.save_and_continue') }}
                        </span>
                        <span class="ms-2" wire:loading wire:target="save(false)">
                            <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ t('settingmanagement::general.save_and_continue') }}
                        </span>
                    </span>
                </button>
                <button type="button" class="btn btn-primary ms-4" wire:click="save(true)"
                    wire:loading.attr="disabled" wire:target="save">
                    <span class="d-flex align-items-center">
                        <span class="ms-2" wire:loading.remove wire:target="save(true)">
                            <i class="fa-thin fa-floppy-disk me-2"></i> {{ t('settingmanagement::general.save') }}
                        </span>
                        <span class="ms-2" wire:loading wire:target="save(true)">
                            <i class="fa-duotone fa-solid fa-spinner fa-spin me-2"></i> {{ t('settingmanagement::general.save') }}
                        </span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</form>

@push('styles')
<style>
    .form-label.required:after {
        content: " *";
        color: red;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function () {
        // Livewire'ın parent_id değiştiğinde prefix alanını göster/gizle
        Livewire.on('parentIdChanged', function(value) {
            const prefixField = document.getElementById('prefix-field');
            if (prefixField) {
                if (value) {
                    prefixField.style.display = 'block';
                } else {
                    prefixField.style.display = 'none';
                }
            }
        });
        
        // Sayfa yüklendiğinde manuel olarak kontrol et
        const parentSelect = document.getElementById('parent-group-select');
        const prefixField = document.getElementById('prefix-field');
        
        if (parentSelect && prefixField) {
            parentSelect.addEventListener('change', function() {
                if (this.value) {
                    prefixField.style.display = 'block';
                } else {
                    prefixField.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush