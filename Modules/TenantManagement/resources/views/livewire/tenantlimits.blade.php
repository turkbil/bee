@include('tenantmanagement::helper')

<div>
<div class="row">
    <div class="col-12">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('tenantmanagement::admin.resource_limits') }}</h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulk-update-modal">
                    <i class="fas fa-edit me-2"></i>
                    {{ __('tenantmanagement::admin.bulk_update') }}
                </button>
                <button type="button" class="btn btn-success" wire:click="applyPreset('standard')">
                    <i class="fas fa-star me-2"></i>
                    Standard Preset
                </button>
            </div>
        </div>

        {{-- Filters --}}
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
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                            <option value="active">{{ __('tenantmanagement::admin.active') }}</option>
                            <option value="inactive">{{ __('tenantmanagement::admin.not_active') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Limits Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('tenantmanagement::admin.resource_limits') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" wire:model="selectAll">
                            </th>
                            <th>{{ __('tenantmanagement::admin.tenant') }}</th>
                            <th>{{ __('tenantmanagement::admin.resource_type') }}</th>
                            <th>{{ __('tenantmanagement::admin.hourly_limit') }}</th>
                            <th>{{ __('tenantmanagement::admin.daily_limit') }}</th>
                            <th>{{ __('tenantmanagement::admin.monthly_limit') }}</th>
                            <th>{{ __('tenantmanagement::admin.current_usage') }}</th>
                            <th>{{ __('tenantmanagement::admin.status') }}</th>
                            <th>{{ __('tenantmanagement::admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($limits as $limit)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" wire:model="selectedLimits" value="{{ $limit->id }}">
                            </td>
                            <td>
                                <strong>{{ $limit->tenant->title ?? $limit->tenant_id }}</strong>
                            </td>
                            <td>
                                {{ __('tenantmanagement::admin.' . $limit->resource_type) }}
                            </td>
                            <td>
                                @if($limit->hourly_limit)
                                    {{ number_format($limit->hourly_limit) }}
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.unlimited') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($limit->daily_limit)
                                    {{ number_format($limit->daily_limit) }}
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.unlimited') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($limit->monthly_limit)
                                    {{ number_format($limit->monthly_limit) }}
                                @else
                                    <span class="text-muted">{{ __('tenantmanagement::admin.unlimited') }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $usage = $this->getCurrentUsage($limit->tenant_id, $limit->resource_type);
                                    $percentage = $limit->daily_limit ? ($usage / $limit->daily_limit) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ number_format($usage) }}</span>
                                    @if($limit->daily_limit)
                                        <div class="progress progress-sm flex-fill">
                                            <div class="progress-bar @if($percentage > 80) bg-danger @elseif($percentage > 60) bg-warning @else bg-success @endif" 
                                                 style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                        <small class="ms-2 text-muted">{{ number_format($percentage, 1) }}%</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($limit->is_active)
                                    {{ __('tenantmanagement::admin.active') }}
                                @else
                                    {{ __('tenantmanagement::admin.not_active') }}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            wire:click="editLimit({{ $limit->id }})" data-bs-toggle="modal" data-bs-target="#edit-limit-modal">
                                        <i class="fas fa-edit me-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            wire:click="deleteLimit({{ $limit->id }})" 
                                            onclick="return confirm('{{ __('tenantmanagement::admin.are_you_sure') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if($limit->is_active)
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                wire:click="toggleStatus({{ $limit->id }})">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                wire:click="toggleStatus({{ $limit->id }})">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $limits->links() }}
        </div>
    </div>
</div>

{{-- Edit Limit Modal --}}
<div class="modal modal-blur fade" id="edit-limit-modal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('tenantmanagement::admin.edit') }} {{ __('tenantmanagement::admin.resource_limits') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @if($editingLimitId)
            <form wire:submit.prevent="saveLimit">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.hourly_limit') }}</label>
                            <input type="number" class="form-control" wire:model="hourly_limit" min="0">
                            <small class="form-hint">{{ __('tenantmanagement::admin.leave_empty_for_unlimited') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.daily_limit') }}</label>
                            <input type="number" class="form-control" wire:model="daily_limit" min="0">
                            <small class="form-hint">{{ __('tenantmanagement::admin.leave_empty_for_unlimited') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.monthly_limit') }}</label>
                            <input type="number" class="form-control" wire:model="monthly_limit" min="0">
                            <small class="form-hint">{{ __('tenantmanagement::admin.leave_empty_for_unlimited') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.action') }}</label>
                            <select class="form-select" wire:model="limit_action">
                                <option value="block">Block</option>
                                <option value="throttle">Throttle</option>
                                <option value="warn">Warn</option>
                                <option value="queue">Queue</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="is_active">
                                <span class="form-check-label">{{ __('tenantmanagement::admin.active') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('tenantmanagement::admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('tenantmanagement::admin.save') }}</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Bulk Update Modal --}}
<div class="modal modal-blur fade" id="bulk-update-modal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('tenantmanagement::admin.bulk_update') }} {{ __('tenantmanagement::admin.resource_limits') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit.prevent="bulkUpdate">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p class="mb-0">{{ count($selectedLimits) }} {{ __('tenantmanagement::admin.items_selected') }}</p>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.hourly_limit') }}</label>
                            <input type="number" class="form-control" wire:model="bulkData.hourly_limit" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.daily_limit') }}</label>
                            <input type="number" class="form-control" wire:model="bulkData.daily_limit" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.monthly_limit') }}</label>
                            <input type="number" class="form-control" wire:model="bulkData.monthly_limit" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.action') }}</label>
                            <select class="form-select" wire:model="bulkData.limit_action">
                                <option value="">{{ __('tenantmanagement::admin.no_change') }}</option>
                                <option value="block">Block</option>
                                <option value="throttle">Throttle</option>
                                <option value="warn">Warn</option>
                                <option value="queue">Queue</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('tenantmanagement::admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('tenantmanagement::admin.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>