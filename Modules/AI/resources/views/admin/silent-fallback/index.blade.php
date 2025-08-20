@extends('admin.layout')

@section('title', 'Silent Fallback Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@1.119.0/icons-sprite.svg">
<style>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}
.status-online { background-color: #28a745; }
.status-offline { background-color: #dc3545; }
.status-warning { background-color: #ffc107; }

.provider-card {
    transition: all 0.3s ease;
}
.provider-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.log-entry {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.85rem;
    background: #f8f9fa;
    border-left: 3px solid #007bff;
    padding: 8px 12px;
    margin-bottom: 8px;
    border-radius: 4px;
}

.log-entry.error {
    border-left-color: #dc3545;
    background: #fff5f5;
}

.log-entry.success {
    border-left-color: #28a745;
    background: #f0fff4;
}
</style>
@endpush

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        🔇 Silent Fallback Dashboard
                    </h2>
                    <div class="text-muted">AI provider fallback sistem izleme ve yönetimi</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.silent-fallback.configuration') }}" class="btn btn-primary">
                            <i class="fa-solid fa-gear me-1"></i>
                            Yapılandırma
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="testFallback()">
                            <i class="fa-solid fa-vial me-1"></i>
                            Test Fallback
                        </button>
                        <button type="button" class="btn btn-outline-danger" onclick="clearStats()">
                            <i class="fa-solid fa-trash me-1"></i>
                            İstatistikleri Temizle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            
            <!-- Statistics Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa-solid fa-arrows-left-right text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <div>
                                    <div class="h1 m-0">{{ $stats['total_fallbacks'] }}</div>
                                    <div class="text-muted">Toplam Fallback</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa-solid fa-calendar-day text-success" style="font-size: 2.5rem;"></i>
                                </div>
                                <div>
                                    <div class="h1 m-0">{{ $stats['today'] }}</div>
                                    <div class="text-muted">Bugün</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa-solid fa-calendar text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                                <div>
                                    <div class="h1 m-0">{{ $stats['last_30_days'] }}</div>
                                    <div class="text-muted">Son 30 Gün</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fa-solid fa-server text-info" style="font-size: 2.5rem;"></i>
                                </div>
                                <div>
                                    <div class="h1 m-0">{{ is_array($providers) ? count($providers) : $providers->count() }}</div>
                                    <div class="text-muted">Aktif Provider</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-deck row-cards">
                <!-- Provider Status -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa-solid fa-server me-2"></i>
                                Provider Durumları
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($providers as $provider)
                                <div class="col-md-4 mb-3">
                                    <div class="card provider-card">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="status-indicator {{ $provider['is_available'] ? 'status-online' : 'status-offline' }}"></span>
                                                <strong>{{ $provider['name'] }}</strong>
                                            </div>
                                            <div class="text-muted small">
                                                <div>Modeller: {{ $provider['model_count'] }}</div>
                                                <div>Öncelik: {{ $provider['priority'] }}</div>
                                                <div>Varsayılan: {{ $provider['default_model'] }}</div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge {{ $provider['is_available'] ? 'badge-success' : 'badge-danger' }}">
                                                    {{ $provider['is_available'] ? 'Çevrimiçi' : 'Çevrimdışı' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fallback Analytics Chart -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Son 7 Gün
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="fallbackChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Fallback Logs -->
            @if(count($recent_fallbacks) > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa-solid fa-clock-rotate-left me-2"></i>
                                Son Fallback Logları
                            </h3>
                        </div>
                        <div class="card-body">
                            @foreach($recent_fallbacks as $log)
                            <div class="log-entry {{ strpos($log['message'], 'SUCCESS') !== false ? 'success' : (strpos($log['message'], 'failed') !== false ? 'error' : '') }}">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $log['message'] }}</span>
                                    <small class="text-muted">{{ $log['timestamp'] }}</small>
                                </div>
                                @if($log['data'] !== '{}')
                                <div class="mt-1 text-muted small">
                                    {{ $log['data'] }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Test Fallback Modal -->
<div class="modal modal-blur fade" id="testFallbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fallback Sistemi Test</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testFallbackForm">
                    <div class="mb-3">
                        <label class="form-label">Test Prompt</label>
                        <textarea class="form-control" name="prompt" rows="3" placeholder="Test için kullanılacak prompt...">Test prompt for fallback system validation</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Simüle edilecek başarısız provider</label>
                        <select class="form-select" name="original_provider">
                            <option value="unknown">Bilinmeyen</option>
                            @foreach($providers as $provider)
                            <option value="{{ $provider['name'] }}">{{ $provider['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div id="testResult" class="mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                <button type="button" class="btn btn-primary" onclick="executeTest()">
                    <i class="fa-solid fa-play me-1"></i>
                    Test Çalıştır
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
// Fallback Chart
const ctx = document.getElementById('fallbackChart').getContext('2d');
const fallbackChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(array_slice(array_keys($stats['daily_fallbacks']), -7)),
        datasets: [{
            label: 'Fallback Sayısı',
            data: @json(array_slice(array_values($stats['daily_fallbacks']), -7)),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4,
            fill: true
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

// Test Fallback Function
function testFallback() {
    const modal = new bootstrap.Modal(document.getElementById('testFallbackModal'));
    modal.show();
}

function executeTest() {
    const form = document.getElementById('testFallbackForm');
    const formData = new FormData(form);
    const resultDiv = document.getElementById('testResult');
    
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Test çalıştırılıyor...';
    
    fetch('{{ route("admin.ai.silent-fallback.test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <strong>✅ Test başarılı!</strong><br>
                    Fallback Provider: ${data.fallback_provider}<br>
                    Fallback Model: ${data.fallback_model}
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ Test başarısız!</strong><br>
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <strong>❌ Test hatası!</strong><br>
                ${error.message}
            </div>
        `;
    });
}

// Clear Statistics Function
function clearStats() {
    if (!confirm('Fallback istatistiklerini temizlemek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch('{{ route("admin.ai.silent-fallback.clear-stats") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            location.reload();
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('İstatistik temizleme başarısız: ' + error.message);
    });
}

// Auto refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endpush