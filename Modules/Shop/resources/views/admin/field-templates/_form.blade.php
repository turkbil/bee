<div class="card" x-data="fieldTemplateBuilder(@json($template->fields ?? []))">
    <div class="card-header">
        <h3 class="card-title">
            {{ isset($template) ? __('shop::admin.edit_field_template') : __('shop::admin.new_field_template') }}
        </h3>
    </div>

    <form method="POST"
          action="{{ isset($template) ? route('admin.shop.field-templates.update', $template->template_id) : route('admin.shop.field-templates.store') }}"
          x-on:submit="updateFieldOrders">
        @csrf
        @if(isset($template))
            @method('PUT')
        @endif

        <div class="card-body">
            {{-- Template Name --}}
            <div class="mb-3">
                <label class="form-label required">{{ __('shop::admin.template_name') }}</label>
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $template->name ?? '') }}"
                       placeholder="{{ __('shop::admin.template_name_placeholder') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-hint">
                    {{ __('shop::admin.template_name_hint') }}
                </small>
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="form-label">{{ __('shop::admin.description') }}</label>
                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="2"
                          placeholder="{{ __('shop::admin.template_description_placeholder') }}">{{ old('description', $template->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror>
            </div>

            <hr>

            {{-- Dynamic Fields Builder --}}
            <div class="mb-3">
                <label class="form-label required">
                    {{ __('shop::admin.template_fields') }}
                </label>

                {{-- Field List --}}
                <div class="field-list mb-3">
                    <template x-for="(field, index) in fields" :key="'field-' + index">
                        <div class="card mb-2">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    {{-- Order Buttons --}}
                                    <div class="col-auto">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <button type="button"
                                                    @click="moveField(index, 'up')"
                                                    :disabled="index === 0"
                                                    class="btn btn-ghost-secondary">
                                                <i class="ti ti-arrow-up"></i>
                                            </button>
                                            <button type="button"
                                                    @click="moveField(index, 'down')"
                                                    :disabled="index === fields.length - 1"
                                                    class="btn btn-ghost-secondary">
                                                <i class="ti ti-arrow-down"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Field Name --}}
                                    <div class="col-md-5">
                                        <label class="form-label small mb-1">
                                            {{ __('shop::admin.field_name') }}
                                        </label>
                                        <input type="text"
                                               x-model="fields[index].name"
                                               :name="'fields[' + index + '][name]'"
                                               class="form-control form-control-sm"
                                               placeholder="{{ __('shop::admin.field_name_placeholder') }}"
                                               required>
                                    </div>

                                    {{-- Field Type --}}
                                    <div class="col-md-4">
                                        <label class="form-label small mb-1">
                                            {{ __('shop::admin.field_type') }}
                                        </label>
                                        <select x-model="fields[index].type"
                                                :name="'fields[' + index + '][type]'"
                                                class="form-select form-select-sm"
                                                required>
                                            <option value="input">{{ __('shop::admin.field_type_input') }}</option>
                                            <option value="textarea">{{ __('shop::admin.field_type_textarea') }}</option>
                                            <option value="checkbox">{{ __('shop::admin.field_type_checkbox') }}</option>
                                        </select>
                                    </div>

                                    {{-- Hidden Order Input --}}
                                    <input type="hidden"
                                           :name="'fields[' + index + '][order]'"
                                           :value="index">

                                    {{-- Delete Button --}}
                                    <div class="col-auto">
                                        <button type="button"
                                                @click="removeField(index)"
                                                class="btn btn-sm btn-icon btn-danger mt-4"
                                                :disabled="fields.length === 1">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="fields.length === 0" class="text-center text-muted py-5 border rounded">
                        <i class="ti ti-database-off fs-1 mb-2"></i>
                        <p>{{ __('shop::admin.no_fields_added') }}</p>
                    </div>
                </div>

                {{-- Add Field Button --}}
                <button type="button"
                        @click="addField()"
                        class="btn btn-outline-primary w-100">
                    <i class="ti ti-plus"></i>
                    {{ __('shop::admin.add_new_field') }}
                </button>

                <small class="form-hint mt-2 d-block">
                    {{ __('shop::admin.field_builder_hint') }}
                </small>
            </div>

            <hr>

            {{-- Active Status --}}
            <div class="mb-3">
                <label class="form-check form-switch">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           class="form-check-input"
                           {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">{{ __('shop::admin.active') }}</span>
                </label>
                <small class="form-hint">
                    {{ __('shop::admin.template_active_hint') }}
                </small>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="{{ route('admin.shop.field-templates.index') }}" class="btn btn-link">
                {{ __('shop::admin.cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i>
                {{ isset($template) ? __('shop::admin.update') : __('shop::admin.save') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function fieldTemplateBuilder(initialFields = []) {
    return {
        fields: initialFields.length > 0 ? initialFields : [
            { name: '', type: 'input', order: 0 }
        ],

        init() {
            // Ensure all fields have order property
            this.fields.forEach((field, index) => {
                if (!field.hasOwnProperty('order')) {
                    field.order = index;
                }
            });
        },

        addField() {
            this.fields.push({
                name: '',
                type: 'input',
                order: this.fields.length
            });
        },

        removeField(index) {
            if (this.fields.length <= 1) {
                alert('{{ __('shop::admin.cannot_remove_last_field') }}');
                return;
            }

            if (confirm('{{ __('shop::admin.confirm_remove_field') }}')) {
                this.fields.splice(index, 1);
                this.updateOrders();
            }
        },

        moveField(index, direction) {
            const newIndex = direction === 'up' ? index - 1 : index + 1;

            if (newIndex >= 0 && newIndex < this.fields.length) {
                // Swap elements
                [this.fields[index], this.fields[newIndex]] = [this.fields[newIndex], this.fields[index]];
                this.updateOrders();
            }
        },

        updateOrders() {
            this.fields.forEach((field, index) => {
                field.order = index;
            });
        },

        updateFieldOrders(event) {
            // Update orders before form submission
            this.updateOrders();
        }
    }
}
</script>
@endpush
