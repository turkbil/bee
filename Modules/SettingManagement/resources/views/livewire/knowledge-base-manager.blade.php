<div>
    {{-- Header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-brain me-2"></i>
                        AI Bilgi Bankası
                    </h2>
                    <div class="text-muted mt-1">Yapay zeka asistanınız için soru-cevap ekleyin. Müşterileriniz benzer sorular sorduğunda bu yanıtlar kullanılır.</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button wire:click="openModal" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>
                        Yeni Bilgi Ekle
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="page-body">
        <div class="container-xl">
            {{-- Filters --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Arama</label>
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Soru veya yanıt ara...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori</label>
                            <select wire:model.live="filterCategory" class="form-select">
                                <option value="">Tümü</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button wire:click="$set('search', '')" class="btn btn-outline-secondary me-2">
                                <i class="ti ti-x me-1"></i>
                                Temizle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items List --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bilgi Bankası ({{ $items->count() }} kayıt)</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 40px">#</th>
                                <th>Kategori</th>
                                <th>Soru</th>
                                <th>Yanıt</th>
                                <th style="width: 100px">Durum</th>
                                <th style="width: 180px">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td class="text-muted">{{ $item->sort_order }}</td>
                                    <td>
                                        @if($item->category)
                                            <span class="badge bg-blue-lt">{{ $item->category }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px" title="{{ $item->question }}">
                                            {{ $item->question }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted text-truncate" style="max-width: 400px" title="{{ $item->answer }}">
                                            {{ Str::limit($item->answer, 100) }}
                                        </div>
                                    </td>
                                    <td>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:click="toggleActive({{ $item->id }})"
                                                   {{ $item->is_active ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $item->is_active ? 'Aktif' : 'Pasif' }}</span>
                                        </label>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-primary" title="Düzenle">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $item->id }})"
                                                wire:confirm="Bu kaydı silmek istediğinizden emin misiniz?"
                                                class="btn btn-sm btn-danger" title="Sil">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="ti ti-inbox ti-3x mb-3 d-block"></i>
                                        Henüz bilgi eklenmemiş. Yeni bilgi eklemek için yukarıdaki butonu kullanın.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($isEditing)
                                <i class="ti ti-edit me-2"></i> Bilgi Düzenle
                            @else
                                <i class="ti ti-plus me-2"></i> Yeni Bilgi Ekle
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">Kategori</label>
                                <input type="text" wire:model="category" class="form-control"
                                       placeholder="Örn: Genel, Teknik, Satış" list="categories">
                                <datalist id="categories">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                                <small class="form-hint">Mevcut kategorilerden seçin veya yeni kategori yazın</small>
                                @error('category') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sıralama</label>
                                <input type="number" wire:model="sort_order" class="form-control" min="0">
                                <small class="form-hint">Küçük numara önce görünür</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Soru</label>
                            <input type="text" wire:model="question" class="form-control"
                                   placeholder="Müşterinin sorabileceği soru...">
                            @error('question') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">Yanıt</label>
                            <textarea wire:model="answer" class="form-control" rows="6"
                                      placeholder="AI'nın vereceği yanıt..."></textarea>
                            @error('answer') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <small class="form-hint">AI bu yanıtı kendi kelimeleriyle yeniden ifade edecektir</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" wire:model="is_active" class="form-check-input">
                                <span class="form-check-label">Aktif (AI bu bilgiyi kullanacak)</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" wire:click="closeModal">
                            İptal
                        </button>
                        <button type="button" wire:click="save" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>
                            @if($isEditing) Güncelle @else Kaydet @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    {{-- Loading Indicator --}}
    <div wire:loading class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 9999; background: rgba(0,0,0,0.1);">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Livewire event listener for notifications
    Livewire.on('notify', (event) => {
        const data = event[0] || event;
        const type = data.type || 'info';
        const message = data.message || 'İşlem tamamlandı';

        // Tabler.io notification kullanıyorsan:
        if (typeof Tabler !== 'undefined' && Tabler.Alert) {
            Tabler.Alert.show(message, type);
        } else {
            alert(message);
        }
    });
</script>
@endpush
