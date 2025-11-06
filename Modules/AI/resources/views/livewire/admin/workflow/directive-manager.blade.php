@include('ai::helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.debounce.500ms="search" class="form-control"
                           placeholder="{{ __('ai::admin.workflow.search_directives') }}">
                </div>
            </div>
            <!-- Loading -->
            <div class="col position-relative">
                <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('ai::admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Kategori Filtresi -->
                    <div style="width: 180px">
                        <select wire:model="filterCategory" class="form-select listing-filter-select">
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
                    <!-- İstatistik + Yeni Direktif Butonu -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">
                            {{ $totalCount }} {{ __('ai::admin.workflow.total_directives') }}
                        </span>
                        <button wire:click="openNewModal" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directive Cards by Category -->
        <div class="row row-cards">
            @forelse($groupedDirectives as $category => $directives)
                @php
                    $meta = $categoryMeta[$category] ?? [
                        'title' => ucfirst($category),
                        'icon' => 'fa-cog',
                        'color' => 'blue',
                        'description' => ''
                    ];
                @endphp

                <!-- Category Header -->
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center p-2 bg-{{ $meta['color'] }}-lt dark:bg-{{ $meta['color'] }}-lt rounded">
                        <i class="fa {{ $meta['icon'] }} me-2 text-{{ $meta['color'] }} dark:text-{{ $meta['color'] }}"></i>
                        <h3 class="mb-0 h4 text-gray-900 dark:text-white">{{ $meta['title'] }}</h3>
                        <div class="ms-auto">
                            <span class="badge bg-{{ $meta['color'] }} text-white">
                                {{ $directives->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Directive Cards -->
                @foreach($directives as $directive)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="card module-card">
                            <!-- Card Header -->
                            <div class="card-header d-flex align-items-center bg-{{ $meta['color'] }}-lt dark:bg-{{ $meta['color'] }}-lt">
                                <div class="me-auto">
                                    <h3 class="card-title mb-0">
                                        <code class="text-{{ $meta['color'] }} dark:text-{{ $meta['color'] }}">{{ $directive->directive_key }}</code>
                                    </h3>
                                </div>
                                <div class="dropdown">
                                    <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button wire:click="editDirective({{ $directive->id }})" class="dropdown-item">
                                            <i class="fas fa-edit me-2" style="width: 14px;"></i>{{ __('ai::admin.edit') }}
                                        </button>
                                        <button wire:click="toggleStatus({{ $directive->id }})" class="dropdown-item">
                                            <i class="fas fa-power-off me-2" style="width: 14px;"></i>
                                            {{ $directive->is_active ? __('ai::admin.deactivate') : __('ai::admin.activate') }}
                                        </button>
                                        <button wire:click="deleteDirective({{ $directive->id }})"
                                                onclick="return confirm('{{ __('ai::admin.workflow.confirm_delete') }}')"
                                                class="dropdown-item text-danger">
                                            <i class="fas fa-trash me-2" style="width: 14px;"></i>{{ __('ai::admin.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="list-group list-group-flush">
                                @if($editingDirective === $directive->id)
                                    <!-- Edit Mode -->
                                    <div class="list-group-item">
                                        <div class="mb-2">
                                            <label class="form-label form-label-sm">{{ __('ai::admin.workflow.value') }}</label>
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
                                                <label class="form-label form-label-sm">{{ __('ai::admin.workflow.type') }}</label>
                                                <select wire:model.defer="directiveType" class="form-select form-select-sm">
                                                    <option value="string">String</option>
                                                    <option value="integer">Integer</option>
                                                    <option value="boolean">Boolean</option>
                                                    <option value="json">JSON</option>
                                                    <option value="array">Array</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label form-label-sm">{{ __('ai::admin.workflow.category') }}</label>
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
                                            <label class="form-label form-label-sm">{{ __('ai::admin.description') }}</label>
                                            <textarea wire:model.defer="directiveDescription"
                                                      class="form-control form-control-sm"
                                                      rows="2"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                                <span class="form-check-label">{{ __('ai::admin.active') }}</span>
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
                                    <div class="list-group-item py-2">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-{{ $meta['color'] }} text-white me-2">{{ $directive->directive_type }}</span>
                                            @if($directive->description)
                                                <small class="text-gray-700 dark:text-gray-300">{{ Str::limit($directive->description, 40) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="list-group-item py-3 px-2 mx-1">
                                        @if($directive->directive_type === 'json' || strlen($directive->directive_value) > 80)
                                            <details class="mb-0">
                                                <summary class="text-gray-700 dark:text-gray-300 cursor-pointer">
                                                    <small>
                                                        <i class="fa fa-chevron-right me-1"></i>
                                                        {{ Str::limit($directive->directive_value, 60) }}
                                                    </small>
                                                </summary>
                                                <pre class="bg-light dark:bg-gray-800 p-2 rounded mt-2 small mb-0 text-gray-900 dark:text-gray-100"><code>{{ $directive->directive_value }}</code></pre>
                                            </details>
                                        @else
                                            <code class="small text-gray-900 dark:text-gray-100">{{ $directive->directive_value }}</code>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer -->
                            @if($editingDirective !== $directive->id)
                                <div class="card-footer">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex gap-2">
                                            <span class="text-gray-700 dark:text-gray-300 small">
                                                {{ ucfirst($directive->category) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                                <input type="checkbox" wire:click="toggleStatus({{ $directive->id }})"
                                                       {{ $directive->is_active ? 'checked' : '' }} value="1" />
                                                <div class="state p-success p-on ms-2">
                                                    <label>{{ __('ai::admin.active') }}</label>
                                                </div>
                                                <div class="state p-danger p-off ms-2">
                                                    <label>{{ __('ai::admin.inactive') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-img">
                            <img src="{{ asset('tabler/static/illustrations/undraw_quitting_time_dm8t.svg') }}"
                                 height="128" alt="">
                        </div>
                        <p class="empty-title">{{ __('ai::admin.workflow.no_directives') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('ai::admin.workflow.directives_subtitle') }}
                        </p>
                        <div class="empty-action">
                            <button wire:click="openNewModal" class="btn btn-primary">
                                <i class="fa fa-plus me-1"></i>
                                {{ __('ai::admin.workflow.new_directive') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- New Directive Modal -->
@if($showNewModal)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('ai::admin.workflow.new_directive') }}</h5>
                    <button type="button" class="btn-close" wire:click="closeNewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">{{ __('ai::admin.workflow.key') }}</label>
                        <input type="text" wire:model.defer="directiveKey" class="form-control"
                               placeholder="e.g. max_products_per_response">
                        @error('directiveKey')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label required">{{ __('ai::admin.workflow.value') }}</label>
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
                                <label class="form-label required">{{ __('ai::admin.workflow.type') }}</label>
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
                        <label class="form-label required">{{ __('ai::admin.workflow.category') }}</label>
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
                        <label class="form-label">{{ __('ai::admin.description') }}</label>
                        <textarea wire:model.defer="directiveDescription" class="form-control" rows="3"
                                  placeholder="What does this directive control?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" wire:model.defer="isActive" class="form-check-input" checked>
                            <span class="form-check-label">{{ __('ai::admin.active') }}</span>
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
