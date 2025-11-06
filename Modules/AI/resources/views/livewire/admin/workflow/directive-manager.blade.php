<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fa fa-cogs me-2"></i>
                        {{ __('ai::admin.workflow.directives_title') }}
                    </h2>
                    <div class="text-muted mt-1">{{ __('ai::admin.workflow.directives_subtitle') }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button wire:click="openNewModal" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i>
                        {{ __('ai::admin.workflow.new_directive') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Filters & Stats -->
            <div class="row mb-3">
                <div class="col-md-5">
                    <input type="text" wire:model.debounce.500ms="search" class="form-control"
                           placeholder="{{ __('ai::admin.workflow.search_directives') }}">
                </div>
                <div class="col-md-3">
                    <select wire:model="filterCategory" class="form-select">
                        <option value="all">{{ __('ai::admin.workflow.category_all') }}</option>
                        <option value="ai_config">AI Configuration</option>
                        <option value="chat">Chat Settings</option>
                        <option value="general">{{ __('ai::admin.workflow.category_general') }}</option>
                        <option value="behavior">{{ __('ai::admin.workflow.category_behavior') }}</option>
                        <option value="display">{{ __('ai::admin.workflow.category_display') }}</option>
                        <option value="pricing">{{ __('ai::admin.workflow.category_pricing') }}</option>
                        <option value="lead">{{ __('ai::admin.workflow.category_lead') }}</option>
                        <option value="contact">{{ __('ai::admin.workflow.category_contact') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <i class="fa fa-cogs"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalCount }} {{ __('ai::admin.workflow.total_directives') }}
                                    </div>
                                    <div class="text-muted">
                                        {{ $groupedDirectives->count() }} categories
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Directive Cards by Category -->
            @forelse($groupedDirectives as $category => $directives)
                @php
                    $meta = $categoryMeta[$category] ?? [
                        'title' => ucfirst($category),
                        'icon' => 'fa-cog',
                        'color' => 'secondary',
                        'description' => ''
                    ];
                @endphp

                <div class="mb-4">
                    <!-- Category Header -->
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-sm bg-{{ $meta['color'] }}-lt me-2">
                            <i class="fa {{ $meta['icon'] }}"></i>
                        </span>
                        <div>
                            <h3 class="mb-0">{{ $meta['title'] }}</h3>
                            <small class="text-muted">{{ $meta['description'] }}</small>
                        </div>
                        <div class="ms-auto">
                            <span class="badge bg-{{ $meta['color'] }}">{{ $directives->count() }}</span>
                        </div>
                    </div>

                    <!-- Directive Cards in this Category -->
                    <div class="row row-cards">
                        @foreach($directives as $directive)
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-status-top bg-{{ $directive->is_active ? 'success' : 'secondary' }}"></div>
                                    <div class="card-body">
                                        <!-- Key & Status -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h4 class="card-title mb-1">
                                                    <code class="text-primary">{{ $directive->directive_key }}</code>
                                                </h4>
                                                @if($directive->description)
                                                    <p class="text-muted small mb-2">{{ $directive->description }}</p>
                                                @endif
                                            </div>
                                            <div class="ms-2">
                                                <span class="badge bg-secondary">{{ $directive->directive_type }}</span>
                                            </div>
                                        </div>

                                        @if($editingDirective === $directive->id)
                                            <!-- Edit Mode -->
                                            <div class="border-top pt-3 mt-2">
                                                <div class="mb-2">
                                                    <label class="form-label form-label-sm">Value</label>
                                                    @if($directive->directive_type === 'json' || strlen($directive->directive_value) > 100)
                                                        <textarea wire:model.defer="directiveValue"
                                                                  class="form-control form-control-sm font-monospace"
                                                                  rows="4"></textarea>
                                                    @else
                                                        <input type="text" wire:model.defer="directiveValue"
                                                               class="form-control form-control-sm font-monospace">
                                                    @endif
                                                    @error('directiveValue')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-6">
                                                        <label class="form-label form-label-sm">Type</label>
                                                        <select wire:model.defer="directiveType" class="form-select form-select-sm">
                                                            <option value="string">String</option>
                                                            <option value="integer">Integer</option>
                                                            <option value="boolean">Boolean</option>
                                                            <option value="json">JSON</option>
                                                            <option value="array">Array</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label form-label-sm">Category</label>
                                                        <select wire:model.defer="directiveCategory" class="form-select form-select-sm">
                                                            <option value="ai_config">AI Config</option>
                                                            <option value="chat">Chat</option>
                                                            <option value="general">General</option>
                                                            <option value="behavior">Behavior</option>
                                                            <option value="display">Display</option>
                                                            <option value="pricing">Pricing</option>
                                                            <option value="lead">Lead</option>
                                                            <option value="contact">Contact</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <label class="form-label form-label-sm">Description</label>
                                                    <textarea wire:model.defer="directiveDescription"
                                                              class="form-control form-control-sm"
                                                              rows="2"></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-check form-switch">
                                                        <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                                        <span class="form-check-label">Active</span>
                                                    </label>
                                                </div>

                                                <div class="btn-group w-100">
                                                    <button wire:click="saveDirective" class="btn btn-success btn-sm">
                                                        <i class="fa fa-check me-1"></i> {{ __('ai::admin.save') }}
                                                    </button>
                                                    <button wire:click="cancelEdit" class="btn btn-secondary btn-sm">
                                                        <i class="fa fa-times me-1"></i> {{ __('ai::admin.cancel') }}
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <!-- View Mode -->
                                            <div class="border-top pt-3 mt-2">
                                                <div class="mb-3">
                                                    @if($directive->directive_type === 'json' || strlen($directive->directive_value) > 100)
                                                        <details class="mb-0">
                                                            <summary class="text-muted cursor-pointer">
                                                                <small>
                                                                    <i class="fa fa-chevron-right me-1"></i>
                                                                    View full value ({{ strlen($directive->directive_value) }} chars)
                                                                </small>
                                                            </summary>
                                                            <pre class="bg-light p-2 rounded mt-2 small"><code>{{ $directive->directive_value }}</code></pre>
                                                        </details>
                                                    @else
                                                        <div class="bg-light p-2 rounded">
                                                            <code class="small">{{ $directive->directive_value }}</code>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="btn-group w-100">
                                                    <button wire:click="editDirective({{ $directive->id }})"
                                                            class="btn btn-outline-primary btn-sm">
                                                        <i class="fa fa-edit"></i> {{ __('ai::admin.edit') }}
                                                    </button>
                                                    <button wire:click="toggleStatus({{ $directive->id }})"
                                                            class="btn btn-outline-secondary btn-sm"
                                                            title="{{ $directive->is_active ? __('ai::admin.deactivate') : __('ai::admin.activate') }}">
                                                        <i class="fa fa-power-off"></i>
                                                    </button>
                                                    <button wire:click="deleteDirective({{ $directive->id }})"
                                                            onclick="return confirm('{{ __('ai::admin.workflow.confirm_delete') }}')"
                                                            class="btn btn-outline-danger btn-sm">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fa fa-cog"></i>
                    </div>
                    <p class="empty-title">{{ __('ai::admin.workflow.no_directives') }}</p>
                    <p class="empty-subtitle text-muted">
                        Start by creating your first directive
                    </p>
                    <div class="empty-action">
                        <button wire:click="openNewModal" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i>
                            {{ __('ai::admin.workflow.new_directive') }}
                        </button>
                    </div>
                </div>
            @endforelse
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
                            <input type="text" wire:model.defer="directiveKey" class="form-control"
                                   placeholder="e.g. max_products_per_response">
                            @error('directiveKey')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label required">Directive Value</label>
                                    @if($directiveType === 'json')
                                        <textarea wire:model.defer="directiveValue" class="form-control font-monospace"
                                                  rows="5" placeholder='{"key": "value"}'></textarea>
                                    @else
                                        <input type="text" wire:model.defer="directiveValue" class="form-control"
                                               placeholder="e.g. 5">
                                    @endif
                                    @error('directiveValue')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required">Type</label>
                                    <select wire:model="directiveType" class="form-select">
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
                                <option value="ai_config">AI Configuration</option>
                                <option value="chat">Chat Settings</option>
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
                            <textarea wire:model.defer="directiveDescription" class="form-control" rows="3"
                                      placeholder="What does this directive control?"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-check form-switch">
                                <input type="checkbox" wire:model.defer="isActive" class="form-check-input" checked>
                                <span class="form-check-label">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" wire:click="closeNewModal">{{ __('ai::admin.cancel') }}</button>
                        <button type="button" class="btn btn-primary" wire:click="saveDirective">
                            <i class="fa fa-check me-1"></i>
                            {{ __('ai::admin.save') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
