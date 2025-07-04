@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', 'Token Yönetimi')

@section('content')
<!-- System Overview Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Kiracı</div>
                </div>
                <div class="h1 mb-3">{{ number_format($systemStats['total_tenants']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kayıtlı Kiracı</div>
                    <div class="ms-auto">
                        <span class="badge badge-outline text-green">
                            {{ number_format($systemStats['active_ai_tenants']) }} Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Toplam Dağıtılan Token</div>
                </div>
                <div class="h1 mb-3">{{ \App\Helpers\TokenHelper::format($systemStats['total_tokens_distributed']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Token</div>
                    <div class="ms-auto">
                        <span class="badge badge-outline text-blue">
                            {{ \App\Helpers\TokenHelper::format($systemStats['total_tokens_used']) }} Kullanıldı
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Aktif Token Bakiyesi</div>
                </div>
                <div class="h1 mb-3">{{ \App\Helpers\TokenHelper::format($systemStats['total_tokens_distributed'] - $systemStats['total_tokens_used']) }}</div>
                <div class="d-flex mb-2">
                    <div class="text-muted">Kullanılabilir Token</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Hızlı İşlemler</div>
                </div>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.ai.tokens.statistics.overview') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>İstatistikler
                    </a>
                    <a href="{{ route('admin.ai.tokens.packages') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-box me-2"></i>Paketler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hızlı İşlemler</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.ai.tokens.purchases') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Satın Alımları Görüntüle
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.ai.tokens.usage-stats') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-chart-bar me-2"></i>
                            Kullanım İstatistikleri
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.ai.tokens.statistics.overview') }}" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-chart-line me-2"></i>
                            Genel İstatistikler
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.ai.tokens.packages') }}" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-box me-2"></i>
                            Token Paketleri
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tenant List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kiracı Token Yönetimi</h3>
            </div>
            <div class="card-body">
                @if($tenants->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Kiracı</th>
                                <th>AI Durumu</th>
                                <th>Token Bakiyesi</th>
                                <th>Aylık Kullanım</th>
                                <th>Aylık Limit</th>
                                <th>Son Kullanım</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenants as $tenant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong>{{ $tenant->title }}</strong>
                                            <div class="text-muted">ID: {{ $tenant->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-outline {{ $tenant->ai_enabled ? 'text-green' : 'text-red' }}">
                                        {{ $tenant->ai_enabled ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-blue">
                                        {{ \App\Helpers\TokenHelper::format($tenant->ai_tokens_balance) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-orange">
                                        {{ \App\Helpers\TokenHelper::format($tenant->ai_tokens_used_this_month) }}
                                    </span>
                                </td>
                                <td>
                                    @if($tenant->ai_monthly_token_limit > 0)
                                        <span class="badge badge-outline text-purple">
                                            {{ \App\Helpers\TokenHelper::format($tenant->ai_monthly_token_limit) }}
                                        </span>
                                    @else
                                        <span class="text-muted">Sınırsız</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tenant->ai_last_used_at)
                                        {{ $tenant->ai_last_used_at->diffForHumans() }}
                                    @else
                                        <span class="text-muted">Henüz kullanılmadı</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <a href="{{ route('admin.ai.tokens.show', $tenant->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Detayları Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.ai.tokens.tenant-statistics', $tenant->id) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="İstatistikler">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="toggleAI({{ $tenant->id }}, {{ $tenant->ai_enabled ? 'false' : 'true' }})"
                                                title="AI Durumunu Değiştir">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="adjustTokens({{ $tenant->id }})"
                                                title="Token Ayarla">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $tenants->links() }}
                </div>
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-users text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz kiracı yok</p>
                    <p class="empty-subtitle text-muted">
                        Sistemde henüz hiçbir kiracı bulunmamaktadır.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Token Adjustment Modal -->
<div class="modal modal-blur fade" id="tokenAdjustmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Token Ayarlama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="tokenAdjustmentForm">
                    <input type="hidden" id="tenantId" name="tenantId">
                    
                    <div class="mb-3">
                        <label for="tokenAmount" class="form-label">Token Miktarı</label>
                        <input type="number" class="form-control" id="tokenAmount" name="tokenAmount" 
                               placeholder="Pozitif değer ekler, negatif değer çıkarır" required>
                        <small class="form-text text-muted">
                            Pozitif değer token ekler, negatif değer token çıkarır
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adjustmentReason" class="form-label">Ayarlama Nedeni</label>
                        <textarea class="form-control" id="adjustmentReason" name="adjustmentReason" 
                                  rows="3" placeholder="Token ayarlama nedenini açıklayın" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="submitTokenAdjustment()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleAI(tenantId, enable) {
    if (confirm('AI durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch(`/admin/ai/tokens/tenant/${tenantId}/toggle-ai`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                enabled: enable
            })
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
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

function adjustTokens(tenantId) {
    document.getElementById('tenantId').value = tenantId;
    document.getElementById('tokenAmount').value = '';
    document.getElementById('adjustmentReason').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('tokenAdjustmentModal'));
    modal.show();
}

function submitTokenAdjustment() {
    const form = document.getElementById('tokenAdjustmentForm');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const tenantId = formData.get('tenantId');
    const tokenAmount = formData.get('tokenAmount');
    const adjustmentReason = formData.get('adjustmentReason');
    
    if (!tokenAmount || tokenAmount == 0) {
        alert('Token miktarı sıfırdan farklı olmalıdır');
        return;
    }
    
    fetch(`/admin/ai/tokens/tenant/${tenantId}/adjust`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            tokenAmount: parseInt(tokenAmount),
            adjustmentReason: adjustmentReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('tokenAdjustmentModal')).hide();
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}
</script>
@endpush