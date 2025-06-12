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
                        placeholder="İsim veya e-posta ile ara...">
                </div>
            </div>
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, roleFilter, statusFilter, viewType, deleteUser, toggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center"
                    style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Filtreler ve Options) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Rol Filtresi -->
                    <div style="width: 150px">
                        <select wire:model.live="roleFilter" class="form-select">
                            <option value="">Tüm Roller</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Durum Filtresi -->
                    <div style="width: 120px">
                        <select wire:model.live="statusFilter" class="form-select">
                            <option value="">Tüm Durumlar</option>
                            <option value="1">Aktif</option>
                            <option value="0">Pasif</option>
                        </select>
                    </div>
                    <!-- Görünüm Değiştirme -->
                    <div class="btn-group">
                        <button type="button"
                            class="btn {{ $viewType == 'grid' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'grid')" title="Grid Görünüm">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button"
                            class="btn {{ $viewType == 'list' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'list')" title="Liste Görünüm">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
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
                        <span class="avatar avatar-xl mb-3 avatar-rounded bg-primary-lt">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 2, 'UTF-8'), 'UTF-8') }}
                        </span>
                        <!-- Kullanıcı Bilgileri -->
                        <h3 class="m-0 mb-1">{{ $user->name }}</h3>
                        <div class="text-muted">{{ $user->email }}</div>
                        <!-- Roller ve Durum -->
                        <div class="mt-3">
                            @if($user->roles && count($user->roles) > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                @endforeach
                            @endif
                            <span class="badge mt-2 {{ $user->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                {{ $user->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </div>
                    </div>
                    <!-- İşlem Butonları -->
                    <div class="d-flex">
                        <a href="{{ route('admin.usermanagement.manage', $user->id) }}" class="card-btn">
                            <i class="fas fa-edit me-2"></i>
                            Düzenle
                        </a>

                        @if(auth()->user()->isRoot() || !$user->isRoot())
                        <a href="{{ route('admin.usermanagement.user.activity.logs', $user->id) }}" class="card-btn">
                            <i class="fas fa-history me-2"></i>
                            Kayıtlar
                        </a>
                        @endif

                        <a href="javascript:void(0);"
                            wire:click="$dispatch('showDeleteModal', { userId: {{ $user->id }}, userName: '{{ $user->name }}' })"
                            class="card-btn text-danger">
                            <i class="fas fa-trash me-2"></i>
                            Sil
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">Kayıt bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun kayıt bulunmamaktadır.
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
                                ID
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('name')">
                                İsim Soyisim
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'email' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('email')">
                                E-posta
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">
                            <button
                                class="table-sort {{ $sortField === 'role' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('role')">
                                Rol
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">
                            <button
                                class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('is_active')">
                                Durum
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="hover-trigger" wire:key="row-{{ $user->id }}">
                        <td class="small">{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->roles && count($user->roles) > 0)
                                @foreach($user->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            <button wire:click="toggleActive({{ $user->id }})"
                                class="btn btn-icon btn-sm {{ $user->is_active ? 'text-success' : 'text-danger' }}">
                                <div wire:loading wire:target="toggleActive({{ $user->id }})"
                                    class="spinner-border spinner-border-sm">
                                </div>
                                <div wire:loading.remove wire:target="toggleActive({{ $user->id }})">
                                    @if($user->is_active)
                                    <i class="fas fa-check"></i>
                                    @else
                                    <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('admin.usermanagement.manage', $user->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Düzenle">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col">
                                        @if(auth()->user()->isRoot() || !$user->isRoot())
                                        <a href="{{ route('admin.usermanagement.user.activity.logs', $user->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="İşlem Kayıtları">
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
                                                    Sil
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
                        <td colspan="6">
                            <div class="empty">
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Arama kriterlerinize uygun kayıt bulunmamaktadır.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        <!-- Pagination -->
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>

    <livewire:modals.user-delete-modal />
</div>