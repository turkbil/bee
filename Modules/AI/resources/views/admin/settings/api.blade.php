@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Ayarları')
@section('title', 'API Yapılandırması')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <!-- AI Provider Seçimi -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-robot me-2"></i>
                        AI Provider Seçimi
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($providers->count() > 0)
                        <!-- Provider Kartları -->
                        <div class="row">
                            @foreach($providers as $provider)
                                <div class="col-md-4 mb-3">
                                    <div class="card provider-card {{ $activeProvider && $activeProvider->id === $provider->id ? 'border-primary bg-primary-lt' : '' }}">
                                        <div class="card-body text-center position-relative">
                                            <!-- Priority Badge -->
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-azure text-azure-fg">{{ is_array($provider->priority) ? json_encode($provider->priority) : ($provider->priority ?? 'N/A') }}</span>
                                            </div>
                                            
                                            <div class="mb-2 mt-3">
                                                @if($provider->name === 'openai')
                                                    <i class="fas fa-brain fa-2x text-green"></i>
                                                @elseif($provider->name === 'claude')
                                                    <i class="fas fa-robot fa-2x text-purple"></i>
                                                @else
                                                    <i class="fas fa-microchip fa-2x text-blue"></i>
                                                @endif
                                            </div>
                                            <h5 class="card-title">{{ $provider->display_name }}</h5>
                                            <p class="card-text text-muted">
                                                <small>{{ $provider->description }}</small>
                                            </p>
                                            
                                            <!-- Performance Badge -->
                                            @if($provider->average_response_time)
                                                <div class="mt-2">
                                                    <span class="badge bg-{{ $provider->average_response_time < 5000 ? 'green' : ($provider->average_response_time < 15000 ? 'yellow' : 'red') }}">
                                                        {{ number_format($provider->average_response_time / 1000, 1) }}s
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-2">
                                                @if($activeProvider && $activeProvider->id === $provider->id)
                                                    <span class="badge bg-primary">Aktif Provider</span>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="setActiveProvider({{ $provider->id }})">
                                                        Seç
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Provider'lar henüz yüklenmemiş. Lütfen seeder çalıştırın.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Aktif Provider Ayarları -->
            @if($activeProvider)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog me-2"></i>
                            {{ $activeProvider->display_name }} Ayarları
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.ai.settings.api.update') }}">
                        @csrf
                        <input type="hidden" name="provider_id" value="{{ $activeProvider->id }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control @error('api_key') is-invalid @enderror" 
                                               name="api_key" id="api_key" placeholder="API Key..." 
                                               value="{{ old('api_key') }}">
                                        <label for="api_key">API Anahtarı</label>
                                        @error('api_key')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-hint">
                                        @if(!empty($activeProvider->api_key))
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            API anahtarı kayıtlı: {{ substr($activeProvider->api_key, 0, 8) }}***{{ substr($activeProvider->api_key, -4) }}
                                            <br><small>Değiştirmek için yeni bir anahtar girin.</small>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                            {{ $activeProvider->display_name }} API anahtarınızı girin.
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-control @error('default_model') is-invalid @enderror" 
                                                name="default_model" id="default_model">
                                            @if($activeProvider->available_models)
                                                @foreach($activeProvider->available_models as $model)
                                                    @php
                                                        $modelValue = is_array($model) ? (isset($model['name']) ? $model['name'] : json_encode($model)) : $model;
                                                        $modelDisplay = is_array($model) ? (isset($model['display_name']) ? $model['display_name'] : $modelValue) : $model;
                                                    @endphp
                                                    <option value="{{ $modelValue }}" {{ $activeProvider->default_model == $modelValue ? 'selected' : '' }}>
                                                        {{ $modelDisplay }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <label for="default_model">Model</label>
                                        @error('default_model')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    @php
                                        $maxTokensDefault = is_array($activeProvider->default_settings) && isset($activeProvider->default_settings['max_tokens']) 
                                            ? $activeProvider->default_settings['max_tokens'] 
                                            : 800;
                                        $maxTokensValue = old('max_tokens', $maxTokensDefault);
                                    @endphp
                                    <div class="form-floating">
                                        <input type="number" class="form-control @error('max_tokens') is-invalid @enderror" 
                                               name="max_tokens" id="max_tokens" placeholder="800"
                                               value="{{ $maxTokensValue }}" 
                                               min="1">
                                        <label for="max_tokens">Maksimum Token</label>
                                        @error('max_tokens')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    @php
                                        $temperatureDefault = is_array($activeProvider->default_settings) && isset($activeProvider->default_settings['temperature']) 
                                            ? $activeProvider->default_settings['temperature'] 
                                            : 0.7;
                                        $temperature = old('temperature', $temperatureDefault);
                                    @endphp
                                    <label for="temperature" class="form-label">
                                        Temperature
                                        <span class="badge bg-primary text-white ms-2" id="temperature-value">{{ $temperature }}</span>
                                    </label>
                                    <div class="row align-items-center">
                                        <div class="col-2">
                                            <small class="text-muted">0.0<br><small>Deterministik</small></small>
                                        </div>
                                        <div class="col-8">
                                            <input type="range" class="form-range @error('temperature') is-invalid @enderror" 
                                                   name="temperature" id="temperature" 
                                                   min="0" max="2" step="0.1" 
                                                   value="{{ $temperature }}"
                                                   oninput="document.getElementById('temperature-value').textContent = this.value">
                                            @error('temperature')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-2 text-end">
                                            <small class="text-muted">2.0+<br><small>Yaratıcı</small></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Provider Bilgileri -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>Provider Bilgileri</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Base URL:</strong></td>
                                                <td>{{ $activeProvider->base_url ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Varsayılan Model:</strong></td>
                                                <td>{{ is_array($activeProvider->default_model) ? 'Array' : ($activeProvider->default_model ?? 'N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ortalama Yanıt Süresi:</strong></td>
                                                <td>
                                                    @if($activeProvider->average_response_time)
                                                        {{ number_format($activeProvider->average_response_time / 1000, 1) }} saniye
                                                    @else
                                                        Henüz test edilmemiş
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Öncelik:</strong></td>
                                                <td>{{ is_array($activeProvider->priority) ? json_encode($activeProvider->priority) : ($activeProvider->priority ?? 'N/A') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kullanılabilir Modeller:</strong></td>
                                                <td>
                                                    @if(is_array($activeProvider->available_models) && !empty($activeProvider->available_models))
                                                        @php
                                                            $modelList = array_map(function($model) {
                                                                return is_array($model) ? (isset($model['name']) ? $model['name'] : json_encode($model)) : $model;
                                                            }, $activeProvider->available_models);
                                                        @endphp
                                                        {{ implode(', ', $modelList) }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Kaydet
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="testProvider({{ $activeProvider->id }})">
                                    <i class="fas fa-vial me-2"></i>
                                    Provider Test Et
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Provider Karşılaştırma Tablosu -->
            @if($providers->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            AI Provider ve Model Karşılaştırması
                        </h3>
                        <div class="card-subtitle text-muted">
                            Provider seçmeden önce tüm seçenekleri ve maliyetleri görün
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th class="w-20">Provider</th>
                                        <th class="w-25">Model</th>
                                        <th class="w-12 text-center">Input<br><small class="text-muted">1M token</small></th>
                                        <th class="w-12 text-center">Output<br><small class="text-muted">1M token</small></th>
                                        <th class="w-10 text-center">Süre</th>
                                        <th class="w-11 text-center">Fiyat/Performans<br><small class="text-muted">1-10 skor</small></th>
                                        <th class="w-10 text-center">Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($providers as $provider)
                                        @if($provider->available_models && count($provider->available_models) > 0)
                                            @php 
                                                $modelCount = count($provider->available_models); 
                                                $isActiveProvider = $activeProvider && $activeProvider->id === $provider->id;
                                            @endphp
                                            @foreach($provider->available_models as $model)
                                                @php
                                                    $modelValue = is_array($model) ? (isset($model['name']) ? $model['name'] : json_encode($model)) : $model;
                                                    $modelDisplay = is_array($model) ? (isset($model['display_name']) ? $model['display_name'] : $modelValue) : $model;
                                                @endphp
                                                <tr class="{{ $isActiveProvider ? 'table-active' : '' }}">
                                                    @if($loop->first)
                                                        <td rowspan="{{ $modelCount }}" class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <span class="avatar avatar-sm me-3" style="background-color: {{ $provider->name === 'openai' ? 'var(--tblr-green)' : ($provider->name === 'claude' ? 'var(--tblr-purple)' : 'var(--tblr-blue)') }};">
                                                                    @if($provider->name === 'openai')
                                                                        <i class="fas fa-brain text-white"></i>
                                                                    @elseif($provider->name === 'claude')
                                                                        <i class="fas fa-robot text-white"></i>
                                                                    @else
                                                                        <i class="fas fa-microchip text-white"></i>
                                                                    @endif
                                                                </span>
                                                                <div>
                                                                    <div class="font-weight-medium">{{ $provider->display_name }}</div>
                                                                    <div class="text-muted">
                                                                        <small>{{ $provider->description }}</small>
                                                                    </div>
                                                                    @if($isActiveProvider)
                                                                        <span class="badge bg-primary mt-1">Aktif Provider</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                    
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <div class="font-weight-medium">{{ $modelDisplay }}</div>
                                                                @if($provider->default_model === $modelValue)
                                                                    <span class="badge bg-blue-lt text-blue mt-1">Varsayılan</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    
                                                    <!-- Sabit fiyat bilgileri (gerçek API entegrasyonu için güncellenebilir) -->
                                                    @php
                                                        // Gerçek 2025 fiyatları (1M token başına USD)
                                                        $modelPricing = [
                                                            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
                                                            'gpt-4o' => ['input' => 5.0, 'output' => 15.0],
                                                            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
                                                            'deepseek-chat' => ['input' => 0.27, 'output' => 1.10], // Cache miss pricing
                                                            'claude-3-haiku-20240307' => ['input' => 0.25, 'output' => 1.25],
                                                            'claude-3-sonnet-20240229' => ['input' => 3.0, 'output' => 15.0],
                                                        ];
                                                        
                                                        $pricing = $modelPricing[$modelValue] ?? ['input' => 0, 'output' => 0];
                                                        $inputCost = $pricing['input'];
                                                        $outputCost = $pricing['output'];
                                                        
                                                        // Fiyat/Performans skoru hesaplama
                                                        $responseTime = $provider->average_response_time ?? 20000;
                                                        $avgCost = ($inputCost * 0.3) + ($outputCost * 0.7);
                                                        $costScore = max(1, min(10, 10 - (($avgCost - 0.15) / 3) * 9));
                                                        $speedScore = max(1, min(10, 11 - (($responseTime - 1500) / 2500)));
                                                        $score = round(($costScore * 0.6) + ($speedScore * 0.4), 1);
                                                        $scoreColor = $score >= 8 ? 'green' : ($score >= 6 ? 'yellow' : ($score >= 4 ? 'orange' : 'red'));
                                                    @endphp
                                                    
                                                    <td class="text-center">
                                                        <span class="text-{{ $inputCost <= 0.5 ? 'green' : ($inputCost <= 2 ? 'yellow' : 'red') }} font-weight-medium">
                                                            ${{ number_format($inputCost, 2) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-{{ $outputCost <= 2 ? 'green' : ($outputCost <= 10 ? 'yellow' : 'red') }} font-weight-medium">
                                                            ${{ number_format($outputCost, 2) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-{{ $score >= 8 ? 'green' : ($score >= 6 ? 'yellow' : ($score >= 4 ? 'orange' : 'red')) }} font-weight-bold fs-4">{{ $score }}</span>
                                                        <div class="progress mt-1" style="height: 4px;">
                                                            <div class="progress-bar bg-{{ $scoreColor }}" style="width: {{ $score * 10 }}%"></div>
                                                        </div>
                                                    </td>
                                                    
                                                    @if($loop->first)
                                                        <td rowspan="{{ $modelCount }}" class="align-middle text-center">
                                                            @if($provider->average_response_time)
                                                                <span class="text-{{ $provider->average_response_time < 5000 ? 'green' : ($provider->average_response_time < 15000 ? 'yellow' : 'red') }} font-weight-medium">
                                                                    {{ number_format($provider->average_response_time / 1000, 1) }}s
                                                                </span>
                                                            @else
                                                                <span class="text-muted">Test edilmemiş</span>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $modelCount }}" class="align-middle text-center">
                                                            @if($provider->is_active)
                                                                <span class="status status-green">
                                                                    <span class="status-dot"></span>
                                                                    Aktif
                                                                </span>
                                                            @else
                                                                <span class="status status-gray">
                                                                    <span class="status-dot"></span>
                                                                    Pasif
                                                                </span>
                                                            @endif
                                                            <div class="mt-1">
                                                                <small class="text-muted">#{{ is_array($provider->priority) ? json_encode($provider->priority) : ($provider->priority ?? 'N/A') }}</small>
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Renk Kodları Açıklaması -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">Maliyet Renk Kodları</h6>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="text-green font-weight-medium">● Yeşil: En ucuz</span>
                                            <span class="text-yellow font-weight-medium">● Sarı: Orta</span>
                                            <span class="text-red font-weight-medium">● Kırmızı: Pahalı</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">Performans Renk Kodları</h6>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="text-green font-weight-medium">● Yeşil: &lt;5s</span>
                                            <span class="text-yellow font-weight-medium">● Sarı: 5-15s</span>
                                            <span class="text-red font-weight-medium">● Kırmızı: &gt;15s</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">Fiyat/Performans Skoru</h6>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="text-green font-weight-medium">● 8-10: Mükemmel</span>
                                            <span class="text-yellow font-weight-medium">● 6-7: İyi</span>
                                            <span class="text-orange font-weight-medium">● 4-5: Orta</span>
                                            <span class="text-red font-weight-medium">● 1-3: Kötü</span>
                                        </div>
                                        <small class="text-muted mt-2 d-block">Maliyet %60 + Hız %40</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- En İyi Seçenekler -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-lightbulb me-2"></i>Öneriler:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Varsayılan:</strong> OpenAI GPT-4o Mini ($0.15 input, $0.60 output) - Hızlı ve güvenilir</li>
                                        <li><strong>En Ucuz:</strong> DeepSeek Chat ($0.27 input, $1.10 output) - Fallback seçeneği</li>
                                        <li><strong>En Güçlü:</strong> Claude 3 Sonnet ($3.0 input, $15.0 output) - Reasoning</li>
                                        <li><strong>En Pahalı:</strong> OpenAI GPT-4o ($5.0 input, $15.0 output) - En gelişmiş</li>
                                        <li><small class="text-muted">💡 Fiyatlar 2025 resmi API dokumentasyonlarından alınmıştır (1M token başına USD)</small></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<style>
.provider-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid var(--tblr-border-color);
}

.provider-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(var(--tblr-primary-rgb), 0.15);
    border-color: var(--tblr-primary);
}

.provider-card.border-primary {
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 2px 8px rgba(var(--tblr-primary-rgb), 0.2);
}

.provider-card.bg-primary-lt {
    background-color: var(--tblr-primary-lt) !important;
}
</style>

<script>
function setActiveProvider(providerId) {
    if (confirm('Bu provider\'ı aktif yapmak istediğinizden emin misiniz?')) {
        // Form data olarak gönder
        const formData = new FormData();
        formData.append('action', 'set_active_provider');
        formData.append('active_provider', providerId);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("admin.ai.settings.api.update") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            alert('Provider değiştirme sırasında hata oluştu');
            console.error('Error:', error);
        });
    }
}

function testProvider(providerId) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test Ediliyor...';
    button.disabled = true;
    
    fetch('{{ route("admin.ai.settings.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            provider_id: providerId,
            test_message: 'Merhaba, test mesajı'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let message = '✅ Test başarılı!\n\n';
            message += '🏷️ Provider: ' + (data.provider_name || 'N/A') + '\n';
            message += '🔗 API Endpoint: ' + (data.api_endpoint || 'N/A') + '\n';
            message += '🤖 Model: ' + (data.model_used || 'N/A') + '\n';
            message += '⏱️ Yanıt Süresi: ' + (data.response_time || 'N/A') + 'ms\n\n';
            message += '💬 Yanıt: ' + (data.response || 'Boş yanıt');
            alert(message);
        } else {
            alert('❌ Test başarısız!\n\nHata: ' + data.message);
        }
        button.innerHTML = originalText;
        button.disabled = false;
    })
    .catch(error => {
        alert('Test sırasında hata oluştu');
        button.innerHTML = originalText;
        button.disabled = false;
        console.error('Error:', error);
    });
}
</script>
@endpush