@include('menumanagement::admin.helper')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-list"></i>
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
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">{{ __('menumanagement::admin.loading') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Durum Filtresi -->
                    <div style="width: 120px">
                        <select wire:model.live="statusFilter" name="statusFilter" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="all">{{ __('menumanagement::admin.all_statuses') }}</option>
                            <option value="active">{{ __('admin.active') }}</option>
                            <option value="inactive">{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                    <!-- Konum Filtresi -->
                    <div style="width: 200px">
                        <select wire:model.live="locationFilter" name="locationFilter" class="form-control listing-filter-select" 
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
                        <th style="width: 50px">
                            <div class="d-flex align-items-center gap-2">
                                <button
                                    class="table-sort {{ $sortField === 'menu_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('menu_id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('name')">
                                {{ __('menumanagement::admin.menu_name') }}
                            </button>
                        </th>
                        <th style="width: 160px">
                            <button
                                class="table-sort {{ $sortField === 'location' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('location')">
                                {{ __('menumanagement::admin.menu_location') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('admin.status_tooltip') }}">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                {{ __('admin.status') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($menus as $menu)
                    <tr class="hover-trigger" wire:key="row-{{ $menu->menu_id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $menu->menu_id }}</span>
                            </div>
                        </td>
                        <td wire:key="title-{{ $menu->menu_id }}" class="position-relative">
                            <div class="d-flex align-items-center">
                                @if($menu->is_default)
                                    <i class="fas fa-home text-primary me-2" title="{{ __('menumanagement::admin.is_default') }}"></i>
                                @endif
                                @if($menu->menu_id == 1)
                                    <a href="{{ route('admin.menumanagement.index') }}" class="text-decoration-none text-reset">
                                        <span class="editable-title pr-4">{{ $menu->getTranslated('name', $currentSiteLocale) }}</span>
                                    </a>
                                @else
                                    <a href="{{ route('admin.menumanagement.menu.edit', $menu->menu_id) }}" class="text-decoration-none text-reset">
                                        <span class="editable-title pr-4">{{ $menu->getTranslated('name', $currentSiteLocale) }}</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a href="javascript:void(0);"
                                wire:click="$set('locationFilter', '{{ $menu->location }}')"
                                class="text-muted">
                                {{ __('menumanagement::admin.location_' . $menu->location) }}
                            </a>
                        </td>
                        <td wire:key="status-{{ $menu->menu_id }}" class="text-center align-middle">
                            <button wire:click="toggleActive({{ $menu->menu_id }})"
                                class="btn btn-icon btn-sm {{ $menu->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                {{ $menu->is_default && $menu->is_active ? 'disabled' : '' }}>
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $menu->menu_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $menu->menu_id }})">
                                    @if($menu->is_active)
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
                                        @if($menu->menu_id == 1)
                                            <a href="{{ route('admin.menumanagement.index') }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('menumanagement::admin.edit_menu_items') }}">
                                                <i class="fas fa-bars link-secondary fa-lg"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.menumanagement.menu.edit', $menu->menu_id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('menumanagement::admin.edit_menu_items') }}">
                                                <i class="fas fa-bars link-secondary fa-lg"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('admin.menumanagement.menu.manage', $menu->menu_id) }}"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('menumanagement::admin.edit_menu_settings') }}">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        @if(!$menu->is_default)
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" wire:click="duplicateMenu({{ $menu->menu_id }})" class="dropdown-item">
                                                    {{ __('menumanagement::admin.duplicate_menu') }}
                                                </a>
                                                <a href="javascript:void(0);" wire:click="deleteMenu({{ $menu->menu_id }})" 
                                                   onclick="return confirm('{{ __('menumanagement::admin.confirm_menu_delete') }}')" 
                                                   class="dropdown-item link-danger">
                                                    {{ __('menumanagement::admin.delete_menu') }}
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('menumanagement::admin.no_menus_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('menumanagement::admin.no_menus_found_subtitle') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $menus->links() }}
</div>