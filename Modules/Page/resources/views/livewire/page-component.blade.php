<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <!-- FontAwesome Arama İkonu -->
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Aramak için yazmaya başlayın...">
                </div>
            </div>

            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                     wire:target="search, perPage, sortBy, gotoPage, previousPage, nextPage"
                     class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                    <!-- Yeni Loading: Progress Bar -->
                    <div class="small text-muted mb-2">Yükleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>

            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Table Switch -->
                    <div class="table-mode">
                        <input type="checkbox" id="table-switch" class="table-switch">
                        <div class="app">
                            <div class="switch-content">
                                <div class="switch-label"></div>
                                <label for="table-switch">
                                    <div class="toggle"></div>
                                    <div class="names">
                                        <p class="large" data-bs-toggle="tooltip" data-bs-placement="left" title="Satırları daralt">
                                            <i class="fa-thin fa-table-cells fa-lg fa-fade" style="--fa-animation-duration: 2s;"></i>
                                        </p>
                                        <p class="small" data-bs-toggle="tooltip" data-bs-placement="left" title="Satırları genişlet">
                                            <i class="fa-thin fa-table-cells-large fa-lg fa-fade" style="--fa-animation-duration: 2s;"></i>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
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
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px">
                            <button class="table-sort {{ $sortField === 'page_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('page_id')">
                                ID
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                Başlık
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">
                            <button class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                Durum
                            </button>
                        </th>
                        <th class="text-center" style="width: 120px">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($pages as $page)
                        <tr>
                            <td class="sort-id">{{ $page->page_id }}</td>
                            <td class="sort-title">{{ $page->title }}</td>
                            <td wire:key="status-{{ $page->page_id }}" class="text-center align-middle">
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
                            </td>
                            <td class="text-center align-middle">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <a href="{{ route('admin.page.manage', $page->page_id) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                    </a>
                                </div>
                                <div class="col lh-1">
                                    <div class="dropdown mt-1">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item btn-delete link-danger" data-module="page" data-id="{{ $page->page_id }}" data-title="{{ $page->title }}"> Sil </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                            </td>
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
    <!-- Pagination -->
    {{ $pages->links() }}
</div>
