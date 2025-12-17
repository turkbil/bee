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
                    <i class="fas fa-chart-bar me-1"></i>İstatistikler
                </a>
                <a href="{{ route('admin.subscription.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list me-1"></i>Abonelik Listesi
                </a>
            </div>
        </div>
    </div>

    {{-- Genel Durum İstatistikleri --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-chart-line me-2"></i>Genel Durum
            </h3>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-success text-white avatar avatar-lg">
                            <i class="fas fa-crown fa-2x"></i>
                        </span>
                        <span class="badge bg-success-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Ödeme yapmış, aktif olarak hizmet alan aboneler. Deneme süresi bitmiş ve premium'a geçmiş kullanıcılar.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-success mb-2">{{ $stats['active'] ?? 0 }}</div>
                    <h4 class="mb-1">Premium Aktif</h4>
                    <div class="text-muted small">Ödeme yapan aktif aboneler</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-info text-white avatar avatar-lg">
                            <i class="fas fa-gift fa-2x"></i>
                        </span>
                        <span class="badge bg-info-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Ücretsiz deneme süresi içinde olan kullanıcılar. Henüz ödeme yapmamış ama sistemi test ediyorlar.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-info mb-2">{{ $stats['trial'] ?? 0 }}</div>
                    <h4 class="mb-1">Deneme Süresi</h4>
                    <div class="text-muted small">Ücretsiz test eden kullanıcılar</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-danger text-white avatar avatar-lg">
                            <i class="fas fa-hourglass-end fa-2x"></i>
                        </span>
                        <span class="badge bg-danger-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Abonelik süresi dolmuş, henüz yenileme yapmamış kullanıcılar. Bunlara hatırlatma yapılmalı.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-danger mb-2">{{ $stats['expired'] ?? 0 }}</div>
                    <h4 class="mb-1">Süresi Doldu</h4>
                    <div class="text-muted small">Yenileme bekleyen aboneler</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-secondary h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-secondary text-white avatar avatar-lg">
                            <i class="fas fa-ban fa-2x"></i>
                        </span>
                        <span class="badge bg-secondary-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Aboneliklerini iptal etmiş kullanıcılar. İptal nedenlerini analiz edin.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-secondary mb-2">{{ $stats['cancelled'] ?? 0 }}</div>
                    <h4 class="mb-1">İptal Edildi</h4>
                    <div class="text-muted small">Abonelik iptal edenler</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kritik Durum Takibi --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Kritik Durum Takibi
            </h3>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-danger text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="ACİL! Yarın süresi dolacak abonelikler. Bu müşterilere HEMEN ulaşın, hatırlatma gönderin.">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold text-danger mb-2">{{ $expiringStats['critical_24h'] ?? 0 }}</div>
                    <h5 class="mb-1">24 Saat Altı</h5>
                    <div class="badge bg-danger text-white w-100">KRİTİK - ACİL!</div>
                    <div class="text-muted small mt-2">Yarın süresi dolacaklar</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-warning text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="UYARI! Bir hafta içinde süresi dolacak abonelikler. Hatırlatma e-postası gönderin.">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold text-warning mb-2">{{ $expiringStats['warning_7d'] ?? 0 }}</div>
                    <h5 class="mb-1">7 Gün Altı</h5>
                    <div class="badge bg-warning text-white w-100">UYARI</div>
                    <div class="text-muted small mt-2">Bu hafta dolacaklar</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-primary text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="30 gün içinde süresi dolacak abonelikler. Yenileme kampanyası planlanabilir.">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold text-primary mb-2">{{ $expiringStats['warning_30d'] ?? 0 }}</div>
                    <h5 class="mb-1">30 Gün Altı</h5>
                    <div class="badge bg-primary-lt w-100">TAKİP</div>
                    <div class="text-muted small mt-2">Bu ay dolacaklar</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-pink h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-pink text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Bugün süresi dolan abonelikler. Acil hatırlatma gerekir.">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold text-pink mb-2">{{ $expiringStats['expiring_today'] ?? 0 }}</div>
                    <h5 class="mb-1">Bugün Bitenler</h5>
                    <div class="text-muted small mt-2">Bugün dolacaklar</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-secondary text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Bu hafta süresi dolacak tüm abonelikler.">
                            <i class="fas fa-calendar-week"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold mb-2">{{ $expiringStats['expiring_this_week'] ?? 0 }}</div>
                    <h5 class="mb-1">Bu Hafta</h5>
                    <div class="text-muted small mt-2">Haftalık toplam</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="bg-warning text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="RİSK! Otomatik yenileme kapalı abonelikler. Bunlar süre bitince otomatik yenilenmeyecek.">
                            <i class="fas fa-rotate-left"></i>
                        </span>
                    </div>
                    <div class="display-5 font-weight-bold text-warning mb-2">{{ $expiringStats['auto_renew_off'] ?? 0 }}</div>
                    <h5 class="mb-1">Yenileme Kapalı</h5>
                    <div class="badge bg-warning text-white w-100">RİSK</div>
                    <div class="text-muted small mt-2">Elle yenilenmeli</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Paket Bazlı Dağılım --}}
    @if(count($planStats) > 0)
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-chart-pie text-primary me-2"></i>Paket Bazlı Dağılım
            </h3>
        </div>
        @foreach($planStats as $planStat)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="mb-1">{{ $planStat['plan']->title_text }}</h3>
                            <p class="text-muted mb-0">
                                {{ $planStat['plan']->cycle_key ?? 'Aylık' }} - {{ number_format($planStat['plan']->price ?? 0, 2) }} {{ $planStat['plan']->currency ?? 'TL' }}
                            </p>
                        </div>
                        <span class="bg-blue text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Bu paketten kaç abone var ve ne kadar gelir getiriyor?">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="text-muted small">Toplam Abone</div>
                            <div class="h2 mb-0">{{ $planStat['count'] }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Toplam Gelir</div>
                            <div class="h2 mb-0 text-success">{{ number_format($planStat['revenue'], 0) }} TL</div>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ $planStat['percentage'] }}%"></div>
                    </div>
                    <div class="text-center text-muted small">
                        Toplam abonelerin <strong>%{{ $planStat['percentage'] }}</strong>'i bu pakette
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Deneme Süresi Analizi --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-gift text-info me-2"></i>Deneme Süresi Analizi
            </h3>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-blue text-white avatar avatar-lg">
                            <i class="fas fa-play fa-2x"></i>
                        </span>
                        <span class="badge bg-info-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Şu anda ücretsiz deneme süresinde olan kullanıcılar. Bunları premium'a dönüştürmeye odaklanın.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-info mb-2">{{ $trialStats['active_trial'] ?? 0 }}</div>
                    <h4 class="mb-1">Aktif Deneme</h4>
                    <div class="text-muted small">Test süreci devam ediyor</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-success text-white avatar avatar-lg">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </span>
                        <span class="badge bg-success-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="BAŞARI! Deneme süresi bittikten sonra ödeme yapıp premium'a geçenler. Dönüşüm oranı ne kadar yüksekse o kadar iyidir.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-success mb-2">{{ $trialStats['trial_to_premium'] ?? 0 }}</div>
                    <h4 class="mb-1">Premium'a Geçti</h4>
                    <div class="badge bg-success text-white w-100 mb-2">BAŞARILI DÖNÜŞÜM</div>
                    <div class="text-success small fw-bold">%{{ $trialStats['conversion_rate'] ?? 0 }} dönüşüm oranı</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-danger h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-danger text-white avatar avatar-lg">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </span>
                        <span class="badge bg-danger-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="KAYIP! Deneme süresi bittikten sonra ödeme yapmayan kullanıcılar. Neden geçmediklerini analiz edin.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-danger mb-2">{{ $trialStats['trial_to_cancel'] ?? 0 }}</div>
                    <h4 class="mb-1">Geçmedi</h4>
                    <div class="badge bg-danger text-white w-100 mb-2">KAYIP</div>
                    <div class="text-danger small fw-bold">%{{ 100 - ($trialStats['conversion_rate'] ?? 0) }} kayıp oranı</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="bg-orange text-white avatar avatar-lg">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </span>
                        <span class="badge bg-warning-lt" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Bu hafta deneme süresi bitecek kullanıcılar. Bunlara hatırlatma e-postası gönderin ve premium avantajları anlatın.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-4 font-weight-bold text-warning mb-2">{{ $trialStats['trial_ending_this_week'] ?? 0 }}</div>
                    <h4 class="mb-1">Bu Hafta Bitenler</h4>
                    <div class="text-muted small">Hatırlatma gönderin</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gelir Özeti --}}
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h3 class="text-muted">
                <i class="fas fa-money-bill-wave text-success me-2"></i>Gelir Özeti
            </h3>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="text-muted mb-0">Aylık Toplam Gelir</h3>
                        </div>
                        <span class="bg-success text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Her ay tekrar eden gelir. Aylık paket abonelerinden gelen toplam gelir.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-3 text-success mb-2">{{ number_format($revenueStats['monthly_revenue'] ?? 0, 0) }} TL</div>
                    <div class="text-muted">{{ $revenueStats['monthly_count'] ?? 0 }} aylık aboneden</div>
                    <div class="mt-2 small text-success">
                        <i class="fas fa-sync-alt me-1"></i>Tekrar eden gelir
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="text-muted mb-0">Yıllık Toplam Gelir</h3>
                        </div>
                        <span class="bg-primary text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Yılda bir kere yenilenen gelir. Yıllık paket abonelerinden gelen toplam gelir.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-3 text-primary mb-2">{{ number_format($revenueStats['yearly_revenue'] ?? 0, 0) }} TL</div>
                    <div class="text-muted">{{ $revenueStats['yearly_count'] ?? 0 }} yıllık aboneden</div>
                    <div class="mt-2 small text-primary">
                        <i class="fas fa-calendar-alt me-1"></i>Yıllık gelir
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-purple h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="text-muted mb-0">Beklenen Gelir (Bu Ay)</h3>
                        </div>
                        <span class="bg-purple text-white avatar" data-bs-toggle="tooltip" data-bs-placement="top"
                              title="Bu ay içinde otomatik yenilenecek aboneliklerden gelecek gelir. Tahmin edilen gelir.">
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                    <div class="display-3 text-purple mb-2">{{ number_format($revenueStats['expected_revenue'] ?? 0, 0) }} TL</div>
                    <div class="text-muted">{{ $revenueStats['renewing_count'] ?? 0 }} yenilenecek abonelik</div>
                    <div class="mt-2 small text-purple">
                        <i class="fas fa-hourglass-half me-1"></i>Otomatik yenileme ile
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Bootstrap tooltip'leri aktif et
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
