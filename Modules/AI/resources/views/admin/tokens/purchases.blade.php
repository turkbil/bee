@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Kredi Yönetimi')
@section('title', 'Kredi Satın Alımları')

@section('content')
<!-- Tenant Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-0">Kredi Satın Alımları</h5>
                        @if($selectedTenant)
                            <small class="text-muted">
                                {{ $tenants->where('id', $selectedTenant)->first()->title ?? 'Tenant #' . $selectedTenant }} verileri gösteriliyor
                            </small>
                        @else
                            <small class="text-muted">Tüm kiracıların verileri gösteriliyor</small>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-2"></i>
                                @if($selectedTenant)
                                    {{ $tenants->where('id', $selectedTenant)->first()->title ?? 'Tenant #' . $selectedTenant }}
                                @else
                                    Tüm Kiracılar
                                @endif
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item {{ !$selectedTenant ? 'active' : '' }}" 
                                       href="{{ route('admin.ai.credits.purchases') }}">
                                        <i class="fas fa-globe me-2"></i>Tüm Kiracılar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($tenants as $tenant)
                                    <li>
                                        <a class="dropdown-item {{ $selectedTenant == $tenant->id ? 'active' : '' }}" 
                                           href="{{ route('admin.ai.credits.purchases', ['tenant_id' => $tenant->id]) }}">
                                            <i class="fas fa-user me-2"></i>{{ $tenant->title ?: 'Tenant #' . $tenant->id }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kredi Satın Alım Kayıtları</h3>
            </div>
            <div class="card-body">
                @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kiracı</th>
                                <th>Paket</th>
                                <th>Kredi Miktarı</th>
                                <th>Ödenen Fiyat</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>
                                    @if($purchase->tenant)
                                        <strong>{{ $purchase->tenant->title }}</strong>
                                    @else
                                        <span class="text-muted">Bilinmiyor</span>
                                    @endif
                                </td>
                                <td>
                                    @if($purchase->package)
                                        {{ $purchase->package->name }}
                                    @else
                                        <span class="text-muted">Paket silinmiş</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ number_format($purchase->token_amount, 0) }} Kredi</span>
                                </td>
                                <td>{{ number_format($purchase->price_paid, 2) }} {{ $purchase->currency }}</td>
                                <td>
                                    <span class="badge badge-{{ $purchase->status_badge_color }}">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                </td>
                                <td>{{ $purchase->purchased_at ? $purchase->purchased_at->format('d.m.Y H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-list">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="showPurchaseDetails({{ $purchase->id }})"
                                                title="Detayları Görüntüle">
                                            <i class="fas fa-eye"></i>
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
                    {{ $purchases->links() }}
                </div>
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-shopping-cart text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz satın alım yok</p>
                    <p class="empty-subtitle text-muted">
                        Henüz hiçbir kiracı tarafından kredi paketi satın alınmamış.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Purchase Details Modal -->
<div class="modal modal-blur fade" id="purchaseDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Satın Alım Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="purchaseDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showPurchaseDetails(purchaseId) {
    // Placeholder for purchase details functionality
    alert('Satın alım detayları: ' + purchaseId);
}
</script>
@endpush
@endsection