@php
    View::share('pretitle', 'Tema Listesi');
@endphp

@include('thememanagement::helper')
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
                        placeholder="{{ __('thememanagement::admin.search_placeholder') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, toggleTenantAccess, selectTheme, saveThemeSettings"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('admin.loading') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    @if($isCentral)
                    <!-- Tenant Gösterim - Sadece Central -->
                    <button wire:click="toggleTenants" class="btn btn-outline-primary btn-icon" data-bs-toggle="tooltip"
                        title="{{ $showTenants ? 'Tenant Gizle' : 'Tenant Göster' }}">
                        <i class="fas fa-building"></i>
                    </button>
                    @endif
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 60px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($currentTenantId && !$isCentral)
        <!-- Tenant Tema Seçimi Paneli -->
        <div class="alert alert-info mb-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Tema Seçimi:</strong> Aşağıdan temanızı seçin. Seçtiğiniz tema otomatik kaydedilir.
                </div>
            </div>
        </div>
        @endif
        
        <!-- Tema Kartları -->
        <div class="row row-cards">
            @forelse($themes as $theme)
            @php
                // Tenant için bu tema erişilebilir mi?
                $isAvailable = $currentTenantId ? $theme->isAvailableForTenant($currentTenantId) : true;
                $isSelected = $selectedThemeId == $theme->theme_id;
            @endphp

            @if($isCentral || $isAvailable)
            <div class="col-md-4 col-xl-3" wire:key="theme-{{ $theme->theme_id }}">
                <div class="card {{ $theme->is_default ? 'card-active' : '' }} {{ $isSelected ? 'border-primary border-2' : '' }} position-relative">

                    @if($isSelected && !$isCentral)
                    <div class="ribbon ribbon-top ribbon-bookmark bg-primary">
                        <i class="fas fa-check"></i>
                    </div>
                    @endif

                    <!-- Tema Önizleme Görseli -->
                    @if($theme->getFirstMedia('images'))
                    <div class="card-img-top img-responsive img-responsive-16x9" style="background-image: url({{ url($theme->getFirstMedia('images')->getUrl()) }}); background-size: cover; background-position: center; height: 160px;"></div>
                    @else
                    <div class="card-img-top img-responsive img-responsive-16x9 bg-primary-lt d-flex align-items-center justify-content-center" style="height: 160px;">
                        <i class="fas fa-palette fa-3x text-primary opacity-50"></i>
                    </div>
                    @endif

                    @if(!$isCentral && $isAvailable)
                    <!-- Tema Seçim Butonu - Görsel üzerinde -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                         style="background: rgba(0,0,0,0.4); opacity: 0; transition: opacity 0.2s; cursor: pointer; z-index: 10;"
                         onmouseover="this.style.opacity='1'"
                         onmouseout="this.style.opacity='0'"
                         onclick="selectThemeWithCache({{ $theme->theme_id }})">
                        <span class="btn btn-primary btn-lg">
                            <i class="fas fa-check me-2"></i>
                            {{ $isSelected ? 'Seçili' : 'Seç' }}
                        </span>
                    </div>
                    @endif

                    <div class="card-body">
                        <h3 class="card-title">
                            @if($isCentral && $editingTitleId === $theme->theme_id)
                            <div class="d-flex align-items-center gap-2" x-data
                                @click.outside="$wire.updateTitleInline()">
                                <div class="flexible-input-wrapper">
                                    <input type="text" wire:model.defer="newTitle"
                                        class="form-control form-control-sm flexible-input"
                                        placeholder="{{ __('thememanagement::admin.new_title') }}" wire:keydown.enter="updateTitleInline"
                                        wire:keydown.escape="$set('editingTitleId', null)" x-init="$nextTick(() => {
                                                $el.focus();
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px';
                                            })" x-on:input="
                                                $el.style.width = '20px';
                                                $el.style.width = ($el.scrollWidth + 2) + 'px'
                                            " style="min-width: 60px; max-width: 100%;">
                                </div>
                                <button class="btn px-2 py-1 btn-outline-success" wire:click="updateTitleInline">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn px-2 py-1 btn-outline-danger"
                                    wire:click="$set('editingTitleId', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @else
                            <div class="d-flex align-items-center justify-content-between">
                                <span>{{ $theme->title }}</span>
                                @if($isCentral)
                                <button class="btn btn-sm px-2 py-1 edit-icon ms-2"
                                    wire:click="startEditingTitle({{ $theme->theme_id }}, '{{ $theme->title }}')">
                                    <i class="fas fa-pen"></i>
                                </button>
                                @endif
                            </div>
                            @endif
                        </h3>

                        <div class="mb-2">
                            <span class="badge bg-blue-lt">{{ $theme->name }}</span>
                            <span class="badge bg-purple-lt">{{ $theme->folder_name }}</span>
                            @if($isCentral)
                            <span class="badge bg-{{ empty($theme->available_for_tenants) || in_array('all', $theme->available_for_tenants ?? []) ? 'green' : 'orange' }}-lt">
                                {{ $theme->access_summary }}
                            </span>
                            @endif
                        </div>

                        <div class="text-muted small">
                            {{ Str::limit($theme->description, 80) }}
                        </div>
                    </div>

                    @if($isCentral)
                    <!-- Tenant Erişim Listesi - Sadece Central -->
                    <div class="list-group list-group-flush">
                        <div class="list-group-item py-2 bg-muted-lt">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-building text-muted me-2"></i>
                                <strong class="small">Tenant Erişimi</strong>
                            </div>
                        </div>

                        @if($showTenants)
                        @foreach($tenants as $tenant)
                        @php
                            $available = $theme->available_for_tenants ?? [];
                            $hasAccess = empty($available) || in_array('all', $available) || in_array($tenant->id, $available) || in_array((string)$tenant->id, $available);
                        @endphp
                        <div class="list-group-item py-2 list-group-item-action">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-xs me-2 bg-{{ $hasAccess ? 'success' : 'secondary' }}-lt">
                                    <i class="fas fa-building fa-sm"></i>
                                </span>
                                <div class="flex-fill small">{{ $tenant->title ?? $tenant->id }}</div>
                                <div class="pretty p-switch p-fill">
                                    <input type="checkbox"
                                        wire:click="toggleTenantAccess({{ $theme->theme_id }}, '{{ $tenant->id }}')"
                                        {{ $hasAccess ? 'checked' : '' }} />
                                    <div class="state p-success">
                                        <label></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="list-group-item py-2 px-2">
                            <div class="d-flex flex-wrap gap-1">
                                @php
                                    $available = $theme->available_for_tenants ?? [];
                                    if (empty($available) || in_array('all', $available)) {
                                        $accessTenants = $tenants->take(3);
                                        $totalCount = $tenants->count();
                                    } else {
                                        $accessTenants = $tenants->whereIn('id', $available)->take(3);
                                        $totalCount = count($available);
                                    }
                                @endphp

                                @forelse($accessTenants as $tenant)
                                <span class="badge bg-light-lt">{{ $tenant->title ?? $tenant->id }}</span>
                                @empty
                                <span class="badge bg-secondary-lt">Erişim yok</span>
                                @endforelse

                                @if($totalCount > 3)
                                <span class="badge bg-light-lt">+{{ $totalCount - 3 }}</span>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Kart Footer -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($theme->is_default)
                                <span class="badge bg-green">{{ __('admin.default') }}</span>
                                @endif
                                <span class="badge {{ $theme->is_active ? 'bg-blue' : 'bg-red' }}">
                                    {{ $theme->is_active ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </div>

                            @if($isCentral)
                            <div class="dropdown">
                                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="javascript:void(0);"
                                       onclick="openThemePreview('{{ $theme->name }}', '{{ $theme->title }}')"
                                       class="dropdown-item">
                                        <i class="fas fa-eye me-2"></i> {{ __('thememanagement::admin.preview') ?? 'Önizleme' }}
                                    </a>
                                    <a href="{{ route('admin.thememanagement.manage', $theme->theme_id) }}" class="dropdown-item">
                                        <i class="fas fa-edit me-2"></i> {{ __('admin.edit') }}
                                    </a>
                                    @if(!$theme->is_default)
                                    <a href="javascript:void(0);" wire:click="setDefault({{ $theme->theme_id }})" class="dropdown-item">
                                        <i class="fas fa-check-circle me-2"></i> {{ __('thememanagement::admin.set_as_default') }}
                                    </a>
                                    @endif
                                    <a href="javascript:void(0);" wire:click="toggleActive({{ $theme->theme_id }})" class="dropdown-item">
                                        <i class="fas {{ $theme->is_active ? 'fa-ban' : 'fa-check' }} me-2"></i>
                                        {{ $theme->is_active ? __('thememanagement::admin.deactivate') : __('thememanagement::admin.activate') }}
                                    </a>
                                    @if(!$theme->is_default)
                                    <a href="javascript:void(0);" wire:click="$dispatch('showDeleteModal', {
                                        module: 'thememanagement',
                                        id: {{ $theme->theme_id }},
                                        title: '{{ $theme->title }}'
                                    })" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> {{ __('admin.delete') }}
                                    </a>
                                    @else
                                    <a href="javascript:void(0);" class="dropdown-item text-muted" onclick="event.preventDefault();">
                                        <i class="fas fa-lock me-2"></i> {{ __('thememanagement::admin.default_theme_cannot_be_deleted') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @empty
            <div class="col-12">
                <div class="empty">
                    <div class="empty-img">
                        <img src="{{ asset('assets/static/illustrations/undraw_no_data_re_kwbl.svg') }}" height="128" alt="">
                    </div>
                    <p class="empty-title">{{ __('thememanagement::admin.no_theme_found') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('thememanagement::admin.no_theme_description') }}
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.thememanagement.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> {{ __('thememanagement::admin.add_new_theme') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $themes->links() }}
        </div>

        @if($currentTenantId && !$isCentral)
        <!-- Subheader Ayarları - Sadece dropdown -->
        <div class="card mt-4">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-layer-group text-muted me-2"></i>
                        <span>Subheader Stili:</span>
                    </div>
                    <div style="width: 200px;">
                        <select wire:model.live="selectedSubheaderStyle" wire:change="saveThemeSettings" class="form-select form-select-sm">
                            <option value="">Varsayılan</option>
                            <option value="glass">Glass (Şeffaf)</option>
                            <option value="minimal">Minimal</option>
                            <option value="hero">Hero (Büyük)</option>
                            <option value="colored">Colored (Renkli)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <livewire:modals.delete-modal />

    <!-- Theme Preview Modal -->
    <div class="modal modal-blur fade" id="modal-theme-preview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        <span id="preview-theme-title">Tema Önizleme</span>
                    </h5>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <!-- Device Selector -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="setPreviewDevice('desktop')" id="btn-desktop">
                                <i class="fas fa-desktop"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPreviewDevice('tablet')" id="btn-tablet">
                                <i class="fas fa-tablet-alt"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setPreviewDevice('mobile')" id="btn-mobile">
                                <i class="fas fa-mobile-alt"></i>
                            </button>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-0" style="height: 70vh; background: #f1f5f9;">
                    <div class="d-flex justify-content-center align-items-start h-100 p-3" id="preview-container">
                        <iframe id="theme-preview-iframe"
                                src=""
                                style="width: 100%; height: 100%; border: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1); background: white;"
                                loading="lazy">
                        </iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Önizleme mevcut sitenin temasıyla gösterilir
                        </div>
                        <button type="button" class="btn" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentDevice = 'desktop';

        // Tema seç ve cache temizle
        async function selectThemeWithCache(themeId) {
            // Toast'ları geçici olarak devre dışı bırak
            const originalShowToast = window.showToast;
            window.showToast = function() {}; // Sustur

            try {
                // 1. Önce cache temizle (eski tema cache'ini sil)
                await fetch('/admin/cache/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // 2. Livewire ile tema kaydet
                await @this.call('selectTheme', themeId);

                // 3. Tekrar cache temizle (yeni tema ile oluşan cache'i de sil)
                await fetch('/admin/cache/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                // Toast'ı geri aç ve tek mesaj göster
                window.showToast = originalShowToast;
                if (typeof window.showToast === 'function') {
                    window.showToast('Başarılı', 'Tema değiştirildi', 'success');
                }

            } catch (error) {
                console.error('Theme change error:', error);
                window.showToast = originalShowToast;
                if (typeof window.showToast === 'function') {
                    window.showToast('Hata', 'Tema değiştirilemedi', 'error');
                }
            }
        }

        function openThemePreview(themeName, themeTitle) {
            const modal = new bootstrap.Modal(document.getElementById('modal-theme-preview'));
            const iframe = document.getElementById('theme-preview-iframe');
            const titleEl = document.getElementById('preview-theme-title');

            // Set title
            titleEl.textContent = themeTitle + ' - Önizleme';

            // Load preview URL (homepage with theme parameter)
            // Using current domain to preview
            const previewUrl = '/?theme_preview=' + encodeURIComponent(themeName);
            iframe.src = previewUrl;

            // Reset to desktop view
            setPreviewDevice('desktop');

            modal.show();
        }

        function setPreviewDevice(device) {
            currentDevice = device;
            const iframe = document.getElementById('theme-preview-iframe');
            const container = document.getElementById('preview-container');

            // Remove active class from all buttons
            document.querySelectorAll('#btn-desktop, #btn-tablet, #btn-mobile').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active class to selected button
            document.getElementById('btn-' + device).classList.add('active');

            // Set iframe dimensions based on device
            switch(device) {
                case 'mobile':
                    iframe.style.width = '375px';
                    iframe.style.maxWidth = '375px';
                    break;
                case 'tablet':
                    iframe.style.width = '768px';
                    iframe.style.maxWidth = '768px';
                    break;
                default: // desktop
                    iframe.style.width = '100%';
                    iframe.style.maxWidth = '100%';
            }
        }

        // Cleanup iframe on modal close
        document.getElementById('modal-theme-preview')?.addEventListener('hidden.bs.modal', function() {
            document.getElementById('theme-preview-iframe').src = '';
        });
    </script>
    @endpush
</div>