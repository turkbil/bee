<div>
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
                    <div class="d-flex align-items-center p-3 bg-{{ $meta['color'] }}-lt rounded">
                        <i class="fa {{ $meta['icon'] }} me-3 text-{{ $meta['color'] }}" style="font-size: 1.25rem;"></i>
                        <h3 class="mb-0 h3">{{ $meta['title'] }}</h3>
                        <div class="ms-auto">
                            <span class="badge bg-light text-dark">
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
                            <div class="card-header d-flex align-items-center">
                                <div class="me-auto">
                                    <h3 class="card-title mb-1" style="font-size: 1rem;">
                                        <strong>{{ $directive->directive_key }}</strong>
                                    </h3>
                                    @if($directive->description)
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">{{ Str::limit($directive->description, 50) }}</p>
                                    @endif
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
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('ai::admin.workflow.value') }}</label>
                                            @if($directive->directive_type === 'json')
                                                <button type="button" class="btn btn-outline-primary w-100"
                                                        onclick="openJsonEditor({{ $directive->id }}, @js($directive->directive_value))">
                                                    <i class="fa fa-code me-2"></i>
                                                    JSON Düzenleyici Aç
                                                </button>
                                                <textarea wire:model.defer="directiveValue" class="form-control font-monospace mt-2"
                                                          rows="6" style="font-size: 0.875rem;"></textarea>
                                            @elseif(strlen($directive->directive_value) > 100)
                                                <textarea wire:model.defer="directiveValue" class="form-control"
                                                          rows="4" style="font-size: 0.875rem;"></textarea>
                                            @else
                                                <input type="text" wire:model.defer="directiveValue" class="form-control"
                                                       style="font-size: 0.875rem;">
                                            @endif
                                            @error('directiveValue')
                                                <div class="text-danger mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <label class="form-label">{{ __('ai::admin.workflow.type') }}</label>
                                                <select wire:model.defer="directiveType" class="form-select">
                                                    <option value="string">String</option>
                                                    <option value="integer">Integer</option>
                                                    <option value="boolean">Boolean</option>
                                                    <option value="json">JSON</option>
                                                    <option value="array">Array</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">{{ __('ai::admin.workflow.category') }}</label>
                                                <select wire:model.defer="directiveCategory" class="form-select">
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

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('ai::admin.description') }}</label>
                                            <textarea wire:model.defer="directiveDescription" class="form-control" rows="2"></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-check form-switch">
                                                <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                                <span class="form-check-label">{{ __('ai::admin.active') }}</span>
                                            </label>
                                        </div>

                                        <div class="btn-group w-100">
                                            <button wire:click="saveDirective" class="btn btn-success">
                                                <i class="fa fa-check me-2"></i> {{ __('ai::admin.save') }}
                                            </button>
                                            <button wire:click="cancelEdit" class="btn btn-secondary">
                                                <i class="fa fa-times me-2"></i> {{ __('ai::admin.cancel') }}
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- View Mode -->
                                    <div class="list-group-item py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark">{{ $directive->directive_type }}</span>
                                            <span class="badge bg-light text-dark">{{ ucfirst($directive->category) }}</span>
                                        </div>
                                    </div>
                                    <div class="list-group-item py-3">
                                        @if($directive->directive_type === 'json')
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">JSON Value:</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="viewJson(@js($directive->directive_value), '{{ $directive->directive_key }}')">
                                                    <i class="fa fa-eye me-1"></i> Görüntüle
                                                </button>
                                            </div>
                                            <code class="d-block p-2 bg-light rounded" style="font-size: 0.75rem; max-height: 100px; overflow: hidden;">{{ Str::limit($directive->directive_value, 150) }}</code>
                                        @elseif(strlen($directive->directive_value) > 80)
                                            <details>
                                                <summary class="cursor-pointer mb-2">
                                                    <strong>{{ Str::limit($directive->directive_value, 60) }}</strong>
                                                </summary>
                                                <div class="p-2 bg-light rounded mt-2">
                                                    <code style="font-size: 0.875rem; white-space: pre-wrap;">{{ $directive->directive_value }}</code>
                                                </div>
                                            </details>
                                        @else
                                            <div class="p-2 bg-light rounded">
                                                <code style="font-size: 0.875rem;">{{ $directive->directive_value }}</code>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer -->
                            @if($editingDirective !== $directive->id)
                                <div class="card-footer">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="pretty p-default p-curve p-toggle p-smooth">
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
                                <i class="fa fa-plus me-2"></i>
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
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label required">{{ __('ai::admin.workflow.value') }}</label>
                                @if($directiveType === 'json')
                                    <button type="button" class="btn btn-outline-primary w-100 mb-2"
                                            onclick="openJsonEditorNew()">
                                        <i class="fa fa-code me-2"></i>
                                        JSON Düzenleyici Aç
                                    </button>
                                    <textarea wire:model.defer="directiveValue" class="form-control font-monospace"
                                              rows="6" placeholder='{"key": "value"}'></textarea>
                                @else
                                    <input type="text" wire:model.defer="directiveValue" class="form-control"
                                           placeholder="e.g. 5">
                                @endif
                                @error('directiveValue')
                                    <span class="text-danger">{{ $message }}</span>
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
                        <i class="fa fa-check me-2"></i>
                        {{ __('ai::admin.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
@endif

<!-- JSON Viewer/Editor Modal -->
<div id="jsonModal" class="modal modal-blur" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">JSON Görüntüleyici</h5>
                <button type="button" class="btn-close" onclick="closeJsonModal()"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="p-3 bg-light rounded" style="font-size: 1rem; max-height: 70vh; overflow: auto;"><code></code></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeJsonModal()">{{ __('ai::admin.close') }}</button>
            </div>
        </div>
    </div>
</div>

    @push('scripts')
    <script>
    function viewJson(jsonString, title) {
        try {
            const jsonObj = typeof jsonString === 'string' ? JSON.parse(jsonString) : jsonString;
            const formatted = JSON.stringify(jsonObj, null, 2);
            document.querySelector('#jsonModal .modal-title').textContent = 'JSON: ' + title;
            document.querySelector('#jsonContent code').textContent = formatted;
            document.getElementById('jsonModal').style.display = 'block';
            document.body.classList.add('modal-open');
        } catch(e) {
            alert('JSON parse hatası: ' + e.message);
        }
    }

    function closeJsonModal() {
        document.getElementById('jsonModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function openJsonEditor(id, jsonString) {
        viewJson(jsonString, 'Directive #' + id);
    }

    function openJsonEditorNew() {
        const textarea = document.querySelector('[wire\\:model\\.defer="directiveValue"]');
        if (textarea && textarea.value) {
            viewJson(textarea.value, 'New Directive');
        } else {
            alert('Lütfen önce JSON değerini girin');
        }
    }
    </script>
    @endpush
</div>
