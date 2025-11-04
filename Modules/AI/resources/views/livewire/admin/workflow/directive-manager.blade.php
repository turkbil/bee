<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-settings me-2"></i>
                        AI Directives
                    </h2>
                    <div class="text-muted mt-1">Configure AI behavior for your tenant</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button wire:click="openNewModal" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        New Directive
                    </button>
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
                            <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="Search directives...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select wire:model="filterCategory" class="form-select">
                                <option value="all">All Categories</option>
                                <option value="general">General</option>
                                <option value="behavior">Behavior</option>
                                <option value="display">Display</option>
                                <option value="pricing">Pricing</option>
                                <option value="lead">Lead Collection</option>
                                <option value="contact">Contact</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Statistics</label>
                            <div class="fw-bold">
                                Total: {{ $categories->sum() }} directives
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Directives Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th class="w-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($directives as $directive)
                                <tr>
                                    <td>
                                        <span class="badge bg-azure-lt">{{ $directive->directive_key }}</span>
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <input type="text" wire:model.defer="directiveValue" class="form-control form-control-sm">
                                            @error('directiveValue') <span class="text-danger small">{{ $message }}</span> @enderror
                                        @else
                                            <code>{{ Str::limit($directive->directive_value, 50) }}</code>
                                        @endif
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <select wire:model.defer="directiveType" class="form-select form-select-sm">
                                                <option value="string">String</option>
                                                <option value="integer">Integer</option>
                                                <option value="boolean">Boolean</option>
                                                <option value="json">JSON</option>
                                                <option value="array">Array</option>
                                            </select>
                                        @else
                                            <span class="badge bg-secondary">{{ $directive->directive_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <select wire:model.defer="directiveCategory" class="form-select form-select-sm">
                                                <option value="general">General</option>
                                                <option value="behavior">Behavior</option>
                                                <option value="display">Display</option>
                                                <option value="pricing">Pricing</option>
                                                <option value="lead">Lead</option>
                                                <option value="contact">Contact</option>
                                            </select>
                                        @else
                                            <span class="badge bg-info-lt">{{ $directive->category }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <textarea wire:model.defer="directiveDescription" class="form-control form-control-sm" rows="2"></textarea>
                                        @else
                                            <small class="text-muted">{{ $directive->description ?? '-' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <label class="form-check form-switch">
                                                <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                            </label>
                                        @else
                                            @if($directive->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($editingDirective === $directive->id)
                                            <div class="btn-group">
                                                <button wire:click="saveDirective" class="btn btn-sm btn-success">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                                <button wire:click="cancelEdit" class="btn btn-sm btn-secondary">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="btn-group">
                                                <button wire:click="editDirective({{ $directive->id }})" class="btn btn-sm btn-ghost-secondary">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <button wire:click="toggleStatus({{ $directive->id }})" class="btn btn-sm btn-ghost-secondary">
                                                    <i class="ti ti-power"></i>
                                                </button>
                                                <button wire:click="deleteDirective({{ $directive->id }})"
                                                        onclick="return confirm('Are you sure?')"
                                                        class="btn btn-sm btn-ghost-danger">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="ti ti-settings-off fs-1 mb-2 d-block"></i>
                                        No directives found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $directives->links() }}
            </div>
        </div>
    </div>

    <!-- New Directive Modal -->
    @if($showNewModal)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">New Directive</h5>
                        <button type="button" class="btn-close" wire:click="closeNewModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Directive Key</label>
                            <input type="text" wire:model.defer="directiveKey" class="form-control" placeholder="e.g. max_products_per_response">
                            @error('directiveKey') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label required">Directive Value</label>
                                    <input type="text" wire:model.defer="directiveValue" class="form-control" placeholder="e.g. 5">
                                    @error('directiveValue') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Type</label>
                                    <select wire:model.defer="directiveType" class="form-select">
                                        <option value="string">String</option>
                                        <option value="integer">Integer</option>
                                        <option value="boolean">Boolean</option>
                                        <option value="json">JSON</option>
                                        <option value="array">Array</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Category</label>
                            <select wire:model.defer="directiveCategory" class="form-select">
                                <option value="general">General</option>
                                <option value="behavior">Behavior</option>
                                <option value="display">Display</option>
                                <option value="pricing">Pricing</option>
                                <option value="lead">Lead Collection</option>
                                <option value="contact">Contact</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea wire:model.defer="directiveDescription" class="form-control" rows="3" placeholder="What does this directive control?"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model.defer="isActive" class="form-check-input" checked>
                                <span class="form-check-label">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" wire:click="closeNewModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="saveDirective">Create Directive</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
