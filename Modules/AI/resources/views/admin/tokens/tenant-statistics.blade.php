@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', $tenant->title . ' - Detaylı İstatistikler')

@section('content')
<!-- Tenant Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar avatar-rounded" style="background-color: {{ $tenant->ai_enabled ? '#28a745' : '#dc3545' }}">
                            <i class="fas fa-{{ $tenant->ai_enabled ? 'check' : 'times' }} text-white"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="mb-0">{{ $tenant->title }}</h2>
                        <div class="text-muted">
                            <span class="badge badge-outline {{ $tenant->ai_enabled ? 'text-green' : 'text-red' }}">
                                {{ $tenant->ai_enabled ? 'AI Aktif' : 'AI Pasif' }}
                            </span>
                            <span class="mx-2">•</span>
                            <span>Kiracı ID: {{ $tenant->id }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="btn-list">
                            <a href="{{ route('admin.ai.tokens.show', $tenant->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>
                                Kiracı Detayı
                            </a>
                            <a href="{{ route('admin.ai.tokens.usage-stats', ['tenant_id' => $tenant->id]) }}" class="btn btn-outline-info">
                                <i class="fas fa-list me-2"></i>
                                Kullanım Kayıtları
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Mevcut Bakiye</div>
                </div>
                <div class="h1 mb-3 text-green">{{ ai_format_token_count($stats['real_balance']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Kullanım</div>
                </div>
                <div class="h1 mb-3">{{ ai_format_token_count($stats['total_usage']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Satın Alınan</div>
                </div>
                <div class="h1 mb-3">{{ ai_format_token_count($stats['total_purchases']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bu Ay</div>
                </div>
                <div class="h1 mb-3">{{ ai_format_token_count($stats['monthly_usage']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bu Hafta</div>
                </div>
                <div class="h1 mb-3">{{ ai_format_token_count($stats['weekly_usage']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-2">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Bugün</div>
                </div>
                <div class="h1 mb-3">{{ ai_format_token_count($stats['today_usage']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
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
    
    <!-- Model Usage -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Model Kullanımı</h3>
            </div>
            <div class="card-body">
                @if($modelUsage->count() > 0)
                    @foreach($modelUsage as $model)
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <span class="badge badge-outline">{{ $model->model }}</span>
                        </div>
                        <div class="col">
                            <div class="progress progress-sm">
                                <div class="progress-bar" style="width: {{ $modelUsage->first()->total > 0 ? ($model->total / $modelUsage->first()->total) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <span class="text-muted">{{ ai_format_token_count($model->total) }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">Henüz model kullanımı yok</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Usage -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Son Kullanım Kayıtları</h3>
            </div>
            <div class="card-body">
                @if($recentUsage->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kullanıcı</th>
                                <th>Token</th>
                                <th>Model</th>
                                <th>Amaç</th>
                                <th>Açıklama</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsage as $usage)
                            <tr>
                                <td>
                                    @if($usage->user)
                                        {{ $usage->user->name }}
                                    @else
                                        <span class="text-muted">Sistem</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ ai_format_token_count($usage->tokens_used) }}</span>
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
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-chart-bar text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz kullanım verisi yok</p>
                    <p class="empty-subtitle text-muted">
                        Bu kiracı henüz AI token kullanımı gerçekleştirmemiş.
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
    const dailyUsageData = @json($formattedDailyUsage);
    const ctx = document.getElementById('dailyUsageChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(dailyUsageData),
            datasets: [{
                label: 'Günlük Token Kullanımı',
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