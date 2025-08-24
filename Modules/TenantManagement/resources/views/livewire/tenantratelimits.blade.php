@include('tenantmanagement::helper')

<div>
<div class="row">
    <div class="col-12">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ __('tenantmanagement::admin.rate_limits') }}</h1>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-rule-modal">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('tenantmanagement::admin.add_new') }}
                </button>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#bulk-update-modal">
                    <i class="fas fa-edit me-2"></i>
                    {{ __('tenantmanagement::admin.bulk_update') }}
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tenant</label>
                        <select class="form-select" wire:model.live="selectedTenantId">
                            <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}">{{ $tenant->title ?? $tenant->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('tenantmanagement::admin.method') }}</label>
                        <select class="form-select" wire:model.live="selectedMethod">
                            <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                            <option value="*">{{ __('tenantmanagement::admin.all_methods') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('tenantmanagement::admin.strategy') }}</label>
                        <select class="form-select" wire:model.live="selectedStrategy">
                            <option value="">{{ __('tenantmanagement::admin.all') }}</option>
                            <option value="fixed_window">Fixed Window</option>
                            <option value="sliding_window">Sliding Window</option>
                            <option value="token_bucket">Token Bucket</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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

        {{-- Rate Limits Table --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('tenantmanagement::admin.rate_limits') }} {{ __('tenantmanagement::admin.rules') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" wire:model="selectAll">
                            </th>
                            <th>{{ __('tenantmanagement::admin.tenant') }}</th>
                            <th>{{ __('tenantmanagement::admin.endpoint') }}</th>
                            <th>{{ __('tenantmanagement::admin.method') }}</th>
                            <th>{{ __('tenantmanagement::admin.strategy') }}</th>
                            <th>{{ __('tenantmanagement::admin.limit') }}</th>
                            <th>{{ __('tenantmanagement::admin.window') }}</th>
                            <th>{{ __('tenantmanagement::admin.priority') }}</th>
                            <th>{{ __('tenantmanagement::admin.status') }}</th>
                            <th>{{ __('tenantmanagement::admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rateLimits as $rule)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" wire:model="selectedRules" value="{{ $rule->id }}">
                            </td>
                            <td>
                                <strong>{{ $rule->tenant->title ?? $rule->tenant_id }}</strong>
                            </td>
                            <td>
                                <code class="text-primary">{{ $rule->endpoint_pattern }}</code>
                            </td>
                            <td>
                                {{ $rule->http_method }}
                            </td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $rule->rate_limit_strategy)) }}
                            </td>
                            <td>
                                <strong>{{ $rule->max_requests }}</strong>
                            </td>
                            <td>
                                {{ $rule->window_minutes }}{{ __('tenantmanagement::admin.minutes_short') }}
                            </td>
                            <td>
                                {{ $rule->priority }}
                            </td>
                            <td>
                                @if($rule->is_active)
                                    {{ __('tenantmanagement::admin.active') }}
                                @else
                                    {{ __('tenantmanagement::admin.not_active') }}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            wire:click="editRule({{ $rule->id }})" data-bs-toggle="modal" data-bs-target="#edit-rule-modal">
                                        <i class="fas fa-edit me-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            wire:click="testRule({{ $rule->id }})">
                                        <i class="fas fa-flask me-2"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            wire:click="deleteRule({{ $rule->id }})" 
                                            onclick="return confirm('{{ __('tenantmanagement::admin.are_you_sure') }}')">
                                        <i class="fas fa-trash me-2"></i>
                                    </button>
                                    @if($rule->is_active)
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                wire:click="toggleStatus({{ $rule->id }})">
                                            <i class="fas fa-pause me-2"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                wire:click="toggleStatus({{ $rule->id }})">
                                            <i class="fas fa-play me-2"></i>
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
            {{ $rateLimits->links() }}
        </div>
    </div>
</div>

{{-- Add Rule Modal --}}
<div class="modal modal-blur fade" id="add-rule-modal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('tenantmanagement::admin.add_new') }} {{ __('tenantmanagement::admin.rate_limits') }} {{ __('tenantmanagement::admin.rule') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit.prevent="createRule">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.tenant') }} *</label>
                            <select class="form-select" wire:model="newRule.tenant_id" required>
                                <option value="">{{ __('tenantmanagement::admin.select_tenant') }}</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->title ?? $tenant->id }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.endpoint') }} {{ __('tenantmanagement::admin.pattern') }} *</label>
                            <input type="text" class="form-control" wire:model="newRule.endpoint_pattern" 
                                   placeholder="/api/*" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">HTTP {{ __('tenantmanagement::admin.method') }}</label>
                            <select class="form-select" wire:model="newRule.http_method">
                                <option value="*">{{ __('tenantmanagement::admin.all_methods') }}</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.strategy') }}</label>
                            <select class="form-select" wire:model="newRule.rate_limit_strategy">
                                <option value="fixed_window">Fixed Window</option>
                                <option value="sliding_window">Sliding Window</option>
                                <option value="token_bucket">Token Bucket</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('tenantmanagement::admin.max_requests') }} *</label>
                            <input type="number" class="form-control" wire:model="newRule.max_requests" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('tenantmanagement::admin.window') }} ({{ __('tenantmanagement::admin.minutes') }}) *</label>
                            <input type="number" class="form-control" wire:model="newRule.window_minutes" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('tenantmanagement::admin.priority') }} (1-100)</label>
                            <input type="number" class="form-control" wire:model="newRule.priority" min="1" max="100" value="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.whitelist_ips') }}</label>
                            <textarea class="form-control" wire:model="newRule.whitelist_ips" rows="2" 
                                      placeholder="127.0.0.1,192.168.1.0/24"></textarea>
                            <small class="form-hint">{{ __('tenantmanagement::admin.comma_separated') }}</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.blacklist_ips') }}</label>
                            <textarea class="form-control" wire:model="newRule.blacklist_ips" rows="2" 
                                      placeholder="1.2.3.4,5.6.7.0/24"></textarea>
                            <small class="form-hint">{{ __('tenantmanagement::admin.comma_separated') }}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input" wire:model="newRule.is_active" checked>
                                <span class="form-check-label">{{ __('tenantmanagement::admin.active') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('tenantmanagement::admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('tenantmanagement::admin.create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Rule Modal --}}
<div class="modal modal-blur fade" id="edit-rule-modal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('tenantmanagement::admin.edit') }} {{ __('tenantmanagement::admin.rate_limits') }} {{ __('tenantmanagement::admin.rule') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @if($editingRuleId)
            <form wire:submit.prevent="updateRule">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.endpoint') }} {{ __('tenantmanagement::admin.pattern') }} *</label>
                            <input type="text" class="form-control" wire:model="endpoint_pattern" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">HTTP {{ __('tenantmanagement::admin.method') }}</label>
                            <select class="form-select" wire:model="method">
                                <option value="*">{{ __('tenantmanagement::admin.all_methods') }}</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.strategy') }}</label>
                            <select class="form-select" wire:model="throttle_strategy">
                                <option value="fixed_window">Fixed Window</option>
                                <option value="sliding_window">Sliding Window</option>
                                <option value="token_bucket">Token Bucket</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.priority') }} (1-100)</label>
                            <input type="number" class="form-control" wire:model="priority" min="1" max="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('tenantmanagement::admin.max_requests') }} *</label>
                            <input type="number" class="form-control" wire:model="requests_per_minute" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('tenantmanagement::admin.window') }} ({{ __('tenantmanagement::admin.minutes') }}) *</label>
                            <input type="number" class="form-control" wire:model="requests_per_hour" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" wire:model="is_active">
                                <label class="form-check-label">{{ __('tenantmanagement::admin.active') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.whitelist_ips') }}</label>
                            <textarea class="form-control" wire:model="ip_whitelist" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('tenantmanagement::admin.blacklist_ips') }}</label>
                            <textarea class="form-control" wire:model="ip_blacklist" rows="2"></textarea>
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
</div>