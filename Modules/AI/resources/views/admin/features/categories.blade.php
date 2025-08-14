@extends('admin.layout')
@section('title', __('ai::admin.ai_feature_categories'))

@include('ai::helper')

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search" class="form-control" 
                           placeholder="{{ __('ai::admin.search_categories') }}" 
                           autocomplete="off">
                </div>
            </div>
            
            <!-- Sağ üst butonlar -->
            <div class="col-auto">
                <div class="btn-list">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="show-inactive" role="switch">
                        <label class="form-check-label" for="show-inactive">
                            {{ __('ai::admin.show_inactive') }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Açıklama -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('ai::admin.drag_to_reorder') }}
                </div>
            </div>
        </div>

        <!-- Kategoriler Tablosu -->
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead>
                    <tr>
                        <th>{{ __('ai::admin.order') }}</th>
                        <th>{{ __('ai::admin.icon') }}</th>
                        <th>{{ __('ai::admin.title') }}</th>
                        <th>{{ __('ai::admin.description') }}</th>
                        <th>{{ __('ai::admin.status') }}</th>
                        <th>{{ __('ai::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="sortable-list">
                    @foreach($categories as $category)
                    <tr data-id="{{ $category->ai_feature_category_id }}" class="sortable-item">
                        <td>
                            <div class="drag-handle" style="cursor: grab;">
                                <i class="fas fa-grip-vertical text-muted"></i>
                                <span class="ms-2">{{ $category->order }}</span>
                            </div>
                        </td>
                        <td>
                            <i class="{{ $category->icon }} fa-lg" style="color: {{ $category->is_active ? '#206bc4' : '#6c757d' }};"></i>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $category->title }}</div>
                            <div class="text-muted small">{{ $category->slug }}</div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $category->description }}">
                                {{ $category->description }}
                            </div>
                        </td>
                        <td>
                            <label class="form-check form-switch form-check-single">
                                <input class="form-check-input status-toggle" type="checkbox" 
                                       data-id="{{ $category->ai_feature_category_id }}"
                                       {{ $category->is_active ? 'checked' : '' }}>
                            </label>
                        </td>
                        <td>
                            <div class="btn-list flex-nowrap">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="editCategory({{ $category->ai_feature_category_id }})"
                                        title="{{ __('admin.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable initialization
    const sortableList = document.getElementById('sortable-list');
    if (sortableList) {
        const sortable = new Sortable(sortableList, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
                // Get all rows and their new order
                const rows = Array.from(sortableList.children);
                const updates = rows.map((row, index) => ({
                    value: row.dataset.id,
                    order: index + 1
                }));

                // Send update to server
                fetch('/admin/ai/features/categories/update-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        updates: updates
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update order numbers in the table
                        rows.forEach((row, index) => {
                            const orderCell = row.querySelector('.drag-handle span');
                            if (orderCell) {
                                orderCell.textContent = index + 1;
                            }
                        });
                        
                        // Show success toast
                        if (window.toast) {
                            window.toast('{{ __("admin.success") }}', data.message, 'success');
                        }
                    } else {
                        console.error('Update failed:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating order:', error);
                });
            }
        });
    }

    // Search functionality
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = sortableList.querySelectorAll('tr');
            
            rows.forEach(row => {
                const title = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
                const description = row.querySelector('.text-truncate')?.textContent.toLowerCase() || '';
                const slug = row.querySelector('.text-muted.small')?.textContent.toLowerCase() || '';
                
                if (title.includes(searchTerm) || description.includes(searchTerm) || slug.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Disable sorting when searching
            if (searchTerm) {
                sortable.option('disabled', true);
                sortableList.removeAttribute('id');
            } else {
                sortable.option('disabled', false);
                sortableList.setAttribute('id', 'sortable-list');
            }
        });
    }

    // Status toggle functionality
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const categoryId = this.dataset.id;
            const isActive = this.checked;
            
            fetch(`/admin/ai/features/categories/${categoryId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    is_active: isActive
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update icon color
                    const row = this.closest('tr');
                    const icon = row.querySelector('td:nth-child(2) i');
                    if (icon) {
                        icon.style.color = isActive ? '#206bc4' : '#6c757d';
                    }
                    
                    if (window.toast) {
                        window.toast('{{ __("admin.success") }}', data.message, 'success');
                    }
                } else {
                    // Revert toggle state
                    this.checked = !isActive;
                    console.error('Status toggle failed:', data.message);
                }
            })
            .catch(error => {
                // Revert toggle state
                this.checked = !isActive;
                console.error('Error toggling status:', error);
            });
        });
    });

    // Show/hide inactive categories
    const showInactiveToggle = document.getElementById('show-inactive');
    if (showInactiveToggle) {
        showInactiveToggle.addEventListener('change', function() {
            const showInactive = this.checked;
            const rows = sortableList.querySelectorAll('tr');
            
            rows.forEach(row => {
                const statusToggle = row.querySelector('.status-toggle');
                const isActive = statusToggle ? statusToggle.checked : true;
                
                if (!showInactive && !isActive) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
        });
    }
});

function editCategory(categoryId) {
    // Placeholder for edit functionality
    alert('Edit category ' + categoryId + ' - This feature will be implemented');
}
</script>
@endsection