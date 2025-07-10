<div>

    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex">
                <div>
                    <div class="text-muted">
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
                            <div class="small text-muted text-uppercase fw-bold">Kalan Token</div>
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
                            <div class="small text-muted text-uppercase fw-bold">Bugün Kullanılan</div>
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
                            <div class="small text-muted text-uppercase fw-bold">Bu Ay Kullanılan</div>
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
                            <div class="small text-muted text-uppercase fw-bold">Günlük Ortalama</div>
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
                    <div class="text-center text-muted py-4">
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
                        <a href="{{ route('admin.ai.tokens.usage-stats') }}" class="btn btn-outline-primary btn-sm">
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
                                <div class="small text-muted">
                                    {{ $activity['type'] }} • {{ $activity['user'] }} •
                                    {{ $activity['tokens'] }} token
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="small text-muted">{{ $activity['time'] }}</div>
                                <div class="small text-muted">{{ $activity['time_exact'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center text-muted py-4">
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
                                    <div class="small text-muted">Chat panelini aç</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('admin.ai.tokens.packages') }}"
                            class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-box text-success me-2"></i>
                                <div>
                                    <div class="fw-medium">Token Paketleri</div>
                                    <div class="small text-muted">Paket yönetimi</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('admin.ai.tokens.usage-stats') }}"
                            class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-chart-area text-warning me-2"></i>
                                <div>
                                    <div class="fw-medium">Kullanım İstatistikleri</div>
                                    <div class="small text-muted">Detaylı analiz ve raporlar</div>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('admin.ai.conversations.index') }}"
                            class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clipboard-list text-info me-2"></i>
                                <div>
                                    <div class="fw-medium">Konuşmalar</div>
                                    <div class="small text-muted">Geçmiş görüntüle</div>
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
                                <div class="small text-muted">Aylık Kullanım</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h3 mb-0 text-success">{{ round($this->getBalancePercentage(), 1) }}%</div>
                                <div class="small text-muted">Kalan Bakiye</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ApexCharts rendering function
        function renderCharts() {
            // Weekly Trend Chart (ApexCharts)
            const weeklyElement = document.getElementById('weeklyTrendChart');
            if (weeklyElement) {
                // Destroy existing chart if exists
                if (window.weeklyChart) {
                    window.weeklyChart.destroy();
                }
                
                const weeklyData = @json($weeklyTrend ?? []);
                
                if (window.ApexCharts) {
                    window.weeklyChart = new ApexCharts(weeklyElement, {
                        chart: {
                            type: 'line',
                            fontFamily: 'inherit',
                            height: 200,
                            parentHeightOffset: 0,
                            toolbar: { show: false },
                            animations: { enabled: false }
                        },
                        stroke: {
                            width: 2,
                            lineCap: 'round',
                            curve: 'smooth'
                        },
                        series: [{
                            name: 'Token Kullanımı',
                            data: weeklyData.map(item => item.value)
                        }],
                        xaxis: {
                            categories: weeklyData.map(item => item.date),
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    colors: '#6c757d'
                                }
                            },
                            axisBorder: { show: false },
                            axisTicks: { show: false }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    colors: '#6c757d'
                                },
                                formatter: function(value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        colors: ['var(--tblr-primary)'],
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'light',
                                type: 'vertical',
                                shadeIntensity: 0.3,
                                gradientToColors: ['var(--tblr-primary)'],
                                inverseColors: false,
                                opacityFrom: 0.4,
                                opacityTo: 0.1
                            }
                        },
                        grid: {
                            strokeDashArray: 4,
                            padding: { top: -20, right: 0, left: -4, bottom: -4 }
                        },
                        tooltip: {
                            theme: 'dark',
                            fillSeriesColor: false,
                            y: {
                                formatter: function(value) {
                                    return value.toLocaleString() + ' token';
                                }
                            }
                        }
                    });
                    window.weeklyChart.render();
                }
            }

            // Usage Type Chart (ApexCharts Donut)
            const typeElement = document.getElementById('usageTypeChart');
            if (typeElement) {
                // Destroy existing chart if exists
                if (window.typeChart) {
                    window.typeChart.destroy();
                }
                
                const typeData = @json($usageByType ?? []);
                console.log('Type Data:', typeData); // Debug
                
                if (typeData && typeData.length > 0 && window.ApexCharts) {
                    window.typeChart = new ApexCharts(typeElement, {
                        chart: {
                            type: 'donut',
                            fontFamily: 'inherit',
                            height: 180,
                            animations: { enabled: false },
                            toolbar: { show: false }
                        },
                        series: typeData.map(item => parseInt(item.value)),
                        labels: typeData.map(item => item.type),
                        colors: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1', '#dc3545'],
                        legend: {
                            show: true,
                            position: 'bottom',
                            fontFamily: 'inherit',
                            fontSize: '11px',
                            fontWeight: 400,
                            labels: {
                                colors: '#6c757d'
                            },
                            markers: {
                                width: 8,
                                height: 8,
                                strokeColor: '#fff',
                                strokeWidth: 1,
                                radius: 2
                            },
                            itemMargin: {
                                horizontal: 8,
                                vertical: 4
                            }
                        },
                        tooltip: {
                            theme: 'light',
                            fillSeriesColor: false,
                            style: {
                                fontSize: '12px',
                                fontFamily: 'inherit'
                            },
                            y: {
                                formatter: function(value) {
                                    return value.toLocaleString('tr-TR') + ' token';
                                }
                            }
                        },
                        stroke: {
                            width: 0
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%',
                                    labels: {
                                        show: false
                                    }
                                },
                                expandOnClick: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    height: 150
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    });
                    window.typeChart.render();
                } else {
                    console.log('No type data available or ApexCharts not loaded');
                }
            }
        }

        // Initial render
        document.addEventListener('DOMContentLoaded', function() {
            // ApexCharts yüklendikten sonra çalıştır
            if (window.ApexCharts) {
                renderCharts();
            } else {
                // ApexCharts yüklenene kadar bekle
                const checkApexCharts = setInterval(() => {
                    if (window.ApexCharts) {
                        clearInterval(checkApexCharts);
                        renderCharts();
                    }
                }, 100);
            }
        });

        // Re-render on Livewire updates
        document.addEventListener('livewire:updated', function() {
            setTimeout(() => {
                if (window.ApexCharts) {
                    renderCharts();
                }
            }, 200);
        });
    </script>
    @endpush
</div>