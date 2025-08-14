/**
 * Analytics Dashboard V3 Professional
 * 
 * Enterprise-level analytics and reporting dashboard with:
 * - Real-time performance metrics visualization
 * - Advanced usage analytics and insights
 * - Cost analysis and optimization recommendations
 * - Quality assessment dashboards
 * - Interactive charts and reports
 * 
 * @version 3.0.0 Professional
 * @since 2025-08-10
 */

class AnalyticsDashboardV3 {
    constructor(container, options = {}) {
        this.container = typeof container === 'string' ? document.querySelector(container) : container;
        this.options = this.mergeOptions(options);
        this.metricsData = new Map();
        this.chartInstances = new Map();
        this.refreshTimers = new Map();
        this.isInitialized = false;
        this.realTimeUpdateInterval = null;
        this.dataCache = new Map();
        
        this.init();
    }

    /**
     * Default configuration options
     */
    getDefaultOptions() {
        return {
            apiEndpoint: '/admin/ai/v3/analytics',
            refreshInterval: 30000, // 30 seconds
            enableRealTimeUpdates: true,
            enableDataExport: true,
            enableAdvancedFiltering: true,
            maxDataPoints: 100,
            defaultTimeRange: '24h',
            theme: 'professional',
            chartLibrary: 'chartjs', // 'chartjs', 'apexcharts', 'd3'
            animations: true,
            debugMode: false,
            dateFormat: 'DD/MM/YYYY HH:mm',
            currencySymbol: '₺',
            percentagePrecision: 2,
            i18n: {
                loading: 'Analitik verileri yükleniyor...',
                dashboardTitle: 'Analitik Dashboard',
                usageTitle: 'Kullanım Analitikleri',
                performanceTitle: 'Performans Metrikleri',
                qualityTitle: 'Kalite Analizi',
                costTitle: 'Maliyet Analizi',
                noData: 'Veri bulunamadı',
                error: 'Hata oluştu',
                export: 'Dışa Aktar',
                refresh: 'Yenile',
                filter: 'Filtrele',
                customize: 'Özelleştir'
            },
            metrics: [
                'usage', 'performance', 'quality', 'costs', 'errors'
            ],
            charts: {
                usage: { type: 'line', height: 300 },
                performance: { type: 'bar', height: 250 },
                quality: { type: 'doughnut', height: 200 },
                costs: { type: 'area', height: 280 }
            },
            colors: {
                primary: '#007bff',
                success: '#28a745',
                warning: '#ffc107',
                danger: '#dc3545',
                info: '#17a2b8',
                gradient: ['#667eea', '#764ba2']
            }
        };
    }

    /**
     * Merge user options with defaults
     */
    mergeOptions(userOptions) {
        const defaults = this.getDefaultOptions();
        return this.deepMerge(defaults, userOptions);
    }

    /**
     * Deep merge objects
     */
    deepMerge(target, source) {
        const result = { ...target };
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        return result;
    }

    /**
     * Initialize the analytics dashboard
     */
    async init() {
        try {
            this.log('Initializing Analytics Dashboard V3');
            
            if (!this.container) {
                throw new Error('Container element not found');
            }

            // Initialize UI structure
            this.initializeUI();
            
            // Load initial analytics data
            await this.loadAnalyticsData();
            
            // Setup event listeners
            this.setupEventListeners();
            
            // Start real-time updates if enabled
            if (this.options.enableRealTimeUpdates) {
                this.startRealTimeUpdates();
            }
            
            this.isInitialized = true;
            this.log('Analytics dashboard initialized successfully');
            this.emit('initialized');
            
        } catch (error) {
            this.handleError('Analytics dashboard initialization failed', error);
            throw error;
        }
    }

    /**
     * Initialize UI structure
     */
    initializeUI() {
        this.container.classList.add('analytics-dashboard-v3', `theme-${this.options.theme}`);
        
        const initialHTML = `
            <div class="ad-header">
                <h3 class="ad-title">
                    <i class="fas fa-chart-bar"></i>
                    ${this.options.i18n.dashboardTitle}
                </h3>
                <div class="ad-controls">
                    <div class="ad-time-selector">
                        <select class="ad-time-range" data-control="time-range">
                            <option value="1h">Son 1 Saat</option>
                            <option value="24h" selected>Son 24 Saat</option>
                            <option value="7d">Son 7 Gün</option>
                            <option value="30d">Son 30 Gün</option>
                            <option value="90d">Son 90 Gün</option>
                        </select>
                    </div>
                    <button class="ad-btn ad-btn-refresh" data-action="refresh">
                        <i class="fas fa-sync-alt"></i>
                        ${this.options.i18n.refresh}
                    </button>
                    <button class="ad-btn ad-btn-export" data-action="export">
                        <i class="fas fa-download"></i>
                        ${this.options.i18n.export}
                    </button>
                    <button class="ad-btn ad-btn-settings" data-action="settings">
                        <i class="fas fa-cog"></i>
                    </button>
                </div>
            </div>
            <div class="ad-content">
                <div class="ad-loading" id="ad-loading">
                    <div class="ad-spinner"></div>
                    <span>${this.options.i18n.loading}</span>
                </div>
            </div>
        `;
        
        this.container.innerHTML = initialHTML;
    }

    /**
     * Load analytics data from API
     */
    async loadAnalyticsData() {
        try {
            const timeRange = this.getCurrentTimeRange();
            
            // Load different types of analytics in parallel
            const [usageData, performanceData, qualityData, costData] = await Promise.all([
                this.loadUsageAnalytics(timeRange),
                this.loadPerformanceMetrics(timeRange),
                this.loadQualityAnalytics(timeRange),
                this.loadCostAnalysis(timeRange)
            ]);

            this.metricsData.set('usage', usageData);
            this.metricsData.set('performance', performanceData);
            this.metricsData.set('quality', qualityData);
            this.metricsData.set('costs', costData);
            
            await this.renderDashboard();
            
        } catch (error) {
            this.handleError('Failed to load analytics data', error);
            throw error;
        }
    }

    /**
     * Load usage analytics
     */
    async loadUsageAnalytics(timeRange) {
        const response = await this.apiCall(`${this.options.apiEndpoint}/usage-analytics`, {
            period: timeRange,
            metrics: ['usage', 'performance', 'quality'],
            granularity: this.getGranularity(timeRange)
        });
        return response.data;
    }

    /**
     * Load performance metrics
     */
    async loadPerformanceMetrics(timeRange) {
        const response = await this.apiCall(`${this.options.apiEndpoint}/performance-metrics`, {
            period: timeRange,
            include_benchmarks: true,
            compare_with_previous: true
        });
        return response.data;
    }

    /**
     * Load quality analytics
     */
    async loadQualityAnalytics(timeRange) {
        const response = await this.apiCall(`${this.options.apiEndpoint}/quality-analytics`, {
            period: timeRange,
            include_trends: true
        });
        return response.data;
    }

    /**
     * Load cost analysis
     */
    async loadCostAnalysis(timeRange) {
        const response = await this.apiCall(`${this.options.apiEndpoint}/cost-analysis`, {
            period: timeRange,
            breakdown_by: 'feature',
            include_projections: true
        });
        return response.data;
    }

    /**
     * Render the complete dashboard
     */
    async renderDashboard() {
        const dashboardHTML = this.generateDashboardHTML();
        
        // Replace loading with dashboard
        const contentContainer = this.container.querySelector('.ad-content');
        contentContainer.innerHTML = dashboardHTML;
        
        // Initialize charts
        await this.initializeCharts();
        
        // Setup interactive elements
        this.setupInteractiveElements();
    }

    /**
     * Generate dashboard HTML
     */
    generateDashboardHTML() {
        return `
            <div class="ad-dashboard-grid">
                <div class="ad-metrics-overview">
                    ${this.generateMetricsOverviewHTML()}
                </div>
                <div class="ad-charts-section">
                    <div class="ad-chart-container" data-chart="usage">
                        <div class="ad-chart-header">
                            <h4>${this.options.i18n.usageTitle}</h4>
                            <div class="ad-chart-controls">
                                <button class="ad-chart-btn" data-action="fullscreen">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ad-chart-content">
                            <canvas id="ad-usage-chart"></canvas>
                        </div>
                    </div>
                    <div class="ad-chart-container" data-chart="performance">
                        <div class="ad-chart-header">
                            <h4>${this.options.i18n.performanceTitle}</h4>
                            <div class="ad-chart-controls">
                                <button class="ad-chart-btn" data-action="fullscreen">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ad-chart-content">
                            <canvas id="ad-performance-chart"></canvas>
                        </div>
                    </div>
                    <div class="ad-chart-container" data-chart="quality">
                        <div class="ad-chart-header">
                            <h4>${this.options.i18n.qualityTitle}</h4>
                            <div class="ad-chart-controls">
                                <button class="ad-chart-btn" data-action="fullscreen">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ad-chart-content">
                            <canvas id="ad-quality-chart"></canvas>
                        </div>
                    </div>
                    <div class="ad-chart-container" data-chart="costs">
                        <div class="ad-chart-header">
                            <h4>${this.options.i18n.costTitle}</h4>
                            <div class="ad-chart-controls">
                                <button class="ad-chart-btn" data-action="fullscreen">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ad-chart-content">
                            <canvas id="ad-cost-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="ad-insights-panel">
                    ${this.generateInsightsPanelHTML()}
                </div>
            </div>
        `;
    }

    /**
     * Generate metrics overview HTML
     */
    generateMetricsOverviewHTML() {
        const usageData = this.metricsData.get('usage') || {};
        const performanceData = this.metricsData.get('performance') || {};
        const qualityData = this.metricsData.get('quality') || {};
        const costData = this.metricsData.get('costs') || {};

        return `
            <div class="ad-metrics-grid">
                <div class="ad-metric-card" data-metric="requests">
                    <div class="ad-metric-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatNumber(usageData.analytics?.total_requests || 0)}</div>
                        <div class="ad-metric-label">Toplam İstek</div>
                        <div class="ad-metric-change ${this.getTrendClass(usageData.analytics?.request_change || 0)}">
                            <i class="fas ${this.getTrendIcon(usageData.analytics?.request_change || 0)}"></i>
                            ${Math.abs(usageData.analytics?.request_change || 0)}%
                        </div>
                    </div>
                </div>
                <div class="ad-metric-card" data-metric="success-rate">
                    <div class="ad-metric-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatPercentage(usageData.analytics?.success_rate || 0)}</div>
                        <div class="ad-metric-label">Başarı Oranı</div>
                        <div class="ad-metric-change ${this.getTrendClass(usageData.analytics?.success_rate_change || 0)}">
                            <i class="fas ${this.getTrendIcon(usageData.analytics?.success_rate_change || 0)}"></i>
                            ${Math.abs(usageData.analytics?.success_rate_change || 0)}%
                        </div>
                    </div>
                </div>
                <div class="ad-metric-card" data-metric="response-time">
                    <div class="ad-metric-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatDuration(performanceData.performance?.avg_response_time || 0)}</div>
                        <div class="ad-metric-label">Ortalama Yanıt Süresi</div>
                        <div class="ad-metric-change ${this.getTrendClass(-(performanceData.performance?.response_time_change || 0))}">
                            <i class="fas ${this.getTrendIcon(-(performanceData.performance?.response_time_change || 0))}"></i>
                            ${Math.abs(performanceData.performance?.response_time_change || 0)}%
                        </div>
                    </div>
                </div>
                <div class="ad-metric-card" data-metric="quality-score">
                    <div class="ad-metric-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatPercentage(qualityData.quality_data?.average_quality || 0)}</div>
                        <div class="ad-metric-label">Ortalama Kalite</div>
                        <div class="ad-metric-change ${this.getTrendClass(qualityData.quality_data?.quality_change || 0)}">
                            <i class="fas ${this.getTrendIcon(qualityData.quality_data?.quality_change || 0)}"></i>
                            ${Math.abs(qualityData.quality_data?.quality_change || 0)}%
                        </div>
                    </div>
                </div>
                <div class="ad-metric-card" data-metric="total-cost">
                    <div class="ad-metric-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatCurrency(costData.cost_analysis?.total_cost || 0)}</div>
                        <div class="ad-metric-label">Toplam Maliyet</div>
                        <div class="ad-metric-change ${this.getTrendClass(-(costData.cost_analysis?.cost_change || 0))}">
                            <i class="fas ${this.getTrendIcon(-(costData.cost_analysis?.cost_change || 0))}"></i>
                            ${Math.abs(costData.cost_analysis?.cost_change || 0)}%
                        </div>
                    </div>
                </div>
                <div class="ad-metric-card" data-metric="error-rate">
                    <div class="ad-metric-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="ad-metric-content">
                        <div class="ad-metric-value">${this.formatPercentage(performanceData.performance?.error_rate || 0)}</div>
                        <div class="ad-metric-label">Hata Oranı</div>
                        <div class="ad-metric-change ${this.getTrendClass(-(performanceData.performance?.error_rate_change || 0))}">
                            <i class="fas ${this.getTrendIcon(-(performanceData.performance?.error_rate_change || 0))}"></i>
                            ${Math.abs(performanceData.performance?.error_rate_change || 0)}%
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Generate insights panel HTML
     */
    generateInsightsPanelHTML() {
        const insights = this.generateInsights();
        
        return `
            <div class="ad-insights-container">
                <div class="ad-insights-header">
                    <h4>
                        <i class="fas fa-lightbulb"></i>
                        Akıllı İçgörüler
                    </h4>
                </div>
                <div class="ad-insights-list">
                    ${insights.map(insight => `
                        <div class="ad-insight-item ${insight.type}">
                            <div class="ad-insight-icon">
                                <i class="fas ${insight.icon}"></i>
                            </div>
                            <div class="ad-insight-content">
                                <div class="ad-insight-title">${insight.title}</div>
                                <div class="ad-insight-description">${insight.description}</div>
                                ${insight.action ? `
                                    <button class="ad-insight-action" data-action="${insight.action.type}" data-params='${JSON.stringify(insight.action.params)}'>
                                        ${insight.action.label}
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    /**
     * Initialize charts
     */
    async initializeCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded, charts will not be displayed');
            return;
        }

        // Initialize usage chart
        await this.createUsageChart();
        
        // Initialize performance chart
        await this.createPerformanceChart();
        
        // Initialize quality chart
        await this.createQualityChart();
        
        // Initialize cost chart
        await this.createCostChart();
    }

    /**
     * Create usage chart
     */
    async createUsageChart() {
        const canvas = document.getElementById('ad-usage-chart');
        if (!canvas) return;

        const usageData = this.metricsData.get('usage') || {};
        const chartData = this.prepareUsageChartData(usageData);

        const chart = new Chart(canvas, {
            type: 'line',
            data: chartData,
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

        this.chartInstances.set('usage', chart);
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Time range selector
        this.container.addEventListener('change', (e) => {
            if (e.target.matches('[data-control="time-range"]')) {
                this.handleTimeRangeChange(e.target.value);
            }
        });

        // Action buttons
        this.container.addEventListener('click', (e) => {
            const action = e.target.closest('[data-action]')?.dataset.action;
            if (action) {
                this.handleAction(action, e);
            }
        });

        // Chart interactions
        this.container.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="fullscreen"]')) {
                this.toggleChartFullscreen(e);
            }
        });
    }

    /**
     * Handle time range change
     */
    async handleTimeRangeChange(timeRange) {
        try {
            this.setLoadingState(true);
            await this.loadAnalyticsData();
            this.updateAllCharts();
        } catch (error) {
            this.handleError('Failed to update time range', error);
        } finally {
            this.setLoadingState(false);
        }
    }

    /**
     * Handle action buttons
     */
    async handleAction(action, e) {
        try {
            switch (action) {
                case 'refresh':
                    await this.refreshData();
                    break;
                case 'export':
                    await this.exportData();
                    break;
                case 'settings':
                    this.showSettings();
                    break;
                default:
                    this.log(`Unknown action: ${action}`);
            }
        } catch (error) {
            this.handleError(`Action ${action} failed`, error);
        }
    }

    /**
     * Update all charts
     */
    updateAllCharts() {
        this.chartInstances.forEach((chart, chartType) => {
            this.updateChart(chartType, chart);
        });
    }

    /**
     * Start real-time updates
     */
    startRealTimeUpdates() {
        if (this.realTimeUpdateInterval) {
            clearInterval(this.realTimeUpdateInterval);
        }

        this.realTimeUpdateInterval = setInterval(async () => {
            try {
                await this.loadAnalyticsData();
                this.updateAllCharts();
                this.updateMetricsOverview();
            } catch (error) {
                this.log('Real-time update failed', error);
            }
        }, this.options.refreshInterval);
    }

    /**
     * Utility methods
     */
    getCurrentTimeRange() {
        const selector = this.container.querySelector('[data-control="time-range"]');
        return selector ? selector.value : this.options.defaultTimeRange;
    }

    getGranularity(timeRange) {
        switch (timeRange) {
            case '1h': return 'minutely';
            case '24h': return 'hourly';
            case '7d': return 'daily';
            case '30d': return 'daily';
            case '90d': return 'weekly';
            default: return 'hourly';
        }
    }

    formatNumber(value) {
        if (value >= 1000000) {
            return (value / 1000000).toFixed(1) + 'M';
        } else if (value >= 1000) {
            return (value / 1000).toFixed(1) + 'K';
        }
        return value.toString();
    }

    formatPercentage(value) {
        return value.toFixed(this.options.percentagePrecision) + '%';
    }

    formatCurrency(value) {
        return this.options.currencySymbol + value.toFixed(2);
    }

    formatDuration(ms) {
        if (ms < 1000) return ms + 'ms';
        if (ms < 60000) return (ms / 1000).toFixed(1) + 's';
        return (ms / 60000).toFixed(1) + 'm';
    }

    getTrendClass(change) {
        if (change > 0) return 'positive';
        if (change < 0) return 'negative';
        return 'neutral';
    }

    getTrendIcon(change) {
        if (change > 0) return 'fa-arrow-up';
        if (change < 0) return 'fa-arrow-down';
        return 'fa-minus';
    }

    generateInsights() {
        // Generate intelligent insights based on data
        const insights = [];
        const performanceData = this.metricsData.get('performance') || {};
        const usageData = this.metricsData.get('usage') || {};
        
        // Example insights - these would be generated based on actual data analysis
        if (performanceData.performance?.avg_response_time > 2000) {
            insights.push({
                type: 'warning',
                icon: 'fa-clock',
                title: 'Yavaş Yanıt Süreleri',
                description: 'Ortalama yanıt süreleri normalin üzerinde. Performans optimizasyonu önerilir.',
                action: {
                    type: 'optimize_performance',
                    params: { metric: 'response_time' },
                    label: 'Optimizasyon Önerilerini Gör'
                }
            });
        }
        
        return insights;
    }

    /**
     * API call helper
     */
    async apiCall(url, data = {}, method = 'GET') {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        };

        if (method !== 'GET') {
            options.body = JSON.stringify(data);
        } else if (Object.keys(data).length > 0) {
            url += '?' + new URLSearchParams(data).toString();
        }

        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`API call failed: ${response.status} ${response.statusText}`);
        }

        return await response.json();
    }

    log(message, data = null) {
        if (this.options.debugMode) {
            console.log(`[AnalyticsDashboardV3] ${message}`, data);
        }
    }

    emit(eventName, data = null) {
        const event = new CustomEvent(`ad:${eventName}`, { detail: data });
        this.container.dispatchEvent(event);
    }

    handleError(message, error) {
        this.log(`Error: ${message}`, error);
        this.emit('error', { message, error });
    }

    setLoadingState(loading) {
        const loadingElement = this.container.querySelector('.ad-loading');
        if (loadingElement) {
            loadingElement.style.display = loading ? 'flex' : 'none';
        }
    }
}

// Global initialization
window.AnalyticsDashboardV3 = AnalyticsDashboardV3;

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-analytics-dashboard]').forEach(container => {
        const options = JSON.parse(container.dataset.analyticsDashboardOptions || '{}');
        new AnalyticsDashboardV3(container, options);
    });
});