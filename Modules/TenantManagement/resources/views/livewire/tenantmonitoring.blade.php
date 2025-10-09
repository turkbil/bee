{{-- Modules/TenantManagement/resources/views/livewire/tenantmonitoring.blade.php --}}
<div class="tenantmonitoring-component-wrapper">
    <div class="card">
@php
    View::share('pretitle', 'Kiracı İzleme');
@endphp

        @include('tenantmanagement::helper')
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('tenantmanagement::admin.search_placeholder') }}">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, selectedTenant, selectedResourceType, selectedStatus, refreshData"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('tenantmanagement::admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-primary" wire:click="toggleAutoRefresh">
                            <i class="fas fa-sync-alt me-2" @if($autoRefresh) data-bs-toggle="tooltip" title="{{ __('tenantmanagement::admin.auto_refresh') }}: ON" @endif></i>
                            {{ $autoRefresh ? 'ON' : 'OFF' }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="refreshData">
                            <i class="fas fa-sync-alt me-2"></i>
                            {{ __('tenantmanagement::admin.refresh') }}
                        </button>
                        <button type="button" class="btn btn-success" wire:click="exportData">
                            <i class="fas fa-download me-2"></i>
                            {{ __('tenantmanagement::admin.export') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mx-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tenant</label>
                    <select class="form-select" wire:model.live="selectedTenantId">
                        <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->title ?? $tenant->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('tenantmanagement::admin.resource_type') }}</label>
                    <select class="form-select" wire:model.live="selectedResourceType">
                        <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                        <option value="api_calls">{{ __('tenantmanagement::admin.api_calls') }}</option>
                        <option value="database_queries">{{ __('tenantmanagement::admin.database_queries') }}</option>
                        <option value="cache_operations">{{ __('tenantmanagement::admin.cache_operations') }}</option>
                        <option value="storage_usage">{{ __('tenantmanagement::admin.storage_usage') }}</option>
                        <option value="ai_operations">{{ __('tenantmanagement::admin.ai_operations') }}</option>
                        <option value="cpu_usage">{{ __('tenantmanagement::admin.cpu_usage') }}</option>
                        <option value="memory_usage">{{ __('tenantmanagement::admin.memory_usage') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('tenantmanagement::admin.status') }}</label>
                    <select class="form-select" wire:model.live="selectedStatus">
                        <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                        <option value="normal">{{ __('tenantmanagement::admin.normal') }}</option>
                        <option value="warning">{{ __('tenantmanagement::admin.warning') }}</option>
                        <option value="critical">{{ __('tenantmanagement::admin.critical') }}</option>
                        <option value="blocked">{{ __('tenantmanagement::admin.blocked') }}</option>
                    </select>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="row mx-2 mb-3">
                @if($resourceStats && is_array($resourceStats))
                    @foreach($resourceStats as $stat)
                    <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($stat['type'] == 'api_calls')
                                        <i class="fas fa-network-wired fs-1 text-primary"></i>
                                    @elseif($stat['type'] == 'database_queries')
                                        <i class="fas fa-database fs-1 text-info"></i>
                                    @elseif($stat['type'] == 'cache_operations')
                                        <i class="fas fa-server fs-1 text-warning"></i>
                                    @elseif($stat['type'] == 'storage_usage')
                                        <i class="fas fa-folder fs-1 text-secondary"></i>
                                    @elseif($stat['type'] == 'ai_operations')
                                        <i class="fas fa-brain fs-1 text-purple"></i>
                                    @elseif($stat['type'] == 'cpu_usage')
                                        <i class="fas fa-microchip fs-1 text-danger"></i>
                                    @else
                                        <i class="fas fa-memory fs-1 text-success"></i>
                                    @endif
                                </div>
                                <div class="flex-fill">
                                    <h4 class="mb-0">{{ number_format($stat['current_usage']) }}</h4>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.' . $stat['type']) }}</small>
                                    <div class="progress progress-sm mt-1">
                                        <div class="progress-bar @if($stat['usage_percentage'] > 80) bg-danger @elseif($stat['usage_percentage'] > 60) bg-warning @else bg-success @endif" 
                                             style="width: {{ min($stat['usage_percentage'], 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($stat['usage_percentage'], 1) }}% {{ __('tenantmanagement::admin.usage') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    @endforeach
                @endif
            </div>

            {{-- Usage Chart --}}
            <div class="mx-2 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('tenantmanagement::admin.usage_statistics') }} - {{ __('tenantmanagement::admin.last_24_hours') }}</h3>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; height: 400px; width: 100%;">
                            <canvas id="usageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Usage Logs Table --}}
            <div class="table-responsive">
                <table class="table table-vcenter table-mobile-md card-table">
                    <thead>
                        <tr>
                            <th>{{ __('tenantmanagement::admin.tenant') }}</th>
                            <th>{{ __('tenantmanagement::admin.resource_type') }}</th>
                            <th>{{ __('tenantmanagement::admin.usage') }}</th>
                            <th>{{ __('tenantmanagement::admin.status') }}</th>
                            <th>{{ __('tenantmanagement::admin.date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->tenant->title ?? $log->tenant_id }}</strong>
                            </td>
                            <td>
                                {{ __('tenantmanagement::admin.' . $log->resource_type) }}
                            </td>
                            <td>
                                {{ number_format($log->usage_amount) }}
                            </td>
                            <td>
                                @if($log->status == 'normal')
                                    {{ __('tenantmanagement::admin.normal') }}
                                @elseif($log->status == 'warning')
                                    {{ __('tenantmanagement::admin.warning') }}
                                @elseif($log->status == 'critical')
                                    {{ __('tenantmanagement::admin.critical') }}
                                @else
                                    {{ __('tenantmanagement::admin.blocked') }}
                                @endif
                            </td>
                            <td>{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('usageChart').getContext('2d');
        let chartData = @json($chartData);
        
        // Empty state fallback for chart
        if (!chartData || !chartData.labels || chartData.labels.length === 0) {
            const currentTime = new Date();
            const labels = [];
            for(let i = 23; i >= 0; i--) {
                const time = new Date(currentTime.getTime() - (i * 60 * 60 * 1000));
                labels.push(time.getHours() + ':00');
            }
            
            chartData = {
                labels: labels,
                datasets: [
                    {
                        label: 'CPU (%)',
                        data: new Array(24).fill(0),
                        borderColor: '#ff6384',
                        backgroundColor: '#ff638420',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'RAM (MB)',
                        data: new Array(24).fill(0),
                        borderColor: '#36a2eb',
                        backgroundColor: '#36a2eb20',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'API İstekleri',
                        data: new Array(24).fill(0),
                        borderColor: '#4bc0c0',
                        backgroundColor: '#4bc0c020',
                        tension: 0.4,
                        fill: false
                    }
                ]
            };
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Zaman'
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Kullanım'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white'
                    }
                },
                elements: {
                    point: {
                        radius: 3,
                        hoverRadius: 5
                    },
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });

        @if($autoRefresh)
        // Auto refresh every 30 seconds
        setInterval(function() {
            Livewire.dispatch('refreshData');
        }, 30000);
        @endif
    });
    </script>
    @endpush
</div>