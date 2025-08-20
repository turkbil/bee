@extends('admin.layout')

@include('ai::helper')

@section('page-title', 'Kredi Paket Yönetimi')
@section('page-subtitle', 'AI kredi paketlerini oluşturun ve düzenleyin')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes me-2"></i>
                        Kredi Paketleri
                    </h3>
                    <div class="card-actions">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal">
                            <i class="fas fa-plus me-1"></i>
                            Yeni Paket
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($packages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Paket</th>
                                        <th>Kredi</th>
                                        <th colspan="2">Fiyat</th>
                                        <th>İndirim</th>
                                        <th>Durum</th>
                                        <th>Sıra</th>
                                        <th class="w-1">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($packages as $package)
                                    <tr id="package-{{ $package->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($package->is_popular)
                                                    <span class="badge badge-primary me-2">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $package->name }}</div>
                                                    <div class="text-muted small">{{ $package->description }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">
                                                {{ format_credit($package->credit_amount, false) }}
                                            </span>
                                        </td>
                                        <td colspan="2">
                                            @if($package->discount_percentage > 0)
                                                <div class="text-decoration-line-through text-muted small">
                                                    {{ $package->formatted_price }}
                                                </div>
                                                <div class="fw-bold text-success">
                                                    {{ $package->formatted_price }}
                                                </div>
                                            @else
                                                <span class="fw-bold">
                                                    {{ $package->formatted_price }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($package->discount_percentage > 0)
                                                <span class="badge bg-success">
                                                    %{{ $package->discount_percentage }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" 
                                                       {{ $package->is_active ? 'checked' : '' }}
                                                       onchange="togglePackageStatus({{ $package->id }})">
                                            </label>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" 
                                                   style="width: 80px;" 
                                                   value="{{ $package->sort_order }}"
                                                   onchange="updateSortOrder({{ $package->id }}, this.value)">
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" 
                                                       onclick="editPackage({{ $package->id }})">
                                                        <i class="fas fa-edit me-2"></i>
                                                        Düzenle
                                                    </a>
                                                    <a class="dropdown-item" href="#" 
                                                       onclick="togglePopular({{ $package->id }})">
                                                        <i class="fas fa-star me-2"></i>
                                                        {{ $package->is_popular ? 'Popüler İptal' : 'Popüler Yap' }}
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="deletePackage({{ $package->id }})">
                                                        <i class="fas fa-trash me-2"></i>
                                                        Sil
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <p class="empty-title">Henüz kredi paketi yok</p>
                            <p class="empty-subtitle text-muted">
                                İlk kredi paketinizi oluşturmak için "Yeni Paket" butonuna tıklayın.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<!-- Paket Modal -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kredi Paketi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="packageForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Paket Adı</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kredi Miktarı</label>
                                <input type="number" class="form-control" name="credit_amount" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fiyat</label>
                                <input type="number" class="form-control" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Para Birimi</label>
                                <select class="form-control" name="currency" required>
                                    <option value="TRY" selected>TRY (₺)</option>
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">İndirim (%)</label>
                                <input type="number" class="form-control" name="discount_percentage" min="0" max="100" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sıralama</label>
                                <input type="number" class="form-control" name="sort_order" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" checked>
                                    <span class="form-check-label">Aktif</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="is_popular">
                                    <span class="form-check-label">Popüler Paket</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Özellikler (JSON)</label>
                        <textarea class="form-control" name="features_json" rows="4" 
                                  placeholder='["Özellik 1", "Özellik 2", "Özellik 3"]'></textarea>
                        <div class="form-hint">JSON formatında paket özelliklerini girin</div>
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

<script>
// Package management functions will be implemented here
function togglePackageStatus(packageId) {
    // AJAX call to toggle package status
    console.log('Toggle status for package:', packageId);
}

function updateSortOrder(packageId, sortOrder) {
    // AJAX call to update sort order
    console.log('Update sort order for package:', packageId, sortOrder);
}

function editPackage(packageId) {
    // Open modal with package data
    console.log('Edit package:', packageId);
}

function togglePopular(packageId) {
    // AJAX call to toggle popular status
    console.log('Toggle popular for package:', packageId);
}

function deletePackage(packageId) {
    if (confirm('Bu paketi silmek istediğinizden emin misiniz?')) {
        // AJAX call to delete package
        console.log('Delete package:', packageId);
    }
}

// Form submission
document.getElementById('packageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // AJAX form submission
    console.log('Submit package form');
});
</script>
@endsection