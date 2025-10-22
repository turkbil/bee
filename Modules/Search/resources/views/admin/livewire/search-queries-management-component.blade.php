@php
    View::share('pretitle', 'Arama Yönetimi');
@endphp

<div class="search-queries-management-wrapper">
    <div class="card">
        @include('search::admin.helper')
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col-md-5">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Arama sorgusunda ara...">
                    </div>
                </div>
                <!-- Yeni Arama Ekle -->
                <div class="col-md-5" x-data="{ newQuery: @entangle('newQuery') }">
                    <div class="input-group">
                        <input type="text"
                            wire:model="newQuery"
                            x-model="newQuery"
                            class="form-control"
                            placeholder="Yeni arama sorgusu ekle..."
                            @keydown.enter="if(newQuery.trim()) $wire.addQuery()">
                        <button class="btn btn-primary"
                            wire:click="addQuery"
                            type="button"
                            :disabled="!newQuery.trim()"
                            x-bind:class="{'opacity-50': !newQuery.trim()}">
                            <i class="fas fa-plus me-1"></i>
                            Ekle
                        </button>
                    </div>
                </div>
                <!-- Loading -->
                <div class="col-md-1 position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, togglePopular, toggleHidden, deleteQuery"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sayfa Adeti -->
                <div class="col-md-1">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Sayfa Adeti Seçimi -->
                        <div style="width: 80px; min-width: 80px">
                            <select wire:model.live="perPage" class="form-control listing-filter-select" data-choices
                                data-choices-search="false" data-choices-filter="true">
                                <option value="10">
                                    <nobr>10</nobr>
                                </option>
                                <option value="50">
                                    <nobr>50</nobr>
                                </option>
                                <option value="100">
                                    <nobr>100</nobr>
                                </option>
                                <option value="500">
                                    <nobr>500</nobr>
                                </option>
                                <option value="1000">
                                    <nobr>1000</nobr>
                                </option>
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
                                <button
                                    class="table-sort {{ $sortField === 'search_count' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('search_count')">
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'query' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('query')">
                                    Arama Sorgusu
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">
                                <button
                                    class="table-sort {{ $sortField === 'search_count' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('search_count')">
                                    Arama Sayısı
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">
                                Son Bulunan
                            </th>
                            <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Popüler">
                                <button
                                    class="table-sort {{ $sortField === 'is_popular' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_popular')">
                                    Popüler
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Gizli">
                                <button
                                    class="table-sort {{ $sortField === 'is_hidden' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_hidden')">
                                    Gizli
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($queries as $query)
                            <tr class="hover-trigger" wire:key="row-{{ md5($query->query) }}">
                                <td class="sort-id small">
                                    <span class="badge bg-primary-lt">{{ $query->search_count }}</span>
                                </td>
                                <td wire:key="query-{{ md5($query->query) }}" class="position-relative">
                                    @if ($editingQueryId === $query->query)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateQueryInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="editingQueryText"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="Arama sorgusu"
                                                    wire:keydown.enter="updateQueryInline"
                                                    wire:keydown.escape="$set('editingQueryId', null)"
                                                    x-init="$nextTick(() => {
                                                        $el.focus();
                                                        $el.style.width = '20px';
                                                        $el.style.width = ($el.scrollWidth + 2) + 'px';
                                                    })"
                                                    x-on:input="
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px'
                                            "
                                                    style="min-width: 60px; max-width: 100%;">
                                            </div>
                                            <button class="btn px-2 py-1 btn-outline-success"
                                                wire:click="updateQueryInline">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn px-2 py-1 btn-outline-danger"
                                                wire:click="$set('editingQueryId', null)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <span class="editable-title pr-4">{{ $query->query }}</span>
                                            <button class="btn btn-sm px-2 py-1 edit-icon ms-4"
                                                wire:click="startEditingQuery('{{ addslashes($query->query) }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary">{{ number_format($query->search_count) }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-info-lt">{{ number_format($query->last_results_count ?? 0) }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="togglePopular('{{ addslashes($query->query) }}')"
                                        class="btn btn-icon btn-sm {{ $query->is_popular ? 'text-warning bg-transparent' : 'text-muted bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="togglePopular('{{ addslashes($query->query) }}')"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum -->
                                        <div wire:loading.remove
                                            wire:target="togglePopular('{{ addslashes($query->query) }}')">
                                            @if ($query->is_popular)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <button wire:click="toggleHidden('{{ addslashes($query->query) }}')"
                                        class="btn btn-icon btn-sm {{ $query->is_hidden ? 'text-danger bg-transparent' : 'text-muted bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading wire:target="toggleHidden('{{ addslashes($query->query) }}')"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum -->
                                        <div wire:loading.remove
                                            wire:target="toggleHidden('{{ addslashes($query->query) }}')">
                                            @if ($query->is_hidden)
                                                <i class="fas fa-eye-slash"></i>
                                            @else
                                                <i class="fas fa-eye"></i>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="javascript:void(0);"
                                            wire:click="deleteQuery('{{ addslashes($query->query) }}')"
                                            wire:confirm="Bu arama sorgusunu silmek istediğinize emin misiniz?"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Sil"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">Arama sorgusu bulunamadı</p>
                                        <p class="empty-subtitle text-muted">
                                            Filtreleri değiştirmeyi deneyin
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
        <div class="card-footer">
            @if ($queries->hasPages())
                {{ $queries->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $queries->total() }}</span> sonuç
                    </p>
                </div>
            @endif
        </div>

    </div>
</div>
