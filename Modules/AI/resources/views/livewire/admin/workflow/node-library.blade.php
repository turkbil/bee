<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fa fa-cube me-2"></i>
                        {{ __('ai::admin.workflow.node_library') }}
                    </h2>
                    <div class="text-muted mt-1">{{ __('ai::admin.workflow.node_library_subtitle') }}</div>
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
                        <div class="col-md-4">
                            <label class="form-label">{{ __('ai::admin.search_placeholder') }}</label>
                            <input type="text" wire:model.debounce.500ms="search" class="form-control"
                                   placeholder="{{ __('ai::admin.workflow.search_nodes') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('ai::admin.workflow.category') }}</label>
                            <select wire:model="filterCategory" class="form-select">
                                <option value="all">{{ __('ai::admin.workflow.category_all') }}</option>
                                <option value="common">{{ __('ai::admin.workflow.global_functions') }}</option>
                                <option value="ecommerce">{{ __('ai::admin.workflow.ecommerce') }}</option>
                                <option value="communication">{{ __('ai::admin.workflow.communication') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('ai::admin.workflow.scope') }}</label>
                            <select wire:model="filterGlobal" class="form-select">
                                <option value="all">{{ __('ai::admin.workflow.scope_all') }}</option>
                                <option value="global">{{ __('ai::admin.workflow.scope_global') }}</option>
                                <option value="tenant">{{ __('ai::admin.workflow.scope_tenant') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row row-cards mb-3">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('ai::admin.workflow.total_nodes') }}</div>
                                <div class="ms-auto lh-1">
                                    <i class="fa fa-cube text-muted"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0">{{ $nodes->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('ai::admin.workflow.global_nodes') }}</div>
                                <div class="ms-auto lh-1">
                                    <i class="fa fa-globe text-muted"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0">{{ $nodes->where('is_global', true)->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('ai::admin.workflow.tenant_nodes') }}</div>
                                <div class="ms-auto lh-1">
                                    <i class="fa fa-building text-muted"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0">{{ $nodes->where('is_global', false)->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('ai::admin.workflow.categories') }}</div>
                                <div class="ms-auto lh-1">
                                    <i class="fa fa-tags text-muted"></i>
                                </div>
                            </div>
                            <div class="h1 mb-0">{{ $nodesByCategory->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nodes by Category -->
            @forelse($nodesByCategory as $category => $categoryNodes)
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-folder me-2"></i>
                            @if($category === 'common')
                                {{ __('ai::admin.workflow.global_functions') }}
                            @elseif($category === 'ecommerce')
                                {{ __('ai::admin.workflow.ecommerce') }}
                            @elseif($category === 'communication')
                                {{ __('ai::admin.workflow.communication') }}
                            @else
                                {{ ucfirst($category) }}
                            @endif
                            <span class="badge bg-secondary ms-2">{{ $categoryNodes->count() }}</span>
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>{{ __('ai::admin.workflow.node_icon') }}</th>
                                    <th>{{ __('ai::admin.workflow.node_name') }}</th>
                                    <th>{{ __('ai::admin.workflow.node_key') }}</th>
                                    <th>{{ __('ai::admin.workflow.node_class') }}</th>
                                    <th>{{ __('ai::admin.workflow.scope') }}</th>
                                    <th>{{ __('ai::admin.status') }}</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryNodes as $node)
                                    <tr>
                                        <td>
                                            <i class="fa {{ $node->icon }} fa-lg text-muted"></i>
                                        </td>
                                        <td>
                                            <strong>{{ $node->getName() }}</strong>
                                            @if($node->getDescription())
                                                <br>
                                                <small class="text-muted">{{ $node->getDescription() }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-xs">{{ $node->node_key }}</code>
                                        </td>
                                        <td>
                                            <code class="text-xs">{{ class_basename($node->node_class) }}</code>
                                        </td>
                                        <td>
                                            @if($node->is_global)
                                                <span class="badge bg-success">
                                                    <i class="fa fa-globe me-1"></i>
                                                    {{ __('ai::admin.workflow.scope_global') }}
                                                </span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="fa fa-building me-1"></i>
                                                    {{ __('ai::admin.workflow.scope_tenant') }}
                                                </span>
                                                @if($node->tenant_whitelist)
                                                    <br>
                                                    <small class="text-muted">Tenant: {{ implode(', ', $node->tenant_whitelist) }}</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($node->is_active)
                                                <span class="badge bg-success">{{ __('ai::admin.workflow.status_active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('ai::admin.workflow.status_inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $node->id }})"
                                                    class="btn btn-sm {{ $node->is_active ? 'btn-ghost-danger' : 'btn-ghost-success' }}"
                                                    title="{{ $node->is_active ? __('ai::admin.workflow.deactivate') : __('ai::admin.workflow.activate') }}">
                                                <i class="fa fa-power-off"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fa fa-cube"></i>
                        </div>
                        <p class="empty-title">{{ __('ai::admin.workflow.no_nodes') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('ai::admin.workflow.no_nodes_subtitle') }}
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
