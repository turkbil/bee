@props([
    'featureId' => null,
    'timeRange' => '7d',
    'enableRealTime' => true,
    'showAdvancedMetrics' => true,
    'dashboardTheme' => 'professional',
    'autoRefresh' => 30,
])

<div 
    class="analytics-dashboard-v3" 
    data-feature-id="{{ $featureId }}"
    data-time-range="{{ $timeRange }}"
    data-real-time="{{ $enableRealTime ? 'true' : 'false' }}"
    data-auto-refresh="{{ $autoRefresh }}"
>
    <!-- Dashboard Header -->
    <div class="analytics-header">
        <div class="header-content">
            <div class="dashboard-title">
                <h3 class="title">{{ __('ai::admin.analytics_dashboard') }}</h3>
                <div class="last-updated">
                    {{ __('ai::admin.last_updated') }}: <span id="last-update-time">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>
            
            <div class="dashboard-controls">
                <div class="time-range-selector">
                    <label>{{ __('ai::admin.time_range') }}</label>
                    <select id="timeRange" class="form-control">
                        <option value="1h" {{ $timeRange === '1h' ? 'selected' : '' }}>{{ __('ai::admin.last_hour') }}</option>
                        <option value="24h" {{ $timeRange === '24h' ? 'selected' : '' }}>{{ __('ai::admin.last_24_hours') }}</option>
                        <option value="7d" {{ $timeRange === '7d' ? 'selected' : '' }}>{{ __('ai::admin.last_7_days') }}</option>
                        <option value="30d" {{ $timeRange === '30d' ? 'selected' : '' }}>{{ __('ai::admin.last_30_days') }}</option>
                        <option value="90d" {{ $timeRange === '90d' ? 'selected' : '' }}>{{ __('ai::admin.last_90_days') }}</option>
                    </select>
                </div>
                
                <div class="refresh-controls">
                    <button id="manualRefresh" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-sync-alt"></i> {{ __('ai::admin.refresh') }}
                    </button>
                    <div class="auto-refresh-toggle">
                        <input type="checkbox" id="autoRefreshToggle" {{ $enableRealTime ? 'checked' : '' }}>
                        <label for="autoRefreshToggle">{{ __('ai::admin.auto_refresh') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="kpi-section">
        <div class="kpi-grid">
            <div class="kpi-card total-requests">
                <div class="kpi-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="totalRequests">0</div>
                    <div class="kpi-label">{{ __('ai::admin.total_requests') }}</div>
                    <div class="kpi-change positive" id="requestsChange">+0%</div>
                </div>
            </div>
            
            <div class="kpi-card success-rate">
                <div class="kpi-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="successRate">0%</div>
                    <div class="kpi-label">{{ __('ai::admin.success_rate') }}</div>
                    <div class="kpi-change positive" id="successRateChange">+0%</div>
                </div>
            </div>
            
            <div class="kpi-card avg-response-time">
                <div class="kpi-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="avgResponseTime">0ms</div>
                    <div class="kpi-label">{{ __('ai::admin.avg_response_time') }}</div>
                    <div class="kpi-change negative" id="responseTimeChange">+0%</div>
                </div>
            </div>
            
            <div class="kpi-card error-rate">
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="errorRate">0%</div>
                    <div class="kpi-label">{{ __('ai::admin.error_rate') }}</div>
                    <div class="kpi-change negative" id="errorRateChange">+0%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="charts-grid">
            <!-- Request Volume Chart -->
            <div class="chart-card request-volume">
                <div class="chart-header">
                    <h4>{{ __('ai::admin.request_volume') }}</h4>
                    <div class="chart-controls">
                        <select id="volumeMetric" class="form-control form-control-sm">
                            <option value="requests">{{ __('ai::admin.requests') }}</option>
                            <option value="users">{{ __('ai::admin.unique_users') }}</option>
                            <option value="features">{{ __('ai::admin.feature_usage') }}</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="requestVolumeChart"></canvas>
                </div>
            </div>
            
            <!-- Performance Metrics Chart -->
            <div class="chart-card performance-metrics">
                <div class="chart-header">
                    <h4>{{ __('ai::admin.performance_metrics') }}</h4>
                    <div class="chart-legend" id="performanceLegend"></div>
                </div>
                <div class="chart-container">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <!-- Feature Usage Distribution -->
            <div class="chart-card feature-distribution">
                <div class="chart-header">
                    <h4>{{ __('ai::admin.feature_usage_distribution') }}</h4>
                </div>
                <div class="chart-container">
                    <canvas id="featureDistributionChart"></canvas>
                </div>
            </div>
            
            <!-- Error Analysis -->
            <div class="chart-card error-analysis">
                <div class="chart-header">
                    <h4>{{ __('ai::admin.error_analysis') }}</h4>
                    <div class="error-summary" id="errorSummary"></div>
                </div>
                <div class="chart-container">
                    <canvas id="errorAnalysisChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @if($showAdvancedMetrics)
    <!-- Advanced Metrics Section -->
    <div class="advanced-metrics-section">
        <div class="section-header">
            <h4>{{ __('ai::admin.advanced_metrics') }}</h4>
            <button id="toggleAdvanced" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-chevron-down"></i> {{ __('ai::admin.show_advanced') }}
            </button>
        </div>
        
        <div class="advanced-content" id="advancedContent">
            <div class="metrics-grid">
                <!-- User Behavior Analysis -->
                <div class="metric-card user-behavior">
                    <h5>{{ __('ai::admin.user_behavior_analysis') }}</h5>
                    <div class="behavior-metrics">
                        <div class="metric-row">
                            <span>{{ __('ai::admin.session_duration') }}</span>
                            <span id="avgSessionDuration">0m 0s</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.bounce_rate') }}</span>
                            <span id="bounceRate">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.return_users') }}</span>
                            <span id="returnUsers">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.feature_adoption') }}</span>
                            <span id="featureAdoption">0%</span>
                        </div>
                    </div>
                </div>
                
                <!-- System Performance -->
                <div class="metric-card system-performance">
                    <h5>{{ __('ai::admin.system_performance') }}</h5>
                    <div class="performance-metrics">
                        <div class="metric-row">
                            <span>{{ __('ai::admin.cpu_usage') }}</span>
                            <span id="cpuUsage">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.memory_usage') }}</span>
                            <span id="memoryUsage">0 MB</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.cache_hit_rate') }}</span>
                            <span id="cacheHitRate">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.queue_depth') }}</span>
                            <span id="queueDepth">0</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quality Metrics -->
                <div class="metric-card quality-metrics">
                    <h5>{{ __('ai::admin.quality_metrics') }}</h5>
                    <div class="quality-metrics">
                        <div class="metric-row">
                            <span>{{ __('ai::admin.ai_response_quality') }}</span>
                            <span id="responseQuality">0/10</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.user_satisfaction') }}</span>
                            <span id="userSatisfaction">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.completion_rate') }}</span>
                            <span id="completionRate">0%</span>
                        </div>
                        <div class="metric-row">
                            <span>{{ __('ai::admin.retry_rate') }}</span>
                            <span id="retryRate">0%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Alerts and Issues -->
                <div class="metric-card alerts-issues">
                    <h5>{{ __('ai::admin.alerts_issues') }}</h5>
                    <div class="alerts-list" id="alertsList">
                        <div class="no-alerts">{{ __('ai::admin.no_active_alerts') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table Section -->
    <div class="data-table-section">
        <div class="table-header">
            <h4>{{ __('ai::admin.detailed_analytics') }}</h4>
            <div class="table-controls">
                <input type="search" id="tableSearch" placeholder="{{ __('ai::admin.search_analytics') }}" class="form-control form-control-sm">
                <button id="exportData" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-download"></i> {{ __('ai::admin.export') }}
                </button>
            </div>
        </div>
        
        <div class="table-container">
            <table id="analyticsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{ __('ai::admin.timestamp') }}</th>
                        <th>{{ __('ai::admin.feature') }}</th>
                        <th>{{ __('ai::admin.user') }}</th>
                        <th>{{ __('ai::admin.status') }}</th>
                        <th>{{ __('ai::admin.response_time') }}</th>
                        <th>{{ __('ai::admin.input_size') }}</th>
                        <th>{{ __('ai::admin.output_size') }}</th>
                        <th>{{ __('ai::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="analyticsTableBody">
                    <tr class="loading-row">
                        <td colspan="8" class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            {{ __('ai::admin.loading_data') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="table-pagination">
            <nav aria-label="Analytics pagination">
                <ul class="pagination" id="analyticsPagination">
                    <!-- Pagination will be generated by JavaScript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
.analytics-dashboard-v3 {
    padding: 1.5rem;
    background: var(--bs-body-bg);
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.analytics-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--bs-border-color);
}

.header-content {
    display: flex;
    justify-content: between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.dashboard-title .title {
    margin: 0;
    color: var(--bs-dark);
    font-weight: 600;
}

.last-updated {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    margin-top: 0.25rem;
}

.dashboard-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.time-range-selector label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.time-range-selector select {
    min-width: 150px;
}

.refresh-controls {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.auto-refresh-toggle {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.auto-refresh-toggle input[type="checkbox"] {
    margin: 0;
}

.kpi-section {
    margin-bottom: 2rem;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.kpi-card {
    background: var(--bs-white);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.2s ease;
}

.kpi-card:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.kpi-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
}

.total-requests .kpi-icon { background: #007bff; }
.success-rate .kpi-icon { background: #28a745; }
.avg-response-time .kpi-icon { background: #ffc107; }
.error-rate .kpi-icon { background: #dc3545; }

.kpi-content {
    flex: 1;
}

.kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--bs-dark);
    line-height: 1.2;
}

.kpi-label {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    margin: 0.25rem 0;
}

.kpi-change {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.125rem 0.5rem;
    border-radius: 1rem;
}

.kpi-change.positive {
    background: #d4edda;
    color: #155724;
}

.kpi-change.negative {
    background: #f8d7da;
    color: #721c24;
}

.charts-section {
    margin-bottom: 2rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.chart-card {
    background: var(--bs-white);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.chart-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--bs-dark);
}

.chart-controls select {
    min-width: 120px;
}

.chart-container {
    position: relative;
    height: 300px;
}

.chart-container canvas {
    max-width: 100%;
    max-height: 100%;
}

.advanced-metrics-section {
    margin-bottom: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.section-header h4 {
    margin: 0;
    font-weight: 600;
}

.advanced-content {
    display: none;
    margin-top: 1rem;
}

.advanced-content.show {
    display: block;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.metric-card {
    background: var(--bs-white);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.metric-card h5 {
    margin: 0 0 1rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-dark);
    border-bottom: 1px solid var(--bs-border-color);
    padding-bottom: 0.5rem;
}

.metric-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--bs-light);
}

.metric-row:last-child {
    border-bottom: none;
}

.metric-row span:first-child {
    color: var(--bs-secondary);
    font-size: 0.875rem;
}

.metric-row span:last-child {
    font-weight: 600;
    color: var(--bs-dark);
}

.alerts-list {
    max-height: 200px;
    overflow-y: auto;
}

.alert-item {
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    border-radius: 0.25rem;
    border-left: 4px solid;
    font-size: 0.875rem;
}

.alert-item.warning {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}

.alert-item.error {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert-item.info {
    background: #d1ecf1;
    border-color: #17a2b8;
    color: #0c5460;
}

.no-alerts {
    text-align: center;
    color: var(--bs-secondary);
    font-style: italic;
    padding: 2rem 0;
}

.data-table-section {
    background: var(--bs-white);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.5rem;
    padding: 1.5rem;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.table-header h4 {
    margin: 0;
    font-weight: 600;
}

.table-controls {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.table-controls input[type="search"] {
    min-width: 200px;
}

.table-container {
    overflow-x: auto;
    margin-bottom: 1rem;
}

#analyticsTable {
    margin: 0;
    white-space: nowrap;
}

#analyticsTable th {
    background: var(--bs-light);
    font-weight: 600;
    border-top: none;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-success {
    background: #d4edda;
    color: #155724;
}

.status-error {
    background: #f8d7da;
    color: #721c24;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.table-pagination {
    display: flex;
    justify-content: center;
}

.loading-row td {
    padding: 2rem !important;
}

@media (max-width: 768px) {
    .analytics-dashboard-v3 {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .dashboard-controls {
        justify-content: center;
    }
    
    .kpi-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
    
    .metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .table-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .table-controls input[type="search"] {
        min-width: auto;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .analytics-dashboard-v3 {
        background: var(--bs-dark);
        color: var(--bs-light);
    }
    
    .kpi-card,
    .chart-card,
    .metric-card,
    .data-table-section {
        background: var(--bs-gray-800);
        color: var(--bs-light);
    }
    
    .dashboard-title .title,
    .kpi-value,
    .metric-row span:last-child {
        color: var(--bs-light);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Analytics Dashboard V3 Component
    if (typeof AnalyticsDashboardV3 !== 'undefined') {
        const dashboard = new AnalyticsDashboardV3('.analytics-dashboard-v3', {
            featureId: {{ $featureId ?? 'null' }},
            timeRange: '{{ $timeRange }}',
            enableRealTime: {{ $enableRealTime ? 'true' : 'false' }},
            autoRefresh: {{ $autoRefresh }},
            showAdvancedMetrics: {{ $showAdvancedMetrics ? 'true' : 'false' }},
            theme: '{{ $dashboardTheme }}'
        });
        
        // Make dashboard globally accessible
        window.analyticsDashboard = dashboard;
    } else {
        console.warn('AnalyticsDashboardV3 class not found. Please ensure analytics-dashboard-v3.js is loaded.');
    }
    
    // Basic functionality fallback for when JavaScript class is not loaded
    const timeRangeSelect = document.getElementById('timeRange');
    const manualRefresh = document.getElementById('manualRefresh');
    const autoRefreshToggle = document.getElementById('autoRefreshToggle');
    const toggleAdvanced = document.getElementById('toggleAdvanced');
    const advancedContent = document.getElementById('advancedContent');
    
    // Time range change handler
    if (timeRangeSelect) {
        timeRangeSelect.addEventListener('change', function() {
            if (window.analyticsDashboard) {
                window.analyticsDashboard.setTimeRange(this.value);
            }
        });
    }
    
    // Manual refresh handler
    if (manualRefresh) {
        manualRefresh.addEventListener('click', function() {
            if (window.analyticsDashboard) {
                window.analyticsDashboard.refreshData();
            }
            
            // Visual feedback
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
                setTimeout(() => icon.classList.remove('fa-spin'), 1000);
            }
        });
    }
    
    // Auto refresh toggle handler
    if (autoRefreshToggle) {
        autoRefreshToggle.addEventListener('change', function() {
            if (window.analyticsDashboard) {
                window.analyticsDashboard.setAutoRefresh(this.checked);
            }
        });
    }
    
    // Advanced metrics toggle
    if (toggleAdvanced && advancedContent) {
        toggleAdvanced.addEventListener('click', function() {
            const isVisible = advancedContent.classList.contains('show');
            advancedContent.classList.toggle('show');
            
            const icon = this.querySelector('i');
            const text = this.querySelector('span') || this;
            
            if (isVisible) {
                icon.className = 'fas fa-chevron-down';
                text.textContent = '{{ __("ai::admin.show_advanced") }}';
            } else {
                icon.className = 'fas fa-chevron-up';
                text.textContent = '{{ __("ai::admin.hide_advanced") }}';
            }
        });
    }
    
    // Export data handler
    const exportButton = document.getElementById('exportData');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            if (window.analyticsDashboard) {
                window.analyticsDashboard.exportData();
            } else {
                alert('{{ __("ai::admin.export_not_available") }}');
            }
        });
    }
    
    // Table search handler
    const tableSearch = document.getElementById('tableSearch');
    if (tableSearch) {
        let searchTimeout;
        tableSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (window.analyticsDashboard) {
                    window.analyticsDashboard.filterTable(this.value);
                }
            }, 300);
        });
    }
    
    // Update last update time
    function updateLastUpdateTime() {
        const element = document.getElementById('last-update-time');
        if (element) {
            const now = new Date();
            element.textContent = now.toLocaleTimeString();
        }
    }
    
    // Update time every minute
    setInterval(updateLastUpdateTime, 60000);
});
</script>