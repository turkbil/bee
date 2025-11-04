<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-git-branch me-2"></i>
                        AI Conversation Flows
                    </h2>
                    <div class="text-muted mt-1">Manage conversation workflows for your AI chatbot</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.ai.workflow.flows.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Create New Flow
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
                            <label class="form-label">Search</label>
                            <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Search flows...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select wire:model="filterStatus" class="form-select">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    {{ $flow->flow_description ?? 'No description' }}
                                </p>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-muted small">Nodes</div>
                                        <div class="fw-bold">{{ count($flow->getNodes()) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Priority</div>
                                        <div class="fw-bold">{{ $flow->priority }}</div>
                                    </div>
                                </div>

                                <div class="mt-3 small text-muted">
                                    Updated {{ $flow->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100">
                                    <a href="{{ route('admin.ai.workflow.flows.edit', $flow->id) }}" class="btn btn-sm">
                                        <i class="ti ti-edit"></i> Edit
                                    </a>
                                    <button wire:click="toggleStatus({{ $flow->id }})" class="btn btn-sm">
                                        <i class="ti ti-power"></i> {{ $flow->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                    <button wire:click="duplicateFlow({{ $flow->id }})" class="btn btn-sm">
                                        <i class="ti ti-copy"></i> Duplicate
                                    </button>
                                    <button wire:click="deleteFlow({{ $flow->id }})"
                                            onclick="return confirm('Are you sure?')"
                                            class="btn btn-sm text-danger">
                                        <i class="ti ti-trash"></i> Delete
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
                                    <i class="ti ti-git-branch"></i>
                                </div>
                                <p class="empty-title">No flows found</p>
                                <p class="empty-subtitle text-muted">
                                    Create your first conversation flow to get started
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.ai.workflow.flows.create') }}" class="btn btn-primary">
                                        <i class="ti ti-plus"></i>
                                        Create Flow
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
