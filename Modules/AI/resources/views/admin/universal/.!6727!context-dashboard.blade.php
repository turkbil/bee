{{--
    Context Analysis Dashboard - Enterprise AI Intelligence Interface
    UNIVERSAL INPUT SYSTEM V3 - Advanced Context Monitoring & Analytics
    
    Features:
    - Real-time context quality monitoring
    - Multi-dimensional analysis visualization
    - Performance metrics and insights
    - Context recommendation engine
    - Historical trend analysis
    - Alert system for context issues
--}}

@extends('admin.layout')

@section('title', 'Context Analysis Dashboard - AI Module')

@push('styles')
<link rel="stylesheet" href="{{ asset('modules/ai/css/universal-input-system-v3.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js">
<style>
    .context-dashboard-container {
        background: linear-gradient(135deg, #f1f4f7 0%, #e8f0f5 100%);
        min-height: calc(100vh - 180px);
        padding: 1.5rem;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(30, 60, 114, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        opacity: 0.1;
    }
    
    .header-content {
        position: relative;
        z-index: 2;
    }
    
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .metric-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e8f0f5;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--uis-primary) 0%, var(--uis-info) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    
    .metric-card:hover::before {
        opacity: 1;
    }
    
    .metric-value {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    
    .metric-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }
    
    .metric-trend {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .trend-up {
        color: #10b981;
    }
    
    .trend-down {
        color: #ef4444;
    }
    
    .trend-stable {
        color: #8b5cf6;
    }
    
    .main-dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .analysis-panel {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .panel-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .panel-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .panel-body {
        padding: 2rem;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 1rem;
    }
    
    .context-heatmap {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 2rem;
    }
    
    .heatmap-cell {
        aspect-ratio: 1;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .heatmap-cell:hover {
        transform: scale(1.1);
        z-index: 2;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .heatmap-legend {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .legend-scale {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .scale-item {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }
    
    .alert-panel {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .alert-icon {
        width: 24px;
        height: 24px;
        color: #d97706;
        margin-right: 0.75rem;
    }
    
    .recommendations-grid {
        display: grid;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .recommendation-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #3b82f6;
        border-radius: 0.5rem;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }
    
    .recommendation-card:hover {
        background: #f1f5f9;
        border-left-color: #1d4ed8;
        transform: translateX(4px);
    }
    
    .recommendation-priority {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.5rem;
    }
    
    .priority-high {
        background: #fecaca;
        color: #b91c1c;
    }
    
    .priority-medium {
        background: #fed7aa;
        color: #c2410c;
    }
    
    .priority-low {
        background: #bbf7d0;
        color: #166534;
    }
    
    .context-timeline {
        position: relative;
        padding: 1rem 0;
    }
    
    .timeline-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem 0;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 40px;
        bottom: -20px;
        width: 2px;
        background: #e5e7eb;
    }
    
    .timeline-item:last-child::before {
        display: none;
    }
    
    .timeline-marker {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        position: relative;
        z-index: 2;
        flex-shrink: 0;
    }
    
    .timeline-content {
        flex: 1;
        background: #f8fafc;
        border-radius: 0.5rem;
        padding: 1rem;
    }
    
    .timeline-time {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .timeline-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .timeline-description {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.5;
    }
    
    .context-insights {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .insight-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .insight-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .insight-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin: 0 auto 1rem;
    }
    
    .insight-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .insight-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .filter-toolbar {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        white-space: nowrap;
    }
    
    .filter-select {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        background: white;
        min-width: 120px;
    }
    
    .refresh-indicator {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #10b981;
        margin-left: auto;
    }
    
    .refresh-icon {
        animation: spin 2s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .context-score-gauge {
        position: relative;
        width: 200px;
        height: 100px;
        margin: 0 auto 2rem;
    }
    
    .gauge-bg {
        stroke: #e5e7eb;
        stroke-width: 8;
        fill: none;
    }
    
    .gauge-progress {
        stroke: url(#gaugeGradient);
        stroke-width: 8;
        fill: none;
        stroke-linecap: round;
        transition: stroke-dasharray 1s ease-in-out;
    }
    
    .gauge-text {
        text-anchor: middle;
        font-size: 2rem;
        font-weight: 800;
        fill: #1f2937;
    }
    
    .gauge-label {
        text-anchor: middle;
        font-size: 0.875rem;
        font-weight: 500;
        fill: #6b7280;
    }
    
    @media (max-width: 1200px) {
        .main-dashboard-grid {
            grid-template-columns: 1fr;
        }
        
        .metrics-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
    }
    
    @media (max-width: 768px) {
        .context-dashboard-container {
            padding: 1rem;
        }
        
        .dashboard-header {
            padding: 1.5rem;
        }
        
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .main-dashboard-grid {
            gap: 1rem;
        }
        
        .panel-body {
            padding: 1rem;
        }
        
        .filter-toolbar {
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        
        .context-insights {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }
</style>
@endpush

@section('content')
<div class="context-dashboard-container universal-input-system-v3" x-data="contextDashboard()">
    {{-- Dashboard Header --}}
    <div class="dashboard-header">
        <div class="header-content">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2" style="font-size: 2.75rem; font-weight: 800;">
                        <i class="fas fa-brain me-3"></i>Context Intelligence Dashboard
                    </h1>
                    <p class="mb-0 opacity-90" style="font-size: 1.125rem;">
                        Real-time AI context analysis with predictive insights and performance monitoring
                    </p>
                </div>
                <div class="text-end">
                    <div class="d-flex flex-column gap-2">
                        <div class="badge bg-light text-dark px-3 py-2 rounded-pill" style="font-size: 0.875rem; font-weight: 600;">
                            <i class="fas fa-shield-alt me-2 text-success"></i>Enterprise AI V3
                        </div>
                        <div class="small opacity-75">
                            <i class="fas fa-clock me-1"></i>Last updated: <span x-text="lastUpdated"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="filter-toolbar">
        <div class="filter-group">
            <label class="filter-label">Time Range:</label>
            <select class="filter-select" x-model="selectedTimeRange" @change="updateDashboard()">
                <option value="1h">Last Hour</option>
                <option value="24h" selected>Last 24 Hours</option>
                <option value="7d">Last 7 Days</option>
                <option value="30d">Last 30 Days</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">Module:</label>
            <select class="filter-select" x-model="selectedModule" @change="updateDashboard()">
                <option value="all" selected>All Modules</option>
                <option value="page">Page Management</option>
                <option value="portfolio">Portfolio</option>
                <option value="announcement">Announcements</option>
                <option value="user">User Management</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label class="filter-label">Context Type:</label>
            <select class="filter-select" x-model="selectedContextType" @change="updateDashboard()">
                <option value="all" selected>All Types</option>
                <option value="user">User Context</option>
                <option value="content">Content Context</option>
                <option value="temporal">Temporal Context</option>
                <option value="system">System Context</option>
            </select>
        </div>
        
        <div class="refresh-indicator">
            <i class="fas fa-sync-alt refresh-icon" :class="{ 'fa-spin': isRefreshing }"></i>
            <span x-show="isRefreshing">Refreshing...</span>
            <span x-show="!isRefreshing">Live Data</span>
        </div>
    </div>

    {{-- Key Metrics Grid --}}
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-value text-primary" x-text="metrics.averageScore">87</div>
            <div class="metric-label">Average Context Score</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>+12% from last period</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value text-success" x-text="metrics.totalAnalyses">1,247</div>
            <div class="metric-label">Context Analyses</div>
            <div class="metric-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>+18% from last period</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value text-warning" x-text="metrics.alertsCount">3</div>
            <div class="metric-label">Active Alerts</div>
            <div class="metric-trend trend-stable">
                <i class="fas fa-minus"></i>
                <span>No change</span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-value text-info" x-text="metrics.performance + 'ms'">42ms</div>
            <div class="metric-label">Avg Response Time</div>
            <div class="metric-trend trend-down">
                <i class="fas fa-arrow-down"></i>
                <span>-8% faster than target</span>
            </div>
        </div>
    </div>

    {{-- Alert Panel --}}
    <div class="alert-panel" x-show="hasActiveAlerts">
        <div class="d-flex align-items-start">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <div class="flex-grow-1">
                <h4 class="mb-2" style="color: #d97706; font-weight: 700;">Context Quality Alerts</h4>
                <p class="mb-3" style="color: #92400e;">
                    We've detected some context quality issues that require your attention.
                </p>
                <div class="d-flex gap-3">
                    <button class="btn btn-warning btn-sm" @click="viewAlerts()">
                        <i class="fas fa-eye me-1"></i>View Details
                    </button>
                    <button class="btn btn-outline-warning btn-sm" @click="dismissAlerts()">
                        <i class="fas fa-times me-1"></i>Dismiss All
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Grid --}}
    <div class="main-dashboard-grid">
        {{-- Analysis Panel --}}
        <div class="analysis-panel">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fas fa-chart-line text-primary"></i>
                    Context Quality Trends
                </h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" @click="exportChart()">
                        <i class="fas fa-download"></i>Export
                    </button>
                    <button class="btn btn-sm btn-outline-primary" @click="refreshChart()">
                        <i class="fas fa-refresh"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="panel-body">
                {{-- Context Score Gauge --}}
                <div class="context-score-gauge">
                    <svg width="200" height="100" viewBox="0 0 200 100">
                        <defs>
                            <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                                <stop offset="50%" style="stop-color:#10b981;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        
                        {{-- Background arc --}}
                        <path class="gauge-bg" d="M 20 80 A 80 80 0 0 1 180 80"></path>
                        
                        {{-- Progress arc --}}
                        <path class="gauge-progress" 
                              d="M 20 80 A 80 80 0 0 1 180 80"
                              :stroke-dasharray="gaugeProgress"
                              stroke-dashoffset="0"></path>
                        
                        {{-- Score text --}}
                        <text x="100" y="60" class="gauge-text" x-text="metrics.averageScore">87</text>
                        <text x="100" y="80" class="gauge-label">Context Score</text>
                    </svg>
                </div>

                {{-- Chart Container --}}
                <div class="chart-container">
                    <canvas id="contextTrendChart"></canvas>
                </div>

                {{-- Context Heatmap --}}
                <div class="mb-4">
                    <h5 class="mb-3">Weekly Context Quality Heatmap</h5>
                    <div class="context-heatmap" id="contextHeatmap">
                        {{-- Heatmap cells will be generated by JavaScript --}}
                    </div>
                    <div class="heatmap-legend">
                        <span>Lower Quality</span>
                        <div class="legend-scale">
                            <div class="scale-item" style="background: #fee2e2;"></div>
                            <div class="scale-item" style="background: #fed7aa;"></div>
                            <div class="scale-item" style="background: #fef3c7;"></div>
                            <div class="scale-item" style="background: #d1fae5;"></div>
                            <div class="scale-item" style="background: #a7f3d0;"></div>
                        </div>
                        <span>Higher Quality</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Panel --}}
        <div class="analysis-panel">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fas fa-lightbulb text-warning"></i>
                    Smart Recommendations
                </h3>
            </div>
            <div class="panel-body">
                <div class="recommendations-grid">
                    <template x-for="recommendation in recommendations" :key="recommendation.id">
                        <div class="recommendation-card">
                            <div class="recommendation-priority" 
                                 :class="'priority-' + recommendation.priority"
                                 x-text="recommendation.priority"></div>
                            <h5 class="mb-2" x-text="recommendation.title"></h5>
                            <p class="mb-3 small text-muted" x-text="recommendation.description"></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Impact: <strong x-text="recommendation.impact"></strong>
                                </small>
                                <button class="btn btn-sm btn-outline-primary" 
                                        @click="implementRecommendation(recommendation)">
                                    Apply
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Context Timeline --}}
                <div class="mt-4">
                    <h5 class="mb-3">Recent Context Events</h5>
                    <div class="context-timeline">
                        <template x-for="event in recentEvents" :key="event.id">
                            <div class="timeline-item">
                                <div class="timeline-marker" 
                                     :style="'background: ' + event.color">
                                    <i :class="event.icon"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-time" x-text="event.time"></div>
                                    <div class="timeline-title" x-text="event.title"></div>
                                    <div class="timeline-description" x-text="event.description"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Context Insights Grid --}}
    <div class="analysis-panel">
        <div class="panel-header">
            <h3 class="panel-title">
                <i class="fas fa-analytics text-info"></i>
                Advanced Context Insights
            </h3>
        </div>
        <div class="panel-body">
            <div class="context-insights">
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="insight-value" x-text="insights.activeUsers">342</div>
                    <div class="insight-label">Active Context Users</div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="insight-value" x-text="insights.processingSpeed">2.3s</div>
                    <div class="insight-label">Avg Processing Speed</div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-shield-check"></i>
                    </div>
                    <div class="insight-value" x-text="insights.reliability + '%'">99.2</div>
                    <div class="insight-label">System Reliability</div>
                </div>
                
                <div class="insight-card">
                    <div class="insight-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="insight-value" x-text="insights.efficiency + '%'">94.7</div>
                    <div class="insight-label">Context Efficiency</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('modules/ai/js/components/context-engine.js') }}"></script>

<script>
// Context Dashboard Alpine.js Component
function contextDashboard() {
    return {
        selectedTimeRange: '24h',
        selectedModule: 'all',
        selectedContextType: 'all',
        isRefreshing: false,
        hasActiveAlerts: true,
        lastUpdated: new Date().toLocaleTimeString(),
        
        metrics: {
            averageScore: 87,
            totalAnalyses: 1247,
            alertsCount: 3,
            performance: 42
        },
        
        insights: {
            activeUsers: 342,
            processingSpeed: 2.3,
            reliability: 99.2,
            efficiency: 94.7
        },
        
        recommendations: [
            {
                id: 1,
                priority: 'high',
                title: 'Optimize User Context Collection',
                description: 'User context data collection can be improved by 23% with better sampling methods.',
                impact: 'High Performance Gain'
            },
            {
                id: 2,
                priority: 'medium',
                title: 'Enhance Temporal Analysis',
                description: 'Adding time-based context rules could improve prediction accuracy.',
                impact: 'Better Predictions'
            },
            {
                id: 3,
                priority: 'low',
                title: 'Update Context Validation Rules',
                description: 'Some context validation rules are outdated and could be optimized.',
                impact: 'Improved Accuracy'
            }
        ],
        
        recentEvents: [
            {
                id: 1,
                time: '2 minutes ago',
                title: 'High Context Quality Detected',
                description: 'Page module achieved 95% context score',
                icon: 'fas fa-check',
                color: '#10b981'
            },
            {
                id: 2,
                time: '15 minutes ago',
                title: 'Context Analysis Alert',
                description: 'Low quality context detected in user management',
                icon: 'fas fa-exclamation',
                color: '#f59e0b'
            },
            {
                id: 3,
                time: '1 hour ago',
                title: 'System Optimization',
                description: 'Context processing speed improved by 12%',
                icon: 'fas fa-rocket',
                color: '#3b82f6'
            }
        ],
        
        gaugeProgress: '0 251.2', // Will be calculated based on score
        
        init() {
            this.initializeCharts();
            this.generateHeatmap();
            this.updateGauge();
            this.startRealTimeUpdates();
        },
        
        updateDashboard() {
            this.isRefreshing = true;
            
            // Simulate API call
            setTimeout(() => {
                this.refreshData();
                this.isRefreshing = false;
                this.lastUpdated = new Date().toLocaleTimeString();
                this.updateCharts();
            }, 1500);
        },
        
        refreshData() {
            // Simulate data refresh with slight variations
            this.metrics.averageScore = 85 + Math.floor(Math.random() * 10);
            this.metrics.totalAnalyses += Math.floor(Math.random() * 50);
            this.metrics.performance = 35 + Math.floor(Math.random() * 15);
            
            this.updateGauge();
        },
        
        updateGauge() {
            const score = this.metrics.averageScore;
            const circumference = 160; // Approximate arc length
            const progress = (score / 100) * circumference;
            this.gaugeProgress = `${progress} ${circumference}`;
        },
        
        initializeCharts() {
            const ctx = document.getElementById('contextTrendChart').getContext('2d');
            
            window.contextChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.generateTimeLabels(),
                    datasets: [{
                        label: 'Context Quality Score',
                        data: this.generateChartData(),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Performance Score',
                        data: this.generatePerformanceData(),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: 'white',
                            bodyColor: 'white',
                            borderColor: '#3b82f6',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            grid: {
                                color: '#f3f4f6'
                            }
                        },
                        y: {
                            display: true,
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#f3f4f6'
                            }
                        }
                    }
                }
            });
        },
        
        generateHeatmap() {
            const heatmapContainer = document.getElementById('contextHeatmap');
            const colors = ['#fee2e2', '#fed7aa', '#fef3c7', '#d1fae5', '#a7f3d0'];
            
