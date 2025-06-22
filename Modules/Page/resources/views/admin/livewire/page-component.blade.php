@include('page::admin.helper')
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
                        placeholder="{{ t('page::general.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ t('common.updating') }}</div>
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
                                    class="table-sort {{ $sortField === 'page_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('page_id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('title')">
                                {{ t('page::general.title_field') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 50px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ t('page::general.home') }}">
                            <i class="fa-solid fa-house"></i>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ t('page::general.status') }}">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                {{ t('page::general.status') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($pages as $page)
                    <tr class="hover-trigger" wire:key="row-{{ $page->page_id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $page->page_id }}</span>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $page->page_id }}"
                                    class="form-check-input hover-show" @if(in_array($page->page_id, $selectedItems))
                                checked @endif>
                            </div>
                        </td>
                        <td wire:key="title-{{ $page->page_id }}" class="position-relative">
                            @if($editingTitleId === $page->page_id)
                            <div class="d-flex align-items-center gap-3" x-data
                                @click.outside="$wire.updateTitleInline()">
                                <div class="flexible-input-wrapper">
                                    <input type="text" wire:model.defer="newTitle"
                                        class="form-control form-control-sm flexible-input"
                                        placeholder="{{ t('page::general.title_field') }}" wire:keydown.enter="updateTitleInline"
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
                                <span class="editable-title pr-4">{{ $page->title }}</span>
                                <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                    wire:click="startEditingTitle({{ $page->page_id }}, '{{ $page->title }}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($page->is_homepage)
                                <i class="fa-solid fa-house link-secondary" title="{{ t('page::general.home') }}"></i>
                            @endif
                        </td>
                        <td wire:key="status-{{ $page->page_id }}" class="text-center align-middle">
                            @if(!$page->is_homepage)
                            <button wire:click="toggleActive({{ $page->page_id }})"
                                class="btn btn-icon btn-sm {{ $page->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $page->page_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $page->page_id }})">
                                    @if($page->is_active)
                                    <i class="fas fa-check"></i>
                                    @else
                                    <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </button>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('admin.page.manage', $page->page_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ t('common.edit') }}">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $page->page_id]) }}" target="_blank"
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ t('studio.editor', ['default' => 'Studio Editor']) }}">
                                            <i class="fa-solid fa-wand-magic-sparkles link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        @if(!$page->is_homepage)
                                        @hasmoduleaccess('page', 'delete')
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                                    module: 'page',
                                                    id: {{ $page->page_id }},
                                                    title: '{{ $page->title }}'
                                                })" class="dropdown-item link-danger">
                                                    {{ t('common.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                        @endhasmoduleaccess
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
                                <p class="empty-title">{{ t('page::messages.no_pages_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ t('page::messages.no_results') }}
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
    {{ $pages->links() }}
    <!-- Bulk Actions -->
    @include('page::admin.partials.bulk-actions', ['moduleType' => 'page'])

    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
</div>