{{-- AI Debug Dashboard - Main Dashboard --}}
@extends('admin.layout')

@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

@section('title')
🎯 {{ __('ai::admin.priority_debug_dashboard') }}
@endsection

@push('head')
<style>
.priority-badge-1 { background-color: var(--tblr-red-lt); color: var(--tblr-red); }
.priority-badge-2 { background-color: var(--tblr-orange-lt); color: var(--tblr-orange); }
.priority-badge-3 { background-color: var(--tblr-blue-lt); color: var(--tblr-blue); }
.priority-badge-4 { background-color: var(--tblr-gray-100); color: var(--tblr-gray-600); }
.priority-badge-5 { background-color: var(--tblr-gray-50); color: var(--tblr-gray-500); }

.live-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: var(--tblr-green);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.test-section {
    background: linear-gradient(135deg, var(--tblr-blue-lt) 0%, var(--tblr-indigo-lt) 100%);
    border: 1px solid var(--tblr-blue);
    border-radius: 0.75rem;
}

.prompt-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.prompt-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.score-high { color: var(--tblr-green); font-weight: bold; }
.score-medium { color: var(--tblr-yellow); font-weight: 500; }
.score-low { color: var(--tblr-red); }

.dashboard-stat {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.dashboard-stat:hover {
    transform: translateY(-1px);
}
</style>
@endpush

@section('content')
{{-- Header Navigation --}}
<div class="row mb-3">
    <div class="col">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="text-white mb-1">🔍 AI Debug Dashboard</h2>
                        <p class="text-white-50 mb-0">Gerçek zamanlı AI prompt analizi ve tenant analytics</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="live-indicator"></span>
                        <span class="text-white-75 small">Live Monitoring</span>
                        <div class="badge bg-white-10 text-white">
                            Son güncelleme: {{ now()->format('H:i:s') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Navigation --}}
<div class="row mb-3">
    <div class="col">
        <div class="btn-list">
            <a href="{{ route('admin.ai.debug.performance') }}" class="btn btn-outline-success">
                <i class="fas fa-tachometer-alt me-2"></i>📊 Performance Analytics
            </a>
            <a href="{{ route('admin.ai.debug.heatmap') }}" class="btn btn-outline-warning">
                <i class="fas fa-fire me-2"></i>🔥 Prompt Heatmap
            </a>
            <a href="{{ route('admin.ai.debug.errors') }}" class="btn btn-outline-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>⚠️ Error Analysis
            </a>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>Export
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('admin.ai.debug.export', ['type' => 'csv']) }}">
                        <i class="fas fa-file-csv me-2"></i>CSV İndir
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.ai.debug.export', ['type' => 'json']) }}">
                        <i class="fas fa-file-code me-2"></i>JSON İndir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-primary-lt me-3">
                        <i class="fas fa-bolt icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Toplam İstek</div>
                        <div class="h2 mb-0 text-primary">{{ number_format($stats['total_requests'] ?? 0) }}</div>
                        <div class="small text-muted">Son {{ request('date_range', 7) }} günde</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-success-lt me-3">
                        <i class="fas fa-clock icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Ort. Yanıt Süresi</div>
                        <div class="h2 mb-0 text-success">{{ number_format($stats['avg_execution_time'] ?? 0, 1) }}ms</div>
                        <div class="small text-success">
                            <i class="fas fa-arrow-down me-1"></i>Performanslı
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-purple-lt me-3">
                        <i class="fas fa-brain icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Ort. Prompt Kullanımı</div>
                        <div class="h2 mb-0 text-purple">{{ number_format($stats['avg_prompts_used'] ?? 0, 1) }}/12</div>
                        <div class="small text-muted">Prompt efficiency</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-warning-lt me-3">
                        <i class="fas fa-coins icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Token Kullanımı</div>
                        <div class="h2 mb-0 text-warning">{{ number_format($stats['total_tokens'] ?? 0) }}</div>
                        <div class="small text-muted">Total tokens used</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Real-time Prompt Testing --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card test-section">
            <div class="card-header">
                <h3 class="card-title text-primary">
                    <i class="fas fa-vial me-2"></i>
                    🧪 Gerçek Zamanlı Prompt Testi
                </h3>
                <div class="card-actions">
                    <div class="live-indicator me-2"></div>
                    <span class="small text-primary">Live Testing</span>
                </div>
            </div>
            <div class="card-body">
                <form id="promptTestForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Feature Seçin</label>
                            <select name="feature_slug" class="form-select">
                                <option value="">Genel Chat</option>
                                @if(!empty($features))
                                    @foreach($features as $feature)
                                        <option value="{{ $feature->slug }}">{{ $feature->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Context Type</label>
                            <select name="context_type" class="form-select">
                                <option value="minimal">Minimal (8000+)</option>
                                <option value="essential">Essential (7000+)</option>
                                <option value="normal" selected>Normal (4000+)</option>
                                <option value="detailed">Detailed (2000+)</option>
                                <option value="complete">Complete (Tümü)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Test Input</label>
                        <textarea name="input" class="form-control" rows="3" 
                                  placeholder="AI'ya test için göndermek istediğiniz prompt'u yazın..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play me-2"></i>Test Et
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearResults()">
                        <i class="fas fa-broom me-2"></i>Temizle
                    </button>
                </form>
                
                <div id="testResults" class="mt-4" style="display: none;">
                    <h5 class="text-primary">🎯 Test Sonuçları</h5>
                    <div id="testOutput" class="border rounded p-3 bg-white"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie me-2"></i>
                    En Popüler Features
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Feature</th>
                                <th>Kullanım</th>
                                <th>Avg Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($topFeatures))
                                @foreach($topFeatures as $feature)
                                <tr>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ $feature->feature_slug ?: 'chat' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-outline text-green">{{ $feature->usage_count }}</span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ round($feature->avg_time, 1) }}ms</span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Henüz veri bulunmuyor
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activity --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history me-2"></i>
                    Son Aktiviteler
                </h3>
                <div class="card-actions">
                    <button class="btn btn-outline-primary" onclick="loadLiveLogs()">
                        <i class="fas fa-sync-alt me-2"></i>Yenile
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Zaman</th>
                                <th>Tenant</th>
                                <th>Feature</th>
                                <th>Request Type</th>
                                <th class="text-center">Prompts</th>
                                <th class="text-center">Süre</th>
                                <th class="text-center">Durum</th>
                                <th>Input Preview</th>
                            </tr>
                        </thead>
                        <tbody id="liveLogsTable">
                            @if(!empty($recentLogs))
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td class="text-muted small">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-gray-lt">{{ $log->tenant_id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-lt">{{ $log->feature_slug ?: 'chat' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-purple-lt">{{ $log->request_type }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-outline text-primary">
                                            {{ $log->actually_used_prompts }}/{{ $log->total_available_prompts }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-outline text-{{ $log->execution_time_ms > 3000 ? 'danger' : ($log->execution_time_ms > 1500 ? 'warning' : 'success') }}">
                                            {{ round($log->execution_time_ms, 1) }}ms
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($log->has_error)
                                            <span class="badge bg-red-lt">
                                                <i class="fas fa-times me-1"></i>Hata
                                            </span>
                                        @else
                                            <span class="badge bg-green-lt">
                                                <i class="fas fa-check me-1"></i>Başarılı
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ Str::limit($log->input_preview, 50) }}
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Henüz log verisi bulunmuyor
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('promptTestForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultsDiv = document.getElementById('testResults');
    const outputDiv = document.getElementById('testOutput');
    
    // Show loading
    resultsDiv.style.display = 'block';
    outputDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm me-2"></div>
            <span>Test çalışıyor...</span>
        </div>
    `;
    
    try {
        const response = await fetch('{{ route("admin.ai.debug.test-prompt") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                input: formData.get('input'),
                feature_slug: formData.get('feature_slug'),
                context_type: formData.get('context_type')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const analysis = data.analysis;
            outputDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">✅ Test Başarılı</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Feature:</strong> ${analysis.feature}</li>
                            <li><strong>Context Type:</strong> ${analysis.context_type}</li>
                            <li><strong>Threshold:</strong> ${analysis.threshold}</li>
                            <li><strong>Execution Time:</strong> ${analysis.execution_time_ms}ms</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">📊 Prompt İstatistikleri</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Toplam Prompts:</strong> ${analysis.total_components}</li>
                            <li><strong>Kullanılan:</strong> ${analysis.used_components}</li>
                            <li><strong>Filtrelenen:</strong> ${analysis.filtered_components}</li>
                            <li><strong>Efficiency:</strong> ${Math.round((analysis.used_components / analysis.total_components) * 100)}%</li>
                        </ul>
                    </div>
                </div>
                
                <h6 class="mt-3 text-primary">🎯 Kullanılan Prompt'lar</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Prompt</th>
                                <th>Kategori</th>
                                <th>Priority</th>
                                <th>Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${analysis.used_prompts.map(prompt => `
                                <tr>
                                    <td><span class="badge bg-primary-lt">${prompt.position}</span></td>
                                    <td>${prompt.name}</td>
                                    <td><span class="badge priority-badge-${prompt.priority}">${prompt.category_label}</span></td>
                                    <td><span class="badge bg-gray-lt">${prompt.priority_label}</span></td>
                                    <td><span class="score-high">${prompt.final_score}</span></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } else {
            outputDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h4 class="alert-heading">❌ Test Başarısız</h4>
                    <p class="mb-0">${data.error}</p>
                </div>
            `;
        }
    } catch (error) {
        outputDiv.innerHTML = `
            <div class="alert alert-danger">
                <h4 class="alert-heading">⚠️ Bağlantı Hatası</h4>
                <p class="mb-0">Test sırasında bir hata oluştu: ${error.message}</p>
            </div>
        `;
    }
});

function clearResults() {
    document.getElementById('testResults').style.display = 'none';
    document.getElementById('promptTestForm').reset();
}

function loadLiveLogs() {
    // Live logs loading logic
    location.reload();
}

// Auto-refresh every 30 seconds
setInterval(function() {
    loadLiveLogs();
}, 30000);
</script>
@endsection