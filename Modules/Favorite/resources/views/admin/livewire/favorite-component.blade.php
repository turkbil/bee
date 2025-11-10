@php
    View::share('pretitle', 'Favori Yönetimi');
@endphp

<div class="favorite-component-wrapper">
    <div class="card">
        @include('favorite::admin.helper')
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Kullanıcı ara (isim, email)...">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, deleteFavorite, filterType"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf (Filter ve PerPage) -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Filtre -->
                        <div style="width: 120px;">
                            <select wire:model.live="filterType" class="form-control" data-choices>
                                <option value="all">Tümü</option>
                                <option value="user">Kullanıcıya Göre</option>
                                <option value="model">Modele Göre</option>
                            </select>
                        </div>
                        <!-- Sayfa Adeti -->
                        <div style="width: 80px;">
                            <select wire:model.live="perPage" class="form-control" data-choices>
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="500">500</option>
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
                                    class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('id')">
                                    ID
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'user_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('user_id')">
                                    Kullanıcı
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'favoritable_type' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('favoritable_type')">
                                    Model Tipi
                                </button>
                            </th>
                            <th>Model ID</th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('created_at')">
                                    Eklenme Tarihi
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($favorites as $favorite)
                            <tr wire:key="row-{{ $favorite->id }}">
                                <td class="sort-id small">{{ $favorite->id }}</td>
                                <td>
                                    @if($favorite->user)
                                        <div>
                                            <strong>{{ $favorite->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $favorite->user->email }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">
                                        {{ class_basename($favorite->favoritable_type) }}
                                    </span>
                                </td>
                                <td>
                                    <code>{{ $favorite->favoritable_id }}</code>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $favorite->created_at->format('d.m.Y H:i') }}
                                    </small>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="javascript:void(0);"
                                            wire:click="deleteFavorite({{ $favorite->id }})"
                                            wire:confirm="Bu favoriyi silmek istediğinizden emin misiniz?"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="{{ __('admin.delete') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">Favori bulunamadı</p>
                                        <p class="empty-subtitle text-muted">
                                            Henüz hiç favori eklenmemiş.
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
            @if ($favorites->hasPages())
                {{ $favorites->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $favorites->total() }}</span> favori
                    </p>
                </div>
            @endif
        </div>

    </div>
</div>
