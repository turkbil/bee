<div>

  <!-- Dashboard Header -->
  <div class="row mb-4">
    <div class="col">
      <div class="d-flex">
        <div>
          <div class="">
            <span>Son güncelleme: {{ now()->format('H:i:s') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Token Status Cards -->
  <div class="row mb-4">
    <!-- Remaining Tokens -->
    <div class="col-md-3">
      <div class="card dashboard-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar bg-{{ $this->getBalanceColor() }}-lt me-3">
              <i class="fas fa-coins icon-lg"></i>
            </div>
            <div class="flex-fill">
              <div class="small text-uppercase fw-bold">Kalan Token</div>
              <div class="h2 mb-0 text-{{ $this->getBalanceColor() }}">
                {{ \App\Helpers\TokenHelper::remainingFormatted() }}
              </div>
            </div>
          </div>
          <div class="progress progress-sm mt-3">
            <div class="progress-bar bg-{{ $this->getBalanceColor() }}"
              style="width: {{ $this->getBalancePercentage() }}%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Daily Usage -->
    <div class="col-md-3">
      <div class="card dashboard-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar bg-info-lt me-3">
              <i class="fas fa-calendar-day icon-lg"></i>
            </div>
            <div class="flex-fill">
              <div class="small text-uppercase fw-bold">Bugün Kullanılan</div>
              <div class="h2 mb-0 text-info">{{ \App\Helpers\TokenHelper::todayUsageFormatted() }}</div>
            </div>
          </div>
          <div class="progress progress-sm mt-3">
            <div class="progress-bar bg-info" style="width: {{ $this->getDailyProgressPercentage() }}%">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly Usage -->
    <div class="col-md-3">
      <div class="card dashboard-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar bg-warning-lt me-3">
              <i class="fas fa-calendar-alt icon-lg"></i>
            </div>
            <div class="flex-fill">
              <div class="small text-uppercase fw-bold">Bu Ay Kullanılan</div>
              <div class="h2 mb-0 text-warning">{{ \App\Helpers\TokenHelper::monthlyUsageFormatted() }}</div>
            </div>
          </div>
          <div class="progress progress-sm mt-3">
            <div class="progress-bar bg-warning" style="width: {{ $this->getUsagePercentage() }}%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Daily vs Monthly -->
    <div class="col-md-3">
      <div class="card dashboard-card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar bg-purple-lt me-3">
              <i class="fas fa-chart-bar icon-lg"></i>
            </div>
            <div class="flex-fill">
              <div class="small text-uppercase fw-bold">Günlük Ortalama</div>
              <div class="h2 mb-0 text-purple">{{ \App\Helpers\TokenHelper::dailyAverageFormatted() }}</div>
            </div>
          </div>
          <div class="progress progress-sm mt-3">
            <div class="progress-bar bg-purple" style="width: {{ $this->getAverageProgressPercentage() }}%">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row mb-4">
    <!-- Weekly Trend Chart -->
    <div class="col-md-8">
      <div class="card dashboard-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-chart-line me-2"></i>
            7 Günlük Token Kullanım Trendi
          </h3>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <div id="weeklyTrendChart"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Usage by Type -->
    <div class="col-md-4">
      <div class="card dashboard-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-chart-pie me-2"></i>
            Türe Göre Kullanım
          </h3>
        </div>
        <div class="card-body">
          @if(!empty($usageByType))
          <div class="chart-container">
            <div id="usageTypeChart"></div>
          </div>
          @else
          <div class="text-center py-4">
            <i class="fas fa-chart-pie" style="font-size: 48px;"></i>
            <div class="mt-2">Henüz kullanım verisi yok</div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Last Activities -->
  <div class="row">
    <div class="col-md-8">
      <div class="card dashboard-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-history me-2"></i>
            Son Aktiviteler
          </h3>
          <div class="card-actions">
            <a href="{{ route('admin.ai.credits.usage-stats') }}" class="btn btn-outline-primary btn-sm">
              Tümünü Gör
            </a>
          </div>
        </div>
        <div class="card-body">
          @if(!empty($lastActivities))
          @foreach($lastActivities as $index => $activity)
          <div class="activity-item {{ $index === 0 ? 'recent' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-medium">{{ $activity['description'] }}</div>
                <div class="small ">
                  {{ $activity['type'] }} • {{ $activity['user'] }} •
                  {{ $activity['tokens'] }} token
                </div>
              </div>
              <div class="text-end">
                <div class="small ">{{ $activity['time'] }}</div>
                <div class="small ">{{ $activity['time_exact'] }}</div>
              </div>
            </div>
          </div>
          @endforeach
          @else
          <div class="text-center py-4">
            <i class="fas fa-history" style="font-size: 48px;"></i>
            <div class="mt-2">Henüz aktivite kaydı yok</div>
          </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
      <div class="card dashboard-card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-bolt me-2"></i>
            Hızlı İşlemler
          </h3>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            <a href="{{ route('admin.ai.index') }}" class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="fas fa-comments text-primary me-2"></i>
                <div>
                  <div class="fw-medium">AI Asistan</div>
                  <div class="small ">Chat panelini aç</div>
                </div>
              </div>
            </a>
            <a href="{{ route('admin.ai.credits.packages') }}"
              class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="fas fa-box text-success me-2"></i>
                <div>
                  <div class="fw-medium">Token Paketleri</div>
                  <div class="small ">Paket yönetimi</div>
                </div>
              </div>
            </a>
            <a href="{{ route('admin.ai.credits.usage-stats') }}"
              class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="fas fa-chart-area text-warning me-2"></i>
                <div>
                  <div class="fw-medium">Kullanım İstatistikleri</div>
                  <div class="small ">Detaylı analiz ve raporlar</div>
                </div>
              </div>
            </a>
            <a href="{{ route('admin.ai.conversations.index') }}"
              class="list-group-item list-group-item-action">
              <div class="d-flex align-items-center">
                <i class="fas fa-clipboard-list text-info me-2"></i>
                <div>
                  <div class="fw-medium">Konuşmalar</div>
                  <div class="small ">Geçmiş görüntüle</div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <!-- Performance Metrics -->
      <div class="card dashboard-card mt-3">
        <div class="card-header">
          <h4 class="card-title">Performans</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <div class="text-center">
                <div class="h3 mb-0 text-primary">{{ round($this->getUsagePercentage(), 1) }}%</div>
                <div class="small ">Aylık Kullanım</div>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center">
                <div class="h3 mb-0 text-success">{{ round($this->getBalancePercentage(), 1) }}%</div>
                <div class="small ">Kalan Bakiye</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    // Initialize Charts using Tabler Standard Pattern
    document.addEventListener('DOMContentLoaded', function() {
      initializeCharts();
    });

    // Re-render on Livewire updates
    document.addEventListener('livewire:updated', function() {
      setTimeout(() => {
        initializeCharts();
      }, 200);
    });

    function initializeCharts() {
      console.log('Initializing charts...');
      
      // Destroy existing charts
      if (window.weeklyChart) {
        window.weeklyChart.destroy();
        window.weeklyChart = null;
      }
      if (window.typeChart) {
        window.typeChart.destroy();
        window.typeChart = null;
      }

      // Weekly Trend Chart - Fixed Data Structure
      const weeklyElement = document.getElementById('weeklyTrendChart');
      if (weeklyElement && window.ApexCharts) {
        try {
          const weeklyData = {!! json_encode($weeklyTrend ?? []) !!};
          const weeklyValues = Array.isArray(weeklyData) ? weeklyData.map(item => parseInt(item.value) || 0) : [0];
          const weeklyLabels = Array.isArray(weeklyData) ? weeklyData.map(item => String(item.date) || '') : [''];
          
          window.weeklyChart = window.ApexCharts && new ApexCharts(weeklyElement, {
            chart: {
              type: 'line',
              fontFamily: 'inherit',
              height: 200,
              parentHeightOffset: 0,
              toolbar: { show: false },
              animations: { enabled: false }
            },
            series: [{
              name: 'Token Kullanimi',
              data: weeklyValues
            }],
            stroke: {
              width: 2,
              curve: 'smooth'
            },
            xaxis: {
              categories: weeklyLabels,
              labels: {
                style: { fontSize: '12px' }
              }
            },
            yaxis: {
              labels: {
                style: { fontSize: '12px' },
                formatter: function(value) {
                  return Math.round(value).toLocaleString();
                }
              }
            },
            colors: ['#206bc4'],
            grid: {
              strokeDashArray: 4
            },
            tooltip: {
              theme: 'light'
            }
          });
          window.weeklyChart && window.weeklyChart.render();
        } catch (e) {
          console.error('Weekly chart error:', e);
        }
      }

      // Usage Type Chart - Fixed Data Structure 
      const typeElement = document.getElementById('usageTypeChart');
      if (typeElement && window.ApexCharts) {
        try {
          const typeData = {!! json_encode($usageByType ?? []) !!};
          
          if (Array.isArray(typeData) && typeData.length > 0) {
            const typeValues = typeData.map(item => parseInt(item.value) || 0);
            const typeLabels = typeData.map(item => String(item.type) || 'Unknown');
            
            window.typeChart = window.ApexCharts && new ApexCharts(typeElement, {
              chart: {
                type: 'donut',
                fontFamily: 'inherit',
                height: 180,
                parentHeightOffset: 0,
                toolbar: { show: false },
                animations: { enabled: false }
              },
              series: typeValues,
              labels: typeLabels,
              colors: ['#206bc4', '#79a6dc', '#a8cc8c', '#fab005', '#fd7e14'],
              plotOptions: {
                pie: {
                  donut: {
                    size: '65%'
                  }
                }
              },
              legend: {
                position: 'bottom',
                fontSize: '12px'
              },
              tooltip: {
                theme: 'light'
              }
            });
            window.typeChart && window.typeChart.render();
          }
        } catch (e) {
          console.error('Type chart error:', e);
        }
      }
    }
  </script>
  @endpush
</div>