{{-- AI Debug Dashboard - Error Analysis --}}
@extends('admin.layout')

@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

@section('title')
⚠️ {{ __('ai::admin.error_analysis') }}
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.ai.debug.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Ana Dashboard'a Dön
        </a>
    </div>
    <div class="col-auto">
        <div class="btn-list">
            <div class="dropdown">
                <button class="btn btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i>Hata Filtreleri
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <form class="p-3" style="min-width: 280px;">
                        <div class="mb-3">
                            <label class="form-label">Zaman Aralığı</label>
                            <select name="date_range" class="form-select">
                                <option value="1">Son 24 Saat</option>
                                <option value="7" selected>Son 7 Gün</option>
                                <option value="30">Son 30 Gün</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hata Tipi</label>
                            <select name="error_type" class="form-select">
                                <option value="">Tüm Hatalar</option>
                                <option value="timeout">Timeout Hataları</option>
                                <option value="api">API Hataları</option>
                                <option value="validation">Validation Hataları</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="critical_only">
                            <label class="form-check-label">Sadece kritik hatalar</label>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Filtrele</button>
                    </form>
                </div>
            </div>
            <button class="btn btn-outline-warning" onclick="refreshErrors()">
                <i class="fas fa-sync-alt me-2"></i>Yenile
            </button>
            <button class="btn btn-outline-info" onclick="exportErrors()">
                <i class="fas fa-download me-2"></i>Rapor İndir
            </button>
        </div>
    </div>
</div>

{{-- Hata İstatistikleri --}}
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-danger-lt me-3">
                        <i class="fas fa-exclamation-triangle icon-lg text-danger"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Toplam Hata</div>
                        <div class="h2 mb-0 text-danger">
                            @if(!empty($errorData['error_summary']['total_errors']))
                                {{ number_format($errorData['error_summary']['total_errors']) }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="small text-muted">Son 7 günde</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-warning-lt me-3">
                        <i class="fas fa-percentage icon-lg text-warning"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Hata Oranı</div>
                        <div class="h2 mb-0 text-warning">
                            @if(!empty($errorData['error_summary']['error_rate']) && is_numeric($errorData['error_summary']['error_rate']))
                                {{ round($errorData['error_summary']['error_rate'], 1) }}%
                            @else
                                0.0%
                            @endif
                        </div>
                        <div class="small text-success">
                            @if(!empty($errorData['error_summary']['error_rate_change']))
                                <i class="fas fa-arrow-{{ $errorData['error_summary']['error_rate_change'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ $errorData['error_summary']['error_rate_change'] >= 0 ? '+' : '' }}{{ $errorData['error_summary']['error_rate_change'] }}% 
                                {{ $errorData['error_summary']['error_rate_change'] >= 0 ? 'artış' : 'düşüş' }}
                            @else
                                <i class="fas fa-minus me-1"></i>Değişim yok
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-info-lt me-3">
                        <i class="fas fa-list-alt icon-lg text-info"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Farklı Hata Tipi</div>
                        <div class="h2 mb-0 text-info">
                            @if(!empty($errorData['error_summary']['unique_errors']))
                                {{ $errorData['error_summary']['unique_errors'] }}
                            @else
                                0
                            @endif
                        </div>
                        <div class="small text-muted">Benzersiz hata mesajı</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-success-lt me-3">
                        <i class="fas fa-shield-alt icon-lg text-success"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Kurtarma Oranı</div>
                        <div class="h2 mb-0 text-success">
                            @if(!empty($errorData['recovery_rates']['auto_recovery_rate']) && is_numeric($errorData['recovery_rates']['auto_recovery_rate']))
                                {{ round($errorData['recovery_rates']['auto_recovery_rate'], 1) }}%
                            @else
                                0.0%
                            @endif
                        </div>
                        <div class="small text-muted">Otomatik çözüm</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hata Trend Analizi --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Hata Trend Analizi
                </h3>
                <div class="card-actions">
                    <div class="dropdown">
                        <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-download me-2"></i>Grafik İndir
                            </a>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-share me-2"></i>Paylaş
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="fas fa-chart-line fa-3x mb-3 opacity-50"></i>
                            <p>Hata trend grafigi yükleniyor...</p>
                            <div class="progress w-50 mx-auto">
                                <div class="progress-bar bg-danger progress-bar-indeterminate"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    En Sık Hata Mesajları
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Hata Mesajı</th>
                                <th class="text-end">Adet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($errorData['top_error_messages']))
                                @foreach($errorData['top_error_messages'] as $error)
                                <tr>
                                    <td>
                                        <div class="text-wrap" style="max-width: 200px;">
                                            <span class="fw-bold small text-danger">{{ Str::limit($error->error_message, 40) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-danger-lt">{{ $error->count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        <i class="fas fa-check-circle me-2 text-success"></i>
                                        Son dönemde hata bulunamadı
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Feature Bazında Hata Analizi --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-puzzle-piece me-2"></i>
                    Feature Bazında Hata Dağılımı
                </h3>
                <div class="card-actions">
                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#errorsByFeature">
                        <i class="fas fa-eye me-2"></i>Detay Göster/Gizle
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th class="text-center">Toplam İstek</th>
                                <th class="text-center">Hatalı İstek</th>
                                <th class="text-center">Hata Oranı</th>
                                <th class="text-center">Durum</th>
                                <th class="text-end">Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($errorData['error_by_feature']))
                                @foreach($errorData['error_by_feature'] as $feature)
                                @php
                                    $errorRate = $feature->total > 0 ? ($feature->errors / $feature->total) * 100 : 0;
                                    $statusClass = $errorRate > 10 ? 'danger' : ($errorRate > 5 ? 'warning' : 'success');
                                    $statusText = $errorRate > 10 ? 'Kritik' : ($errorRate > 5 ? 'Dikkat' : 'Sağlıklı');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar bg-{{ $statusClass }}-lt me-2">
                                                <i class="fas fa-{{ $errorRate > 10 ? 'exclamation-triangle' : ($errorRate > 5 ? 'exclamation-circle' : 'check-circle') }}"></i>
                                            </span>
                                            <div>
                                                <div class="fw-bold">{{ $feature->feature_slug ?: 'Chat' }}</div>
                                                <div class="small text-muted">AI Feature</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-blue-lt">{{ number_format($feature->total) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $statusClass }}-lt">{{ number_format($feature->errors) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="progress me-2" style="width: 50px; height: 8px;">
                                                <div class="progress-bar bg-{{ $statusClass }}" style="width: {{ min(100, $errorRate) }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-{{ $statusClass }}">{{ round($errorRate, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-outline text-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-{{ $statusClass }}" 
                                                data-bs-toggle="tooltip" 
                                                title="Detay görünüm">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-info-circle fa-2x mb-3 opacity-50"></i>
                                        <p>Feature bazında hata verisi henüz bulunmuyor</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="collapse mt-3" id="errorsByFeature">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">Hata Analiz Detayları</h4>
                        <p class="mb-0">
                            • <strong>Sağlıklı:</strong> %5'in altında hata oranı<br>
                            • <strong>Dikkat:</strong> %5-10 arası hata oranı<br>
                            • <strong>Kritik:</strong> %10'ün üzerinde hata oranı
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshErrors() {
    location.reload();
}

function exportErrors() {
    window.location.href = '{{ route("admin.ai.debug.export", ["type" => "csv"]) }}?error_report=1';
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection