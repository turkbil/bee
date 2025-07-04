@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Ayarları')
@section('title', 'API Yapılandırması')

@section('content')
    <div class="row">
        <div class="col-3">
            @include('ai::admin.settings.sidebar')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key me-2"></i>
                        API Ayarları
                    </h3>
                </div>
                <form method="POST" action="{{ route('admin.ai.settings.api.update') }}">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control @error('api_key') is-invalid @enderror" 
                                           name="api_key" id="api_key" placeholder="sk-..." 
                                           value="{{ old('api_key') }}">
                                    <label for="api_key">API Anahtarı</label>
                                    @error('api_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">DeepSeek API anahtarınızı girin. Boş bırakırsanız mevcut anahtar korunur.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-floating">
                                    <select class="form-control @error('model') is-invalid @enderror" 
                                            name="model" id="model">
                                        <option value="deepseek-chat" {{ ($settings->model ?? 'deepseek-chat') == 'deepseek-chat' ? 'selected' : '' }}>
                                            DeepSeek Chat
                                        </option>
                                        <option value="deepseek-coder" {{ ($settings->model ?? '') == 'deepseek-coder' ? 'selected' : '' }}>
                                            DeepSeek Coder
                                        </option>
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
                                           name="max_tokens" id="max_tokens" placeholder="4096"
                                           value="{{ old('max_tokens', $settings->max_tokens ?? 4096) }}" 
                                           min="1">
                                    <label for="max_tokens">Maksimum Token</label>
                                    @error('max_tokens')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-hint">Minimum 1 token gerekli</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="temperature" class="form-label">
                                    Temperature
                                    <span class="badge bg-primary ms-2" id="temperature-value">{{ old('temperature', $settings->temperature ?? 0.7) }}</span>
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
                                <div class="form-hint text-center mt-2">
                                    <small class="text-muted">Sürükleyerek AI yaratıcılık seviyesini ayarlayın</small>
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
                    </div>
                    <div class="card-footer">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Kaydet
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" onclick="testConnection()">
                                <i class="fas fa-plug me-2"></i>
                                Bağlantıyı Test Et
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function testConnection() {
    const apiKey = document.querySelector('input[name="api_key"]').value;
    const button = event.target;
    const originalText = button.innerHTML;
    
    if (!apiKey) {
        alert('API anahtarı gerekli');
        return;
    }
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test Ediliyor...';
    button.disabled = true;
    
    fetch('{{ route("admin.ai.settings.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ api_key: apiKey })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        button.innerHTML = originalText;
        button.disabled = false;
    })
    .catch(error => {
        alert('Test sırasında hata oluştu');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}
</script>
@endpush