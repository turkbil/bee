@php
    View::share('pretitle', 'Test 3: Performance & Analytics Dashboard');
@endphp

<div>
    {{-- Page Helper - birebir aynı --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $testName }}
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        {{-- Test modal butonları --}}
                        <button type="button" class="btn btn-primary" onclick="openAnalyticsTranslationModal()">
                            <i class="fa-solid fa-chart-line me-1"></i>
                            Analytics Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content area - Page manage benzeri --}}
    <div class="page-body">
        <div class="container-xl">
            <form method="post">
                <div class="card">
                    
                    {{-- Tab System - Page ile aynı --}}
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-content" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                    <i class="fa-solid fa-file-text me-1"></i>
                                    İçerik
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-seo" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                    <i class="fa-solid fa-search me-1"></i>
                                    SEO
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content">
                            {{-- Content Tab --}}
                            <div class="tab-pane fade show active" id="tab-content" role="tabpanel">
                                {{-- Title Field --}}
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="Test başlığı" value="Performance Analytics Test">
                                            <label>Başlık ★</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" placeholder="test-sayfa-url" value="analytics-test">
                                            <label>URL Slug</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Content Editor Area --}}
                                <div class="mb-3">
                                    <label class="form-label">İçerik ★</label>
                                    <div style="border: 1px solid #dee2e6; border-radius: 0.375rem; min-height: 300px; padding: 15px; background: #f8f9fa;">
                                        <div style="color: #6c757d; text-align: center; margin-top: 120px;">
                                            <i class="fa-solid fa-chart-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="mt-3">Performance & Analytics Dashboard</p>
                                            <p>Real-time metrics ve analytics burada test edilir</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Active Checkbox --}}
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_active" checked />
                                        <div class="state p-success p-on ms-2">
                                            <label>Aktif</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>Pasif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SEO Tab --}}
                            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" placeholder="Meta başlık" value="Analytics Dashboard SEO">
                                    <label>Meta Başlık</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" style="height: 100px;" placeholder="Meta açıklama">Performance analytics ve real-time metrics dashboard</textarea>
                                    <label>Meta Açıklama</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer --}}
                    <div class="card-footer text-end">
                        <div class="d-flex">
                            <button type="button" class="btn btn-link">İptal</button>
                            <button type="button" class="btn btn-primary ms-auto">
                                <i class="fa-solid fa-save me-1"></i>
                                Kaydet
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

{{-- PERFORMANCE & ANALYTICS DASHBOARD MODAL --}}
<div class="modal modal-blur fade" id="analyticsTranslationModal" tabindex="-1" role="dialog" aria-labelledby="analyticsTranslationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            
            {{-- Analytics Header with Real-time Dashboard --}}
            <div class="modal-header bg-dark text-white">
                <div class="d-flex align-items-center w-100">
                    <div class="me-3">
                        <i class="fa-solid fa-tachometer-alt fa-2x" style="color: #00d4aa;"></i>
                    </div>
                    <div class="flex-fill">
                        <h5 class="modal-title mb-0" id="analyticsTranslationModalLabel">
                            Performance Analytics Dashboard v3.0
                        </h5>
                        <small class="text-white-75">Real-time Translation Metrics & Insights</small>
                    </div>
                    <div class="d-flex align-items-center">
                        {{-- Live System Status --}}
                        <div class="me-4">
                            <div class="row text-center" style="min-width: 300px;">
                                <div class="col-3">
                                    <div style="font-size: 14px; font-weight: bold; color: #00d4aa;" id="systemCPU">45%</div>
                                    <small style="font-size: 9px;">CPU</small>
                                </div>
                                <div class="col-3">
                                    <div style="font-size: 14px; font-weight: bold; color: #ffd43b;" id="systemMemory">2.1GB</div>
                                    <small style="font-size: 9px;">RAM</small>
                                </div>
                                <div class="col-3">
                                    <div style="font-size: 14px; font-weight: bold; color: #fd7e14;" id="systemNetwork">125ms</div>
                                    <small style="font-size: 9px;">PING</small>
                                </div>
                                <div class="col-3">
                                    <div style="font-size: 14px; font-weight: bold; color: #e83e8c;" id="systemQueue">7</div>
                                    <small style="font-size: 9px;">QUEUE</small>
                                </div>
                            </div>
                        </div>
                        <div class="badge bg-success me-3" id="systemStatus">Online</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-0">
                {{-- Full Dashboard Layout --}}
                <div class="row g-0 h-100">
                    {{-- Left Sidebar - Controls & Settings --}}
                    <div class="col-md-3 border-end bg-light" style="min-height: 600px;">
                        <div class="p-3">
                            {{-- Translation Controls --}}
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">🚀 Translation Setup</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label small">Source</label>
                                        <select class="form-select form-select-sm">
                                            <option>🇹🇷 Turkish</option>
                                            <option>🇬🇧 English</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Targets</label>
                                        <div class="form-check form-check-sm">
                                            <input class="form-check-input" type="checkbox" checked>
                                            <label class="form-check-label small">🇬🇧 English</label>
                                        </div>
                                        <div class="form-check form-check-sm">
                                            <input class="form-check-input" type="checkbox">
                                            <label class="form-check-label small">🇸🇦 Arabic</label>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="startAnalyticsTranslation()">
                                        <i class="fa-solid fa-play me-1"></i> Start
                                    </button>
                                </div>
                            </div>

                            {{-- Performance Settings --}}
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">⚡ Performance</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label small">Processing Mode</label>
                                        <select class="form-select form-select-sm">
                                            <option>High Performance</option>
                                            <option>Balanced</option>
                                            <option>Quality First</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Chunk Size</label>
                                        <input type="range" class="form-range" min="100" max="2000" value="500" id="chunkSizeSlider">
                                        <small class="text-muted">500 tokens</small>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Concurrent Tasks</label>
                                        <input type="number" class="form-control form-control-sm" value="3" min="1" max="10">
                                    </div>
                                </div>
                            </div>

                            {{-- Analytics Settings --}}
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">📊 Analytics</h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="form-check form-check-sm mb-1">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label small">Real-time Monitoring</label>
                                    </div>
                                    <div class="form-check form-check-sm mb-1">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label small">Performance Metrics</label>
                                    </div>
                                    <div class="form-check form-check-sm mb-1">
                                        <input class="form-check-input" type="checkbox">
                                        <label class="form-check-label small">Cost Tracking</label>
                                    </div>
                                    <div class="form-check form-check-sm">
                                        <input class="form-check-input" type="checkbox" checked>
                                        <label class="form-check-label small">Quality Analysis</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Dashboard Area --}}
                    <div class="col-md-9">
                        <div class="p-3" style="background: #f8f9fa; min-height: 600px;">
                            {{-- Top Metrics Row --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-3 text-center">
                                            <div class="text-muted small mb-1">Processing Speed</div>
                                            <div class="h4 mb-1 text-primary" id="processingSpeed">1,247</div>
                                            <small class="text-success">tokens/min</small>
                                            <div class="mt-1">
                                                <div class="progress" style="height: 3px;">
                                                    <div class="progress-bar bg-primary" id="speedProgress" style="width: 78%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-3 text-center">
                                            <div class="text-muted small mb-1">Quality Score</div>
                                            <div class="h4 mb-1 text-success" id="qualityScore">94.7%</div>
                                            <small class="text-muted">average</small>
                                            <div class="mt-1">
                                                <div class="progress" style="height: 3px;">
                                                    <div class="progress-bar bg-success" id="qualityProgress" style="width: 95%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-3 text-center">
                                            <div class="text-muted small mb-1">Cost Efficiency</div>
                                            <div class="h4 mb-1 text-warning" id="costEfficiency">$0.034</div>
                                            <small class="text-muted">per 1k tokens</small>
                                            <div class="mt-1">
                                                <div class="progress" style="height: 3px;">
                                                    <div class="progress-bar bg-warning" id="costProgress" style="width: 65%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-3 text-center">
                                            <div class="text-muted small mb-1">Success Rate</div>
                                            <div class="h4 mb-1 text-info" id="successRate">99.2%</div>
                                            <small class="text-muted">completed</small>
                                            <div class="mt-1">
                                                <div class="progress" style="height: 3px;">
                                                    <div class="progress-bar bg-info" id="successProgress" style="width: 99%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Live Charts Row --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header py-2 bg-white">
                                            <h6 class="mb-0">📈 Real-time Performance</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="position-relative" style="height: 200px;">
                                                <canvas id="performanceChart" width="100%" height="200"></canvas>
                                                {{-- Simulated Chart Area --}}
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <div class="text-center text-muted">
                                                        <i class="fa-solid fa-chart-line fa-3x mb-2 opacity-25"></i>
                                                        <p class="small">Real-time Performance Chart</p>
                                                        <div class="row text-center">
                                                            <div class="col-4">
                                                                <div class="text-primary h6" id="chartCPU">45%</div>
                                                                <small>CPU</small>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-success h6" id="chartThroughput">1.2k/min</div>
                                                                <small>Throughput</small>
                                                            </div>
                                                            <div class="col-4">
                                                                <div class="text-warning h6" id="chartLatency">125ms</div>
                                                                <small>Latency</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header py-2 bg-white">
                                            <h6 class="mb-0">🎯 Live Translation Progress</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            <div class="position-relative d-inline-block mb-3">
                                                <div class="progress-circle" style="width: 100px; height: 100px; border-radius: 50%; background: conic-gradient(#0d6efd 0deg, #0d6efd calc(var(--progress, 0) * 1deg), #e9ecef calc(var(--progress, 0) * 1deg));" id="circularProgress">
                                                    <div class="position-absolute top-50 start-50 translate-middle">
                                                        <div class="h5 mb-0" id="circularProgressText">0%</div>
                                                        <small class="text-muted">Complete</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <div class="small text-muted mb-1">Current Task</div>
                                                <div class="badge bg-primary" id="currentTask">Idle</div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="h6" id="processedChunks">0</div>
                                                    <small class="text-muted">Chunks</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="h6" id="remainingTime">--</div>
                                                    <small class="text-muted">ETA</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bottom Analytics Row --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header py-2 bg-white">
                                            <h6 class="mb-0">📊 Translation Analytics</h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="table-responsive" style="max-height: 150px;">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="small">Language</th>
                                                            <th class="small">Progress</th>
                                                            <th class="small">Quality</th>
                                                            <th class="small">Speed</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="translationAnalyticsTable">
                                                        <tr>
                                                            <td>🇬🇧 English</td>
                                                            <td><div class="progress" style="height: 4px;"><div class="progress-bar bg-primary" style="width: 0%"></div></div></td>
                                                            <td><span class="badge bg-success">--</span></td>
                                                            <td><small class="text-muted">-- t/min</small></td>
                                                        </tr>
                                                        <tr>
                                                            <td>🇸🇦 Arabic</td>
                                                            <td><div class="progress" style="height: 4px;"><div class="progress-bar bg-info" style="width: 0%"></div></div></td>
                                                            <td><span class="badge bg-success">--</span></td>
                                                            <td><small class="text-muted">-- t/min</small></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header py-2 bg-white">
                                            <h6 class="mb-0">🔍 System Logs</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="bg-dark text-light p-2" style="height: 180px; overflow-y: auto; font-family: 'Courier New', monospace; font-size: 11px;" id="systemLogs">
                                                <div class="text-success">[INFO] Analytics dashboard initialized</div>
                                                <div class="text-info">[DEBUG] Performance monitoring started</div>
                                                <div class="text-warning">[WARN] High memory usage detected</div>
                                                <div class="text-muted">[INFO] System ready for translation tasks</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-dark text-light">
                <div class="w-100">
                    <div class="row align-items-center">
                        <div class="col">
                            <small>
                                <span class="text-success">●</span> Analytics Dashboard Active
                                <span class="ms-3">Last Update: <span id="lastUpdate">--</span></span>
                                <span class="ms-3">Uptime: <span id="systemUptime">00:00:00</span></span>
                            </small>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-light btn-sm" onclick="exportAnalytics()">
                                <i class="fa-solid fa-download me-1"></i>Export
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times me-1"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Dashboard Animations */
.progress-circle {
    background: conic-gradient(#0d6efd 0deg, #0d6efd calc(var(--progress, 0) * 3.6deg), #e9ecef calc(var(--progress, 0) * 3.6deg));
    transition: background 0.3s ease;
}

/* Live Metrics Animation */
.card-body h4, .card-body h5, .card-body h6 {
    animation: metricsUpdate 0.5s ease;
}

@keyframes metricsUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); color: var(--bs-primary); }
    100% { transform: scale(1); }
}

/* System Logs Styling */
#systemLogs {
    scrollbar-width: thin;
    scrollbar-color: #495057 #212529;
}

#systemLogs::-webkit-scrollbar {
    width: 6px;
}

#systemLogs::-webkit-scrollbar-track {
    background: #212529;
}

#systemLogs::-webkit-scrollbar-thumb {
    background: #495057;
    border-radius: 3px;
}

/* Analytics Table Hover Effect */
.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.1);
}

/* Live Status Indicators */
.badge {
    animation: badgePulse 2s infinite;
}

@keyframes badgePulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}
</style>
@endpush

@push('scripts')
<script>
let analyticsInterval;
let uptimeSeconds = 0;
let currentProgress = 0;

function openAnalyticsTranslationModal() {
    console.log('📊 Analytics Dashboard Modal açılıyor...');
    
    // Start all analytics systems
    startLiveMetrics();
    startSystemUptime();
    
    // Modal'ı aç
    const modal = new bootstrap.Modal(document.getElementById('analyticsTranslationModal'));
    modal.show();
}

function startLiveMetrics() {
    analyticsInterval = setInterval(() => {
        updateSystemMetrics();
        updatePerformanceChart();
        updateSystemLogs();
        updateLastUpdateTime();
    }, 2000);
    
    // Stop when modal closes
    document.getElementById('analyticsTranslationModal').addEventListener('hidden.bs.modal', () => {
        if (analyticsInterval) {
            clearInterval(analyticsInterval);
            analyticsInterval = null;
        }
    });
}

function updateSystemMetrics() {
    // System header metrics
    document.getElementById('systemCPU').textContent = (Math.random() * 20 + 40).toFixed(0) + '%';
    document.getElementById('systemMemory').textContent = (Math.random() * 1 + 2).toFixed(1) + 'GB';
    document.getElementById('systemNetwork').textContent = (Math.random() * 50 + 100).toFixed(0) + 'ms';
    document.getElementById('systemQueue').textContent = Math.floor(Math.random() * 5 + 5);
    
    // Main metrics cards
    document.getElementById('processingSpeed').textContent = (Math.random() * 300 + 1000).toFixed(0);
    document.getElementById('qualityScore').textContent = (Math.random() * 5 + 94).toFixed(1) + '%';
    document.getElementById('costEfficiency').textContent = '$' + (Math.random() * 0.01 + 0.03).toFixed(3);
    document.getElementById('successRate').textContent = (Math.random() * 1 + 98.5).toFixed(1) + '%';
    
    // Chart metrics
    document.getElementById('chartCPU').textContent = (Math.random() * 20 + 40).toFixed(0) + '%';
    document.getElementById('chartThroughput').textContent = (Math.random() * 0.3 + 1.0).toFixed(1) + 'k/min';
    document.getElementById('chartLatency').textContent = (Math.random() * 50 + 100).toFixed(0) + 'ms';
}

function updatePerformanceChart() {
    // Update progress bars
    const speedProgress = document.getElementById('speedProgress');
    const qualityProgress = document.getElementById('qualityProgress');
    const costProgress = document.getElementById('costProgress');
    const successProgress = document.getElementById('successProgress');
    
    speedProgress.style.width = (Math.random() * 30 + 60) + '%';
    qualityProgress.style.width = (Math.random() * 10 + 90) + '%';
    costProgress.style.width = (Math.random() * 40 + 50) + '%';
    successProgress.style.width = (Math.random() * 5 + 95) + '%';
}

function updateSystemLogs() {
    const logs = document.getElementById('systemLogs');
    const logMessages = [
        '[INFO] Processing chunk completed successfully',
        '[DEBUG] Memory usage optimized',
        '[INFO] Translation quality check passed',
        '[WARN] High API response time detected',
        '[SUCCESS] Batch translation completed',
        '[INFO] System performance within normal range',
        '[DEBUG] Cache hit ratio: 94.7%'
    ];
    
    const randomLog = logMessages[Math.floor(Math.random() * logMessages.length)];
    const timestamp = new Date().toLocaleTimeString();
    const logClass = randomLog.includes('WARN') ? 'text-warning' : 
                     randomLog.includes('SUCCESS') ? 'text-success' :
                     randomLog.includes('DEBUG') ? 'text-info' : 'text-light';
    
    const newLog = `<div class="${logClass}">[${timestamp}] ${randomLog}</div>`;
    logs.innerHTML += newLog;
    logs.scrollTop = logs.scrollHeight;
    
    // Keep only last 10 logs
    const logLines = logs.children;
    if (logLines.length > 10) {
        logs.removeChild(logLines[0]);
    }
}

function updateLastUpdateTime() {
    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
}

function startSystemUptime() {
    setInterval(() => {
        uptimeSeconds++;
        const hours = Math.floor(uptimeSeconds / 3600);
        const minutes = Math.floor((uptimeSeconds % 3600) / 60);
        const seconds = uptimeSeconds % 60;
        
        document.getElementById('systemUptime').textContent = 
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

function startAnalyticsTranslation() {
    console.log('🚀 Analytics translation başlatılıyor...');
    
    // Update current task
    document.getElementById('currentTask').textContent = 'Processing';
    document.getElementById('currentTask').className = 'badge bg-warning';
    
    // Start progress simulation
    currentProgress = 0;
    const progressInterval = setInterval(() => {
        currentProgress += Math.random() * 5;
        
        if (currentProgress >= 100) {
            currentProgress = 100;
            clearInterval(progressInterval);
            
            // Update completion status
            document.getElementById('currentTask').textContent = 'Completed';
            document.getElementById('currentTask').className = 'badge bg-success';
            document.getElementById('remainingTime').textContent = '0s';
            
            // Success notification
            setTimeout(() => {
                alert('🎉 Analytics-powered translation tamamlandı!');
            }, 1000);
        }
        
        // Update circular progress
        const circularProgress = document.getElementById('circularProgress');
        circularProgress.style.setProperty('--progress', currentProgress);
        document.getElementById('circularProgressText').textContent = Math.floor(currentProgress) + '%';
        
        // Update other progress elements
        document.getElementById('processedChunks').textContent = Math.floor(currentProgress / 10);
        document.getElementById('remainingTime').textContent = Math.floor((100 - currentProgress) / 5) + 's';
        
        // Update translation table
        const tableRows = document.querySelectorAll('#translationAnalyticsTable tr');
        tableRows.forEach((row, index) => {
            const progressBar = row.querySelector('.progress-bar');
            const qualityBadge = row.querySelector('.badge');
            const speedText = row.querySelector('small');
            
            progressBar.style.width = Math.min(currentProgress + Math.random() * 10, 100) + '%';
            qualityBadge.textContent = (Math.random() * 5 + 90).toFixed(1) + '%';
            speedText.textContent = (Math.random() * 100 + 200).toFixed(0) + ' t/min';
        });
        
    }, 500);
}

function exportAnalytics() {
    console.log('📥 Exporting analytics data...');
    
    // Simulate export
    const exportBtn = event.target;
    const originalText = exportBtn.innerHTML;
    
    exportBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Exporting...';
    exportBtn.disabled = true;
    
    setTimeout(() => {
        exportBtn.innerHTML = originalText;
        exportBtn.disabled = false;
        alert('📊 Analytics data exported successfully!');
    }, 2000);
}

// Chunk size slider interaction
document.addEventListener('DOMContentLoaded', function() {
    const chunkSlider = document.getElementById('chunkSizeSlider');
    if (chunkSlider) {
        chunkSlider.addEventListener('input', function() {
            const value = this.value;
            this.nextElementSibling.textContent = value + ' tokens';
        });
    }
});
</script>
@endpush