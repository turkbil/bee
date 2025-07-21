@extends('admin.layout')

@section('title', 'Kredi Satın Alımları')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        AI Yönetimi
                    </div>
                    <h2 class="page-title">
                        <i class="ti ti-shopping-cart me-2"></i>
                        Kredi Satın Alımları
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.credits.index') }}" class="btn btn-ghost-dark">
                            <i class="ti ti-arrow-left me-1"></i>
                            Geri Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="ti ti-history me-2"></i>
                                Satın Alma Geçmişi
                            </h3>
                            <div class="card-actions">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Ara..." id="searchInput">
                                    <button type="button" class="btn btn-outline-primary">
                                        <i class="ti ti-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-mobile-md card-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kullanıcı</th>
                                            <th>Paket</th>
                                            <th>Kredi Miktarı</th>
                                            <th>Tutar</th>
                                            <th>Para Birimi</th>
                                            <th>Durum</th>
                                            <th>Tarih</th>
                                            <th class="w-1">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Demo veriler - Gerçek veritabanı entegrasyonu sonrası kaldırılacak
                                            $purchases = [
                                                [
                                                    'id' => 1,
                                                    'user_name' => 'Demo Kullanıcı',
                                                    'package_name' => 'Starter Paket',
                                                    'credits' => 10000,
                                                    'amount' => 10.00,
                                                    'currency' => 'USD',
                                                    'status' => 'completed',
                                                    'created_at' => now()->subDays(2)
                                                ],
                                                [
                                                    'id' => 2,
                                                    'user_name' => 'Test Kullanıcı',
                                                    'package_name' => 'Pro Paket',
                                                    'credits' => 50000,
                                                    'amount' => 45.00,
                                                    'currency' => 'USD',
                                                    'status' => 'pending',
                                                    'created_at' => now()->subHours(5)
                                                ],
                                                [
                                                    'id' => 3,
                                                    'user_name' => 'Admin User',
                                                    'package_name' => 'Enterprise Paket',
                                                    'credits' => 200000,
                                                    'amount' => 160.00,
                                                    'currency' => 'USD',
                                                    'status' => 'completed',
                                                    'created_at' => now()->subDays(7)
                                                ]
                                            ];
                                        @endphp
                                        
                                        @forelse($purchases as $purchase)
                                        <tr>
                                            <td>
                                                <span class="text-secondary">#{{ $purchase['id'] }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-blue-lt rounded me-2">
                                                        {{ substr($purchase['user_name'], 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $purchase['user_name'] }}</div>
                                                        <div class="text-secondary">Kullanıcı</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $purchase['package_name'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-blue-lt">
                                                    {{ number_format($purchase['credits']) }} Kredi
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-green">
                                                    ${{ number_format($purchase['amount'], 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary-lt">{{ $purchase['currency'] }}</span>
                                            </td>
                                            <td>
                                                @if($purchase['status'] === 'completed')
                                                    <span class="badge bg-success">Tamamlandı</span>
                                                @elseif($purchase['status'] === 'pending')
                                                    <span class="badge bg-warning">Beklemede</span>
                                                @elseif($purchase['status'] === 'failed')
                                                    <span class="badge bg-danger">Başarısız</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($purchase['status']) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $purchase['created_at']->format('d.m.Y') }}</div>
                                                <div class="text-secondary">{{ $purchase['created_at']->format('H:i') }}</div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                        İşlemler
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" onclick="viewDetails({{ $purchase['id'] }})">
                                                            <i class="ti ti-eye me-2"></i>
                                                            Detayları Görüntüle
                                                        </a>
                                                        @if($purchase['status'] === 'pending')
                                                        <a class="dropdown-item text-success" href="#" onclick="approvePurchase({{ $purchase['id'] }})">
                                                            <i class="ti ti-check me-2"></i>
                                                            Onayla
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="#" onclick="rejectPurchase({{ $purchase['id'] }})">
                                                            <i class="ti ti-x me-2"></i>
                                                            Reddet
                                                        </a>
                                                        @endif
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deletePurchase({{ $purchase['id'] }})">
                                                            <i class="ti ti-trash me-2"></i>
                                                            Sil
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <div class="empty">
                                                    <div class="empty-img">
                                                        <i class="ti ti-shopping-cart" style="font-size: 4rem; color: #6c757d;"></i>
                                                    </div>
                                                    <p class="empty-title">Henüz satın alma bulunamadı</p>
                                                    <p class="empty-subtitle text-secondary">
                                                        Kredi satın alma işlemleri burada görüntülenecek.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-secondary">
                                    Toplam {{ count($purchases) }} kayıt
                                </div>
                                <nav aria-label="Sayfa navigasyonu">
                                    <ul class="pagination pagination-sm m-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Önceki</a>
                                        </li>
                                        <li class="page-item active">
                                            <a class="page-link" href="#">1</a>
                                        </li>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#">Sonraki</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- İstatistikler -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Toplam Satış</div>
                                <div class="ms-auto lh-1">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">Bu Ay</a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item active" href="#">Bu Ay</a>
                                            <a class="dropdown-item" href="#">Geçen Ay</a>
                                            <a class="dropdown-item" href="#">Bu Yıl</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="h1 mb-3">$215.00</div>
                            <div class="d-flex mb-2">
                                <div>Tamamlanan: 2</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        12%
                                        <i class="ti ti-trending-up ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Satılan Kredi</div>
                            <div class="h1 mb-3">210,000</div>
                            <div class="d-flex mb-2">
                                <div>Aktif krediler</div>
                                <div class="ms-auto">
                                    <span class="text-green d-inline-flex align-items-center lh-1">
                                        8%
                                        <i class="ti ti-trending-up ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Bekleyen İşlemler</div>
                            <div class="h1 mb-3">1</div>
                            <div class="d-flex mb-2">
                                <div>İnceleme gerekli</div>
                                <div class="ms-auto">
                                    <span class="text-yellow d-inline-flex align-items-center lh-1">
                                        Beklemede
                                        <i class="ti ti-clock ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="subheader">Ortalama Paket</div>
                            <div class="h1 mb-3">$71.67</div>
                            <div class="d-flex mb-2">
                                <div>Per işlem</div>
                                <div class="ms-auto">
                                    <span class="text-blue d-inline-flex align-items-center lh-1">
                                        Stabil
                                        <i class="ti ti-minus ms-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewDetails(purchaseId) {
    // Detayları görüntüleme modal'ı açılacak
    alert('Satın alma detayları: #' + purchaseId);
}

function approvePurchase(purchaseId) {
    if (confirm('Bu satın alma işlemini onaylamak istediğinizden emin misiniz?')) {
        // AJAX ile onaylama işlemi
        alert('Satın alma onaylandı: #' + purchaseId);
    }
}

function rejectPurchase(purchaseId) {
    if (confirm('Bu satın alma işlemini reddetmek istediğinizden emin misiniz?')) {
        // AJAX ile reddetme işlemi
        alert('Satın alma reddedildi: #' + purchaseId);
    }
}

function deletePurchase(purchaseId) {
    if (confirm('Bu kaydı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        // AJAX ile silme işlemi
        alert('Kayıt silindi: #' + purchaseId);
    }
}

// Arama fonksiyonu
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection