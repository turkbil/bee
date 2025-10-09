{{-- Modules/TenantManagement/resources/views/livewire/tenantcache.blade.php --}}
<div class="tenantcache-component-wrapper">
    <div class="card">
        @include('tenantmanagement::helper')
        <div class="card-body p-0">
<div class="row">
    <div class="col-12">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('tenantmanagement::admin.cache_management') }}</h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning" wire:click="clearAllCache">
                    <i class="fas fa-trash me-2"></i>
                    {{ __('tenantmanagement::admin.clear_cache') }}
                </button>
                <button type="button" class="btn btn-success" wire:click="optimizeCache">
                    <i class="fas fa-bolt me-2"></i>
                    {{ __('tenantmanagement::admin.optimize_cache') }}
                </button>
                <button type="button" class="btn btn-primary" wire:click="refreshStats">
                    <i class="fas fa-sync-alt me-2"></i>
                    {{ __('tenantmanagement::admin.refresh') }}
                </button>
            </div>
        </div>

        {{-- Cache Statistics --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-server fs-1 text-primary"></i>
                            </div>
                            <div class="flex-fill">
                                <h4 class="mb-0">{{ number_format($cacheStats['total_keys']) }}</h4>
                                <small class="text-muted">{{ __('tenantmanagement::admin.total_keys') }}</small>
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-database fs-1 text-success"></i>
                            </div>
                            <div class="flex-fill">
                                <h4 class="mb-0">{{ $cacheStats['hit_rate'] }}%</h4>
                                <small class="text-muted">{{ __('tenantmanagement::admin.hit_rate') }}</small>
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar bg-success" style="width: {{ $cacheStats['hit_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-memory fs-1 text-warning"></i>
                            </div>
                            <div class="flex-fill">
                                <h4 class="mb-0">{{ $cacheStats['memory_usage'] }}</h4>
                                <small class="text-muted">{{ __('tenantmanagement::admin.memory_usage') }}</small>
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar bg-warning" style="width: {{ $cacheStats['memory_percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-clock fs-1 text-info"></i>
                            </div>
                            <div class="flex-fill">
                                <h4 class="mb-0">{{ $cacheStats['avg_response_time'] }}ms</h4>
                                <small class="text-muted">{{ __('tenantmanagement::admin.avg_response_time') }}</small>
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar bg-info" style="width: 75%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tenant Filter --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
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
                        <label class="form-label">{{ __('tenantmanagement::admin.cache_type') }}</label>
                        <select class="form-select" wire:model.live="selectedCacheType">
                            <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                            <option value="database">Database</option>
                            <option value="api">API</option>
                            <option value="view">View</option>
                            <option value="config">Config</option>
                            <option value="route">Route</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('tenantmanagement::admin.search') }}</label>
                        <input type="text" class="form-control" wire:model.live="search" placeholder="{{ __('tenantmanagement::admin.search_cache_keys') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Cache Operations --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('tenantmanagement::admin.bulk_operations') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-danger w-100" wire:click="clearExpiredKeys">
                                    <i class="fas fa-trash me-2"></i>
                                    {{ __('tenantmanagement::admin.clear_expired_keys') }}
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-warning w-100" wire:click="clearTenantCache">
                                    <i class="fas fa-eraser me-2"></i>
                                    {{ __('tenantmanagement::admin.clear_selected_tenant') }}
                                </button>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-info w-100" wire:click="compressCache">
                                    <i class="fas fa-archive me-2"></i>
                                    {{ __('tenantmanagement::admin.compress_cache') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('tenantmanagement::admin.cache_statistics') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ number_format($cacheStats['hits']) }}</h4>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.cache_hits') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-danger">{{ number_format($cacheStats['misses']) }}</h4>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.cache_misses') }}</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $cacheStats['active_connections'] }}</h4>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.active_connections') }}</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ $cacheStats['expired_keys'] }}</h4>
                                    <small class="text-muted">{{ __('tenantmanagement::admin.expired_keys') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cache Keys Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('tenantmanagement::admin.cache_keys') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" wire:model="selectAll">
                            </th>
                            <th>{{ __('tenantmanagement::admin.key') }}</th>
                            <th>{{ __('tenantmanagement::admin.tenant') }}</th>
                            <th>{{ __('tenantmanagement::admin.type') }}</th>
                            <th>{{ __('tenantmanagement::admin.size') }}</th>
                            <th>{{ __('tenantmanagement::admin.ttl') }}</th>
                            <th>{{ __('tenantmanagement::admin.hits') }}</th>
                            <th>{{ __('tenantmanagement::admin.last_accessed') }}</th>
                            <th>{{ __('tenantmanagement::admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cacheKeys as $key)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" wire:model="selectedKeys" value="{{ $key['key'] }}">
                            </td>
                            <td>
                                <code class="text-primary">{{ Str::limit($key['key'], 50) }}</code>
                                @if(strlen($key['key']) > 50)
                                    <i class="fas fa-info-circle text-muted" data-bs-toggle="tooltip" title="{{ $key['key'] }}"></i>
                                @endif
                            </td>
                            <td>
                                @if($key['tenant_id'])
                                    {{ $key['tenant_name'] ?? $key['tenant_id'] }}
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.global') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $key['type'] }}
                            </td>
                            <td>
                                {{ $key['size_human'] }}
                            </td>
                            <td>
                                @if($key['ttl'] > 0)
                                    <span class="text-success">{{ $key['ttl_human'] }}</span>
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.never') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ number_format($key['hits']) }}
                            </td>
                            <td>
                                @if($key['last_accessed'])
                                    {{ $key['last_accessed']->diffForHumans() }}
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.never') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            wire:click="viewCacheValue('{{ $key['key'] }}')" data-bs-toggle="modal" data-bs-target="#cache-view-modal">
                                        <i class="fas fa-eye me-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            wire:click="refreshKey('{{ $key['key'] }}')">
                                        <i class="fas fa-sync-alt me-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            wire:click="deleteKey('{{ $key['key'] }}')" 
                                            onclick="return confirm('{{ __('tenantmanagement::admin.are_you_sure') }}')">
                                        <i class="fas fa-trash me-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($cacheKeys && is_object($cacheKeys) && method_exists($cacheKeys, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $cacheKeys->links() }}
        </div>
        @endif
        </div>
    </div>

{{-- Cache Value Modal --}}
<div class="modal modal-blur fade" id="cache-view-modal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('tenantmanagement::admin.cache_value') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($selectedKey)
                <div class="mb-3">
                    <label class="form-label">{{ __('tenantmanagement::admin.key') }}</label>
                    <code class="form-control">{{ $selectedKey }}</code>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('tenantmanagement::admin.value') }}</label>
                    <textarea class="form-control" rows="10" readonly>{{ $keyValue }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('tenantmanagement::admin.size') }}</label>
                        <input type="text" class="form-control" value="N/A" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('tenantmanagement::admin.ttl') }}</label>
                        <input type="text" class="form-control" value="{{ $keyTtl }}" readonly>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('tenantmanagement::admin.close') }}</button>
                @if($selectedKey)
                <button type="button" class="btn btn-warning" wire:click="refreshKey('{{ $selectedKey }}')">
                    <i class="fas fa-sync-alt me-2"></i>
                    {{ __('tenantmanagement::admin.refresh') }}
                </button>
                <button type="button" class="btn btn-danger" wire:click="deleteKey('{{ $selectedKey }}')"
                        onclick="return confirm('{{ __('tenantmanagement::admin.are_you_sure') }}')">
                    <i class="fas fa-trash me-2"></i>
                    {{ __('tenantmanagement::admin.delete') }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>        </div>
    </div>
</div>