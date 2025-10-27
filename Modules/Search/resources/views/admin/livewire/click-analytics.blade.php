@php
    View::share('pretitle', 'Tıklama İstatistikleri');
@endphp
@include('search::admin.helper')
<div>
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Tıklama</div>
                        <div class="ms-auto">
                            <i class="ti ti-click fs-1 text-blue"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['total_clicks']) }}</div>
                    <div class="text-muted small">Tüm tıklamalar</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Benzersiz İçerik</div>
                        <div class="ms-auto">
                            <i class="ti ti-file fs-1 text-green"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['unique_items']) }}</div>
                    <div class="text-muted small">Farklı içerik</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Benzersiz Sorgu</div>
                        <div class="ms-auto">
                            <i class="ti ti-search fs-1 text-purple"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ number_format($stats['unique_queries']) }}</div>
                    <div class="text-muted small">Farklı arama</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Yeni Sekme</div>
                        <div class="ms-auto">
                            <i class="ti ti-external-link fs-1 text-orange"></i>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['new_tab_percentage'] }}%</div>
                    <div class="text-muted small">Yeni sekmede açılma oranı</div>
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
                    <label class="form-label">Gruplama</label>
                    <select wire:model.live="groupBy" class="form-select">
                        <option value="item">İçeriğe Göre</option>
                        <option value="query">Sorguya Göre</option>
                        <option value="position">Pozisyona Göre</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if($groupBy === 'item')
                    En Çok Tıklanan İçerikler
                @elseif($groupBy === 'query')
                    Aramaya Göre Tıklamalar
                @else
                    Pozisyona Göre Tıklamalar
                @endif
            </h3>
            <div class="ms-auto">
                <span class="text-muted">Toplam: {{ $results->total() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        @if($groupBy === 'item')
                            <th>#</th>
                            <th>İçerik</th>
                            <th>Tip</th>
                            <th class="text-center">Tıklama</th>
                            <th class="text-center">Benzersiz Sorgu</th>
                            <th class="text-center">Ort. Pozisyon</th>
                            <th class="text-center">Yeni Sekme</th>
                            <th>Son Tıklama</th>
                            <th class="text-end">İşlem</th>
                        @elseif($groupBy === 'query')
                            <th>#</th>
                            <th>Arama Sorgusu</th>
                            <th class="text-center">Tıklama</th>
                            <th class="text-center">Benzersiz İçerik</th>
                            <th class="text-center">Ort. Pozisyon</th>
                            <th class="text-center">Yeni Sekme</th>
                            <th>Son Tıklama</th>
                        @else
                            <th>Pozisyon</th>
                            <th class="text-center">Tıklama</th>
                            <th class="text-center">Benzersiz İçerik</th>
                            <th class="text-center">Benzersiz Sorgu</th>
                            <th class="text-center">Yeni Sekme</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $index => $result)
                    <tr>
                        @if($groupBy === 'item')
                            <td>{{ $results->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong>{{ $result['title'] }}</strong>
                                    <span class="text-muted small">ID: {{ $result['id'] }}</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeLabel = match(true) {
                                        str_contains($result['type'], 'Product') => 'Ürün',
                                        str_contains($result['type'], 'Category') => 'Kategori',
                                        str_contains($result['type'], 'Brand') => 'Marka',
                                        default => 'Diğer'
                                    };
                                    $typeColor = match(true) {
                                        str_contains($result['type'], 'Product') => 'blue',
                                        str_contains($result['type'], 'Category') => 'green',
                                        str_contains($result['type'], 'Brand') => 'purple',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $typeColor }}-lt">{{ $typeLabel }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue">{{ $result['click_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['unique_queries'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['avg_position'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['new_tab_count'] }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($result['latest_click'])->format('d.m.Y H:i') }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ $result['url'] }}" target="_blank" class="btn btn-sm btn-icon btn-ghost-secondary" title="Görüntüle">
                                    <i class="ti ti-external-link"></i>
                                </a>
                            </td>
                        @elseif($groupBy === 'query')
                            <td>{{ $results->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $result['query'] }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue">{{ $result['click_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['unique_items'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['avg_position'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['new_tab_count'] }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ \Carbon\Carbon::parse($result['latest_click'])->format('d.m.Y H:i') }}</span>
                            </td>
                        @else
                            <td>
                                <span class="badge bg-purple">Pozisyon {{ $result['position'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue">{{ $result['click_count'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['unique_items'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['unique_queries'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $result['new_tab_count'] }}</span>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="ti ti-click fs-1 mb-2"></i>
                            <div>Tıklama kaydı bulunamadı</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
        <div class="card-footer">
            {{ $results->links() }}
        </div>
        @endif
    </div>
</div>
