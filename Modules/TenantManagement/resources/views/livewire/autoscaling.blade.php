<div class="page-wrapper">
    @include('tenantmanagement::helper')
    
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fas fa-sliders-h me-2"></i>
                        {{ __('tenantmanagement::admin.auto_scaling') }}
                    </h2>
                    <div class="text-secondary mt-1">
                        Otomatik kaynak ölçeklendirme yönetimi
                    </div>
                </div>
                
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button type="button" class="btn btn-outline-{{ $autoScalingEnabled ? 'success' : 'secondary' }}" 
                                wire:click="toggleAutoScaling">
                            <i class="fas fa-{{ $autoScalingEnabled ? 'toggle-right' : 'toggle-left' }} me-1"></i>
                            Auto-Scaling {{ $autoScalingEnabled ? 'Açık' : 'Kapalı' }}
                        </button>
                        
                        <button type="button" class="btn btn-outline-{{ $autoRefresh ? 'info' : 'secondary' }}" 
                                wire:click="toggleAutoRefresh">
                            <i class="fas fa-refresh me-1"></i>
                            {{ $autoRefresh ? 'Auto Refresh' : 'Manual' }}
                        </button>
                        
                        <button type="button" class="btn btn-primary" wire:click="exportScalingReport">
                            <i class="fas fa-file-export me-1"></i>
                            Rapor İndir
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" wire:click="refreshData">
                            <i class="fas fa-refresh me-1"></i>
                            Yenile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="page-body">
        <div class="container-xl">
            
            <!-- Scaling İstatistikleri -->
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Aktif Tenant</div>
                                <div class="ms-auto">
                                    <i class="fas fa-users text-primary"></i>
                                </div>
                            </div>
                            <div class="h1 mb-3">{{ $scalingStats['enabled_tenants'] ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>Auto-scaling etkin</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Günlük Ortalama</div>
                                <div class="ms-auto">
                                    <i class="fas fa-activity text-success"></i>
                                </div>
                            </div>
                            <div class="h1 mb-3">{{ $scalingStats['avg_scaling_frequency'] ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>scaling işlemi</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Verimlilik</div>
                                <div class="ms-auto">
                                    <i class="fas fa-chart-line text-warning"></i>
                                </div>
                            </div>
                            <div class="h1 mb-3">{{ $scalingStats['efficiency_rating'] ?? 0 }}%</div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ $scalingStats['efficiency_rating'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Aylık Tasarruf</div>
                                <div class="ms-auto">
                                    <i class="fas fa-currency-dollar text-info"></i>
                                </div>
                            </div>
                            <div class="h1 mb-3">${{ $scalingStats['cost_savings_estimate']['monthly_usd'] ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>{{ $scalingStats['cost_savings_estimate']['resource_optimization'] ?? '0%' }} optimizasyon</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kaynak Kullanımı -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-server me-2"></i>
                                Sistem Kaynak Kullanımı
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-outline-{{ $globalScalingEnabled ? 'success' : 'secondary' }} btn-sm" 
                                        wire:click="toggleGlobalScaling">
                                    <i class="fas fa-globe me-1"></i>
                                    Global Scaling {{ $globalScalingEnabled ? 'Açık' : 'Kapalı' }}
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($scalingStats['resource_utilization'] ?? [] as $resource => $usage)
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="subheader">{{ ucfirst($resource) }}</div>
                                                <div class="ms-auto">
                                                    {{ round($usage, 1) }}%
                                                </div>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-{{ $resourceColors[$resource] ?? 'primary' }}" 
                                                     style="width: {{ $usage }}%"></div>
                                            </div>
                                            <div class="small text-secondary mt-1">
                                                @if($usage > 85)
                                                    <i class="fas fa-alert-triangle text-warning me-1"></i>
                                                    Kritik seviye
                                                @elseif($usage > 70)
                                                    <i class="fas fa-info-circle text-info me-1"></i>
                                                    Yüksek kullanım
                                                @else
                                                    <i class="fas fa-check text-success me-1"></i>
                                                    Normal
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ana İçerik -->
            <div class="row row-cards">
                
                <!-- Scaling Aksiyonları -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktif Scaling Aksiyonları</h3>
                            
                            <div class="card-actions">
                                <!-- Filtreler -->
                                <div class="d-flex gap-2">
                                    <select class="form-select form-select-sm" wire:model.live="statusFilter">
                                        <option value="all">Tüm Durumlar</option>
                                        <option value="active">Scale Up</option>
                                        <option value="inactive">Scale Down</option>
                                    </select>
                                    
                                    <select class="form-select form-select-sm" wire:model.live="resourceFilter">
                                        <option value="all">Tüm Kaynaklar</option>
                                        @foreach($availableResources as $resource => $label)
                                        <option value="{{ $resource }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select class="form-select form-select-sm" wire:model.live="priorityFilter">
                                        <option value="all">Tüm Öncelikler</option>
                                        @foreach($priorityLevels as $priority => $config)
                                        <option value="{{ $priority }}">{{ $config['label'] }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <input type="text" class="form-control form-control-sm" 
                                           placeholder="Ara..." wire:model.live="search">
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Kaynak</th>
                                        <th>Tenant</th>
                                        <th>Aksiyon</th>
                                        <th>Öncelik</th>
                                        <th>Kullanım</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $actionCount = 0; @endphp
                                    @forelse($filteredActions as $key => $actions)
                                        @foreach($actions as $index => $action)
                                        @php $actionCount++; @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-server me-2 text-{{ $resourceColors[$action['resource']] ?? 'primary' }}"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $availableResources[$action['resource']] ?? $action['resource'] }}</div>
                                                        <div class="text-secondary small">{{ $action['resource'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($key === 'global')
                                                    Global
                                                @else
                                                    <div class="fw-bold">#{{ $key }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-{{ $action['type'] === 'scale_up' ? 'arrow-up text-success' : 'arrow-down text-warning' }} me-2"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $action['type'] === 'scale_up' ? 'Scale Up' : 'Scale Down' }}</div>
                                                        <div class="text-secondary small">{{ $action['scale_amount'] ?? 'N/A' }} birim</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $priorityLevels[$action['priority'] ?? 'medium']['label'] }}
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div class="fw-bold">{{ round($action['current_usage'] ?? 0, 1) }}%</div>
                                                    <div class="progress progress-sm">
                                                        <div class="progress-bar bg-{{ $action['current_usage'] > 80 ? 'danger' : ($action['current_usage'] > 60 ? 'warning' : 'success') }}" 
                                                             style="width: {{ min(100, $action['current_usage'] ?? 0) }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            wire:click="executeScalingAction({{ $index }}, {{ $key === 'global' ? 'null' : $key }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="executeScalingAction">
                                                        <i class="fas fa-play me-1"></i>
                                                        Uygula
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary py-4">
                                            <i class="fas fa-adjustments-off mb-2" style="font-size: 2rem;"></i>
                                            <div>Aktif scaling aksiyonu bulunamadı</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($actionCount > 0)
                        <div class="card-footer text-secondary">
                            Toplam {{ $actionCount }} aksiyon mevcut
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Yan Panel - Scaling Kuralları -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Scaling Kuralları</h3>
                        </div>
                        <div class="card-body">
                            @foreach($scalingRules as $resource => $rule)
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <strong class="text-{{ $resourceColors[$resource] ?? 'primary' }}">
                                        {{ $availableResources[$resource] ?? $resource }}
                                    </strong>
                                    <div class="ms-auto">
                                        {{ $rule['enabled'] ? 'Aktif' : 'Pasif' }}
                                    </div>
                                </div>
                                <div class="small text-secondary">
                                    <div class="row">
                                        <div class="col-6">
                                            <div>Scale Up: %{{ $rule['scale_up_threshold'] }}</div>
                                            <div>Scale Down: %{{ $rule['scale_down_threshold'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <div>Max Scale: {{ $rule['max_scale_up'] }}</div>
                                            <div>Cooldown: {{ $rule['cooldown_minutes'] }}dk</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Aktif Scaling İşlemleri -->
                    @if(!empty($activeScalings))
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Devam Eden İşlemler</h3>
                        </div>
                        <div class="card-body">
                            @foreach($activeScalings as $scaling)
                            <div class="card card-sm mb-2">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong>Tenant #{{ $scaling['tenant_id'] }}</strong>
                                        <div class="ms-auto">
                                            {{ $scaling['progress'] }}%
                                        </div>
                                    </div>
                                    <div class="text-sm text-secondary mb-2">
                                        {{ $availableResources[$scaling['resource']] ?? $scaling['resource'] }}
                                        - {{ $scaling['type'] === 'scale_up' ? 'Scale Up' : 'Scale Down' }}
                                    </div>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" style="width: {{ $scaling['progress'] }}%"></div>
                                    </div>
                                    <div class="text-xs text-secondary mt-1">
                                        Başlangıç: {{ \Carbon\Carbon::parse($scaling['started_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scaling Detay Modal -->
    @if($showScalingModal && $selectedTenant)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-sliders-h me-2"></i>
                        {{ $selectedTenant->title }} - Scaling Geçmişi
                    </h5>
                    <button type="button" class="btn-close" wire:click="clearTenantSelection"></button>
                </div>
                
                <div class="modal-body">
                    @if(!empty($scalingHistory))
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card card-sm text-center">
                                <div class="card-body">
                                    <div class="h3 text-primary">{{ $scalingHistory['total_actions'] ?? 0 }}</div>
                                    <div class="text-secondary">Toplam</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm text-center">
                                <div class="card-body">
                                    <div class="h3 text-success">{{ $scalingHistory['scale_up_count'] ?? 0 }}</div>
                                    <div class="text-secondary">Scale Up</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm text-center">
                                <div class="card-body">
                                    <div class="h3 text-warning">{{ $scalingHistory['scale_down_count'] ?? 0 }}</div>
                                    <div class="text-secondary">Scale Down</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm text-center">
                                <div class="card-body">
                                    <div class="h4 text-info">{{ $scalingHistory['most_scaled_resource'] ?? 'N/A' }}</div>
                                    <div class="text-secondary">En Çok</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(!empty($scalingHistory['recent_actions']))
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Son İşlemler</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table">
                                <thead>
                                    <tr>
                                        <th>Zaman</th>
                                        <th>Kaynak</th>
                                        <th>Aksiyon</th>
                                        <th>Miktar</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scalingHistory['recent_actions'] as $action)
                                    <tr>
                                        <td>
                                            <div class="text-secondary small">
                                                {{ \Carbon\Carbon::parse($action['timestamp'])->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td>{{ $availableResources[$action['resource']] ?? $action['resource'] }}</td>
                                        <td>
                                            {{ $action['type'] === 'scale_up' ? 'Scale Up' : 'Scale Down' }}
                                        </td>
                                        <td>{{ $action['amount'] }}</td>
                                        <td>
                                            {{ $action['success'] ? 'Başarılı' : 'Hatalı' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="alert alert-info">
                        Bu tenant için henüz scaling geçmişi bulunmuyor.
                    </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="clearTenantSelection">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', function () {
    // Auto refresh mekanizması
    let refreshInterval;
    
    Livewire.on('startAutoRefresh', () => {
        refreshInterval = setInterval(() => {
            @this.call('loadData');
        }, 15000); // 15 saniye
    });
    
    Livewire.on('stopAutoRefresh', () => {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
    
    // Toast notifications
    Livewire.on('toast', (event) => {
        // Toast event received
    });
    
    // İlk yüklemede auto refresh başlat
    if (@js($autoRefresh)) {
        setTimeout(() => {
            Livewire.dispatch('startAutoRefresh');
        }, 1000);
    }
});
</script>