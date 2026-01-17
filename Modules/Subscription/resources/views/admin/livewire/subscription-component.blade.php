@php
    View::share('pretitle', __('subscription::admin.subscriptions'));

    $statusLabels = [
        'active' => 'Aktif',
        'trial' => 'Deneme',
        'expired' => 'Bitti',
        'cancelled' => 'Iptal',
        'paused' => 'Durduruldu',
        'pending' => 'Sirada',
        'pending_payment' => 'Odeme Bekliyor',
    ];

    $statusColors = [
        'active' => 'success',
        'trial' => 'info',
        'expired' => 'danger',
        'cancelled' => 'dark',
        'paused' => 'warning',
        'pending' => 'warning',
        'pending_payment' => 'orange',
    ];
@endphp

<div wire:key="subscription-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    {{-- Compact Stats Line --}}
    @if(isset($mrrStats))
    <div class="mb-3 d-flex flex-wrap gap-3 align-items-center">
        <span class="badge bg-success-lt text-success py-2 px-3">
            <i class="fas fa-users me-1"></i>Aktif: {{ number_format($mrrStats['active_count']) }}
        </span>
        <span class="badge bg-info-lt text-info py-2 px-3">
            <i class="fas fa-gift me-1"></i>Aktif Deneme: {{ number_format($mrrStats['trial_count'] ?? 0) }}
        </span>
        <span class="badge bg-orange-lt text-orange py-2 px-3">
            <i class="fas fa-hourglass-half me-1"></i>Denemesi Biten (7g): {{ number_format($mrrStats['trial_expiring_7_days'] ?? 0) }}
        </span>
        <span class="badge bg-danger-lt text-danger py-2 px-3">
            <i class="fas fa-calendar-times me-1"></i>Premium'u Biten (7g): {{ number_format($mrrStats['premium_expiring_7_days'] ?? 0) }}
        </span>
    </div>
    @endif

    {{-- Filters Card --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm {{ $this->hasActiveFilters() ? 'btn-primary' : 'btn-outline-primary' }}" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i>Filtreler
                    @if($this->hasActiveFilters())
                        <span class="badge bg-white text-primary ms-1">Aktif</span>
                    @endif
                </button>

                {{-- Active Filter Badges --}}
                @if($this->hasActiveFilters())
                    <div class="d-flex flex-wrap gap-1">
                        @if($search)
                            <span class="badge bg-blue-lt text-blue">Arama: {{ Str::limit($search, 15) }}</span>
                        @endif
                        @if($filterStatus)
                            <span class="badge bg-purple-lt text-purple">Durum: {{ $statusLabels[$filterStatus] ?? $filterStatus }}</span>
                        @endif
                        @if($filterPlan)
                            @php $planName = $plans->firstWhere('subscription_plan_id', $filterPlan)?->title_text ?? 'Plan'; @endphp
                            <span class="badge bg-cyan-lt text-cyan">{{ Str::limit($planName, 15) }}</span>
                        @endif
                        @if($startDateFrom || $startDateTo)
                            <span class="badge bg-teal-lt text-teal">Baslangic Tarihi</span>
                        @endif
                        @if($endDateFrom || $endDateTo)
                            <span class="badge bg-lime-lt text-lime">Bitis Tarihi</span>
                        @endif
                        @if($priceMin || $priceMax)
                            <span class="badge bg-yellow-lt text-yellow">Fiyat Araligi</span>
                        @endif
                        @if($expiryWarning)
                            <span class="badge bg-red-lt text-red">7 Gun Uyarisi</span>
                        @endif
                        @if($corporateFilter)
                            <span class="badge bg-indigo-lt text-indigo">{{ $corporateFilter === 'corporate' ? 'Kurumsal' : 'Bireysel' }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2 align-items-center">
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

                @if($this->hasActiveFilters())
                    <button class="btn btn-sm btn-outline-danger" wire:click="clearFilters">
                        <i class="fas fa-times me-1"></i>Temizle
                    </button>
                @endif

                <button class="btn btn-sm btn-success" wire:click="exportSubscriptions">
                    <i class="fas fa-download me-1"></i>Excel
                </button>
            </div>
        </div>

        {{-- Collapsible Filter Panel --}}
        <div class="collapse {{ $this->hasActiveFilters() ? 'show' : '' }}" id="filtersCollapse">
            <div class="card-body border-top py-3">
                <div class="row g-3">
                    {{-- Search --}}
                    <div class="col-md-3">
                        <label class="form-label small">Ara</label>
                        <div class="input-icon">
                            <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Musteri, email...">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-2">
                        <label class="form-label small">Durum</label>
                        <select wire:model.live="filterStatus" class="form-select">
                            <option value="">Tumu</option>
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Plan --}}
                    <div class="col-md-2">
                        <label class="form-label small">Plan</label>
                        <select wire:model.live="filterPlan" class="form-select">
                            <option value="">Tum Planlar</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->subscription_plan_id }}">{{ $plan->title_text }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Corporate --}}
                    <div class="col-md-2">
                        <label class="form-label small">Tip</label>
                        <select wire:model.live="corporateFilter" class="form-select">
                            <option value="">Tumu</option>
                            <option value="corporate">Kurumsal</option>
                            <option value="individual">Bireysel</option>
                        </select>
                    </div>

                    {{-- Checkboxes --}}
                    <div class="col-md-3">
                        <label class="form-label small d-block">&nbsp;</label>
                        <div class="d-flex flex-column gap-1">
                            <label class="form-check mb-0">
                                <input type="checkbox" wire:model.live="expiryWarning" class="form-check-input">
                                <span class="form-check-label text-danger small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>7 Gün İçinde Bitenler
                                </span>
                            </label>
                            <label class="form-check mb-0">
                                <input type="checkbox" wire:model.live="showFree" class="form-check-input">
                                <span class="form-check-label text-muted small">
                                    <i class="fas fa-gift me-1"></i>Ücretsizleri Göster
                                </span>
                            </label>
                            <label class="form-check mb-0">
                                <input type="checkbox" wire:model.live="showPendingPayment" class="form-check-input">
                                <span class="form-check-label text-orange small">
                                    <i class="fas fa-clock me-1"></i>Ödeme Bekleyenleri Göster
                                </span>
                            </label>
                            <label class="form-check mb-0">
                                <input type="checkbox" wire:model.live="showExpired" class="form-check-input">
                                <span class="form-check-label text-danger small">
                                    <i class="fas fa-calendar-times me-1"></i>Süresi Bitenleri Göster
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Advanced Date/Price Filters --}}
                <div class="row g-3 mt-2 pt-2 border-top">
                    <div class="col-md-2">
                        <label class="form-label small">Baslangic (Min)</label>
                        <input type="date" wire:model.live="startDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Baslangic (Max)</label>
                        <input type="date" wire:model.live="startDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Bitis (Min)</label>
                        <input type="date" wire:model.live="endDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Bitis (Max)</label>
                        <input type="date" wire:model.live="endDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Min Fiyat (TL)</label>
                        <input type="number" wire:model.live.debounce.300ms="priceMin" class="form-control form-control-sm" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Max Fiyat (TL)</label>
                        <input type="number" wire:model.live.debounce.300ms="priceMax" class="form-control form-control-sm" placeholder="9999">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>
                            <button class="table-sort {{ $sortField === 'user_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" wire:click="sortBy('user_id')">
                                Musteri
                            </button>
                        </th>
                        <th>Plan</th>
                        <th>
                            <button class="table-sort {{ $sortField === 'current_period_end' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" wire:click="sortBy('current_period_end')">
                                Kalan
                            </button>
                        </th>
                        <th>
                            <button class="table-sort {{ $sortField === 'last_payment_date' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" wire:click="sortBy('last_payment_date')">
                                Son Odeme
                            </button>
                        </th>
                        <th>Toplam</th>
                        <th>
                            <button class="table-sort {{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" wire:click="sortBy('status')">
                                Durum
                            </button>
                        </th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    @php
                        // Status hesaplama
                        $effectiveStatus = $subscription->status;
                        if ($subscription->has_trial && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
                            $effectiveStatus = 'trial';
                        } elseif ($subscription->current_period_end && $subscription->current_period_end->isPast()) {
                            $effectiveStatus = 'expired';
                        }

                        // Toplam kalan gun (users.subscription_expires_at)
                        $userExpiry = $subscription->customer?->subscription_expires_at;
                        $totalDaysLeft = $userExpiry && $userExpiry->isFuture()
                            ? (int) now()->diffInDays($userExpiry, false)
                            : 0;

                        // Son odeme bilgisi
                        $lastOrder = null;
                        if ($subscription->customer) {
                            $lastOrder = \Modules\Cart\App\Models\Order::where('user_id', $subscription->customer->id)
                                ->whereIn('payment_status', ['paid', 'completed'])
                                ->orderBy('created_at', 'desc')
                                ->first();
                        }

                        // Toplam odenen (KDV dahil)
                        $totalPaid = \Modules\Subscription\App\Models\Subscription::where('user_id', $subscription->user_id)
                            ->whereIn('status', ['active', 'pending', 'cancelled', 'expired'])
                            ->sum('price_per_cycle') * 1.20;
                    @endphp
                    <tr wire:key="subscription-{{ $subscription->subscription_id }}" class="{{ $effectiveStatus === 'pending_payment' ? 'table-warning' : '' }}">
                        <td>
                            @if($subscription->customer)
                            <button type="button" wire:click="openUserModal({{ $subscription->customer->id }})" class="btn btn-link text-reset text-decoration-none p-0 text-start">
                                <div class="fw-medium">
                                    <span class="badge bg-azure-lt text-azure me-1">#{{ $subscription->customer->id }}</span>
                                    {{ $subscription->customer->name }}
                                    <i class="fas fa-external-link-alt text-azure ms-1" style="font-size: 10px;"></i>
                                </div>
                                <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">{{ $subscription->customer->email }}</div>
                            </button>
                            @else
                            <span style="color: var(--tblr-body-color); opacity: 0.5;">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $subscription->plan?->title_text ?? '-' }}</span>
                        </td>
                        <td>
                            @if($totalDaysLeft > 0)
                                <div class="text-success fw-semibold">{{ $totalDaysLeft }} gun</div>
                                <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">{{ $userExpiry?->format('d.m.Y') }}</div>
                            @elseif($effectiveStatus === 'pending_payment')
                                <span style="color: var(--tblr-body-color); opacity: 0.5;">-</span>
                            @else
                                <span class="text-danger">Bitti</span>
                            @endif
                        </td>
                        <td>
                            @if($lastOrder)
                                <div>{{ $lastOrder->created_at->format('d.m.Y') }}</div>
                                <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">{{ number_format($lastOrder->total_amount, 0, ',', '.') }} TL</div>
                            @else
                                <span style="color: var(--tblr-body-color); opacity: 0.5;">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-warning">{{ number_format($totalPaid, 0, ',', '.') }} TL</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$effectiveStatus] ?? 'dark' }}">
                                {{ $statusLabels[$effectiveStatus] ?? $effectiveStatus }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" data-bs-toggle="dropdown" style="color: var(--tblr-body-color);">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <button class="dropdown-item" wire:click="openUserModal({{ $subscription->customer?->id ?? 0 }})">
                                        <i class="fas fa-eye me-2"></i>Detay
                                    </button>
                                    @if(in_array($effectiveStatus, ['active', 'trial']))
                                    <button class="dropdown-item text-danger" wire:click="terminateNow({{ $subscription->subscription_id }})" wire:confirm="Bu aboneligi HEMEN sonlandirmak istediginize emin misiniz?">
                                        <i class="fas fa-times-circle me-2"></i>Sonlandir
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div style="color: var(--tblr-body-color); opacity: 0.6;">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Abonelik bulunamadi
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
        <div class="card-footer">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>

    {{-- User Detail Modal --}}
    @if($showUserModal && $selectedUserData)
    <div class="modal modal-blur fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar avatar-md bg-primary-lt">
                            <i class="fas fa-user text-primary"></i>
                        </span>
                        <div>
                            <h5 class="modal-title mb-0">
                                <span class="badge bg-azure-lt text-azure me-1">#{{ $selectedUserData['user']['id'] }}</span>
                                {{ $selectedUserData['user']['name'] }}
                            </h5>
                            <small style="color: var(--tblr-body-color); opacity: 0.7;">{{ $selectedUserData['user']['email'] }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeUserModal"></button>
                </div>

                <div class="modal-body">
                    {{-- Stats --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-green-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-green">{{ $selectedUserData['user']['total_days_left'] }}</div>
                                    <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">Kalan Gun</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-blue-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-blue">{{ $selectedUserData['stats']['total_subscriptions'] }}</div>
                                    <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">Abonelik</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-yellow-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-yellow">{{ number_format((float) ($selectedUserData['stats']['total_paid'] ?? 0), 0, ',', '.') }} TL</div>
                                    <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">Odenen</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-purple-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-purple">{{ count($selectedUserData['orders']) }}</div>
                                    <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">Odeme</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-subscriptions">
                                <i class="fas fa-list me-2"></i>Abonelikler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-payments">
                                <i class="fas fa-credit-card me-2"></i>Odemeler
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Abonelikler Tab --}}
                        <div class="tab-pane active show" id="tab-subscriptions">
                            @php
                                $subStatusColors = ['active' => 'green', 'pending' => 'yellow', 'pending_payment' => 'orange', 'trial' => 'cyan', 'cancelled' => 'dark', 'expired' => 'red'];
                                $subStatusLabels = ['active' => 'Aktif', 'pending' => 'Sirada', 'pending_payment' => 'Odeme Bekliyor', 'trial' => 'Deneme', 'cancelled' => 'Iptal', 'expired' => 'Bitti'];
                            @endphp

                            <div class="space-y-3">
                                @forelse($selectedUserData['subscriptions'] as $sub)
                                <div class="card border-start border-4 border-{{ $subStatusColors[$sub['status']] ?? 'dark' }} mb-2">
                                    <div class="card-body py-3">
                                        {{-- Header --}}
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $subStatusColors[$sub['status']] ?? 'dark' }}-lt text-{{ $subStatusColors[$sub['status']] ?? 'dark' }}">
                                                    {{ $subStatusLabels[$sub['status']] ?? $sub['status'] }}
                                                </span>
                                                <span class="fw-semibold">{{ $sub['plan_title'] }}</span>
                                                <span style="color: var(--tblr-body-color); opacity: 0.7;">{{ $sub['cycle_label'] }}</span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                @if($sub['can_activate'])
                                                <button class="btn btn-sm btn-ghost-success" wire:click="activateSubscription({{ $sub['id'] }})" wire:confirm="Aktif etmek istediginize emin misiniz?" title="Aktif Et">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                @endif
                                                @if($sub['can_delete'])
                                                <button class="btn btn-sm btn-ghost-danger" wire:click="deleteSubscription({{ $sub['id'] }})" wire:confirm="Silmek istediginize emin misiniz?" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="row text-sm">
                                            <div class="col-4">
                                                <span style="color: var(--tblr-body-color); opacity: 0.7;">Donem:</span>
                                                <span class="ms-1">{{ $sub['current_period_start'] ?? '-' }} - {{ $sub['current_period_end'] ?? '-' }}</span>
                                            </div>
                                            <div class="col-4">
                                                <span style="color: var(--tblr-body-color); opacity: 0.7;">Kalan:</span>
                                                @if($sub['days_left'] > 0)
                                                <span class="ms-1 text-success fw-medium">{{ $sub['days_left'] }} gun</span>
                                                @else
                                                <span class="ms-1" style="color: var(--tblr-body-color); opacity: 0.5;">-</span>
                                                @endif
                                            </div>
                                            <div class="col-4">
                                                <span style="color: var(--tblr-body-color); opacity: 0.7;">Tutar:</span>
                                                <span class="ms-1 text-warning fw-medium">{{ $sub['price'] }}</span>
                                            </div>
                                        </div>

                                        {{-- Payment Info --}}
                                        <div class="border-top mt-2 pt-2 d-flex justify-content-between align-items-center text-sm">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($sub['payment_status'] === 'manual')
                                                    <i class="fas fa-user-check text-purple"></i>
                                                    <span style="color: var(--tblr-body-color); opacity: 0.7;">Manuel Onay</span>
                                                @elseif($sub['payment_status'] === 'paid' || $sub['payment_status'] === 'completed')
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    <span style="color: var(--tblr-body-color); opacity: 0.7;">Odendi</span>
                                                    @if($sub['payment_method'])
                                                    <span style="color: var(--tblr-body-color); opacity: 0.5;">|</span>
                                                    <span style="color: var(--tblr-body-color); opacity: 0.7;">{{ $sub['payment_method'] }}</span>
                                                    @endif
                                                @elseif($sub['status'] === 'pending_payment')
                                                    <i class="fas fa-clock text-orange"></i>
                                                    <span class="text-orange">Odeme Bekleniyor</span>
                                                @else
                                                    <i class="fas fa-question-circle" style="color: var(--tblr-body-color); opacity: 0.5;"></i>
                                                    <span style="color: var(--tblr-body-color); opacity: 0.7;">{{ $sub['payment_label'] }}</span>
                                                @endif
                                            </div>
                                            @if($sub['order_number'])
                                            <a href="{{ route('admin.orders.index') }}?search={{ $sub['order_number'] }}" class="text-primary small" target="_blank">
                                                <i class="fas fa-external-link-alt me-1"></i>{{ $sub['order_number'] }}
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4" style="color: var(--tblr-body-color); opacity: 0.6;">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Abonelik bulunamadi
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Odemeler Tab --}}
                        <div class="tab-pane" id="tab-payments">
                            @if(count($selectedUserData['orders']) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($selectedUserData['orders'] as $order)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar bg-{{ $order['payment_status'] === 'paid' ? 'success' : 'warning' }}-lt">
                                            <i class="fas fa-{{ $order['payment_status'] === 'paid' ? 'check' : 'clock' }} text-{{ $order['payment_status'] === 'paid' ? 'success' : 'warning' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $order['plan_title'] ?? 'Abonelik' }}</div>
                                            <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">
                                                <span>{{ $order['payment_method'] ?? 'Kredi Karti' }}</span>
                                                <span class="mx-1">|</span>
                                                <span>{{ $order['date'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold text-{{ $order['payment_status'] === 'paid' ? 'success' : 'warning' }}">{{ $order['total'] }}</div>
                                        <a href="{{ route('admin.orders.index') }}?search={{ $order['number'] }}" class="small" style="color: var(--tblr-body-color); opacity: 0.7;" target="_blank">
                                            {{ $order['number'] }}
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-4" style="color: var(--tblr-body-color); opacity: 0.6;">
                                <i class="fas fa-credit-card fa-2x mb-2 d-block"></i>
                                Odeme kaydi bulunamadi
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" wire:click="closeUserModal">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
    .space-y-3 > * + * { margin-top: 0.75rem; }
    .btn-ghost-success { color: var(--tblr-success); background: transparent; }
    .btn-ghost-success:hover { background: var(--tblr-success-lt); }
    .btn-ghost-danger { color: var(--tblr-danger); background: transparent; }
    .btn-ghost-danger:hover { background: var(--tblr-danger-lt); }
    </style>
</div>
