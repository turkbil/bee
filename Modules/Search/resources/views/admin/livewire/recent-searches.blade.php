@php
    View::share('pretitle', 'Son Aramalar');
@endphp
@include('search::admin.helper')
<div>
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bugün</div>
                        <div class="ms-auto">
                            <i class="fas fa-clock fa-2x text-blue"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['total_today']) }}</div>
                    <div class="text-muted small">Toplam arama</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bu Hafta</div>
                        <div class="ms-auto">
                            <i class="fas fa-calendar-week fa-2x text-green"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['total_week']) }}</div>
                    <div class="text-muted small">Toplam arama</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bu Ay</div>
                        <div class="ms-auto">
                            <i class="fas fa-calendar-alt fa-2x text-purple"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['total_month']) }}</div>
                    <div class="text-muted small">Toplam arama</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Sonuçsuz (Bugün)</div>
                        <div class="ms-auto">
                            <i class="fas fa-search-minus fa-2x text-red"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['zero_results_today']) }}</div>
                    <div class="text-muted small">Sıfır sonuç</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tarih Filtresi</label>
                    <select wire:model.live="dateFilter" class="form-select">
                        <option value="today">Bugün</option>
                        <option value="week">Bu Hafta</option>
                        <option value="month">Bu Ay</option>
                        <option value="all" selected>Tümü</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tip Filtresi</label>
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="all">Tümü</option>
                        <option value="products">Ürünler</option>
                        <option value="categories">Kategoriler</option>
                        <option value="brands">Markalar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Arama Terimi</label>
                    <input type="text" wire:model.live.debounce.500ms="searchTerm" class="form-control" placeholder="Ara...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">IP Adresi</label>
                    <input type="text" wire:model.live.debounce.500ms="ipFilter" class="form-control" placeholder="IP Filtrele...">
                    @if($ipFilter)
                        <button type="button" wire:click="$set('ipFilter', '')" class="btn btn-sm btn-ghost-secondary mt-1">
                            <i class="ti ti-x"></i> Temizle
                        </button>
                    @endif
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" wire:model.live="showZeroResults" class="form-check-input" id="zeroResults">
                        <label class="form-check-label" for="zeroResults">Sadece Sonuçsuz</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Searches Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Son Aramalar</h3>
            <div class="ms-auto">
                <span class="text-muted">Toplam: {{ $searches->total() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Arama Sorgusu</th>
                        <th>Kullanıcı</th>
                        <th>Tip</th>
                        <th class="text-center">Sonuç</th>
                        <th class="text-center">Süre (ms)</th>
                        <th>Dil</th>
                        <th>IP</th>
                        <th class="text-center" style="width: 160px">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($searches as $search)
                    <tr>
                        <td>
                            <div>{{ $search->created_at->format('d.m.Y') }}</div>
                            <div class="text-muted small">{{ $search->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <strong>{{ $search->query }}</strong>
                                @if($search->is_popular)
                                    <span class="badge bg-yellow text-dark" title="Popüler">
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                @endif
                                @if($search->is_hidden)
                                    <span class="badge bg-red" title="Gizli">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($search->user)
                                <a href="{{ route('admin.usermanagement.manage', $search->user->id) }}">
                                    {{ $search->user->name }}
                                </a>
                            @else
                                <span class="text-muted">Misafir</span>
                            @endif
                        </td>
                        <td>
                            @if($search->searchable_type)
                                <span class="badge bg-blue-lt">{{ ucfirst($search->searchable_type) }}</span>
                            @else
                                <span class="text-muted">Tümü</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($search->results_count > 0)
                                <span class="badge bg-green">{{ $search->results_count }}</span>
                            @else
                                <span class="badge bg-red">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-muted">{{ $search->response_time_ms ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge">{{ strtoupper($search->locale ?? 'tr') }}</span>
                        </td>
                        <td>
                            <button type="button"
                                    wire:click="filterByIp('{{ $search->ip_address }}')"
                                    class="btn btn-sm btn-ghost-secondary text-muted"
                                    title="Bu IP'ye göre filtrele">
                                {{ $search->ip_address }}
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex align-items-center gap-3 justify-content-center">
                                <a href="javascript:void(0);"
                                   wire:click="markAsPopular({{ $search->id }})"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="{{ $search->is_popular ? 'Popüler İşaretini Kaldır' : 'Popüler Olarak İşaretle' }}"
                                   style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                    <i class="{{ $search->is_popular ? 'fa-solid' : 'fa-regular' }} fa-star {{ $search->is_popular ? 'link-warning' : 'link-secondary' }} fa-lg"></i>
                                </a>
                                <a href="javascript:void(0);"
                                   wire:click="hideSearch({{ $search->id }})"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="{{ $search->is_hidden ? 'Görünür Yap' : 'Gizle' }}"
                                   style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                    <i class="fa-solid fa-eye{{ $search->is_hidden ? '-slash' : '' }} link-secondary fa-lg"></i>
                                </a>
                                <a href="javascript:void(0);"
                                   wire:click="deleteSearch({{ $search->id }})"
                                   wire:confirm="Bu arama kaydını silmek istediğinize emin misiniz?"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="Sil"
                                   style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                    <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fas fa-search-minus fa-3x mb-2"></i>
                            <div>Kayıt bulunamadı</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($searches->hasPages())
        <div class="card-footer">
            {{ $searches->links() }}
        </div>
        @endif
    </div>
</div>
