@php
    View::share('pretitle', 'Favori İstatistikleri');
@endphp

<div class="favorite-statistics-wrapper">
    @include('favorite::admin.helper')

    <div class="row row-deck row-cards">
        <!-- Genel İstatistikler -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Genel İstatistikler</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-primary">{{ number_format($stats['total']) }}</h1>
                                <p class="text-muted mb-0">Toplam Favori</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-success">{{ number_format($stats['total_users']) }}</h1>
                                <p class="text-muted mb-0">Aktif Kullanıcı</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-info">{{ $stats['by_model']->count() }}</h1>
                                <p class="text-muted mb-0">Farklı Model Tipi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Model Tiplerine Göre Dağılım -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Model Tiplerine Göre Dağılım</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Model Tipi</th>
                                    <th class="text-end">Favori Sayısı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['by_model'] as $model => $count)
                                    <tr>
                                        <td>
                                            <span class="badge bg-azure-lt">{{ $model }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ number_format($count) }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Veri yok</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- En Çok Favori Alan İçerikler -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">En Çok Favori Alan İçerikler (Top 10)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-sm">
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    <th>ID</th>
                                    <th class="text-end">Favori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['most_favorited'] as $item)
                                    <tr>
                                        <td>
                                            <span class="badge bg-cyan-lt">{{ class_basename($item->favoritable_type) }}</span>
                                        </td>
                                        <td><code>{{ $item->favoritable_id }}</code></td>
                                        <td class="text-end">
                                            <strong class="text-success">{{ $item->count }}x</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Veri yok</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Son Eklenen Favoriler -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Son Eklenen Favoriler</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Model Tipi</th>
                                    <th>Model ID</th>
                                    <th>Eklenme Tarihi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent'] as $favorite)
                                    <tr>
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
                                            <span class="badge bg-azure-lt">{{ class_basename($favorite->favoritable_type) }}</span>
                                        </td>
                                        <td><code>{{ $favorite->favoritable_id }}</code></td>
                                        <td>
                                            <small class="text-muted">{{ $favorite->created_at->format('d.m.Y H:i') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Henüz favori yok</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
