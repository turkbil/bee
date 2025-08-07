@extends('admin.layout')

@include('ai::helper')

@section('title', 'Kredi Detayları')

@section('content')
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
                            <!-- 🎯 EN ÖNEMLİ İSTATİSTİK: KALAN KREDİ -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="text-center p-4 bg-primary-lt rounded-3">
                                        <div class="text-secondary mb-2 h5">💰 Mevcut Bakiye</div>
                                        <div class="display-4 fw-bold text-primary mb-2">
                                            {{ format_credit(ai_get_credit_balance(), false) }}
                                        </div>
                                        <div class="h4 text-primary">KREDİ</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Diğer İstatistikler (Daha Az Önemli) -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Toplam Kullanılan</label>
                                        <div class="h4 text-red">
                                            {{ format_credit(ai_get_total_credits_used()) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Bu Ay Kullanılan</label>
                                        <div class="h4 text-orange">
                                            {{ format_credit(ai_get_monthly_credits_used()) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Bugün Kullanılan</label>
                                        <div class="h4 text-yellow">
                                            {{ format_credit(ai_get_daily_credits_used()) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Kullanım Oranı</label>
                                    @php
                                        $totalPurchased = ai_get_total_credits_purchased();
                                        $totalUsed = ai_get_total_credits_used();
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
@endsection