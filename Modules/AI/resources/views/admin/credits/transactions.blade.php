@extends('admin.layout')

@section('page-title', 'Kredi İşlemleri')
@section('page-subtitle', 'Kredi satın alma ve kullanım işlemleri')

@section('content')
<div class="container-xl">
    <!-- Filtreleme -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter me-2"></i>
                        Filtreler
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="startDate" value="{{ date('Y-m-01') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="endDate" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">İşlem Türü</label>
                            <select class="form-select" id="transactionType">
                                <option value="">Tümü</option>
                                <option value="purchase">Satın Alma</option>
                                <option value="usage">Kullanım</option>
                                <option value="refund">İade</option>
                                <option value="adjustment">Düzeltme</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tenant</label>
                            <select class="form-select" id="tenantFilter">
                                <option value="">Tüm Tenant'lar</option>
                                <!-- AJAX ile doldurulacak -->
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-primary" onclick="applyFilters()">
                                <i class="fas fa-search me-2"></i>
                                Filtrele
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>
                                Temizle
                            </button>
                            <button class="btn btn-success ms-2" onclick="exportTransactions()">
                                <i class="fas fa-download me-2"></i>
                                Dışa Aktar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Özet Bilgiler -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Satın Alınan</div>
                    </div>
                    <div class="h1 mb-0 text-success" id="totalPurchased">-</div>
                    <div class="text-muted small">Kredi</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Kullanılan</div>
                    </div>
                    <div class="h1 mb-0 text-danger" id="totalUsed">-</div>
                    <div class="text-muted small">Kredi</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Gelir</div>
                    </div>
                    <div class="h1 mb-0 text-primary" id="totalRevenue">-</div>
                    <div class="text-muted small">USD</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Aktif Bakiye</div>
                    </div>
                    <div class="h1 mb-0 text-info" id="totalBalance">-</div>
                    <div class="text-muted small">Kredi</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- İşlemler Tablosu -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-receipt me-2"></i>
                        Kredi İşlemleri
                    </h3>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                            <i class="fas fa-plus me-1"></i>
                            Manuel İşlem
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter" id="transactionsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tarih</th>
                                    <th>Tenant</th>
                                    <th>Tür</th>
                                    <th>Açıklama</th>
                                    <th>Kredi</th>
                                    <th>Tutar</th>
                                    <th>Provider</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- AJAX ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Loading State -->
                    <div class="text-center py-4 d-none" id="loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div class="empty d-none" id="emptyState">
                        <div class="empty-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <p class="empty-title">İşlem bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            Seçilen kriterlere uygun işlem bulunamadı.
                        </p>
                    </div>
                    
                    <!-- Pagination -->
                    <nav class="mt-3">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- AJAX ile doldurulacak -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manuel İşlem Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manuel Kredi İşlemi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manualTransactionForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tenant</label>
                        <select class="form-select" name="tenant_id" required>
                            <option value="">Tenant seçin</option>
                            <!-- AJAX ile doldurulacak -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İşlem Türü</label>
                        <select class="form-select" name="type" required>
                            <option value="">Tür seçin</option>
                            <option value="credit">Kredi Ekle</option>
                            <option value="debit">Kredi Çıkar</option>
                            <option value="adjustment">Düzeltme</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kredi Miktarı</label>
                        <input type="number" class="form-control" name="credits" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Referans</label>
                        <input type="text" class="form-control" name="reference" placeholder="İşlem referansı (opsiyonel)">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">İşlemi Gerçekleştir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- İşlem Detay Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İşlem Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <!-- AJAX ile doldurulacak -->
            </div>
        </div>
    </div>
</div>

<script>
// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadSummaryStats();
    loadTenants();
    loadTransactions();
});

// Özet istatistikleri yükle
function loadSummaryStats() {
    fetch('/admin/ai/credits/api/transaction-summary')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalPurchased').textContent = data.total_purchased || 0;
            document.getElementById('totalUsed').textContent = data.total_used || 0;
            document.getElementById('totalRevenue').textContent = '$' + (data.total_revenue || 0);
            document.getElementById('totalBalance').textContent = data.total_balance || 0;
        })
        .catch(error => {
            console.error('Özet istatistik yükleme hatası:', error);
        });
}

// Tenant'ları yükle
function loadTenants() {
    fetch('/admin/ai/credits/api/tenants')
        .then(response => response.json())
        .then(data => {
            const tenantFilter = document.getElementById('tenantFilter');
            const tenantSelect = document.querySelector('select[name="tenant_id"]');
            
            data.forEach(tenant => {
                const option = new Option(tenant.name, tenant.id);
                tenantFilter.appendChild(option.cloneNode(true));
                tenantSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Tenant yükleme hatası:', error);
        });
}

// İşlemleri yükle
function loadTransactions(page = 1) {
    showLoading(true);
    
    const params = new URLSearchParams({
        page: page,
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        type: document.getElementById('transactionType').value,
        tenant_id: document.getElementById('tenantFilter').value
    });
    
    fetch(`/admin/ai/credits/api/transactions?${params}`)
        .then(response => response.json())
        .then(data => {
            updateTransactionsTable(data.data || []);
            updatePagination(data.pagination || {});
            showEmptyState(data.data.length === 0);
        })
        .catch(error => {
            console.error('İşlem yükleme hatası:', error);
            showEmptyState(true);
        })
        .finally(() => {
            showLoading(false);
        });
}

// İşlemler tablosunu güncelle
function updateTransactionsTable(transactions) {
    const tbody = document.querySelector('#transactionsTable tbody');
    tbody.innerHTML = '';
    
    transactions.forEach(transaction => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>#${transaction.id}</td>
            <td>${transaction.created_at}</td>
            <td>
                <span class="badge bg-secondary">${transaction.tenant_name}</span>
            </td>
            <td>
                <span class="badge ${getTypeBadgeClass(transaction.type)}">
                    ${getTypeLabel(transaction.type)}
                </span>
            </td>
            <td>${transaction.description}</td>
            <td class="fw-bold ${transaction.credits > 0 ? 'text-success' : 'text-danger'}">
                ${transaction.credits > 0 ? '+' : ''}${transaction.credits}
            </td>
            <td>
                ${transaction.amount ? '$' + transaction.amount : '-'}
            </td>
            <td>
                ${transaction.provider ? '<span class="badge bg-primary">' + transaction.provider + '</span>' : '-'}
            </td>
            <td>
                <span class="badge ${getStatusBadgeClass(transaction.status)}">
                    ${getStatusLabel(transaction.status)}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="showTransactionDetails(${transaction.id})">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Yardımcı fonksiyonlar
function getTypeBadgeClass(type) {
    const classes = {
        'purchase': 'bg-success',
        'usage': 'bg-danger',
        'refund': 'bg-warning',
        'adjustment': 'bg-info'
    };
    return classes[type] || 'bg-secondary';
}

function getTypeLabel(type) {
    const labels = {
        'purchase': 'Satın Alma',
        'usage': 'Kullanım',
        'refund': 'İade',
        'adjustment': 'Düzeltme'
    };
    return labels[type] || type;
}

function getStatusBadgeClass(status) {
    const classes = {
        'completed': 'bg-success',
        'pending': 'bg-warning',
        'failed': 'bg-danger',
        'cancelled': 'bg-secondary'
    };
    return classes[status] || 'bg-secondary';
}

function getStatusLabel(status) {
    const labels = {
        'completed': 'Tamamlandı',
        'pending': 'Bekliyor',
        'failed': 'Başarısız',
        'cancelled': 'İptal'
    };
    return labels[status] || status;
}

// Pagination güncelle
function updatePagination(pagination) {
    const paginationElement = document.getElementById('pagination');
    paginationElement.innerHTML = '';
    
    if (pagination.total_pages > 1) {
        for (let i = 1; i <= pagination.total_pages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" onclick="loadTransactions(${i})">${i}</a>`;
            paginationElement.appendChild(li);
        }
    }
}

// Loading state
function showLoading(show) {
    document.getElementById('loading').classList.toggle('d-none', !show);
    document.getElementById('transactionsTable').classList.toggle('d-none', show);
}

// Empty state
function showEmptyState(show) {
    document.getElementById('emptyState').classList.toggle('d-none', !show);
    document.getElementById('transactionsTable').classList.toggle('d-none', show);
}

// Filtreleri uygula
function applyFilters() {
    loadTransactions(1);
}

// Filtreleri temizle
function clearFilters() {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('transactionType').value = '';
    document.getElementById('tenantFilter').value = '';
    loadTransactions(1);
}

// İşlemleri dışa aktar
function exportTransactions() {
    const params = new URLSearchParams({
        export: 'excel',
        start_date: document.getElementById('startDate').value,
        end_date: document.getElementById('endDate').value,
        type: document.getElementById('transactionType').value,
        tenant_id: document.getElementById('tenantFilter').value
    });
    
    window.open(`/admin/ai/credits/api/export-transactions?${params}`);
}

// İşlem detaylarını göster
function showTransactionDetails(transactionId) {
    fetch(`/admin/ai/credits/api/transactions/${transactionId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('transactionDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Temel Bilgiler</h6>
                        <table class="table table-sm">
                            <tr><td>ID:</td><td>#${data.id}</td></tr>
                            <tr><td>Tarih:</td><td>${data.created_at}</td></tr>
                            <tr><td>Tenant:</td><td>${data.tenant_name}</td></tr>
                            <tr><td>Tür:</td><td>${getTypeLabel(data.type)}</td></tr>
                            <tr><td>Durum:</td><td>${getStatusLabel(data.status)}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Mali Bilgiler</h6>
                        <table class="table table-sm">
                            <tr><td>Kredi:</td><td class="fw-bold">${data.credits}</td></tr>
                            <tr><td>Tutar:</td><td>${data.amount ? '$' + data.amount : '-'}</td></tr>
                            <tr><td>Provider:</td><td>${data.provider || '-'}</td></tr>
                            <tr><td>Referans:</td><td>${data.reference || '-'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Açıklama</h6>
                        <p>${data.description}</p>
                        ${data.metadata ? '<h6>Metadata</h6><pre>' + JSON.stringify(data.metadata, null, 2) + '</pre>' : ''}
                    </div>
                </div>
            `;
            
            new bootstrap.Modal(document.getElementById('transactionDetailModal')).show();
        })
        .catch(error => {
            console.error('İşlem detayı yükleme hatası:', error);
        });
}

// Manuel işlem formu
document.getElementById('manualTransactionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/ai/credits/api/manual-transaction', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addTransactionModal')).hide();
            this.reset();
            loadTransactions();
            loadSummaryStats();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Manuel işlem hatası:', error);
        alert('İşlem gerçekleştirilemedi.');
    });
});
</script>
@endsection