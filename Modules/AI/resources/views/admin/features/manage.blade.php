@extends('admin.layout')

@section('title', 'AI Features Yönetimi')

@include('ai::helper')

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="search-input" class="form-control" placeholder="Feature ara...">
                </div>
            </div>
            <!-- Orta Loading Alanı -->
            <div class="col position-relative">
                <div id="loading-indicator" class="position-absolute top-50 start-50 translate-middle text-center d-none" style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Filtreler ve Actions) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Filtreleme -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>
                            Filtrele
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}?status=active">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Sadece Aktif
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}?status=inactive">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                Sadece Pasif
                            </a>
                            <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}?featured=1">
                                <i class="fas fa-star text-warning me-2"></i>
                                Öne Çıkanlar
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}">
                                <i class="fas fa-refresh me-2"></i>
                                Filtreleri Temizle
                            </a>
                        </div>
                    </div>
                    
                    <!-- Yeni Feature Ekle -->
                    <a href="{{ route('admin.ai.features.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Yeni Feature
                    </a>
                </div>
            </div>
        </div>

        <!-- İstatistik Kartları -->
        <div class="row mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <i class="fas fa-cogs"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Toplam Features
                                </div>
                                <div class="text-muted">
                                    {{ $features->total() }} feature
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
                                    <i class="fas fa-check"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Aktif Features
                                </div>
                                <div class="text-muted">
                                    {{ $features->where('status', 'active')->count() }} aktif
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
                                    <i class="fas fa-star"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Öne Çıkanlar
                                </div>
                                <div class="text-muted">
                                    {{ $features->where('is_featured', true)->count() }} featured
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
                                    <i class="fas fa-layer-group"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Kategoriler
                                </div>
                                <div class="text-muted">
                                    {{ count($categories) }} kategori
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablo Bölümü -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap" id="features-table">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th style="width: 50px">Sıra</th>
                        <th>Feature</th>
                        <th>Kategori</th>
                        <th class="text-center" style="width: 80px">Durum</th>
                        <th>Helper</th>
                        <th class="text-center" style="width: 160px">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="sortable-features">
                    @forelse($features as $feature)
                    <tr data-id="{{ $feature->id }}" class="sortable-row">
                        <td>
                            <input class="form-check-input feature-checkbox" type="checkbox" value="{{ $feature->id }}">
                        </td>
                        <td>
                            <div class="sort-handle" style="cursor: move;">
                                <i class="fas fa-grip-vertical text-muted"></i>
                                <span class="badge badge-secondary ms-1">{{ $feature->sort_order ?? $loop->iteration }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    {{ $feature->icon ?? '⚡' }}
                                </span>
                                <div>
                                    <div class="font-weight-medium">{{ $feature->name }}</div>
                                    <div class="text-muted small">{{ Str::limit($feature->description ?? '', 60) }}</div>
                                    @if($feature->is_featured)
                                        <span class="badge badge-warning me-1 mt-1">
                                            <i class="fas fa-star me-1"></i>Öne Çıkan
                                        </span>
                                    @endif
                                    @if($feature->is_new)
                                        <span class="badge badge-success me-1 mt-1">
                                            <i class="fas fa-star me-1"></i>Yeni
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($feature->aiFeatureCategory)
                                <span class="badge badge-info">
                                    <i class="{{ $feature->aiFeatureCategory->icon ?? 'fas fa-folder' }} me-1"></i>
                                    {{ $feature->aiFeatureCategory->title }}
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Kategorisiz
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm status-toggle {{ $feature->status === 'active' ? 'text-success' : 'text-danger' }}" 
                                    data-id="{{ $feature->id }}" 
                                    title="Durumu değiştir"
                                    data-bs-toggle="tooltip">
                                @if($feature->status === 'active')
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-times"></i>
                                @endif
                            </button>
                        </td>
                        <td>
                            @if($feature->helper_function)
                                <span class="badge badge-info">
                                    <i class="fas fa-code me-1"></i>
                                    {{ Str::limit($feature->helper_function, 20) }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.ai.features.manage', $feature->id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Düzenle" 
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($feature->is_system)
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            title="Sistem feature'ı - silinemez" 
                                            disabled 
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-danger delete-feature" 
                                            data-id="{{ $feature->id }}" 
                                            data-name="{{ $feature->name }}"
                                            title="Sil" 
                                            data-bs-toggle="tooltip">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">Henüz feature eklenmemiş</p>
                                <p class="empty-subtitle text-muted">
                                    İlk AI feature'ınızı eklemek için yukarıdaki "Yeni Feature" butonunu kullanın.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.ai.features.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>
                                        İlk Feature'ı Ekle
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
    <div class="card-footer">
        {{ $features->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feature Silme Onayı</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu AI Feature'ını silmek istediğinizden emin misiniz?</p>
                <div class="alert alert-warning">
                    <strong id="delete-feature-name"></strong> feature'ı kalıcı olarak silinecek ve geri getirilemeyecek.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Sil</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/Sortable.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable yapısı
    const sortableTable = document.getElementById('sortable-features');
    if (sortableTable) {
        new Sortable(sortableTable, {
            animation: 150,
            handle: '.sort-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                // Sıralama değiştiğinde AJAX ile kaydet
                const items = Array.from(sortableTable.children).map((row, index) => ({
                    id: row.dataset.id,
                    sort_order: index + 1
                }));
                
                fetch('{{ route("admin.ai.features.update-sort") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ items: items })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Başarı mesajı göster
                        showToast('Sıralama güncellendi', 'success');
                    }
                });
            }
        });
    }

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const featureId = this.dataset.id;
            const icon = this.querySelector('i');
            
            fetch(`/admin/ai/features/${featureId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // İkon ve renk güncelle
                    if (data.status === 'active') {
                        icon.className = 'fas fa-check';
                        this.className = 'btn btn-sm status-toggle text-success';
                    } else {
                        icon.className = 'fas fa-times';
                        this.className = 'btn btn-sm status-toggle text-danger';
                    }
                    showToast('Durum güncellendi', 'success');
                }
            });
        });
    });

    // Delete feature
    document.querySelectorAll('.delete-feature').forEach(button => {
        button.addEventListener('click', function() {
            const featureId = this.dataset.id;
            const featureName = this.dataset.name;
            
            document.getElementById('delete-feature-name').textContent = featureName;
            document.getElementById('confirm-delete').dataset.id = featureId;
            
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        });
    });

    // Confirm delete
    document.getElementById('confirm-delete').addEventListener('click', function() {
        const featureId = this.dataset.id;
        
        fetch(`/admin/ai/features/${featureId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Silme işlemi başarısız', 'error');
            }
        });
    });

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.feature-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Toast function
    function showToast(message, type = 'info') {
        // Basit toast implementasyonu
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>
@endpush

@push('styles')
<style>
.sortable-ghost {
    opacity: 0.4;
}

.sort-handle:hover {
    background-color: var(--tblr-gray-50);
    border-radius: 4px;
}

.avatar {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>
@endpush