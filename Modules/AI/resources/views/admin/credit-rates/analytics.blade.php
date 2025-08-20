@extends('admin.layout')

@section('title', 'Kredi Oranları Analitik')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">AI Kredi Sistemi</div>
                    <h2 class="page-title">Kredi Oranları Analitik</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.credit-rates.calculator') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                                <rect x="8" y="7" width="8" height="10" rx="1"></rect>
                                <path d="m8 11 2 2l4 -4"></path>
                            </svg>
                            Kredi Hesaplayıcı
                        </a>
                        <a href="{{ route('admin.ai.credit-rates.index') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg>
                            Kredi Oranları Listesi
                        </a>
                        <a href="{{ route('admin.ai.credit-rates.index') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <polyline points="9,6 15,12 9,18"></polyline>
                            </svg>
                            Ana Sayfaya Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Date Range Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Analiz Filtresi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Başlangıç Tarihi</label>
                                    <input type="date" class="form-control" id="start-date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Bitiş Tarihi</label>
                                    <input type="date" class="form-control" id="end-date" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Provider</label>
                                    <select class="form-select" id="provider-filter">
                                        <option value="">Tüm Provider'lar</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-primary d-block" id="apply-filter">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <circle cx="10" cy="10" r="7"></circle>
                                            <path d="m21 21-6 -6"></path>
                                        </svg>
                                        Filtrele
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4" id="summary-stats">
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Toplam İstek</div>
                                <div class="ms-auto">
                                    <span class="status-dot status-dot-animated bg-green"></span>
                                </div>
                            </div>
                            <div class="h1 mb-3" id="total-requests">0</div>
                            <div class="d-flex mb-2">
                                <div>Bugün</div>
                                <div class="ms-auto">
                                    <span id="today-requests" class="text-green d-inline-flex align-items-center lh-1">
                                        0 <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><polyline points="3,17 9,11 13,15 21,7"></polyline><polyline points="14,7 21,7 21,14"></polyline></svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Toplam Kredi Kullanımı</div>
                                <div class="ms-auto">
                                    <span class="status-dot status-dot-animated bg-blue"></span>
                                </div>
                            </div>
                            <div class="h1 mb-3" id="total-credits">0</div>
                            <div class="d-flex mb-2">
                                <div>Ortalama/İstek</div>
                                <div class="ms-auto">
                                    <span id="avg-credits" class="text-blue d-inline-flex align-items-center lh-1">
                                        0
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Token Kullanımı</div>
                                <div class="ms-auto">
                                    <span class="status-dot status-dot-animated bg-yellow"></span>
                                </div>
                            </div>
                            <div class="h1 mb-3" id="total-tokens">0</div>
                            <div class="d-flex mb-2">
                                <div>Input/Output</div>
                                <div class="ms-auto">
                                    <span id="token-ratio" class="text-yellow d-inline-flex align-items-center lh-1">
                                        0/0
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">En Popüler Model</div>
                                <div class="ms-auto">
                                    <span class="status-dot status-dot-animated bg-purple"></span>
                                </div>
                            </div>
                            <div class="h1 mb-3" id="popular-model">-</div>
                            <div class="d-flex mb-2">
                                <div>Kullanım Oranı</div>
                                <div class="ms-auto">
                                    <span id="popular-rate" class="text-purple d-inline-flex align-items-center lh-1">
                                        0%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-4">
                <!-- Credit Usage Trend -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kredi Kullanım Trendi</h3>
                            <div class="card-actions">
                                <div class="dropdown">
                                    <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="19" r="1"></circle><circle cx="12" cy="5" r="1"></circle></svg>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="#" data-period="daily">Günlük</a>
                                        <a class="dropdown-item" href="#" data-period="weekly">Haftalık</a>
                                        <a class="dropdown-item" href="#" data-period="monthly">Aylık</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="usage-trend-chart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Provider Distribution -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Provider Dağılımı</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="provider-distribution-chart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Model Performance Analysis -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Model Performans Analizi</h3>
                            <div class="card-actions">
                                <div class="form-selectgroup form-selectgroup-pills">
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metric" value="usage" class="form-selectgroup-input" checked>
                                        <span class="form-selectgroup-label">Kullanım</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metric" value="cost" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Maliyet</span>
                                    </label>
                                    <label class="form-selectgroup-item">
                                        <input type="radio" name="metric" value="efficiency" class="form-selectgroup-input">
                                        <span class="form-selectgroup-label">Verimlilik</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="model-performance-chart" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cost Analysis Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detaylı Maliyet Analizi</h3>
                            <div class="card-actions">
                                <button class="btn btn-outline-primary btn-sm" id="export-analysis">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                        <path d="M12 17v-6"></path>
                                        <path d="m15 14 -3 3 -3 -3"></path>
                                    </svg>
                                    Excel'e Aktar
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table" id="cost-analysis-table">
                                    <thead>
                                        <tr>
                                            <th>Provider</th>
                                            <th>Model</th>
                                            <th>Toplam İstek</th>
                                            <th>Toplam Token</th>
                                            <th>Ortalama Input</th>
                                            <th>Ortalama Output</th>
                                            <th>Toplam Kredi</th>
                                            <th>Ortalama Kredi/İstek</th>
                                            <th>Verimlilik Skoru</th>
                                            <th class="w-1">Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cost-analysis-body">
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <div class="spinner-border" role="status">
                                                    <span class="visually-hidden">Yükleniyor...</span>
                                                </div>
                                                <p class="mt-2">Analiz verileri yükleniyor...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real-time Monitoring -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Gerçek Zamanlı Monitoring</h3>
                            <div class="card-actions">
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                                    <span class="form-check-label">Otomatik Yenileme</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-uppercase text-muted font-weight-medium">Son 1 Saat</div>
                                            <div class="display-6" id="last-hour-requests">0</div>
                                            <div class="text-muted">istek</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-uppercase text-muted font-weight-medium">Kredi Kullanımı</div>
                                            <div class="display-6" id="last-hour-credits">0</div>
                                            <div class="text-muted">kredi</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-uppercase text-muted font-weight-medium">Aktif Model</div>
                                            <div class="display-6" id="active-models">0</div>
                                            <div class="text-muted">model</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card card-sm">
                                        <div class="card-body text-center">
                                            <div class="text-uppercase text-muted font-weight-medium">Sistem Durumu</div>
                                            <div class="display-6">
                                                <span id="system-status" class="status-dot status-dot-animated bg-green"></span>
                                            </div>
                                            <div class="text-muted" id="system-status-text">Normal</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.status-dot-animated {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
.card-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    let usageTrendChart, providerDistributionChart, modelPerformanceChart;
    let autoRefreshInterval;
    
    // Initialize
    loadProviders();
    loadAnalytics();
    initializeRealTimeMonitoring();
    
    // Event Listeners
    $('#apply-filter').click(loadAnalytics);
    $('#auto-refresh').change(function() {
        if ($(this).is(':checked')) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });
    
    $('input[name="metric"]').change(function() {
        updateModelPerformanceChart();
    });
    
    $('.dropdown-menu a[data-period]').click(function(e) {
        e.preventDefault();
        const period = $(this).data('period');
        updateUsageTrendChart(period);
    });
    
    $('#export-analysis').click(exportAnalysisData);
    
    // Functions
    function loadProviders() {
        $.get('/admin/ai/api/providers-models')
            .done(function(data) {
                let options = '<option value="">Tüm Provider\'lar</option>';
                data.forEach(provider => {
                    options += `<option value="${provider.id}">${provider.name}</option>`;
                });
                $('#provider-filter').html(options);
            });
    }
    
    function loadAnalytics() {
        const filters = {
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val(),
            provider_id: $('#provider-filter').val() || null
        };
        
        // Show loading
        showLoading();
        
        // Load summary stats
        $.get('/admin/ai/credit-rates/analytics/summary', filters)
            .done(function(data) {
                updateSummaryStats(data);
            });
            
        // Load charts data
        $.get('/admin/ai/credit-rates/analytics/charts', filters)
            .done(function(data) {
                updateCharts(data);
            });
            
        // Load cost analysis table
        $.get('/admin/ai/credit-rates/analytics/cost-analysis', filters)
            .done(function(data) {
                updateCostAnalysisTable(data);
            });
    }
    
    function updateSummaryStats(data) {
        $('#total-requests').text(data.total_requests?.toLocaleString() || '0');
        $('#today-requests').text(data.today_requests?.toLocaleString() || '0');
        $('#total-credits').text(data.total_credits?.toLocaleString() || '0');
        $('#avg-credits').text(data.avg_credits?.toFixed(2) || '0');
        $('#total-tokens').text(data.total_tokens?.toLocaleString() || '0');
        $('#token-ratio').text(`${data.input_tokens?.toLocaleString() || '0'}/${data.output_tokens?.toLocaleString() || '0'}`);
        $('#popular-model').text(data.popular_model || '-');
        $('#popular-rate').text(`${data.popular_rate || 0}%`);
    }
    
    function updateCharts(data) {
        // Usage Trend Chart
        if (usageTrendChart) {
            usageTrendChart.destroy();
        }
        
        const ctx1 = document.getElementById('usage-trend-chart').getContext('2d');
        usageTrendChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: data.usage_trend.labels,
                datasets: [{
                    label: 'Kredi Kullanımı',
                    data: data.usage_trend.credits,
                    borderColor: '#206bc4',
                    backgroundColor: 'rgba(32, 107, 196, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'İstek Sayısı',
                    data: data.usage_trend.requests,
                    borderColor: '#29b765',
                    backgroundColor: 'rgba(41, 183, 101, 0.1)',
                    tension: 0.3,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Kredi'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'İstek Sayısı'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
        
        // Provider Distribution Chart
        if (providerDistributionChart) {
            providerDistributionChart.destroy();
        }
        
        const ctx2 = document.getElementById('provider-distribution-chart').getContext('2d');
        providerDistributionChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: data.provider_distribution.labels,
                datasets: [{
                    data: data.provider_distribution.data,
                    backgroundColor: [
                        '#206bc4',
                        '#29b765',
                        '#f59f00',
                        '#d63384',
                        '#6f42c1',
                        '#20c997'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Model Performance Chart (initialize)
        updateModelPerformanceChart(data.model_performance);
    }
    
    function updateModelPerformanceChart(data = null) {
        if (!data) {
            // Reload data based on current metric
            const metric = $('input[name="metric"]:checked').val();
            const filters = {
                start_date: $('#start-date').val(),
                end_date: $('#end-date').val(),
                provider_id: $('#provider-filter').val() || null,
                metric: metric
            };
            
            $.get('/admin/ai/credit-rates/analytics/model-performance', filters)
                .done(function(data) {
                    renderModelPerformanceChart(data);
                });
        } else {
            renderModelPerformanceChart(data);
        }
    }
    
    function renderModelPerformanceChart(data) {
        if (modelPerformanceChart) {
            modelPerformanceChart.destroy();
        }
        
        const ctx3 = document.getElementById('model-performance-chart').getContext('2d');
        modelPerformanceChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: data.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
    }
    
    function updateCostAnalysisTable(data) {
        let tbody = '';
        
        if (data.length === 0) {
            tbody = `
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <p>Seçilen tarih aralığında veri bulunamadı.</p>
                    </td>
                </tr>
            `;
        } else {
            data.forEach(item => {
                const trend = item.trend > 0 
                    ? '<span class="text-green">↗</span>' 
                    : item.trend < 0 
                        ? '<span class="text-red">↘</span>' 
                        : '<span class="text-muted">→</span>';
                        
                const efficiencyColor = item.efficiency_score >= 80 
                    ? 'text-green' 
                    : item.efficiency_score >= 60 
                        ? 'text-yellow' 
                        : 'text-red';
                        
                tbody += `
                    <tr>
                        <td><strong>${item.provider_name}</strong></td>
                        <td><code>${item.model_name}</code></td>
                        <td>${item.total_requests?.toLocaleString() || 0}</td>
                        <td>${item.total_tokens?.toLocaleString() || 0}</td>
                        <td>${item.avg_input_tokens?.toLocaleString() || 0}</td>
                        <td>${item.avg_output_tokens?.toLocaleString() || 0}</td>
                        <td><strong>${item.total_credits?.toLocaleString() || 0}</strong></td>
                        <td>${item.avg_credits_per_request?.toFixed(3) || '0.000'}</td>
                        <td class="${efficiencyColor}"><strong>${item.efficiency_score || 0}/100</strong></td>
                        <td>${trend}</td>
                    </tr>
                `;
            });
        }
        
        $('#cost-analysis-body').html(tbody);
    }
    
    function initializeRealTimeMonitoring() {
        updateRealTimeStats();
        if ($('#auto-refresh').is(':checked')) {
            startAutoRefresh();
        }
    }
    
    function updateRealTimeStats() {
        $.get('/admin/ai/credit-rates/analytics/realtime')
            .done(function(data) {
                $('#last-hour-requests').text(data.last_hour_requests?.toLocaleString() || '0');
                $('#last-hour-credits').text(data.last_hour_credits?.toLocaleString() || '0');
                $('#active-models').text(data.active_models || '0');
                
                // Update system status
                if (data.system_status === 'healthy') {
                    $('#system-status').removeClass().addClass('status-dot status-dot-animated bg-green');
                    $('#system-status-text').text('Normal');
                } else if (data.system_status === 'warning') {
                    $('#system-status').removeClass().addClass('status-dot status-dot-animated bg-yellow');
                    $('#system-status-text').text('Uyarı');
                } else {
                    $('#system-status').removeClass().addClass('status-dot status-dot-animated bg-red');
                    $('#system-status-text').text('Hata');
                }
            });
    }
    
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            updateRealTimeStats();
        }, 30000); // 30 seconds
    }
    
    function stopAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    }
    
    function updateUsageTrendChart(period) {
        const filters = {
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val(),
            provider_id: $('#provider-filter').val() || null,
            period: period
        };
        
        $.get('/admin/ai/credit-rates/analytics/usage-trend', filters)
            .done(function(data) {
                usageTrendChart.data.labels = data.labels;
                usageTrendChart.data.datasets[0].data = data.credits;
                usageTrendChart.data.datasets[1].data = data.requests;
                usageTrendChart.update();
            });
    }
    
    function exportAnalysisData() {
        const filters = {
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val(),
            provider_id: $('#provider-filter').val() || null
        };
        
        // Create form and submit
        const form = $('<form>', {
            'method': 'POST',
            'action': '/admin/ai/credit-rates/analytics/export'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        // Add filters
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key,
                    'value': filters[key]
                }));
            }
        });
        
        $('body').append(form);
        form.submit();
        form.remove();
    }
    
    function showLoading() {
        $('#summary-stats .h1').text('...');
        $('#cost-analysis-body').html(`
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                    <p class="mt-2">Analiz verileri yükleniyor...</p>
                </td>
            </tr>
        `);
    }
    
    // Auto refresh initial setup
    startAutoRefresh();
});
</script>
@endpush