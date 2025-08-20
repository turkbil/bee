@include('ai::helper')

<div>
    {{-- Tab Navigation --}}
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                <li class="nav-item">
                    <a href="#packages-list" 
                       class="nav-link @if($activeTab === 'list') active @endif"
                       wire:click="setActiveTab('list')">
                        <i class="fas fa-list me-2"></i>Paket Listesi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#packages-manage" 
                       class="nav-link @if($activeTab === 'manage') active @endif"
                       wire:click="setActiveTab('manage')">
                        <i class="fas fa-{{ $editMode ? 'edit' : 'plus' }} me-2"></i>
                        {{ $editMode ? 'Paket Düzenle' : 'Yeni Paket' }}
                    </a>
                </li>
                <li class="nav-item ms-auto">
                    <div class="d-flex gap-2">
                        @if($activeTab === 'list')
                            <button wire:click="createPackage" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Yeni Paket
                            </button>
                        @endif
                        @if($activeTab === 'manage')
                            <button wire:click="setActiveTab('list')" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Listeye Dön
                            </button>
                        @endif
                    </div>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                {{-- Paket Listesi Tab --}}
                <div class="tab-pane @if($activeTab === 'list') active show @endif" id="packages-list">
                    {{-- Arama ve Filtre --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       wire:model.live="search" 
                                       class="form-control" 
                                       placeholder="Paket ara...">
                                <label>Paket Ara</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(count($selectedItems) > 0)
                            <div class="d-flex gap-2">
                                <button wire:click="bulkToggleActive(true)" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-2"></i>Seçilenleri Aktif Yap
                                </button>
                                <button wire:click="bulkToggleActive(false)" class="btn btn-warning btn-sm">
                                    <i class="fas fa-times me-2"></i>Seçilenleri Pasif Yap
                                </button>
                                <span class="badge badge-primary align-self-center">{{ count($selectedItems) }} seçili</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Sürüklenebilir Paket Listesi --}}
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px">
                                        <input type="checkbox" 
                                               wire:model.live="selectAll"
                                               wire:click="toggleSelectAll()"
                                               class="form-check-input">
                                    </th>
                                    <th style="width: 80px; cursor: pointer" wire:click="sortBy('sort_order')">
                                        <div class="d-flex align-items-center">
                                            Sıra
                                            @if($sortField === 'sort_order')
                                                <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1 text-muted"></i>
                                            @endif
                                        </div>
                                    </th>
                                    <th style="cursor: pointer" wire:click="sortBy('name')">
                                        <div class="d-flex align-items-center">
                                            Paket Bilgileri
                                            @if($sortField === 'name')
                                                <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1 text-muted"></i>
                                            @endif
                                        </div>
                                    </th>
                                    <th style="cursor: pointer" wire:click="sortBy('token_amount')">
                                        <div class="d-flex align-items-center">
                                            Kredi Miktarı
                                            @if($sortField === 'token_amount')
                                                <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1 text-muted"></i>
                                            @endif
                                        </div>
                                    </th>
                                    <th style="cursor: pointer" wire:click="sortBy('price')">
                                        <div class="d-flex align-items-center">
                                            Fiyat
                                            @if($sortField === 'price')
                                                <i class="fas fa-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1 text-muted"></i>
                                            @endif
                                        </div>
                                    </th>
                                    <th class="text-center">Durum</th>
                                    <th class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="sortable-packages">
                                @forelse($packages as $package)
                                <tr wire:key="package-{{ $package->id }}" 
                                    data-id="{{ $package->id }}" 
                                    class="package-row" 
                                    style="cursor: move;">
                                    <td>
                                        <input type="checkbox" 
                                               wire:model.live="selectedItems" 
                                               value="{{ $package->id }}"
                                               class="form-check-input">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-grip-vertical text-muted me-2"></i>
                                            <span class="badge badge-primary">{{ $package->sort_order }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="fw-bold">{{ $package->name }}</div>
                                                @if($package->description)
                                                    <div class="text-muted small">{{ Str::limit($package->description, 60) }}</div>
                                                @endif
                                                @if($package->features && is_array($package->features) && count($package->features) > 0)
                                                    <div class="mt-1">
                                                        @foreach(array_slice($package->features, 0, 2) as $feature)
                                                            <span class="badge me-1">{{ $feature }}</span>
                                                        @endforeach
                                                        @if(is_array($package->features) && count($package->features) > 2)
                                                            <span class="badge">+{{ (is_array($package->features) ? count($package->features) : 0) - 2 }} özellik</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($package->token_amount, 0) }}</div>
                                        <div class="text-muted small">kredi</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($package->price, 2) }}</div>
                                        <div class="text-muted small">{{ $package->currency }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button wire:click="toggleActive({{ $package->id }})" 
                                                    class="btn btn-icon btn-sm {{ $package->is_active ? 'text-success' : 'text-muted' }}"
                                                    title="{{ $package->is_active ? 'Aktif' : 'Pasif' }}">
                                                <i class="fas fa-{{ $package->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                            </button>
                                            <button wire:click="togglePopular({{ $package->id }})" 
                                                    class="btn btn-icon btn-sm {{ $package->is_popular ? 'text-warning' : 'text-muted' }}"
                                                    title="{{ $package->is_popular ? 'Popüler' : 'Normal' }}">
                                                <i class="fas fa-{{ $package->is_popular ? 'star' : 'star-o' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button wire:click="editPackage({{ $package->id }})" 
                                                    class="btn btn-icon btn-sm text-primary"
                                                    title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="confirmDelete({{ $package->id }})" 
                                                    class="btn btn-icon btn-sm text-danger"
                                                    title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            @if($search)
                                                Arama kriterinize uygun paket bulunamadı.
                                            @else
                                                Henüz paket oluşturulmamış.
                                                <button wire:click="createPackage" class="btn btn-primary btn-sm ms-2">
                                                    <i class="fas fa-plus me-1"></i>İlk Paketi Oluştur
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Sayfalama --}}
                    @if($packages->hasPages())
                        <div class="mt-3">
                            {{ $packages->links() }}
                        </div>
                    @endif
                </div>

                {{-- Paket Yönetimi Tab --}}
                <div class="tab-pane @if($activeTab === 'manage') active show @endif" id="packages-manage">
                    <form wire:submit="savePackage">
                        <div class="row">
                            {{-- Sol Kolon - Ana Bilgiler --}}
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            {{ $editMode ? 'Paket Düzenle' : 'Yeni Paket Oluştur' }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Paket Adı --}}
                                        <div class="form-floating mb-3">
                                            <input type="text" 
                                                   wire:model="name" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   placeholder="Paket adı">
                                            <label>Paket Adı *</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Kredi Miktarı ve Fiyat --}}
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" 
                                                           wire:model="token_amount" 
                                                           class="form-control @error('token_amount') is-invalid @enderror" 
                                                           min="1"
                                                           placeholder="Token miktarı">
                                                    <label>Kredi Miktarı *</label>
                                                    @error('token_amount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating mb-3">
                                                    <input type="number" 
                                                           wire:model="price" 
                                                           class="form-control @error('price') is-invalid @enderror" 
                                                           step="0.01"
                                                           min="0"
                                                           placeholder="Fiyat">
                                                    <label>Fiyat *</label>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-floating mb-3">
                                                    <select wire:model="currency" 
                                                            class="form-select @error('currency') is-invalid @enderror">
                                                        <option value="TRY">TRY</option>
                                                        <option value="USD">USD</option>
                                                        <option value="EUR">EUR</option>
                                                    </select>
                                                    <label>Para Birimi</label>
                                                    @error('currency')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Açıklama --}}
                                        <div class="form-floating mb-3">
                                            <textarea wire:model="description" 
                                                      class="form-control @error('description') is-invalid @enderror" 
                                                      style="height: 80px"
                                                      placeholder="Paket açıklaması"></textarea>
                                            <label>Açıklama</label>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Özellikler --}}
                                        <div class="mb-3">
                                            <label class="form-label">Paket Özellikleri</label>
                                            
                                            {{-- Yeni Özellik Ekleme --}}
                                            <div class="input-group mb-2">
                                                <div class="form-floating flex-fill">
                                                    <input type="text" 
                                                           wire:model="newFeature" 
                                                           wire:keydown.enter.prevent="addFeature"
                                                           class="form-control" 
                                                           placeholder="Yeni özellik">
                                                    <label>Yeni özellik ekle</label>
                                                </div>
                                                <button type="button" 
                                                        wire:click="addFeature" 
                                                        class="btn btn-primary"
                                                        {{ (is_array($features) && count($features) >= 10) ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>

                                            {{-- Mevcut Özellikler --}}
                                            @if(is_array($features) && count($features) > 0)
                                                <div class="row g-2">
                                                    @foreach($features as $index => $feature)
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <span class="flex-fill">{{ $feature }}</span>
                                                            <button type="button" 
                                                                    wire:click="removeFeature({{ $index }})"
                                                                    class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <small class="text-muted">{{ is_array($features) ? count($features) : 0 }}/10 özellik eklendi</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sağ Kolon - Ayarlar --}}
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Paket Ayarları</h3>
                                    </div>
                                    <div class="card-body">
                                        {{-- Sıralama --}}
                                        <div class="form-floating mb-3">
                                            <input type="number" 
                                                   wire:model="sort_order" 
                                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                                   min="0"
                                                   placeholder="Sıralama">
                                            <label>Sıralama</label>
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Durum Ayarları --}}
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:model="is_active" 
                                                   id="is_active">
                                            <label class="form-check-label" for="is_active">
                                                Paket Aktif
                                            </label>
                                        </div>

                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:model="is_popular" 
                                                   id="is_popular">
                                            <label class="form-check-label" for="is_popular">
                                                Popüler Paket
                                            </label>
                                        </div>

                                        {{-- Önizleme --}}
                                        @if($name || $token_amount || $price)
                                        <div class="mt-4">
                                            <h4 class="text-muted mb-3">Önizleme</h4>
                                            <div class="card">
                                                <div class="card-body text-center">
                                                    @if($is_popular)
                                                        <div class="ribbon bg-warning">Popüler</div>
                                                    @endif
                                                    <h3 class="card-title">{{ $name ?: 'Paket Adı' }}</h3>
                                                    <div class="text-h1 text-primary">
                                                        {{ $token_amount ? ai_format_token_count($token_amount) : '0' }}
                                                    </div>
                                                    <div class="text-muted mb-2">Kredi</div>
                                                    @if($price)
                                                        <div class="text-h2">
                                                            {{ number_format($price, 2) }} {{ $currency }}
                                                        </div>
                                                    @endif
                                                    @if($description)
                                                        <p class="text-muted small mt-2">{{ Str::limit($description, 60) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Buttons --}}
                        <div class="mt-4">
                            <div class="d-flex justify-content-between">
                                <button type="button" 
                                        wire:click="setActiveTab('list')" 
                                        class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>İptal
                                </button>
                                <button type="submit" 
                                        class="btn btn-primary">
                                    <span wire:loading.remove>
                                        <i class="fas fa-{{ $editMode ? 'save' : 'plus' }} me-2"></i>
                                        {{ $editMode ? 'Güncelle' : 'Oluştur' }}
                                    </span>
                                    <span wire:loading>
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        {{ $editMode ? 'Güncelleniyor...' : 'Oluşturuluyor...' }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    @if($showDeleteModal)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Paket Sil</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                </div>
                <div class="modal-body">
                    <p>Bu paketi silmek istediğinizden emin misiniz?</p>
                    <p class="text-danger"><strong>Bu işlem geri alınamaz!</strong></p>
                    
                    <div class="form-floating">
                        <input type="text" 
                               wire:model="deleteConfirmText" 
                               class="form-control @error('deleteConfirmText') is-invalid @enderror" 
                               placeholder="SİL">
                        <label>Silmek için "SİL" yazın</label>
                        @error('deleteConfirmText')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" 
                            wire:click="$set('showDeleteModal', false)" 
                            class="btn btn-secondary">
                        İptal
                    </button>
                    <button type="button" 
                            wire:click="deletePackage" 
                            class="btn btn-danger"
                            {{ $deleteConfirmText !== 'SİL' ? 'disabled' : '' }}>
                        <span wire:loading.remove>Sil</span>
                        <span wire:loading>Siliniyor...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortablePackages = document.getElementById('sortable-packages');
    if (sortablePackages) {
        new Sortable(sortablePackages, {
            animation: 250,
            delay: 50,
            ghostClass: "table-active",
            chosenClass: "table-warning",
            onEnd: function (evt) {
                const items = Array.from(sortablePackages.children).map((row, index) => ({
                    id: parseInt(row.dataset.id),
                    order: index + 1
                }));
                
                @this.call('updatePackageOrder', items);
            }
        });
    }
});
</script>
@endpush