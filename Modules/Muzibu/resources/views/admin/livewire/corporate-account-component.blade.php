<div class="corporate-account-wrapper">
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-building"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['total_accounts']) }}</div>
                            <div class="text-muted">Toplam Hesap</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-blue text-white avatar">
                                <i class="fas fa-city"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['parent_accounts']) }}</div>
                            <div class="text-muted">Ana Firma</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['active_accounts']) }}</div>
                            <div class="text-muted">Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-secondary text-white avatar">
                                <i class="fas fa-pause"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['inactive_accounts']) }}</div>
                            <div class="text-muted">Pasif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Firma veya kod ara...">
                    </div>
                </div>

                <!-- Durum Filtresi -->
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Pasif</option>
                    </select>
                </div>

                <!-- Firma Sayısı -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center h-100">
                        <span class="text-muted">
                            <i class="fas fa-building me-2"></i>
                            <strong>{{ $this->corporateAccounts->total() }}</strong> firma
                        </span>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div class="col-md-3 position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Yeni Ekle Butonu -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createParentModal">
                            <i class="fas fa-plus me-1"></i>
                            Yeni Ana Firma
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px"></th>
                            <th style="width: 50px">#</th>
                            <th class="text-center" style="width: 60px">
                                <i class="fas fa-store text-muted" data-bs-toggle="tooltip" title="Şubeler"></i>
                            </th>
                            <th>Firma Adı</th>
                            <th>Hesap Sahibi</th>
                            <th>Kurumsal Kod</th>
                            <th style="width: 80px">Durum</th>
                            <th class="text-center" style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody x-data="{ openBranches: {} }">
                        @forelse($this->corporateAccounts as $account)
                            <!-- Ana Firma Satırı -->
                            <tr class="hover-trigger"
                                wire:key="parent-{{ $account->id }}"
                                x-init="openBranches[{{ $account->id }}] = false">
                                <td class="text-center">
                                    @if($account->children->count() > 0)
                                        <i class="fas fa-grip-vertical text-muted" style="cursor: grab;"></i>
                                    @endif
                                </td>
                                <td class="sort-id small">{{ $account->id }}</td>
                                <td class="text-center">
                                    @if($account->children->count() > 0)
                                        <button @click="openBranches[{{ $account->id }}] = !openBranches[{{ $account->id }}]"
                                            class="btn btn-sm btn-ghost-secondary"
                                            data-bs-toggle="tooltip"
                                            :title="openBranches[{{ $account->id }}] ? 'Şubeleri gizle' : 'Şubeleri göster'">
                                            <i class="fas fa-store fa-sm me-1"></i>
                                            <span class="badge bg-primary">{{ $account->children->count() }}</span>
                                            <i class="fas ms-1"
                                               :class="openBranches[{{ $account->id }}] ? 'fa-chevron-up' : 'fa-chevron-down'"
                                               style="font-size: 0.7rem;"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm bg-blue me-2">
                                            <i class="fas fa-building"></i>
                                        </span>
                                        <div>
                                            <div class="fw-medium">
                                                {{ $account->company_name }}
                                                <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">Ana Şube</span>
                                            </div>
                                            <small class="text-muted">{{ $account->children->count() }} alt şube</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $account->owner->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $account->owner->email ?? '' }}</small>
                                </td>
                                <td>
                                    <code class="user-select-all" style="font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Mono', 'Droid Sans Mono', monospace; letter-spacing: 1px;">{{ $account->corporate_code }}</code>
                                </td>
                                <td>
                                    <span class="badge {{ $account->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $account->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="javascript:void(0);"
                                           wire:click="toggleActive({{ $account->id }})"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="{{ $account->is_active ? 'Devre Dışı Bırak' : 'Aktifleştir' }}"
                                           style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid {{ $account->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} link-secondary fa-lg"></i>
                                        </a>
                                        <a href="javascript:void(0);"
                                           wire:click="deleteParent({{ $account->id }})"
                                           wire:confirm="⚠️ DİKKAT: Bu ana firmayı silmek istediğinize emin misiniz?

🏢 {{ $account->company_name }}
📊 {{ $account->children->count() }} alt şube silinecek
🎵 {{ $account->spots()->count() }} spot/anons silinecek

Bu işlem geri alınamaz!"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="Sil"
                                           style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Şube Satırları (Varyant gibi açılır) -->
                            @foreach($account->children as $branch)
                                <tr x-show="openBranches[{{ $account->id }}]"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    wire:key="branch-{{ $branch->id }}"
                                    style="background: rgba(32, 107, 196, 0.05);">
                                    <td></td>
                                    <td class="sort-id small text-muted">{{ $branch->id }}</td>
                                    <td></td>
                                    <td>
                                        <div class="d-flex align-items-center ps-4">
                                            <span class="avatar avatar-sm bg-azure me-2">
                                                <i class="fas fa-store"></i>
                                            </span>
                                            <div>
                                                <div class="fw-medium">
                                                    {{ $branch->branch_name ?: 'İsimsiz Şube' }}
                                                    <span class="badge bg-azure ms-2" style="font-size: 0.7rem;">Alt Şube</span>
                                                </div>
                                                <small class="text-muted">{{ $account->company_name }} şubesi</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $branch->owner->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $branch->owner->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">—</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $branch->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $branch->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex align-items-center gap-3 justify-content-center">
                                            <a href="javascript:void(0);"
                                               wire:click="detachBranch({{ $branch->id }})"
                                               wire:confirm="Bu şubeyi bağımsızlaştırmak istediğinize emin misiniz? Şube kendi başına ana firma olacaktır."
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Bağımsızlaştır"
                                               style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-rocket link-success fa-lg"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                               wire:click="removeBranch({{ $branch->id }})"
                                               wire:confirm="Bu şubeyi silmek istediğinize emin misiniz?"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="Şubeden Çıkar"
                                               style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-unlink link-danger fa-lg"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty py-5">
                                        <div class="empty-icon">
                                            <i class="fas fa-building fa-3x text-muted"></i>
                                        </div>
                                        <p class="empty-title">Kurumsal hesap bulunamadı</p>
                                        <p class="empty-subtitle text-muted">Yeni ana firma oluşturmak için butona tıklayın</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($this->corporateAccounts->hasPages())
                <div class="card-footer d-flex align-items-center">
                    {{ $this->corporateAccounts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Şube Yönetimi Kartı -->
    <div class="card mt-4">
        <div class="card-header">
            <div class="row align-items-center w-100">
                <div class="col">
                    <h3 class="card-title">
                        <i class="fas fa-sitemap me-2"></i>
                        Şube Yönetimi
                    </h3>
                </div>
                <div class="col-auto">
                    <select class="form-select" wire:model.live="selectedParentId" style="width: 300px;">
                        <option value="">Ana firma seçin...</option>
                        @foreach($this->parentAccounts as $parent)
                            <option value="{{ $parent->id }}">
                                {{ $parent->company_name }} ({{ $parent->children->count() }} şube)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($selectedParentId)
                <div class="row g-4">
                    <!-- Sol: Bağlı Olmayan Kullanıcılar -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-users text-muted me-2"></i>
                                Bağlı Olmayan Kullanıcılar
                            </h4>
                            <span class="badge bg-secondary">{{ $this->availableUsers->count() }}</span>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="branchSearch" placeholder="Kullanıcı ara...">
                        </div>
                        <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                            @forelse($this->availableUsers as $user)
                                <div class="list-group-item" wire:key="available-user-{{ $user->id }}">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm me-2 bg-blue-lt">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                                <div>
                                                    <div class="fw-medium">{{ $user->name }}</div>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button wire:click="addBranch({{ $user->id }})"
                                                    class="btn btn-sm btn-primary"
                                                    wire:loading.attr="disabled"
                                                    wire:target="addBranch({{ $user->id }})">
                                                <i class="fas fa-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4 text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p class="mb-0">Kullanıcı bulunamadı</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Sağ: Şubeler -->
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">
                                <i class="fas fa-store text-primary me-2"></i>
                                {{ $this->selectedParent->company_name ?? 'Şubeler' }}
                            </h4>
                            <span class="badge bg-primary">{{ $this->branchAccounts->count() }}</span>
                        </div>
                        <div class="alert alert-info py-2 mb-3">
                            <small><i class="fas fa-info-circle me-1"></i>Soldan kullanıcı ekleyin</small>
                        </div>
                        <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                            @forelse($this->branchAccounts as $branch)
                                <div class="list-group-item" wire:key="branch-manage-{{ $branch->id }}">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm me-2 bg-azure-lt">
                                                    <i class="fas fa-store"></i>
                                                </span>
                                                <div class="flex-fill">
                                                    <input type="text"
                                                           class="form-control form-control-sm border-0 p-0 fw-medium"
                                                           value="{{ $branch->branch_name }}"
                                                           wire:blur="updateBranchName({{ $branch->id }}, $event.target.value)"
                                                           placeholder="Şube adı girin..."
                                                           style="background: transparent;">
                                                    <small class="text-muted">{{ $branch->owner->name ?? '' }} ({{ $branch->owner->email ?? '' }})</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button wire:click="removeBranch({{ $branch->id }})"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-arrow-left"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center py-4 text-muted">
                                    <i class="fas fa-store fa-2x mb-2"></i>
                                    <p class="mb-0">Henüz şube yok</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <div class="empty py-4">
                    <div class="empty-icon">
                        <i class="fas fa-building fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Ana firma seçin</p>
                    <p class="empty-subtitle text-muted">Şube yönetimi için yukarıdan bir ana firma seçin</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Yeni Ana Firma Modal -->
    <div class="modal fade" id="createParentModal" tabindex="-1" wire:ignore.self
         x-data="{
            generateCode(length = 6) {
                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                let code = '';
                for (let i = 0; i < length; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                $wire.set('corporateCode', code);
            }
         }">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-building me-2"></i>
                        Yeni Ana Firma Oluştur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Kullanıcı Seçimi -->
                    <div class="mb-3">
                        <label class="form-label required">Kullanıcı Seç</label>
                        <select class="form-select" wire:model="selectedUserId">
                            <option value="">Kullanıcı seçin...</option>
                            @foreach($this->allUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('selectedUserId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <!-- Firma Adı -->
                    <div class="mb-3">
                        <label class="form-label">Firma Adı <small class="text-muted">(opsiyonel)</small></label>
                        <input type="text" class="form-control" wire:model="companyName" placeholder="Örn: ABC Müzik Ltd.">
                        <small class="form-hint">Boş bırakılırsa kullanıcı adı kullanılır</small>
                    </div>

                    <!-- Kurumsal Kod -->
                    <div class="mb-3">
                        <label class="form-label required">Kurumsal Kod</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace" wire:model="corporateCode" placeholder="MZB-XXXXXX" style="letter-spacing: 2px;">
                            <button class="btn btn-outline-primary" type="button" @click="generateCode()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        @error('corporateCode') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <small class="form-hint">Manuel girin veya otomatik oluşturun</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" wire:click="createParent" data-bs-dismiss="modal">
                        <i class="fas fa-check me-1"></i>
                        Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    [x-cloak] { display: none !important; }
    .font-monospace {
        font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Mono', 'Droid Sans Mono', monospace !important;
    }
    </style>
</div>
