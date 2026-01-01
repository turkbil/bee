<div class="spot-wrapper">
    <div class="card">
        <div class="card-body p-0">
            <!-- Header -->
            <div class="row mx-2 my-3">
                <!-- Arama -->
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Spot ara...">
                    </div>
                </div>

                <!-- Kurumsal Filtre -->
                <div class="col-md-3">
                    <select wire:model.live="corporateFilter" class="form-select">
                        <option value="">Tüm Kurumlar</option>
                        @foreach($corporateAccounts as $corp)
                            <option value="{{ $corp->id }}">{{ $corp->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sayfa Başına -->
                <div class="col-md-2">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Loading -->
                <div class="col-md-2 position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </div>
                </div>

                <!-- Yeni Ekle -->
                <div class="col-md-2 text-end">
                    <a href="{{ route('admin.muzibu.spot.manage') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Yeni Spot
                    </a>
                </div>
            </div>

            <!-- Toplu İşlem -->
            @if(count($selectedIds) > 0)
            <div class="alert alert-warning mx-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span>
                        <strong>{{ count($selectedIds) }}</strong> spot seçildi
                    </span>
                    <button type="button" wire:click="bulkDelete" wire:confirm="Seçili spotları silmek istediğinize emin misiniz?"
                        class="btn btn-danger btn-sm">
                        <i class="fas fa-trash me-1"></i>
                        Seçilenleri Sil
                    </button>
                </div>
            </div>
            @endif

            <!-- Sıralama Bilgisi -->
            @if($corporateFilter)
            <div class="alert alert-info mx-3 py-2">
                <i class="fas fa-info-circle me-1"></i>
                <small>Spotları sıralamak için <i class="fas fa-grip-vertical"></i> simgesini tutup sürükleyin.</small>
            </div>
            @endif

            <!-- Tablo -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 40px">
                                <input type="checkbox" class="form-check-input"
                                    wire:model.live="selectAll">
                            </th>
                            @if($corporateFilter)
                            <th style="width: 40px"></th>
                            @endif
                            <th wire:click="sortBy('position')" style="cursor: pointer; width: 60px">
                                #
                                @if($sortField === 'position')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('title')" style="cursor: pointer">
                                Başlık
                                @if($sortField === 'title')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            @if(!$corporateFilter)
                            <th>Kurum</th>
                            @endif
                            <th style="width: 100px">Süre</th>
                            <th style="width: 120px">Başlangıç</th>
                            <th style="width: 120px">Bitiş</th>
                            <th style="width: 100px">Durum</th>
                            <th class="text-center" style="width: 140px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody id="spot-sortable-list">
                        @forelse($spots as $spot)
                        <tr wire:key="spot-{{ $spot->id }}" class="spot-item" data-id="{{ $spot->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input"
                                    wire:model.live="selectedIds" value="{{ $spot->id }}">
                            </td>
                            @if($corporateFilter)
                            <td>
                                <span class="spot-drag-handle cursor-move text-muted" title="Sürükle">
                                    <i class="fas fa-grip-vertical"></i>
                                </span>
                            </td>
                            @endif
                            <td class="text-muted">{{ $spot->position }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($spot->hasAudio())
                                        <i class="fas fa-volume-up text-success me-2" title="Ses dosyası var"></i>
                                    @else
                                        <i class="fas fa-volume-mute text-danger me-2" title="Ses dosyası yok"></i>
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $spot->title }}</div>
                                        <div class="small text-muted">{{ $spot->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            @if(!$corporateFilter)
                            <td>
                                <span class="badge bg-azure-lt">
                                    {{ $spot->corporateAccount->company_name ?? '-' }}
                                </span>
                            </td>
                            @endif
                            <td>
                                <span class="text-muted">{{ $spot->getFormattedDuration() }}</span>
                            </td>
                            <td>
                                @if($spot->starts_at)
                                    <span class="small">{{ $spot->starts_at->format('d.m.Y H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($spot->ends_at)
                                    <span class="small">{{ $spot->ends_at->format('d.m.Y H:i') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($spot->is_archived)
                                    <span class="badge bg-dark">Arşiv</span>
                                @elseif($spot->is_enabled)
                                    @if($spot->isCurrentlyActive())
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Zamanlanmış</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-3 justify-content-center">
                                    <a href="{{ route('admin.muzibu.spot.manage', $spot->id) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ __('admin.edit') }}"
                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                    </a>
                                    <a href="javascript:void(0);" wire:click="toggleEnabled({{ $spot->id }})"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ $spot->is_enabled ? 'Devre dışı bırak' : 'Etkinleştir' }}"
                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-{{ $spot->is_enabled ? 'toggle-on' : 'toggle-off' }} {{ $spot->is_enabled ? 'link-success' : 'link-secondary' }} fa-lg"></i>
                                    </a>
                                    <a href="javascript:void(0);" wire:click="toggleArchived({{ $spot->id }})"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ $spot->is_archived ? 'Arşivden çıkar' : 'Arşivle' }}"
                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-{{ $spot->is_archived ? 'box-open' : 'archive' }} {{ $spot->is_archived ? 'link-warning' : 'link-secondary' }} fa-lg"></i>
                                    </a>
                                    <a href="javascript:void(0);" wire:click="deleteSpot({{ $spot->id }})"
                                        wire:confirm="Bu spotu silmek istediğinize emin misiniz?"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Sil"
                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                        <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $corporateFilter ? 10 : 10 }}" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-img">
                                        <i class="fas fa-bullhorn fa-3x text-muted"></i>
                                    </div>
                                    <p class="empty-title">Spot bulunamadı</p>
                                    <p class="empty-subtitle text-muted">
                                        Henüz hiç spot eklenmemiş veya arama kriterlerinize uygun spot yok.
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.muzibu.spot.manage') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-1"></i>
                                            Yeni Spot Ekle
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($spots->hasPages())
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-muted">
                    Toplam <strong>{{ $spots->total() }}</strong> spot
                </p>
                <div class="ms-auto">
                    {{ $spots->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
        .spot-drag-handle {
            cursor: move;
            padding: 5px;
        }
        .spot-drag-handle:hover {
            color: var(--tblr-primary) !important;
        }
        .spot-sortable-ghost {
            opacity: 0.4;
            background: #f1f5f9;
        }
        .spot-sortable-drag {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', function() {
            initSpotSortable();

            Livewire.on('refresh-sortable', () => {
                setTimeout(() => initSpotSortable(), 100);
            });
        });

        function initSpotSortable() {
            const container = document.getElementById('spot-sortable-list');
            if (!container) return;

            // Sadece kurumsal filtre aktifse sıralama aktif
            const hasCorporateFilter = '{{ $corporateFilter }}' !== '';
            if (!hasCorporateFilter) return;

            // Mevcut sortable'ı temizle
            if (window.spotSortable) {
                window.spotSortable.destroy();
                window.spotSortable = null;
            }

            window.spotSortable = new Sortable(container, {
                animation: 150,
                ghostClass: 'spot-sortable-ghost',
                dragClass: 'spot-sortable-drag',
                handle: '.spot-drag-handle',

                onEnd: function(evt) {
                    const items = [];
                    const allItems = container.querySelectorAll('.spot-item');

                    allItems.forEach((item, index) => {
                        items.push({
                            id: parseInt(item.getAttribute('data-id')),
                            order: index + 1
                        });
                    });

                    if (items.length > 0) {
                        Livewire.dispatch('updateSpotOrder', { list: items });
                    }
                }
            });
        }
    </script>
    @endpush
</div>
