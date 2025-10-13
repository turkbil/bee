@extends('admin.layout')

@include('ai::helper')

@section('page-title', 'Konuşma Analitiği')
@section('page-subtitle', 'Cihaz, tarayıcı, işletim sistemi ve kullanım istatistikleri')

@section('content')
    <!-- Zaman Aralığı Seçici -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.ai.analytics.conversations') }}" class="row align-items-center">
                        <div class="col-auto">
                            <label class="form-label">Zaman Aralığı:</label>
                        </div>
                        <div class="col-auto">
                            <select name="days" class="form-select" onchange="this.form.submit()">
                                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Son 7 Gün</option>
                                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Son 30 Gün</option>
                                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Son 90 Gün</option>
                                <option value="365" {{ $days == 365 ? 'selected' : '' }}>Son 1 Yıl</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Genel İstatistikler -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Konuşma</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($totalConversations) }}</div>
                    <div class="text-muted small">Son {{ $days }} gün</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Mesaj</div>
                    </div>
                    <div class="h1 mb-0 text-primary">{{ number_format($totalMessages) }}</div>
                    <div class="text-muted small">Tüm konuşmalarda</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Ortalama Mesaj/Konuşma</div>
                    </div>
                    <div class="h1 mb-0 text-success">{{ $avgMessagesPerConv }}</div>
                    <div class="text-muted small">Konuşma başına</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cihaz, Tarayıcı, OS Stats -->
    <div class="row mb-4">
        <!-- Cihaz Dağılımı -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Cihaz Dağılımı
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($deviceStats) > 0)
                        <canvas id="deviceChart" style="max-height: 300px;"></canvas>
                        <div class="mt-3">
                            @foreach($deviceStats as $device => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-capitalize">
                                        @if($device === 'mobile')
                                            <i class="fas fa-mobile-alt me-2 text-primary"></i>Mobil
                                        @elseif($device === 'tablet')
                                            <i class="fas fa-tablet-alt me-2 text-info"></i>Tablet
                                        @elseif($device === 'desktop')
                                            <i class="fas fa-desktop me-2 text-success"></i>Masaüstü
                                        @else
                                            <i class="fas fa-question-circle me-2 text-muted"></i>{{ ucfirst($device) }}
                                        @endif
                                    </span>
                                    <span class="badge bg-secondary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <p class="empty-title">Veri yok</p>
                            <p class="empty-subtitle text-muted">Henüz cihaz verisi yok</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tarayıcı Dağılımı -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe me-2"></i>
                        Tarayıcı Dağılımı
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($browserStats) > 0)
                        <canvas id="browserChart" style="max-height: 300px;"></canvas>
                        <div class="mt-3">
                            @foreach($browserStats as $browser => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>
                                        @if($browser === 'Chrome')
                                            <i class="fab fa-chrome me-2 text-warning"></i>
                                        @elseif($browser === 'Safari')
                                            <i class="fab fa-safari me-2 text-primary"></i>
                                        @elseif($browser === 'Firefox')
                                            <i class="fab fa-firefox me-2 text-danger"></i>
                                        @elseif($browser === 'Edge')
                                            <i class="fab fa-edge me-2 text-info"></i>
                                        @else
                                            <i class="fas fa-browser me-2 text-muted"></i>
                                        @endif
                                        {{ $browser }}
                                    </span>
                                    <span class="badge bg-secondary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <p class="empty-title">Veri yok</p>
                            <p class="empty-subtitle text-muted">Henüz tarayıcı verisi yok</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- İşletim Sistemi Dağılımı -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-laptop me-2"></i>
                        İşletim Sistemi
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($osStats) > 0)
                        <canvas id="osChart" style="max-height: 300px;"></canvas>
                        <div class="mt-3">
                            @foreach($osStats as $os => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>
                                        @if(str_contains($os, 'Windows'))
                                            <i class="fab fa-windows me-2 text-primary"></i>
                                        @elseif(str_contains($os, 'Mac') || str_contains($os, 'iOS'))
                                            <i class="fab fa-apple me-2 text-secondary"></i>
                                        @elseif(str_contains($os, 'Android'))
                                            <i class="fab fa-android me-2 text-success"></i>
                                        @elseif(str_contains($os, 'Linux'))
                                            <i class="fab fa-linux me-2 text-dark"></i>
                                        @else
                                            <i class="fas fa-question-circle me-2 text-muted"></i>
                                        @endif
                                        {{ $os }}
                                    </span>
                                    <span class="badge bg-secondary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <p class="empty-title">Veri yok</p>
                            <p class="empty-subtitle text-muted">Henüz işletim sistemi verisi yok</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Saatlik Dağılım -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>
                        Saatlik Konuşma Dağılımı
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($hourlyStats) > 0)
                        <canvas id="hourlyChart" style="height: 300px;"></canvas>
                    @else
                        <div class="empty">
                            <p class="empty-title">Veri yok</p>
                            <p class="empty-subtitle text-muted">Henüz saatlik veri yok</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ürün Etkileşimi -->
    @if(count($productEngagement) > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart me-2"></i>
                        En Çok Etkileşim Alan Ürünler
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter">
                            <thead>
                                <tr>
                                    <th>Sıra</th>
                                    <th>Ürün ID</th>
                                    <th>Etkileşim Sayısı</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productEngagement as $productId => $count)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><span class="badge bg-blue-lt">{{ $productId }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-primary"
                                                 style="width: {{ ($count / max($productEngagement)) * 100 }}%"
                                                 role="progressbar">
                                                {{ $count }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // 🎨 Chart.js Color Palette (Tabler.io colors)
    const colors = {
        primary: '#206bc4',
        secondary: '#6c757d',
        success: '#2fb344',
        info: '#4299e1',
        warning: '#f76707',
        danger: '#d63939',
        purple: '#ae3ec9',
        pink: '#d6336c',
        cyan: '#17a2b8',
        teal: '#0ca678',
    };

    const colorArray = [
        colors.primary,
        colors.success,
        colors.warning,
        colors.info,
        colors.danger,
        colors.purple,
        colors.pink,
        colors.cyan,
        colors.teal,
        colors.secondary,
    ];

    // 📱 Device Chart
    @if(count($deviceStats) > 0)
    new Chart(document.getElementById('deviceChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_map(function($key) {
                return ucfirst($key);
            }, array_keys($deviceStats))) !!},
            datasets: [{
                data: {!! json_encode(array_values($deviceStats)) !!},
                backgroundColor: colorArray,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif

    // 🌐 Browser Chart
    @if(count($browserStats) > 0)
    new Chart(document.getElementById('browserChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($browserStats)) !!},
            datasets: [{
                data: {!! json_encode(array_values($browserStats)) !!},
                backgroundColor: colorArray,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif

    // 💻 OS Chart
    @if(count($osStats) > 0)
    new Chart(document.getElementById('osChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($osStats)) !!},
            datasets: [{
                data: {!! json_encode(array_values($osStats)) !!},
                backgroundColor: colorArray,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
    @endif

    // 🕐 Hourly Chart
    @if(count($hourlyStats) > 0)
    const hourlyLabels = [];
    const hourlyData = [];

    // 0-23 arası tüm saatleri doldur
    for (let i = 0; i < 24; i++) {
        const hour = i.toString().padStart(2, '0') + ':00';
        hourlyLabels.push(hour);
        hourlyData.push({!! json_encode($hourlyStats) !!}[hour] || 0);
    }

    new Chart(document.getElementById('hourlyChart'), {
        type: 'line',
        data: {
            labels: hourlyLabels,
            datasets: [{
                label: 'Konuşma Sayısı',
                data: hourlyData,
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush
