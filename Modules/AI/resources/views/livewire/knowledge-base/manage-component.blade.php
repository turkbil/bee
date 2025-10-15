@php
    View::share('pretitle', 'Bilgi Bankası');
@endphp

<div class="knowledge-base-component-wrapper">
    <div class="card">
        {{-- Helper/Info Section --}}
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-brain me-2 text-primary"></i>
                        <div>
                            <h3 class="card-title mb-0">AI Bilgi Bankası</h3>
                            <div class="text-muted small">Yapay zeka asistanınız için soru-cevap ekleyin</div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#formModal">
                        <i class="fas fa-plus me-2"></i>
                        Yeni Bilgi Ekle
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            {{-- Header Bölümü --}}
            <div class="row mx-2 my-3">
                {{-- Arama Kutusu --}}
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Soru, cevap veya kategori ara...">
                    </div>
                </div>
                {{-- Ortadaki Loading --}}
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, filterCategory, filterActive, toggleActive, delete"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">Güncelleniyor</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                {{-- Sağ Taraf (Filtreler) --}}
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <select class="form-select" wire:model.live="filterCategory" style="width: auto;">
                            <option value="">Tüm Kategoriler</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <select class="form-select" wire:model.live="filterActive" style="width: auto;">
                            <option value="all">Tümü</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Pasif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Card-based Listing --}}
            <div class="px-3 pb-3">
                @if($items->count() > 0)
                    <div class="row row-cols-1 row-cols-md-2 g-3" id="knowledge-base-sortable">
                        @foreach($items as $item)
                            <div class="col knowledge-base-item"
                                 wire:key="item-{{ $item->id }}"
                                 data-id="{{ $item->id }}">
                                <div class="card h-100 hover-shadow-sm">
                                    <div class="card-body">
                                        {{-- Drag Handle --}}
                                        <div class="knowledge-base-drag-handle position-absolute top-0 start-0 p-2 cursor-move" style="cursor: grab;">
                                            <i class="fas fa-grip-vertical text-muted"></i>
                                        </div>

                                        {{-- Header: Category + Status --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                                            <div>
                                                @if($item->category)
                                                    <span class="badge bg-azure">{{ $item->category }}</span>
                                                @else
                                                    <span class="badge bg-secondary-lt">Kategorisiz</span>
                                                @endif
                                                <span class="badge bg-secondary-lt ms-1">#{{ $item->sort_order }}</span>
                                            </div>
                                            <button wire:click="toggleActive({{ $item->id }})"
                                                class="btn btn-icon btn-sm {{ $item->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                                <!-- Loading Durumu -->
                                                <div wire:loading wire:target="toggleActive({{ $item->id }})"
                                                    class="spinner-border spinner-border-sm">
                                                </div>
                                                <!-- Normal Durum: Aktif/Pasif İkonları -->
                                                <div wire:loading.remove
                                                    wire:target="toggleActive({{ $item->id }})">
                                                    @if ($item->is_active)
                                                        <i class="fas fa-check"></i>
                                                    @else
                                                        <i class="fas fa-times"></i>
                                                    @endif
                                                </div>
                                            </button>
                                        </div>

                                        {{-- Question --}}
                                        <div class="mb-3">
                                            <h4 class="card-title mb-2">
                                                <i class="ti ti-message-question me-1 text-primary"></i>
                                                {{ $item->question }}
                                            </h4>
                                        </div>

                                        {{-- Answer --}}
                                        <div class="mb-3">
                                            @if($item->answer)
                                                <div>
                                                    {{ Str::limit($item->answer, 200) }}
                                                </div>
                                            @else
                                                <span class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Cevap girilmemiş
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Actions --}}
                                        <div class="d-flex gap-2 mt-auto">
                                            <button type="button" class="btn btn-sm btn-primary"
                                                    wire:click="edit({{ $item->id }})"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#formModal">
                                                <i class="fas fa-edit me-1"></i>
                                                Düzenle
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    wire:click="confirmDelete({{ $item->id }})">
                                                <i class="fas fa-trash me-1"></i>
                                                Sil
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty py-5">
                        <div class="empty-icon">
                            <i class="ti ti-brain ti-3x"></i>
                        </div>
                        <p class="empty-title">Bilgi bulunamadı</p>
                        <p class="empty-subtitle text-muted">
                            @if($search || $filterCategory || $filterActive !== 'all')
                                Filtrelere uygun kayıt bulunamadı.
                            @else
                                Henüz bilgi eklenmemiş. Yeni bilgi eklemek için yukarıdaki butonu kullanın.
                            @endif
                        </p>
                        @if($search || $filterCategory || $filterActive !== 'all')
                            <div class="empty-action">
                                <button type="button" class="btn btn-primary" wire:click="$set('search', ''); $set('filterCategory', ''); $set('filterActive', 'all')">
                                    <i class="fas fa-times me-2"></i>
                                    Filtreleri Temizle
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Pagination --}}
        <div class="card-footer">
            @if ($items->hasPages())
                {{ $items->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $items->total() }}</span> kayıt
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Form Modal --}}
    <div class="modal fade" id="formModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditing ? 'Bilgi Düzenle' : 'Yeni Bilgi Ekle' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                       wire:model="category"
                                       placeholder="Ör: Kargo, Ödeme, Ürün">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Opsiyonel - Bilgileri gruplamak için</small>
                            </div>

                            {{-- Sort Order --}}
                            <div class="col-md-6">
                                <label class="form-label">Sıralama</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                       wire:model="sort_order"
                                       min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Question --}}
                            <div class="col-12">
                                <label class="form-label required">Soru</label>
                                <input type="text" class="form-control @error('question') is-invalid @enderror"
                                       wire:model="question"
                                       placeholder="Ör: Kargo ücreti ne kadar?">
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Answer --}}
                            <div class="col-12">
                                <label class="form-label">Cevap</label>
                                <textarea class="form-control @error('answer') is-invalid @enderror"
                                          wire:model="answer"
                                          rows="5"
                                          placeholder="Ör: 150 TL üzeri alışverişlerde kargo ücretsizdir."></textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Bu cevap AI bot tarafından kullanılacak</small>
                            </div>

                            {{-- Active Status --}}
                            <div class="col-12">
                                <label class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" wire:model="is_active">
                                    <span class="form-check-label">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            İptal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ $isEditing ? 'Güncelle' : 'Kaydet' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h3>Emin misiniz?</h3>
                        <p class="text-muted">Bu bilgiyi silmek istediğinizden emin misiniz?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">
                            İptal
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            Evet, Sil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .knowledge-base-component-wrapper .hover-shadow-sm {
        transition: box-shadow 0.2s ease-in-out, border-color 0.2s ease-in-out;
        border: 1px solid rgba(98, 105, 118, 0.16);
        position: relative;
    }

    .knowledge-base-component-wrapper .hover-shadow-sm:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        border-color: rgba(98, 105, 118, 0.24);
    }

    .knowledge-base-component-wrapper .card-body .btn {
        transition: all 0.15s ease-in-out;
    }

    /* Dark mode support */
    [data-bs-theme="dark"] .knowledge-base-component-wrapper .hover-shadow-sm {
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="dark"] .knowledge-base-component-wrapper .hover-shadow-sm:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 255, 255, 0.16);
    }

    /* Drag handle styles */
    .knowledge-base-drag-handle {
        cursor: grab;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .knowledge-base-drag-handle:hover {
        opacity: 1;
    }

    .knowledge-base-drag-handle:active {
        cursor: grabbing;
    }

    /* Sortable ghost and drag styles */
    .sortable-ghost {
        opacity: 0.4;
    }

    .sortable-drag {
        opacity: 0.8;
    }

    .knowledge-base-item.sortable-chosen .card {
        border-color: var(--tblr-primary);
        box-shadow: 0 0.5rem 1.5rem rgba(32, 107, 196, 0.15);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // Bootstrap modal event listeners for Livewire
        const formModal = document.getElementById('formModal');

        if (formModal) {
            formModal.addEventListener('hidden.bs.modal', function () {
                @this.call('resetForm');
            });
        }

        // Initialize SortableJS for drag-drop sorting
        initKnowledgeBaseSortable();
    });

    function initKnowledgeBaseSortable() {
        const sortableList = document.getElementById('knowledge-base-sortable');

        if (!sortableList) {
            console.warn('Knowledge Base sortable list not found');
            return;
        }

        const sortable = new Sortable(sortableList, {
            animation: 200,
            handle: '.knowledge-base-drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            forceFallback: true,
            fallbackClass: 'sortable-fallback',
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: function (evt) {
                // Get all item IDs in new order
                const items = Array.from(sortableList.querySelectorAll('.knowledge-base-item'));
                const newOrder = items.map(item => item.getAttribute('data-id'));

                // Call Livewire method to update order
                @this.call('updateOrder', newOrder);
            }
        });
    }

    // Reinitialize sortable after Livewire updates
    document.addEventListener('livewire:navigated', () => {
        initKnowledgeBaseSortable();
    });

    // Also reinitialize when items list updates
    Livewire.hook('morph.updated', ({ el, component }) => {
        if (el.id === 'knowledge-base-sortable') {
            initKnowledgeBaseSortable();
        }
    });
</script>
@endpush
