@extends('admin.layout')

@section('title', 'Çeviri Sistemi Debug')
@section('pretitle', 'Sistem Tanılama')

@push('head')
<style>
    .debug-card {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        margin-bottom: 1rem;
    }
    .debug-card.error {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
    .debug-card.success {
        border-left-color: #28a745;
        background: #f0fff4;
    }
    .debug-card.warning {
        border-left-color: #ffc107;
        background: #fffbf0;
    }
    .log-entry {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        background: #1e1e1e;
        color: #f8f8f2;
        padding: 8px;
        border-radius: 4px;
        margin-bottom: 5px;
        word-wrap: break-word;
    }
    .log-entry.error { border-left: 3px solid #ff6b6b; }
    .log-entry.warning { border-left: 3px solid #ffc947; }
    .log-entry.info { border-left: 3px solid #4ecdc4; }
    .log-entry.debug { border-left: 3px solid #a8e6cf; }
    
    .status-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }
    .status-active { background: #28a745; color: white; }
    .status-inactive { background: #6c757d; color: white; }
    .status-hidden { background: #ffc107; color: black; }
    
    .test-panel {
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .real-time-logs {
        height: 400px;
        overflow-y: auto;
        background: #1e1e1e;
        color: #f8f8f2;
        padding: 10px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
    }
    
    .json-viewer {
        background: #2d3748;
        color: #e2e8f0;
        padding: 12px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        max-height: 300px;
        overflow-y: auto;
    }
    
    .metric-card {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .metric-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    
    .metric-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .action-button {
        margin: 5px;
        border-radius: 20px;
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bug me-2"></i>
                        Çeviri Sistemi Debug Paneli
                    </h3>
                    <div class="card-actions">
                        <button type="button" class="btn btn-sm btn-danger action-button" onclick="clearLogs()">
                            <i class="fas fa-trash"></i> Logları Temizle
                        </button>
                        <button type="button" class="btn btn-sm btn-warning action-button" onclick="clearCache()">
                            <i class="fas fa-broom"></i> Cache Temizle
                        </button>
                        <button type="button" class="btn btn-sm btn-primary action-button" onclick="refreshData()">
                            <i class="fas fa-sync"></i> Yenile
                        </button>
                    </div>
                </div>
                
                <!-- Sistem Metrikleri -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-number">{{ count($debugData['language_system']['active_languages'] ?? []) }}</div>
                                <div class="metric-label">Aktif Dil</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-number">{{ count($debugData['page_system']['statistics'] ?? []) > 0 ? $debugData['page_system']['statistics']['total_pages'] : 0 }}</div>
                                <div class="metric-label">Toplam Sayfa</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-number">{{ count($debugData['error_logs']['recent_logs'] ?? []) }}</div>
                                <div class="metric-label">Son Log Kaydı</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-number">
                                    @if(isset($debugData['ai_system']['provider_status']['test_successful']) && $debugData['ai_system']['provider_status']['test_successful'])
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </div>
                                <div class="metric-label">AI Sistemi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Menu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tenant-info">
                                <i class="fas fa-server me-2"></i>Tenant Bilgisi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#language-system">
                                <i class="fas fa-language me-2"></i>Dil Sistemi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#ai-system">
                                <i class="fas fa-robot me-2"></i>AI Sistemi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#test-panel">
                                <i class="fas fa-flask me-2"></i>Test Paneli
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#logs">
                                <i class="fas fa-file-alt me-2"></i>Loglar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#real-time">
                                <i class="fas fa-broadcast-tower me-2"></i>Canlı İzleme
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#advanced-debug">
                                <i class="fas fa-microscope me-2"></i>Gelişmiş Debug
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content">
                        
                        <!-- Tenant Info Tab -->
                        <div class="tab-pane fade show active" id="tenant-info">
                            <div class="debug-card card p-3">
                                <h5><i class="fas fa-info-circle me-2"></i>Tenant Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Tenant ID:</strong></td>
                                                <td>{{ $debugData['tenant_info']['id'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tenant Name:</strong></td>
                                                <td>{{ $debugData['tenant_info']['name'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Default Locale:</strong></td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $debugData['tenant_info']['default_locale'] ?? 'N/A' }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Current Domain:</strong></td>
                                                <td>{{ $debugData['tenant_info']['current_domain'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tenancy Status:</strong></td>
                                                <td>
                                                    @if($debugData['tenant_info']['tenancy_initialized'] ?? false)
                                                        <span class="badge bg-success">Initialized</span>
                                                    @else
                                                        <span class="badge bg-danger">Not Initialized</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Session Verileri:</h6>
                                        <div class="json-viewer">
                                            <pre>{{ json_encode($debugData['session_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="debug-card card p-3 mt-3">
                                <h5><i class="fas fa-memory me-2"></i>Cache Durumu</h5>
                                @if(isset($debugData['cache_data']['error']))
                                    <div class="alert alert-danger">{{ $debugData['cache_data']['error'] }}</div>
                                @else
                                    <div class="row">
                                        @foreach($debugData['cache_data'] as $key => $data)
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title">{{ $key }}</h6>
                                                        <p class="mb-1">
                                                            <strong>Exists:</strong> 
                                                            @if($data['exists'])
                                                                <span class="badge bg-success">Yes</span>
                                                            @else
                                                                <span class="badge bg-danger">No</span>
                                                            @endif
                                                        </p>
                                                        @if($data['exists'])
                                                            <p class="mb-0">
                                                                <strong>Value:</strong>
                                                                <code>{{ Str::limit(json_encode($data['value']), 100) }}</code>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Language System Tab -->
                        <div class="tab-pane fade" id="language-system">
                            @if(isset($debugData['language_system']['error']))
                                <div class="debug-card error card p-3">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Dil Sistemi Hatası</h5>
                                    <p>{{ $debugData['language_system']['error'] }}</p>
                                    @if(isset($debugData['language_system']['trace']))
                                        <details>
                                            <summary>Stack Trace</summary>
                                            <pre>{{ $debugData['language_system']['trace'] }}</pre>
                                        </details>
                                    @endif
                                </div>
                            @else
                                <div class="debug-card success card p-3">
                                    <h5><i class="fas fa-language me-2"></i>Dil Sistemi Özeti</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ $debugData['language_system']['total_languages'] }}</h3>
                                                <p>Toplam Dil</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ count($debugData['language_system']['active_languages']) }}</h3>
                                                <p>Aktif Dil</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ count($debugData['language_system']['inactive_languages']) }}</h3>
                                                <p>Pasif Dil</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h3 class="text-danger">{{ count($debugData['language_system']['hidden_languages']) }}</h3>
                                                <p>Gizli Dil</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="debug-card card p-3 mt-3">
                                    <h5><i class="fas fa-list me-2"></i>Dil Detayları</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Kod</th>
                                                    <th>İsim</th>
                                                    <th>Yerel İsim</th>
                                                    <th>Durum</th>
                                                    <th>Görünürlük</th>
                                                    <th>Varsayılan</th>
                                                    <th>Sıra</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($debugData['language_system']['language_details'] as $lang)
                                                    <tr>
                                                        <td>{{ $lang['id'] }}</td>
                                                        <td><code>{{ $lang['code'] }}</code></td>
                                                        <td>{{ $lang['name'] }}</td>
                                                        <td>{{ $lang['native_name'] }}</td>
                                                        <td>
                                                            @if($lang['is_active'])
                                                                <span class="status-badge status-active">Aktif</span>
                                                            @else
                                                                <span class="status-badge status-inactive">Pasif</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lang['is_visible'])
                                                                <span class="status-badge status-active">Görünür</span>
                                                            @else
                                                                <span class="status-badge status-hidden">Gizli</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($lang['is_default'])
                                                                <span class="badge bg-primary">Varsayılan</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>{{ $lang['sort_order'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- AI System Tab -->
                        <div class="tab-pane fade" id="ai-system">
                            @if(isset($debugData['ai_system']['error']))
                                <div class="debug-card error card p-3">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>AI Sistemi Hatası</h5>
                                    <p>{{ $debugData['ai_system']['error'] }}</p>
                                </div>
                            @else
                                <div class="debug-card card p-3">
                                    <h5><i class="fas fa-robot me-2"></i>AI Provider Durumu</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Durum:</strong></td>
                                                    <td>
                                                        @if($debugData['ai_system']['provider_status']['available'])
                                                            <span class="badge bg-success">Aktif</span>
                                                        @else
                                                            <span class="badge bg-danger">Hata</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Provider:</strong></td>
                                                    <td>{{ $debugData['ai_system']['provider_status']['provider_name'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Model:</strong></td>
                                                    <td>{{ $debugData['ai_system']['provider_status']['model'] }}</td>
                                                </tr>
                                                @if(isset($debugData['ai_system']['provider_status']['error']))
                                                    <tr>
                                                        <td><strong>Hata:</strong></td>
                                                        <td class="text-danger">{{ $debugData['ai_system']['provider_status']['error'] }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Test Çevirisi:</h6>
                                            @if(isset($debugData['ai_system']['provider_status']['test_translation']))
                                                <div class="alert alert-success">
                                                    <strong>İngilizce:</strong> Hello World<br>
                                                    <strong>Türkçe:</strong> {{ $debugData['ai_system']['provider_status']['test_translation'] }}
                                                </div>
                                            @else
                                                <div class="alert alert-warning">Test çevirisi yapılamadı</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="debug-card card p-3 mt-3">
                                    <h5><i class="fas fa-cogs me-2"></i>Çeviri Özellikleri</h5>
                                    <div class="row">
                                        @foreach($debugData['ai_system']['translation_features'] as $feature => $status)
                                            <div class="col-md-3 mb-2">
                                                <div class="d-flex align-items-center">
                                                    @if($status)
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                                    @endif
                                                    <span>{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Test Panel Tab -->
                        <div class="tab-pane fade" id="test-panel">
                            <div class="test-panel">
                                <h5><i class="fas fa-flask me-2"></i>Canlı Çeviri Testi</h5>
                                <p class="text-muted">Bu panel ile çeviri sistemini gerçek zamanlı olarak test edebilirsiniz.</p>
                                
                                <form id="translation-test-form">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kaynak Metin:</label>
                                                <textarea class="form-control" id="source-text" rows="3" placeholder="Çevrilecek metni girin...">Merhaba dünya! Bu bir test metnidir.</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Kaynak Dil:</label>
                                                <select class="form-select" id="source-lang">
                                                    @foreach($debugData['language_system']['active_languages'] ?? [] as $lang)
                                                        <option value="{{ $lang }}" {{ $lang === 'tr' ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Hedef Dil:</label>
                                                <select class="form-select" id="target-lang">
                                                    @foreach($debugData['language_system']['active_languages'] ?? [] as $lang)
                                                        <option value="{{ $lang }}" {{ $lang === 'en' ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-language me-2"></i>Çevir
                                            </button>
                                            <span id="test-loading" class="ms-3" style="display: none;">
                                                <i class="fas fa-spinner fa-spin"></i> Çeviriliyor...
                                            </span>
                                        </div>
                                    </div>
                                </form>
                                
                                <div id="translation-result" style="display: none;" class="mt-4">
                                    <h6>Çeviri Sonucu:</h6>
                                    <div class="alert alert-success">
                                        <div id="translated-text"></div>
                                        <small class="text-muted">
                                            <strong>Süre:</strong> <span id="translation-duration"></span>ms
                                        </small>
                                    </div>
                                </div>
                                
                                <div id="translation-error" style="display: none;" class="mt-4">
                                    <div class="alert alert-danger">
                                        <strong>Hata:</strong> <span id="error-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logs Tab -->
                        <div class="tab-pane fade" id="logs">
                            <div class="debug-card card p-3">
                                <h5><i class="fas fa-file-alt me-2"></i>Son Log Kayıtları</h5>
                                @if(isset($debugData['error_logs']['error']))
                                    <div class="alert alert-danger">{{ $debugData['error_logs']['error'] }}</div>
                                @else
                                    <p class="text-muted">
                                        <strong>Log dosyası:</strong> {{ $debugData['error_logs']['log_file_path'] ?? 'N/A' }}<br>
                                        <strong>Dosya boyutu:</strong> {{ number_format(($debugData['error_logs']['log_file_size'] ?? 0) / 1024, 2) }} KB<br>
                                        <strong>Gösterilen kayıt:</strong> {{ count($debugData['error_logs']['recent_logs'] ?? []) }} adet
                                    </p>
                                    
                                    <div style="max-height: 500px; overflow-y: auto;">
                                        @forelse($debugData['error_logs']['recent_logs'] ?? [] as $log)
                                            <div class="log-entry {{ strtolower($log['level']) }}">
                                                <div class="d-flex justify-content-between">
                                                    <span class="badge badge-sm bg-{{ strtolower($log['level']) === 'error' ? 'danger' : (strtolower($log['level']) === 'warning' ? 'warning' : 'info') }}">
                                                        {{ $log['level'] }}
                                                    </span>
                                                    <small>{{ $log['timestamp'] }}</small>
                                                </div>
                                                <div class="mt-1">{{ $log['message'] }}</div>
                                            </div>
                                        @empty
                                            <div class="alert alert-info">Çeviri ile ilgili log kaydı bulunamadı</div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Real-time Tab -->
                        <div class="tab-pane fade" id="real-time">
                            <div class="debug-card card p-3">
                                <h5><i class="fas fa-broadcast-tower me-2"></i>Canlı Log İzleme</h5>
                                <p class="text-muted">Bu panel çeviri sistemindeki gerçek zamanlı aktiviteleri gösterir.</p>
                                
                                <div class="mb-3">
                                    <button type="button" class="btn btn-success btn-sm" onclick="startLogStream()">
                                        <i class="fas fa-play"></i> Başlat
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="stopLogStream()">
                                        <i class="fas fa-stop"></i> Durdur
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearRealTimeLogs()">
                                        <i class="fas fa-trash"></i> Temizle
                                    </button>
                                    <span id="stream-status" class="ms-3">
                                        <span class="badge bg-secondary">Durduruldu</span>
                                    </span>
                                </div>
                                
                                <div id="real-time-logs" class="real-time-logs">
                                    <div class="text-muted text-center">Log akışı başlatılmadı...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Debug Tab -->
                        <div class="tab-pane fade" id="advanced-debug">
                            <div class="debug-card card p-3">
                                <h5><i class="fas fa-microscope me-2"></i>Gelişmiş Çeviri Debug Sistemi</h5>
                                <p class="text-muted">Bu panel ile Page modülündeki çeviri sistemini detaylı olarak test edebilir ve debug edebilirsiniz.</p>
                                
                                <!-- Full System Debug -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6><i class="fas fa-cogs me-2"></i>Tam Sistem Analizi</h6>
                                        <button type="button" class="btn btn-info" onclick="runFullSystemDebug()">
                                            <i class="fas fa-search me-2"></i>Sistem Analizi Çalıştır
                                        </button>
                                        <div id="full-debug-result" class="mt-3" style="display: none;"></div>
                                    </div>
                                </div>
                                
                                <!-- Page Translation Test -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6><i class="fas fa-file-alt me-2"></i>Page Çeviri Testi</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Page ID:</label>
                                                <input type="number" class="form-control" id="test-page-id" placeholder="1" value="1">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Kaynak Dil:</label>
                                                <select class="form-select" id="test-source-lang">
                                                    @foreach($debugData['language_system']['active_languages'] ?? [] as $lang)
                                                        <option value="{{ $lang }}" {{ $lang === 'tr' ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Hedef Diller:</label>
                                                <select class="form-select" id="test-target-langs" multiple>
                                                    @foreach($debugData['language_system']['active_languages'] ?? [] as $lang)
                                                        <option value="{{ $lang }}" {{ $lang === 'en' ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Test Modu:</label>
                                                <select class="form-select" id="test-mode">
                                                    <option value="simulate">Simülasyon</option>
                                                    <option value="real">Gerçek Çeviri</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-primary" onclick="runPageTranslationTest()">
                                                <i class="fas fa-play me-2"></i>Çeviri Testi Çalıştır
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="analyzePageData()">
                                                <i class="fas fa-chart-line me-2"></i>Page Verilerini Analiz Et
                                            </button>
                                        </div>
                                        <div id="page-test-result" class="mt-3" style="display: none;"></div>
                                    </div>
                                </div>
                                
                                <!-- Live Debug Results -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6><i class="fas fa-terminal me-2"></i>Debug Sonuçları</h6>
                                        <div id="advanced-debug-output" class="json-viewer" style="min-height: 300px; background: #1e1e1e; color: #f8f8f2; padding: 15px; border-radius: 8px;">
                                            <div class="text-muted text-center">Debug testi çalıştırın...</div>
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

@push('scripts')
<script>
let eventSource = null;

// Translation test
document.getElementById('translation-test-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const sourceText = document.getElementById('source-text').value;
    const sourceLang = document.getElementById('source-lang').value;
    const targetLang = document.getElementById('target-lang').value;
    
    document.getElementById('test-loading').style.display = 'inline';
    document.getElementById('translation-result').style.display = 'none';
    document.getElementById('translation-error').style.display = 'none';
    
    try {
        const response = await fetch('{{ route("admin.debug.translation.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                text: sourceText,
                from: sourceLang,
                to: targetLang
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('translated-text').textContent = data.translated;
            document.getElementById('translation-duration').textContent = data.duration_ms;
            document.getElementById('translation-result').style.display = 'block';
        } else {
            document.getElementById('error-message').textContent = data.error;
            document.getElementById('translation-error').style.display = 'block';
        }
    } catch (error) {
        document.getElementById('error-message').textContent = 'Network error: ' + error.message;
        document.getElementById('translation-error').style.display = 'block';
    }
    
    document.getElementById('test-loading').style.display = 'none';
});

// Clear logs
async function clearLogs() {
    if (!confirm('Tüm log kayıtları silinecek. Emin misiniz?')) return;
    
    try {
        const response = await fetch('{{ route("admin.debug.translation.clear-logs") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Log dosyası temizlendi');
            window.location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}

// Clear cache
async function clearCache() {
    if (!confirm('Tüm cache verileri silinecek. Emin misiniz?')) return;
    
    try {
        const response = await fetch('{{ route("admin.debug.translation.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Hata: ' + data.error);
        }
    } catch (error) {
        alert('Network error: ' + error.message);
    }
}

// Refresh data
function refreshData() {
    window.location.reload();
}

// Real-time log streaming
function startLogStream() {
    if (eventSource) {
        eventSource.close();
    }
    
    eventSource = new EventSource('{{ route("admin.debug.translation.stream-logs") }}');
    
    eventSource.onopen = function() {
        document.getElementById('stream-status').innerHTML = '<span class="badge bg-success">Aktif</span>';
    };
    
    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        const logsContainer = document.getElementById('real-time-logs');
        
        if (data.error) {
            logsContainer.innerHTML = '<div class="text-danger">Error: ' + data.error + '</div>';
            return;
        }
        
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry ' + data.level.toLowerCase();
        logEntry.innerHTML = `
            <div class="d-flex justify-content-between">
                <span class="badge badge-sm bg-${data.level.toLowerCase() === 'error' ? 'danger' : (data.level.toLowerCase() === 'warning' ? 'warning' : 'info')}">
                    ${data.level}
                </span>
                <small>${new Date(data.timestamp).toLocaleTimeString()}</small>
            </div>
            <div class="mt-1">${data.message}</div>
        `;
        
        // Add to top
        if (logsContainer.firstChild) {
            logsContainer.insertBefore(logEntry, logsContainer.firstChild);
        } else {
            logsContainer.appendChild(logEntry);
        }
        
        // Keep only last 50 entries
        while (logsContainer.children.length > 50) {
            logsContainer.removeChild(logsContainer.lastChild);
        }
    };
    
    eventSource.onerror = function() {
        document.getElementById('stream-status').innerHTML = '<span class="badge bg-danger">Hata</span>';
    };
}

function stopLogStream() {
    if (eventSource) {
        eventSource.close();
        eventSource = null;
    }
    document.getElementById('stream-status').innerHTML = '<span class="badge bg-secondary">Durduruldu</span>';
}

function clearRealTimeLogs() {
    document.getElementById('real-time-logs').innerHTML = '<div class="text-muted text-center">Log akışı temizlendi...</div>';
}

// Auto-refresh every 30 seconds for tabs other than real-time
setInterval(function() {
    const activeTab = document.querySelector('.tab-pane.active');
    if (activeTab && activeTab.id !== 'real-time') {
        // Only refresh specific sections to avoid disrupting user interaction
        console.log('Auto-refreshing debug data...');
    }
}, 30000);
</script>
@endpush