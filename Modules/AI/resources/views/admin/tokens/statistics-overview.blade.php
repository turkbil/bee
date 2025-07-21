@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Kredi Yönetimi')
@section('title', 'Genel İstatistikler')

@section('content')
<!-- System Statistics Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Kiracı</div>
                </div>
                <div class="h1 mb-3">{{ number_format($systemStats['total_tenants']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kayıtlı Kiracı</div>
                    <div class="ms-auto">
                        <span class="badge badge-outline text-green">
                            {{ number_format($systemStats['active_ai_tenants']) }} Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Dağıtılan Kredi</div>
                </div>
                <div class="h1 mb-3">{{ number_format($systemStats['total_tokens_distributed'], 0) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kredi</div>
                    <div class="ms-auto">
                        <span class="badge badge-outline text-blue">
                            {{ number_format($systemStats['total_tokens_used'], 0) }} Kullanıldı
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Satın Alım</div>
                </div>
                <div class="h1 mb-3">{{ number_format($systemStats['total_purchases']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">İşlem</div>
                    <div class="ms-auto">
                        <span class="badge badge-outline text-green">
                            {{ number_format($systemStats['total_revenue'], 2) }} ₺
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Haftalık Büyüme</div>
                </div>
                <div class="h1 mb-3 {{ $weeklyGrowth >= 0 ? 'text-green' : 'text-red' }}">
                    {{ $weeklyGrowth >= 0 ? '+' : '' }}{{ number_format($weeklyGrowth, 1) }}%
                </div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Bu Hafta: {{ number_format($currentWeekUsage, 0) }} Kredi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Monthly Trend Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Aylık Kullanım Trendi (Son 12 Ay)</h3>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Top Models -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">En Çok Kullanılan Modeller</h3>
            </div>
            <div class="card-body">
                @if($topModels->count() > 0)
                    @foreach($topModels as $model)
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <span class="badge badge-outline">{{ $model->model }}</span>
                        </div>
                        <div class="col">
                            <div class="progress progress-sm">
                                <div class="progress-bar" style="width: {{ $topModels->first()->total_tokens > 0 ? ($model->total_tokens / $topModels->first()->total_tokens) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="text-muted">{{ \App\Helpers\TokenHelper::format($model->total_tokens) }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">Henüz model kullanım verisi yok</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tenant Activity -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">En Aktif Kiracılar (Son 30 Gün)</h3>
            </div>
            <div class="card-body">
                @if($tenantActivity->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Kiracı</th>
                                <th>Toplam Kredi Kullanımı</th>
                                <th>Mevcut Bakiye</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenantActivity as $index => $activity)
                            <tr>
                                <td>
                                    <span class="badge badge-outline">{{ $index + 1 }}</span>
                                </td>
                                <td>
                                    @if($activity->tenant)
                                        <strong>{{ $activity->tenant->title }}</strong>
                                    @else
                                        <span class="text-muted">Bilinmiyor</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline text-blue">
                                        {{ number_format($activity->total_tokens, 0) }} Kredi
                                    </span>
                                </td>
                                <td>
                                    @if($activity->tenant)
                                        <span class="badge badge-outline text-green">
                                            {{ number_format($activity->tenant->real_balance, 0) }} Kredi
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activity->tenant && $activity->tenant->ai_enabled)
                                        <span class="badge badge-outline text-green">Aktif</span>
                                    @else
                                        <span class="badge badge-outline text-red">Pasif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('admin.ai.credits.tenant-statistics', $activity->tenant_id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Detaylı İstatistik">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="{{ route('admin.ai.credits.show', $activity->tenant_id) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Kiracı Detayı">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-chart-bar text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz aktivite yok</p>
                    <p class="empty-subtitle text-muted">
                        Son 30 gün içinde hiçbir kiracı token kullanımı gerçekleştirmemiş.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Health -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sistem Sağlığı</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-auto">
                        <i class="fas fa-database text-blue"></i>
                    </div>
                    <div class="col">
                        <div class="text-muted">Ortalama Kiracı Bakiyesi</div>
                        <div class="h4 mb-0">{{ number_format($systemStats['avg_tokens_per_tenant'] ?? 0, 0) }} Kredi</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-auto">
                        <i class="fas fa-trophy text-yellow"></i>
                    </div>
                    <div class="col">
                        <div class="text-muted">En Aktif Kiracı</div>
                        <div class="h4 mb-0">
                            @if($systemStats['most_active_tenant'])
                                {{ $systemStats['most_active_tenant']->title }}
                            @else
                                <span class="text-muted">Henüz yok</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-auto">
                        <i class="fas fa-percentage text-green"></i>
                    </div>
                    <div class="col">
                        <div class="text-muted">AI Aktif Kiracı Oranı</div>
                        <div class="h4 mb-0">
                            {{ $systemStats['total_tenants'] > 0 ? number_format(($systemStats['active_ai_tenants'] / $systemStats['total_tenants']) * 100, 1) : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hızlı İşlemler</h3>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.ai.credits.usage-stats') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Detaylı Kullanım İstatistikleri
                    </a>
                    <a href="{{ route('admin.ai.credits.purchases') }}" class="btn btn-outline-info">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Tüm Satın Alımları Görüntüle
                    </a>
                    <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-outline-success">
                        <i class="fas fa-box me-2"></i>
                        Kredi Paketlerini Yönet
                    </a>
                    <a href="{{ route('admin.ai.credits.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-users me-2"></i>
                        Kiracı Yönetimi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const monthlyTrendData = @json($monthlyTrend);
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(monthlyTrendData),
            datasets: [{
                label: 'Aylık Kredi Kullanımı',
                data: Object.values(monthlyTrendData),
                borderColor: 'rgb(32, 107, 196)',
                backgroundColor: 'rgba(32, 107, 196, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endpush