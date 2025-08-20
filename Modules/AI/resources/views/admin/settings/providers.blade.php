@extends('admin.layout')

@include('ai::helper')

@section('title', 'AI Provider Yönetimi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">AI Provider Yönetimi</h2>
                        <div class="page-subtitle">AI servis sağlayıcılarını yönet ve yapılandır</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-robot me-2"></i>
                        AI Provider'lar
                    </h3>
                </div>
                <div class="card-body">
                    @if($providers->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Henüz AI provider tanımlanmamış.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Provider</th>
                                        <th>Model</th>
                                        <th>Durum</th>
                                        <th>Performans</th>
                                        <th>Öncelik</th>
                                        <th>Varsayılan</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($providers as $provider)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        @if($provider->name === 'openai')
                                                            <i class="fas fa-brain text-success" title="OpenAI"></i>
                                                        @elseif($provider->name === 'claude')
                                                            <i class="fas fa-robot text-purple" title="Claude"></i>
                                                        @else
                                                            <i class="fas fa-microchip text-info" title="DeepSeek"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $provider->display_name }}</strong>
                                                        <div class="text-muted small">{{ $provider->description }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $provider->default_model }}</span>
                                            </td>
                                            <td>
                                                @if($provider->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger">Pasif</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($provider->average_response_time)
                                                    <span class="badge bg-{{ $provider->average_response_time < 5000 ? 'success' : ($provider->average_response_time < 15000 ? 'warning' : 'danger') }}">
                                                        {{ number_format($provider->average_response_time / 1000, 1) }}s
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $provider->priority }}</span>
                                            </td>
                                            <td>
                                                @if($provider->is_default)
                                                    <span class="badge bg-warning">Varsayılan</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="editProvider({{ $provider->id }})"
                                                            title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="testProvider({{ $provider->id }})"
                                                            title="Test Et">
                                                        <i class="fas fa-vial"></i>
                                                    </button>
                                                    @if(!$provider->is_default)
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="makeDefault({{ $provider->id }})"
                                                                title="Varsayılan Yap">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Provider Edit Modal -->
<div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="providerModalLabel">Provider Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="providerForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Görünen Ad</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="api_key" class="form-label">API Anahtarı</label>
                        <input type="password" class="form-control" id="api_key" name="api_key" placeholder="Değiştirmek için yeni anahtarı girin">
                        <div class="form-text">Boş bırakırsanız mevcut anahtar korunur.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="default_model" class="form-label">Varsayılan Model</label>
                        <input type="text" class="form-control" id="default_model" name="default_model">
                    </div>
                    
                    <div class="mb-3">
                        <label for="priority" class="form-label">Öncelik</label>
                        <input type="number" class="form-control" id="priority" name="priority" min="0" max="100" value="0">
                        <div class="form-text">Yüksek öncelik numarası = daha öncelikli</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                                <label class="form-check-label" for="is_active">
                                    Aktif
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default">
                                <label class="form-check-label" for="is_default">
                                    Varsayılan
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentProviderId = null;
const providers = @json($providers);

function editProvider(providerId) {
    currentProviderId = providerId;
    const provider = providers.find(p => p.id === providerId);
    
    if (!provider) return;
    
    document.getElementById('display_name').value = provider.display_name;
    document.getElementById('api_key').value = ''; // API key'i gösterme
    document.getElementById('default_model').value = provider.default_model || '';
    document.getElementById('priority').value = provider.priority || 0;
    document.getElementById('description').value = provider.description || '';
    document.getElementById('is_active').checked = provider.is_active;
    document.getElementById('is_default').checked = provider.is_default;
    
    const modal = new bootstrap.Modal(document.getElementById('providerModal'));
    modal.show();
}

function testProvider(providerId) {
    const btn = event.target.closest('button');
    const originalContent = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;
    
    fetch(`/admin/ai/providers/${providerId}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            if (data.response) {
                toastr.info('Yanıt: ' + data.response);
            }
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('Test sırasında hata oluştu');
        console.error('Error:', error);
    })
    .finally(() => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
    });
}

function makeDefault(providerId) {
    if (!confirm('Bu provider\'ı varsayılan yapmak istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`/admin/ai/providers/${providerId}/make-default`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toastr.error(data.message);
        }
    })
    .catch(error => {
        toastr.error('İşlem sırasında hata oluştu');
        console.error('Error:', error);
    });
}

document.getElementById('providerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentProviderId) return;
    
    const formData = new FormData(this);
    
    fetch(`/admin/ai/providers/${currentProviderId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => {
        if (response.ok) {
            toastr.success('Provider başarıyla güncellendi');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toastr.error('Güncelleme sırasında hata oluştu');
        }
    })
    .catch(error => {
        toastr.error('Güncelleme sırasında hata oluştu');
        console.error('Error:', error);
    });
});
</script>
@endpush