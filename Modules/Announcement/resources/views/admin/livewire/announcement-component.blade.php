@include('announcement::admin.helper')
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
                        placeholder="{{ __('announcement::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('announcement::admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
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
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                <button
                                    class="table-sort {{ $sortField === 'announcement_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('announcement_id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('title')">
                                {{ __('announcement::admin.title') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('announcement::admin.active_status_info') }}">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                {{ __('announcement::admin.status') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">{{ __('announcement::admin.operations') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($announcements as $announcement)
                    <tr class="hover-trigger" wire:key="row-{{ $announcement->announcement_id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $announcement->announcement_id }}</span>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $announcement->announcement_id }}"
                                    class="form-check-input hover-show" @if(in_array($announcement->announcement_id, $selectedItems))
                                checked @endif>
                            </div>
                        </td>
                        <td wire:key="title-{{ $announcement->announcement_id }}" class="position-relative">
                            @if($editingTitleId === $announcement->announcement_id)
                            <div class="d-flex align-items-center gap-3" x-data
                                @click.outside="$wire.updateTitleInline()">
                                <div class="flexible-input-wrapper">
                                    <input type="text" wire:model.defer="newTitle"
                                        class="form-control form-control-sm flexible-input"
                                        placeholder="{{ __('announcement::admin.enter_new_title') }}" wire:keydown.enter="updateTitleInline"
                                        wire:keydown.escape="$set('editingTitleId', null)" x-init="$nextTick(() => {
                                                $el.focus();
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px';
                                            })" x-on:input="
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px'
                                            " style="min-width: 60px; max-width: 100%;">
                                </div>
                                <button class="btn px-2 py-1 btn-outline-success" wire:click="updateTitleInline">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn px-2 py-1 btn-outline-danger"
                                    wire:click="$set('editingTitleId', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @else
                            <div class="d-flex align-items-center">
                                <span class="editable-title pr-4">{{ $announcement->getTranslated('title', app()->getLocale()) ?? $announcement->getTranslated('title', 'tr') }}</span>
                                <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                    wire:click="startEditingTitle({{ $announcement->announcement_id }}, '{{ addslashes($announcement->getTranslated('title', app()->getLocale()) ?? $announcement->getTranslated('title', 'tr')) }}')"
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                        <td wire:key="status-{{ $announcement->announcement_id }}" class="text-center align-middle">
                            <button wire:click="toggleActive({{ $announcement->announcement_id }})"
                                class="btn btn-icon btn-sm {{ $announcement->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $announcement->announcement_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $announcement->announcement_id }})">
                                    @if($announcement->is_active)
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
                                        <a href="{{ route('admin.announcement.manage', $announcement->announcement_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('announcement::admin.edit') }}">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        @hasmoduleaccess('announcement', 'delete')
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                                    module: 'announcement',
                                                    id: {{ $announcement->announcement_id }},
                                                    title: '{{ addslashes($announcement->getTranslated('title', app()->getLocale()) ?? $announcement->getTranslated('title', 'tr')) }}'
                                                })" class="dropdown-item link-danger">
                                                    {{ __('announcement::admin.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                        @endhasmoduleaccess
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('announcement::admin.no_records_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('announcement::admin.no_records_criteria') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
    {{ $announcements->links() }}
    <!-- Bulk Actions -->
    @include('announcement::admin.partials.bulk-actions', ['moduleType' => 'announcement'])

    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
</div>