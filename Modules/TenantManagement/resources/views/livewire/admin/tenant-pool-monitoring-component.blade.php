@include('tenantmanagement::helper')

@section('title', __('tenantmanagement::admin.pool_monitoring'))

<div x-data="{ 
        autoRefresh: @entangle('isAutoRefresh'), 
        refreshInterval: @entangle('refreshInterval'),
        intervalId: null,
        
        startRefresh() {
            this.stopRefresh();
            if (this.autoRefresh && this.refreshInterval > 0) {
                this.intervalId = setInterval(() => {
                    if (this.autoRefresh) {
                        $wire.loadPoolStats();
                    }
                }, this.refreshInterval * 1000);
            }
        },
        
        stopRefresh() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },
        
        updateInterval() {
            if (this.autoRefresh) {
                this.startRefresh();
            }
        }
    }" 
    x-init="$watch('autoRefresh', () => autoRefresh ? startRefresh() : stopRefresh()); $watch('refreshInterval', () => updateInterval());"
    x-on:beforeunload.window="stopRefresh()">

    <!-- Header Bölümü -->
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="fas fa-database me-2"></i>
                            {{ __('tenantmanagement::admin.database_pool_monitoring') }}
                        </h2>
                        <div class="text-secondary mt-1">
                            {{ __('tenantmanagement::admin.pool_monitoring') }}
                        </div>
                    </div>
                    
                    <div class="col-auto ms-auto d-print-none">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-primary" wire:click="toggleAutoRefresh">
                                <i class="fas fa-{{ $isAutoRefresh ? 'pause' : 'play' }} me-2"></i>
                                {{ $isAutoRefresh ? __('tenantmanagement::admin.auto_refresh_on') : __('tenantmanagement::admin.auto_refresh_off') }}
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="refresh">
                                <i class="fas fa-sync-alt me-2"></i>
                                {{ __('tenantmanagement::admin.refresh') }}
                            </button>
                            <button type="button" class="btn btn-warning" wire:click="cleanupIdleConnections">
                                <i class="fas fa-broom me-2"></i>
                                {{ __('tenantmanagement::admin.cleanup_idle') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="page-body">
            <div class="container-xl">

                <!-- System Health Alert -->
                @php
                    $healthData = $this->getSystemHealth();
                @endphp
                
                @if($healthData && $healthData['status'] !== 'healthy')
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-{{ $healthData['color'] }} alert-dismissible">
                            <h4><i class="icon fas fa-{{ $healthData['status'] === 'critical' ? 'exclamation-triangle' : 'exclamation-circle' }}"></i> 
                                {{ __('tenantmanagement::admin.system_health_' . $healthData['status']) }}
                            </h4>
                            {{ $healthData['message'] }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Pool Overview Cards -->
                @if(!empty($poolStats))
                @php
                    $metrics = $this->getPerformanceMetrics();
                @endphp
                
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fas fa-database text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                            <p class="card-category">{{ __('tenantmanagement::admin.total_pools') }}</p>
                                            <p class="card-title">{{ $poolStats['total_pools'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fas fa-link text-success"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                            <p class="card-category">{{ __('tenantmanagement::admin.active_connections') }}</p>
                                            <p class="card-title">{{ $poolStats['total_active_connections'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fas fa-pause text-warning"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                            <p class="card-category">{{ __('tenantmanagement::admin.idle_connections') }}</p>
                                            <p class="card-title">{{ $poolStats['total_idle_connections'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-stats">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-5 col-md-4">
                                        <div class="icon-big text-center icon-warning">
                                            <i class="fas fa-chart-line text-info"></i>
                                        </div>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        <div class="numbers">
                                            <p class="card-category">{{ __('tenantmanagement::admin.utilization') }}</p>
                                            <p class="card-title">%{{ $metrics['connection_utilization'] ?? 0 }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Refresh Controls -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        {{ __('tenantmanagement::admin.auto_refresh') }}: {{ $isAutoRefresh ? __('tenantmanagement::admin.on') : __('tenantmanagement::admin.off') }}
                                        @if($isAutoRefresh)
                                        ({{ $refreshInterval }}s)
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="btn-group btn-group-sm float-end" role="group">
                                            @foreach([1, 3, 5, 10, 30] as $interval)
                                            <button wire:click="updateRefreshInterval({{ $interval }})" 
                                                    class="btn {{ $refreshInterval == $interval ? 'btn-primary' : 'btn-outline-primary' }} btn-sm">
                                                {{ $interval }}s
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tenant Pool Details Table -->
                @php
                    $tenantDetails = $this->getTenantPoolDetails();
                @endphp
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('tenantmanagement::admin.tenant_pool_details') }}</h3>
                                <div class="card-tools">
                                    {{ __('tenantmanagement::admin.last_update') }}: {{ $poolStats['updated_at'] ? \Carbon\Carbon::parse($poolStats['updated_at'])->format('H:i:s') : '-' }}
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if(!empty($tenantDetails))
                                <div class="table-responsive">
                                    <table class="table table-striped table-valign-middle">
                                        <thead>
                                            <tr>
                                                <th>{{ __('tenantmanagement::admin.tenant') }}</th>
                                                <th>{{ __('tenantmanagement::admin.active_connections') }}</th>
                                                <th>{{ __('tenantmanagement::admin.idle_connections') }}</th>
                                                <th>{{ __('tenantmanagement::admin.max_connections') }}</th>
                                                <th>{{ __('tenantmanagement::admin.utilization') }}</th>
                                                <th>{{ __('tenantmanagement::admin.created_at') }}</th>
                                                <th>{{ __('tenantmanagement::admin.last_activity') }}</th>
                                                <th>{{ __('tenantmanagement::admin.status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tenantDetails as $detail)
                                            <tr>
                                                <td>
                                                    <strong>{{ $detail['tenant'] === 'central' ? 'Central' : 'Tenant ' . $detail['tenant'] }}</strong>
                                                </td>
                                                <td>
                                                    {{ $detail['active'] }}
                                                </td>
                                                <td>
                                                    {{ $detail['idle'] }}
                                                </td>
                                                <td>
                                                    {{ $detail['max'] }}
                                                </td>
                                                <td>
                                                    %{{ $detail['utilization'] }}
                                                </td>
                                                <td>{{ $detail['created_at'] }}</td>
                                                <td>{{ $detail['last_activity'] }}</td>
                                                <td>
                                                    @if($detail['utilization'] > 90)
                                                        {{ __('tenantmanagement::admin.critical') }}
                                                    @elseif($detail['utilization'] > 75)
                                                        {{ __('tenantmanagement::admin.high') }}
                                                    @else
                                                        {{ __('tenantmanagement::admin.normal') }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center p-4">
                                    <p class="text-muted">{{ __('tenantmanagement::admin.no_pool_data') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                
            </div>
        </div>
    </div>
</div>

