<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fa fa-code-branch me-2"></i>
                        {{ __('ai::admin.workflow.flows_title') }}
                    </h2>
                    <div class="text-muted mt-1">{{ __('ai::admin.workflow.flows_subtitle') }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.ai.workflow.flows.manage') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>
                        {{ __('ai::admin.workflow.create_flow') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('ai::admin.search_placeholder') }}</label>
                            <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="{{ __('ai::admin.workflow.search_flows') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('ai::admin.status') }}</label>
                            <select wire:model="filterStatus" class="form-select">
                                <option value="all">{{ __('ai::admin.workflow.status_all') }}</option>
                                <option value="active">{{ __('ai::admin.workflow.status_active') }}</option>
                                <option value="inactive">{{ __('ai::admin.workflow.status_inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flows List -->
            <div class="row row-cards">
                @forelse($flows as $flow)
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ $flow->flow_name }}</h3>
                                <div class="card-actions">
                                    @if($flow->is_active)
                                        <span class="badge bg-success">{{ __('ai::admin.workflow.status_active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('ai::admin.workflow.status_inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    {{ $flow->flow_description ?? 'No description' }}
                                </p>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-muted small">{{ __('ai::admin.workflow.nodes') }}</div>
                                        <div class="fw-bold">{{ count($flow->getNodes()) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">{{ __('ai::admin.workflow.priority') }}</div>
                                        <div class="fw-bold">{{ $flow->priority }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 small text-muted">
                                    {{ __('ai::admin.workflow.updated') }} {{ $flow->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100">
                                    <a href="{{ route('admin.ai.workflow.flows.manage', $flow->id) }}" class="btn btn-sm">
                                        <i class="fa fa-edit"></i> {{ __('ai::admin.edit') }}
                                    </a>
                                    <button wire:click="toggleStatus({{ $flow->id }})" class="btn btn-sm">
                                        <i class="fa fa-power-off"></i> {{ $flow->is_active ? __('ai::admin.workflow.deactivate') : __('ai::admin.workflow.activate') }}
                                    </button>
                                    <button wire:click="duplicateFlow({{ $flow->id }})" class="btn btn-sm">
                                        <i class="fa fa-copy"></i> {{ __('ai::admin.workflow.duplicate') }}
                                    </button>
                                    <button wire:click="deleteFlow({{ $flow->id }})"
                                            onclick="return confirm('{{ __('ai::admin.workflow.confirm_delete') }}')"
                                            class="btn btn-sm text-danger">
                                        <i class="fa fa-trash"></i> {{ __('ai::admin.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fa fa-code-branch"></i>
                                </div>
                                <p class="empty-title">{{ __('ai::admin.workflow.no_flows_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('ai::admin.workflow.no_flows_subtitle') }}
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.ai.workflow.flows.manage') }}" class="btn btn-primary">
                                        <i class="fa fa-plus"></i>
                                        {{ __('ai::admin.workflow.create_flow') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $flows->links() }}
            </div>
        </div>
    </div>
</div>
