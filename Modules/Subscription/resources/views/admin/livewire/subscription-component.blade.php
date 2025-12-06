@php
    View::share('pretitle', __('subscription::admin.subscriptions'));

    // Status labels ve renkler
    $statusLabels = [
        'active' => 'Aktif',
        'trial' => 'Deneme',
        'expired' => 'Süresi Doldu',
        'cancelled' => 'İptal Edildi',
        'paused' => 'Duraklatıldı',
        'pending_payment' => 'Ödeme Bekliyor',
    ];

    $statusColors = [
        'active' => 'success',
        'trial' => 'info',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'paused' => 'warning',
        'pending_payment' => 'warning',
    ];

    // Gerçek durumu belirle (trial mı premium mı)
    function getEffectiveStatus($subscription) {
        // Trial kontrolü: has_trial=true VE trial_ends_at gelecekte
        if ($subscription->has_trial && $subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
            return 'trial';
        }

        // Period sona erdiyse expired
        if ($subscription->current_period_end && $subscription->current_period_end->isPast()) {
            return 'expired';
        }

        return $subscription->status;
    }
@endphp

<div wire:key="subscription-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-crown"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['active'] ?? 0 }}</div>
                            <div class="text-muted">Premium Aktif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-gift"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['trial'] ?? 0 }}</div>
                            <div class="text-muted">Deneme Süresi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-danger text-white avatar">
                                <i class="fas fa-hourglass-end"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['expired'] ?? 0 }}</div>
                            <div class="text-muted">Süresi Doldu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-secondary text-white avatar">
                                <i class="fas fa-ban"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['cancelled'] ?? 0 }}</div>
                            <div class="text-muted">İptal Edildi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Ara..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Tüm Durumlar</option>
                        <option value="active">Aktif</option>
                        <option value="trial">Deneme</option>
                        <option value="expired">Süresi Doldu</option>
                        <option value="cancelled">İptal Edildi</option>
                        <option value="paused">Duraklatıldı</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterPlan">
                        <option value="">Tüm Planlar</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->subscription_plan_id }}">{{ $plan->title_text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterCycle">
                        <option value="">Tüm Dönemler</option>
                        <option value="monthly">Aylık</option>
                        <option value="yearly">Yıllık</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscriptions List --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Abonelik No</th>
                        <th>Müşteri</th>
                        <th>Plan</th>
                        <th>Dönem</th>
                        <th>Bitiş Tarihi</th>
                        <th>Durum</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    @php
                        $effectiveStatus = getEffectiveStatus($subscription);
                        $statusLabel = $statusLabels[$effectiveStatus] ?? $effectiveStatus;
                        $statusColor = $statusColors[$effectiveStatus] ?? 'secondary';

                        // Dönem label
                        $cycleLabels = [
                            'daily' => 'Günlük',
                            'weekly' => 'Haftalık',
                            'monthly' => 'Aylık',
                            'quarterly' => '3 Aylık',
                            'yearly' => 'Yıllık',
                        ];
                        $cycleLabel = $cycleLabels[$subscription->billing_cycle] ?? ($subscription->getCycleLabel() ?? $subscription->cycle_key ?? '-');
                    @endphp
                    <tr wire:key="subscription-{{ $subscription->subscription_id }}">
                        <td>
                            <div class="font-weight-medium">{{ $subscription->subscription_number }}</div>
                            @if($subscription->has_trial && $effectiveStatus === 'trial')
                                <small class="text-info"><i class="fas fa-gift me-1"></i>Deneme</small>
                            @endif
                        </td>
                        <td>
                            @if($subscription->customer)
                            <div>{{ $subscription->customer->name }}</div>
                            <div class="text-muted small">{{ $subscription->customer->email }}</div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->plan)
                            {{ $subscription->plan->title_text }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $cycleLabel }}</td>
                        <td>
                            @if($effectiveStatus === 'trial' && $subscription->trial_ends_at)
                                <span class="text-info">{{ $subscription->trial_ends_at->format('d.m.Y H:i') }}</span>
                                <div class="text-muted small">
                                    @php
                                        $daysLeft = (int) floor(now()->diffInDays($subscription->trial_ends_at, false));
                                        $hoursLeft = (int) floor(now()->diffInHours($subscription->trial_ends_at, false)) % 24;
                                    @endphp
                                    @if($daysLeft > 0)
                                        {{ $daysLeft }} gün {{ $hoursLeft }} saat kaldı
                                    @elseif($hoursLeft > 0)
                                        {{ $hoursLeft }} saat kaldı
                                    @else
                                        Bugün bitiyor
                                    @endif
                                </div>
                            @elseif($subscription->current_period_end)
                                {{ $subscription->current_period_end->format('d.m.Y') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if(in_array($effectiveStatus, ['active', 'trial']))
                                    <button class="dropdown-item text-danger" wire:click="cancel({{ $subscription->subscription_id }})" wire:confirm="Bu aboneliği iptal etmek istediğinize emin misiniz?">
                                        <i class="fas fa-ban me-2"></i>İptal Et
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-users fa-4x text-muted"></i>
                                </div>
                                <p class="empty-title mt-2">Henüz abonelik yok</p>
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
</div>
