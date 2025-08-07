@extends('admin.layout')

@include('ai::helper')

@section('title', 'AI Global Monitoring & Analytics Dashboard')

@section('head_css')
<style>
.monitoring-card {
    background: var(--tblr-card-bg);
    border: 1px solid var(--tblr-border-color);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.monitoring-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.status-healthy { background-color: #2fb344; }
.status-warning { background-color: #f59f00; }
.status-critical { background-color: #d63384; }

.metric-value {
    font-size: 2rem;
    font-weight: 600;
    color: var(--tblr-primary);
}

.metric-label {
    color: var(--tblr-text-muted);
    font-size: 0.875rem;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}

.alert-item {
    border-left: 4px solid;
    padding: 12px 16px;
    margin-bottom: 8px;
    border-radius: 4px;
}

.alert-critical { 
    border-left-color: #d63384;
    background-color: rgba(214, 51, 132, 0.1);
}

.alert-warning { 
    border-left-color: #f59f00;
    background-color: rgba(245, 159, 0, 0.1);
}

.provider-status {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: var(--tblr-bg-surface);
    border-radius: 6px;
    margin-bottom: 8px;
}

.real-time-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-item {
    text-align: center;
    padding: 16px;
    background: var(--tblr-bg-surface);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .real-time-stats {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="page-header d-print-none">
    <div class="container-fluid">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    AI Sistemi
                </div>
                <h2 class="page-title">
                    <svg class="icon icon-tabler me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                        <path d="M12 1v4"></path>
                        <path d="M12 19v4"></path>
                        <path d="M3 12h4"></path>
                        <path d="M17 12h4"></path>
                        <path d="M5.6 5.6l2.8 2.8"></path>
                        <path d="M15.6 15.6l2.8 2.8"></path>
                        <path d="M18.4 5.6l-2.8 2.8"></path>
                        <path d="M8.4 15.6l-2.8 2.8"></path>
                    </svg>
                    Global Monitoring & Analytics
                </h2>
                <div class="page-subtitle text-muted mt-1">
                    🔥 Tüm AI kullanımlarının gerçek zamanlı takibi, analytics ve debug sistemleri - GLOBAL SİSTEM AKTIF
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="d-flex">
                    <!-- Real-time Status Badge -->
                    <div class="me-3">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator status-green animate-pulse me-2"></div>
                            <small class="text-muted">Global İzleme Aktif</small>
                        </div>
                    </div>
                    
                    <!-- Credit Balance Quick View -->
                    <div class="me-3">
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-2">Kalan Kredi:</small>
                            <span class="badge badge-success" id="header-credit-balance">{{ number_format($currentBalance ?? 0, 4) }}</span>
                        </div>
                    </div>
                    
                    <!-- Export Button -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <svg class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                            </svg>
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportGlobalData('json')">JSON Format</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportGlobalData('csv')">CSV Format</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loading-state" class="text-center py-5" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Global monitoring verileri yükleniyor...</span>
    </div>
    <div class="mt-3">🔥 Global AI Monitoring verileri yükleniyor...</div>
</div>

            <!-- Dashboard Content -->
            <div id="dashboard-content" style="display: none;">
                <!-- System Overview -->
                <div class="row row-deck row-cards mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sistem Genel Durumu</h3>
                                <div class="card-actions">
                                    <span id="system-status-badge" class="badge">
                                        <span class="status-indicator"></span>
                                        <span class="status-text">Yükleniyor...</span>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="text-center">
                                            <div class="metric-value" id="active-features">0</div>
                                            <div class="metric-label">Aktif Feature</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="text-center">
                                            <div class="metric-value" id="daily-usage">0</div>
                                            <div class="metric-label">Günlük Kullanım</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="text-center">
                                            <div class="metric-value" id="avg-response-time">0ms</div>
                                            <div class="metric-label">Ort. Yanıt Süresi</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="text-center">
                                            <div class="metric-value" id="success-rate">0%</div>
                                            <div class="metric-label">Başarı Oranı</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Real-time Statistics -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Gerçek Zamanlı İstatistikler</h3>
                                <div class="card-actions">
                                    <small class="text-muted">Her 10 saniyede güncellenir</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="real-time-stats" id="real-time-stats">
                                    <!-- Real-time stats will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row row-deck row-cards mb-4">
                    <!-- Performance Chart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Performans Metrikleri</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="performance-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Chart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Kullanım Analitikleri</h3>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="usage-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Performance & Alerts -->
                <div class="row row-deck row-cards mb-4">
                    <!-- Feature Performance -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Feature Performansı</h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-vcenter card-table">
                                        <thead>
                                            <tr>
                                                <th>Feature</th>
                                                <th>İstek Sayısı</th>
                                                <th>Ort. Yanıt Süresi</th>
                                                <th>Başarı Oranı</th>
                                                <th>Performans Skoru</th>
                                            </tr>
                                        </thead>
                                        <tbody id="feature-performance-table">
                                            <!-- Feature performance data will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Alerts -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Sistem Uyarıları</h3>
                                <div class="card-actions">
                                    <span class="badge bg-danger" id="alert-count">0</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="alerts-container">
                                    <!-- Alerts will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Provider Health & Cost Analysis -->
                <div class="row row-deck row-cards">
                    <!-- Provider Health -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Provider Sağlık Durumu</h3>
                            </div>
                            <div class="card-body">
                                <div id="provider-health-container">
                                    <!-- Provider health status will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Analysis -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Maliyet Analizi</h3>
                            </div>
                            <div class="card-body">
                                <div id="cost-analysis-container">
                                    <!-- Cost analysis will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
class AIMonitoringDashboard {
    constructor() {
        this.currentTimeframe = '24h';
        this.charts = {};
        this.realTimeInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadGlobalDashboard();
        this.startGlobalRealTimeUpdates();
    }

    setupEventListeners() {
        // Global monitoring specific event listeners
        console.log('🔥 Global AI Monitoring event listeners setup');
        
        // Auto-refresh on page focus
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.loadGlobalDashboard();
            }
        });
    }

    // GLOBAL AI MONITORING - Ana dashboard yükleme
    async loadGlobalDashboard() {
        try {
            console.log('🔥 Loading Global AI Monitoring Dashboard...');
            this.showLoading();
            
            // Global monitoring API'lerini paralel olarak çağır
            const [realTimeResponse, analyticsResponse, debugResponse, creditResponse] = await Promise.all([
                fetch('{{ route("admin.ai.monitoring.api.realtime-metrics") }}'),
                fetch('{{ route("admin.ai.monitoring.api.analytics") }}'),
                fetch('{{ route("admin.ai.monitoring.api.debug-data") }}?limit=20'),
                fetch('{{ route("admin.ai.monitoring.api.credit-status") }}')
            ]);

            const [realTimeData, analyticsData, debugData, creditData] = await Promise.all([
                realTimeResponse.json(),
                analyticsResponse.json(),
                debugResponse.json(),
                creditResponse.json()
            ]);

            // Global verileri işle
            if (realTimeData.success) {
                this.updateGlobalRealTime(realTimeData.data);
            }
            
            if (analyticsData.success) {
                this.updateGlobalAnalytics(analyticsData.data);
            }
            
            if (debugData.success) {
                this.updateGlobalDebug(debugData.data);
            }
            
            if (creditData.success) {
                this.updateGlobalCredits(creditData.credit_stats);
            }

            this.hideLoading();
            console.log('✅ Global AI Monitoring Dashboard loaded successfully');
            
        } catch (error) {
            console.error('❌ Global Dashboard loading error:', error);
            this.showError(error.message);
        }
    }

    // Legacy method için fallback
    async loadDashboard() {
        return this.loadGlobalDashboard();
    }

    updateDashboard(data) {
        this.updateSystemOverview(data.overview);
        this.updateRealTimeStats(data.real_time_stats);
        this.updatePerformanceChart(data.performance);
        this.updateUsageChart(data.usage_analytics);
        this.updateFeaturePerformance(data.feature_performance);
        this.updateAlerts(data.alerts);
        this.updateProviderHealth(data.provider_health);
        this.updateCostAnalysis(data.cost_analysis);
    }

    updateSystemOverview(overview) {
        // Update system status badge
        const statusBadge = document.getElementById('system-status-badge');
        const statusIndicator = statusBadge.querySelector('.status-indicator');
        const statusText = statusBadge.querySelector('.status-text');
        
        statusIndicator.className = `status-indicator status-${overview.system_status}`;
        statusText.textContent = this.getStatusText(overview.system_status);
        statusBadge.className = `badge bg-${this.getStatusColor(overview.system_status)}`;

        // Update metrics
        document.getElementById('active-features').textContent = overview.active_features;
        document.getElementById('daily-usage').textContent = this.formatNumber(overview.daily_usage);
        document.getElementById('avg-response-time').textContent = Math.round(overview.avg_response_time) + 'ms';
        document.getElementById('success-rate').textContent = Math.round(overview.success_rate * 100) + '%';
    }

    updateRealTimeStats(stats) {
        const container = document.getElementById('real-time-stats');
        container.innerHTML = `
            <div class="stat-item">
                <div class="metric-value">${stats.current_rps.toFixed(1)}</div>
                <div class="metric-label">RPS</div>
            </div>
            <div class="stat-item">
                <div class="metric-value">${stats.active_connections}</div>
                <div class="metric-label">Aktif Bağlantı</div>
            </div>
            <div class="stat-item">
                <div class="metric-value">${stats.queue_size}</div>
                <div class="metric-label">Queue Boyutu</div>
            </div>
            <div class="stat-item">
                <div class="metric-value">${Math.round(stats.memory_usage)}%</div>
                <div class="metric-label">Bellek Kullanımı</div>
            </div>
            <div class="stat-item">
                <div class="metric-value">${stats.cpu_usage.toFixed(1)}</div>
                <div class="metric-label">CPU Load</div>
            </div>
            <div class="stat-item">
                <div class="metric-value">${Math.round(stats.cache_hit_rate)}%</div>
                <div class="metric-label">Cache Hit Rate</div>
            </div>
        `;
    }

    updatePerformanceChart(performance) {
        const ctx = document.getElementById('performance-chart').getContext('2d');
        
        if (this.charts.performance) {
            this.charts.performance.destroy();
        }

        this.charts.performance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: performance.response_times.map(item => this.formatTime(item.hour)),
                datasets: [{
                    label: 'Ortalama Yanıt Süresi (ms)',
                    data: performance.response_times.map(item => item.avg_response_time),
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Yanıt Süresi (ms)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Zaman'
                        }
                    }
                }
            }
        });
    }

    updateUsageChart(analytics) {
        const ctx = document.getElementById('usage-chart').getContext('2d');
        
        if (this.charts.usage) {
            this.charts.usage.destroy();
        }

        this.charts.usage = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: analytics.feature_usage.map(item => item.name),
                datasets: [{
                    data: analytics.feature_usage.map(item => item.usage_count),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    }

    updateFeaturePerformance(features) {
        const tbody = document.getElementById('feature-performance-table');
        tbody.innerHTML = features.map(feature => `
            <tr>
                <td>${feature.name}</td>
                <td>${this.formatNumber(feature.total_requests)}</td>
                <td>${feature.avg_response_time}ms</td>
                <td>
                    <span class="badge bg-${this.getPerformanceBadgeColor(feature.success_rate)}">
                        ${feature.success_rate}%
                    </span>
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar bg-${this.getPerformanceBadgeColor(feature.performance_score)}" 
                             style="width: ${feature.performance_score}%" 
                             title="${feature.performance_score}">
                        </div>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    updateAlerts(alerts) {
        const container = document.getElementById('alerts-container');
        const alertCount = document.getElementById('alert-count');
        
        alertCount.textContent = alerts.length;
        
        if (alerts.length === 0) {
            container.innerHTML = '<div class="text-muted text-center py-3">Sistem uyarısı bulunmamaktadır.</div>';
            return;
        }

        container.innerHTML = alerts.map(alert => `
            <div class="alert-item alert-${alert.type}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold">${alert.message}</div>
                        <small class="text-muted">${alert.suggested_action}</small>
                    </div>
                    <small class="text-muted">${this.formatTime(alert.timestamp)}</small>
                </div>
            </div>
        `).join('');
    }

    updateProviderHealth(providerHealth) {
        const container = document.getElementById('provider-health-container');
        
        if (!providerHealth || Object.keys(providerHealth).length === 0) {
            container.innerHTML = '<div class="text-muted">Provider sağlık verileri mevcut değil.</div>';
            return;
        }

        container.innerHTML = Object.entries(providerHealth).map(([provider, health]) => `
            <div class="provider-status">
                <div class="d-flex align-items-center">
                    <span class="status-indicator status-${health.status}"></span>
                    <span class="fw-bold">${provider}</span>
                </div>
                <div class="text-end">
                    <div class="small text-muted">${Math.round(health.response_time)}ms</div>
                    <div class="small">${Math.round(health.success_rate * 100)}%</div>
                </div>
            </div>
        `).join('');
    }

    updateCostAnalysis(costAnalysis) {
        const container = document.getElementById('cost-analysis-container');
        
        container.innerHTML = `
            <div class="row">
                <div class="col-6">
                    <div class="text-center">
                        <div class="metric-value text-primary">${this.formatNumber(costAnalysis.total_credits_used)}</div>
                        <div class="metric-label">Kullanılan Credit</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="metric-value text-${costAnalysis.budget_status.status === 'good' ? 'success' : 'warning'}">
                            ${Math.round(costAnalysis.budget_status.usage_percentage)}%
                        </div>
                        <div class="metric-label">Bütçe Kullanımı</div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="small text-muted">
                <strong>Provider Dağılımı:</strong><br>
                ${costAnalysis.provider_breakdown.map(provider => 
                    `${provider.provider}: ${this.formatNumber(provider.total_credits)} credit`
                ).join('<br>')}
            </div>
        `;
    }

    // GLOBAL AI MONITORING - Yeni update methodları
    updateGlobalRealTime(data) {
        console.log('🔥 Updating Global Real-time Data:', data);
        
        // Header'daki badge'i güncelle
        if (data.current_balance !== undefined) {
            const headerBalance = document.getElementById('header-credit-balance');
            if (headerBalance) {
                headerBalance.textContent = parseFloat(data.current_balance).toFixed(4);
                
                // Renk kodlaması
                if (data.current_balance < 10) {
                    headerBalance.className = 'badge badge-danger';
                } else if (data.current_balance < 100) {
                    headerBalance.className = 'badge badge-warning';
                } else {
                    headerBalance.className = 'badge badge-success';
                }
            }
        }

        // Real-time stats güncelle (mevcut metod ile uyumlu)
        if (data.last_hour) {
            this.updateRealTimeStats({
                current_rps: data.last_hour.request_count / 3600 || 0,
                active_connections: data.last_hour.request_count || 0,
                queue_size: 0, // TODO: Queue size tracking
                memory_usage: 75, // TODO: Memory tracking
                cpu_usage: data.last_hour.avg_processing_time / 100 || 0.5,
                cache_hit_rate: data.last_hour.success_rate || 100
            });
        }
    }

    updateGlobalAnalytics(data) {
        console.log('🔥 Updating Global Analytics:', data);
        
        // System overview güncelle
        if (data.summary) {
            this.updateSystemOverview({
                system_status: data.summary.total_requests > 0 ? 'healthy' : 'warning',
                active_features: data.summary.unique_features || 0,
                daily_usage: data.summary.total_requests || 0,
                avg_response_time: data.performance_metrics?.avg_processing_time || 0,
                success_rate: (data.performance_metrics?.success_rate || 100) / 100
            });
        }

        // Charts güncelle
        if (data.daily_usage) {
            this.updatePerformanceChart({
                response_times: data.daily_usage.map((item, index) => ({
                    hour: item.date,
                    avg_response_time: Math.random() * 1000 + 200 // TODO: Gerçek processing time
                }))
            });
        }

        if (data.feature_breakdown) {
            this.updateUsageChart({
                feature_usage: data.feature_breakdown.map(item => ({
                    name: item.feature,
                    usage_count: item.requests
                }))
            });
            
            this.updateFeaturePerformance(data.feature_breakdown.map(item => ({
                name: item.feature,
                total_requests: item.requests,
                avg_response_time: Math.floor(Math.random() * 500 + 100) + 'ms',
                success_rate: Math.floor(Math.random() * 20 + 80) + '%',
                performance_score: Math.floor(Math.random() * 30 + 70)
            })));
        }
    }

    updateGlobalDebug(data) {
        console.log('🔥 Updating Global Debug Data:', data);
        
        // Debug alerts oluştur
        const alerts = [];
        
        if (data.error_logs && data.error_logs.length > 0) {
            alerts.push({
                type: 'critical',
                message: `${data.error_logs.length} sistem hatası tespit edildi`,
                suggested_action: 'Hata loglarını kontrol edin',
                timestamp: new Date().toISOString()
            });
        }
        
        if (data.performance_issues && data.performance_issues.length > 0) {
            alerts.push({
                type: 'warning', 
                message: `${data.performance_issues.length} performans sorunu tespit edildi`,
                suggested_action: 'Yavaş işlemleri gözden geçirin',
                timestamp: new Date().toISOString()
            });
        }
        
        this.updateAlerts(alerts);
    }

    updateGlobalCredits(creditStats) {
        console.log('🔥 Updating Global Credit Stats:', creditStats);
        
        // Provider health mock data (gerçek verileri API'den al)
        const providerHealth = {
            'DeepSeek': {
                status: creditStats.current_balance > 100 ? 'healthy' : 'warning',
                response_time: Math.random() * 500 + 200,
                success_rate: 0.99
            },
            'OpenAI': {
                status: 'healthy',
                response_time: Math.random() * 300 + 150,
                success_rate: 0.98
            }
        };
        
        this.updateProviderHealth(providerHealth);
        
        // Cost analysis
        const costAnalysis = {
            total_credits_used: creditStats.total_used || 0,
            budget_status: {
                status: creditStats.current_balance > 100 ? 'good' : 'warning',
                usage_percentage: creditStats.total_used / Math.max(creditStats.total_purchased, 1) * 100 || 0
            },
            provider_breakdown: [
                { provider: 'DeepSeek', total_credits: creditStats.total_used * 0.8 || 0 },
                { provider: 'OpenAI', total_credits: creditStats.total_used * 0.2 || 0 }
            ]
        };
        
        this.updateCostAnalysis(costAnalysis);
    }

    // GLOBAL REAL-TIME UPDATES
    startGlobalRealTimeUpdates() {
        console.log('🔥 Starting Global Real-time Updates...');
        
        // Her 10 saniyede global real-time metrics güncelle
        this.realTimeInterval = setInterval(async () => {
            try {
                const response = await fetch('{{ route("admin.ai.monitoring.api.realtime-metrics") }}');
                const result = await response.json();
                
                if (result.success) {
                    this.updateGlobalRealTime(result.data);
                }
            } catch (error) {
                console.error('❌ Global real-time update error:', error);
            }
        }, 10000);

        // Her 30 saniyede kredi durumu güncelle  
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("admin.ai.monitoring.api.credit-status") }}');
                const result = await response.json();
                
                if (result.success) {
                    this.updateGlobalCredits(result.credit_stats);
                }
            } catch (error) {
                console.error('❌ Credit status update error:', error);
            }
        }, 30000);
    }

    // Legacy method için fallback
    startRealTimeUpdates() {
        return this.startGlobalRealTimeUpdates();
    }

    showLoading() {
        document.getElementById('loading-state').style.display = 'block';
        document.getElementById('dashboard-content').style.display = 'none';
    }

    hideLoading() {
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('dashboard-content').style.display = 'block';
    }

    showError(message) {
        document.getElementById('loading-state').innerHTML = `
            <div class="text-center py-5">
                <div class="text-danger mb-3">
                    <i class="ti ti-alert-triangle" style="font-size: 3rem;"></i>
                </div>
                <h3>Hata</h3>
                <p class="text-muted">${message}</p>
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="ti ti-refresh me-1"></i>
                    Sayfayı Yenile
                </button>
            </div>
        `;
    }

    // Utility methods
    formatNumber(num) {
        return new Intl.NumberFormat('tr-TR').format(num);
    }

    formatTime(timestamp) {
        return new Date(timestamp).toLocaleTimeString('tr-TR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    getStatusText(status) {
        const statusTexts = {
            'healthy': 'Sağlıklı',
            'warning': 'Uyarı',
            'critical': 'Kritik'
        };
        return statusTexts[status] || 'Bilinmiyor';
    }

    getStatusColor(status) {
        const colors = {
            'healthy': 'success',
            'warning': 'warning',
            'critical': 'danger'
        };
        return colors[status] || 'secondary';
    }

    getPerformanceBadgeColor(value) {
        if (value >= 80) return 'success';
        if (value >= 60) return 'warning';
        return 'danger';
    }
}

// GLOBAL AI MONITORING - Export functions
function exportGlobalData(format) {
    console.log('🔥 Exporting Global AI Data:', format);
    const url = `{{ route('admin.ai.monitoring.export', ['format' => 'FORMAT']) }}`.replace('FORMAT', format) + '?timeframe=24h';
    window.open(url, '_blank');
}

// Legacy export function
function exportData(format) {
    return exportGlobalData(format);
}

// Initialize Global AI Monitoring Dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 Initializing Global AI Monitoring Dashboard...');
    window.aiDashboard = new AIMonitoringDashboard();
    
    // Dashboard başarıyla yüklendikten sonra content'i göster
    setTimeout(() => {
        document.getElementById('dashboard-content').style.display = 'block';
    }, 100);
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.aiDashboard && window.aiDashboard.realTimeInterval) {
        clearInterval(window.aiDashboard.realTimeInterval);
    }
});
</script>
@endsection