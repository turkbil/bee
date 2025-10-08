@section('page-title', __('tenantmanagement::admin.health_check'))
@section('page-description', __('tenantmanagement::admin.health_check_description'))

@push('styles')
<!-- Chart.js CSS -->
<style>
.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
.metric-chart {
    max-height: 250px;
}
.alert-badge {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>
@endpush

<div>
    @include('tenantmanagement::helper')
    
    <!-- Sistem Metrikleri Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        {{ __('tenantmanagement::admin.system_metrics') }}
                    </h3>
                    <button wire:click="refreshMetrics" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="refreshMetrics">
                            <i class="fas fa-sync-alt me-1"></i>{{ __('tenantmanagement::admin.refresh') }}
                        </span>
                        <span wire:loading wire:target="refreshMetrics">
                            <i class="fas fa-spinner fa-spin me-1"></i>Yükleniyor...
                        </span>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Tenant İstatistikleri -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-building" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1">{{ $systemMetrics['total_tenants'] ?? 0 }}</h3>
                                    <p class="text-muted mb-0">Toplam Tenant</p>
                                    <small class="text-success">{{ $systemMetrics['active_tenants'] ?? 0 }} aktif</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bellek Kullanımı -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-memory" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1">{{ $systemMetrics['memory_usage']['percentage'] ?? 0 }}%</h3>
                                    <p class="text-muted mb-0">Bellek Kullanımı</p>
                                    <small class="text-muted">{{ $systemMetrics['memory_usage']['used'] ?? 0 }}MB / {{ $systemMetrics['memory_usage']['total'] ?? 0 }}MB</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CPU Kullanımı -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="text-danger mb-2">
                                        <i class="fas fa-microchip" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1">{{ $systemMetrics['cpu_usage'] ?? 0 }}%</h3>
                                    <p class="text-muted mb-0">CPU Kullanımı</p>
                                    <small class="text-muted">Load Average</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Disk Kullanımı -->
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-hdd" style="font-size: 2rem;"></i>
                                    </div>
                                    <h3 class="mb-1">{{ $systemMetrics['disk_usage']['percentage'] ?? 0 }}%</h3>
                                    <p class="text-muted mb-0">Disk Kullanımı</p>
                                    <small class="text-muted">{{ $systemMetrics['disk_usage']['used'] ?? 0 }}GB / {{ $systemMetrics['disk_usage']['total'] ?? 0 }}GB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bağlantı İstatistikleri -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="text-primary me-3">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div>
                                    <strong>{{ $systemMetrics['database_connections'] ?? 0 }}</strong>
                                    <p class="text-muted mb-0">Database Bağlantıları</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="text-danger me-3">
                                    <i class="fa-brands fa-redis"></i>
                                </div>
                                <div>
                                    <strong>{{ $systemMetrics['redis_connections'] ?? 0 }}</strong>
                                    <p class="text-muted mb-0">Redis Bağlantıları</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="text-success me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <strong>{{ $systemMetrics['uptime'] ?? 'Bilinmiyor' }}</strong>
                                    <p class="text-muted mb-0">Sistem Çalışma Süresi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tenant Sağlık Durumu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heartbeat"></i>
                        {{ __('tenantmanagement::admin.tenant_health_status') }}
                    </h3>
                    <div class="card-actions">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" wire:model.live="search" class="form-control" 
                                   placeholder="{{ __('tenantmanagement::admin.search_tenant') }}">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($healthStatus) > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>{{ __('tenantmanagement::admin.tenant_name') }}</th>
                                        <th>{{ __('tenantmanagement::admin.health_score') }}</th>
                                        <th>{{ __('tenantmanagement::admin.status') }}</th>
                                        <th>{{ __('tenantmanagement::admin.memory_usage') }}</th>
                                        <th>{{ __('tenantmanagement::admin.cpu_usage') }}</th>
                                        <th>{{ __('tenantmanagement::admin.connections') }}</th>
                                        <th>{{ __('tenantmanagement::admin.issues') }}</th>
                                        <th>{{ __('tenantmanagement::admin.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($healthStatus as $tenantId => $health)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $health['tenant_name'] ?? 'Tenant #' . $health['tenant_id'] }}</strong>
                                                    <div class="small text-muted">ID: {{ $health['tenant_id'] }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                                        <div class="progress-bar 
                                                            @if($health['score'] >= 80) bg-success 
                                                            @elseif($health['score'] >= 60) bg-warning 
                                                            @else bg-danger @endif" 
                                                            style="width: {{ $health['score'] }}%"></div>
                                                    </div>
                                                    <span class="text-muted">{{ $health['score'] }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($health['status'] == 'healthy')
                                                    <span class="badge bg-success text-success bg-opacity-10">
                                                        <i class="fas fa-check"></i> Sağlıklı
                                                    </span>
                                                @elseif($health['status'] == 'warning')
                                                    <span class="badge bg-warning text-warning bg-opacity-10">
                                                        <i class="fas fa-exclamation-triangle"></i> Uyarı
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger text-danger bg-opacity-10">
                                                        <i class="fas fa-times"></i> Kritik
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="@if($health['metrics']['memory_usage_percentage'] > 80) text-danger @elseif($health['metrics']['memory_usage_percentage'] > 60) text-warning @else text-success @endif">
                                                    {{ $health['metrics']['memory_usage_percentage'] ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="@if($health['metrics']['cpu_usage_percentage'] > 80) text-danger @elseif($health['metrics']['cpu_usage_percentage'] > 60) text-warning @else text-success @endif">
                                                    {{ $health['metrics']['cpu_usage_percentage'] ?? 0 }}%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="@if($health['metrics']['connection_count'] > 40) text-danger @elseif($health['metrics']['connection_count'] > 30) text-warning @else text-success @endif">
                                                    {{ $health['metrics']['connection_count'] ?? 0 }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(count($health['issues']) > 0)
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-danger dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            {{ count($health['issues']) }} Sorun
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @foreach($health['issues'] as $issue)
                                                                <div class="dropdown-item text-wrap" style="max-width: 200px;">
                                                                    <i class="fas fa-exclamation-circle text-danger"></i>
                                                                    {{ $issue }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-success">
                                                        <i class="fas fa-check"></i> Sorun Yok
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button wire:click="selectTenant('{{ $tenantId }}')" 
                                                        class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detay
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-search" style="font-size: 3rem;"></i>
                                <h3>Tenant bulunamadı</h3>
                                <p>Arama kriterlerinize uygun tenant bulunamadı.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detaylı Metrik Modal -->
    @if($selectedTenant && count($selectedMetrics) > 0)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-chart-line"></i>
                            Tenant #{{ $selectedTenant }} - Gerçek Zamanlı Metrikler
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('selectedTenant', null)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>{{ $selectedMetrics['memory_usage_percentage'] ?? 0 }}%</h4>
                                        <p class="text-muted">Bellek Kullanımı</p>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: {{ $selectedMetrics['memory_usage_percentage'] ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>{{ $selectedMetrics['cpu_usage_percentage'] ?? 0 }}%</h4>
                                        <p class="text-muted">CPU Kullanımı</p>
                                        <div class="progress">
                                            <div class="progress-bar bg-danger" style="width: {{ $selectedMetrics['cpu_usage_percentage'] ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>{{ $selectedMetrics['connection_count'] ?? 0 }}</h4>
                                        <p class="text-muted">Aktif Bağlantılar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>{{ $selectedMetrics['api_requests_per_hour'] ?? 0 }}</h4>
                                        <p class="text-muted">Saatlik API İstekleri</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4>{{ $selectedMetrics['storage_usage_mb'] ?? 0 }}MB</h4>
                                        <p class="text-muted">Depolama Kullanımı</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('selectedTenant', null)">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
    
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh her 30 saniyede bir
    setInterval(function() {
        if (typeof Livewire !== 'undefined') {
            @this.call('refreshMetrics');
        }
    }, 30000);
    
    // Initialize Performance Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['10 dk önce', '8 dk önce', '6 dk önce', '4 dk önce', '2 dk önce', 'Şimdi'],
                datasets: [{
                    label: 'Memory Usage %',
                    data: [{{ $systemMetrics['memory_usage']['percentage'] ?? 0 }}, 45, 52, 48, 55, {{ $systemMetrics['memory_usage']['percentage'] ?? 0 }}],
                    borderColor: 'rgb(255, 193, 7)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'CPU Usage %',
                    data: [{{ $systemMetrics['cpu_usage'] ?? 0 }}, 35, 42, 38, 45, {{ $systemMetrics['cpu_usage'] ?? 0 }}],
                    borderColor: 'rgb(220, 53, 69)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Initialize Resource Chart
    const resourceCtx = document.getElementById('resourceChart');
    if (resourceCtx) {
        const resourceChart = new Chart(resourceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kullanılan', 'Boş'],
                datasets: [{
                    label: 'Memory',
                    data: [{{ $systemMetrics['memory_usage']['percentage'] ?? 0 }}, {{ 100 - ($systemMetrics['memory_usage']['percentage'] ?? 0) }}],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(108, 117, 125, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 193, 7)',
                        'rgb(108, 117, 125)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Memory Usage: {{ $systemMetrics['memory_usage']['used'] ?? 0 }}MB / {{ $systemMetrics['memory_usage']['total'] ?? 0 }}MB'
                    }
                }
            }
        });
    }
    
    // Livewire refresh chart update
    Livewire.on('metricsUpdated', function(metrics) {
        if (typeof performanceChart !== 'undefined') {
            // Update chart data
            performanceChart.data.datasets[0].data.push(metrics.memory_percentage);
            performanceChart.data.datasets[1].data.push(metrics.cpu_percentage);
            
            if (performanceChart.data.datasets[0].data.length > 6) {
                performanceChart.data.datasets[0].data.shift();
                performanceChart.data.datasets[1].data.shift();
            }
            
            performanceChart.update();
        }
        
        if (typeof resourceChart !== 'undefined') {
            resourceChart.data.datasets[0].data = [metrics.memory_percentage, 100 - metrics.memory_percentage];
            resourceChart.update();
        }
    });
});
</script>
@endpush