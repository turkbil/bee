@extends('admin.layouts.master')

@section('title', __('shop::admin.field_templates'))

@section('content')
    {{-- Helper --}}
    @include('admin.partials.helper', [
        'title' => __('shop::admin.field_templates'),
        'description' => __('shop::admin.field_templates_description'),
        'icon' => 'ti ti-template',
    ])

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('shop::admin.field_templates') }}
                    </h2>
                    <div class="text-muted mt-1">
                        {{ __('shop::admin.field_templates_description') }}
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.shop.field-templates.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i>
                        {{ __('shop::admin.new_field_template') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 80px;">{{ __('shop::admin.sort_order') }}</th>
                                <th>{{ __('shop::admin.template_name') }}</th>
                                <th>{{ __('shop::admin.description') }}</th>
                                <th class="text-center" style="width: 100px;">{{ __('shop::admin.field_count') }}</th>
                                <th class="text-center" style="width: 100px;">{{ __('shop::admin.status') }}</th>
                                <th class="text-end" style="width: 120px;">{{ __('shop::admin.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="template-list">
                            @forelse($templates as $template)
                                <tr data-id="{{ $template->template_id }}">
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-ghost-secondary move-up"
                                                    @if($loop->first) disabled @endif>
                                                <i class="ti ti-arrow-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-ghost-secondary move-down"
                                                    @if($loop->last) disabled @endif>
                                                <i class="ti ti-arrow-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $template->name }}</strong>
                                    </td>
                                    <td>
                                        <div class="text-muted">
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
                                                   class="form-check-input toggle-active"
                                                   data-id="{{ $template->template_id }}"
                                                   {{ $template->is_active ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.shop.field-templates.edit', $template->template_id) }}"
                                               class="btn btn-sm btn-icon btn-ghost-primary"
                                               title="{{ __('shop::admin.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-icon btn-ghost-danger delete-template"
                                                    data-id="{{ $template->template_id }}"
                                                    title="{{ __('shop::admin.delete') }}">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="ti ti-database-off fs-1 mb-2"></i>
                                        <p>{{ __('shop::admin.no_templates_found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Active Status
    document.querySelectorAll('.toggle-active').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const id = this.dataset.id;
            const isChecked = this.checked;

            fetch(`{{ url('admin/shop/field-templates') }}/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !isChecked; // Revert on error
            });
        });
    });

    // Move Up/Down
    document.querySelectorAll('.move-up, .move-down').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const direction = this.classList.contains('move-up') ? 'up' : 'down';
            const targetRow = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;

            if (targetRow && targetRow.tagName === 'TR') {
                if (direction === 'up') {
                    row.parentNode.insertBefore(row, targetRow);
                } else {
                    row.parentNode.insertBefore(targetRow, row);
                }
                updateSortOrder();
                updateButtonStates();
            }
        });
    });

    function updateSortOrder() {
        const rows = document.querySelectorAll('#template-list tr[data-id]');
        const order = Array.from(rows).map((row, index) => ({
            id: row.dataset.id,
            order: index
        }));

        fetch('{{ route('admin.shop.field-templates.update-order') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Order updated successfully');
            }
        });
    }

    function updateButtonStates() {
        const rows = document.querySelectorAll('#template-list tr[data-id]');
        rows.forEach((row, index) => {
            const upBtn = row.querySelector('.move-up');
            const downBtn = row.querySelector('.move-down');

            if (upBtn) upBtn.disabled = index === 0;
            if (downBtn) downBtn.disabled = index === rows.length - 1;
        });
    }

    // Delete Template
    document.querySelectorAll('.delete-template').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;

            if (!confirm('{{ __('shop::admin.confirm_delete_template') }}')) {
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/shop/field-templates') }}/${id}`;

            const csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = '{{ csrf_token() }}';

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';

            form.appendChild(csrfField);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        });
    });

    // Toast function
    function showToast(message, type = 'info') {
        // Simple alert fallback - you can replace with Tabler toast
        const alertClass = type === 'success' ? 'alert-success' : 'alert-info';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', alertHtml);

        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    }
});
</script>
@endpush
