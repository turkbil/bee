@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Kredi Yönetimi')
@section('title', 'Kredi Kullanım İstatistikleri')

@section('content')
<!-- Tenant Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">Kredi Kullanım İstatistikleri</h5>
                        @if($selectedTenant)
                            <small class="text-muted">
                                {{ $tenants->where('id', $selectedTenant)->first()->title ?? 'Tenant #' . $selectedTenant }} verileri gösteriliyor
                            </small>
                        @else
                            <small class="text-muted">Tüm kiracıların verileri gösteriliyor</small>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-2"></i>
                                @if($selectedTenant)
                                    {{ $tenants->where('id', $selectedTenant)->first()->title ?? 'Tenant #' . $selectedTenant }}
                                @else
                                    Tüm Kiracılar
                                @endif
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ !$selectedTenant ? 'active' : '' }}" 
                                       href="{{ route('admin.ai.credits.usage-stats') }}">
                                        <i class="fas fa-globe me-2"></i>Tüm Kiracılar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($tenants as $tenant)
                                    <li>
                                        <a class="dropdown-item {{ $selectedTenant == $tenant->id ? 'active' : '' }}" 
                                           href="{{ route('admin.ai.credits.usage-stats', ['tenant_id' => $tenant->id]) }}">
                                            <i class="fas fa-user me-2"></i>{{ $tenant->title ?: 'Tenant #' . $tenant->id }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Statistics Cards -->
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Kullanım</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['total_usage'], 0) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kredi</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bugün</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['today_usage'], 0) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kredi</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bu Hafta</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['week_usage'], 0) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kredi</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bu Ay</div>
                </div>
                <div class="h1 mb-3">{{ number_format($stats['month_usage'], 0) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kredi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Daily Usage Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Son 30 Gün Günlük Kullanım</h3>
            </div>
            <div class="card-body">
                <canvas id="dailyUsageChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Provider/Model Analytics -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-server me-2"></i>
                    Provider/Model Analizi
                </h3>
            </div>
            <div class="card-body">
                @if(!empty($stats['provider_analysis']['providers']))
                    @foreach($stats['provider_analysis']['providers'] as $providerKey => $provider)
                    <div class="mb-3">
                        <div class="row align-items-center mb-2">
                            <div class="col-auto">
                                <span class="badge bg-primary">{{ $provider['name'] }}</span>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm">
                                    <div class="progress-bar" style="width: {{ $provider['token_percentage'] }}%"></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">{{ $provider['token_percentage'] }}%</span>
                            </div>
                        </div>
                        @if(!empty($provider['models']))
                            @foreach($provider['models'] as $modelName => $modelData)
                            <div class="row align-items-center mb-1 ms-3">
                                <div class="col-auto">
                                    <span class="badge badge-outline">{{ $modelName }}</span>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-secondary" style="width: {{ $modelData['token_percentage'] }}%"></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <span class="text-muted small">{{ number_format($modelData['total_tokens'], 0) }}</span>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">Henüz provider kullanım verisi yok</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Purpose Usage -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Amaç Bazlı Kullanım</h3>
            </div>
            <div class="card-body">
                @if(!empty($stats['by_purpose']))
                    @foreach($stats['by_purpose'] as $purpose => $usage)
                    <div class="row align-items-center mb-2">
                        <div class="col-auto">
                            <span class="badge badge-outline">{{ ucfirst($purpose) }}</span>
                        </div>
                        <div class="col">
                            <div class="progress progress-sm">
                                <div class="progress-bar" style="width: {{ $stats['by_purpose'] && max($stats['by_purpose']) > 0 ? ($usage / max($stats['by_purpose'])) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="text-muted">{{ number_format($usage, 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">Henüz kullanım verisi yok</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Usage -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Son Kullanım Kayıtları</h3>
            </div>
            <div class="card-body">
                @if($usageRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-sm">
                        <thead>
                            <tr>
                                <th>Kiracı</th>
                                <th>Token</th>
                                <th>Model</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageRecords->take(10) as $usage)
                            <tr>
                                <td>
                                    @if($usage->tenant)
                                        {{ Str::limit($usage->tenant->title, 20) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ number_format($usage->tokens_used, 0) }}</span>
                                </td>
                                <td>{{ $usage->model }}</td>
                                <td>{{ $usage->used_at ? $usage->used_at->format('d.m H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">Henüz kullanım verisi yok</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Detailed Usage Records -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detaylı Kullanım Kayıtları</h3>
            </div>
            <div class="card-body">
                @if($usageRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kiracı</th>
                                <th>Kullanıcı</th>
                                <th>Token</th>
                                <th>Model</th>
                                <th>Amaç</th>
                                <th>Açıklama</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usageRecords as $usage)
                            <tr>
                                <td>{{ $usage->id }}</td>
                                <td>
                                    @if($usage->tenant)
                                        {{ $usage->tenant->title }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($usage->user)
                                        {{ $usage->user->name }}
                                    @else
                                        <span class="text-muted">Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ number_format($usage->tokens_used, 0) }}</span>
                                </td>
                                <td>{{ $usage->model }}</td>
                                <td>{{ ucfirst($usage->purpose) }}</td>
                                <td>{{ Str::limit($usage->description ?? '-', 50) }}</td>
                                <td>{{ $usage->used_at ? $usage->used_at->format('d.m.Y H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $usageRecords->links() }}
                </div>
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-chart-bar text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz kullanım verisi yok</p>
                    <p class="empty-subtitle text-muted">
                        Henüz hiçbir AI token kullanımı gerçekleşmemiş.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Usage Chart
    const dailyUsageData = @json($stats['daily_usage'] ?? []);
    const ctx = document.getElementById('dailyUsageChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(dailyUsageData),
            datasets: [{
                label: 'Günlük Kredi Kullanımı',
                data: Object.values(dailyUsageData),
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