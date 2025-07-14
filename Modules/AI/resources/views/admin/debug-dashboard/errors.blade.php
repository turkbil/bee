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
            <div class="small text-uppercase fw-bold">Toplam Hata</div>
            <div class="h2 mb-0 text-danger">
              @if(!empty($errorData['error_summary']['total_errors']))
                {{ number_format($errorData['error_summary']['total_errors']) }}
              @else
                0
              @endif
            </div>
            <div class="small">Son 7 günde</div>
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
            <div class="small text-uppercase fw-bold">Hata Oranı</div>
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
            <div class="small text-uppercase fw-bold">Farklı Hata Tipi</div>
            <div class="h2 mb-0 text-info">
              @if(!empty($errorData['error_summary']['unique_errors']))
                {{ $errorData['error_summary']['unique_errors'] }}
              @else
                0
              @endif
            </div>
            <div class="small">Benzersiz hata mesajı</div>
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
            <div class="small text-uppercase fw-bold">Kurtarma Oranı</div>
            <div class="h2 mb-0 text-success">
              @if(!empty($errorData['recovery_rates']['auto_recovery_rate']) && is_numeric($errorData['recovery_rates']['auto_recovery_rate']))
                {{ round($errorData['recovery_rates']['auto_recovery_rate'], 1) }}%
              @else
                0.0%
              @endif
            </div>
            <div class="small">Otomatik çözüm</div>
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
        <div id="errorTrendChart" style="height: 300px;"></div>
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
                    <span class="text-danger">{{ $error->count }}</span>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="2" class="text-center py-4">
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
                        <div class="small">AI Feature</div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span>{{ number_format($feature->total) }}</span>
                  </td>
                  <td class="text-center">
                    <span class="text-{{ $statusClass }}">{{ number_format($feature->errors) }}</span>
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
                    <span class="text-{{ $statusClass }}">{{ $statusText }}</span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary" 
                            type="button" 
                            onclick="openFeatureErrorModal({{ json_encode($feature) }}, {{ $errorRate }})"
                            style="height: 32px;">
                      <i class="fas fa-search-plus me-1"></i>Detay
                    </button>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="6" class="text-center py-5">
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

{{-- Feature Error Detail Modal --}}
<div class="modal fade" id="featureErrorModal" tabindex="-1" aria-labelledby="featureErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="featureErrorModalLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Feature Hata Analizi
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="featureErrorModalBody">
        {{-- Content will be loaded via JavaScript --}}
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2 text-muted">Hata detayları yükleniyor...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Kapat
        </button>
        <button type="button" class="btn btn-primary" onclick="goToDetailedAnalysis()">
          <i class="fas fa-chart-line me-2"></i>Detaylı Analiz
        </button>
        <button type="button" class="btn btn-warning" onclick="exportFeatureErrorData()">
          <i class="fas fa-download me-2"></i>Veri İndir
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Global variables for modal data
let currentFeatureData = null;
let currentErrorRate = 0;

// Vanilla JS document ready - Fixed Bootstrap Modal
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips if available
  if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function(tooltip) {
      try {
        new bootstrap.Tooltip(tooltip);
      } catch (e) {
        console.log('Tooltip initialization skipped:', e);
      }
    });
  }
  
  // Initialize Error Trend Chart
  initializeErrorTrendChart();
});

function openFeatureErrorModal(featureData, errorRate) {
  currentFeatureData = featureData;
  currentErrorRate = errorRate;
  
  // Update modal content with jQuery
  $('#featureErrorModalBody').html(generateFeatureErrorHTML(featureData, errorRate));
  
  // Show modal with jQuery
  $('#featureErrorModal').modal('show');
}

function generateFeatureErrorHTML(feature, errorRate) {
  const statusClass = errorRate > 10 ? 'danger' : (errorRate > 5 ? 'warning' : 'success');
  const statusText = errorRate > 10 ? 'Kritik' : (errorRate > 5 ? 'Dikkat' : 'Sağlıklı');
  const statusIcon = errorRate > 10 ? 'exclamation-triangle' : (errorRate > 5 ? 'exclamation-circle' : 'check-circle');
  
  return `
    <div class="row">
      <div class="col-md-6">
        <div class="card border-${statusClass}">
          <div class="card-header bg-${statusClass}-lt">
            <h6 class="mb-0 text-${statusClass}"><i class="fas fa-${statusIcon} me-2"></i>Feature Özeti</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm mb-0">
              <tr><td class="fw-bold">Feature Adı:</td><td class="text-primary">${feature.feature_slug || 'Chat'}</td></tr>
              <tr><td class="fw-bold">Toplam İstek:</td><td><span class="text-info">${parseInt(feature.total).toLocaleString()}</span></td></tr>
              <tr><td class="fw-bold">Hatalı İstek:</td><td><span class="text-${statusClass}">${parseInt(feature.errors).toLocaleString()}</span></td></tr>
              <tr><td class="fw-bold">Hata Oranı:</td><td><span class="text-${statusClass} fw-bold">${errorRate.toFixed(1)}%</span></td></tr>
              <tr><td class="fw-bold">Durum:</td><td><span class="text-${statusClass}"><i class="fas fa-${statusIcon} me-1"></i>${statusText}</span></td></tr>
            </table>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card border-info">
          <div class="card-header bg-info-lt">
            <h6 class="mb-0 text-info"><i class="fas fa-chart-bar me-2"></i>Performans Metrikleri</h6>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-6">
                <div class="h2 mb-0 text-${statusClass}">${errorRate.toFixed(1)}%</div>
                <div class="small text-muted">Hata Oranı</div>
              </div>
              <div class="col-6">
                <div class="h2 mb-0 text-success">${(100 - errorRate).toFixed(1)}%</div>
                <div class="small text-muted">Başarı Oranı</div>
              </div>
            </div>
            <div class="progress mt-3" style="height: 8px;">
              <div class="progress-bar bg-success" style="width: ${100 - errorRate}%"></div>
              <div class="progress-bar bg-${statusClass}" style="width: ${errorRate}%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row mt-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Öneriler ve Aksiyon Planı</h6>
          </div>
          <div class="card-body">
            ${errorRate > 10 ? `
              <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-danger text-white me-3">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                  <h4 class="mb-0 text-danger">Kritik Durum!</h4>
                  <div class="text-muted small">Bu feature immediate action gerektiriyor</div>
                </div>
              </div>
              
              <div class="alert alert-danger border-danger">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-danger mb-3"><i class="fas fa-tasks me-1"></i>Acil Aksiyonlar</h6>
                    <div class="list-group list-group-flush">
                      <div class="list-group-item px-0 py-2 border-0 bg-transparent">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-search text-danger me-2"></i>
                          <div>
                            <div class="fw-bold">Error logs detaylı analizi</div>
                            <small class="text-muted">Son 24 saatteki tüm hataları incele</small>
                          </div>
                        </div>
                      </div>
                      <div class="list-group-item px-0 py-2 border-0 bg-transparent">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-code-branch text-warning me-2"></i>
                          <div>
                            <div class="fw-bold">Son deployment değişiklikleri</div>
                            <small class="text-muted">Git history ve recent changes kontrol</small>
                          </div>
                        </div>
                      </div>
                      <div class="list-group-item px-0 py-2 border-0 bg-transparent">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-chart-line text-info me-2"></i>
                          <div>
                            <div class="fw-bold">Performance monitoring aktif</div>
                            <small class="text-muted">Real-time alerts ve notifications</small>
                          </div>
                        </div>
                      </div>
                      <div class="list-group-item px-0 py-2 border-0 bg-transparent">
                        <div class="d-flex align-items-center">
                          <i class="fas fa-users text-primary me-2"></i>
                          <div>
                            <div class="fw-bold">Kullanıcı yönlendirme</div>
                            <small class="text-muted">Alternative features ve backup plans</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-danger mb-3"><i class="fas fa-cogs me-1"></i>Teknik Adımlar</h6>
                    <div class="row g-2">
                      <div class="col-6">
                        <div class="card border-danger">
                          <div class="card-body p-2 text-center">
                            <i class="fas fa-bug text-danger mb-1"></i>
                            <div class="small fw-bold">Error Tracking</div>
                            <div class="text-muted" style="font-size: 10px;">Sentry/logs</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="card border-warning">
                          <div class="card-body p-2 text-center">
                            <i class="fas fa-undo text-warning mb-1"></i>
                            <div class="small fw-bold">Rollback</div>
                            <div class="text-muted" style="font-size: 10px;">Previous version</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="card border-info">
                          <div class="card-body p-2 text-center">
                            <i class="fas fa-bell text-info mb-1"></i>
                            <div class="small fw-bold">Alert Team</div>
                            <div class="text-muted" style="font-size: 10px;">DevOps/Engineering</div>
                          </div>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="card border-primary">
                          <div class="card-body p-2 text-center">
                            <i class="fas fa-shield-alt text-primary mb-1"></i>
                            <div class="small fw-bold">Escalate</div>
                            <div class="text-muted" style="font-size: 10px;">Management</div>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="mt-3">
                      <div class="bg-danger-lt p-3 rounded">
                        <div class="d-flex justify-content-between align-items-center">
                          <div>
                            <div class="fw-bold text-danger">Kritik Metrikler</div>
                            <div class="small text-muted">Anında müdahale gerekli</div>
                          </div>
                          <div class="text-end">
                            <div class="h4 mb-0 text-danger">${errorRate.toFixed(1)}%</div>
                            <div class="small text-muted">Hata Oranı</div>
                          </div>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                          <div class="progress-bar bg-danger" style="width: ${Math.min(100, errorRate)}%"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            ` : errorRate > 5 ? `
              <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-warning text-white me-3">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                  <h4 class="mb-0 text-warning">Dikkat Gerekli</h4>
                  <div class="text-muted small">Enhanced monitoring ve analiz öneriliyor</div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="text-center p-3 border border-warning rounded">
                    <i class="fas fa-chart-bar text-warning fa-2x mb-2"></i>
                    <div class="fw-bold">Pattern Analizi</div>
                    <div class="small text-muted">Error trendlerini incele</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="text-center p-3 border border-info rounded">
                    <i class="fas fa-clock text-info fa-2x mb-2"></i>
                    <div class="fw-bold">Spike Kontrolü</div>
                    <div class="small text-muted">Zaman dilimi analizi</div>
                  </div>
                </div>
              </div>
            ` : `
              <div class="d-flex align-items-center mb-3">
                <div class="avatar bg-success text-white me-3">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div>
                  <h4 class="mb-0 text-success">Sağlıklı Durum</h4>
                  <div class="text-muted small">Feature optimal çalışıyor, routine monitoring yeterli</div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <div class="text-center p-3 border border-success rounded">
                    <i class="fas fa-check text-success fa-2x mb-2"></i>
                    <div class="fw-bold">Optimal</div>
                    <div class="small text-muted">Hata oranı kabul edilebilir</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="text-center p-3 border border-info rounded">
                    <i class="fas fa-eye text-info fa-2x mb-2"></i>
                    <div class="fw-bold">Monitor</div>
                    <div class="small text-muted">Routine takip yeterli</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="text-center p-3 border border-primary rounded">
                    <i class="fas fa-thumbs-up text-primary fa-2x mb-2"></i>
                    <div class="fw-bold">Continue</div>
                    <div class="small text-muted">Mevcut yapı iyi</div>
                  </div>
                </div>
            `}
          </div>
        </div>
      </div>
    </div>
    
    <div class="row mt-3">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Son 24 Saat Trend</h6>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="text-muted small">Mock trend data - gerçek trend buraya gelecek</div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">00:00</small>
                <div class="progress flex-fill mx-2" style="height: 4px;">
                  <div class="progress-bar bg-${statusClass}" style="width: ${Math.min(100, errorRate * 2)}%"></div>
                </div>
                <small class="text-muted">23:59</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Hata Kategorileri</h6>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="text-muted small">Error kategorileri - gerçek data buraya gelecek</div>
              <div class="row mt-2">
                <div class="col-4">
                  <div class="text-danger">${Math.round(errorRate * 0.6)}</div>
                  <div class="small text-muted">API Errors</div>
                </div>
                <div class="col-4">
                  <div class="text-warning">${Math.round(errorRate * 0.3)}</div>
                  <div class="small text-muted">Validation</div>
                </div>
                <div class="col-4">
                  <div class="text-info">${Math.round(errorRate * 0.1)}</div>
                  <div class="small text-muted">Timeout</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function goToDetailedAnalysis() {
  if (!currentFeatureData) {
    alert('Feature data bulunamadı!');
    return;
  }
  
  const featureSlug = currentFeatureData.feature_slug || 'chat';
  const url = '{{ route("admin.ai.debug.dashboard") }}?feature=' + encodeURIComponent(featureSlug);
  window.open(url, '_blank');
}

function exportFeatureErrorData() {
  if (!currentFeatureData) {
    alert('İhraç edilecek veri bulunamadı!');
    return;
  }
  
  const exportData = {
    feature: currentFeatureData,
    error_rate: currentErrorRate,
    export_time: new Date().toISOString(),
    recommendations: currentErrorRate > 10 ? 'Critical - Immediate action required' : 
                    currentErrorRate > 5 ? 'Warning - Enhanced monitoring needed' : 
                    'Healthy - Continue monitoring'
  };
  
  const dataStr = JSON.stringify(exportData, null, 2);
  const dataBlob = new Blob([dataStr], {type: 'application/json'});
  const url = URL.createObjectURL(dataBlob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `feature-error-analysis-${currentFeatureData.feature_slug || 'chat'}-${new Date().getTime()}.json`;
  link.click();
  URL.revokeObjectURL(url);
}

function refreshErrors() {
  location.reload();
}

function exportErrors() {
  window.location.href = '{{ route("admin.ai.debug.export", ["type" => "csv"]) }}?error_report=1';
}

// viewDetailedLogs function removed - now handled by dropdown links

function initializeErrorTrendChart() {
  // Fixed error trend data with safe parsing
  const errorTrendData = [
    @if(!empty($errorData['error_trends']) && is_array($errorData['error_trends']))
      @foreach($errorData['error_trends'] as $trend)
        @php
          $errorCount = 0;
          if (is_object($trend)) {
            $errorCount = $trend->error_count ?? $trend->errors ?? 0;
          } elseif (is_array($trend)) {
            $errorCount = $trend['error_count'] ?? $trend['errors'] ?? 0;
          } else {
            $errorCount = is_numeric($trend) ? intval($trend) : 0;
          }
        @endphp
        {{ intval($errorCount) }},
      @endforeach
    @else
      12, 19, 3, 5, 2, 8, 15, 22, 18, 25, 14, 16, 8, 12, 5, 9, 18, 22, 15, 19, 16, 12, 8, 6
    @endif
  ].filter(value => Number.isFinite(value));
  
  const options = {
    series: [{
      name: 'Hata Sayisi',
      data: errorTrendData
    }],
    chart: {
      type: 'area',
      fontFamily: 'inherit',
      height: 300,
      parentHeightOffset: 0,
      toolbar: { show: false },
      animations: { enabled: false }
    },
    colors: ['#dc3545'],
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.7,
        opacityTo: 0.1
      }
    },
    stroke: {
      curve: 'smooth',
      width: 2
    },
    xaxis: {
      categories: [
        @if(!empty($errorData['error_trends']) && is_array($errorData['error_trends']))
          @foreach($errorData['error_trends'] as $trend)
            @php
              $date = 'N/A';
              if (is_object($trend)) {
                $date = addslashes($trend->date ?? 'N/A');
              } elseif (is_array($trend)) {
                $date = addslashes($trend['date'] ?? 'N/A');
              }
            @endphp
            '{!! $date !!}',
          @endforeach
        @else
          '01.07', '02.07', '03.07', '04.07', '05.07', '06.07', '07.07', '08.07',
          '09.07', '10.07', '11.07', '12.07', '13.07', '14.07', '15.07', '16.07',
          '17.07', '18.07', '19.07', '20.07', '21.07', '22.07', '23.07', '24.07'
        @endif
      ],
      labels: { style: { fontSize: '12px' } }
    },
    yaxis: {
      labels: { 
        style: { fontSize: '12px' },
        formatter: function(val) {
          return Math.round(val);
        }
      }
    },
    grid: {
      borderColor: '#e9ecef',
      strokeDashArray: 3
    },
    tooltip: {
      theme: 'light',
      y: {
        formatter: function(val) {
          return val + ' hata';
        }
      }
    }
  };

  // Initialize the chart - Tabler Pattern
  const chartElement = document.querySelector("#errorTrendChart");
  if (chartElement && window.ApexCharts) {
    try {
      const chart = window.ApexCharts && new ApexCharts(chartElement, options);
      chart && chart.render();
    } catch (e) {
      console.error('Error trend chart error:', e);
    }
  }
}
</script>
@endsection