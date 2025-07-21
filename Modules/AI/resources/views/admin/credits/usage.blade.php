@extends('admin.layout')

@section('page-title', 'Kredi Kullanım Raporları')
@section('page-subtitle', 'AI kredi kullanım analizi ve istatistikleri')

@section('content')
<div class="container-xl">
    <!-- Özet Kartları -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Kredi Kullanımı</div>
                    </div>
                    <div class="h1 mb-0 text-primary" id="totalCreditsUsed">-</div>
                    <div class="text-muted small">Tüm zamanlar</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bu Ay Kullanılan</div>
                    </div>
                    <div class="h1 mb-0 text-info" id="monthlyCreditsUsed">-</div>
                    <div class="text-muted small">{{ date('F Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Bugün Kullanılan</div>
                    </div>
                    <div class="h1 mb-0 text-success" id="dailyCreditsUsed">-</div>
                    <div class="text-muted small">{{ date('d.m.Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Ortalama Günlük</div>
                    </div>
                    <div class="h1 mb-0 text-warning" id="avgDailyUsage">-</div>
                    <div class="text-muted small">Son 30 gün</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Kullanım Trendi Grafiği -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>
                        Kredi Kullanım Trendi
                    </h3>
                    <div class="card-actions">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="period" id="period7" value="7" checked>
                            <label for="period7" class="btn btn-sm btn-outline-primary">7 Gün</label>
                            
                            <input type="radio" class="btn-check" name="period" id="period30" value="30">
                            <label for="period30" class="btn btn-sm btn-outline-primary">30 Gün</label>
                            
                            <input type="radio" class="btn-check" name="period" id="period90" value="90">
                            <label for="period90" class="btn btn-sm btn-outline-primary">90 Gün</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="usageChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Provider Bazlı Kullanım -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-server me-2"></i>
                        Provider Bazlı Kullanım
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="providerChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs me-2"></i>
                        Feature Bazlı Kullanım
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="featureChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detaylı Kullanım Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i>
                        Detaylı Kredi Kullanımı
                    </h3>
                    <div class="card-actions">
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-01') }}">
                            <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                            <button class="btn btn-primary" onclick="filterUsage()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter" id="usageTable">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Tenant</th>
                                    <th>Provider</th>
                                    <th>Feature</th>
                                    <th>Input Token</th>
                                    <th>Output Token</th>
                                    <th>Toplam Kredi</th>
                                    <th>Maliyet</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AJAX ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- AJAX ile doldurulacak -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik değişkenleri
let usageChart, providerChart, featureChart;

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    initializeCharts();
    loadUsageData();
    
    // Period değişikliklerini dinle
    document.querySelectorAll('input[name="period"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateUsageChart(this.value);
        });
    });
});

// İstatistikleri yükle
function loadStatistics() {
    // AJAX call to load statistics
    fetch('/admin/ai/credits/api/statistics')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalCreditsUsed').textContent = data.total_credits_used || 0;
            document.getElementById('monthlyCreditsUsed').textContent = data.monthly_credits_used || 0;
            document.getElementById('dailyCreditsUsed').textContent = data.daily_credits_used || 0;
            document.getElementById('avgDailyUsage').textContent = data.avg_daily_usage || 0;
        })
        .catch(error => {
            console.error('İstatistik yükleme hatası:', error);
        });
}

// Grafikleri başlat
function initializeCharts() {
    // Kullanım trendi grafiği
    const usageCtx = document.getElementById('usageChart').getContext('2d');
    usageChart = new Chart(usageCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Kredi Kullanımı',
                data: [],
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Provider grafiği
    const providerCtx = document.getElementById('providerChart').getContext('2d');
    providerChart = new Chart(providerCtx, {
        type: 'doughnut',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Feature grafiği
    const featureCtx = document.getElementById('featureChart').getContext('2d');
    featureChart = new Chart(featureCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Kredi Kullanımı',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Kullanım verilerini yükle
function loadUsageData() {
    // AJAX call to load usage data
    fetch('/admin/ai/credits/api/usage-data')
        .then(response => response.json())
        .then(data => {
            updateUsageChart(7); // Default 7 gün
            updateProviderChart(data.provider_usage || []);
            updateFeatureChart(data.feature_usage || []);
            updateUsageTable(data.detailed_usage || []);
        })
        .catch(error => {
            console.error('Kullanım verisi yükleme hatası:', error);
        });
}

// Kullanım grafiğini güncelle
function updateUsageChart(period) {
    fetch(`/admin/ai/credits/api/usage-trend?period=${period}`)
        .then(response => response.json())
        .then(data => {
            usageChart.data.labels = data.labels || [];
            usageChart.data.datasets[0].data = data.values || [];
            usageChart.update();
        })
        .catch(error => {
            console.error('Trend verisi yükleme hatası:', error);
        });
}

// Provider grafiğini güncelle
function updateProviderChart(data) {
    providerChart.data.labels = data.map(item => item.provider);
    providerChart.data.datasets[0].data = data.map(item => item.credits);
    providerChart.update();
}

// Feature grafiğini güncelle
function updateFeatureChart(data) {
    featureChart.data.labels = data.map(item => item.feature);
    featureChart.data.datasets[0].data = data.map(item => item.credits);
    featureChart.update();
}

// Kullanım tablosunu güncelle
function updateUsageTable(data) {
    const tbody = document.querySelector('#usageTable tbody');
    tbody.innerHTML = '';
    
    data.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.date}</td>
            <td>${item.tenant}</td>
            <td><span class="badge bg-primary">${item.provider}</span></td>
            <td>${item.feature}</td>
            <td>${item.input_tokens}</td>
            <td>${item.output_tokens}</td>
            <td class="fw-bold">${item.total_credits}</td>
            <td>$${item.cost}</td>
        `;
        tbody.appendChild(row);
    });
}

// Kullanımı filtrele
function filterUsage() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    fetch(`/admin/ai/credits/api/usage-filter?start=${startDate}&end=${endDate}`)
        .then(response => response.json())
        .then(data => {
            updateUsageTable(data.usage || []);
        })
        .catch(error => {
            console.error('Filtreleme hatası:', error);
        });
}
</script>
@endsection