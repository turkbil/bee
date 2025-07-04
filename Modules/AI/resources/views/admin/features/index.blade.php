@extends('admin.layouts.app')
@section('title', 'AI Özellikleri Yönetimi')

@section('content')
<div class="container-xl">
    <!-- Sayfa Başlığı -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">AI Modülü</div>
                    <h2 class="page-title">AI Özellikleri Yönetimi</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.features.manage') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <i class="fas fa-plus me-2"></i>
                            Yeni AI Özelliği
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtreler -->
    <div class="page-body">
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.ai.features.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                                       placeholder="AI özelliği ara...">
                                <label>Arama</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-control" name="category">
                                    <option value="">Tüm Kategoriler</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>Kategori</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-control" name="status">
                                    <option value="">Tüm Durumlar</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>Durum</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrele
                            </button>
                            <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Temizle
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- AI Özellikleri Listesi -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">AI Özellikleri ({{ $features->total() }})</h3>
                <div class="card-actions">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i>Toplu İşlemler
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="bulkStatusUpdate('active')">
                                <i class="fas fa-check-circle text-success me-2"></i>Aktif Yap
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="bulkStatusUpdate('inactive')">
                                <i class="fas fa-times-circle text-danger me-2"></i>Pasif Yap
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            @if($features->count() > 0)
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Sıra</th>
                            <th>Özellik</th>
                            <th>Kategori</th>
                            <th>Prompt'lar</th>
                            <th>Durum</th>
                            <th>Kullanım</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-features">
                        @foreach($features as $feature)
                        <tr data-id="{{ $feature->id }}">
                            <td>
                                <input type="checkbox" class="feature-checkbox" value="{{ $feature->id }}">
                            </td>
                            <td>
                                <div class="sort-handle cursor-pointer">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                    <span class="ms-1">{{ $feature->sort_order }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span class="fs-2">{{ $feature->emoji ?: '🤖' }}</span>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $feature->name }}</div>
                                        <div class="text-muted small">{{ Str::limit($feature->description, 50) }}</div>
                                        @if($feature->is_system)
                                            <span class="badge bg-info-lt">Sistem</span>
                                        @endif
                                        @if($feature->is_featured)
                                            <span class="badge bg-warning-lt">Öne Çıkan</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-azure-lt">{{ $feature->getCategoryName() }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @foreach($feature->prompts->take(3) as $prompt)
                                        <span class="badge bg-gray-lt" title="{{ $prompt->name }}">
                                            {{ $prompt->pivot->prompt_role }}
                                        </span>
                                    @endforeach
                                    @if($feature->prompts->count() > 3)
                                        <span class="badge bg-secondary">+{{ $feature->prompts->count() - 3 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="{{ $feature->getBadgeClass() }}">
                                    {{ ucfirst($feature->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <div><strong>{{ number_format($feature->usage_count) }}</strong> kullanım</div>
                                    @if($feature->avg_rating > 0)
                                        <div class="text-warning">
                                            ⭐ {{ number_format($feature->avg_rating, 1) }}/5
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.ai.features.show', $feature) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ai.features.manage', $feature) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.ai.features.duplicate', $feature) }}" 
                                          style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Kopyala">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    @if(!$feature->is_system)
                                        <form method="POST" action="{{ route('admin.ai.features.destroy', $feature) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Bu AI özelliğini silmek istediğinizden emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                {{ $features->withQueryString()->links() }}
            </div>
            @else
            <div class="card-body text-center py-5">
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-robot fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Henüz AI özelliği bulunmuyor</p>
                    <p class="empty-subtitle text-muted">İlk AI özelliğinizi oluşturmak için aşağıdaki butona tıklayın.</p>
                    <div class="empty-action">
                        <a href="{{ route('admin.ai.features.manage') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Yeni AI Özelliği Oluştur
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sortable tablo
    const sortable = Sortable.create(document.getElementById('sortable-features'), {
        handle: '.sort-handle',
        animation: 150,
        onEnd: function(evt) {
            const orders = [];
            document.querySelectorAll('#sortable-features tr').forEach((row, index) => {
                orders.push({
                    id: row.dataset.id,
                    sort_order: index + 1
                });
            });
            
            fetch('{{ route("admin.ai.features.update-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({orders: orders})
            });
        }
    });
    
    // Tümünü seç
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.feature-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

// Toplu durum güncelleme
function bulkStatusUpdate(status) {
    const selected = Array.from(document.querySelectorAll('.feature-checkbox:checked')).map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Lütfen en az bir özellik seçin.');
        return;
    }
    
    if (confirm(`Seçili ${selected.length} özelliğin durumunu ${status} yapmak istediğinizden emin misiniz?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.ai.features.bulk-status") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="status" value="${status}">
            ${selected.map(id => `<input type="hidden" name="feature_ids[]" value="${id}">`).join('')}
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection