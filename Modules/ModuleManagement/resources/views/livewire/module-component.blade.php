@include('modulemanagement::helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Sol Taraf (Arama ve Filtreler) -->
            <div class="col-md-6">
                <div class="row g-2">
                    <!-- Arama Kutusu -->
                    <div class="col-md-8">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Modül ara...">
                        </div>
                    </div>
                    <!-- Tip Filtresi -->
                    <div class="col-md-4">
                        <select wire:model.live="typeFilter" class="form-select">
                            <option value="">Tüm Tipler</option>
                            <option value="content">İçerik Modülü</option>
                            <option value="management">Yönetim Modülü</option>
                            <option value="system">Sistem Modülü</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col-md-2 d-flex justify-content-center align-items-center">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, typeFilter, toggleActive"
                    class="text-center">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">İşleniyor...</span>
                    </div>
                    <span class="ms-2 text-muted">İşleniyor...</span>
                </div>
            </div>
            
            <!-- Sağ Taraf (Domain Gösterim ve Sayfalama) -->
            <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <button wire:click="toggleDomains" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip"
                        title="{{ $showDomains ? 'Domainleri Gizle' : 'Domainleri Göster' }}">
                        <i class="fas fa-globe"></i>
                    </button>

                    <select wire:model.live="perPage" class="form-select" style="width: 80px">
                        <option value="10">10</option>
                        <option value="40">40</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Modül Listesi -->
        <div class="row row-cards">
            @php
            $groupedModules = $modules->groupBy('type');
            $typeOrder = ['content', 'management', 'system'];
            @endphp

            @forelse($typeOrder as $type)
            @if($groupedModules->has($type))
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center p-2 bg-muted-lt rounded">
                    @switch($type)
                    @case('system')
                    <i class="fas fa-shield-alt me-2 text-muted"></i>
                    <h3 class="mb-0 h4">Sistem Modülleri</h3>
                    @break
                    @case('management')
                    <i class="fas fa-cogs me-2 text-muted"></i>
                    <h3 class="mb-0 h4">Yönetim Modülleri</h3>
                    @break
                    @case('content')
                    <i class="fas fa-file-alt me-2 text-muted"></i>
                    <h3 class="mb-0 h4">İçerik Modülleri</h3>
                    @break
                    @endswitch
                    <div class="ms-auto">
                        <span class="badge bg-primary">
                            {{ $groupedModules[$type]->count() }} modül
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
                            <a href="#" class="btn btn-ghost-secondary btn-icon btn-sm" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                @if($module->settings)
                                {{-- Ayarlar bağlantısını geçici olarak kaldır veya kontrol ekle --}}
                                @if(Route::has('admin.settingmanagement.values'))
                                <a href="{{ route('admin.settingmanagement.values', ['group' => $module->settings]) }}"
                                    class="dropdown-item">
                                    <i class="fas fa-cogs me-2"></i>Ayarlar
                                </a>
                                @endif
                                @endif

                                <a href="{{ route('admin.modulemanagement.manage', $module->module_id) }}"
                                    class="dropdown-item">
                                    <i class="fas fa-edit me-2"></i>Düzenle
                                </a>
                                <button class="dropdown-item text-danger" wire:click="$dispatch('showDeleteModal', ['module' => 'module', 'id' => {{ $module->module_id }}, 'title' => '{{ $module->display_name }}'])">
                                    <i class="fas fa-trash me-2"></i>Sil
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Domain Listesi -->
                    <div class="list-group list-group-flush">
                        <div class="list-group-item py-2 bg-muted-lt">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-globe text-muted me-2"></i>
                                <strong>Atanan Domainler</strong>
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
                                <span class="avatar avatar-xs me-2 bg-{{ $isActive ? 'blue' : 'secondary' }}-lt">
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
                                <span class="badge bg-blue-lt">{{ $tenant->title ?? $tenant->id }}</span>
                                @empty
                                <span class="badge bg-secondary-lt">Atanmamış</span>
                                @endforelse
                                
                                @if($module->tenants->where('pivot.is_active', true)->count() > 3)
                                <span class="badge bg-blue-lt">
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
                                <span
                                    class="badge bg-{{ $module->type === 'system' ? 'red' : ($module->type === 'management' ? 'yellow' : 'green') }}-lt">
                                    {{ ucfirst($module->type) }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                    <input type="checkbox" wire:click="toggleActive({{ $module->module_id }})"
                                        {{ $module->is_active ? 'checked' : '' }} value="1" />

                                    <div class="state p-success p-on ms-2">
                                        <label>Aktif</label>
                                    </div>
                                    <div class="state p-danger p-off ms-2">
                                        <label>Aktif Değil </label>
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
                    <p class="empty-title">Hiç modül bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Yeni bir modül eklemek için "Yeni Modül" butonunu kullanabilirsiniz
                    </p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    <!-- Pagination -->
    @if($modules->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-end">
        {{ $modules->links() }}
    </div>
    @endif

    <livewire:modals.delete-modal />
</div>