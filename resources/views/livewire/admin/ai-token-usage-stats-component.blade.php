<div>
<!-- İstatistik Kartları -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Kullanım İstatistiklerim</h3>
        <div class="card-subtitle">AI token kullanım raporlarınız</div>
    </div>
    <div class="card-body">
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Toplam Kullanım</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($usageStats['total_used_all_time']) }}</div>
                        <div class="text-muted">token</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Bu Ay Kullanım</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($usageStats['total_used_this_month']) }}</div>
                        <div class="text-muted">
                            @if($usageStats['monthly_limit'] > 0)
                                / {{ number_format($usageStats['monthly_limit']) }} token
                            @else
                                token (Sınırsız)
                            @endif
                        </div>
                        @if($usageStats['monthly_limit'] > 0)
                        <div class="progress progress-sm mt-1">
                            <div class="progress-bar" style="width: {{ min(100, ($usageStats['total_used_this_month'] / $usageStats['monthly_limit']) * 100) }}%"></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Günlük Ortalama</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($usageStats['average_daily_usage'] ?? 0, 1) }}</div>
                        <div class="text-muted">token/gün</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Mevcut Bakiye</div>
                        </div>
                        <div class="h1 mb-0 text-green">{{ number_format($usageStats['current_balance']) }}</div>
                        <div class="text-muted">token</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafikler ve Tablolar -->
<div class="row">
    <!-- Günlük Kullanım Grafiği -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Son 30 Gün Kullanım Grafiği</h3>
            </div>
            <div class="card-body">
                <div id="dailyUsageChart" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Kullanım Türleri -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kullanım Türleri</h3>
            </div>
            <div class="card-body">
                @forelse($usageByType as $usage)
                <div class="row align-items-center mb-3">
                    <div class="col">
                        <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $usage->usage_type)) }}</div>
                        <div class="text-muted small">{{ $usage->usage_count }} işlem</div>
                    </div>
                    <div class="col-auto">
                        <div class="fw-bold">{{ number_format($usage->total_tokens) }}</div>
                        <div class="text-muted small">token</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    Henüz kullanım verisi bulunmuyor.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Aylık Kullanım Tablosu -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Aylık Kullanım Geçmişi</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>Ay</th>
                        <th>Toplam Kullanım</th>
                        <th>İşlem Sayısı</th>
                        <th>Günlük Ortalama</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyUsage as $month)
                    <tr>
                        <td>
                            <div class="fw-bold">
                                {{ \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->locale('tr')->format('F Y') }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ number_format($month->total_tokens) }}</div>
                            <div class="text-muted small">token</div>
                        </td>
                        <td>
                            <span class="fw-bold">{{ number_format($month->usage_count) }}</span>
                        </td>
                        <td>
                            @php
                                $daysInMonth = \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->daysInMonth;
                                $avgDaily = $month->total_tokens / $daysInMonth;
                            @endphp
                            <span class="fw-bold">{{ number_format($avgDaily, 1) }}</span>
                            <div class="text-muted small">token/gün</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="text-muted">Henüz aylık kullanım verisi bulunmuyor.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Son Kullanım Bilgisi -->
@if($usageStats['last_usage'])
<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-blue text-white me-3">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="fw-bold">Son AI Kullanımı</div>
                <div class="text-muted">
                    {{ $usageStats['last_usage']->format('d.m.Y H:i') }} 
                    ({{ $usageStats['last_usage']->diffForHumans() }})
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Günlük kullanım grafiği
document.addEventListener('DOMContentLoaded', function() {
    const dailyData = @json($dailyUsage);
    
    const options = {
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            }
        },
        series: [{
            name: 'Token Kullanımı',
            data: dailyData.map(item => ({
                x: item.date,
                y: parseInt(item.total_tokens)
            }))
        }],
        xaxis: {
            type: 'datetime',
            labels: {
                format: 'dd/MM'
            }
        },
        yaxis: {
            title: {
                text: 'Token Miktarı'
            }
        },
        colors: ['#206bc4'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        },
        stroke: {
            curve: 'smooth'
        },
        tooltip: {
            x: {
                format: 'dd/MM/yyyy'
            },
            y: {
                formatter: function(value) {
                    return value + ' token';
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#dailyUsageChart"), options);
    chart.render();
});
</script>
</div>