<div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Satın Alma Geçmişim</h3>
        <div class="card-subtitle">Token paketi satın alma işlemleriniz</div>
    </div>
    <div class="card-body">
        <!-- İstatistik Kartları -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Toplam Satın Alma</div>
                        </div>
                        <div class="h1 mb-0">{{ $purchaseStats['total_purchases'] }}</div>
                        <div class="text-muted">işlem</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Toplam Harcama</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($purchaseStats['total_spent'], 2) }}</div>
                        <div class="text-muted">TRY</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Satın Alınan Token</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($purchaseStats['total_tokens_bought']) }}</div>
                        <div class="text-muted">token</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Bekleyen İşlem</div>
                        </div>
                        <div class="h1 mb-0">{{ $purchaseStats['pending_purchases'] }}</div>
                        <div class="text-muted">işlem</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>Paket</th>
                        <th style="width: 140px">Token Miktarı</th>
                        <th style="width: 120px">Fiyat</th>
                        <th class="text-center" style="width: 100px">Durum</th>
                        <th style="width: 140px">Satın Alma Tarihi</th>
                        <th class="text-center" style="width: 120px">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td class="small">{{ $purchase->id }}</td>
                        <td>
                            <div>
                                <div class="fw-bold">{{ $purchase->package ? $purchase->package->name : 'Paket Silinmiş' }}</div>
                                @if($purchase->package && $purchase->package->description)
                                    <div class="text-muted small">{{ Str::limit($purchase->package->description, 50) }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ number_format($purchase->token_amount) }}</div>
                            <div class="text-muted small">token</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ number_format($purchase->price_paid, 2) }}</div>
                            <div class="text-muted small">{{ $purchase->currency }}</div>
                        </td>
                        <td class="text-center align-middle">
                            @if($purchase->status === 'completed')
                                <span class="badge bg-success">Tamamlandı</span>
                            @elseif($purchase->status === 'pending')
                                <span class="badge bg-warning">Beklemede</span>
                            @elseif($purchase->status === 'failed')
                                <span class="badge bg-danger">Başarısız</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($purchase->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $purchase->purchased_at ? $purchase->purchased_at->format('d.m.Y') : '-' }}</div>
                            <div class="text-muted small">{{ $purchase->purchased_at ? $purchase->purchased_at->format('H:i') : '' }}</div>
                        </td>
                        <td class="text-center align-middle">
                            <div class="btn-list">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPurchase({{ $purchase->id }})" data-bs-toggle="tooltip" title="Detayları Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($purchase->status === 'completed')
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="downloadReceipt({{ $purchase->id }})" data-bs-toggle="tooltip" title="Fatura İndir">
                                    <i class="fas fa-download"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">Henüz satın alma bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Henüz hiç token paketi satın almamışsınız.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.ai.tokens.packages') }}" class="btn btn-primary">
                                        <i class="fas fa-shopping-cart me-2"></i>Token Satın Al
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($purchases->hasPages())
    <div class="card-footer d-flex align-items-center">
        {{ $purchases->links() }}
    </div>
    @endif
</div>

<script>
function viewPurchase(purchaseId) {
    // TODO: Purchase details modal
    alert('Satın alma detayları modalı yakında eklenecek');
}

function downloadReceipt(purchaseId) {
    // TODO: Download receipt
    alert('Fatura indirme işlemi yakında eklenecek');
}
</script></div>
