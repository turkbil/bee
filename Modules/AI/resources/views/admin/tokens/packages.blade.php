@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', 'AI Token Yönetimi')
@section('title', 'Token Paketleri')

@section('content')
<!-- Add Package Button -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Token Paket Yönetimi</h3>
                <div class="card-actions">
                    <button class="btn btn-primary" onclick="addPackage()">
                        <i class="fas fa-plus me-2"></i>
                        Yeni Paket Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Package List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mevcut Token Paketleri</h3>
            </div>
            <div class="card-body">
                @if($packages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Sıra</th>
                                <th>Paket Adı</th>
                                <th>Token Miktarı</th>
                                <th>Fiyat</th>
                                <th>Açıklama</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="packageList">
                            @foreach($packages as $package)
                            <tr data-package-id="{{ $package->id }}">
                                <td>
                                    <span class="badge badge-outline">{{ $package->sort_order }}</span>
                                </td>
                                <td>
                                    <strong>{{ $package->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-blue">
                                        {{ \App\Helpers\TokenHelper::format($package->token_amount) }} Token
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline text-green">
                                        {{ number_format($package->price, 2) }} {{ $package->currency }}
                                    </span>
                                </td>
                                <td>
                                    {{ Str::limit($package->description ?? '-', 50) }}
                                </td>
                                <td>
                                    <span class="badge badge-outline {{ $package->is_active ? 'text-green' : 'text-red' }}">
                                        {{ $package->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-list">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="editPackage({{ $package->id }})"
                                                title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deletePackage({{ $package->id }})"
                                                title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-box text-muted" style="font-size: 64px;"></i>
                    </div>
                    <p class="empty-title">Henüz token paketi yok</p>
                    <p class="empty-subtitle text-muted">
                        Yeni token paketi eklemek için "Yeni Paket Ekle" butonunu kullanın.
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Package Modal -->
<div class="modal modal-blur fade" id="packageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="packageModalTitle">Token Paketi Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="packageForm">
                    <input type="hidden" id="packageId" name="packageId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="packageName" class="form-label">Paket Adı</label>
                                <input type="text" class="form-control" id="packageName" name="name" 
                                       placeholder="Örn: Başlangıç Paketi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tokenAmount" class="form-label">Token Miktarı</label>
                                <input type="number" class="form-control" id="tokenAmount" name="token_amount" 
                                       placeholder="Örn: 10000" min="1" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Fiyat</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       placeholder="Örn: 99.99" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">Para Birimi</label>
                                <select class="form-control" id="currency" name="currency" required>
                                    <option value="TRY">TRY (Türk Lirası)</option>
                                    <option value="USD">USD (Amerikan Doları)</option>
                                    <option value="EUR">EUR (Euro)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Paket açıklaması..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sortOrder" class="form-label">Sıra</label>
                                <input type="number" class="form-control" id="sortOrder" name="sort_order" 
                                       placeholder="Görüntülenme sırası" min="0" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durum</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" checked>
                                    <label class="form-check-label" for="isActive">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="features" class="form-label">Özellikler</label>
                        <div id="featuresContainer">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Özellik adını yazın" name="features[]">
                                <button class="btn btn-outline-secondary" type="button" onclick="addFeature()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" onclick="submitPackage()">Kaydet</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Package form functions
function addPackage() {
    document.getElementById('packageModalTitle').textContent = 'Token Paketi Ekle';
    document.getElementById('packageForm').reset();
    document.getElementById('packageId').value = '';
    document.getElementById('isActive').checked = true;
    
    // Clear features
    const featuresContainer = document.getElementById('featuresContainer');
    featuresContainer.innerHTML = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" placeholder="Özellik adını yazın" name="features[]">
            <button class="btn btn-outline-secondary" type="button" onclick="addFeature()">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('packageModal'));
    modal.show();
}

function editPackage(packageId) {
    fetch(`/admin/ai/tokens/packages/${packageId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const package = data.package;
                
                document.getElementById('packageModalTitle').textContent = 'Token Paketi Düzenle';
                document.getElementById('packageId').value = package.id;
                document.getElementById('packageName').value = package.name;
                document.getElementById('tokenAmount').value = package.token_amount;
                document.getElementById('price').value = package.price;
                document.getElementById('currency').value = package.currency;
                document.getElementById('description').value = package.description || '';
                document.getElementById('sortOrder').value = package.sort_order;
                document.getElementById('isActive').checked = package.is_active;
                
                // Load features
                const featuresContainer = document.getElementById('featuresContainer');
                featuresContainer.innerHTML = '';
                
                if (package.features && package.features.length > 0) {
                    package.features.forEach(feature => {
                        addFeatureWithValue(feature);
                    });
                } else {
                    addFeature();
                }
                
                const modal = new bootstrap.Modal(document.getElementById('packageModal'));
                modal.show();
            } else {
                alert('Paket bilgileri yüklenemedi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
}

function addFeature() {
    const featuresContainer = document.getElementById('featuresContainer');
    const featureDiv = document.createElement('div');
    featureDiv.className = 'input-group mb-2';
    featureDiv.innerHTML = `
        <input type="text" class="form-control" placeholder="Özellik adını yazın" name="features[]">
        <button class="btn btn-outline-danger" type="button" onclick="removeFeature(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    featuresContainer.appendChild(featureDiv);
}

function addFeatureWithValue(value) {
    const featuresContainer = document.getElementById('featuresContainer');
    const featureDiv = document.createElement('div');
    featureDiv.className = 'input-group mb-2';
    featureDiv.innerHTML = `
        <input type="text" class="form-control" placeholder="Özellik adını yazın" name="features[]" value="${value}">
        <button class="btn btn-outline-danger" type="button" onclick="removeFeature(this)">
            <i class="fas fa-trash"></i>
        </button>
    `;
    featuresContainer.appendChild(featureDiv);
}

function removeFeature(button) {
    button.parentElement.remove();
}

function submitPackage() {
    const form = document.getElementById('packageForm');
    const formData = new FormData(form);
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const packageId = formData.get('packageId');
    const isEdit = packageId && packageId !== '';
    
    // Prepare features array
    const features = [];
    const featureInputs = document.querySelectorAll('input[name="features[]"]');
    featureInputs.forEach(input => {
        if (input.value.trim()) {
            features.push(input.value.trim());
        }
    });
    
    // Prepare data
    const data = {
        name: formData.get('name'),
        token_amount: parseInt(formData.get('token_amount')),
        price: parseFloat(formData.get('price')),
        currency: formData.get('currency'),
        description: formData.get('description'),
        sort_order: parseInt(formData.get('sort_order')),
        is_active: document.getElementById('isActive').checked,
        features: features
    };
    
    const url = isEdit ? `/admin/ai/tokens/packages/${packageId}` : '/admin/ai/tokens/packages';
    const method = isEdit ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('packageModal')).hide();
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

function deletePackage(packageId) {
    if (confirm('Bu paketi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')) {
        fetch(`/admin/ai/tokens/packages/${packageId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

// Initialize sortable for package order
document.addEventListener('DOMContentLoaded', function() {
    const packageList = document.getElementById('packageList');
    if (packageList) {
        Sortable.create(packageList, {
            animation: 150,
            onEnd: function(evt) {
                const packages = [];
                const rows = packageList.querySelectorAll('tr[data-package-id]');
                rows.forEach((row, index) => {
                    packages.push({
                        id: parseInt(row.getAttribute('data-package-id')),
                        sort_order: index + 1
                    });
                });
                
                // Update server
                fetch('/admin/ai/tokens/packages/update-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        packages: packages
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Sıralama güncellenemedi: ' + data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Bir hata oluştu');
                    location.reload();
                });
            }
        });
    }
});
</script>
@endpush