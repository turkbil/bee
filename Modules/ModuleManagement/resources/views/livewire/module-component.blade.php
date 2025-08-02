@include('modulemanagement::helper')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="{{ __('modulemanagement::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, selectedItems, selectAll, bulkDelete, bulkToggleActive, typeFilter, toggleDomains"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('modulemanagement::admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Switch ve Select) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Tip Filtresi -->
                    <div style="width: 150px">
                        <select wire:model.live="typeFilter" class="form-select listing-filter-select">
                            <option value="">{{ __('modulemanagement::admin.all_types') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Domain Gösterim -->
                    <button wire:click="toggleDomains" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip"
                        title="{{ $showDomains ? __('modulemanagement::admin.hide_domains') : __('modulemanagement::admin.show_domains') }}">
                        <i class="fas fa-globe"></i>
                    </button>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 60px">
                        <select wire:model.live="perPage" class="form-select listing-filter-select">
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modül Listesi -->
        <div class="row row-cards">
            @php
            $groupedModules = $modules->groupBy('type');
            $typeOrder = ['content', 'ai', 'widget', 'management', 'system'];
            @endphp

            @forelse($typeOrder as $type)
            @if($groupedModules->has($type))
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center p-2 bg-muted-lt rounded">
                    @switch($type)
                    @case('system')
                    <i class="fas fa-shield-alt me-2 text-muted"></i>
                    <h3 class="mb-0 h4">{{ __('modulemanagement::admin.system_modules') }}</h3>
                    @break
                    @case('management')
                    <i class="fas fa-cogs me-2 text-muted"></i>
                    <h3 class="mb-0 h4">{{ __('modulemanagement::admin.management_modules') }}</h3>
                    @break
                    @case('content')
                    <i class="fas fa-file-alt me-2 text-muted"></i>
                    <h3 class="mb-0 h4">{{ __('modulemanagement::admin.content_modules') }}</h3>
                    @break
                    @case('ai')
                    <i class="fas fa-stars me-2 text-muted"></i>
                    <h3 class="mb-0 h4">{{ __('modulemanagement::admin.ai_modules') }}</h3>
                    @break
                    @case('widget')
                    <i class="fas fa-puzzle-piece me-2 text-muted"></i>
                    <h3 class="mb-0 h4">{{ __('modulemanagement::admin.widget_modules') }}</h3>
                    @break
                    @endswitch
                    <div class="ms-auto">
                        <span class="badge bg-primary">
                            {{ $groupedModules[$type]->count() }} {{ __('modulemanagement::admin.module_count') }}
                        </span>
                    </div>
                </div>
            </div>

            @foreach($groupedModules[$type] as $module)
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card module-card">
                    <!-- Kart Header -->
                    <div class="card-header d-flex align-items-center">
                        <div class="me-auto">
                            <h3 class="card-title mb-0">{{ $module->display_name }}</h3>
                        </div>
                        <div class="dropdown">
                            <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($module->settings)
                                {{-- Ayarlar bağlantısını geçici olarak kaldır veya kontrol ekle --}}
                                @if(Route::has('admin.settingmanagement.values'))
                                <a href="{{ route('admin.settingmanagement.values', ['group' => $module->settings]) }}"
                                    class="dropdown-item">
                                    <i class="fas fa-cog me-2" style="width: 14px;"></i>{{ __('modulemanagement::admin.settings') }}
                                </a>
                                @endif
                                @endif

                                @if($module->type === 'content')
                                <a href="{{ route('admin.modulemanagement.slug-manage', $module->name) }}"
                                    class="dropdown-item">
                                    <i class="fas fa-link me-2" style="width: 14px;"></i>{{ __('modulemanagement::admin.url_settings') }}
                                </a>
                                @endif

                                <a href="{{ route('admin.modulemanagement.manage', $module->module_id) }}"
                                    class="dropdown-item">
                                    <i class="fas fa-edit me-2" style="width: 14px;"></i>{{ __('modulemanagement::admin.edit') }}
                                </a>
                                <button class="dropdown-item text-danger" wire:click="$dispatch('showDeleteModal', {'module': 'module', 'id': {{ $module->module_id }}, 'title': '{{ $module->display_name }}'})">
                                    <i class="fas fa-trash me-2" style="width: 14px;"></i>{{ __('modulemanagement::admin.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Domain Listesi -->
                    <div class="list-group list-group-flush">
                        <div class="list-group-item py-2 bg-muted-lt">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-globe text-muted me-2"></i>
                                <strong>{{ __('modulemanagement::admin.assigned_domains') }}</strong>
                            </div>
                        </div>

                        @if($showDomains)
                        @foreach($domains as $domain)
                        @php
                        $tenant = $module->tenants->where('id', $domain->id)->first();
                        $isActive = $tenant && $tenant->pivot->is_active;
                        @endphp
                        <div class="list-group-item py-2 list-group-item-action">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 bg-{{ $isActive ? 'secondary' : 'secondary' }}-lt">
                                    <i class="fas fa-globe fa-sm"></i>
                                </span>
                                <div class="flex-fill">{{ $domain->title ?? $domain->id }}</div>
                                <div class="pretty p-switch p-slim">
                                    <input type="checkbox"
                                        wire:click="toggleDomainStatus({{ $module->module_id }}, '{{ $domain->id }}')"
                                        {{ $isActive ? 'checked' : '' }} />
                                    <div class="state p-warning">
                                        <label></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="list-group-item py-3 px-2 mx-1">
                            <div class="domain-badges d-flex flex-wrap gap-2">
                                @php
                                $activeDomains = $module->tenants->where('pivot.is_active', true)->take(3);
                                @endphp
                                
                                @forelse($activeDomains as $tenant)
                                <span class="badge bg-light-lt">{{ $tenant->title ?? $tenant->id }}</span>
                                @empty
                                <span class="badge bg-secondary-lt">{{ __('modulemanagement::admin.unassigned') }}</span>
                                @endforelse
                                
                                @if($module->tenants->where('pivot.is_active', true)->count() > 3)
                                <span class="badge bg-light-lt">
                                    +{{ $module->tenants->where('pivot.is_active', true)->count() - 3 }}
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2">
                                <span class="text-muted small">
                                    {{ ucfirst($module->type) }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:click="toggleActive({{ $module->module_id }})"
                                        {{ $module->is_active ? 'checked' : '' }} value="1" />

                                    <div class="state p-success p-on ms-2">
                                        <label>{{ __('modulemanagement::admin.active') }}</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>{{ __('modulemanagement::admin.inactive') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('tabler/static/illustrations/undraw_quitting_time_dm8t.svg') }}"
                            height="128" alt="">
                    </div>
                    <p class="empty-title">{{ __('modulemanagement::admin.no_modules_found') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('modulemanagement::admin.add_new_module_info') }}
                    </p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($modules->hasPages())
    {{ $modules->links() }}
    @endif

    <livewire:modals.delete-modal />
</div>