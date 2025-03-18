@include('modulemanagement::helper')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Sol Taraf (Arama ve Filtreler) -->
            <div class="col-md-8">
                <div class="row g-2">
                    <!-- Arama Kutusu -->
                    <div class="col-md-6">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Modül ara...">
                        </div>
                    </div>
                    <!-- Durum Filtresi -->
                    <!-- Durum Filtresi yerine Type ve Group filtreleri -->
                    <div class="col-md-3">
                        <select wire:model.live="typeFilter" class="form-select">
                            <option value="">Tüm Tipler</option>
                            <option value="content">İçerik Modülü</option>
                            <option value="management">Yönetim Modülü</option>
                            <option value="system">Sistem Modülü</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="groupFilter" class="form-select">
                            <option value="">Tüm Gruplar</option>
                            @foreach($groups as $group)
                            <option value="{{ $group }}">{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col-md-2 position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, statusFilter, toggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="progress" style="height: 2px;">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col-md-2">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <button wire:click="toggleDomains" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip"
                        title="{{ $showDomains ? 'Domainleri Gizle' : 'Domainleri Göster' }}">
                        <i class="fas fa-globe"></i>
                    </button>

                    <select wire:model.live="perPage" class="form-select" style="max-width: 80px">
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
                                <button class="dropdown-item text-danger" wire:click="$dispatch('showDeleteModal', {
                                                        module: 'module',
                                                        id: {{ $module->module_id }},
                                                        title: '{{ $module->display_name }}'
                                                    })">
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
                        $isActive = isset($module->domains[$domain]) && $module->domains[$domain] === true;
                        @endphp
                        <div class="list-group-item py-2 list-group-item-action">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 bg-{{ $isActive ? 'blue' : 'secondary' }}-lt">
                                    <i class="fas fa-globe fa-sm"></i>
                                </span>
                                <div class="flex-fill">{{ $domain }}</div>
                                <div class="pretty p-switch p-slim">
                                    <input type="checkbox"
                                        wire:click="toggleDomainStatus({{ $module->module_id }}, '{{ $domain }}')"
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
                                $activeDomains = collect($module->domains ?? [])
                                    ->filter(function ($active, $domain) {
                                        return $active === true;
                                    })
                                    ->keys()
                                    ->take(3);
                                @endphp
                                
                                @forelse($activeDomains as $domain)
                                <span class="badge bg-blue-lt">{{ $domain }}</span>
                                @empty
                                <span class="badge bg-secondary-lt">Atanmamış</span>
                                @endforelse
                                
                                @if(collect($module->domains ?? [])->filter()->count() > 3)
                                <span class="badge bg-blue-lt">
                                    +{{ collect($module->domains ?? [])->filter()->count() - 3 }}
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
                                @if($module->group)
                                <span class="badge bg-primary text-white">{{ $module->group }}</span>
                                @endif
                                <span
                                    class="badge bg-{{ $module->type === 'system' ? 'red' : ($module->type === 'management' ? 'yellow' : 'green') }}-lt">
                                    {{ ucfirst($module->type) }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        wire:click="toggleActive({{ $module->module_id }})"
                                        {{ $module->is_active ? 'checked' : '' }}>
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