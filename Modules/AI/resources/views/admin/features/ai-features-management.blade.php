@php
    View::share('pretitle', 'AI Özellik Listesi');
@endphp
@include('ai::helper')

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
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Feature ara...">
                </div>
            </div>
            <!-- Orta Loading Alanı -->
            <div class="col position-relative">
                <div wire:loading wire:target="render, search, perPage, status, category, featured, toggleStatus, deleteFeature, updateSort, bulkDelete, bulkStatusUpdate, toggleFeatured" 
                     class="position-absolute top-50 start-50 translate-middle text-center d-none" 
                     wire:loading.class.remove="d-none"
                     style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">Güncelleniyor...</div>
                    <div class="progress mb-1">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>
            <!-- Sağ Taraf (Filtreler ve Actions) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Kategori Filtresi -->
                    <div style="width: 200px">
                        <select wire:model.live="category" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="width: 80px; min-width: 80px">
                        <select wire:model.live="perPage" class="form-control listing-filter-select" 
                                data-choices 
                                data-choices-search="false"
                                data-choices-filter="true">
                            <option value="10"><nobr>10</nobr></option>
                            <option value="50"><nobr>50</nobr></option>
                            <option value="100"><nobr>100</nobr></option>
                            <option value="150"><nobr>150</nobr></option>
                            <option value="500"><nobr>500</nobr></option>
                            <option value="1000"><nobr>1000</nobr></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <i class="fas fa-rocket"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    Toplam Feature
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
                                    Aktif Feature
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
                                    Öne Çıkan
                                </div>
                                <div class="text-muted">
                                    {{ $features->where('is_featured', true)->count() }} öne çıkan
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
                                <span class="bg-blue text-white avatar">
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
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable" id="sortable-list">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                                <button class="table-sort {{ $sortField === 'sort_order' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('sort_order')">
                                </button>
                            </div>
                        </th>
                        <th style="width: 40px" class="text-center">
                            <i class="fas fa-arrows-alt" title="Sırala"></i>
                        </th>
                        <th style="width: 50px"></th>
                        <th>
                            <button class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('name')">
                                Başlık
                            </button>
                        </th>
                        <th style="width: 160px">
                            <button class="table-sort {{ $sortField === 'ai_feature_category_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('ai_feature_category_id')">
                                Kategori
                            </button>
                        </th>
                        <th class="text-center" style="width: 50px" data-bs-toggle="tooltip" data-bs-placement="top" title="Öne Çıkan">
                            <i class="fas fa-star"></i>
                        </th>
                        <th class="text-center" style="width: 80px" data-bs-toggle="tooltip" data-bs-placement="top" title="Durum">
                            <button class="table-sort {{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('status')">
                                Durum
                            </button>
                        </th>
                        <th class="text-center" style="width: 160px">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($features as $feature)
                    <tr class="hover-trigger sortable-item" wire:key="feature-{{ $feature->id }}" data-id="{{ $feature->id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide order-number">{{ $feature->sort_order ?? $loop->iteration }}</span>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $feature->id }}"
                                    class="form-check-input hover-show" @if(in_array($feature->id, $selectedItems)) checked @endif>
                            </div>
                        </td>
                        <td class="text-center">
                            <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: move;"></i>
                        </td>
                        <td class="text-center">
                            @if($feature->icon)
                                <i class="{{ $feature->icon }}"></i>
                            @else
                                <i class="fas fa-rocket"></i>
                            @endif
                        </td>
                        <td>
                            <div>
                                <div class="font-weight-medium">{{ $feature->name }}</div>
                                <div class="text-muted small">{{ Str::limit($feature->description ?? '', 60) }}</div>
                            </div>
                        </td>
                        <td>
                            @if($feature->aiFeatureCategory)
                                <a href="javascript:void(0);"
                                    wire:click="$set('category', '{{ $feature->aiFeatureCategory->ai_feature_category_id }}')" 
                                    class="text-muted {{ $category == $feature->aiFeatureCategory->ai_feature_category_id ? 'text-primary' : '' }}">
                                    <i class="{{ $feature->aiFeatureCategory->icon ?? 'fas fa-folder' }} me-1"></i>
                                    {{ $feature->aiFeatureCategory->title }}
                                </a>
                            @else
                                <span class="text-muted">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Kategorisiz
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-ghost-secondary btn-sm {{ $feature->is_featured ? 'text-warning' : 'text-muted' }}" 
                                    wire:click="toggleFeatured({{ $feature->id }})"
                                    title="Öne çıkana ekle/çıkar"
                                    data-bs-toggle="tooltip">
                                <i class="fas fa-star"></i>
                            </button>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm status-toggle {{ $feature->status === 'active' ? 'text-success' : 'text-danger' }}" 
                                    wire:click="toggleStatus({{ $feature->id }})"
                                    title="Durumu değiştir"
                                    data-bs-toggle="tooltip"
                                    @if(isset($loadingToggle[$feature->id]) && $loadingToggle[$feature->id]) disabled @endif>
                                
                                @if(isset($loadingToggle[$feature->id]) && $loadingToggle[$feature->id])
                                    <div class="spinner-border spinner-border-sm"></div>
                                @else
                                    @if($feature->status === 'active')
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-times"></i>
                                    @endif
                                @endif
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('admin.ai.features.manage', $feature->id) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Düzenle">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                    <div class="col lh-1">
                                        <div class="dropdown mt-1">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if($feature->is_system)
                                                    <span class="dropdown-item text-muted">
                                                        <i class="fas fa-lock me-2"></i>Sistem Feature'ı
                                                    </span>
                                                @else
                                                    <a href="javascript:void(0);" wire:click="deleteFeature({{ $feature->id }})"
                                                       onclick="return confirm('{{ $feature->name }} feature\'ını silmek istediğinizden emin misiniz?')"
                                                       class="dropdown-item link-danger">
                                                        <i class="fas fa-trash me-2"></i>Sil
                                                    </a>
                                                @endif
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
                            <div class="empty">
                                <p class="empty-title">Henüz feature eklenmemiş</p>
                                <p class="empty-subtitle text-muted">
                                    İlk AI feature'ınızı eklemek için yukarıdaki "Yeni Feature" butonunu kullanın.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $features->links() }}

    @include('ai::admin.partials.bulk-actions', ['bulkActionsEnabled' => $this->bulkActionsEnabled])

    <livewire:modals.bulk-delete-modal />
    <livewire:modals.delete-modal />
</div>

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}?v={{ filemtime(public_path('admin-assets/libs/sortable/sortable.min.js')) }}"></script>
<script>
document.addEventListener("livewire:initialized", () => {
    const sortableList = document.getElementById("sortable-list").querySelector('tbody');
    if (sortableList) {
        new Sortable(sortableList, {
            animation: 250,
            delay: 50,
            delayOnTouchOnly: true,
            handle: ".sortable-handle",
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            forceFallback: false,
            onStart: function () {
                document.body.style.cursor = "grabbing";
            },
            onEnd: function (evt) {
                document.body.style.cursor = "default";
                const items = Array.from(sortableList.children).map(
                    (item, index) => ({
                        value: parseInt(item.dataset.id),
                        order: index + 1,
                    })
                );

                // Sayı animasyonu
                sortableList
                    .querySelectorAll(".order-number")
                    .forEach((el, index) => {
                        const oldNumber = parseInt(el.textContent);
                        const newNumber = index + 1;

                        if (oldNumber !== newNumber) {
                            el.classList.add("animate");
                            setTimeout(() => {
                                el.textContent = newNumber;
                            }, 250);
                            setTimeout(() => {
                                el.classList.remove("animate");
                            }, 500);
                        }
                    });

                Livewire.dispatch("updateOrder", { list: items });
            },
        });
    }
});
</script>
@endpush