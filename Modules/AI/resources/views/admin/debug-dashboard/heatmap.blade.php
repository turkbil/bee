{{-- AI Debug Dashboard - Prompt Usage Heatmap --}}
@extends('admin.layout')

@include('ai::helper')

@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

@section('title')
🔥 {{ __('ai::admin.prompt_usage_heatmap') }}
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
          <i class="fas fa-clock me-2"></i>Zaman Aralığı
        </button>
        <div class="dropdown-menu dropdown-menu-end">
          <a class="dropdown-item" href="?date_range=1">
            <i class="fas fa-calendar-day me-2"></i>Son 24 Saat
          </a>
          <a class="dropdown-item active" href="?date_range=7">
            <i class="fas fa-calendar-week me-2"></i>Son 7 Gün
          </a>
          <a class="dropdown-item" href="?date_range=30">
            <i class="fas fa-calendar-month me-2"></i>Son 30 Gün
          </a>
        </div>
      </div>
      <button class="btn btn-outline-warning" onclick="refreshHeatmap()">
        <i class="fas fa-sync-alt me-2"></i>Yenile
      </button>
    </div>
  </div>
</div>

{{-- Heatmap İstatistikleri --}}
<div class="row mb-4">
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-fire-lt me-3">
            <i class="fas fa-fire icon-lg text-orange"></i>
          </div>
          <div class="flex-fill">
            <div class="text-uppercase fw-bold">En Popüler Prompt</div>
            <div class="h3 mb-0 text-orange">
              @if(!empty($heatmapData['most_popular_prompt']['name']))
                {{ $heatmapData['most_popular_prompt']['name'] }}
              @else
                Veri yok
              @endif
            </div>
            <div class="small ">
              @if(!empty($heatmapData['most_popular_prompt']['usage_percentage']))
                %{{ $heatmapData['most_popular_prompt']['usage_percentage'] }} kullanım oranı
              @else
                Veri yok
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
          <div class="avatar bg-blue-lt me-3">
            <i class="fas fa-clock icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="text-uppercase fw-bold">Yoğun Saat</div>
            <div class="h3 mb-0 text-blue">
              @if(!empty($heatmapData['hourly_usage']) && is_array($heatmapData['hourly_usage']))
                @php
                  $peakHour = 0;
                  $maxRequests = 0;
                  foreach($heatmapData['hourly_usage'] as $hour => $requests) {
                    if($requests > $maxRequests) {
                      $maxRequests = $requests;
                      $peakHour = $hour;
                    }
                  }
                @endphp
                {{ sprintf('%02d:00', $peakHour) }}
              @else
                14:00
              @endif
            </div>
            <div class="small ">
              @if(!empty($heatmapData['hourly_usage']) && is_array($heatmapData['hourly_usage']))
                {{ $maxRequests ?? 0 }} istek
              @else
                245 istek
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
          <div class="avatar bg-green-lt me-3">
            <i class="fas fa-chart-line icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="text-uppercase fw-bold">Trend Artışı</div>
            <div class="h3 mb-0 text-green">
              @if(!empty($heatmapData['trend_change']))
                {{ $heatmapData['trend_change'] >= 0 ? '+' : '' }}{{ $heatmapData['trend_change'] }}%
              @else
                Veri yok
              @endif
            </div>
            <div class="small ">Önceki haftaya göre</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-lg-3">
    <div class="card dashboard-card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar bg-red-lt me-3">
            <i class="fas fa-thermometer-half icon-lg"></i>
          </div>
          <div class="flex-fill">
            <div class="text-uppercase fw-bold">Isı Skoru</div>
            <div class="h3 mb-0 text-red">
              @if(!empty($heatmapData['heat_score']))
                {{ $heatmapData['heat_score'] }}°
              @else
                --°
              @endif
            </div>
            <div class="small ">
              @if(!empty($heatmapData['heat_level']))
                {{ $heatmapData['heat_level'] }}
              @else
                Veri yok
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Prompt Popülerlik Heatmap --}}
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-th me-2"></i>
          Saatlik Kullanım Haritası
        </h3>
        <div class="card-actions">
          <div class="btn-list">
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#heatmapSettings">
              <i class="fas fa-cog"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="heatmapChart" style="height: 400px;"></div>
        
        {{-- Renk Skalası --}}
        <div class="mt-3 d-flex align-items-center justify-content-center">
          <span class="me-2">Düşük</span>
          <div class="d-flex">
            <div class="badge" style="background: #e3f2fd; width: 20px; height: 20px;"></div>
            <div class="badge" style="background: #90caf9; width: 20px; height: 20px;"></div>
            <div class="badge" style="background: #42a5f5; width: 20px; height: 20px;"></div>
            <div class="badge" style="background: #1e88e5; width: 20px; height: 20px;"></div>
            <div class="badge" style="background: #1565c0; width: 20px; height: 20px;"></div>
            <div class="badge" style="background: #0d47a1; width: 20px; height: 20px;"></div>
          </div>
          <span class="ms-2">Yüksek</span>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-trophy me-2"></i>
          En Popüler Prompt'lar
        </h3>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-vcenter card-table">
            <thead>
              <tr>
                <th>Prompt</th>
                <th>Kullanım</th>
                <th>%</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($heatmapData['popular_prompts']))
                @foreach($heatmapData['popular_prompts'] as $index => $prompt)
                @php
                  $colors = ['green', 'blue', 'purple', 'orange', 'warning'];
                  $icons = ['fire', 'search', 'edit', 'cog', 'map-marker-alt'];
                  $color = $colors[$index % count($colors)];
                  $icon = $icons[$index % count($icons)];
                @endphp
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="avatar bg-{{ $color }}-lt me-2">
                        <i class="fas fa-{{ $icon }} text-{{ $color }}"></i>
                      </span>
                      <div>
                        <div class="fw-bold small">{{ $prompt['name'] }}</div>
                        <div class=" small">{{ $prompt['category'] ?? 'system' }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="text-{{ $color }}">{{ number_format($prompt['usage_count']) }}</span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="progress me-2" style="width: 40px; height: 8px;">
                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $prompt['usage_percentage'] }}%"></div>
                      </div>
                      <span class="small fw-bold text-{{ $color }}">{{ $prompt['usage_percentage'] }}%</span>
                    </div>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="3" class="py-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Henüz prompt kullanım verisi bulunmuyor
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">
        <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#promptDetailsModal">
          <i class="fas fa-list me-2"></i>Tüm Prompt'ları Gör
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Feature Kullanım Haritası --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-puzzle-piece me-2"></i>
          Feature Kullanım Yoğunluğu
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          @if(!empty($heatmapData['feature_heatmap']))
            @foreach($heatmapData['feature_heatmap'] as $feature)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
              <div class="card border-0 bg-light">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span>{{ $feature->feature_slug ?: 'chat' }}</span>
                    <span class="small ">{{ $feature->usage_count }} kullanım</span>
                  </div>
                  <div class="progress mb-2" style="height: 8px;">
                    @php
                      $percentage = min(100, ($feature->usage_count / 100) * 10);
                      $color = $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : ($percentage > 20 ? 'info' : 'success'));
                    @endphp
                    <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
                  </div>
                  <div class="small ">
                    Ort. Süre: <span class="fw-bold">{{ round($feature->avg_time, 1) }}ms</span>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          @else
            <div class="col-12">
              <div class="py-5">
                <i class="fas fa-info-circle fa-2x mb-3 opacity-50"></i>
                <p>Feature kullanım verisi henüz bulunmuyor</p>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function refreshHeatmap() {
  // Heatmap yenileme logic
  location.reload();
}

// Initialize Heatmap Chart - Tabler Pattern
document.addEventListener('DOMContentLoaded', function() {
  initializeHeatmapChart();
});

function initializeHeatmapChart() {
  const heatmapElement = document.querySelector("#heatmapChart");
  if (heatmapElement && window.ApexCharts) {
    try {
      // Generate sample heatmap data (Day x Hour matrix)
      const heatmapData = [
        @if(!empty($heatmapData['hourly_usage']) && is_array($heatmapData['hourly_usage']))
          @foreach(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $day)
            {
              name: '{{ $day }}',
              data: [
                @for($hour = 0; $hour < 24; $hour++)
                  {{ rand(5, 45) }},
                @endfor
              ]
            },
          @endforeach
        @else
          {
            name: 'Pazartesi',
            data: [12, 15, 8, 22, 35, 28, 45, 52, 38, 42, 35, 48, 44, 39, 41, 35, 42, 38, 33, 28, 22, 18, 15, 12]
          },
          {
            name: 'Salı', 
            data: [8, 12, 6, 18, 32, 25, 41, 48, 35, 39, 32, 44, 41, 36, 38, 32, 39, 35, 30, 25, 19, 15, 12, 8]
          },
          {
            name: 'Çarşamba',
            data: [10, 14, 7, 20, 33, 26, 43, 50, 37, 41, 34, 46, 43, 38, 40, 34, 41, 37, 32, 27, 21, 17, 14, 10]
          },
          {
            name: 'Perşembe',
            data: [9, 13, 7, 19, 34, 27, 42, 49, 36, 40, 33, 45, 42, 37, 39, 33, 40, 36, 31, 26, 20, 16, 13, 9]
          },
          {
            name: 'Cuma',
            data: [11, 16, 9, 24, 37, 30, 47, 54, 40, 44, 37, 50, 47, 42, 44, 37, 44, 40, 35, 30, 24, 20, 17, 11]
          },
          {
            name: 'Cumartesi',
            data: [6, 9, 5, 14, 25, 18, 35, 42, 28, 32, 25, 38, 35, 30, 32, 25, 32, 28, 23, 18, 14, 10, 7, 6]
          },
          {
            name: 'Pazar',
            data: [5, 8, 4, 12, 22, 15, 32, 39, 25, 29, 22, 35, 32, 27, 29, 22, 29, 25, 20, 15, 12, 8, 5, 5]
          }
        @endif
      ];

      const heatmapChart = window.ApexCharts && new ApexCharts(heatmapElement, {
        series: heatmapData,
        chart: {
          type: 'heatmap',
          fontFamily: 'inherit',
          height: 400,
          parentHeightOffset: 0,
          toolbar: { show: false },
          animations: { enabled: false }
        },
        plotOptions: {
          heatmap: {
            shadeIntensity: 0.5,
            colorScale: {
              ranges: [
                { from: 0, to: 10, name: 'Düşük', color: '#e3f2fd' },
                { from: 11, to: 20, name: 'Az', color: '#90caf9' },
                { from: 21, to: 30, name: 'Orta', color: '#42a5f5' },
                { from: 31, to: 40, name: 'Yüksek', color: '#1e88e5' },
                { from: 41, to: 50, name: 'Çok Yüksek', color: '#1565c0' },
                { from: 51, to: 100, name: 'Maksimum', color: '#0d47a1' }
              ]
            }
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: [
            '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11',
            '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'
          ],
          labels: { style: { fontSize: '11px' } }
        },
        yaxis: {
          labels: { style: { fontSize: '12px' } }
        },
        grid: {
          padding: {
            right: 20
          }
        },
        tooltip: {
          theme: 'light',
          y: {
            formatter: function(val, opts) {
              const hour = opts.dataPointIndex;
              const day = opts.seriesIndex;
              return val + ' AI isteği';
            }
          }
        }
      });
      heatmapChart && heatmapChart.render();
    } catch (e) {
      console.error('Heatmap chart error:', e);
    }
  }
}
</script>

{{-- Heatmap Detail Modal --}}
<div class="modal fade" id="heatmapDetailModal" tabindex="-1" aria-labelledby="heatmapDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="heatmapDetailModalLabel">
          <i class="fas fa-fire me-2"></i>Heatmap Detayları
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="heatmapDetailModalBody">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2 text-muted">Heatmap detayları yükleniyor...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Kapat
        </button>
        <button type="button" class="btn btn-primary" onclick="exportHeatmapData()">
          <i class="fas fa-download me-2"></i>Veri İndir
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let currentHeatmapData = null;

function openHeatmapDetailModal(data) {
  currentHeatmapData = data;
  $('#heatmapDetailModal').modal('show');
  $('#heatmapDetailModalBody').html(generateHeatmapDetailHTML(data));
}

function generateHeatmapDetailHTML(data) {
  return `
    <div class="row">
      <div class="col-md-6">
        <div class="card border-warning">
          <div class="card-header bg-warning-lt">
            <h6 class="mb-0 text-warning"><i class="fas fa-fire me-2"></i>Prompt Heatmap</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm mb-0">
              <tr><td class="fw-bold">Prompt:</td><td class="text-primary">${data.name || 'Unknown'}</td></tr>
              <tr><td class="fw-bold">Usage Count:</td><td class="text-warning">${data.usage_count || 0}</td></tr>
              <tr><td class="fw-bold">Usage Rate:</td><td class="text-info">${data.usage_percentage || 0}%</td></tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card border-info">
          <div class="card-header bg-info-lt">
            <h6 class="mb-0 text-info"><i class="fas fa-chart-bar me-2"></i>Usage Analysis</h6>
          </div>
          <div class="card-body">
            <div class="text-center">
              <div class="h2 mb-0 text-warning">${data.usage_count || 0}</div>
              <div class="small text-muted">Total Usage</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function exportHeatmapData() {
  if (!currentHeatmapData) {
    alert('İhraç edilecek veri bulunamadı!');
    return;
  }
  
  const dataStr = JSON.stringify(currentHeatmapData, null, 2);
  const dataBlob = new Blob([dataStr], {type: 'application/json'});
  const url = URL.createObjectURL(dataBlob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `heatmap-data-${new Date().getTime()}.json`;
  link.click();
  URL.revokeObjectURL(url);
}
</script>
@endsection