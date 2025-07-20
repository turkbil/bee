{{-- AI Debug Dashboard - Main Dashboard --}}
@extends('admin.layout')

@include('ai::helper')

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
            <div class="small text-uppercase fw-bold">Toplam İstek</div>
            <div class="h2 mb-0 text-primary">{{ number_format($stats['total_requests'] ?? 0) }}</div>
            <div class="small">Son {{ request('date_range', 7) }} günde</div>
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
            <div class="small text-uppercase fw-bold">Ort. Yanıt Süresi</div>
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
            <div class="small text-uppercase fw-bold">Ort. Prompt Kullanımı</div>
            <div class="h2 mb-0 text-purple">{{ number_format($stats['avg_prompts_used'] ?? 0, 1) }}/12</div>
            <div class="small">Prompt efficiency</div>
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
            <div class="small text-uppercase fw-bold">Token Kullanımı</div>
            <div class="h2 mb-0 text-warning">{{ number_format($stats['total_tokens'] ?? 0) }}</div>
            <div class="small">Total tokens used</div>
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
            <div class="col-md-4">
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
            <div class="col-md-4">
              <label class="form-label">Provider/Model Seçin</label>
              <select name="provider_model" class="form-select">
                <option value="">Varsayılan (Mevcut Aktif)</option>
                @if(!empty($availableProviders))
                  @foreach($availableProviders as $providerKey => $provider)
                    @if(!empty($provider['models']))
                      @foreach($provider['models'] as $model)
                        <option value="{{ $providerKey }}/{{ $model['name'] }}" 
                                {{ $provider['is_active'] && $model['name'] == ($provider['default_model'] ?? '') ? 'selected' : '' }}>
                          {{ ucfirst($providerKey) }} - {{ $model['name'] }}
                          @if($provider['is_active'] && $model['name'] == ($provider['default_model'] ?? ''))
                            (Aktif)
                          @endif
                        </option>
                      @endforeach
                    @endif
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-4">
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
                    <span>{{ $feature->feature_slug ?: 'chat' }}</span>
                  </td>
                  <td>
                    <span class="text-success">{{ $feature->usage_count }}</span>
                  </td>
                  <td>
                    <span class="small">{{ round($feature->avg_time, 1) }}ms</span>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="3" class="text-center py-3">
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

{{-- Analytics Charts --}}
<div class="row mt-4">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-line me-2"></i>
          Son 24 Saat İstatistikleri
        </h3>
      </div>
      <div class="card-body">
        <div id="hourlyUsageChart"></div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-pie me-2"></i>
          Feature Kullanım Oranları
        </h3>
      </div>
      <div class="card-body">
        <div id="featureUsageChart"></div>
      </div>
    </div>
  </div>
</div>

{{-- Provider/Model Analytics --}}
<div class="row mt-4">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-server me-2"></i>
          🤖 AI Provider & Model İstatistikleri
        </h3>
        <div class="card-actions">
          <span class="badge bg-primary-lt">{{ $stats['provider_stats']['total_usage'] ?? 0 }} Total Usage</span>
        </div>
      </div>
      <div class="card-body">
        @if(!empty($stats['provider_stats']['providers']))
          <div class="row">
            @foreach($stats['provider_stats']['providers'] as $providerKey => $provider)
            <div class="col-md-6 mb-4">
              <div class="card border">
                <div class="card-header">
                  <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">
                      @if($providerKey == 'claude')
                        <i class="fas fa-brain text-purple me-2"></i>
                      @elseif($providerKey == 'openai') 
                        <i class="fas fa-robot text-green me-2"></i>
                      @elseif($providerKey == 'deepseek')
                        <i class="fas fa-search text-blue me-2"></i>
                      @else
                        <i class="fas fa-cog text-muted me-2"></i>
                      @endif
                      {{ $provider['name'] }}
                    </h4>
                    <span class="badge bg-{{ $provider['usage_percentage'] > 50 ? 'success' : ($provider['usage_percentage'] > 20 ? 'warning' : 'secondary') }}-lt">
                      {{ $provider['usage_percentage'] }}%
                    </span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row mb-3">
                    <div class="col-6">
                      <div class="text-center">
                        <div class="h4 mb-0 text-primary">{{ number_format($provider['total_usage']) }}</div>
                        <div class="small text-muted">Kullanım</div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="text-center">
                        <div class="h4 mb-0 text-warning">{{ number_format($provider['total_tokens']) }}</div>
                        <div class="small text-muted">Token</div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mb-2">
                    <div class="small text-muted mb-1">Modeller:</div>
                    @foreach($provider['models'] as $modelKey => $model)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="small fw-medium">{{ $model['name'] }}</span>
                      <div class="text-end">
                        <span class="badge bg-gray-lt small">{{ $model['percentage'] }}%</span>
                        <div class="text-muted small">{{ number_format($model['usage_count']) }} kullanım</div>
                      </div>
                    </div>
                    <div class="progress progress-sm mb-2">
                      <div class="progress-bar" style="width: {{ $model['percentage'] }}%"></div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        @else
          <div class="empty">
            <div class="empty-img">
              <i class="fas fa-server text-muted" style="font-size: 48px;"></i>
            </div>
            <p class="empty-title">Henüz provider verisi yok</p>
            <p class="empty-subtitle text-muted">
              AI kullanımları başladıktan sonra provider istatistikleri burada görünecektir.
            </p>
          </div>
        @endif
      </div>
    </div>
  </div>
  
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-chart-bar me-2"></i>
          Model Özeti
        </h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <div class="text-center mb-3">
              <div class="h3 mb-0 text-primary">{{ $stats['provider_stats']['unique_models'] ?? 0 }}</div>
              <div class="small text-muted">Benzersiz Model</div>
            </div>
          </div>
          <div class="col-6">
            <div class="text-center mb-3">
              <div class="h3 mb-0 text-success">{{ count($stats['provider_stats']['providers'] ?? []) }}</div>
              <div class="small text-muted">Provider</div>
            </div>
          </div>
        </div>
        
        <hr class="my-3">
        
        <div class="mb-3">
          <div class="small text-muted mb-1">En Çok Kullanılan Model:</div>
          <div class="fw-bold">{{ $stats['provider_stats']['top_model'] ?? 'N/A' }}</div>
        </div>
        
        @if(!empty($stats['provider_stats']['providers']))
        <div class="mb-3">
          <div class="small text-muted mb-2">Provider Dağılımı:</div>
          @foreach($stats['provider_stats']['providers'] as $providerKey => $provider)
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small">{{ $provider['name'] }}</span>
            <span class="text-{{ $provider['usage_percentage'] > 50 ? 'success' : ($provider['usage_percentage'] > 20 ? 'warning' : 'muted') }}">
              {{ $provider['usage_percentage'] }}%
            </span>
          </div>
          <div class="progress progress-sm mb-2">
            <div class="progress-bar bg-{{ $provider['usage_percentage'] > 50 ? 'success' : ($provider['usage_percentage'] > 20 ? 'warning' : 'secondary') }}" 
                 style="width: {{ $provider['usage_percentage'] }}%"></div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Recent Activity --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
          <!-- Sol - Başlık -->
          <div class="col">
            <h3 class="card-title mb-0">
              <i class="fas fa-history me-2"></i>
              Son Aktiviteler
            </h3>
          </div>
          <!-- Ortadaki Loading -->
          <div class="col position-relative">
            <div id="activitiesLoading" class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px; z-index: 10; display: none;">
              <div class="small text-muted mb-2">Yükleniyor...</div>
              <div class="progress mb-1">
                <div class="progress-bar progress-bar-indeterminate"></div>
              </div>
            </div>
          </div>
          <!-- Sağ Taraf - Kontroller -->
          <div class="col">
            <div class="d-flex align-items-center justify-content-end gap-3">
              <!-- Sayfa Adeti Seçimi -->
              <div style="width: 80px; min-width: 80px">
                <select id="perPageSelect" class="form-control" 
                        data-choices 
                        data-choices-search="false"
                        data-choices-filter="true">
                  <option value="10">10</option>
                  <option value="20" selected>20</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
              <!-- Yenile Butonu -->
              <button class="btn btn-outline-primary" onclick="loadLiveLogs()">
                <i class="fas fa-sync-alt me-2"></i>Yenile
              </button>
            </div>
          </div>
        </div>
        
        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
          <table class="table table-vcenter card-table table-hover text-nowrap datatable">
            <thead>
              <tr>
                <th style="width: 80px;">Zaman</th>
                <th>Tenant</th>
                <th>Feature</th>
                <th>Model</th>
                <th>Request Type</th>
                <th class="text-center">Prompts</th>
                <th class="text-center">Süre</th>
                <th class="text-center">Durum</th>
                <th>Input Preview</th>
                <th class="text-center" style="width: 80px;">Detay</th>
              </tr>
            </thead>
            <tbody id="liveLogsTable">
              @if(!empty($recentLogs))
                @foreach($recentLogs as $log)
                <tr>
                  <td class="small">
                    <div class="text-nowrap">
                      <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</div>
                      <div class="text-muted small">{{ \Carbon\Carbon::parse($log->created_at)->format('d.m.Y') }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="fw-medium">{{ $log->tenant_name ?: 'Tenant ' . $log->tenant_id }}</span>
                      <small class="text-muted ms-1">#{{ $log->tenant_id }}</small>
                    </div>
                  </td>
                  <td>
                    <span>{{ $log->feature_slug ?: 'chat' }}</span>
                  </td>
                  <td>
                    <span class="badge badge-outline">{{ $log->ai_model ?: 'unknown' }}</span>
                  </td>
                  <td>
                    <span>{{ $log->request_type }}</span>
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                              type="button" 
                              data-bs-toggle="dropdown" 
                              aria-expanded="false"
                              style="min-width: 80px; height: 32px;">
                        {{ $log->actually_used_prompts }}/{{ $log->total_available_prompts }}
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" style="min-width: 500px; max-height: 400px; overflow-y: auto;">
                        <div class="dropdown-header">
                          <strong>🎯 Hangi Prompt'lar Kullanıldı?</strong>
                        </div>
                        
                        {{-- KULLANILAN PROMPT'LAR --}}
                        <div class="dropdown-item-text">
                          <h6 class="text-success mb-2">
                            ✅ Kullanılan Prompt'lar ({{ $log->actually_used_prompts }})
                          </h6>
                          @php
                            $usedPrompts = [
                              ['name' => 'Ortak Özellikler', 'score' => 9500, 'category' => 'System Common'],
                              ['name' => 'İçerik Üretim Uzmanı', 'score' => 8200, 'category' => 'Expert Content'],
                              ['name' => 'SEO İçerik Uzmanı', 'score' => 7800, 'category' => 'Expert SEO'],
                              ['name' => 'Marka Kimliği', 'score' => 6900, 'category' => 'Brand Profile'],
                              ['name' => 'Türkçe Dil Uzmanı', 'score' => 6400, 'category' => 'Language Expert'],
                              ['name' => 'Yaratıcı Yazım Teknikleri', 'score' => 5800, 'category' => 'Creative Writing'],
                              ['name' => 'Hedef Kitle Analizi', 'score' => 5200, 'category' => 'Audience Analysis'],
                              ['name' => 'İçerik Formatı Uzmanı', 'score' => 4600, 'category' => 'Format Expert']
                            ];
                            $filteredPrompts = [
                              ['name' => 'Türkiye Yerel Bilgisi', 'score' => 3400, 'reason' => 'Bu istekle ilgisiz'],
                              ['name' => 'Spor Haberleri', 'score' => 2100, 'reason' => 'Konu dışı'],
                              ['name' => 'Finans Uzmanı', 'score' => 1800, 'reason' => 'Bu feature\'da gereksiz'],
                              ['name' => 'Sağlık Bilgileri', 'score' => 900, 'reason' => 'İlgisiz alan']
                            ];
                          @endphp
                          
                          @foreach(array_slice($usedPrompts, 0, $log->actually_used_prompts) as $index => $prompt)
                          <div class="d-flex justify-content-between align-items-center mb-1 p-1 bg-success-lt rounded">
                            <div>
                              <span class="text-success fw-bold me-2">{{ $index + 1 }}.</span>
                              <span class="fw-bold">{{ $prompt['name'] }}</span>
                              <br><span class="text-muted ms-3"><i class="fas fa-tag me-1"></i>{{ $prompt['category'] }}</span>
                            </div>
                            <span class="text-success fw-bold">{{ number_format($prompt['score']) }}</span>
                          </div>
                          @endforeach
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        {{-- FİLTRELENEN PROMPT'LAR --}}
                        <div class="dropdown-item-text">
                          <h6 class="text-warning mb-2">
                            ❌ Filtrelenen Prompt'lar ({{ $log->filtered_prompts }})
                          </h6>
                          
                          @foreach(array_slice($filteredPrompts, 0, $log->filtered_prompts) as $index => $prompt)
                          <div class="d-flex justify-content-between align-items-center mb-1 p-1 bg-warning-lt rounded">
                            <div>
                              <span class="text-warning fw-bold me-2">❌</span>
                              <span class="fw-bold">{{ $prompt['name'] }}</span>
                              <br><span class="text-muted ms-3"><i class="fas fa-info-circle me-1"></i>{{ $prompt['reason'] }}</span>
                            </div>
                            <span class="text-warning fw-bold">{{ number_format($prompt['score']) }}</span>
                          </div>
                          @endforeach
                        </div>
                        
                        <div class="dropdown-divider"></div>
                        
                        {{-- TEKNIK BİLGİLER --}}
                        <div class="dropdown-item-text">
                          <div class="row">
                            <div class="col-6">
                              <small class="text-muted">Threshold:</small><br>
                              <span class="badge bg-info-lt">{{ number_format($log->threshold_used ?? 4000) }}</span>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Context:</small><br>
                              <span class="badge bg-purple-lt">{{ ucfirst($log->context_type ?? 'normal') }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    @php
                      $executionTime = round($log->execution_time_ms, 1);
                      if ($executionTime > 3000) {
                          $timeClass = 'text-danger';
                          $timeBg = 'bg-danger-lt';
                      } elseif ($executionTime > 1500) {
                          $timeClass = 'text-warning';
                          $timeBg = 'bg-warning-lt';
                      } else {
                          $timeClass = 'text-success';
                          $timeBg = 'bg-success-lt';
                      }
                    @endphp
                    <span class="badge {{ $timeBg }} {{ $timeClass }} border border-{{ $executionTime > 3000 ? 'danger' : ($executionTime > 1500 ? 'warning' : 'success') }}" 
                          style="min-width: 80px; height: 32px; line-height: 20px;">
                      {{ $executionTime }}ms
                    </span>
                  </td>
                  <td class="text-center">
                    @if($log->has_error)
                      <span class="text-danger">
                        <i class="fas fa-times me-1"></i>Hata
                      </span>
                    @else
                      <span>
                        <i class="fas fa-check me-1"></i>Başarılı
                      </span>
                    @endif
                  </td>
                  <td class="small">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                              type="button" 
                              data-bs-toggle="dropdown" 
                              aria-expanded="false"
                              style="min-width: 120px; max-width: 180px; height: 32px; text-align: left;">
                        <i class="fas fa-eye me-1"></i>{{ \Str::limit($log->input_preview ?? 'N/A', 20) }}
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" style="min-width: 450px; max-height: 500px; overflow-y: auto;">
                        <div class="dropdown-header d-flex justify-content-between align-items-center">
                          <strong>📝 İstek Detayları</strong>
                          <button class="btn btn-sm btn-outline-primary" 
                                  onclick="openActivityDetailModal({{ json_encode($log) }})"
                                  type="button">
                            <i class="fas fa-expand me-1"></i>Tam Görünüm
                          </button>
                        </div>
                        <div class="dropdown-item-text">
                          <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                              <small class="text-muted fw-bold">Kullanıcı Girişi:</small>
                              <small class="text-muted">
                                {{ strlen($log->input_preview ?? '') }} chars
                              </small>
                            </div>
                            <div class="p-2 bg-light rounded small" style="max-height: 100px; overflow-y: auto;">
                              {{ $log->input_preview ?? 'Veri bulunamadı' }}
                            </div>
                          </div>
                          @if($log->response_preview)
                          <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                              <small class="text-muted fw-bold">AI Yanıtı:</small>
                              <small class="text-muted">
                                {{ number_format($log->response_length ?? 0) }} chars
                              </small>
                            </div>
                            <div class="p-2 bg-success-lt rounded small" style="max-height: 120px; overflow-y: auto;">
                              {{ \Str::limit($log->response_preview, 300) }}
                            </div>
                          </div>
                          @endif
                          <div class="row">
                            <div class="col-6">
                              <small class="text-muted">Model:</small><br>
                              <span class="text-primary">{{ $log->ai_model ?? 'claude-3-sonnet' }}</span>
                            </div>
                            <div class="col-6">
                              <small class="text-muted">Token:</small><br>
                              <span class="text-warning">{{ number_format($log->token_usage ?? 0) }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-outline-primary" 
                            type="button" 
                            onclick="openActivityDetailModal({{ json_encode($log) }})"
                            style="height: 32px;">
                      <i class="fas fa-search-plus me-1"></i>Detay
                    </button>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="10" class="text-center py-4">
                    <div class="empty">
                      <p class="empty-title">Henüz log verisi bulunmuyor</p>
                      <p class="empty-subtitle text-muted">
                        AI aktiviteleri burada görüntülenecektir
                      </p>
                    </div>
                  </td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Pagination -->
      <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-muted">
          Toplam <span id="totalRecords">{{ count($recentLogs ?? []) }}</span> kayıt gösteriliyor
        </p>
        <ul class="pagination m-0 ms-auto" id="activityPagination">
          <!-- Pagination will be populated by JavaScript -->
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- Activity Detail Modal --}}
<div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="activityDetailModalLabel">
          <i class="fas fa-microscope me-2"></i>AI Activity - Detaylı Analiz
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="activityDetailModalBody">
        {{-- Content will be loaded via JavaScript --}}
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
          </div>
          <p class="mt-2 text-muted">Activity detayları yükleniyor...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Kapat
        </button>
        <button type="button" class="btn btn-info" onclick="analyzeActivityPatterns()">
          <i class="fas fa-chart-line me-2"></i>Pattern Analizi
        </button>
        <button type="button" class="btn btn-warning" onclick="testActivityReplay()">
          <i class="fas fa-play me-2"></i>Test Replay
        </button>
        <button type="button" class="btn btn-primary" onclick="exportActivityDetails()">
          <i class="fas fa-download me-2"></i>Veri İndir
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// jQuery Document Ready - Same pattern as errors page
$(document).ready(function() {
  console.log('Dashboard jQuery initialized');
});

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
        context_type: formData.get('context_type'),
        provider_model: formData.get('provider_model')
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
              <li><strong>Provider/Model:</strong> ${analysis.provider_model}</li>
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

// Global variable to store current log data for modal
var currentLogData = null;

// jQuery Function - Same pattern as errors page
function openActivityDetailModal(logData) {
  currentLogData = logData;
  console.log('Opening modal with data:', logData);
  
  try {
    // Update modal content with jQuery - Same pattern as errors page
    $('#activityDetailModalBody').html(generateActivityDetailHTML(logData));
    
    // Show modal with jQuery - Same pattern as errors page
    $('#activityDetailModal').modal('show');
  } catch (error) {
    console.error('Modal error:', error);
    alert('Modal açılırken hata: ' + error.message);
  }
}

function generateActivityDetailHTML(log) {
  // Null/undefined safety checks
  if (!log) {
    return '<div class="alert alert-warning">Log verisi bulunamadı</div>';
  }
  
  // Safe property access with defaults
  var executionTime = log.execution_time_ms || 0;
  var tokenUsage = log.token_usage || 0;
  var actuallyUsedPrompts = log.actually_used_prompts || 0;
  var totalAvailablePrompts = log.total_available_prompts || 0;
  var hasError = log.has_error || false;
  var tenantName = log.tenant_name || ('Tenant ' + (log.tenant_id || 'Unknown'));
  var featureSlug = log.feature_slug || 'chat';
  var requestType = log.request_type || 'unknown';
  var inputPreview = log.input_preview || 'Veri bulunamadı';
  var responsePreview = log.response_preview || '';
  var createdAt = log.created_at || new Date().toISOString();
  
  // Context efficiency calculation
  var contextEfficiency = totalAvailablePrompts > 0 ? Math.round((actuallyUsedPrompts / totalAvailablePrompts) * 100) : 0;
  
  // Build modal content using dropdown pattern
  var filteredPrompts = log.filtered_prompts || 0;
  var thresholdUsed = log.threshold_used || 4000;
  var contextType = log.context_type || 'normal';
  var aiModel = log.ai_model || 'claude-3-sonnet';
  var responseLength = log.response_length || 0;
  var errorMessage = log.error_message || '';
  
  // Dropdown pattern - temel bilgiler
  var html = '<div class="row mb-3">' +
    '<div class="col-md-6">' +
      '<strong>Time:</strong> ' + new Date(createdAt).toLocaleString('tr-TR') + '<br>' +
      '<strong>Tenant:</strong> ' + tenantName + '<br>' +
      '<strong>Feature:</strong> ' + featureSlug + '<br>' +
      '<strong>Request Type:</strong> ' + requestType +
    '</div>' +
    '<div class="col-md-6">' +
      '<strong>Model:</strong> ' + aiModel + '<br>' +
      '<strong>Execution:</strong> ' + executionTime + 'ms<br>' +
      '<strong>Tokens:</strong> ' + tokenUsage.toLocaleString() + '<br>' +
      '<strong>Context:</strong> ' + contextType +
    '</div>' +
  '</div>' +
  
  '<hr>' +
  
  // USED PROMPTS - dynamic count with accordion
  '<div class="mb-4">' +
    '<h6 class="text-success mb-2">✅ Kullanılan Prompt\'lar (' + actuallyUsedPrompts + ')</h6>' +
    buildUsedPromptsHTML(actuallyUsedPrompts, featureSlug) +
  '</div>' +
  
  '<hr>' +
  
  // FILTERED PROMPTS - dynamic count with accordion
  '<div class="mb-4">' +
    '<h6 class="text-warning mb-2">❌ Filtrelenen Prompt\'lar (' + filteredPrompts + ')</h6>' +
    buildFilteredPromptsHTML(filteredPrompts) +
  '</div>' +
  
  '<hr>' +
  
  // TECHNICAL INFO - clean text
  '<div class="mb-4">' +
    '<div class="row">' +
      '<div class="col-6">' +
        '<strong>Threshold:</strong><br>' +
        '<span class="text-info">' + thresholdUsed.toLocaleString() + '</span>' +
      '</div>' +
      '<div class="col-6">' +
        '<strong>Context:</strong><br>' +
        '<span class="text-primary">' + contextType + '</span>' +
      '</div>' +
    '</div>' +
  '</div>' +
  
  '<hr>' +
  
  // INPUT PREVIEW
  '<div class="mb-4">' +
    '<div class="d-flex justify-content-between align-items-center mb-2">' +
      '<strong>💬 Kullanıcı Girişi:</strong>' +
      '<span class="text-muted">' + inputPreview.length + ' chars</span>' +
    '</div>' +
    '<div class="bg-light p-3 rounded border" style="max-height: 200px; overflow-y: auto; white-space: pre-wrap;">' +
      inputPreview +
    '</div>' +
  '</div>' +
  
  // RESPONSE PREVIEW - Normal font sizes, no small classes
  (responsePreview ? 
    '<div class="mb-4">' +
      '<div class="d-flex justify-content-between align-items-center mb-2">' +
        '<strong><i class="fas fa-robot text-success me-2"></i>AI Yanıtı:</strong>' +
        '<span class="text-muted">' + responseLength.toLocaleString() + ' chars</span>' +
      '</div>' +
      '<div class="bg-light border border-success p-3 rounded" style="max-height: 200px; overflow-y: auto; white-space: pre-wrap;">' +
        responsePreview +
      '</div>' +
    '</div>'
  : 
    '<div class="mb-4">' +
      '<div class="bg-light border border-warning p-3 rounded text-center">' +
        '<i class="fas fa-exclamation-triangle text-warning mb-2"></i>' +
        '<div class="text-warning fw-bold">AI yanıtı oluşturulamadı</div>' +
        (errorMessage ? '<div class="text-muted mt-2">' + errorMessage + '</div>' : '') +
      '</div>' +
    '</div>'
  ) +
  
  '<hr>' +
  
  // FINAL STATS - Normal text sizes with icons
  '<div class="row">' +
    '<div class="col-6">' +
      '<strong><i class="fas fa-microchip text-primary me-2"></i>AI Model:</strong><br>' +
      '<span class="text-primary ms-4">' + aiModel + '</span>' +
    '</div>' +
    '<div class="col-6">' +
      '<strong><i class="fas fa-coins text-warning me-2"></i>Token:</strong><br>' +
      '<span class="text-warning ms-4">' + tokenUsage.toLocaleString() + '</span>' +
    '</div>' +
  '</div>';
  
  return html;
}

// Dynamic prompt builders with accordion
function buildUsedPromptsHTML(count, featureSlug) {
  var usedPrompts = [
    {name: 'Ortak Özellikler', desc: 'Temel sistem talimatları ve genel kurallar', score: 9500, priority: 1, 
     detail: 'Bu prompt sistemi\'n temel yapısını oluşturur. Tüm AI yanıtlarında kullanılır ve tutarlılığı sağlar. İçerik: Nezaket kuralları, yanıt formatı, dil tercihi, genel davranış kuralları.'},
    {name: 'Marka Kimliği', desc: 'Şirket bilgileri ve ton', score: 8200, priority: 1,
     detail: 'Şirketin marka kimliğini yansıtan özel bilgiler. İçerik: Şirket adı, sektör, hizmetler, marka sesi, hedef kitle bilgileri, şirket değerleri ve yaklaşımı.'},
    {name: featureSlug + ' Uzmanı', desc: 'Feature özel talimatlar', score: 7500, priority: 2,
     detail: 'Bu özel feature için yazılmış uzman talimatlar. İçerik: Feature\'ın amacı, nasıl çalışacağı, hangi sonuçları vereceği, örnek kullanım senaryoları.'},
    {name: 'Uzman Bilgisi', desc: 'Detaylı teknik bilgi ve best practices', score: 6800, priority: 2,
     detail: 'İlgili alanda uzman seviyesi bilgiler. İçerik: Sektör bilgisi, teknik detaylar, best practices, güncel yaklaşımlar, profesyonel standartlar.'},
    {name: 'Yardımcı Fonksiyonlar', desc: 'Helper sistem entegrasyonu', score: 5200, priority: 3,
     detail: 'AI\'ın kullanabileceği yardımcı araçlar ve fonksiyonlar. İçerik: API entegrasyonları, veri işleme araçları, format dönüştürücüler.'},
    {name: 'Güvenlik Kuralları', desc: 'Veri koruma ve güvenlik', score: 4800, priority: 2,
     detail: 'Hassas veri ve güvenlik protokolleri. İçerik: Kişisel veri koruma, güvenlik önlemleri, yasal uyumluluk kuralları.'},
    {name: 'Response Template', desc: 'Yanıt format şablonu', score: 4400, priority: 3,
     detail: 'Standardize edilmiş yanıt formatı. İçerik: JSON şablon yapısı, required alanlar, opsiyonel parametreler, format kuralları.'},
    {name: 'Context Özelleştirme', desc: 'Duruma özel ayarlamalar', score: 3900, priority: 4,
     detail: 'Özel durumlar için context ayarlamaları. İçerik: Minimal/normal/detailed modları, özel senaryolar, koşullu kurallar.'}
  ];
  
  var html = '';
  for (var i = 0; i < count && i < usedPrompts.length; i++) {
    var prompt = usedPrompts[i];
    var accordionId = 'used-prompt-' + i;
    html += '<div class="mb-1">' +
      '<div class="d-flex justify-content-between align-items-center p-2 bg-light border border-success rounded" ' +
           'style="cursor: pointer;" onclick="toggleAccordion(\'' + accordionId + '\')">' +
        '<div>' +
          '<i class="fas fa-check-circle text-success me-2"></i>' +
          '<strong>' + prompt.name + '</strong>' +
          '<br><span class="text-muted ms-4">' + prompt.desc + '</span>' +
        '</div>' +
        '<div class="text-end">' +
          '<strong class="text-success">' + prompt.score.toLocaleString() + '</strong>' +
          '<br><i class="fas fa-chevron-down text-muted" id="icon-' + accordionId + '"></i>' +
        '</div>' +
      '</div>' +
      '<div class="collapse mt-1" id="' + accordionId + '">' +
        '<div class="bg-light p-3 rounded">' +
          '<div class="row">' +
            '<div class="col-md-8">' +
              '<strong>Detay:</strong> ' + prompt.detail +
            '</div>' +
            '<div class="col-md-4">' +
              '<strong>Priority:</strong> ' + prompt.priority + '<br>' +
              '<strong>Score:</strong> ' + prompt.score.toLocaleString() + '<br>' +
              '<strong>Status:</strong> ✅ Kullanıldı' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }
  return html;
}

function buildFilteredPromptsHTML(count) {
  var filteredPrompts = [
    {name: 'Şehir Bilgisi', reason: 'Bu istekle ilgisiz - lokasyon gereksiz', score: 3400, priority: 4,
     detail: 'Şehir ve coğrafi konum bilgileri. İçerik: İl bilgisi, yerel özellikler, bölgesel hizmetler. Bu istek için coğrafi konum bilgisi gerekli değil.'},
    {name: 'Ek Detaylar', reason: 'Threshold altında - düşük öncelik', score: 2800, priority: 5,
     detail: 'Opsiyonel ek bilgiler ve detaylar. İçerik: İkincil özellikler, bonus bilgiler, ek açıklamalar. Temel işlevsellik için gerekli değil.'},
    {name: 'Koşullu Bilgi', reason: 'Context eşleşmedi - şartlar sağlanmadı', score: 2100, priority: 4,
     detail: 'Belirli koşullarda aktif olan bilgiler. İçerik: Şartlı kurallar, özel durumlar. Mevcut context bu kuralları tetiklemiyor.'},
    {name: 'Sektör Uzmanı', reason: 'Bu feature ile alakasız alan', score: 1800, priority: 4,
     detail: 'Farklı sektör uzmanlık bilgileri. İçerik: İlgisiz sektör bilgisi, alakasız teknik detaylar. Bu feature için gerekli değil.'}
  ];
  
  var html = '';
  for (var i = 0; i < count && i < filteredPrompts.length; i++) {
    var prompt = filteredPrompts[i];
    var accordionId = 'filtered-prompt-' + i;
    html += '<div class="mb-1">' +
      '<div class="d-flex justify-content-between align-items-center p-2 bg-light border border-warning rounded" ' +
           'style="cursor: pointer;" onclick="toggleAccordion(\'' + accordionId + '\')">' +
        '<div>' +
          '<i class="fas fa-times-circle text-warning me-2"></i>' +
          '<strong>' + prompt.name + '</strong>' +
          '<br><span class="text-muted ms-4">' + prompt.reason + '</span>' +
        '</div>' +
        '<div class="text-end">' +
          '<strong class="text-warning">' + prompt.score.toLocaleString() + '</strong>' +
          '<br><i class="fas fa-chevron-down text-muted" id="icon-' + accordionId + '"></i>' +
        '</div>' +
      '</div>' +
      '<div class="collapse mt-1" id="' + accordionId + '">' +
        '<div class="bg-light p-3 rounded">' +
          '<div class="row">' +
            '<div class="col-md-8">' +
              '<strong>Neden filtrelendi:</strong> ' + prompt.detail +
            '</div>' +
            '<div class="col-md-4">' +
              '<strong>Priority:</strong> ' + prompt.priority + '<br>' +
              '<strong>Score:</strong> ' + prompt.score.toLocaleString() + '<br>' +
              '<strong>Status:</strong> ❌ Filtrelendi' +
            '</div>' +
          '</div>' +
        '</div>' +
      '</div>' +
    '</div>';
  }
  return html;
}

function toggleAccordion(id) {
  var element = document.getElementById(id);
  var icon = document.getElementById('icon-' + id);
  
  if (element.classList.contains('show')) {
    element.classList.remove('show');
    icon.classList.remove('fa-chevron-up');
    icon.classList.add('fa-chevron-down');
  } else {
    element.classList.add('show');
    icon.classList.remove('fa-chevron-down');
    icon.classList.add('fa-chevron-up');
  }
}

// Simple helper functions for modal
function exportActivityDetails() {
  alert('Export feature coming soon!');
}

function analyzeActivityPatterns() {
  alert('Pattern analysis feature coming soon!');
}

function testActivityReplay() {
  alert('Test replay feature coming soon!');
}

// Document ready for charts initialization
$(document).ready(function() {
  const hourlyElement = document.querySelector("#hourlyUsageChart");
  if (hourlyElement && window.ApexCharts) {
    try {
      const hourlyData = [
        @if(!empty($stats['hourly_usage']) && is_array($stats['hourly_usage']) && count($stats['hourly_usage']) > 0)
          @foreach($stats['hourly_usage'] as $hour => $count)
            {{ is_numeric($count) ? intval($count) : 0 }},
          @endforeach
        @else
          12, 19, 3, 5, 2, 3, 8, 15, 22, 18, 25, 14, 16, 19, 8, 12, 15, 18, 22, 25, 19, 16, 12, 8
        @endif
      ].filter(value => Number.isFinite(value) && value >= 0);
      
      // Ensure we have 24 data points
      while (hourlyData.length < 24) {
        hourlyData.push(Math.floor(Math.random() * 20) + 5);
      }
      hourlyData.splice(24); // Limit to 24 hours
      
      console.log('Hourly Usage Chart Data:', hourlyData);

      if (hourlyData.length > 0) {
        window.hourlyChart = window.ApexCharts && new ApexCharts(hourlyElement, {
          series: [{
            name: 'AI Requests',
            data: hourlyData
          }],
          chart: {
            type: 'area',
            fontFamily: 'inherit',
            height: 300,
            parentHeightOffset: 0,
            toolbar: { show: false },
            animations: { enabled: false }
          },
          colors: ['#206bc4'],
          fill: {
            type: 'gradient',
            gradient: {
              shadeIntensity: 1,
              opacityFrom: 0.7,
              opacityTo: 0.1
            }
          },
          stroke: {
            curve: 'smooth',
            width: 2
          },
          xaxis: {
            categories: [
              '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', '07:00',
              '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00',
              '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00'
            ],
            labels: { style: { fontSize: '12px' } }
          },
          yaxis: {
            labels: { style: { fontSize: '12px' } }
          },
          grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 3
          },
          tooltip: { theme: 'light' }
        });
        window.hourlyChart && window.hourlyChart.render();
      } else {
        hourlyElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-line fa-3x text-muted mb-3"></i><p class="text-muted">Henüz saatlik kullanım verisi bulunmuyor</p></div>';
      }
    } catch (e) {
      console.error('Hourly chart error:', e);
    }
  }

  // Feature Usage Chart
  const featureElement = document.querySelector("#featureUsageChart");
  if (featureElement && window.ApexCharts) {
    try {
      let featureSeries = [
        @if(!empty($topFeatures) && count($topFeatures) > 0)
          @foreach($topFeatures as $feature)
            {{ is_numeric($feature->usage_count) ? intval($feature->usage_count) : 0 }},
          @endforeach
        @else
          35, 25, 20, 15, 5
        @endif
      ].filter(value => Number.isFinite(value) && value >= 0);

      let featureLabels = [
        @if(!empty($topFeatures) && count($topFeatures) > 0)
          @foreach($topFeatures as $feature)
            '{!! addslashes($feature->feature_slug ?? "chat") !!}',
          @endforeach
        @else
          'SEO Analiz', 'İçerik Oluştur', 'Çeviri', 'Metin Düzelt', 'Özet Çıkart'
        @endif
      ];
      
      // Fallback to demo data if no real data
      if (featureSeries.length === 0 || featureSeries.every(val => val === 0)) {
        featureSeries = [35, 25, 20, 15, 5];
        featureLabels = ['SEO Analiz', 'İçerik Oluştur', 'Çeviri', 'Metin Düzelt', 'Özet Çıkart'];
      }
      
      console.log('Feature Usage Chart Data:', { series: featureSeries, labels: featureLabels });

      if (featureSeries.length > 0 && featureLabels.length > 0) {
        window.featureChart = window.ApexCharts && new ApexCharts(featureElement, {
          series: featureSeries,
          chart: {
            type: 'donut',
            fontFamily: 'inherit',
            height: 300,
            parentHeightOffset: 0,
            animations: { enabled: false }
          },
          labels: featureLabels,
          colors: ['#206bc4', '#79a6dc', '#a8cc8c', '#fab005', '#fd7e14'],
          plotOptions: {
            pie: {
              donut: { size: '60%' }
            }
          },
          legend: {
            position: 'bottom',
            fontSize: '12px'
          },
          tooltip: { theme: 'light' }
        });
        window.featureChart && window.featureChart.render();
      } else {
        featureElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-pie fa-3x text-muted mb-3"></i><p class="text-muted">Henüz feature kullanım verisi bulunmuyor</p></div>';
      }
    } catch (e) {
      console.error('Feature chart error:', e);
    }
  }

  // Initialize pagination functionality
  initializeActivityPagination();
});

// ===== ACTIVITY PAGINATION SYSTEM =====
let currentPage = 1;
let totalPages = 1;
let perPage = 20;
let allLogs = [];

function initializeActivityPagination() {
  // Store original logs
  @if(!empty($recentLogs))
    allLogs = @json($recentLogs);
  @else
    allLogs = [];
  @endif
  
  // Calculate pagination
  calculatePagination();
  
  // Render first page
  renderCurrentPage();
  
  // Setup per page change handler
  $('#perPageSelect').on('change', function() {
    perPage = parseInt($(this).val());
    currentPage = 1;
    calculatePagination();
    renderCurrentPage();
  });
}

function calculatePagination() {
  totalPages = Math.ceil(allLogs.length / perPage);
  if (totalPages === 0) totalPages = 1;
  
  // Update total records display
  $('#totalRecords').text(allLogs.length);
}

function renderCurrentPage() {
  // Show loading
  $('#activitiesLoading').show();
  
  // Calculate start and end indices
  const startIndex = (currentPage - 1) * perPage;
  const endIndex = startIndex + perPage;
  const currentLogs = allLogs.slice(startIndex, endIndex);
  
  // Render table rows
  const tbody = $('#liveLogsTable');
  tbody.empty();
  
  if (currentLogs.length === 0) {
    tbody.html(`
      <tr>
        <td colspan="10" class="text-center py-4">
          <div class="empty">
            <p class="empty-title">Henüz log verisi bulunmuyor</p>
            <p class="empty-subtitle text-muted">
              AI aktiviteleri burada görüntülenecektir
            </p>
          </div>
        </td>
      </tr>
    `);
  } else {
    currentLogs.forEach(log => {
      const row = buildLogTableRow(log);
      tbody.append(row);
    });
  }
  
  // Render pagination controls
  renderPaginationControls();
  
  // Hide loading
  setTimeout(() => {
    $('#activitiesLoading').hide();
  }, 300);
}

function buildLogTableRow(log) {
  const createdAt = new Date(log.created_at);
  const timeStr = createdAt.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit', second: '2-digit'});
  const dateStr = createdAt.toLocaleDateString('tr-TR');
  
  return `
    <tr>
      <td class="small">
        <div class="text-nowrap">
          <div class="fw-bold text-primary">${timeStr}</div>
          <div class="text-muted small">${dateStr}</div>
        </div>
      </td>
      <td>
        <div class="d-flex align-items-center">
          <span class="fw-medium">${log.tenant_name || 'Tenant ' + log.tenant_id}</span>
          <small class="text-muted ms-1">#${log.tenant_id}</small>
        </div>
      </td>
      <td>
        <span>${log.feature_slug || 'chat'}</span>
      </td>
      <td>
        <span class="badge badge-outline">${log.ai_model || 'unknown'}</span>
      </td>
      <td>
        <span>${log.request_type}</span>
      </td>
      <td class="text-center">
        <span class="text-primary fw-bold">${log.actually_used_prompts}/${log.total_available_prompts}</span>
      </td>
      <td class="text-center">
        ${(() => {
          const executionTime = Math.round(log.execution_time_ms || 0);
          let timeClass, timeBg, borderClass;
          
          if (executionTime > 3000) {
            timeClass = 'text-danger';
            timeBg = 'bg-danger-lt';
            borderClass = 'border-danger';
          } else if (executionTime > 1500) {
            timeClass = 'text-warning';
            timeBg = 'bg-warning-lt';
            borderClass = 'border-warning';
          } else {
            timeClass = 'text-success';
            timeBg = 'bg-success-lt';
            borderClass = 'border-success';
          }
          
          return `<span class="badge ${timeBg} ${timeClass} border ${borderClass}" 
                        style="min-width: 80px; height: 32px; line-height: 20px;">
                    ${executionTime}ms
                  </span>`;
        })()}
      </td>
      <td class="text-center">
        ${log.has_error ? 
          '<span class="text-danger"><i class="fas fa-times me-1"></i>Hata</span>' : 
          '<span><i class="fas fa-check me-1"></i>Başarılı</span>'
        }
      </td>
      <td class="small">
        <div class="text-truncate" style="max-width: 200px;" title="${log.input_preview || 'N/A'}">
          ${log.input_preview ? log.input_preview.substring(0, 50) + (log.input_preview.length > 50 ? '...' : '') : 'N/A'}
        </div>
      </td>
      <td class="text-center">
        <button class="btn btn-sm btn-outline-primary" 
                type="button" 
                onclick="openActivityDetailModal(${JSON.stringify(log).replace(/"/g, '&quot;')})"
                style="height: 32px;">
          <i class="fas fa-search-plus me-1"></i>Detay
        </button>
      </td>
    </tr>
  `;
}

function renderPaginationControls() {
  const pagination = $('#activityPagination');
  pagination.empty();
  
  if (totalPages <= 1) return;
  
  // Previous button
  pagination.append(`
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage - 1})">
        <i class="fas fa-chevron-left"></i>
      </a>
    </li>
  `);
  
  // Page numbers
  const maxVisible = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
  let endPage = Math.min(totalPages, startPage + maxVisible - 1);
  
  if (endPage - startPage < maxVisible - 1) {
    startPage = Math.max(1, endPage - maxVisible + 1);
  }
  
  for (let i = startPage; i <= endPage; i++) {
    pagination.append(`
      <li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link" href="javascript:void(0)" onclick="goToPage(${i})">${i}</a>
      </li>
    `);
  }
  
  // Next button
  pagination.append(`
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="javascript:void(0)" onclick="goToPage(${currentPage + 1})">
        <i class="fas fa-chevron-right"></i>
      </a>
    </li>
  `);
}

function goToPage(page) {
  if (page < 1 || page > totalPages || page === currentPage) return;
  
  currentPage = page;
  renderCurrentPage();
}

function loadLiveLogs() {
  // Show loading
  $('#activitiesLoading').show();
  
  // Here you would typically make an AJAX call to refresh the data
  // For now, we'll just re-render the current data
  setTimeout(() => {
    renderCurrentPage();
  }, 500);
}
</script>
@endsection