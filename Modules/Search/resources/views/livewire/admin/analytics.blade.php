@php
    View::share('pretitle', 'Arama Sistemi');
@endphp

<div class="search-analytics-component-wrapper">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Arama Analytics</h3>
            <div class="card-actions">
                <select wire:model.live="days" class="form-select">
                    <option value="7">Son 7 Gün</option>
                    <option value="30">Son 30 Gün</option>
                    <option value="90">Son 90 Gün</option>
                </select>
            </div>
        </div>

        <div class="card-body">

            {{-- Stats Cards --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Toplam Arama</div>
                            <h2 class="mb-0">{{ number_format($stats['total_searches'] ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Benzersiz Sorgu</div>
                            <h2 class="mb-0">{{ number_format($stats['unique_queries'] ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Ortalama Süre</div>
                            <h2 class="mb-0">{{ round($stats['avg_response_time'] ?? 0) }}ms</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted">Tıklama Oranı</div>
                            <h2 class="mb-0">{{ round($stats['avg_ctr'] ?? 0, 1) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Popular Searches --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Popüler Aramalar</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>Arama Sorgusu</th>
                                <th class="text-end">Arama Sayısı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($popularSearches as $search)
                                <tr>
                                    <td>{{ $search->query }}</td>
                                    <td class="text-end">{{ $search->search_count }} arama</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Zero Results --}}
            @if(count($zeroResultSearches) > 0)
                <div class="card">
                    <div class="card-header bg-danger-lt">
                        <h3 class="card-title text-danger">Sonuç Bulunamayan Aramalar</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Arama Sorgusu</th>
                                    <th class="text-end">Deneme Sayısı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zeroResultSearches as $search)
                                    <tr>
                                        <td>{{ $search->query }}</td>
                                        <td class="text-end text-danger">{{ $search->attempt_count }} deneme</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
