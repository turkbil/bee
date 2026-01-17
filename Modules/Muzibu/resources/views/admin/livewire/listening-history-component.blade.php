@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="listening-history-component">
    <!-- Kullanıcı Filtresi -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <!-- Kullanıcı Filtresi -->
                        <div class="position-relative" style="width: 280px;">
                            @if($filterUser && $this->selectedUserInfo)
                                <div class="input-group">
                                    <span class="form-control bg-purple-lt d-flex align-items-center">
                                        <i class="fas fa-user me-2"></i>
                                        <span class="text-truncate">{{ $this->selectedUserInfo->name }}</span>
                                        <span class="badge bg-purple ms-2">{{ $this->selectedUserInfo->today_plays }}</span>
                                    </span>
                                    <button wire:click="clearUserFilter" class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <i class="fas fa-user-search"></i>
                                    </span>
                                    <input type="text"
                                        wire:model.live.debounce.300ms="userSearch"
                                        wire:focus="$set('showUserDropdown', true)"
                                        class="form-control"
                                        placeholder="Kullanıcı filtrele...">
                                </div>

                                @if($showUserDropdown && $this->searchedUsers->count() > 0)
                                    <div class="dropdown-menu show w-100 mt-1" style="max-height: 300px; overflow-y: auto; z-index: 1050;">
                                        @foreach($this->searchedUsers as $user)
                                            <button type="button"
                                                wire:click="selectUser({{ $user->id }})"
                                                class="dropdown-item d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs bg-purple-lt">
                                                    {{ substr($user->name, 0, 1) }}
                                                </span>
                                                <div class="flex-fill text-truncate">
                                                    <div class="fw-medium">{{ $user->name }}</div>
                                                    <div class="small text-muted">{{ $user->email }}</div>
                                                </div>
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

                        <div wire:loading class="ms-2">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 mt-3 mt-lg-0">
                    <div class="d-flex align-items-center justify-content-lg-end gap-3">
                        <span class="text-muted small">
                            <i class="fas fa-sync-alt me-1"></i>
                            <strong>{{ $this->lastCacheTime }}</strong>
                        </span>
                        <button wire:click="refreshData" class="btn btn-sm btn-outline-success">
                            <span wire:loading.remove wire:target="refreshData">
                                <i class="fas fa-sync-alt me-1"></i> Yenile
                            </span>
                            <span wire:loading wire:target="refreshData">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    @php
        $dateLabel = Carbon\Carbon::parse($selectedDate)->isToday() ? 'Bugün' : Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y');
        $isUserFiltered = $filterUser && $this->selectedUserInfo;
    @endphp
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-primary-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white avatar me-3">
                            <i class="fas fa-play"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->stats['total_plays']) }}</div>
                            <div class="text-muted small">
                                @if($isUserFiltered)
                                    {{ $dateLabel }} Dinleme
                                @else
                                    {{ $dateLabel }} Toplam
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-green-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-green text-white avatar me-3">
                            @if($isUserFiltered)
                                <i class="fas fa-user"></i>
                            @else
                                <i class="fas fa-users"></i>
                            @endif
                        </span>
                        <div>
                            @if($isUserFiltered)
                                <div class="h2 mb-0">{{ $this->selectedUserInfo->name }}</div>
                                <div class="text-muted small">Seçili Kullanıcı</div>
                            @else
                                <div class="h2 mb-0">{{ number_format($this->stats['unique_listeners']) }}</div>
                                <div class="text-muted small">{{ $dateLabel }} Dinleyici</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-azure-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-azure text-white avatar me-3">
                            <i class="fas fa-music"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->stats['unique_songs']) }}</div>
                            <div class="text-muted small">
                                @if($isUserFiltered)
                                    {{ $dateLabel }} Dinlediği Şarkı
                                @else
                                    {{ $dateLabel }} Farklı Şarkı
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-purple-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-purple text-white avatar me-3">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ $this->stats['total_hours'] }} <small class="h4">saat</small></div>
                            <div class="text-muted small">
                                @if($isUserFiltered)
                                    {{ $dateLabel }} Dinleme Süresi
                                @else
                                    {{ $dateLabel }} Toplam Süre
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dinleme Dağılımı Grafiği -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center w-100">
                <div class="col">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>
                        Dinleme Dağılımı
                        @if($filterUser && $this->selectedUserInfo)
                            <span class="badge bg-purple-lt ms-2">{{ $this->selectedUserInfo->name }}</span>
                        @endif
                    </h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <!-- View Mode Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" wire:click="setViewMode('hourly')"
                                class="btn btn-sm {{ $viewMode === 'hourly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Saatlik
                            </button>
                            <button type="button" wire:click="setViewMode('daily')"
                                class="btn btn-sm {{ $viewMode === 'daily' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Günlük
                            </button>
                            <button type="button" wire:click="setViewMode('weekly')"
                                class="btn btn-sm {{ $viewMode === 'weekly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Haftalık
                            </button>
                            <button type="button" wire:click="setViewMode('monthly')"
                                class="btn btn-sm {{ $viewMode === 'monthly' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                Aylık
                            </button>
                        </div>

                        <!-- Tarih Navigasyonu (Sadece hourly/daily modda) -->
                        @if(in_array($viewMode, ['hourly', 'daily']))
                            <div class="d-flex align-items-center gap-2">
                                <button wire:click="goToPreviousDay" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Önceki Gün">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <input type="date" wire:model.live="selectedDate" class="form-control form-control-sm" style="width: 140px;" max="{{ now()->format('Y-m-d') }}">
                                <button wire:click="goToNextDay" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="tooltip" title="Sonraki Gün" @if(Carbon\Carbon::parse($selectedDate)->isToday()) disabled @endif>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button wire:click="goToToday" class="btn btn-sm btn-outline-primary">
                                    Bugün
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Hourly Chart -->
            @if($viewMode === 'hourly')
                @php
                    $stats = $this->hourlyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 45px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end gap-1 pt-3" style="height: 140px;">
                        @foreach($stats as $hour => $plays)
                            <div class="flex-fill text-center">
                                @if($plays > 0)
                                    <div class="text-muted small mb-1" style="font-size: 9px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : $plays }}</div>
                                @endif
                                <div class="bg-primary rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 10px;">{{ sprintf('%02d', $hour) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @if(Carbon\Carbon::parse($selectedDate)->isToday())
                    <div class="text-muted small mt-2 text-center">
                        <i class="fas fa-info-circle me-1"></i>
                        Son saat hesaplamalara dahil edilmedi (performans optimizasyonu)
                    </div>
                @endif
            @endif

            <!-- Daily Chart (Son 7 Gün) -->
            @if($viewMode === 'daily')
                @php
                    $stats = $this->dailyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 45px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($stats as $date => $plays)
                            @php
                                $carbonDate = Carbon\Carbon::parse($date);
                                $isToday = $carbonDate->isToday();
                            @endphp
                            <div class="flex-fill text-center" style="max-width: 80px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="{{ $isToday ? 'bg-green' : 'bg-primary' }} rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1">{{ $carbonDate->translatedFormat('D') }}</div>
                                <div class="text-muted small" style="font-size: 10px;">{{ $carbonDate->format('d/m') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Weekly Chart (Son 4 Hafta) -->
            @if($viewMode === 'weekly')
                @php
                    $stats = $this->weeklyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 50px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-3 pt-3" style="height: 140px;">
                        @foreach($stats as $label => $plays)
                            <div class="flex-fill text-center" style="max-width: 150px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="bg-purple rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Monthly Chart (Son 6 Ay) -->
            @if($viewMode === 'monthly')
                @php
                    $stats = $this->monthlyStats;
                    $maxValue = (!empty($stats) && max($stats) > 0) ? max($stats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 50px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue) }}</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2)) }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($stats as $label => $plays)
                            <div class="flex-fill text-center" style="max-width: 100px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $plays > 999 ? number_format($plays/1000, 1).'k' : number_format($plays) }}</div>
                                <div class="bg-azure rounded-top transition-all" style="height: {{ ($plays / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Search & Table -->
    <div class="card">
        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Şarkı, kullanıcı veya IP ara...">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading wire:target="render, search, perPage, gotoPage, previousPage, nextPage"
                        class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <span class="badge bg-secondary-lt">{{ Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</span>
                        @if($filterUser && $this->selectedUserInfo)
                            <span class="badge bg-purple-lt">{{ $this->selectedUserInfo->name }}</span>
                        @endif
                        <select wire:model.live="perPage" class="form-select" style="width: 80px;">
                            <option value="25">25</option>
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
                        <th class="ps-3" style="width: 70px;">Saat</th>
                        <th style="width: 50px;"></th>
                        <th>Şarkı</th>
                        <th>Kullanıcı</th>
                        <th class="text-center" style="width: 70px;">Süre</th>
                        <th style="width: 120px;">Platform</th>
                        <th style="width: 120px;">IP Adresi</th>
                        <th class="text-center pe-3" style="width: 60px;">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->plays as $play)
                        <tr wire:key="play-{{ $play->id }}">
                            <td class="ps-3">
                                <span class="badge bg-secondary-lt">
                                    {{ $play->created_at->format('H:i:s') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $cover = $play->song?->album?->getFirstMediaUrl('hero', 'thumb');
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
                                <div class="fw-medium text-truncate" style="max-width: 200px;">{{ $play->song?->title ?? 'Silinmiş Şarkı' }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 200px;">
                                    {{ $play->song?->artist?->title ?? $play->song?->album?->artist?->title ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @if($play->user)
                                    <button type="button" wire:click="selectUser({{ $play->user->id }})" class="border-0 bg-transparent p-0 text-start d-flex align-items-center gap-2 cursor-pointer" style="cursor: pointer;" title="Bu kullanıcıyı filtrele">
                                        <span class="avatar avatar-xs bg-secondary-lt">
                                            {{ substr($play->user->name, 0, 1) }}
                                        </span>
                                        <div>
                                            <div class="fw-medium text-truncate" style="max-width: 120px;">{{ $play->user->name }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 120px;">{{ $play->user->email }}</div>
                                        </div>
                                    </button>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-user-secret me-1"></i> Misafir
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($play->listened_duration)
                                    <span class="badge bg-azure-lt" data-bs-toggle="tooltip" title="{{ $play->listened_duration }} saniye">
                                        {{ gmdate('i:s', $play->listened_duration) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $platform = $play->platform ?? 'Bilinmiyor';
                                    $device = $play->device_type ?? '';
                                    $browser = $play->browser ?? '';

                                    $platformIcons = [
                                        'Windows' => 'fab fa-windows text-info',
                                        'Mac' => 'fab fa-apple text-secondary',
                                        'iOS' => 'fab fa-apple text-dark',
                                        'Android' => 'fab fa-android text-success',
                                        'Linux' => 'fab fa-linux text-warning',
                                        'Chrome OS' => 'fab fa-chrome text-primary',
                                    ];
                                    $platformIcon = $platformIcons[$platform] ?? 'fas fa-desktop text-muted';

                                    $deviceIcons = [
                                        'mobile' => 'fas fa-mobile-alt',
                                        'tablet' => 'fas fa-tablet-alt',
                                        'desktop' => 'fas fa-desktop',
                                    ];
                                    $deviceIcon = $deviceIcons[$device] ?? '';
                                @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $platformIcon }}" data-bs-toggle="tooltip" title="{{ $platform }}"></i>
                                    <div>
                                        <div class="small fw-medium">{{ $platform }}</div>
                                        @if($browser)
                                            <div class="text-muted small">{{ $browser }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($play->ip_address)
                                    <code class="small" data-bs-toggle="tooltip" title="IP Adresi: {{ $play->ip_address }}">
                                        {{ $play->ip_address }}
                                    </code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                @if($play->was_skipped)
                                    <span class="badge bg-warning-lt" data-bs-toggle="tooltip" title="Atlandı">
                                        <i class="fas fa-forward"></i>
                                    </span>
                                @else
                                    <span class="badge bg-success-lt" data-bs-toggle="tooltip" title="Tam dinlendi">
                                        <i class="fas fa-check"></i>
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">
                                    @if($search)
                                        "{{ $search }}" için sonuç bulunamadı
                                    @elseif($filterUser)
                                        Bu kullanıcının bu tarihte dinleme kaydı yok
                                    @else
                                        Bu tarihte dinleme kaydı yok
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            @if($this->plays->hasPages())
                {{ $this->plays->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $this->plays->total() }}</span> kayıt
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@script
<script>
    function initTooltips() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => {
            const existingTooltip = bootstrap.Tooltip.getInstance(el);
            if (existingTooltip) existingTooltip.dispose();
            new bootstrap.Tooltip(el);
        });
    }

    initTooltips();

    Livewire.hook('morph.updated', () => {
        initTooltips();
    });

    // Dropdown dışına tıklandığında kapat
    document.addEventListener('click', function(e) {
        const wrapper = document.querySelector('.listening-history-component .position-relative');
        if (wrapper && !wrapper.contains(e.target)) {
            @this.set('showUserDropdown', false);
        }
    });
</script>
@endscript
