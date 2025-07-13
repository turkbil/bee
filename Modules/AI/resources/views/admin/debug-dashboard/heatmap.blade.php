{{-- AI Debug Dashboard - Prompt Usage Heatmap --}}
@extends('admin.layout')

@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

@section('title')
🔥 {{ __('ai::admin.prompt_usage_heatmap') }}
@endsection

@section('content')
<div class="row mb-3">
    <div class="col">
        <a href="{{ route('admin.ai.debug.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Ana Dashboard'a Dön
        </a>
    </div>
    <div class="col-auto">
        <div class="btn-list">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-clock me-2"></i>Zaman Aralığı
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="?date_range=1">
                        <i class="fas fa-calendar-day me-2"></i>Son 24 Saat
                    </a>
                    <a class="dropdown-item active" href="?date_range=7">
                        <i class="fas fa-calendar-week me-2"></i>Son 7 Gün
                    </a>
                    <a class="dropdown-item" href="?date_range=30">
                        <i class="fas fa-calendar-month me-2"></i>Son 30 Gün
                    </a>
                </div>
            </div>
            <button class="btn btn-outline-warning" onclick="refreshHeatmap()">
                <i class="fas fa-sync-alt me-2"></i>Yenile
            </button>
        </div>
    </div>
</div>

{{-- Heatmap İstatistikleri --}}
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-fire-lt me-3">
                        <i class="fas fa-fire icon-lg text-orange"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">En Popüler Prompt</div>
                        <div class="h3 mb-0 text-orange">
                            @if(!empty($heatmapData['most_popular_prompt']['name']))
                                {{ $heatmapData['most_popular_prompt']['name'] }}
                            @else
                                Veri yok
                            @endif
                        </div>
                        <div class="small text-muted">
                            @if(!empty($heatmapData['most_popular_prompt']['usage_percentage']))
                                %{{ $heatmapData['most_popular_prompt']['usage_percentage'] }} kullanım oranı
                            @else
                                Veri yok
                            @endif
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
                    <div class="avatar bg-blue-lt me-3">
                        <i class="fas fa-clock icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Yoğun Saat</div>
                        <div class="h3 mb-0 text-blue">
                            @if(!empty($heatmapData['hourly_usage'][0]))
                                {{ $heatmapData['hourly_usage'][0]->hour }}:00
                            @else
                                14:00
                            @endif
                        </div>
                        <div class="small text-muted">{{ !empty($heatmapData['hourly_usage'][0]) ? $heatmapData['hourly_usage'][0]->requests : '245' }} istek</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-green-lt me-3">
                        <i class="fas fa-chart-line icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Trend Artışı</div>
                        <div class="h3 mb-0 text-green">
                            @if(!empty($heatmapData['trend_change']))
                                {{ $heatmapData['trend_change'] >= 0 ? '+' : '' }}{{ $heatmapData['trend_change'] }}%
                            @else
                                Veri yok
                            @endif
                        </div>
                        <div class="small text-muted">Önceki haftaya göre</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card dashboard-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-red-lt me-3">
                        <i class="fas fa-thermometer-half icon-lg"></i>
                    </div>
                    <div class="flex-fill">
                        <div class="small text-muted text-uppercase fw-bold">Isı Skoru</div>
                        <div class="h3 mb-0 text-red">
                            @if(!empty($heatmapData['heat_score']))
                                {{ $heatmapData['heat_score'] }}°
                            @else
                                --°
                            @endif
                        </div>
                        <div class="small text-muted">
                            @if(!empty($heatmapData['heat_level']))
                                {{ $heatmapData['heat_level'] }}
                            @else
                                Veri yok
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Prompt Popülerlik Heatmap --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-th me-2"></i>
                    Saatlik Kullanım Haritası
                </h3>
                <div class="card-actions">
                    <div class="btn-list">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#heatmapSettings">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="heatmap-container" style="height: 400px;">
                    {{-- Heatmap Placeholder --}}
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <i class="fas fa-th-large fa-3x mb-3 opacity-50"></i>
                            <p>Isı haritası yükleniyor...</p>
                            <div class="progress w-50 mx-auto">
                                <div class="progress-bar progress-bar-indeterminate"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Renk Skalası --}}
                <div class="mt-3 d-flex align-items-center justify-content-center">
                    <span class="small text-muted me-2">Düşük</span>
                    <div class="d-flex">
                        <div class="badge" style="background: #e3f2fd; width: 20px; height: 20px;"></div>
                        <div class="badge" style="background: #90caf9; width: 20px; height: 20px;"></div>
                        <div class="badge" style="background: #42a5f5; width: 20px; height: 20px;"></div>
                        <div class="badge" style="background: #1e88e5; width: 20px; height: 20px;"></div>
                        <div class="badge" style="background: #1565c0; width: 20px; height: 20px;"></div>
                        <div class="badge" style="background: #0d47a1; width: 20px; height: 20px;"></div>
                    </div>
                    <span class="small text-muted ms-2">Yüksek</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy me-2"></i>
                    En Popüler Prompt'lar
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Prompt</th>
                                <th>Kullanım</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($heatmapData['popular_prompts']))
                                @foreach($heatmapData['popular_prompts'] as $index => $prompt)
                                @php
                                    $colors = ['green', 'blue', 'purple', 'orange', 'warning'];
                                    $icons = ['fire', 'search', 'edit', 'cog', 'map-marker-alt'];
                                    $color = $colors[$index % count($colors)];
                                    $icon = $icons[$index % count($icons)];
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar bg-{{ $color }}-lt me-2">
                                                <i class="fas fa-{{ $icon }} text-{{ $color }}"></i>
                                            </span>
                                            <div>
                                                <div class="fw-bold small">{{ $prompt['name'] }}</div>
                                                <div class="text-muted small">{{ $prompt['category'] ?? 'system' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $color }}-lt">{{ number_format($prompt['usage_count']) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 40px; height: 8px;">
                                                <div class="progress-bar bg-{{ $color }}" style="width: {{ $prompt['usage_percentage'] }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-{{ $color }}">{{ $prompt['usage_percentage'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Henüz prompt kullanım verisi bulunmuyor
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="#" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#promptDetailsModal">
                    <i class="fas fa-list me-2"></i>Tüm Prompt'ları Gör
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Feature Kullanım Haritası --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-puzzle-piece me-2"></i>
                    Feature Kullanım Yoğunluğu
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(!empty($heatmapData['feature_heatmap']))
                        @foreach($heatmapData['feature_heatmap'] as $feature)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-blue-lt">{{ $feature->feature_slug ?: 'chat' }}</span>
                                        <span class="small text-muted">{{ $feature->usage_count }} kullanım</span>
                                    </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        @php
                                            $percentage = min(100, ($feature->usage_count / 100) * 10);
                                            $color = $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : ($percentage > 20 ? 'info' : 'success'));
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="small text-muted">
                                        Ort. Süre: <span class="fw-bold">{{ round($feature->avg_time, 1) }}ms</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-info-circle fa-2x mb-3 opacity-50"></i>
                                <p>Feature kullanım verisi henüz bulunmuyor</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshHeatmap() {
    // Heatmap yenileme logic
    location.reload();
}
</script>
@endsection