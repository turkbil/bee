@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', $tenant->title ?: 'Varsayılan Kiracı')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kiracı Token Detayları</h3>
                <div class="card-actions">
                    <a href="{{ route('admin.ai.tokens.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Geri Dön
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Kiracı Bilgileri</h4>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $tenant->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Başlık:</strong></td>
                                <td>{{ $tenant->title ?: 'Varsayılan' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Domain:</strong></td>
                                <td>{{ $tenant->domain ?? 'Belirlenmemiş' }}</td>
                            </tr>
                            <tr>
                                <td><strong>AI Durumu:</strong></td>
                                <td>
                                    @if($tenant->ai_enabled)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Pasif</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Token İstatistikleri</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ ai_format_token_count($realTokenBalance) }}</h3>
                                        <p class="mb-0">Gerçek Bakiye</p>
                                        <small class="opacity-75">(Satın alınan - Kullanılan)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ ai_format_token_count($totalPurchasedTokens) }}</h3>
                                        <p class="mb-0">Satın Alınan Toplam</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ ai_format_token_count($totalUsedTokens) }}</h3>
                                        <p class="mb-0">Kullanılan Toplam</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">{{ ai_format_token_count($monthlyUsedTokens) }}</h3>
                                        <p class="mb-0">Bu Ay Kullanım</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">
                                            @if($tenant->ai_monthly_token_limit > 0)
                                                {{ ai_format_token_count($tenant->ai_monthly_token_limit) }}
                                            @else
                                                ∞
                                            @endif
                                        </h3>
                                        <p class="mb-0">Aylık Limit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card bg-secondary text-white">
                                    <div class="card-body text-center">
                                        <h3 class="mb-1">
                                            @if($tenant->ai_last_used_at)
                                                {{ $tenant->ai_last_used_at->format('d.m.Y') }}
                                            @else
                                                -
                                            @endif
                                        </h3>
                                        <p class="mb-0">Son Kullanım</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($tenant->ai_monthly_token_limit > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <h4>Aylık Kullanım Durumu</h4>
                        <div class="progress progress-lg">
                            <div class="progress-bar" style="width: {{ min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100) }}%">
                                {{ number_format(min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100), 1) }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ ai_format_token_count($tenant->ai_tokens_used_this_month) }} / {{ ai_format_token_count($tenant->ai_monthly_token_limit) }} token kullanıldı
                        </small>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-12">
                        <h4>Hızlı İşlemler</h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" onclick="openTokenModal()">
                                <i class="fas fa-coins me-2"></i>Token Ekle/Çıkar
                            </button>
                            <button type="button" class="btn btn-{{ $tenant->ai_enabled ? 'danger' : 'success' }}" 
                                    onclick="toggleAI()">
                                <i class="fas fa-{{ $tenant->ai_enabled ? 'times' : 'check' }} me-2"></i>
                                AI'yi {{ $tenant->ai_enabled ? 'Devre Dışı Bırak' : 'Aktif Et' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Token Yönetimi Modal -->
<div class="modal modal-blur fade" id="tokenModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Token Yönetimi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <form id="tokenForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kiracı</label>
                        <div class="form-control-plaintext">{{ $tenant->title ?: 'Varsayılan' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gerçek Bakiye</label>
                        <div class="form-control-plaintext">{{ ai_format_token_count($realTokenBalance) }} token</div>
                        <small class="text-muted">Satın alınan ({{ ai_format_token_count($totalPurchasedTokens) }}) - Kullanılan ({{ ai_format_token_count($totalUsedTokens) }}) = {{ ai_format_token_count($realTokenBalance) }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tokenAmount" class="form-label">Token Miktarı</label>
                        <input type="number" class="form-control" id="tokenAmount" name="tokenAmount" 
                               placeholder="Eklemek için pozitif, çıkarmak için negatif">
                        <small class="form-hint">Pozitif sayı token ekler, negatif sayı token çıkarır.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="adjustmentReason" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="adjustmentReason" name="adjustmentReason" 
                                  rows="3" placeholder="Token düzenleme sebebini açıklayın..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="normal-text">Uygula</span>
                        <span class="loading-text d-none">İşleniyor...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openTokenModal() {
    $('#tokenModal').modal('show');
}

function toggleAI() {
    if (confirm('AI durumunu değiştirmek istediğinizden emin misiniz?')) {
        fetch('{{ route("admin.ai.tokens.toggle-ai", $tenant) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
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
            alert('İşlem sırasında hata oluştu.');
        });
    }
}

document.getElementById('tokenForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const normalText = submitBtn.querySelector('.normal-text');
    const loadingText = submitBtn.querySelector('.loading-text');
    
    // Loading state
    submitBtn.disabled = true;
    normalText.classList.add('d-none');
    loadingText.classList.remove('d-none');
    
    const formData = new FormData(this);
    
    fetch('{{ route("admin.ai.tokens.adjust", $tenant) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#tokenModal').modal('hide');
            setTimeout(() => location.reload(), 500);
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        alert('İşlem sırasında hata oluştu.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        normalText.classList.remove('d-none');
        loadingText.classList.add('d-none');
    });
});
</script>
@endsection