@include('tenantmanagement::helper')

<div class="container-fluid" wire:poll.{{ $refreshInterval }}s="loadQueueData">
    <div class="row">
        <!-- Queue Health Score Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h3 class="m-0 text-{{ $this->getHealthScoreColor() }}">{{ $healthScore['score'] ?? 0 }}</h3>
                            <p class="text-muted mb-0">Sistem Sağlık Skoru</p>
                            <small class="text-{{ $this->getHealthScoreColor() }}">{{ $healthScore['status'] ?? 'Bilinmiyor' }}</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="fas fa-heartbeat fa-2x text-{{ $this->getHealthScoreColor() }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="m-0 text-warning">{{ $queueData['overview']['total_pending'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Bekleyen İşler</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="m-0 text-info">{{ $queueData['overview']['total_processing'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">İşlenmekte</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-cog fa-spin fa-2x text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="m-0 text-danger">{{ $queueData['overview']['total_failed'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Başarısız</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="m-0 text-success">{{ $queueData['overview']['total_completed_today'] ?? 0 }}</h4>
                                    <p class="text-muted mb-0">Bugün Tamamlanan</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="row mt-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert {{ $this->getAlertTypeClass($alert['type']) }} alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>{{ $alert['title'] }}</strong>
                {{ $alert['message'] }}
                @if(isset($alert['action']))
                <br><small><strong>Önerilen Aksiyon:</strong> {{ $alert['action'] }}</small>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Control Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Queue Yönetim Kontrolleri</h4>
                </div>
                <div class="card-body">
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-primary" wire:click="refreshData">
                            <i class="fas fa-sync-alt"></i> Verileri Yenile
                        </button>
                        <button type="button" class="btn btn-{{ $autoRefresh ? 'success' : 'secondary' }}" 
                                wire:click="toggleAutoRefresh">
                            <i class="fas fa-{{ $autoRefresh ? 'pause' : 'play' }}"></i> 
                            Otomatik Yenileme {{ $autoRefresh ? 'Aktif' : 'Pasif' }}
                        </button>
                    </div>
                    
                    <div class="btn-group me-2" role="group">
                        <button type="button" class="btn btn-warning" wire:click="restartFailedJobs">
                            <i class="fas fa-redo-alt"></i> Başarısız İşleri Yeniden Başlat
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="clearFailedJobs" 
                                onclick="return confirm('Tüm başarısız işleri silmek istediğinizden emin misiniz?')">
                            <i class="fas fa-trash-alt"></i> Başarısız İşleri Temizle
                        </button>
                    </div>
                    
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-info" wire:click="restartWorkers">
                            <i class="fas fa-power-off"></i> Worker'ları Yeniden Başlat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row mt-4">
        <div class="col-12">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link {{ $selectedQueue === 'overview' ? 'active' : '' }}" 
                            wire:click="selectQueue('overview')">
                        <i class="fas fa-tachometer-alt me-1"></i> Genel Bakış
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $selectedQueue === 'workers' ? 'active' : '' }}" 
                            wire:click="selectQueue('workers')">
                        <i class="fas fa-users me-1"></i> Worker'lar
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $selectedQueue === 'queues' ? 'active' : '' }}" 
                            wire:click="selectQueue('queues')">
                        <i class="fas fa-list me-1"></i> Kuyruk Detayları
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $selectedQueue === 'performance' ? 'active' : '' }}" 
                            wire:click="selectQueue('performance')">
                        <i class="fas fa-chart-line me-1"></i> Performans
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $selectedQueue === 'system' ? 'active' : '' }}" 
                            wire:click="selectQueue('system')">
                        <i class="fas fa-server me-1"></i> Sistem Kaynakları
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- Overview Tab -->
                @if($selectedQueue === 'overview')
                <div class="tab-pane fade show active">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Kuyruk İstatistikleri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-primary">{{ $queueData['overview']['queue_throughput'] ?? 0 }}</h4>
                                                <small class="text-muted">İş/Dakika</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $queueData['overview']['average_wait_time'] ?? 0 }}</h4>
                                                <small class="text-muted">Ortalama Bekleme</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Son Aktivite</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Son İşlenen İş:</strong>
                                        {{ $queueData['overview']['last_job_processed'] ? $queueData['overview']['last_job_processed']->diffForHumans() : 'Bilinmiyor' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Workers Tab -->
                @if($selectedQueue === 'workers')
                <div class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Docker Container Worker'ları</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Worker</th>
                                            <th>Durum</th>
                                            <th>Çalışma Süresi</th>
                                            <th>Bellek</th>
                                            <th>CPU</th>
                                            <th>Yeniden Başlatma</th>
                                            <th>Sağlık</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($queueData['workers'] ?? [] as $key => $worker)
                                        <tr>
                                            <td><strong>{{ $worker['name'] }}</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $worker['status'] === 'çalışıyor' ? 'success' : 'danger' }}">
                                                    {{ $worker['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ $worker['uptime'] }}</td>
                                            <td>{{ $worker['memory_usage'] }}</td>
                                            <td>{{ $worker['cpu_usage'] }}</td>
                                            <td>
                                                <small>
                                                    {{ $worker['restart_count'] }} kez<br>
                                                    @if($worker['last_restart'])
                                                    <span class="text-muted">{{ $worker['last_restart']->diffForHumans() }}</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $worker['health_check'] === 'sağlıklı' ? 'success' : 'warning' }}">
                                                    {{ $worker['health_check'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Worker bilgisi yüklenemedi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Queues Tab -->
                @if($selectedQueue === 'queues')
                <div class="tab-pane fade show active">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Kuyruk Türleri ve Detayları</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Kuyruk</th>
                                            <th>Bekleyen</th>
                                            <th>İşleniyor</th>
                                            <th>Bugün Başarısız</th>
                                            <th>Ort. Süre</th>
                                            <th>Son İş</th>
                                            <th>Worker</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($queueData['queues'] ?? [] as $key => $queue)
                                        <tr>
                                            <td><strong>{{ $queue['name'] }}</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $queue['pending'] > 100 ? 'warning' : 'info' }}">
                                                    {{ $queue['pending'] }}
                                                </span>
                                            </td>
                                            <td>{{ $queue['processing'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $queue['failed_today'] > 10 ? 'danger' : 'secondary' }}">
                                                    {{ $queue['failed_today'] }}
                                                </span>
                                            </td>
                                            <td>{{ $queue['avg_processing_time'] }}</td>
                                            <td>
                                                @if($queue['last_job'])
                                                <small>{{ $queue['last_job']->diffForHumans() }}</small>
                                                @else
                                                <small class="text-muted">-</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $queue['worker_assignment'] }}</small>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Kuyruk bilgisi yüklenemedi</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Performance Tab -->
                @if($selectedQueue === 'performance')
                <div class="tab-pane fade show active">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">İş Verimlilik Metrikleri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary">{{ $queueData['performance']['jobs_per_minute'] ?? 0 }}</h4>
                                            <small class="text-muted">İş/Dakika</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-info">{{ $queueData['performance']['jobs_per_hour'] ?? 0 }}</h4>
                                            <small class="text-muted">İş/Saat</small>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h5 class="text-success">{{ $queueData['performance']['queue_efficiency'] ?? '0%' }}</h5>
                                            <small class="text-muted">Verimlilik</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-warning">{{ $queueData['performance']['error_rate'] ?? 0 }}%</h5>
                                            <small class="text-muted">Hata Oranı</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Sistem Durumu</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Yoğun Saatler:</strong>
                                        <div class="mt-1">
                                            @foreach($queueData['performance']['peak_processing_time'] ?? [] as $time)
                                            <span class="badge bg-info me-1">{{ $time }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <strong>Bellek Kullanım Trendi:</strong>
                                        <span class="badge bg-info">{{ $queueData['performance']['memory_usage_trend'] ?? 'Bilinmiyor' }}</span>
                                    </div>
                                    <div>
                                        <strong>Yeniden Deneme Oranı:</strong>
                                        <span class="badge bg-warning">{{ $queueData['performance']['retry_rate'] ?? '0%' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- System Resources Tab -->
                @if($selectedQueue === 'system')
                <div class="tab-pane fade show active">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Redis Kaynakları</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="text-info">{{ $queueData['system_resources']['redis_memory'] ?? '0 MB' }}</h5>
                                            <small class="text-muted">Bellek Kullanımı</small>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="text-primary">{{ $queueData['system_resources']['redis_connections'] ?? 0 }}</h5>
                                            <small class="text-muted">Aktif Bağlantı</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Sistem Kaynakları</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Veritabanı Bağlantıları:</strong>
                                        <span class="badge bg-info">{{ $queueData['system_resources']['database_connections'] ?? 0 }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Disk Kullanımı:</strong>
                                        <span class="badge bg-{{ (int)str_replace('%', '', $queueData['system_resources']['disk_usage'] ?? '0%') > 80 ? 'danger' : 'success' }}">
                                            {{ $queueData['system_resources']['disk_usage'] ?? '0%' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Yük Ortalaması:</strong>
                                        <span class="badge bg-info">{{ $queueData['system_resources']['load_average'] ?? '0.00' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading.delay class="position-fixed top-50 start-50 translate-middle">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh logic
        let autoRefreshInterval;
        
        function startAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
            
            autoRefreshInterval = setInterval(function() {
                if (@json($autoRefresh)) {
                    @this.call('loadQueueData');
                }
            }, @json($refreshInterval) * 1000);
        }
        
        startAutoRefresh();
        
        // Listen for Livewire updates
        Livewire.on('queueDataUpdated', function(data) {
            console.log('Queue data updated:', data);
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        });
    });
</script>
@endpush