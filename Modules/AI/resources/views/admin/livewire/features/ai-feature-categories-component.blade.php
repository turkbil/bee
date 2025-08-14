@include('ai::helper')
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
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="{{ __('ai::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, sortBy, toggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">{{ __('ai::admin.loading') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    @if(!$search)
                        <small class="text-muted">{{ __('ai::admin.drag_to_reorder') }}</small>
                    @endif
                </div>
            </div>
        </div>
        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable" @if(!$search) id="sortable-list" @endif>
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <button
                                class="table-sort {{ $sortField === 'ai_feature_category_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('ai_feature_category_id')">
                                ID
                            </button>
                        </th>
                        <th style="width: 50px">
                            <button
                                class="table-sort {{ $sortField === 'order' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('order')">
                                {{ __('ai::admin.order') }}
                            </button>
                        </th>
                        <th style="width: 60px" class="text-center">{{ __('ai::admin.icon') }}</th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('title')">
                                {{ __('ai::admin.title') }}
                            </button>
                        </th>
                        <th>{{ __('ai::admin.description') }}</th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.status_tooltip') }}">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                {{ __('ai::admin.status') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">{{ __('ai::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($categories as $category)
                    <tr class="hover-trigger {{ !$search ? 'sortable-row' : '' }}" 
                        wire:key="row-{{ $category->ai_feature_category_id }}"
                        @if(!$search) data-id="{{ $category->ai_feature_category_id }}" @endif>
                        <td class="sort-id small">
                            {{ $category->ai_feature_category_id }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-outline text-blue">{{ $category->order }}</span>
                        </td>
                        <td class="text-center">
                            @if($category->icon)
                                <i class="{{ $category->icon }}"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold">{{ $category->title }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">{{ Str::limit($category->description, 100) }}</span>
                        </td>
                        <td wire:key="status-{{ $category->ai_feature_category_id }}" class="text-center align-middle">
                            <button wire:click="toggleActive({{ $category->ai_feature_category_id }})"
                                class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $category->ai_feature_category_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $category->ai_feature_category_id }})">
                                    @if($category->is_active)
                                    <i class="fas fa-check"></i>
                                    @else
                                    <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="#"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.edit') }}">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.features') }}">
                                            <i class="fa-solid fa-list link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" class="dropdown-item link-danger">
                                                    {{ __('ai::admin.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('ai::admin.no_categories_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('ai::admin.no_categories_found_subtitle') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script
    src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}?v={{ filemtime(public_path('admin-assets/libs/sortable/sortable.min.js')) }}">
</script>
<script
    src="{{ asset('admin-assets/libs/sortable/sortable-settings.js') }}?v={{ filemtime(public_path('admin-assets/libs/sortable/sortable-settings.js')) }}">
</script>
@endpush