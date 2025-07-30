@include('menumanagement::admin.helper')
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
                        placeholder="{{ __('menumanagement::admin.search_menus') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, deleteMenu, toggleActive, duplicateMenu, statusFilter, locationFilter"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Filtreler ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Durum Filtresi -->
                    <div style="width: 120px; min-width: 120px">
                        <select wire:model.live="statusFilter" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="all">{{ __('menumanagement::admin.all_statuses') }}</option>
                            <option value="active">{{ __('admin.active') }}</option>
                            <option value="inactive">{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                    <!-- Konum Filtresi -->
                    <div style="width: 140px; min-width: 140px">
                        <select wire:model.live="locationFilter" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="all">{{ __('menumanagement::admin.all_locations') }}</option>
                            <option value="header">{{ __('menumanagement::admin.location_header') }}</option>
                            <option value="footer">{{ __('menumanagement::admin.location_footer') }}</option>
                            <option value="sidebar">{{ __('menumanagement::admin.location_sidebar') }}</option>
                            <option value="mobile">{{ __('menumanagement::admin.location_mobile') }}</option>
                        </select>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="width: 80px; min-width: 80px">
                        <select wire:model.live="perPage" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="10"><nobr>10</nobr></option>
                            <option value="50"><nobr>50</nobr></option>
                            <option value="100"><nobr>100</nobr></option>
                            <option value="500"><nobr>500</nobr></option>
                            <option value="1000"><nobr>1000</nobr></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1">
                            <button wire:click="sortBy('menu_id')" class="btn btn-ghost-primary btn-sm">
                                ID 
                                @if($sortField === 'menu_id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('name')" class="btn btn-ghost-primary btn-sm">
                                {{ __('menumanagement::admin.menu_name') }}
                                @if($sortField === 'name')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('location')" class="btn btn-ghost-primary btn-sm">
                                {{ __('menumanagement::admin.menu_location') }}
                                @if($sortField === 'location')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </button>
                        </th>
                        <th class="text-center">{{ __('menumanagement::admin.menu_items') }}</th>
                        <th class="text-center">
                            <button wire:click="sortBy('is_active')" class="btn btn-ghost-primary btn-sm">
                                {{ __('admin.status') }}
                                @if($sortField === 'is_active')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </button>
                        </th>
                        <th class="text-center">
                            <button wire:click="sortBy('created_at')" class="btn btn-ghost-primary btn-sm">
                                {{ __('admin.created_at') }}
                                @if($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                @else
                                    <i class="fas fa-sort ms-1 text-muted"></i>
                                @endif
                            </button>
                        </th>
                        <th class="text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                        <tr wire:key="menu-{{ $menu->menu_id }}">
                            <td>
                                <span class="text-secondary">{{ $menu->menu_id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($menu->is_default)
                                        <i class="fas fa-star text-warning me-2" title="{{ __('menumanagement::admin.is_default') }}"></i>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $menu->getTranslated('name', $currentSiteLocale) }}</div>
                                        @if($menu->getTranslated('description', $currentSiteLocale))
                                            <div class="text-secondary small">{{ Str::limit($menu->getTranslated('description', $currentSiteLocale), 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-lt">
                                    {{ __('menumanagement::admin.location_' . $menu->location) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-lt">{{ $menu->items_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        wire:click="toggleActive({{ $menu->menu_id }})"
                                        {{ $menu->is_active ? 'checked' : '' }}
                                        {{ $menu->is_default && $menu->is_active ? 'disabled' : '' }}
                                        title="{{ $menu->is_default && $menu->is_active ? __('menumanagement::admin.default_menu_cannot_be_deactivated') : '' }}"
                                    >
                                </div>
                            </td>
                            <td class="text-center text-secondary">
                                {{ $menu->created_at->format('d.m.Y') }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <!-- Düzenle -->
                                    <a href="{{ route('admin.menumanagement.manage', $menu->menu_id) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="{{ __('admin.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Kopyala -->
                                    <button wire:click="duplicateMenu({{ $menu->menu_id }})" 
                                            class="btn btn-sm btn-outline-secondary"
                                            title="{{ __('menumanagement::admin.duplicate_menu') }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    
                                    <!-- Sil -->
                                    @if(!$menu->is_default)
                                        <button wire:click="deleteMenu({{ $menu->menu_id }})" 
                                                class="btn btn-sm btn-outline-danger"
                                                title="{{ __('admin.delete') }}"
                                                onclick="return confirm('{{ __('menumanagement::admin.confirm_menu_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="fas fa-bars"></i>
                                    </div>
                                    <p class="empty-title">{{ __('menumanagement::admin.no_menus_found') }}</p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.menumanagement.manage') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            {{ __('menumanagement::admin.create_menu') }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($menus->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-secondary small">
                    {{ __('admin.showing') }} {{ $menus->firstItem() }} - {{ $menus->lastItem() }} 
                    {{ __('admin.of') }} {{ $menus->total() }} {{ __('admin.results') }}
                </div>
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>