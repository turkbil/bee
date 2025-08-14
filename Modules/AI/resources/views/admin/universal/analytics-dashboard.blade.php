@extends('admin.layout')

@section('title', 'AI Analytics Dashboard - Universal Input System V3')

@section('content')
<div class="container-fluid py-4" x-data="analyticsManager()">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">AI Analytics Dashboard</h1>
                    <p class="text-muted">Advanced analytics with machine learning insights and predictive behavior modeling</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-control" style="width: auto;" x-model="selectedTimeRange" @change="loadAnalyticsData()">
                        <option value="24h">Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="1y">Last Year</option>
                    </select>
                    <button class="btn btn-outline-primary" @click="refreshData()" :disabled="loading">
                        <i class="fas fa-sync-alt" :class="{'fa-spin': loading}"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" @click="exportReport()">
                        <i class="fas fa-download"></i>
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total AI Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="kpis.total_requests?.toLocaleString() || '0'"></div>
                            <div class="text-xs text-muted" x-show="kpis.requests_change">
                                <i :class="kpis.requests_change > 0 ? 'fas fa-arrow-up text-success' : 'fas fa-arrow-down text-danger'"></i>
                                <span x-text="Math.abs(kpis.requests_change) + '% from last period'"></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-brain fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Success Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="(kpis.success_rate || 0) + '%'"></div>
                            <div class="text-xs text-muted" x-show="kpis.success_rate_change">
                                <i :class="kpis.success_rate_change > 0 ? 'fas fa-arrow-up text-success' : 'fas fa-arrow-down text-danger'"></i>
                                <span x-text="Math.abs(kpis.success_rate_change) + 'pp change'"></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Response Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="(kpis.avg_response_time || 0) + 's'"></div>
                            <div class="text-xs text-muted" x-show="kpis.response_time_change">
                                <i :class="kpis.response_time_change < 0 ? 'fas fa-arrow-down text-success' : 'fas fa-arrow-up text-danger'"></i>
                                <span x-text="Math.abs(kpis.response_time_change) + '% from last period'"></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="kpis.active_users?.toLocaleString() || '0'"></div>
                            <div class="text-xs text-muted" x-show="kpis.active_users_change">
                                <i :class="kpis.active_users_change > 0 ? 'fas fa-arrow-up text-success' : 'fas fa-arrow-down text-danger'"></i>
                                <span x-text="Math.abs(kpis.active_users_change) + '% from last period'"></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Usage Trends & Patterns</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" @click="toggleChartType('requests')">Requests Over Time</a>
                            <a class="dropdown-item" href="#" @click="toggleChartType('response_times')">Response Times</a>
                            <a class="dropdown-item" href="#" @click="toggleChartType('success_rates')">Success Rates</a>
                            <a class="dropdown-item" href="#" @click="toggleChartType('user_activity')">User Activity</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="mainAnalyticsChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Feature Usage Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="featureUsageChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Content Generation
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Translation
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> SEO Tools
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Analytics Row -->
    <div class="row mb-4">
        <!-- User Behavior Heatmap -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Activity Heatmap</h6>
                </div>
                <div class="card-body">
                    <div class="heatmap-container" style="height: 300px; position: relative;">
                        <div class="heatmap-labels">
                            <div class="time-labels">
                                <template x-for="hour in Array.from({length: 24}, (_, i) => i)" :key="hour">
                                    <div class="time-label" x-text="hour + ':00'"></div>
                                </template>
                            </div>
                            <div class="day-labels">
                                <div class="day-label">Mon</div>
                                <div class="day-label">Tue</div>
                                <div class="day-label">Wed</div>
                                <div class="day-label">Thu</div>
                                <div class="day-label">Fri</div>
                                <div class="day-label">Sat</div>
                                <div class="day-label">Sun</div>
                            </div>
                        </div>
                        <div class="heatmap-grid">
                            <template x-for="(dayData, dayIndex) in heatmapData" :key="dayIndex">
                                <div class="heatmap-row">
                                    <template x-for="(hourData, hourIndex) in dayData" :key="hourIndex">
                                        <div class="heatmap-cell" 
                                             :style="`background-color: ${getHeatmapColor(hourData.intensity)}`"
                                             :title="`${getDayName(dayIndex)} ${hourIndex}:00 - ${hourData.requests} requests`">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Metrics</h6>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Machine Learning Insights -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Machine Learning Insights & Predictions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-900 mb-3">Usage Predictions</h6>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-sm">Next 24h Requests</span>
                                    <span class="text-sm font-weight-bold" x-text="mlInsights.predicted_requests_24h?.toLocaleString()"></span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" :style="`width: ${mlInsights.prediction_confidence || 0}%`"></div>
                                </div>
                                <small class="text-muted" x-text="`${mlInsights.prediction_confidence || 0}% confidence`"></small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-sm">Peak Usage Time</span>
                                    <span class="text-sm font-weight-bold" x-text="mlInsights.peak_time_prediction"></span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" :style="`width: ${mlInsights.peak_confidence || 0}%`"></div>
                                </div>
                                <small class="text-muted" x-text="`${mlInsights.peak_confidence || 0}% confidence`"></small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-sm">Expected Load</span>
                                    <span class="text-sm font-weight-bold" x-text="mlInsights.expected_load"></span>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" :style="`width: ${getLoadPercentage(mlInsights.expected_load)}%`"
                                         :class="getLoadColor(mlInsights.expected_load)"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="font-weight-bold text-gray-900 mb-3">Behavioral Patterns</h6>
                            <div class="mb-3" x-show="mlInsights.user_patterns">
                                <template x-for="pattern in mlInsights.user_patterns" :key="pattern.type">
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-sm" x-text="pattern.description"></span>
                                            <span class="badge" :class="getPatternBadge(pattern.significance)" 
                                                  x-text="pattern.significance"></span>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar" :class="getPatternColor(pattern.significance)" 
                                                 :style="`width: ${pattern.strength}%`"></div>
                                        </div>
                                        <small class="text-muted" x-text="pattern.impact"></small>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- AI Recommendations -->
                    <div class="mt-4 pt-4 border-top">
                        <h6 class="font-weight-bold text-gray-900 mb-3">AI Recommendations</h6>
                        <div class="row">
                            <template x-for="recommendation in mlInsights.recommendations" :key="recommendation.id">
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h6 class="card-title h6 mb-0" x-text="recommendation.title"></h6>
                                                <span class="badge" :class="getPriorityColor(recommendation.priority)" 
                                                      x-text="recommendation.priority"></span>
                                            </div>
                                            <p class="card-text text-sm text-muted" x-text="recommendation.description"></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted" x-text="`Impact: ${recommendation.impact}`"></small>
                                                <button class="btn btn-sm btn-outline-primary" @click="implementRecommendation(recommendation)">
                                                    Apply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-time Alerts & Anomalies -->
        <div class="col-xl-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Real-time Alerts & Anomalies</h6>
                </div>
                <div class="card-body">
                    <div class="alerts-container" style="max-height: 400px; overflow-y: auto;">
                        <template x-for="alert in alerts" :key="alert.id">
                            <div class="alert" :class="getAlertClass(alert.severity)" role="alert">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong x-text="alert.title"></strong>
                                        <p class="mb-1 text-sm" x-text="alert.message"></p>
                                        <small class="text-muted" x-text="formatTime(alert.timestamp)"></small>
                                    </div>
                                    <button type="button" class="close" @click="dismissAlert(alert.id)">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="alerts.length === 0" class="text-center py-4">
                            <i class="fas fa-shield-alt fa-3x text-gray-300 mb-3"></i>
                            <h6 class="text-gray-600">All Systems Normal</h6>
                            <p class="text-gray-500 text-sm">No anomalies or alerts detected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Tables -->
    <div class="row mb-4">
        <!-- Top Performing Features -->
        <div class="col-xl-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Performing AI Features</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Usage</th>
                                    <th>Success Rate</th>
                                    <th>Avg Time</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="feature in topFeatures" :key="feature.id">
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="icon-circle bg-primary">
                                                        <i :class="getFeatureIcon(feature.type)"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold text-sm" x-text="feature.name"></div>
                                                    <div class="text-xs text-muted" x-text="feature.category"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="font-weight-bold" x-text="feature.usage_count.toLocaleString()"></div>
                                            <div class="text-xs text-muted">requests</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="font-weight-bold" x-text="feature.success_rate + '%'"></div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success" :style="`width: ${feature.success_rate}%`"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="font-weight-bold" x-text="feature.avg_response_time + 's'"></div>
                                        </td>
                                        <td class="text-center">
                                            <i :class="feature.trend > 0 ? 'fas fa-arrow-up text-success' : feature.trend < 0 ? 'fas fa-arrow-down text-danger' : 'fas fa-minus text-muted'"></i>
                                            <span class="text-sm" x-text="Math.abs(feature.trend) + '%'"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Engagement Metrics -->
        <div class="col-xl-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Engagement Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="border-left-primary pl-3">
                                <div class="font-weight-bold text-primary h5" x-text="userMetrics.daily_active_users?.toLocaleString() || '0'"></div>
                                <div class="text-muted small">Daily Active Users</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-left-success pl-3">
                                <div class="font-weight-bold text-success h5" x-text="userMetrics.avg_session_duration || '0m'"></div>
                                <div class="text-muted small">Avg Session Duration</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-left-info pl-3">
                                <div class="font-weight-bold text-info h5" x-text="userMetrics.retention_rate || '0%'"></div>
                                <div class="text-muted small">7-Day Retention</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-gray-900 mb-3">Engagement Breakdown</h6>
                    <template x-for="segment in userMetrics.engagement_segments" :key="segment.name">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-sm" x-text="segment.name"></span>
                                <span class="text-sm font-weight-bold" x-text="segment.percentage + '%'"></span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar" :class="getEngagementColor(segment.name)" 
                                     :style="`width: ${segment.percentage}%`"></div>
                            </div>
                            <small class="text-muted" x-text="segment.users.toLocaleString() + ' users'"></small>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" :class="{'show d-block': showExportModal}" tabindex="-1" x-show="showExportModal" 
         style="background-color: rgba(0,0,0,0.5);" @click.self="showExportModal = false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Analytics Report</h5>
                    <button type="button" class="close" @click="showExportModal = false">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Report Type</label>
                        <select class="form-control" x-model="exportOptions.type">
                            <option value="summary">Executive Summary</option>
                            <option value="detailed">Detailed Analytics</option>
                            <option value="performance">Performance Report</option>
                            <option value="ml_insights">ML Insights Report</option>
                            <option value="user_behavior">User Behavior Analysis</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Format</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" x-model="exportOptions.format" value="pdf" id="formatPdf">
                            <label class="form-check-label" for="formatPdf">PDF Report</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" x-model="exportOptions.format" value="excel" id="formatExcel">
                            <label class="form-check-label" for="formatExcel">Excel Spreadsheet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" x-model="exportOptions.format" value="csv" id="formatCsv">
                            <label class="form-check-label" for="formatCsv">CSV Data</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" x-model="exportOptions.includeCharts" id="includeCharts">
                            <label class="form-check-label" for="includeCharts">Include charts and visualizations</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" x-model="exportOptions.includePredictions" id="includePredictions">
                            <label class="form-check-label" for="includePredictions">Include ML predictions</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" x-model="exportOptions.includeRecommendations" id="includeRecommendations">
                            <label class="form-check-label" for="includeRecommendations">Include AI recommendations</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showExportModal = false">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="generateReport()" :disabled="exportLoading">
                        <span x-show="exportLoading">
                            <i class="fas fa-spinner fa-spin"></i>
                            Generating...
                        </span>
                        <span x-show="!exportLoading">
                            <i class="fas fa-download"></i>
                            Generate Report
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.heatmap-container {
    font-size: 11px;
}

.heatmap-labels {
    display: flex;
    margin-bottom: 10px;
}

.time-labels {
    display: flex;
    flex-direction: column;
    margin-right: 10px;
    width: 40px;
}

.time-label {
    height: 20px;
    display: flex;
    align-items: center;
    font-size: 9px;
    color: #666;
}

.day-labels {
    display: flex;
    width: 100%;
}

.day-label {
    flex: 1;
    text-align: center;
    font-weight: bold;
    color: #666;
    margin-bottom: 5px;
}

.heatmap-grid {
    display: flex;
    flex-direction: column;
}

.heatmap-row {
    display: flex;
    margin-bottom: 2px;
}

.heatmap-cell {
    flex: 1;
    height: 15px;
    margin-right: 2px;
    border-radius: 2px;
    cursor: pointer;
    transition: opacity 0.2s;
}

.heatmap-cell:hover {
    opacity: 0.8;
}

.progress-sm {
    height: 0.375rem;
}

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.chart-area {
    position: relative;
    height: 320px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert {
    animation: slideInUp 0.3s ease-out;
}
</style>

<script>
function analyticsManager() {
    return {
        // Data properties
        selectedTimeRange: '7d',
        loading: false,
        exportLoading: false,
        showExportModal: false,

        // KPI Data
        kpis: {
            total_requests: 0,
            requests_change: 0,
            success_rate: 0,
            success_rate_change: 0,
            avg_response_time: 0,
            response_time_change: 0,
            active_users: 0,
            active_users_change: 0
        },

        // Charts
        mainChart: null,
        featureChart: null,
        performanceChart: null,
        currentChartType: 'requests',

        // Heatmap data
        heatmapData: [],

        // ML Insights
        mlInsights: {
            predicted_requests_24h: 0,
            prediction_confidence: 0,
            peak_time_prediction: '',
            peak_confidence: 0,
            expected_load: 'Medium',
            user_patterns: [],
            recommendations: []
        },

        // Alerts
        alerts: [],

        // Analytics data
        topFeatures: [],
        userMetrics: {
            daily_active_users: 0,
            avg_session_duration: '0m',
            retention_rate: '0%',
            engagement_segments: []
        },

        // Export options
        exportOptions: {
            type: 'summary',
            format: 'pdf',
            includeCharts: true,
            includePredictions: true,
            includeRecommendations: true
        },

        // Initialize component
        init() {
            this.loadAnalyticsData();
            this.initializeCharts();
            this.startRealTimeUpdates();
            this.loadHeatmapData();
            this.loadMLInsights();
            this.loadAlerts();
        },

        // Load main analytics data
        async loadAnalyticsData() {
            this.loading = true;
            try {
                const response = await fetch('/admin/ai/analytics/dashboard-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        timeRange: this.selectedTimeRange
                    })
                });

                const data = await response.json();
                this.kpis = data.kpis;
                this.topFeatures = data.top_features;
                this.userMetrics = data.user_metrics;
                this.updateCharts(data.chart_data);
            } catch (error) {
                console.error('Error loading analytics data:', error);
                this.showNotification('Error loading analytics data', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Load heatmap data
        async loadHeatmapData() {
            try {
                const response = await fetch('/admin/ai/analytics/heatmap-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        timeRange: this.selectedTimeRange
                    })
                });

                const data = await response.json();
                this.heatmapData = data.heatmap_data;
            } catch (error) {
                console.error('Error loading heatmap data:', error);
            }
        },

        // Load ML insights
        async loadMLInsights() {
            try {
                const response = await fetch('/admin/ai/analytics/ml-insights');
                const data = await response.json();
                this.mlInsights = data.insights;
            } catch (error) {
                console.error('Error loading ML insights:', error);
            }
        },

        // Load alerts
        async loadAlerts() {
            try {
                const response = await fetch('/admin/ai/analytics/alerts');
                const data = await response.json();
                this.alerts = data.alerts;
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        },

        // Initialize charts
        initializeCharts() {
            this.$nextTick(() => {
                this.initMainChart();
                this.initFeatureChart();
                this.initPerformanceChart();
            });
        },

        // Initialize main analytics chart
        initMainChart() {
            const ctx = document.getElementById('mainAnalyticsChart');
            if (!ctx) return;

            this.mainChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'AI Requests',
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: []
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        x: {
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        },
                        y: {
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            titleMarginBottom: 10,
                            titleColor: '#6e707e',
                            titleFontSize: 14,
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10
                        }
                    }
                }
            });
        },

        // Initialize feature usage chart
        initFeatureChart() {
            const ctx = document.getElementById('featureUsageChart');
            if (!ctx) return;

            this.featureChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Content Generation", "Translation", "SEO Tools", "Social Media", "Email Marketing"],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#c0392b'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)"
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            caretPadding: 10
                        },
                        legend: {
                            display: false
                        }
                    },
                    cutout: '80%'
                }
            });
        },

        // Initialize performance chart
        initPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (!ctx) return;

            this.performanceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Response Time', 'Success Rate', 'Throughput', 'Error Rate'],
                    datasets: [{
                        label: 'Current',
                        data: [2.3, 96.5, 450, 3.5],
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#e74a3b']
                    }, {
                        label: 'Target',
                        data: [2.0, 98.0, 500, 2.0],
                        backgroundColor: ['rgba(78, 115, 223, 0.3)', 'rgba(28, 200, 138, 0.3)', 'rgba(54, 185, 204, 0.3)', 'rgba(231, 74, 59, 0.3)']
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },

        // Update charts with new data
        updateCharts(chartData) {
            if (this.mainChart && chartData) {
                this.mainChart.data.labels = chartData.labels;
                this.mainChart.data.datasets[0].data = chartData.requests;
                this.mainChart.update();
            }

            if (this.featureChart && chartData?.feature_usage) {
                this.featureChart.data.datasets[0].data = chartData.feature_usage;
                this.featureChart.update();
            }
        },

        // Toggle chart type
        toggleChartType(type) {
            this.currentChartType = type;
            // Reload data for the new chart type
            this.loadAnalyticsData();
        },

        // Refresh all data
        async refreshData() {
            await Promise.all([
                this.loadAnalyticsData(),
                this.loadHeatmapData(),
                this.loadMLInsights(),
                this.loadAlerts()
            ]);
        },

        // Export functionality
        exportReport() {
            this.showExportModal = true;
        },

        async generateReport() {
            this.exportLoading = true;
            try {
                const response = await fetch('/admin/ai/analytics/export-report', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        timeRange: this.selectedTimeRange,
                        ...this.exportOptions
                    })
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `analytics-report-${this.selectedTimeRange}.${this.exportOptions.format}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    this.showExportModal = false;
                    this.showNotification('Report exported successfully', 'success');
                } else {
                    this.showNotification('Error exporting report', 'error');
                }
            } catch (error) {
                console.error('Error generating report:', error);
                this.showNotification('Error generating report', 'error');
            } finally {
                this.exportLoading = false;
            }
        },

        // ML Insights actions
        async implementRecommendation(recommendation) {
            try {
                const response = await fetch('/admin/ai/analytics/implement-recommendation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        recommendation_id: recommendation.id
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Recommendation implemented successfully', 'success');
                    this.loadMLInsights(); // Refresh recommendations
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error implementing recommendation:', error);
                this.showNotification('Error implementing recommendation', 'error');
            }
        },

        // Alert management
        dismissAlert(alertId) {
            this.alerts = this.alerts.filter(alert => alert.id !== alertId);
        },

        // Real-time updates
        startRealTimeUpdates() {
            setInterval(() => {
                this.loadAlerts();
                // Update KPIs more frequently than full data refresh
                this.updateRealTimeKPIs();
            }, 30000); // Every 30 seconds

            // Full data refresh less frequently
            setInterval(() => {
                this.loadAnalyticsData();
                this.loadHeatmapData();
            }, 300000); // Every 5 minutes
        },

        async updateRealTimeKPIs() {
            try {
                const response = await fetch('/admin/ai/analytics/real-time-kpis');
                const data = await response.json();
                this.kpis = { ...this.kpis, ...data.kpis };
            } catch (error) {
                console.error('Error updating real-time KPIs:', error);
            }
        },

        // Helper functions for styling and formatting
        getHeatmapColor(intensity) {
            const colors = [
                '#ebedf0', // 0% intensity
                '#c6e48b', // 25% intensity
                '#7bc96f', // 50% intensity
                '#239a3b', // 75% intensity
                '#196127'  // 100% intensity
            ];
            const index = Math.min(Math.floor(intensity / 20), 4);
            return colors[index];
        },

        getDayName(dayIndex) {
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            return days[dayIndex];
        },

        getLoadPercentage(load) {
            const loadMap = { 'Low': 25, 'Medium': 50, 'High': 75, 'Critical': 100 };
            return loadMap[load] || 50;
        },

        getLoadColor(load) {
            const colorMap = {
                'Low': 'bg-success',
                'Medium': 'bg-info', 
                'High': 'bg-warning',
                'Critical': 'bg-danger'
            };
            return colorMap[load] || 'bg-info';
        },

        getPatternBadge(significance) {
            const badgeMap = {
                'High': 'badge-danger',
                'Medium': 'badge-warning',
                'Low': 'badge-info'
            };
            return badgeMap[significance] || 'badge-secondary';
        },

        getPatternColor(significance) {
            const colorMap = {
                'High': 'bg-danger',
                'Medium': 'bg-warning',
                'Low': 'bg-info'
            };
            return colorMap[significance] || 'bg-secondary';
        },

        getPriorityColor(priority) {
            const colorMap = {
                'High': 'badge-danger',
                'Medium': 'badge-warning',
                'Low': 'badge-info'
            };
            return colorMap[priority] || 'badge-secondary';
        },

        getAlertClass(severity) {
            const classMap = {
                'critical': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info',
                'success': 'alert-success'
            };
            return classMap[severity] || 'alert-info';
        },

        getFeatureIcon(type) {
            const iconMap = {
                'content_generation': 'fas fa-pen-nib',
                'translation': 'fas fa-language',
                'seo_optimization': 'fas fa-search',
                'social_media': 'fas fa-share-alt',
                'email_marketing': 'fas fa-envelope'
            };
            return iconMap[type] || 'fas fa-cog';
        },

        getEngagementColor(segment) {
            const colorMap = {
                'Power Users': 'bg-success',
                'Regular Users': 'bg-primary',
                'Occasional Users': 'bg-info',
                'New Users': 'bg-warning',
                'Inactive Users': 'bg-secondary'
            };
            return colorMap[segment] || 'bg-secondary';
        },

        // Utility functions
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString();
        },

        showNotification(message, type = 'info') {
            // Integration with notification system
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    };
}
</script>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>