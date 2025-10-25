@php
    View::share('pretitle', __('shop::admin.field_templates'));
@endphp

<div class="field-template-component-wrapper">
    <div class="card">
        @include('shop::admin.helper')

        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('shop::admin.search_templates') }}">
                    </div>
                </div>

                <!-- Template Sayısı -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center h-100">
                        <span class="text-muted">
                            <i class="fas fa-th-list me-2"></i>
                            <strong>{{ $templates->count() }}</strong> {{ __('shop::admin.templates') }}
                        </span>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div class="col-md-4 position-relative">
                    <div wire:loading
                        wire:target="render, search, toggleTemplateStatus, updateOrder"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 80px"><i class="fas fa-grip-vertical text-muted"></i></th>
                            <th>{{ __('shop::admin.template_name') }}</th>
                            <th>{{ __('shop::admin.description') }}</th>
                            <th class="text-center" style="width: 100px">{{ __('shop::admin.field_count') }}</th>
                            <th class="text-center" style="width: 100px">{{ __('shop::admin.status') }}</th>
                            <th class="text-end" style="width: 120px">{{ __('shop::admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-templates">
                        @forelse($templates as $template)
                            <tr data-id="{{ $template->template_id }}">
                                <td>
                                    <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <strong>{{ $template->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ \Illuminate\Support\Str::limit($template->description, 60) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-blue-lt">
                                        {{ count($template->fields) }} {{ __('shop::admin.fields') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <label class="form-check form-switch m-0">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               wire:click="toggleTemplateStatus({{ $template->template_id }})"
                                               {{ $template->is_active ? 'checked' : '' }}>
                                    </label>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.shop.field-templates.manage', $template->template_id) }}"
                                           class="btn btn-sm btn-icon btn-ghost-primary"
                                           title="{{ __('admin.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-icon btn-ghost-danger"
                                                wire:click="openDeleteModal({{ $template->template_id }}, '{{ addslashes($template->name) }}')"
                                                title="{{ __('admin.delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-database text-muted fs-1 mb-2"></i>
                                    <p>{{ __('shop::admin.no_templates_found') }}</p>
                                    <a href="{{ route('admin.shop.field-templates.manage') }}" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus me-2"></i>
                                        {{ __('shop::admin.create_first_template') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initSortable();
});

function initSortable() {
    const el = document.getElementById('sortable-templates');
    if (!el || el.children.length === 0) return;

    if (window.templateSortable) {
        window.templateSortable.destroy();
    }

    window.templateSortable = Sortable.create(el, {
        animation: 150,
        handle: '.fa-grip-vertical',
        ghostClass: 'bg-light',
        onEnd: function(evt) {
            const items = Array.from(el.children).map((row, index) => ({
                id: row.dataset.id,
                order: index
            }));

            @this.call('updateOrder', items);
        }
    });
}

// Refresh sortable after Livewire updates
document.addEventListener('livewire:load', function() {
    Livewire.on('refresh-sortable', () => {
        setTimeout(() => initSortable(), 100);
    });
});
</script>
@endpush
