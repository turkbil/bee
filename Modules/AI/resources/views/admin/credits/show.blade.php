@extends('admin.layout')

@section('title', 'Kredi Detayları')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        AI Yönetimi
                    </div>
                    <h2 class="page-title">
                        <i class="fas fa-coins me-2"></i>
                        Kredi Detayları
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.credits.index') }}" class="btn btn-ghost-dark">
                            <i class="fas fa-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar me-2"></i>
                                Kredi Kullanım Detayları
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mevcut Bakiye</label>
                                        <div class="h3 text-blue">
                                            {{ number_format(ai_get_credit_balance(1), 4) }} Kredi
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Toplam Kullanılan</label>
                                        <div class="h3 text-red">
                                            {{ number_format(ai_get_total_credits_used(1), 4) }} Kredi
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Bu Ay Kullanılan</label>
                                        <div class="h3 text-orange">
                                            {{ number_format(ai_get_monthly_credits_used(1), 4) }} Kredi
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Bugün Kullanılan</label>
                                        <div class="h3 text-yellow">
                                            {{ number_format(ai_get_daily_credits_used(1), 4) }} Kredi
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Kullanım Oranı</label>
                                    @php
                                        $totalPurchased = ai_get_total_credits_purchased(1);
                                        $totalUsed = ai_get_total_credits_used(1);
                                        $usagePercentage = $totalPurchased > 0 ? ($totalUsed / $totalPurchased) * 100 : 0;
                                    @endphp
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-blue" style="width: {{ min(100, $usagePercentage) }}%"></div>
                                    </div>
                                    <small class="text-secondary">
                                        {{ number_format($usagePercentage, 1) }}% kullanıldı
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>
                                Kredi Bilgileri
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-secondary mb-1">Para Birimi</div>
                                <div class="fw-bold">USD (Amerikan Doları)</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-secondary mb-1">Hesaplama Yöntemi</div>
                                <div class="fw-bold">Token Bazlı Gerçek Maliyet</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-secondary mb-1">Desteklenen Providers</div>
                                <div class="fw-bold">
                                    <div class="badge bg-blue-lt me-1">Claude</div>
                                    <div class="badge bg-green-lt me-1">DeepSeek</div>
                                    <div class="badge bg-orange-lt">OpenAI</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-secondary mb-1">Fatura Döngüsü</div>
                                <div class="fw-bold">Kullandıkça Öde</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-secondary mb-1">Son Güncelleme</div>
                                <div class="fw-bold">{{ now()->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt me-2"></i>
                                Hızlı İşlemler
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-blue">
                                    <i class="fas fa-box me-1"></i>
                                    Kredi Paketleri
                                </a>
                                <a href="{{ route('admin.ai.credits.purchases') }}" class="btn btn-outline-blue">
                                    <i class="fas fa-shopping-cart me-1"></i>
                                    Satın Alma Geçmişi
                                </a>
                                <a href="{{ route('admin.ai.credits.usage') }}" class="btn btn-outline-blue">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Kullanım Raporları
                                </a>
                                <a href="{{ route('admin.ai.credits.transactions') }}" class="btn btn-outline-blue">
                                    <i class="fas fa-receipt me-1"></i>
                                    Kredi İşlemleri
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection