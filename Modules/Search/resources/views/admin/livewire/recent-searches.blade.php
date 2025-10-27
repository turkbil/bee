<div>
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bugün</div>
                        <div class="ms-auto">
                            <i class="ti ti-clock fs-1 text-blue"></i>
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
                            <i class="ti ti-calendar-week fs-1 text-green"></i>
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
                            <i class="ti ti-calendar-month fs-1 text-purple"></i>
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
                            <i class="ti ti-search-off fs-1 text-red"></i>
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
                        <option value="all">Tümü</option>
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
                <div class="col-md-3">
                    <label class="form-label">Arama Terimi</label>
                    <input type="text" wire:model.live.debounce.500ms="searchTerm" class="form-control" placeholder="Ara...">
                </div>
                <div class="col-md-3">
                    <label class="form-label d-block">&nbsp;</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" wire:model.live="showZeroResults" class="form-check-input" id="zeroResults">
                        <label class="form-check-label" for="zeroResults">Sadece Sonuçsuz Aramalar</label>
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
                        <th class="text-center">Tıklamalar</th>
                        <th class="text-center">Süre (ms)</th>
                        <th>Dil</th>
                        <th>IP</th>
                        <th class="text-end">İşlemler</th>
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
                                        <i class="ti ti-star-filled"></i>
                                    </span>
                                @endif
                                @if($search->is_hidden)
                                    <span class="badge bg-red" title="Gizli">
                                        <i class="ti ti-eye-off"></i>
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
                            @if($search->clicks_count > 0)
                                <span class="badge bg-purple">{{ $search->clicks_count }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-muted">{{ $search->response_time_ms ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge">{{ strtoupper($search->locale ?? 'tr') }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $search->ip_address }}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group" role="group">
                                <button type="button"
                                        wire:click="markAsPopular({{ $search->id }})"
                                        class="btn btn-sm btn-icon {{ $search->is_popular ? 'btn-warning' : 'btn-ghost-secondary' }}"
                                        title="{{ $search->is_popular ? 'Popüler İşaretini Kaldır' : 'Popüler Olarak İşaretle' }}">
                                    <i class="ti ti-star{{ $search->is_popular ? '-filled' : '' }}"></i>
                                </button>
                                <button type="button"
                                        wire:click="hideSearch({{ $search->id }})"
                                        class="btn btn-sm btn-icon {{ $search->is_hidden ? 'btn-danger' : 'btn-ghost-secondary' }}"
                                        title="{{ $search->is_hidden ? 'Görünür Yap' : 'Gizle' }}">
                                    <i class="ti ti-eye{{ $search->is_hidden ? '-off' : '' }}"></i>
                                </button>
                                <button type="button"
                                        wire:click="deleteSearch({{ $search->id }})"
                                        wire:confirm="Bu arama kaydını silmek istediğinize emin misiniz?"
                                        class="btn btn-sm btn-icon btn-ghost-secondary"
                                        title="Sil">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="ti ti-search-off fs-1 mb-2"></i>
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
