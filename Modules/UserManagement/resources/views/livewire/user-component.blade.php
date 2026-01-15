@php
    View::share('pretitle', 'Kullanıcı Listesi');
@endphp
@include('usermanagement::helper')
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
                        placeholder="{{ __('usermanagement::admin.search_name_email') }}">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, roleFilter, statusFilter, subscriptionFilter, viewType, toggleActive, toggleEmailVerification"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px; z-index: 10;">
                    <div class="small text-muted mb-2">{{ __('usermanagement::admin.updating') }}</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Filtreler ve Options) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Rol Filtresi -->
                    <div style="width: 140px; min-width: 140px">
                        <select wire:model.live="roleFilter" class="form-control listing-filter-select"
                                data-choices
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value=""><nobr>{{ __('usermanagement::admin.all_roles') }}</nobr></option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}"><nobr>{{ $role->name }}</nobr></option>
                            @endforeach
                        </select>
                    </div>
                    @if(tenant() && tenant()->id == 1001)
                    <!-- Üyelik Durumu Filtresi -->
                    <div style="width: 160px; min-width: 160px">
                        <select wire:model.live="subscriptionFilter" class="form-control listing-filter-select"
                                data-choices
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value=""><nobr><i class="fas fa-crown"></i> Tüm Üyelikler</nobr></option>
                            <option value="active"><nobr>✅ Aktif Üyelik</nobr></option>
                            <option value="expired"><nobr>❌ Süresi Dolmuş</nobr></option>
                            <option value="free"><nobr>👤 Ücretsiz</nobr></option>
                        </select>
                    </div>
                    @endif
                    <!-- Görünüm Değiştirme -->
                    <div class="btn-group">
                        <button type="button"
                            class="btn {{ $viewType == 'grid' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'grid')" title="{{ __('usermanagement::admin.grid_view') }}">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button"
                            class="btn {{ $viewType == 'list' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'list')" title="{{ __('usermanagement::admin.list_view') }}">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="width: 80px; min-width: 80px">
                        <select wire:model.live="perPage" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="12"><nobr>12</nobr></option>
                            <option value="48"><nobr>48</nobr></option>
                            <option value="99"><nobr>99</nobr></option>
                            <option value="498"><nobr>498</nobr></option>
                            <option value="999"><nobr>999</nobr></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Görünümü -->
        @if($viewType == 'grid')
        <div class="row row-cards">
            @forelse($users as $user)
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <div class="card-body p-4 text-center">
                        <!-- Avatar -->
                        <div class="position-relative d-inline-block mb-3">
                            <span class="avatar avatar-xl avatar-rounded bg-primary-lt">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                            </span>
                            <!-- Email Doğrulama Badge -->
                            @if($user->email_verified_at)
                                <span class="badge bg-success position-absolute rounded-circle p-1"
                                      style="bottom: 0; right: 0; width: 24px; height: 24px;"
                                      data-bs-toggle="tooltip"
                                      title="Email doğrulanmış">
                                    <i class="fas fa-check text-white" style="font-size: 12px;"></i>
                                </span>
                            @else
                                <button wire:click="toggleEmailVerification({{ $user->id }})"
                                        class="badge bg-danger position-absolute rounded-circle p-1 border-0"
                                        style="bottom: 0; right: 0; width: 24px; height: 24px; cursor: pointer;"
                                        data-bs-toggle="tooltip"
                                        title="Email doğrulanmamış - Tıklayarak doğrulayın"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleEmailVerification({{ $user->id }})">
                                    <div wire:loading.remove wire:target="toggleEmailVerification({{ $user->id }})">
                                        <i class="fas fa-exclamation text-white" style="font-size: 12px;"></i>
                                    </div>
                                    <div wire:loading wire:target="toggleEmailVerification({{ $user->id }})" class="spinner-border spinner-border-sm text-white" style="width: 12px; height: 12px;"></div>
                                </button>
                            @endif
                        </div>
                        <!-- Kullanıcı Bilgileri -->
                        <h3 class="card-title m-0 mb-1">{{ $user->name }}</h3>
                        <div class="text-muted">{{ $user->email }}</div>
                        @if($user->phone)
                            <div class="small mt-1">
                                <a href="tel:{{ $user->phone }}" class="text-reset text-decoration-none">
                                    <i class="fas fa-phone text-primary me-1" style="font-size: 11px;"></i>{{ $user->phone }}
                                </a>
                            </div>
                        @endif
                        <!-- Roller ve Durum -->
                        <div class="mt-3">
                            @if($user->roles && count($user->roles) > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                @endforeach
                            @endif
                            <span class="badge mt-2 {{ $user->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                {{ $user->is_active ? __('usermanagement::admin.active') : __('usermanagement::admin.passive') }}
                            </span>
                            @if(tenant() && tenant()->id == 1001)
                                @if($user->subscription_expires_at)
                                    @php
                                        $expiry = \Carbon\Carbon::parse($user->subscription_expires_at);
                                        $isExpired = $expiry->isPast();
                                        $daysLeft = $isExpired ? 0 : (int) now()->diffInDays($expiry);
                                    @endphp
                                    @if($isExpired)
                                        <span class="badge mt-2 bg-red-lt" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y') }}">
                                            <i class="fas fa-times-circle me-1"></i>Süresi Doldu
                                        </span>
                                    @else
                                        <span class="badge mt-2 bg-yellow-lt" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y H:i') }}">
                                            <i class="fas fa-crown me-1"></i>{{ $daysLeft }}g
                                        </span>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                    <!-- İşlem Butonları -->
                    <div class="d-flex">
                        <a href="{{ route('admin.usermanagement.manage', $user->id) }}" class="card-btn">
                            <i class="fas fa-edit me-2"></i>
                            {{ __('usermanagement::admin.edit') }}
                        </a>

                        @if(auth()->user()->isRoot() || !$user->isRoot())
                        <a href="{{ route('admin.usermanagement.user.activity.logs', $user->id) }}" class="card-btn">
                            <i class="fas fa-history me-2"></i>
                            {{ __('usermanagement::admin.records') }}
                        </a>
                        @endif

                        <a href="javascript:void(0);"
                            wire:click="$dispatch('showDeleteModal', { userId: {{ $user->id }}, userName: '{{ $user->name }}' })"
                            class="card-btn text-danger">
                            <i class="fas fa-trash me-2"></i>
                            {{ __('usermanagement::admin.delete') }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">{{ __('usermanagement::admin.no_records') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('usermanagement::admin.no_records_text') }}
                    </p>
                </div>
            </div>
            @endforelse
        </div>
        @else
        <!-- Liste Görünümü -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <button
                                class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('id')">
                                {{ __('usermanagement::admin.id') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('name')">
                                {{ __('usermanagement::admin.name_surname') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('email')">
                                {{ __('usermanagement::admin.email_address') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'phone' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('phone')">
                                <i class="fas fa-phone me-1"></i>Telefon
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">
                            <button
                                class="table-sort {{ $sortField === 'role' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('role')">
                                {{ __('usermanagement::admin.role') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 50px" data-bs-toggle="tooltip" title="Email Doğrulama">
                            <button
                                class="table-sort {{ $sortField === 'email_verified_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('email_verified_at')">
                                <i class="fas fa-envelope-circle-check"></i>
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                {{ __('usermanagement::admin.status') }}
                            </button>
                        </th>
                        @if(tenant() && tenant()->id == 1001)
                        <th class="text-center" style="width: 140px">
                            <button
                                class="table-sort {{ $sortField === 'subscription_expires_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('subscription_expires_at')">
                                <i class="fas fa-crown me-1"></i>Üyelik
                            </button>
                        </th>
                        @endif
                        <th class="text-center" style="width: 160px">{{ __('usermanagement::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="hover-trigger" wire:key="row-{{ $user->id }}">
                        <td class="small">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->phone)
                                <a href="tel:{{ $user->phone }}" class="text-reset text-decoration-none">
                                    <i class="fas fa-phone text-primary me-1" style="font-size: 11px;"></i>{{ $user->phone }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($user->roles && count($user->roles) > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->email_verified_at)
                                <span class="text-success"
                                      data-bs-toggle="tooltip"
                                      data-bs-placement="top"
                                      title="Email doğrulanmış ✓">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </span>
                            @else
                                <button wire:click="toggleEmailVerification({{ $user->id }})"
                                        class="border-0 bg-transparent p-0 text-danger"
                                        style="cursor: pointer;"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Email doğrulanmamış - Tıklayarak doğrulayın"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleEmailVerification({{ $user->id }})">
                                    <div wire:loading.remove wire:target="toggleEmailVerification({{ $user->id }})">
                                        <i class="fas fa-times-circle fa-lg"></i>
                                    </div>
                                    <div wire:loading wire:target="toggleEmailVerification({{ $user->id }})" class="spinner-border spinner-border-sm"></div>
                                </button>
                            @endif
                        </td>
                        <td class="text-center">
                            <button wire:click="toggleActive({{ $user->id }})"
                                class="border-0 bg-transparent p-0 {{ $user->is_active ? 'text-success' : 'text-danger' }}"
                                style="cursor: pointer;"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="{{ $user->is_active ? 'Aktif - Tıklayarak pasif yapın' : 'Pasif - Tıklayarak aktif yapın' }}"
                                wire:loading.attr="disabled"
                                wire:target="toggleActive({{ $user->id }})">
                                <div wire:loading wire:target="toggleActive({{ $user->id }})" class="spinner-border spinner-border-sm"></div>
                                <div wire:loading.remove wire:target="toggleActive({{ $user->id }})">
                                    @if($user->is_active)
                                    <i class="fas fa-check-circle fa-lg"></i>
                                    @else
                                    <i class="fas fa-times-circle fa-lg"></i>
                                    @endif
                                </div>
                            </button>
                        </td>
                        @if(tenant() && tenant()->id == 1001)
                        <td class="text-center">
                            @if($user->subscription_expires_at)
                                @php
                                    $expiry = \Carbon\Carbon::parse($user->subscription_expires_at);
                                    $isExpired = $expiry->isPast();
                                    $daysLeft = $isExpired ? 0 : (int) now()->diffInDays($expiry);
                                @endphp
                                @if($isExpired)
                                    <span class="badge bg-red-lt text-red" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y') }}">
                                        <i class="fas fa-times-circle me-1"></i>Süresi Doldu
                                    </span>
                                @elseif($daysLeft <= 7)
                                    <span class="badge bg-orange-lt text-orange" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y H:i') }}">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ $daysLeft }}g
                                    </span>
                                @elseif($daysLeft <= 30)
                                    <span class="badge bg-yellow-lt text-yellow" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y H:i') }}">
                                        <i class="fas fa-clock me-1"></i>{{ $daysLeft }}g
                                    </span>
                                @else
                                    <span class="badge bg-green-lt text-green" data-bs-toggle="tooltip" title="{{ $expiry->format('d.m.Y H:i') }}">
                                        <i class="fas fa-crown me-1"></i>{{ $daysLeft }}g
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary-lt text-muted">
                                    <i class="fas fa-user me-1"></i>Ücretsiz
                                </span>
                            @endif
                        </td>
                        @endif
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('admin.usermanagement.manage', $user->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('usermanagement::admin.edit') }}">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        @if(auth()->user()->isRoot() || !$user->isRoot())
                                        <a href="{{ route('admin.usermanagement.user.activity.logs', $user->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="{{ __('usermanagement::admin.activity_records') }}">
                                            <i class="fa-solid fa-history link-secondary fa-lg"></i>
                                        </a>
                                        @endif
                                    </div>
                                    <div class="col lh-1">
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);"
                                                    wire:click="$dispatch('showDeleteModal', { userId: {{ $user->id }}, userName: '{{ $user->name }}' })"
                                                    class="dropdown-item link-danger">
                                                    {{ __('usermanagement::admin.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ (tenant() && tenant()->id == 1001) ? 9 : 8 }}">
                            <div class="empty">
                                <p class="empty-title">{{ __('usermanagement::admin.no_records') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('usermanagement::admin.no_records_text') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    {{ $users->links() }}

    <livewire:modals.user-delete-modal />
</div>

