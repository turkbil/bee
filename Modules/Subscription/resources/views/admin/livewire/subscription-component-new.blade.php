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

    {{-- Navigation --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subscription.stats') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>İstatistikler
                </a>
                <a href="{{ route('admin.subscription.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list me-1"></i>Abonelik Listesi
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter me-2"></i>Filtreler</h3>
            <div class="card-actions">
                <button class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">
                    <i class="fas fa-undo me-1"></i>Temizle
                </button>
            </div>
        </div>
        <div class="card-body">
            {{-- Temel Filtreler --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Arama</label>
                    <input type="text" class="form-control" placeholder="Müşteri ara..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Durum</label>
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
                    <label class="form-label small text-muted">Plan</label>
                    <select class="form-select" wire:model.live="filterPlan">
                        <option value="">Tüm Planlar</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->subscription_plan_id }}">{{ $plan->title_text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Dönem</label>
                    <select class="form-select" wire:model.live="filterCycle">
                        <option value="">Tüm Dönemler</option>
                        <option value="monthly">Aylık</option>
                        <option value="yearly">Yıllık</option>
                    </select>
                </div>
            </div>

            {{-- Gelişmiş Filtreler --}}
            <div class="border-top pt-3">
                <h4 class="text-muted small mb-3">
                    <i class="fas fa-bolt text-warning me-1"></i>Gelişmiş Filtreler
                </h4>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fas fa-clock me-1"></i>Kalan Süre
                        </label>
                        <select class="form-select" wire:model.live="filterRemainingDays">
                            <option value="">Tümü</option>
                            <option value="critical">🔴 24 Saat Altı (KRİTİK)</option>
                            <option value="warning">🟠 7 Gün Altı</option>
                            <option value="month">🟡 30 Gün Altı</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fas fa-calendar me-1"></i>Bitiş Tarihi
                        </label>
                        <select class="form-select" wire:model.live="filterExpiryRange">
                            <option value="">Tümü</option>
                            <option value="today">Bugün Bitenler</option>
                            <option value="this_week">Bu Hafta Bitenler</option>
                            <option value="this_month">Bu Ay Bitenler</option>
                            <option value="next_3_months">Önümüzdeki 3 Ay</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fas fa-rotate-left me-1"></i>Otomatik Yenileme
                        </label>
                        <select class="form-select" wire:model.live="filterAutoRenew">
                            <option value="">Tümü</option>
                            <option value="1">✅ Açık</option>
                            <option value="0">❌ Kapalı</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">
                            <i class="fas fa-gift me-1"></i>Deneme Durumu
                        </label>
                        <select class="form-select" wire:model.live="filterTrialStatus">
                            <option value="">Tümü</option>
                            <option value="active_trial">Aktif Deneme</option>
                            <option value="trial_to_premium">Deneme → Premium</option>
                            <option value="trial_to_cancel">Deneme → İptal</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Hızlı Filtre Butonları --}}
            <div class="border-top pt-3 mt-3">
                <p class="text-muted small mb-2">
                    <i class="fas fa-bolt text-warning me-1"></i>Hızlı Filtreler (Tek Tıkla)
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-danger" wire:click="quickFilterCritical">
                        <i class="fas fa-exclamation-triangle me-1"></i>Kritik (24s altı)
                    </button>
                    <button class="btn btn-sm btn-outline-warning" wire:click="quickFilterWarning">
                        <i class="fas fa-clock me-1"></i>Az Kalan (7g altı)
                    </button>
                    <button class="btn btn-sm btn-outline-pink" wire:click="quickFilterToday">
                        <i class="fas fa-calendar-day me-1"></i>Bugün Bitenler
                    </button>
                    <button class="btn btn-sm btn-outline-warning" wire:click="quickFilterAutoRenewOff">
                        <i class="fas fa-rotate-left me-1"></i>Yenileme Kapalı
                    </button>
                    <button class="btn btn-sm btn-outline-info" wire:click="quickFilterActiveTrial">
                        <i class="fas fa-gift me-1"></i>Aktif Denemeler
                    </button>
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
                                <div class="text-muted small countdown-timer"
                                     data-end-time="{{ $subscription->trial_ends_at->timestamp }}"
                                     data-subscription-id="{{ $subscription->subscription_id }}">
                                    @php
                                        $totalSeconds = now()->diffInSeconds($subscription->trial_ends_at, false);
                                        $daysLeft = (int) floor($totalSeconds / 86400);
                                        $hoursLeft = (int) floor(($totalSeconds % 86400) / 3600);
                                        $minutesLeft = (int) floor(($totalSeconds % 3600) / 60);
                                        $secondsLeft = (int) ($totalSeconds % 60);
                                    @endphp
                                    @if($totalSeconds <= 0)
                                        <span class="text-danger fw-bold">Süresi doldu!</span>
                                    @elseif($totalSeconds <= 60)
                                        <span class="text-danger fw-bold countdown-text">{{ $secondsLeft }} saniye kaldı</span>
                                    @elseif($totalSeconds <= 3600)
                                        <span class="text-warning fw-bold countdown-text">{{ $minutesLeft }} dakika {{ $secondsLeft }} saniye kaldı</span>
                                    @elseif($daysLeft < 1)
                                        <span class="countdown-text">{{ $hoursLeft }} saat {{ $minutesLeft }} dakika kaldı</span>
                                    @else
                                        <span class="countdown-text">{{ $daysLeft }} gün {{ $hoursLeft }} saat kaldı</span>
                                    @endif
                                </div>
                            @elseif($subscription->current_period_end)
                                {{ $subscription->current_period_end->format('d.m.Y') }}
                                <div class="text-muted small countdown-timer"
                                     data-end-time="{{ $subscription->current_period_end->timestamp }}"
                                     data-subscription-id="{{ $subscription->subscription_id }}">
                                    @php
                                        $totalSeconds = now()->diffInSeconds($subscription->current_period_end, false);
                                        $daysLeft = (int) floor($totalSeconds / 86400);
                                        $hoursLeft = (int) floor(($totalSeconds % 86400) / 3600);
                                        $minutesLeft = (int) floor(($totalSeconds % 3600) / 60);
                                        $secondsLeft = (int) ($totalSeconds % 60);
                                    @endphp
                                    @if($totalSeconds <= 0)
                                        <span class="text-danger fw-bold">Süresi doldu!</span>
                                    @elseif($totalSeconds <= 60)
                                        <span class="text-danger fw-bold countdown-text">{{ $secondsLeft }} saniye kaldı</span>
                                    @elseif($totalSeconds <= 3600)
                                        <span class="text-warning fw-bold countdown-text">{{ $minutesLeft }} dakika {{ $secondsLeft }} saniye kaldı</span>
                                    @elseif($daysLeft < 1)
                                        <span class="countdown-text">{{ $hoursLeft }} saat {{ $minutesLeft }} dakika kaldı</span>
                                    @else
                                        <span class="countdown-text">{{ $daysLeft }} gün {{ $hoursLeft }} saat kaldı</span>
                                    @endif
                                </div>
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

@push('scripts')
<script>
(function() {
    'use strict';

    // Countdown timer güncelleme fonksiyonu
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');
        const now = Math.floor(Date.now() / 1000);

        timers.forEach(timer => {
            const endTime = parseInt(timer.dataset.endTime);
            const countdownText = timer.querySelector('.countdown-text');

            if (!countdownText || !endTime) return;

            const totalSeconds = endTime - now;

            if (totalSeconds <= 0) {
                // Süresi doldu
                countdownText.className = 'text-danger fw-bold';
                countdownText.textContent = 'Süresi doldu!';
                return;
            }

            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            let text = '';
            let className = '';

            if (totalSeconds <= 60) {
                // Son 1 dakika - SANİYE göster (KIRMIZI)
                text = `${seconds} saniye kaldı`;
                className = 'text-danger fw-bold';
            } else if (totalSeconds <= 3600) {
                // Son 1 saat - DAKİKA + SANİYE göster (TURUNCU)
                text = `${minutes} dakika ${seconds} saniye kaldı`;
                className = 'text-warning fw-bold';
            } else if (days < 1) {
                // Son gün - SAAT + DAKİKA göster
                text = `${hours} saat ${minutes} dakika kaldı`;
                className = '';
            } else {
                // Gün kaldı - GÜN + SAAT göster
                text = `${days} gün ${hours} saat kaldı`;
                className = '';
            }

            countdownText.textContent = text;
            countdownText.className = className || 'countdown-text';
        });
    }

    // İlk yüklemede çalıştır
    updateCountdowns();

    // Her saniye güncelle
    setInterval(updateCountdowns, 1000);

    // Livewire component yenilendiğinde timer'ları yeniden başlat
    document.addEventListener('livewire:navigated', updateCountdowns);
    document.addEventListener('livewire:load', updateCountdowns);

    // Livewire component update sonrası
    Livewire.hook('message.processed', () => {
        setTimeout(updateCountdowns, 100);
    });
})();
</script>
@endpush
