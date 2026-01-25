@php
    View::share('pretitle', __('subscription::admin.subscriptions'));
@endphp

<div wire:key="subscription-stats-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    {{-- Navigation --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.subscription.stats') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Istatistikler
                </a>
                <a href="{{ route('admin.subscription.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list me-1"></i>Abonelik Listesi
                </a>
            </div>
        </div>
    </div>

    {{-- Genel Durum Istatistikleri --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-chart-line me-2"></i>Genel Durum
            </h3>
        </div>
        {{-- Aktif --}}
        <div class="col-6 col-lg-2">
            <div class="card border-success h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-success text-white avatar mb-2">
                        <i class="fas fa-crown"></i>
                    </span>
                    <div class="display-5 fw-bold text-success mb-1">{{ $stats['active'] ?? 0 }}</div>
                    <h5 class="mb-0">Aktif</h5>
                    <div class="text-muted small">Su an kullanan</div>
                </div>
            </div>
        </div>
        {{-- Sirada (Pending - odenmis, bekliyor) --}}
        <div class="col-6 col-lg-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-warning text-white avatar mb-2">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="display-5 fw-bold text-warning mb-1">{{ $stats['pending'] ?? 0 }}</div>
                    <h5 class="mb-0">Sirada</h5>
                    <div class="text-muted small">Odenmis</div>
                </div>
            </div>
        </div>
        {{-- Suresi Doldu --}}
        <div class="col-6 col-lg-2">
            <div class="card border-danger h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-danger text-white avatar mb-2">
                        <i class="fas fa-hourglass-end"></i>
                    </span>
                    <div class="display-5 fw-bold text-danger mb-1">{{ $stats['expired'] ?? 0 }}</div>
                    <h5 class="mb-0">Bitti</h5>
                    <div class="text-muted small">Suresi doldu</div>
                </div>
            </div>
        </div>
        {{-- Iptal --}}
        <div class="col-6 col-lg-2">
            <div class="card border-secondary h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-secondary text-white avatar mb-2">
                        <i class="fas fa-ban"></i>
                    </span>
                    <div class="display-5 fw-bold text-secondary mb-1">{{ $stats['cancelled'] ?? 0 }}</div>
                    <h5 class="mb-0">Iptal</h5>
                    <div class="text-muted small">Iptal edildi</div>
                </div>
            </div>
        </div>
        {{-- Toplam Abone --}}
        <div class="col-6 col-lg-2">
            <div class="card border-primary h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-primary text-white avatar mb-2">
                        <i class="fas fa-users"></i>
                    </span>
                    <div class="display-5 fw-bold text-primary mb-1">{{ $stats['total_active'] ?? 0 }}</div>
                    <h5 class="mb-0">Toplam</h5>
                    <div class="text-muted small">Aktif + Sirada</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kurumsal --}}
    @if(($stats['corporate'] ?? 0) > 0)
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-building text-purple me-2"></i>Kurumsal
            </h3>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-purple h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-purple text-white avatar mb-2">
                        <i class="fas fa-building"></i>
                    </span>
                    <div class="display-5 fw-bold text-purple mb-1">{{ $stats['corporate'] ?? 0 }}</div>
                    <h5 class="mb-0">Kurumsal Hesap</h5>
                    <div class="text-muted small">Sirket hesabi</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Kritik Durum Takibi --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Sure Takibi
            </h3>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-danger h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-danger text-white avatar mb-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div class="display-5 fw-bold text-danger mb-1">{{ $expiringStats['critical_24h'] ?? 0 }}</div>
                    <h5 class="mb-0">24 Saat</h5>
                    <div class="badge bg-danger w-100">KRITIK</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-warning text-white avatar mb-2">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="display-5 fw-bold text-warning mb-1">{{ $expiringStats['warning_7d'] ?? 0 }}</div>
                    <h5 class="mb-0">7 Gun</h5>
                    <div class="badge bg-warning w-100">UYARI</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-primary h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-primary text-white avatar mb-2">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div class="display-5 fw-bold text-primary mb-1">{{ $expiringStats['warning_30d'] ?? 0 }}</div>
                    <h5 class="mb-0">30 Gun</h5>
                    <div class="badge bg-primary-lt w-100">TAKIP</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-pink text-white avatar mb-2">
                        <i class="fas fa-calendar-day"></i>
                    </span>
                    <div class="display-5 fw-bold text-pink mb-1">{{ $expiringStats['expiring_today'] ?? 0 }}</div>
                    <h5 class="mb-0">Bugun</h5>
                    <div class="text-muted small">Bugun bitiyor</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center py-3">
                    <span class="bg-secondary text-white avatar mb-2">
                        <i class="fas fa-calendar-week"></i>
                    </span>
                    <div class="display-5 fw-bold mb-1">{{ $expiringStats['expiring_this_week'] ?? 0 }}</div>
                    <h5 class="mb-0">Bu Hafta</h5>
                    <div class="text-muted small">Haftalik toplam</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Paket Bazli Dagilim --}}
    @if(count($planStats) > 0)
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-chart-pie text-primary me-2"></i>Paket Bazli Dagilim
            </h3>
        </div>
        @foreach($planStats as $planStat)
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-1">{{ $planStat['plan']->title_text }}</h4>
                            <p class="text-muted mb-0 small">
                                {{ number_format($planStat['plan']->price ?? 0, 0, ',', '.') }} TL
                            </p>
                        </div>
                        <span class="badge bg-blue-lt text-blue">%{{ $planStat['percentage'] }}</span>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h2 mb-0">{{ $planStat['count'] }}</div>
                            <div class="text-muted small">Abone</div>
                        </div>
                        <div class="col-6">
                            <div class="h2 mb-0 text-success">{{ number_format($planStat['revenue'], 0, ',', '.') }}</div>
                            <div class="text-muted small">TL Gelir</div>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $planStat['percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Gelir Ozeti --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-money-bill-wave text-success me-2"></i>Gelir Ozeti
            </h3>
        </div>
        {{-- Toplam Aktif Gelir --}}
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <span class="bg-success text-white avatar avatar-lg mb-3">
                        <i class="fas fa-wallet fa-lg"></i>
                    </span>
                    <div class="display-4 text-success mb-2">{{ number_format($revenueStats['total_active_revenue'] ?? 0, 0, ',', '.') }}</div>
                    <h4 class="mb-1">TL Toplam</h4>
                    <div class="text-muted">{{ $revenueStats['total_active_count'] ?? 0 }} aboneden</div>
                </div>
            </div>
        </div>
        {{-- Aylik --}}
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <span class="bg-blue text-white avatar avatar-lg mb-3">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </span>
                    <div class="display-4 text-blue mb-2">{{ number_format($revenueStats['monthly_revenue'] ?? 0, 0, ',', '.') }}</div>
                    <h4 class="mb-1">TL Aylik</h4>
                    <div class="text-muted">{{ $revenueStats['monthly_count'] ?? 0 }} aylik abone</div>
                </div>
            </div>
        </div>
        {{-- Yillik --}}
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <span class="bg-purple text-white avatar avatar-lg mb-3">
                        <i class="fas fa-calendar fa-lg"></i>
                    </span>
                    <div class="display-4 text-purple mb-2">{{ number_format($revenueStats['yearly_revenue'] ?? 0, 0, ',', '.') }}</div>
                    <h4 class="mb-1">TL Yillik</h4>
                    <div class="text-muted">{{ $revenueStats['yearly_count'] ?? 0 }} yillik abone</div>
                </div>
            </div>
        </div>
    </div>
</div>
