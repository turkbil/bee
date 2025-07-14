{{-- AI Debug Dashboard - Performance Analytics --}}
@extends('admin.layout')

@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

@section('title')
📊 {{ __('ai::admin.performance_analytics') }}
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
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
          <i class="fas fa-filter me-2"></i>Filtreler
        </button>
        <div class="dropdown-menu dropdown-menu-end">
          <form class="p-3" style="min-width: 250px;">
            <div class="mb-3">
              <label class="form-label">Zaman Aralığı</label>
              <select name="date_range" class="form-select">
                <option value="7" {{ request('date_range', '7') == '7' ? 'selected' : '' }}>Son 7 Gün</option>
                <option value="30" {{ request('date_range') == '30' ? 'selected' : '' }}>Son 30 Gün</option>
                <option value="90" {{ request('date_range') == '90' ? 'selected' : '' }}>Son 90 Gün</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Tenant</label>
              <select name="tenant_id" class="form-select">
                <option value="">Tüm Tenantlar</option>
                @if(!empty($tenants))
                  @foreach($tenants as $tenant)
                    <option value="{{ $tenant->tenant_id }}" {{ request('tenant_id') == $tenant->tenant_id ? 'selected' : '' }}>
                      Tenant {{ $tenant->tenant_id }} ({{ $tenant->usage_count }} kullanım)
                    </option>
                  @endforeach
                @endif
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Uygula</button>
          </form>
        </div>
      </div>
      <button class="btn btn-outline-success" onclick="exportData('csv')">
        <i class="fas fa-download me-2"></i>CSV İndir
      </button>
    </div>
  </div>
</div>

{{-- Performans İstatistikleri --}}
<div class="row mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-success-lt me-3">
            <i class="fas fa-tachometer-alt icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="small text-uppercase fw-bold">Ortalama Yanıt Süresi</div>
            <div class="h2 mb-0 text-success">
              @if(!empty($performanceData['avg_execution_time']) && is_numeric($performanceData['avg_execution_time']))
                {{ round($performanceData['avg_execution_time'], 1) }}ms
              @else
                --ms
              @endif
            </div>
          </div>
        </div>
        <div class="progress progress-sm mt-3">
          @php
            $avgTime = $performanceData['avg_execution_time'] ?? 0;
            $progressWidth = $avgTime > 0 ? min(100, (3000 - min(3000, $avgTime)) / 3000 * 100) : 100;
          @endphp
          <div class="progress-bar bg-success" style="width: {{ $progressWidth }}%"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-blue-lt me-3">
            <i class="fas fa-brain icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="small text-uppercase fw-bold">Prompt Verimliliği</div>
            <div class="h2 mb-0 text-blue">
              @if(!empty($performanceData['prompt_efficiency']['efficiency_ratio']) && is_numeric($performanceData['prompt_efficiency']['efficiency_ratio']))
                {{ round($performanceData['prompt_efficiency']['efficiency_ratio'], 1) }}%
              @else
                --%
              @endif
            </div>
          </div>
        </div>
        <div class="progress progress-sm mt-3">
          @php
            $efficiency = $performanceData['prompt_efficiency']['efficiency_ratio'] ?? 0;
            $efficiencyWidth = is_numeric($efficiency) ? min(100, $efficiency) : 0;
          @endphp
          <div class="progress-bar bg-blue" style="width: {{ $efficiencyWidth }}%"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-warning-lt me-3">
            <i class="fas fa-exclamation-triangle icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="small text-uppercase fw-bold">Hata Oranı</div>
            <div class="h2 mb-0 text-warning">
              @if(!empty($performanceData['error_rates']['overall_error_rate']) && is_numeric($performanceData['error_rates']['overall_error_rate']))
                {{ round($performanceData['error_rates']['overall_error_rate'], 1) }}%
              @else
                --%
              @endif
            </div>
          </div>
        </div>
        <div class="progress progress-sm mt-3">
          @php
            $errorRate = $performanceData['error_rates']['overall_error_rate'] ?? 0;
            $errorWidth = is_numeric($errorRate) ? min(100, $errorRate) : 0;
          @endphp
          <div class="progress-bar bg-warning" style="width: {{ $errorWidth }}%"></div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-purple-lt me-3">
            <i class="fas fa-clock icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="small text-uppercase fw-bold">Yoğun Saat</div>
            <div class="h2 mb-0 text-purple">
              @if(!empty($performanceData['peak_usage_hours'][0]))
                {{ $performanceData['peak_usage_hours'][0]->hour }}:00
              @else
                --:--
              @endif
            </div>
          </div>
        </div>
        <div class="progress progress-sm mt-3">
          @php
            $peakHour = $performanceData['peak_usage_hours'][0] ?? null;
            $peakWidth = $peakHour ? min(100, ($peakHour->requests ?? 0) / 10) : 0; // Her 10 request = %10
          @endphp
          <div class="progress-bar bg-purple" style="width: {{ $peakWidth }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Detaylı Analiz Grafikleri --}}
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-line me-2"></i>
          Yanıt Süresi Trendi
        </h3>
      </div>
      <div class="card-body">
        <div id="responseTrendChart" style="height: 300px;"></div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-list-alt me-2"></i>
          Feature Performansı
        </h3>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-vcenter card-table table-hover">
            <thead>
              <tr>
                <th>Feature</th>
                <th class="text-end">Ort. Süre</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($performanceData['avg_execution_time']['by_feature']))
                @foreach($performanceData['avg_execution_time']['by_feature'] as $feature)
                <tr>
                  <td>
                    <span>{{ $feature->feature_slug ?: 'chat' }}</span>
                  </td>
                  <td class="text-end">
                    <span class="text-{{ $feature->avg_time > 3000 ? 'danger' : ($feature->avg_time > 1500 ? 'warning' : 'success') }}">
                      {{ round($feature->avg_time, 1) }}ms
                    </span>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="2" class="text-center py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Henüz veri bulunmuyor
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

{{-- Token Kullanım Trendi --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-coins me-2"></i>
          Token Kullanım Trendi
        </h3>
        <div class="card-actions">
          <div class="dropdown">
            <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fas fa-dots-vertical"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <a href="#" class="dropdown-item" onclick="exportTokenData()">
                <i class="fas fa-download me-2"></i>Token Verilerini İndir
              </a>
              <div class="dropdown-divider"></div>
              <a href="{{ route('admin.ai.tokens.usage-stats') }}" class="dropdown-item">
                <i class="fas fa-external-link-alt me-2"></i>Detaylı Token Analizi
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="tokenTrendChart" style="height: 250px;"></div>
      </div>
    </div>
  </div>
</div>

<script>
function exportData(type) {
  const params = new URLSearchParams(window.location.search);
  params.set('export', type);
  window.location.href = '{{ route("admin.ai.debug.export", ["type" => "csv"]) }}?' + params.toString();
}

function exportTokenData() {
  // Token export logic
  exportData('token-csv');
}

// Initialize Performance Charts - Tabler Pattern
document.addEventListener('DOMContentLoaded', function() {
  initializePerformanceCharts();
});

function initializePerformanceCharts() {
  // Response Time Trend Chart
  const responseTrendElement = document.querySelector("#responseTrendChart");
  if (responseTrendElement && window.ApexCharts) {
    try {
      const responseTrendData = [
        @if(!empty($performanceData['response_time_trend']) && is_array($performanceData['response_time_trend']))
          @foreach($performanceData['response_time_trend'] as $trend)
            {{ is_numeric($trend['avg_time'] ?? 0) ? round($trend['avg_time'], 1) : 0 }},
          @endforeach
        @else
          890, 1240, 980, 1560, 1100, 950, 1200, 1350, 980, 850, 1100, 980, 1450, 1200
        @endif
      ];

      const responseTrendChart = window.ApexCharts && new ApexCharts(responseTrendElement, {
        series: [{
          name: 'Yanıt Süresi (ms)',
          data: responseTrendData
        }],
        chart: {
          type: 'line',
          fontFamily: 'inherit',
          height: 300,
          parentHeightOffset: 0,
          toolbar: { show: false },
          animations: { enabled: false }
        },
        colors: ['#206bc4'],
        stroke: {
          curve: 'smooth',
          width: 2
        },
        xaxis: {
          categories: [
            @if(!empty($performanceData['response_time_trend']) && is_array($performanceData['response_time_trend']))
              @foreach($performanceData['response_time_trend'] as $trend)
                '{!! addslashes($trend['date'] ?? date('M d')) !!}',
              @endforeach
            @else
              'Jul 01', 'Jul 02', 'Jul 03', 'Jul 04', 'Jul 05', 'Jul 06', 'Jul 07',
              'Jul 08', 'Jul 09', 'Jul 10', 'Jul 11', 'Jul 12', 'Jul 13', 'Jul 14'
            @endif
          ],
          labels: { style: { fontSize: '12px' } }
        },
        yaxis: {
          labels: {
            style: { fontSize: '12px' },
            formatter: function(val) {
              return Math.round(val) + 'ms';
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
              return val + ' ms';
            }
          }
        }
      });
      responseTrendChart && responseTrendChart.render();
    } catch (e) {
      console.error('Response trend chart error:', e);
    }
  }

  // Token Trend Chart
  const tokenTrendElement = document.querySelector("#tokenTrendChart");
  if (tokenTrendElement && window.ApexCharts) {
    try {
      const tokenTrendData = [
        @if(!empty($performanceData['token_usage_trend']) && is_array($performanceData['token_usage_trend']))
          @foreach($performanceData['token_usage_trend'] as $trend)
            {{ is_numeric($trend['tokens_used'] ?? 0) ? intval($trend['tokens_used']) : 0 }},
          @endforeach
        @else
          45, 78, 62, 95, 58, 71, 89, 103, 67, 82, 94, 76, 88, 105
        @endif
      ];

      const tokenTrendChart = window.ApexCharts && new ApexCharts(tokenTrendElement, {
        series: [{
          name: 'Token Kullanımı',
          data: tokenTrendData
        }],
        chart: {
          type: 'area',
          fontFamily: 'inherit',
          height: 250,
          parentHeightOffset: 0,
          toolbar: { show: false },
          animations: { enabled: false }
        },
        colors: ['#fab005'],
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
            @if(!empty($performanceData['token_usage_trend']) && is_array($performanceData['token_usage_trend']))
              @foreach($performanceData['token_usage_trend'] as $trend)
                '{!! addslashes($trend['date'] ?? date('M d')) !!}',
              @endforeach
            @else
              'Jul 01', 'Jul 02', 'Jul 03', 'Jul 04', 'Jul 05', 'Jul 06', 'Jul 07',
              'Jul 08', 'Jul 09', 'Jul 10', 'Jul 11', 'Jul 12', 'Jul 13', 'Jul 14'
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
              return val + ' token';
            }
          }
        }
      });
      tokenTrendChart && tokenTrendChart.render();
    } catch (e) {
      console.error('Token trend chart error:', e);
    }
  }
}
</script>

{{-- Performance Detail Modal --}}
<div class="modal fade" id="performanceDetailModal" tabindex="-1" aria-labelledby="performanceDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="performanceDetailModalLabel">
          <i class="fas fa-tachometer-alt me-2"></i>Performance Detayları
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="performanceDetailModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2 text-muted">Performance detayları yükleniyor...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Kapat
        </button>
        <button type="button" class="btn btn-primary" onclick="exportPerformanceData()">
          <i class="fas fa-download me-2"></i>Veri İndir
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let currentPerformanceData = null;

function openPerformanceDetailModal(data) {
  currentPerformanceData = data;
  $('#performanceDetailModal').modal('show');
  $('#performanceDetailModalBody').html(generatePerformanceDetailHTML(data));
}

function generatePerformanceDetailHTML(data) {
  return `
    <div class="row">
      <div class="col-md-6">
        <div class="card border-primary">
          <div class="card-header bg-primary-lt">
            <h6 class="mb-0 text-primary"><i class="fas fa-info-circle me-2"></i>Performance Özeti</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm mb-0">
              <tr><td class="fw-bold">Feature:</td><td class="text-primary">${data.feature_slug || 'Chat'}</td></tr>
              <tr><td class="fw-bold">Avg Time:</td><td class="text-${data.avg_time > 3000 ? 'danger' : (data.avg_time > 1500 ? 'warning' : 'success')}">${data.avg_time}ms</td></tr>
              <tr><td class="fw-bold">Usage Count:</td><td class="text-info">${data.usage_count}</td></tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-info">
          <div class="card-header bg-info-lt">
            <h6 class="mb-0 text-info"><i class="fas fa-chart-line me-2"></i>Performance Analysis</h6>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="h2 mb-0 text-${data.avg_time > 3000 ? 'danger' : (data.avg_time > 1500 ? 'warning' : 'success')}">${data.avg_time}ms</div>
              <div class="small text-muted">Average Response Time</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function exportPerformanceData() {
  if (!currentPerformanceData) {
    alert('İhraç edilecek veri bulunamadı!');
    return;
  }
  
  const dataStr = JSON.stringify(currentPerformanceData, null, 2);
  const dataBlob = new Blob([dataStr], {type: 'application/json'});
  const url = URL.createObjectURL(dataBlob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `performance-data-${new Date().getTime()}.json`;
  link.click();
  URL.revokeObjectURL(url);
}
</script>
@endsection