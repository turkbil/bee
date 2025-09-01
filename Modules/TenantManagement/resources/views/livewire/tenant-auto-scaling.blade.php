{{-- Modules/TenantManagement/resources/views/livewire/tenant-auto-scaling.blade.php --}}
<div wire:id="{{ $this->getId() }}" class="auto-scaling-component-wrapper">
    <div class="card">
        @include('tenantmanagement::helper')
        <div class="card-body p-0">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 p-4">
                <h1 class="h3">{{ __('tenantmanagement::admin.auto_scaling') }}</h1>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" wire:click="toggleAutoRefresh">
                        <i class="fas fa-sync-alt me-2 @if($autoRefresh) fa-spin @endif"></i>
                        {{ $autoRefresh ? __('tenantmanagement::admin.auto_refresh_on') : __('tenantmanagement::admin.auto_refresh_off') }}
                    </button>
                    <button type="button" class="btn btn-success" wire:click="executeScaling('scale_up')">
                        <i class="fas fa-arrow-up me-2"></i>
                        {{ __('tenantmanagement::admin.scale_up') }}
                    </button>
                    <button type="button" class="btn btn-warning" wire:click="executeScaling('scale_down')">
                        <i class="fas fa-arrow-down me-2"></i>
                        {{ __('tenantmanagement::admin.scale_down') }}
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="loadMetrics">
                        <i class="fas fa-refresh me-2"></i>
                        {{ __('tenantmanagement::admin.refresh') }}
                    </button>
                </div>
            </div>

            <div class="p-4">
                {{-- Real-time Metrics Dashboard --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-users fs-1"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <h3 class="mb-0">{{ $metrics['active_tenants'] ?? 0 }}</h3>
                                        <small class="opacity-75">{{ __('tenantmanagement::admin.active_tenants') }}</small>
                                        <div class="progress progress-sm mt-1 bg-white bg-opacity-25">
                                            <div class="progress-bar bg-white" style="width: {{ min(100, ($metrics['active_tenants'] ?? 0) * 25) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-memory fs-1"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <h3 class="mb-0">{{ $metrics['memory_usage_mb'] ?? 0 }} MB</h3>
                                        <small class="opacity-75">{{ __('tenantmanagement::admin.memory_usage') }}</small>
                                        <div class="progress progress-sm mt-1 bg-white bg-opacity-25">
                                            <div class="progress-bar bg-white" style="width: {{ min(100, ($metrics['memory_usage_mb'] ?? 0) / 10) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-tachometer-alt fs-1"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <h3 class="mb-0">{{ $metrics['efficiency_percent'] ?? 0 }}%</h3>
                                        <small class="opacity-75">{{ __('tenantmanagement::admin.efficiency') }}</small>
                                        <div class="progress progress-sm mt-1 bg-white bg-opacity-25">
                                            <div class="progress-bar bg-white" style="width: {{ $metrics['efficiency_percent'] ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-dollar-sign fs-1"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <h3 class="mb-0">${{ $metrics['monthly_savings'] ?? 0 }}</h3>
                                        <small class="opacity-75">{{ __('tenantmanagement::admin.monthly_savings') }}</small>
                                        <div class="progress progress-sm mt-1 bg-white bg-opacity-25">
                                            <div class="progress-bar bg-white" style="width: {{ min(100, ($metrics['monthly_savings'] ?? 0) / 10) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Status --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('tenantmanagement::admin.system_status') }}</h3>
                                <div class="card-actions">
                                    @if($metrics['auto_scaling_enabled'] ?? false)
                                        {{ __('tenantmanagement::admin.auto_scaling_enabled') }}
                                    @else
                                        {{ __('tenantmanagement::admin.auto_scaling_disabled') }}
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $metrics['avg_response_time'] ?? 0 }}ms</h4>
                                            <small class="text-muted">{{ __('tenantmanagement::admin.avg_response_time') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ $metrics['cpu_usage_percent'] ?? 0 }}%</h4>
                                            <small class="text-muted">{{ __('tenantmanagement::admin.cpu_usage') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('tenantmanagement::admin.daily_operations') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-warning">{{ $metrics['daily_average_operations'] ?? 0 }}</h2>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.operations_per_hour') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Last Scaling Operation --}}
                @if(isset($metrics['last_scaling_operation']) && $metrics['last_scaling_operation'] && is_array($metrics['last_scaling_operation']) && isset($metrics['last_scaling_operation']['type']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('tenantmanagement::admin.last_scaling_operation') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>{{ __('tenantmanagement::admin.operation_type') }}:</strong>
                                {{ ucfirst($metrics['last_scaling_operation']['type']) }}
                            </div>
                            <div class="col-md-4">
                                <strong>{{ __('tenantmanagement::admin.timestamp') }}:</strong>
                                {{ isset($metrics['last_scaling_operation']['timestamp']) ? \Carbon\Carbon::parse($metrics['last_scaling_operation']['timestamp'])->diffForHumans() : 'N/A' }}
                            </div>
                            <div class="col-md-5">
                                <strong>{{ __('tenantmanagement::admin.reason') }}:</strong>
                                {{ $metrics['last_scaling_operation']['reason'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Real-time System Load Graph Placeholder --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('tenantmanagement::admin.real_time_system_load') }}</h3>
                        <div class="card-actions">
                            <small class="text-muted">{{ __('tenantmanagement::admin.updated') }}: {{ now()->format('H:i:s') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h4 class="text-danger">{{ $metrics['system_load'] ?? 0 }}%</h4>
                                    <small>{{ __('tenantmanagement::admin.current_load') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h4 class="text-info">{{ count(explode(',', implode(',', array_keys($metrics ?? [])))) }}</h4>
                                    <small>{{ __('tenantmanagement::admin.active_services') }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded">
                                    <h4 class="text-success">
                                        @if(($metrics['auto_scaling_enabled'] ?? false))
                                            {{ __('tenantmanagement::admin.on') }}
                                        @else
                                            {{ __('tenantmanagement::admin.off') }}
                                        @endif
                                    </h4>
                                    <small>{{ __('tenantmanagement::admin.auto_scaling_status') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Auto-refresh JavaScript --}}
<script>
document.addEventListener('livewire:load', function () {
    let autoRefreshInterval;

    Livewire.on('startAutoRefresh', function (data) {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        
        autoRefreshInterval = setInterval(function () {
            @this.loadMetrics();
        }, data.interval || 5000);
        
        // Auto-refresh started with interval: data.interval
    });

    Livewire.on('stopAutoRefresh', function () {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        // Auto-refresh stopped
    });
});
</script>