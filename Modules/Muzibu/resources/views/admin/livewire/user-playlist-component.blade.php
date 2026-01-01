@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="user-playlist-component-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Stats Cards -->
            <div class="row g-3 mx-3 my-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-primary-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="bg-primary text-white avatar">
                                        <i class="fas fa-list"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h3 mb-0">{{ number_format($this->stats['total_playlists']) }}</div>
                                    <div class="text-muted small">Toplam Playlist</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-green-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="bg-green text-white avatar">
                                        <i class="fas fa-users"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h3 mb-0">{{ number_format($this->stats['total_users']) }}</div>
                                    <div class="text-muted small">Kullanıcı</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-azure-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="bg-azure text-white avatar">
                                        <i class="fas fa-globe"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h3 mb-0">{{ number_format($this->stats['public_playlists']) }}</div>
                                    <div class="text-muted small">Herkese Açık</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm bg-secondary-lt">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="bg-secondary text-white avatar">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="h3 mb-0">{{ number_format($this->stats['private_playlists']) }}</div>
                                    <div class="text-muted small">Gizli</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktif Kullanıcı Filtresi Gösterimi -->
            @if($filterUser)
                @php $filteredUser = $this->usersWithPlaylists->firstWhere('id', $filterUser); @endphp
                <div class="card-header bg-azure-lt">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-filter"></i>
                            <span class="fw-medium">Gösterilen:</span>
                        </div>
                        @if($filteredUser)
                            <span class="badge bg-cyan-lt fs-6">
                                {{ $filteredUser->name }}
                            </span>
                        @endif
                        <button wire:click="clearFilters" class="btn btn-outline-secondary ms-auto">
                            <i class="fas fa-times me-1"></i>Tümünü Göster
                        </button>
                    </div>
                </div>
            @endif

            <!-- Filtre Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Sol Taraf - Arama ve Filtreler -->
                <div class="col-auto">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Arama -->
                        <div class="input-icon" style="width: 220px;">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Playlist veya kullanıcı ara...">
                        </div>

                        <!-- Kullanıcı Filtresi (Arama Tabanlı) -->
                        <div class="position-relative" style="width: 220px;">
                            @if($filterUser)
                                {{-- Seçili kullanıcı gösterimi --}}
                                <div class="input-group">
                                    <span class="form-control bg-azure-lt d-flex align-items-center">
                                        <i class="fas fa-user me-2"></i>
                                        <span class="text-truncate">{{ $this->selectedUserName }}</span>
                                    </span>
                                    <button wire:click="clearFilters" class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                {{-- Arama input --}}
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-user-search"></i>
                                    </span>
                                    <input type="text"
                                        wire:model.live.debounce.300ms="userSearch"
                                        wire:focus="$set('showUserDropdown', true)"
                                        class="form-control"
                                        placeholder="Kullanıcı ara...">
                                </div>

                                {{-- Arama sonuçları dropdown --}}
                                @if($showUserDropdown && $this->searchedUsers->count() > 0)
                                    <div class="dropdown-menu show w-100 mt-1" style="max-height: 300px; overflow-y: auto;">
                                        @foreach($this->searchedUsers as $user)
                                            <button type="button"
                                                wire:click="selectUser({{ $user->id }})"
                                                class="dropdown-item d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs bg-secondary-lt">
                                                    {{ substr($user->name, 0, 1) }}
                                                </span>
                                                <div class="flex-fill text-truncate">
                                                    <div class="fw-medium">{{ $user->name }}</div>
                                                    <div class="small text-muted">{{ $user->email }}</div>
                                                </div>
                                                <span class="badge bg-primary-lt">{{ $user->playlists_count }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @elseif($showUserDropdown && strlen($userSearch) >= 2)
                                    <div class="dropdown-menu show w-100 mt-1">
                                        <div class="dropdown-item text-muted text-center py-3">
                                            <i class="fas fa-search me-1"></i> Sonuç bulunamadı
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Temizle -->
                        @if($search || $filterUser)
                            <button wire:click="clearFilters" class="btn btn-icon btn-ghost-secondary" title="Filtreleri Temizle">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, filterUser, toggleActive, togglePublic, deletePlaylist"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf -->
                <div class="col-auto">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <select wire:model.live="perPage" class="form-select" style="width: 80px;">
                            <option value="15">15</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px">
                                <button class="table-sort {{ $sortField === 'playlist_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('playlist_id')">
                                    ID
                                </button>
                            </th>
                            <th style="width: 60px"></th>
                            <th style="min-width: 200px">
                                <button class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    Playlist Adı
                                </button>
                            </th>
                            <th style="min-width: 150px">Kullanıcı</th>
                            <th class="text-center" style="width: 80px">Şarkı</th>
                            <th class="text-center" style="width: 80px">
                                <button class="table-sort {{ $sortField === 'is_public' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_public')">
                                    Görünürlük
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">
                                <button class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                    Durum
                                </button>
                            </th>
                            <th class="text-center" style="width: 100px">
                                <button class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('created_at')">
                                    Tarih
                                </button>
                            </th>
                            <th class="text-center" style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->playlists as $playlist)
                            <tr wire:key="row-{{ $playlist->playlist_id }}">
                                <td class="text-center small text-muted">
                                    {{ $playlist->playlist_id }}
                                </td>
                                <td>
                                    @php
                                        $cover = $playlist->getFirstMediaUrl('hero', 'thumb');
                                    @endphp
                                    @if($cover)
                                        <span class="avatar avatar-sm" style="background-image: url('{{ $cover }}')"></span>
                                    @else
                                        <span class="avatar avatar-sm bg-primary-lt">
                                            <i class="fas fa-music"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $playlist->title }}</span>
                                        @if($playlist->description)
                                            <small class="text-muted text-truncate" style="max-width: 250px;">
                                                {{ Str::limit($playlist->description, 50) }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($playlist->user)
                                        <a href="?filterUser={{ $playlist->user->id }}" class="d-flex align-items-center text-decoration-none">
                                            <span class="avatar avatar-xs me-2 bg-secondary-lt">
                                                {{ substr($playlist->user->name, 0, 1) }}
                                            </span>
                                            <span>{{ $playlist->user->name }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($playlist->songs_count > 0)
                                        <a href="{{ route('admin.muzibu.playlist.songs', $playlist->playlist_id) }}"
                                           class="badge bg-blue-lt text-decoration-none">
                                            {{ $playlist->songs_count }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-lt">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button wire:click="togglePublic({{ $playlist->playlist_id }})"
                                        class="btn btn-sm {{ $playlist->is_public ? 'btn-azure' : 'btn-outline-secondary' }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ $playlist->is_public ? 'Herkese Açık - Tıklayarak gizli yapabilirsiniz' : 'Gizli - Tıklayarak herkese açık yapabilirsiniz' }}">
                                        <div wire:loading wire:target="togglePublic({{ $playlist->playlist_id }})"
                                            class="spinner-border spinner-border-sm"></div>
                                        <div wire:loading.remove wire:target="togglePublic({{ $playlist->playlist_id }})" class="d-flex align-items-center gap-1">
                                            @if($playlist->is_public)
                                                <i class="fas fa-globe"></i>
                                                <span class="d-none d-xl-inline">Açık</span>
                                            @else
                                                <i class="fas fa-lock"></i>
                                                <span class="d-none d-xl-inline">Gizli</span>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button wire:click="toggleActive({{ $playlist->playlist_id }})"
                                        class="btn btn-sm {{ $playlist->is_active ? 'btn-success' : 'btn-outline-danger' }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ $playlist->is_active ? 'Aktif - Tıklayarak pasif yapabilirsiniz' : 'Pasif - Tıklayarak aktif yapabilirsiniz' }}">
                                        <div wire:loading wire:target="toggleActive({{ $playlist->playlist_id }})"
                                            class="spinner-border spinner-border-sm"></div>
                                        <div wire:loading.remove wire:target="toggleActive({{ $playlist->playlist_id }})" class="d-flex align-items-center gap-1">
                                            @if($playlist->is_active)
                                                <i class="fas fa-check"></i>
                                                <span class="d-none d-xl-inline">Aktif</span>
                                            @else
                                                <i class="fas fa-ban"></i>
                                                <span class="d-none d-xl-inline">Pasif</span>
                                            @endif
                                        </div>
                                    </button>
                                </td>
                                <td class="text-center small text-muted">
                                    {{ $playlist->created_at?->format('d.m.Y') }}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="{{ route('admin.muzibu.playlist.manage', $playlist->playlist_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('admin.edit') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                        <a href="{{ route('admin.muzibu.playlist.songs', $playlist->playlist_id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Şarkıları Yönet"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-list-music link-secondary fa-lg"></i>
                                        </a>
                                        <button wire:click="deletePlaylist({{ $playlist->playlist_id }})"
                                            wire:confirm="Bu playlist'i silmek istediğinize emin misiniz?"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Sil"
                                            style="min-height: 24px; display: inline-flex; align-items: center; background: none; border: none; padding: 0;">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="empty">
                                        <div class="empty-icon">
                                            <i class="fas fa-list fa-3x text-muted"></i>
                                        </div>
                                        <p class="empty-title">Kullanıcı playlist'i bulunamadı</p>
                                        <p class="empty-subtitle text-muted">
                                            @if($search || $filterUser)
                                                Arama kriterlerine uygun sonuç yok
                                            @else
                                                Henüz kullanıcı tarafından oluşturulmuş playlist yok
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            @if ($this->playlists->hasPages())
                {{ $this->playlists->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center">
                    <p class="small mb-0">
                        Toplam <span class="fw-semibold">{{ $this->playlists->total() }}</span> playlist
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    // Bootstrap tooltip'leri başlat (Livewire güncellemelerinden sonra da)
    function initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => {
            // Önce eski tooltip'i kaldır
            const existingTooltip = bootstrap.Tooltip.getInstance(el);
            if (existingTooltip) {
                existingTooltip.dispose();
            }
            // Yeni tooltip oluştur
            new bootstrap.Tooltip(el);
        });
    }

    // Sayfa yüklendiğinde
    initTooltips();

    // Livewire güncellemelerinden sonra
    Livewire.hook('morph.updated', () => {
        initTooltips();
    });

    // Dropdown dışına tıklandığında kapat
    document.addEventListener('click', function(e) {
        const userSearchWrapper = document.querySelector('.user-playlist-component-wrapper .position-relative');
        if (userSearchWrapper && !userSearchWrapper.contains(e.target)) {
            @this.set('showUserDropdown', false);
        }
    });
</script>
@endscript
