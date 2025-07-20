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

                    @php
                        $providers = $settings->providers ?? [];
                        $activeProvider = $settings->active_provider ?? 'deepseek';
                    @endphp

                    @if(!empty($providers))
                        <!-- Sürükle Bırak Provider Sıralaması -->
                        <div class="mb-4">
                            <h6><i class="fas fa-sort me-2"></i>Provider Öncelik Sıralaması (Sürükle-Bırak)</h6>
                            <div id="provider-sortable" class="row">
                                @php 
                                    $sortedProviders = collect($providers)->sortBy('priority')->toArray();
                                @endphp
                                @foreach($sortedProviders as $key => $provider)
                                    <div class="col-md-4 mb-3 provider-item" data-provider="{{ $key }}">
                                        <div class="card provider-card {{ $activeProvider === $key ? 'border-primary bg-primary-lt' : '' }}">
                                            <div class="card-body text-center position-relative">
                                                <!-- Drag Handle -->
                                                <div class="drag-handle position-absolute top-0 start-0 m-2 text-muted" style="cursor: move;">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </div>
                                                
                                                <!-- Priority Badge -->
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-azure text-azure-fg">{{ $provider['priority'] ?? 'N/A' }}</span>
                                                </div>
                                                
                                                <div class="mb-2 mt-3">
                                                    @if($key === 'openai')
                                                        <i class="fas fa-brain fa-2x text-green"></i>
                                                    @elseif($key === 'claude')
                                                        <i class="fas fa-robot fa-2x text-purple"></i>
                                                    @else
                                                        <i class="fas fa-microchip fa-2x text-blue"></i>
                                                    @endif
                                                </div>
                                                <h5 class="card-title">{{ $provider['name'] ?? $key }}</h5>
                                                <p class="card-text text-muted">
                                                    <small>{{ $provider['description'] ?? '' }}</small>
                                                </p>
                                                
                                                <!-- Performance Badge -->
                                                @if(isset($provider['average_response_time']))
                                                    <div class="mt-2">
                                                        <span class="badge bg-{{ $provider['average_response_time'] < 5000 ? 'green' : ($provider['average_response_time'] < 15000 ? 'yellow' : 'red') }}">
                                                            {{ number_format($provider['average_response_time'] / 1000, 1) }}s
                                                        </span>
                                                    </div>
                                                @endif
                                                <div class="mt-2">
                                                    @if($activeProvider === $key)
                                                        <span class="badge bg-primary">Aktif Provider</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="setActiveProvider('{{ $key }}')">
                                                            Seç
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kartları sürükleyerek öncelik sırasını değiştirebilirsiniz. Sağ üstteki sayı öncelik değeridir.
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Provider'lar henüz yüklenmemiş. Lütfen migration çalıştırın.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Aktif Provider Ayarları -->
            @if(!empty($providers) && isset($providers[$activeProvider]))
                @php $currentProvider = $providers[$activeProvider]; @endphp
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog me-2"></i>
                            {{ $currentProvider['name'] ?? $activeProvider }} Ayarları
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('admin.ai.settings.api.update') }}">
                        @csrf
                        <input type="hidden" name="provider" value="{{ $activeProvider }}">
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
                                        @if(!empty($currentProvider['api_key']))
                                            <i class="fas fa-check-circle text-success me-1"></i>
                                            API anahtarı kayıtlı: {{ substr($currentProvider['api_key'], 0, 8) }}***{{ substr($currentProvider['api_key'], -4) }}
                                            <br><small>Değiştirmek için yeni bir anahtar girin.</small>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                            {{ $currentProvider['name'] ?? $activeProvider }} API anahtarınızı girin.
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <select class="form-control @error('model') is-invalid @enderror" 
                                                name="model" id="model">
                                            @if(isset($currentProvider['available_models']))
                                                @foreach($currentProvider['available_models'] as $modelKey => $modelData)
                                                    @if(is_array($modelData))
                                                        <option value="{{ $modelKey }}" {{ ($currentProvider['model'] ?? '') == $modelKey ? 'selected' : '' }}>
                                                            {{ $modelData['name'] }} - ${{ number_format($modelData['input_cost'], 2) }}/1M input, ${{ number_format($modelData['output_cost'], 2) }}/1M output
                                                            @if(isset($modelData['discounted_input']))
                                                                (İndirimli: ${{ number_format($modelData['discounted_input'], 2) }}/${{ number_format($modelData['discounted_output'], 2) }})
                                                            @endif
                                                        </option>
                                                    @else
                                                        <option value="{{ $modelData }}" {{ ($currentProvider['model'] ?? '') == $modelData ? 'selected' : '' }}>
                                                            {{ $modelData }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <label for="model">Model</label>
                                        @error('model')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control @error('max_tokens') is-invalid @enderror" 
                                               name="max_tokens" id="max_tokens" placeholder="800"
                                               value="{{ old('max_tokens', $settings->max_tokens ?? 800) }}" 
                                               min="1">
                                        <label for="max_tokens">Maksimum Token</label>
                                        @error('max_tokens')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="temperature" class="form-label">
                                        Temperature
                                        <span class="badge bg-primary text-white ms-2" id="temperature-value">{{ old('temperature', $settings->temperature ?? 0.7) }}</span>
                                    </label>
                                    <div class="row align-items-center">
                                        <div class="col-2">
                                            <small class="text-muted">0.0<br><small>Deterministik</small></small>
                                        </div>
                                        <div class="col-8">
                                            <input type="range" class="form-range @error('temperature') is-invalid @enderror" 
                                                   name="temperature" id="temperature" 
                                                   min="0" max="2" step="0.1" 
                                                   value="{{ old('temperature', $settings->temperature ?? 0.7) }}"
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
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch form-check-lg">
                                        <input class="form-check-input" type="checkbox" name="enabled" value="1" 
                                               id="enabled" {{ old('enabled', $settings->enabled ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enabled">
                                            AI Servisi Aktif
                                        </label>
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
                                                <td>{{ $currentProvider['base_url'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Varsayılan Model:</strong></td>
                                                <td>{{ $currentProvider['model'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ortalama Yanıt Süresi:</strong></td>
                                                <td>
                                                    @if(isset($currentProvider['average_response_time']))
                                                        {{ number_format($currentProvider['average_response_time'] / 1000, 1) }} saniye
                                                    @else
                                                        Henüz test edilmemiş
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Öncelik:</strong></td>
                                                <td>{{ $currentProvider['priority'] ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kullanılabilir Modeller:</strong></td>
                                                <td>
                                                    @if(isset($currentProvider['available_models']) && is_array($currentProvider['available_models']))
                                                        @foreach($currentProvider['available_models'] as $modelKey => $modelData)
                                                            @if(is_array($modelData))
                                                                <div class="mb-1">
                                                                    <strong>{{ $modelData['name'] }}:</strong><br>
                                                                    <small class="text-muted">
                                                                        Input: ${{ number_format($modelData['input_cost'], 2) }}/1M token | 
                                                                        Output: ${{ number_format($modelData['output_cost'], 2) }}/1M token
                                                                        @if(isset($modelData['discounted_input']))
                                                                            <br><span class="text-success">İndirimli: ${{ number_format($modelData['discounted_input'], 2) }}/${{ number_format($modelData['discounted_output'], 2) }}</span>
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            @else
                                                                <div class="mb-1">{{ $modelData }}</div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>En Ucuz Model (Input):</strong></td>
                                                <td>
                                                    @if(isset($currentProvider['cost_per_1k_tokens']))
                                                        <span class="badge bg-success text-white">
                                                            ${{ number_format($currentProvider['cost_per_1k_tokens'], 5) }}/1K token
                                                        </span>
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
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="testProvider('{{ $activeProvider }}')">
                                    <i class="fas fa-vial me-2"></i>
                                    Provider Test Et
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

        </div>
        
        <!-- Tüm Provider Karşılaştırma Tablosu -->
        <div class="col-12">
            @php
                $providers = $settings->providers ?? [];
            @endphp
            
            @if(!empty($providers))
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-bar me-2"></i>
                            Tüm AI Provider ve Model Karşılaştırması
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
                                    @foreach($providers as $providerKey => $provider)
                                        @if(isset($provider['available_models']) && is_array($provider['available_models']))
                                            @php 
                                                $modelCount = count($provider['available_models']); 
                                            @endphp
                                            @foreach($provider['available_models'] as $modelKey => $modelData)
                                                <tr class="{{ $activeProvider === $providerKey ? 'table-active' : '' }}">
                                                    @if($loop->first)
                                                        <td rowspan="{{ $modelCount }}" class="align-middle">
                                                            <div class="d-flex align-items-center">
                                                                <span class="avatar avatar-sm me-3" style="background-color: {{ $providerKey === 'openai' ? 'var(--tblr-green)' : ($providerKey === 'claude' ? 'var(--tblr-purple)' : 'var(--tblr-blue)') }};">
                                                                    @if($providerKey === 'openai')
                                                                        <i class="fas fa-brain text-white"></i>
                                                                    @elseif($providerKey === 'claude')
                                                                        <i class="fas fa-robot text-white"></i>
                                                                    @else
                                                                        <i class="fas fa-microchip text-white"></i>
                                                                    @endif
                                                                </span>
                                                                <div>
                                                                    <div class="font-weight-medium">{{ $provider['name'] }}</div>
                                                                    <div class="text-muted">
                                                                        <small>{{ $provider['description'] ?? '' }}</small>
                                                                    </div>
                                                                    @if($activeProvider === $providerKey)
                                                                        <span class="badge bg-primary mt-1">Aktif Provider</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                    
                                                    @if(is_array($modelData))
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <div class="font-weight-medium">{{ $modelData['name'] }}</div>
                                                                    @if($provider['model'] === $modelKey)
                                                                        <span class="badge bg-blue-lt text-blue mt-1">Varsayılan</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($modelData['discounted_input']))
                                                                <span class="text-green font-weight-medium">
                                                                    ${{ number_format($modelData['discounted_input'], 2) }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    (Normal: ${{ number_format($modelData['input_cost'], 2) }})
                                                                </small>
                                                            @else
                                                                <span class="text-{{ $modelData['input_cost'] <= 0.5 ? 'green' : ($modelData['input_cost'] <= 2 ? 'yellow' : 'red') }} font-weight-medium">
                                                                    ${{ number_format($modelData['input_cost'], 2) }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if(isset($modelData['discounted_output']))
                                                                <span class="text-green font-weight-medium">
                                                                    ${{ number_format($modelData['discounted_output'], 2) }}
                                                                </span>
                                                                <small class="text-muted d-block">
                                                                    (Normal: ${{ number_format($modelData['output_cost'], 2) }})
                                                                </small>
                                                            @else
                                                                <span class="text-{{ $modelData['output_cost'] <= 2 ? 'green' : ($modelData['output_cost'] <= 10 ? 'yellow' : 'red') }} font-weight-medium">
                                                                    ${{ number_format($modelData['output_cost'], 2) }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                // Fiyat-Performans Skoru Hesaplama (1M token bazında)
                                                                // DeepSeek indirimli kullanım (mevcutsa)
                                                                $inputCost = isset($modelData['discounted_input']) ? $modelData['discounted_input'] : $modelData['input_cost'];
                                                                $outputCost = isset($modelData['discounted_output']) ? $modelData['discounted_output'] : $modelData['output_cost'];
                                                                $responseTime = $provider['average_response_time'] ?? 20000;
                                                                
                                                                // Toplam maliyet (input + output ağırlıklı ortalama, output daha ağır)
                                                                $avgCost = ($inputCost * 0.3) + ($outputCost * 0.7);
                                                                
                                                                // Maliyet skoru (tersine - en ucuz en yüksek puan)
                                                                // En ucuz: GPT-4.1 nano ($0.02) = 10 puan
                                                                // En pahalı: Claude Opus ($75) = 1 puan
                                                                $costScore = max(1, min(10, 10 - (($avgCost - 0.02) / 15) * 9));
                                                                
                                                                // Performans skoru (hız)
                                                                // 1500ms = 10 puan, 25000ms = 1 puan
                                                                $speedScore = max(1, min(10, 11 - (($responseTime - 1500) / 2500)));
                                                                
                                                                // Toplam skor (maliyet %70, hız %30 - fiyat daha önemli)
                                                                $score = round(($costScore * 0.7) + ($speedScore * 0.3), 1);
                                                                
                                                                $scoreColor = $score >= 8 ? 'green' : ($score >= 6 ? 'yellow' : ($score >= 4 ? 'orange' : 'red'));
                                                            @endphp
                                                            <span class="text-{{ $scoreColor }} font-weight-bold fs-4">{{ $score }}</span>
                                                            <div class="progress mt-1" style="height: 4px;">
                                                                <div class="progress-bar bg-{{ $scoreColor }}" style="width: {{ $score * 10 }}%"></div>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td>{{ $modelData }}</td>
                                                        <td colspan="3" class="text-center"><span class="text-muted">Bilgi yok</span></td>
                                                    @endif
                                                    
                                                    @if($loop->first)
                                                        <td rowspan="{{ $modelCount }}" class="align-middle text-center">
                                                            @if(isset($provider['average_response_time']))
                                                                <span class="text-{{ $provider['average_response_time'] < 5000 ? 'green' : ($provider['average_response_time'] < 15000 ? 'yellow' : 'red') }} font-weight-medium">
                                                                    {{ number_format($provider['average_response_time'] / 1000, 1) }}s
                                                                </span>
                                                            @else
                                                                <span class="text-muted">Test edilmemiş</span>
                                                            @endif
                                                        </td>
                                                        <td rowspan="{{ $modelCount }}" class="align-middle text-center">
                                                            @if($provider['is_active'])
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
                                                                <small class="text-muted">#{{ $provider['priority'] }}</small>
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
                                        <li><strong>En Ucuz:</strong> DeepSeek Chat ($0.00027 input, $0.0011 output) - Ama yavaş (24s)</li>
                                        <li><strong>Hız/Fiyat Dengesi:</strong> OpenAI GPT-4o Mini ($0.00015 input, $0.0006 output) - Hızlı (1.6s)</li>
                                        <li><strong>En Güçlü:</strong> Claude 3 Sonnet ($0.003 input, $0.015 output) - Reasoning</li>
                                        <li><strong>En Pahalı:</strong> OpenAI GPT-4o ($0.005 input, $0.02 output) - En gelişmiş</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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

.provider-item {
    transition: all 0.3s ease;
}

.provider-item.sortable-chosen {
    transform: rotate(5deg);
    opacity: 0.8;
}

.provider-item.sortable-ghost {
    opacity: 0.3;
}

.drag-handle:hover {
    color: var(--tblr-primary) !important;
}

/* Tabler.io uyumlu badge'ler */
.badge.bg-green {
    background-color: var(--tblr-green) !important;
    color: #fff !important;
}

.badge.bg-yellow {
    background-color: var(--tblr-yellow) !important;
    color: #fff !important;
}

.badge.bg-red {
    background-color: var(--tblr-red) !important;
    color: #fff !important;
}

.badge.bg-blue {
    background-color: var(--tblr-blue) !important;
    color: #fff !important;
}

.badge.bg-azure {
    background-color: var(--tblr-azure) !important;
    color: #fff !important;
}
</style>

<script>
function setActiveProvider(providerName) {
    if (confirm(`${providerName} provider'ını aktif yapmak istediğinizden emin misiniz?`)) {
        // Form data olarak gönder
        const formData = new FormData();
        formData.append('action', 'set_active_provider');
        formData.append('active_provider', providerName);
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

function testProvider(providerName) {
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
            provider: providerName,
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

// Sortable.js ile sürükle-bırak
document.addEventListener('DOMContentLoaded', function() {
    const sortableElement = document.getElementById('provider-sortable');
    if (sortableElement) {
        new Sortable(sortableElement, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.drag-handle',
            onEnd: function(evt) {
                // Yeni sırayı hesapla
                const items = Array.from(sortableElement.children);
                const newOrder = items.map((item, index) => ({
                    provider: item.getAttribute('data-provider'),
                    priority: index + 1
                }));
                
                // Öncelik güncelleme isteği gönder
                updateProviderPriorities(newOrder);
            }
        });
    }
});

function updateProviderPriorities(newOrder) {
    fetch('{{ route("admin.ai.settings.api.update-priorities") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            priorities: newOrder
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Priority badge'lerini güncelle
            newOrder.forEach(item => {
                const card = document.querySelector(`[data-provider="${item.provider}"]`);
                if (card) {
                    const badge = card.querySelector('.badge.bg-azure');
                    if (badge) {
                        badge.textContent = item.priority;
                    }
                }
            });
            
            // Toast bildirimi (isteğe bağlı)
            console.log('Provider öncelikleri güncellendi');
        } else {
            alert('Öncelik güncelleme hatası: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Öncelik güncelleme sırasında hata oluştu');
    });
}
</script>

<!-- Sortable.js dahil et -->
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
@endpush