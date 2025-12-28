@php
    View::share('pretitle', __('subscription::admin.subscriptions'));

    $statusLabels = [
        'active' => 'Aktif',
        'trial' => 'Deneme',
        'expired' => 'Süresi Doldu',
        'cancelled' => 'İptal',
        'paused' => 'Durduruldu',
        'pending' => 'Sırada',
        'pending_payment' => 'Ödeme Bekliyor',
    ];

    $statusColors = [
        'active' => 'success',
        'trial' => 'info',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'paused' => 'warning',
        'pending' => 'warning',
        'pending_payment' => 'orange',
    ];
@endphp

<div wire:key="subscription-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Müşteri ara...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">Tüm Durumlar</option>
                        <option value="active">Aktif</option>
                        <option value="pending">Sırada</option>
                        <option value="pending_payment">Ödeme Bekliyor</option>
                        <option value="expired">Süresi Doldu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.live="filterPlan">
                        <option value="">Tüm Planlar</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->subscription_plan_id }}">{{ $plan->title_text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-secondary" wire:click="clearFilters">
                        <i class="fas fa-undo me-1"></i>Temizle
                    </button>
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
                                Müşteri
                            </button>
                        </th>
                        <th>Plan</th>
                        <th>
                            <button class="table-sort {{ $sortField === 'current_period_end' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}" wire:click="sortBy('current_period_end')">
                                Kalan
                            </button>
                        </th>
                        <th>Son Ödeme</th>
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

                        // Toplam kalan gün (users.subscription_expires_at)
                        $userExpiry = $subscription->customer?->subscription_expires_at;
                        $totalDaysLeft = $userExpiry && $userExpiry->isFuture()
                            ? (int) now()->diffInDays($userExpiry, false)
                            : 0;

                        // Son ödeme bilgisi
                        $lastOrder = null;
                        if ($subscription->customer) {
                            $lastOrder = \Modules\Cart\App\Models\Order::where('user_id', $subscription->customer->id)
                                ->whereIn('payment_status', ['paid', 'completed'])
                                ->orderBy('created_at', 'desc')
                                ->first();
                        }

                        // Toplam ödenen
                        $totalPaid = \Modules\Subscription\App\Models\Subscription::where('user_id', $subscription->user_id)
                            ->whereIn('status', ['active', 'pending', 'cancelled', 'expired'])
                            ->sum('price_per_cycle');
                    @endphp
                    <tr wire:key="subscription-{{ $subscription->subscription_id }}" class="{{ $effectiveStatus === 'pending_payment' ? 'bg-orange-lt' : '' }}">
                        <td>
                            @if($subscription->customer)
                            <button type="button" wire:click="openUserModal({{ $subscription->customer->id }})" class="btn btn-link text-reset text-decoration-none p-0 text-start">
                                <div class="fw-medium">
                                    <span class="badge bg-secondary-lt me-1">#{{ $subscription->customer->id }}</span>
                                    {{ $subscription->customer->name }}
                                    <i class="fas fa-external-link-alt text-muted ms-1" style="font-size: 10px;"></i>
                                </div>
                                <div class="text-muted small">{{ $subscription->customer->email }}</div>
                            </button>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $subscription->plan?->title_text ?? '-' }}</span>
                        </td>
                        <td>
                            @if($totalDaysLeft > 0)
                                <div class="text-success fw-semibold">{{ $totalDaysLeft }} gün</div>
                                <div class="text-muted small">{{ $userExpiry?->format('d.m.Y') }}</div>
                            @elseif($effectiveStatus === 'pending_payment')
                                <span class="text-muted">-</span>
                            @else
                                <span class="text-danger">Bitti</span>
                            @endif
                        </td>
                        <td>
                            @if($lastOrder)
                                <div>{{ $lastOrder->created_at->format('d.m.Y') }}</div>
                                <div class="text-muted small">{{ number_format($lastOrder->total_amount, 0, ',', '.') }} ₺</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-warning">{{ number_format($totalPaid, 0, ',', '.') }} ₺</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$effectiveStatus] ?? 'secondary' }}">
                                {{ $statusLabels[$effectiveStatus] ?? $effectiveStatus }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <button class="dropdown-item" wire:click="openUserModal({{ $subscription->customer?->id ?? 0 }})">
                                        <i class="fas fa-eye me-2"></i>Detay
                                    </button>
                                    @if(in_array($effectiveStatus, ['active', 'trial']))
                                    <button class="dropdown-item text-danger" wire:click="terminateNow({{ $subscription->subscription_id }})" wire:confirm="Bu aboneliği HEMEN sonlandırmak istediğinize emin misiniz?">
                                        <i class="fas fa-times-circle me-2"></i>Sonlandır
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Abonelik bulunamadı
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
                                <span class="badge bg-secondary me-1">#{{ $selectedUserData['user']['id'] }}</span>
                                {{ $selectedUserData['user']['name'] }}
                            </h5>
                            <small class="text-muted">{{ $selectedUserData['user']['email'] }}</small>
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
                                    <div class="text-muted small">Kalan Gün</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-blue-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-blue">{{ $selectedUserData['stats']['total_subscriptions'] }}</div>
                                    <div class="text-muted small">Abonelik</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-yellow-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-yellow">{{ number_format((float) ($selectedUserData['stats']['total_paid'] ?? 0), 0, ',', '.') }}₺</div>
                                    <div class="text-muted small">Ödenen</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-sm bg-purple-lt">
                                <div class="card-body text-center py-3">
                                    <div class="h2 mb-0 text-purple">{{ count($selectedUserData['orders']) }}</div>
                                    <div class="text-muted small">Ödeme</div>
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
                                <i class="fas fa-credit-card me-2"></i>Ödemeler
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- Abonelikler Tab --}}
                        <div class="tab-pane active show" id="tab-subscriptions">
                            @php
                                $subStatusColors = ['active' => 'green', 'pending' => 'yellow', 'pending_payment' => 'orange', 'trial' => 'cyan', 'cancelled' => 'secondary', 'expired' => 'red'];
                                $subStatusLabels = ['active' => 'Aktif', 'pending' => 'Sırada', 'pending_payment' => 'Ödeme Bekliyor', 'trial' => 'Deneme', 'cancelled' => 'İptal', 'expired' => 'Bitti'];
                            @endphp

                            <div class="space-y-3">
                                @forelse($selectedUserData['subscriptions'] as $sub)
                                <div class="card border-start border-4 border-{{ $subStatusColors[$sub['status']] ?? 'secondary' }} mb-2">
                                    <div class="card-body py-3">
                                        {{-- Header --}}
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-{{ $subStatusColors[$sub['status']] ?? 'secondary' }}-lt text-{{ $subStatusColors[$sub['status']] ?? 'secondary' }}">
                                                    {{ $subStatusLabels[$sub['status']] ?? $sub['status'] }}
                                                </span>
                                                <span class="fw-semibold">{{ $sub['plan_title'] }}</span>
                                                <span class="text-muted">{{ $sub['cycle_label'] }}</span>
                                            </div>
                                            <div class="d-flex gap-1">
                                                @if($sub['can_activate'])
                                                <button class="btn btn-sm btn-ghost-success" wire:click="activateSubscription({{ $sub['id'] }})" wire:confirm="Aktif etmek istediğinize emin misiniz?" title="Aktif Et">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                @endif
                                                @if($sub['can_delete'])
                                                <button class="btn btn-sm btn-ghost-danger" wire:click="deleteSubscription({{ $sub['id'] }})" wire:confirm="Silmek istediğinize emin misiniz?" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="row text-sm">
                                            <div class="col-4">
                                                <span class="text-muted">Dönem:</span>
                                                <span class="ms-1">{{ $sub['current_period_start'] ?? '-' }} → {{ $sub['current_period_end'] ?? '-' }}</span>
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted">Kalan:</span>
                                                @if($sub['days_left'] > 0)
                                                <span class="ms-1 text-success fw-medium">{{ $sub['days_left'] }} gün</span>
                                                @else
                                                <span class="ms-1 text-muted">-</span>
                                                @endif
                                            </div>
                                            <div class="col-4">
                                                <span class="text-muted">Tutar:</span>
                                                <span class="ms-1 text-warning fw-medium">{{ $sub['price'] }}</span>
                                            </div>
                                        </div>

                                        {{-- Payment Info --}}
                                        <div class="border-top mt-2 pt-2 d-flex justify-content-between align-items-center text-sm">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($sub['payment_status'] === 'manual')
                                                    <i class="fas fa-user-check text-purple"></i>
                                                    <span class="text-muted">Manuel Onay</span>
                                                @elseif($sub['payment_status'] === 'paid' || $sub['payment_status'] === 'completed')
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    <span class="text-muted">Ödendi</span>
                                                    @if($sub['payment_method'])
                                                    <span class="text-muted">•</span>
                                                    <span class="text-muted">{{ $sub['payment_method'] }}</span>
                                                    @endif
                                                @elseif($sub['status'] === 'pending_payment')
                                                    <i class="fas fa-clock text-orange"></i>
                                                    <span class="text-orange">Ödeme Bekleniyor</span>
                                                @else
                                                    <i class="fas fa-question-circle text-muted"></i>
                                                    <span class="text-muted">{{ $sub['payment_label'] }}</span>
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
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Abonelik bulunamadı
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Ödemeler Tab --}}
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
                                            <div class="text-muted small">
                                                <span>{{ $order['payment_method'] ?? 'Kredi Kartı' }}</span>
                                                <span class="mx-1">•</span>
                                                <span>{{ $order['date'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold text-{{ $order['payment_status'] === 'paid' ? 'success' : 'warning' }}">{{ $order['total'] }}</div>
                                        <a href="{{ route('admin.orders.index') }}?search={{ $order['number'] }}" class="text-muted small" target="_blank">
                                            {{ $order['number'] }}
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-credit-card fa-2x mb-2 d-block"></i>
                                Ödeme kaydı bulunamadı
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeUserModal">
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
