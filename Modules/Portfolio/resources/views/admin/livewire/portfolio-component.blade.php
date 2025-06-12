@include('portfolio::admin.helper')
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
                        placeholder="Aramak için yazmaya başlayın...">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, selectedCategory"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <div style="width: 200px">
                        <select wire:model.live="selectedCategory" class="form-select">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->portfolio_category_id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 60px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
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
                                    class="table-sort {{ $sortField === 'portfolio_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('portfolio_id')">
                                </button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('title')">
                                Başlık
                            </button>
                        </th>
                        <th style="width: 160px">
                            <button
                                class="table-sort {{ $sortField === 'portfolio_category_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('portfolio_category_id')">
                                Kategori
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="Aktiflik Durumu">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                Durum
                            </button>
                        </th>
                        <th class="text-center" style="width: 120px">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($portfolios as $portfolio)
                    <tr class="hover-trigger" wire:key="row-{{ $portfolio->portfolio_id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">{{ $portfolio->portfolio_id }}</span>
                                <input type="checkbox" wire:model.live="selectedItems"
                                    value="{{ $portfolio->portfolio_id }}" class="form-check-input hover-show"
                                    @if(in_array($portfolio->portfolio_id, $selectedItems))
                                checked @endif>
                            </div>
                        </td>
                        <td wire:key="title-{{ $portfolio->portfolio_id }}" class="position-relative">
                            @if($editingTitleId === $portfolio->portfolio_id)
                            <div class="d-flex align-items-center gap-3" x-data
                                @click.outside="$wire.updateTitleInline()">
                                <div class="flexible-input-wrapper">
                                    <input type="text" wire:model.defer="newTitle"
                                        class="form-control form-control-sm flexible-input"
                                        placeholder="Yeni başlık girin" wire:keydown.enter="updateTitleInline"
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
                                <span class="editable-title pr-4">{{ $portfolio->title }}</span>
                                <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                    wire:click="startEditingTitle({{ $portfolio->portfolio_id }}, '{{ $portfolio->title }}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                        <td>
                            <a href="javascript:void(0);"
                                wire:click="$set('selectedCategory', '{{ $portfolio->category->portfolio_category_id ?? '' }}')"
                                class="text-muted {{ $selectedCategory == ($portfolio->category->portfolio_category_id ?? '') ? '' : '' }}">
                                {{ $portfolio->category->title ?? '-' }}
                            </a>
                        </td>
                        <td wire:key="status-{{ $portfolio->portfolio_id }}" class="text-center align-middle">
                            <button wire:click="toggleActive({{ $portfolio->portfolio_id }})"
                                class="btn btn-icon btn-sm {{ $portfolio->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                <!-- Loading Durumu -->
                                <div wire:loading wire:target="toggleActive({{ $portfolio->portfolio_id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                <div wire:loading.remove wire:target="toggleActive({{ $portfolio->portfolio_id }})">
                                    @if($portfolio->is_active)
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
                                        <a href="{{ route('admin.portfolio.manage', $portfolio->portfolio_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                                    module: 'portfolio',
                                                    id: {{ $portfolio->portfolio_id }},
                                                    title: '{{ $portfolio->title }}'
                                                })" class="dropdown-item link-danger">
                                                    Sil
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
                        <td colspan="4" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Arama kriterlerinize uygun kayıt bulunmamaktadır.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $portfolios->links() }}

    @include('portfolio::admin.partials.bulk-actions')

    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
</div>