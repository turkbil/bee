@extends('admin.layout')

@section('title', 'Central Fallback Yönetimi')

@section('page_title', 'Central Fallback Configuration')

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.ai.index') }}">AI Yönetimi</a></li>
    <li class="breadcrumb-item active">Central Fallback</li>
@endpush

@section('content')
<div class="container-xl">
    
    <!-- Configuration Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Merkezi Fallback Konfigürasyonu</h3>
                    <div class="card-actions">
                        <button class="btn btn-outline-success btn-sm" onclick="testConfiguration()">
                            <i class="fa fa-check-circle me-1"></i>
                            Konfigürasyonu Test Et
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="clearCache()">
                            <i class="fa fa-refresh me-1"></i>
                            Cache Temizle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Fallback Etkin</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           id="fallbackEnabled" {{ $config['fallback_enabled'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="fallbackEnabled">
                                        {{ $config['fallback_enabled'] ? 'Etkin' : 'Pasif' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Max Fallback Denemesi</label>
                                <input type="number" class="form-control" 
                                       value="{{ $config['max_fallback_attempts'] }}" 
                                       id="maxFallbackAttempts" min="1" max="10">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Fallback Timeout (saniye)</label>
                                <input type="number" class="form-control" 
                                       value="{{ $config['fallback_timeout'] }}" 
                                       id="fallbackTimeout" min="5" max="120">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Maliyet Tercihi</label>
                                <select class="form-select" id="costPreference">
                                    @foreach($config['strategies'] as $key => $label)
                                        <option value="{{ $key }}" {{ $config['cost_preference'] === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" onclick="saveConfiguration()">
                        <i class="fa fa-save me-1"></i>
                        Konfigürasyonu Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Order Configuration -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Provider Öncelik Sırası</h3>
                    <div class="card-actions">
                        <span class="badge bg-info">{{ count($providerOrder) }} Provider</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Mevcut Sıralama</h5>
                            <div id="providerOrderList" class="list-group">
                                @foreach($providerOrder as $index => $providerName)
                                    <div class="list-group-item d-flex justify-content-between align-items-center" 
                                         data-provider="{{ $providerName }}">
                                        <div>
                                            <span class="badge badge-primary me-2">{{ $index + 1 }}</span>
                                            <strong>{{ $providerName }}</strong>
                                        </div>
                                        <div>
                                            @if($index > 0)
                                                <button class="btn btn-sm btn-outline-secondary" onclick="moveProviderUp('{{ $providerName }}')">
                                                    <i class="fa fa-arrow-up"></i>
                                                </button>
                                            @endif
                                            @if($index < count($providerOrder) - 1)
                                                <button class="btn btn-sm btn-outline-secondary" onclick="moveProviderDown('{{ $providerName }}')">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Kullanılabilir Provider'lar</h5>
                            <div class="list-group">
                                @foreach($allProviders as $provider)
                                    @if(!in_array($provider->name, $providerOrder))
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $provider->name }}</span>
                                            <button class="btn btn-sm btn-outline-primary" onclick="addProviderToOrder('{{ $provider->name }}')">
                                                <i class="fa fa-plus"></i> Ekle
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Central Fallback İstatistikleri</h3>
                    <div class="card-actions">
                        <button class="btn btn-outline-danger btn-sm" onclick="clearStatistics()">
                            <i class="fa fa-trash me-1"></i>
                            İstatistikleri Temizle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="statisticsContainer">
                        <div class="col-md-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-primary text-white avatar">
                                                <i class="fa fa-server"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ $statistics['total_requests'] ?? 0 }}
                                            </div>
                                            <div class="text-muted">
                                                Toplam İstek
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-warning text-white avatar">
                                                <i class="fa fa-exchange-alt"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ $statistics['fallback_requests'] ?? 0 }}
                                            </div>
                                            <div class="text-muted">
                                                Fallback İsteği
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-success text-white avatar">
                                                <i class="fa fa-check-circle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ number_format($statistics['fallback_success_rate'] ?? 0, 1) }}%
                                            </div>
                                            <div class="text-muted">
                                                Başarı Oranı
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-info text-white avatar">
                                                <i class="fa fa-percentage"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {{ number_format($statistics['fallback_rate'] ?? 0, 1) }}%
                                            </div>
                                            <div class="text-muted">
                                                Fallback Oranı
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

    <!-- Provider Health Status -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Provider Sağlık Durumu</h3>
                    <div class="card-actions">
                        <button class="btn btn-outline-info btn-sm" onclick="refreshProviderHealth()">
                            <i class="fa fa-sync me-1"></i>
                            Sağlık Durumu Yenile
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter" id="providerHealthTable">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Durum</th>
                                    <th>Hata Oranı</th>
                                    <th>Yanıt Süresi</th>
                                    <th>Son Kontrol</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProviders as $provider)
                                    @php
                                        $health = $providerHealth[$provider->name] ?? ['status' => 'unknown'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $provider->name }}</strong>
                                        </td>
                                        <td>
                                            @switch($health['status'] ?? 'unknown')
                                                @case('healthy')
                                                    <span class="badge bg-success">Sağlıklı</span>
                                                    @break
                                                @case('degraded')
                                                    <span class="badge bg-warning">Performans Düşüklüğü</span>
                                                    @break
                                                @case('unavailable')
                                                    <span class="badge bg-danger">Kullanılamaz</span>
                                                    @break
                                                @case('maintenance')
                                                    <span class="badge bg-info">Bakım</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">Bilinmiyor</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ number_format($health['failure_rate'] ?? 0, 1) }}%
                                            @if(isset($health['recent_failures']) && $health['recent_failures'] > 0)
                                                <small class="text-muted">({{ $health['recent_failures'] }} hata)</small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ number_format($health['avg_response_time'] ?? 0) }}ms
                                        </td>
                                        <td>
                                            @if(isset($health['last_checked']))
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($health['last_checked'])->diffForHumans() }}
                                                </small>
                                            @else
                                                <small class="text-muted">Hiç kontrol edilmedi</small>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="testProvider('{{ $provider->name }}')">
                                                <i class="fa fa-check"></i> Test Et
                                            </button>
                                            @if(isset($health['recent_failures']) && $health['recent_failures'] > 0)
                                                <button class="btn btn-sm btn-outline-warning" 
                                                        onclick="resetProviderFailures('{{ $provider->name }}')">
                                                    <i class="fa fa-refresh"></i> Reset
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Configuration Management
    async function saveConfiguration() {
        const config = {
            fallback_enabled: document.getElementById('fallbackEnabled').checked,
            max_fallback_attempts: parseInt(document.getElementById('maxFallbackAttempts').value),
            fallback_timeout: parseInt(document.getElementById('fallbackTimeout').value),
            cost_preference: document.getElementById('costPreference').value,
            _token: '{{ csrf_token() }}'
        };
        
        try {
            const response = await fetch('{{ route("admin.ai.central-fallback.configuration") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(config)
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Konfigürasyon başarıyla kaydedildi!');
            } else {
                alert('Hata: ' + result.message);
            }
        } catch (error) {
            alert('Bir hata oluştu: ' + error.message);
        }
    }
    
    // Test Configuration
    async function testConfiguration() {
        try {
            const response = await fetch('{{ route("admin.ai.central-fallback.test") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.config_valid) {
                alert(`Test Başarılı!\nYürütme Süresi: ${result.execution_time_ms}ms\nTest Edilen Provider Sayısı: ${Object.keys(result.provider_tests).length}`);
            } else {
                alert('Test Başarısız: Konfigürasyon geçersiz');
            }
        } catch (error) {
            alert('Test sırasında hata: ' + error.message);
        }
    }
    
    // Clear Cache
    async function clearCache() {
        try {
            const response = await fetch('{{ route("admin.ai.central-fallback.clear-statistics") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Cache başarıyla temizlendi!');
                location.reload();
            } else {
                alert('Cache temizleme hatası: ' + result.message);
            }
        } catch (error) {
            alert('Hata: ' + error.message);
        }
    }
    
    // Provider Order Management
    function moveProviderUp(providerName) {
        // Implementation for moving provider up in order
        console.log('Move up:', providerName);
    }
    
    function moveProviderDown(providerName) {
        // Implementation for moving provider down in order
        console.log('Move down:', providerName);
    }
    
    function addProviderToOrder(providerName) {
        // Implementation for adding provider to order
        console.log('Add to order:', providerName);
    }
    
    // Provider Health Management
    async function refreshProviderHealth() {
        try {
            const response = await fetch('{{ route("admin.ai.central-fallback.statistics") }}');
            const result = await response.json();
            
            // Update table with new health data
            location.reload(); // Simple reload for now
        } catch (error) {
            alert('Sağlık durumu yenileme hatası: ' + error.message);
        }
    }
    
    async function testProvider(providerName) {
        alert(`${providerName} provider'ı test ediliyor...`);
        // Implementation for testing specific provider
    }
    
    async function resetProviderFailures(providerName) {
        try {
            const response = await fetch('{{ route("admin.ai.central-fallback.reset-failures") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ provider: providerName })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`${providerName} provider'ının hata sayacı sıfırlandı!`);
                location.reload();
            } else {
                alert('Reset işlemi başarısız: ' + result.message);
            }
        } catch (error) {
            alert('Hata: ' + error.message);
        }
    }
    
    // Statistics Management
    async function clearStatistics() {
        if (confirm('Tüm istatistikleri temizlemek istediğinizden emin misiniz?')) {
            try {
                const response = await fetch('{{ route("admin.ai.central-fallback.clear-statistics") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('İstatistikler başarıyla temizlendi!');
                    location.reload();
                } else {
                    alert('İstatistik temizleme hatası: ' + result.message);
                }
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        }
    }
</script>
@endpush

@endsection