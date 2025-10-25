@php
    View::share('pretitle', __('shop::admin.field_templates'));
    View::share('title', $templateId ? __('shop::admin.edit_template') : __('shop::admin.new_template'));
@endphp

<div class="field-template-manage-wrapper">
    @include('shop::admin.helper')

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save(false)">
                <!-- Template Bilgileri -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label required">{{ __('shop::admin.template_name') }}</label>
                        <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="{{ __('shop::admin.template_name_placeholder') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('shop::admin.status') }}</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" wire:model="is_active" class="form-check-input" id="is_active">
                            <label class="form-check-label" for="is_active">
                                {{ __('shop::admin.active') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">{{ __('shop::admin.description') }}</label>
                        <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3"
                            placeholder="{{ __('shop::admin.template_description_placeholder') }}"></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Alanlar Bölümü -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="card-title">{{ __('shop::admin.template_fields') }}</h3>
                        <button type="button" wire:click="addField" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('shop::admin.add_field') }}
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>{{ __('shop::admin.field_name') }}</th>
                                    <th style="width: 200px">{{ __('shop::admin.field_type') }}</th>
                                    <th style="width: 80px">{{ __('shop::admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fields as $index => $field)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <input type="text"
                                                wire:model="fields.{{ $index }}.name"
                                                class="form-control @error("fields.{$index}.name") is-invalid @enderror"
                                                placeholder="{{ __('shop::admin.field_name_placeholder') }}">
                                            @error("fields.{$index}.name")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <select wire:model="fields.{{ $index }}.type"
                                                class="form-select @error("fields.{$index}.type") is-invalid @enderror">
                                                <option value="input">{{ __('shop::admin.field_type_input') }}</option>
                                                <option value="textarea">{{ __('shop::admin.field_type_textarea') }}</option>
                                                <option value="checkbox">{{ __('shop::admin.field_type_checkbox') }}</option>
                                            </select>
                                            @error("fields.{$index}.type")
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <button type="button"
                                                wire:click="removeField({{ $index }})"
                                                class="btn btn-sm btn-icon btn-ghost-danger"
                                                title="{{ __('admin.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('fields')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Butonları -->
                <div class="d-flex justify-content-between">
                    <div>
                        @if($templateId)
                            <button type="button"
                                wire:click="deleteTemplate"
                                wire:confirm="{{ __('shop::admin.confirm_delete_template') }}"
                                class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>
                                {{ __('admin.delete') }}
                            </button>
                        @endif
                    </div>

                    <div class="btn-group">
                        <a href="{{ route('admin.shop.field-templates.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ __('admin.back') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ __('admin.save') }}
                        </button>
                        <button type="button"
                            wire:click="save(true)"
                            class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            {{ __('admin.save_and_exit') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
