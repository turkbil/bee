@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', 'Kiracı Yönetimi')

@section('content')
<div class="row">
    <!-- Genel İstatistikler -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sistem Geneli Token İstatistikleri</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-primary text-white avatar">
                                            <i class="fas fa-users"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">
                                            {{ number_format($systemStats['total_tenants']) }}
                                        </div>
                                        <div class="text-muted">
                                            Toplam Kiracı
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-success text-white avatar">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">
                                            {{ number_format($systemStats['active_ai_tenants']) }}
                                        </div>
                                        <div class="text-muted">
                                            AI Aktif Kiracı
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-info text-white avatar">
                                            <i class="fas fa-coins"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">
                                            {{ number_format($systemStats['total_tokens_distributed']) }}
                                        </div>
                                        <div class="text-muted">
                                            Dağıtılan Token
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="bg-warning text-white avatar">
                                            <i class="fas fa-chart-bar"></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bold">
                                            {{ number_format($systemStats['total_tokens_used']) }}
                                        </div>
                                        <div class="text-muted">
                                            Kullanılan Token
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

    <!-- Kiracılar Listesi -->
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Kiracı Listesi</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.ai.tokens.packages.admin') }}" class="btn btn-primary">
                        <i class="fas fa-box me-2"></i>Paket Yönetimi
                    </a>
                    <a href="{{ route('admin.ai.tokens.purchases.all') }}" class="btn btn-info">
                        <i class="fas fa-receipt me-2"></i>Tüm Satışlar
                    </a>
                    <a href="{{ route('admin.ai.tokens.usage.stats.all') }}" class="btn btn-success">
                        <i class="fas fa-chart-line me-2"></i>Kullanım Raporu
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Kiracı</th>
                                <th class="text-center" style="width: 80px">AI Durumu</th>
                                <th>Token Bakiyesi</th>
                                <th>Aylık Limit</th>
                                <th>Bu Ay Kullanım</th>
                                <th>Son Kullanım</th>
                                <th class="text-center" style="width: 160px">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="table-tbody">
                            @forelse($tenants as $tenant)
                            <tr class="hover-trigger">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $tenant->title ?: 'Varsayılan' }}</div>
                                            <div class="text-muted small">ID: {{ $tenant->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <button onclick="toggleAI({{ $tenant->id }}, {{ $tenant->ai_enabled ? 'false' : 'true' }})"
                                        class="btn btn-icon btn-sm {{ $tenant->ai_enabled ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                        @if($tenant->ai_enabled)
                                        <i class="fas fa-check"></i>
                                        @else
                                        <i class="fas fa-times"></i>
                                        @endif
                                    </button>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($tenant->ai_tokens_balance) }}</div>
                                    <div class="text-muted small">token</div>
                                </td>
                                <td>
                                    @if($tenant->ai_monthly_token_limit > 0)
                                        <div class="fw-bold">{{ number_format($tenant->ai_monthly_token_limit) }}</div>
                                        <div class="text-muted small">token/ay</div>
                                    @else
                                        <span class="text-muted">Sınırsız</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ number_format($tenant->ai_tokens_used_this_month) }}</div>
                                    @if($tenant->ai_monthly_token_limit > 0)
                                        <div class="progress progress-sm mt-1">
                                            <div class="progress-bar" 
                                                 style="width: {{ min(100, ($tenant->ai_tokens_used_this_month / $tenant->ai_monthly_token_limit) * 100) }}%">
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($tenant->ai_last_used_at)
                                        <div class="fw-bold">{{ $tenant->ai_last_used_at->format('d.m.Y H:i') }}</div>
                                        <div class="text-muted small">{{ $tenant->ai_last_used_at->diffForHumans() }}</div>
                                    @else
                                        <span class="text-muted">Hiç kullanılmamış</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col">
                                                <a href="{{ route('admin.ai.tokens.tenant.show', $tenant) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Detayları Görüntüle">
                                                    <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                                </a>
                                            </div>
                                            <div class="col lh-1">
                                                <div class="dropdown mt-1">
                                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0);" 
                                                           onclick="toggleAI({{ $tenant->id }}, {{ $tenant->ai_enabled ? 'false' : 'true' }})" 
                                                           class="dropdown-item {{ $tenant->ai_enabled ? 'link-danger' : 'link-success' }}">
                                                            <i class="fas fa-{{ $tenant->ai_enabled ? 'times' : 'check' }} me-2"></i>
                                                            AI'yi {{ $tenant->ai_enabled ? 'Devre Dışı Bırak' : 'Aktif Et' }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">Henüz kiracı bulunmuyor.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($tenants->hasPages())
                    <div class="mt-3">
                        {{ $tenants->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleAI(tenantId, enable) {
    if (!confirm('AI durumunu değiştirmek istediğinize emin misiniz?')) {
        return;
    }
    
    fetch(`/admin/ai-tokens/tenant/${tenantId}/toggle-ai`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ai_enabled: enable })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Hata: ' + (data.message || 'İşlem başarısız oldu'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}
</script>
@endsection