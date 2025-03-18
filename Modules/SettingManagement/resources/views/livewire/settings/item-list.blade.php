@include('settingmanagement::helper')

<div class="card">
    <div class="card-body">
        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Sol Taraf (Arama) -->
            <div class="col-md-8">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Aramak için yazmaya başlayın...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ortadaki Loading -->
            <div class="col-md-1 position-relative">
                <div wire:loading
                    wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, delete, viewType, toggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="progress" style="height: 2px;">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>

            <!-- Sağ Taraf (Görünüm Seçimi ve Sayfalama) -->
            <div class="col-md-3">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Table Mode Switch (Sadece Tablo Görünümünde) -->
                    @if($viewType == 'table')
                    <div class="table-mode">
                        <input type="checkbox" id="table-switch" class="table-switch" <?php echo
                            (!isset($_COOKIE['tableCompact']) || $_COOKIE['tableCompact']=='1' ) ? 'checked' : '' ; ?>
                        onchange="toggleTableMode(this.checked)">
                        <div class="app">
                            <div class="switch-content">
                                <div class="switch-label"></div>
                                <label for="table-switch">
                                    <div class="toggle"></div>
                                    <div class="names">
                                        <p class="large" data-bs-toggle="tooltip" data-bs-placement="left"
                                            title="Satırları daralt">
                                            <i class="fa-thin fa-table-cells fa-lg fa-fade"
                                                style="--fa-animation-duration: 2s;"></i>
                                        </p>
                                        <p class="small" data-bs-toggle="tooltip" data-bs-placement="left"
                                            title="Satırları genişlet">
                                            <i class="fa-thin fa-table-cells-large fa-lg fa-fade"
                                                style="--fa-animation-duration: 2s;"></i>
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Görünüm Değiştirme -->
                    <div class="btn-group">
                        <button type="button"
                            class="btn {{ $viewType == 'preview' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'preview')" title="Önizleme Görünümü">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button"
                            class="btn {{ $viewType == 'table' ? 'btn-secondary' : 'btn-outline-secondary' }}"
                            wire:click="$set('viewType', 'table')" title="Tablo Görünümü">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>

                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perPage" class="form-select">
                            <option value="10">10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if($viewType == 'table')
        <!-- Tablo Görünümü -->
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-arrows-sort text-muted me-2"></i>
                                <button
                                    class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('id')">ID</button>
                            </div>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'label' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('label')">Etiket</button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'key' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('key')">Anahtar</button>
                        </th>
                        <th style="width: 100px">Tip</th>
                        <th class="text-center" style="width: 80px">Durum</th>
                        <th class="text-center" style="width: 120px">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="sortable-list">
                    @forelse($settings as $setting)
                    <tr wire:key="setting-{{ $setting->id }}" id="item-{{ $setting->id }}" data-id="{{ $setting->id }}"
                        class="hover-trigger">
                        <td class="sort-handle cursor-move">
                            <div class="d-flex align-items-center">
                                <span class="me-2">
                                    <i class="ti ti-grip-vertical text-muted"></i>
                                </span>
                                {{ $setting->id }}
                            </div>
                        </td>
                        <td class="sort-handle cursor-move">{{ $setting->label }}</td>
                        <td><code>{{ $setting->key }}</code></td>
                        <td><span class="badge bg-blue-lt">{{ $setting->type }}</span></td>
                        <td class="text-center">
                            <button wire:click="toggleActive({{ $setting->id }})"
                                class="btn btn-icon btn-sm {{ $setting->is_active ? 'text-success' : 'text-danger' }}">
                                <div wire:loading wire:target="toggleActive({{ $setting->id }})"
                                    class="spinner-border spinner-border-sm"></div>
                                <div wire:loading.remove wire:target="toggleActive({{ $setting->id }})">
                                    @if($setting->is_active)
                                    <i class="fas fa-check"></i>
                                    @else
                                    <i class="fas fa-times"></i>
                                    @endif
                                </div>
                            </button>
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="{{ route('admin.settingmanagement.manage.edit', $setting->id) }}"
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
                                                <a href="{{ route('admin.settingmanagement.value', $setting->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Değer Düzenle
                                                </a>
                                                <a href="javascript:void(0);"
                                                    wire:confirm="Silmek istediğinize emin misiniz?"
                                                    wire:click="delete({{ $setting->id }})"
                                                    class="dropdown-item link-danger">
                                                    <i class="fas fa-trash me-2"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Arama kriterlerinize uygun kayıt bulunmamaktadır.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
        <!-- Önizleme Görünümü -->
        <div class="row row-cards">
            @php $activeSettings = $settings->where('is_active', true); @endphp
            @forelse($activeSettings as $setting)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">{{ $setting->label }}</label>
                            @switch($setting->type)
                            @case('text')
                            <input type="text" class="form-control" value="{{ $setting->getValue() }}" readonly>
                            @break
                            @case('textarea')
                            <textarea class="form-control" rows="3" readonly>{{ $setting->getValue() }}</textarea>
                            @break
                            @case('select')
                            <select class="form-select" disabled>
                                @foreach($setting->options ?? [] as $key => $label)
                                <option @selected($key === $setting->getValue())>{{ $label }}</option>
                                @endforeach
                            </select>
                            @break
                            @case('checkbox')
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" @checked($setting->getValue())
                                disabled>
                                <label class="form-check-label">{{ $setting->label }}</label>
                            </div>
                            @break
                            @default
                            <input type="text" class="form-control" value="{{ $setting->getValue() }}" readonly>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">Kayıt bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun aktif kayıt bulunmamaktadır.
                    </p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Önizleme notu -->
        <div class="mt-4">
            <div class="alert alert-info">
                <i class="ti ti-info-circle me-2"></i>
                Not: Önizleme görünümünde yalnızca aktif ayarlar gösterilmektedir.
            </div>
        </div>
        @endif

        <!-- Pagination -->
        <div class="mt-3">
            {{ $settings->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
    .sort-handle {
        cursor: move;
    }

    .table-sort {
        background: none;
        border: none;
        padding: 0;
        margin: 0;
        font-weight: bold;
    }

    .table-sort.asc:after {
        content: " ↑";
    }

    .table-sort.desc:after {
        content: " ↓";
    }
</style>
@endpush

@push('scripts')
<script
    src="{{ asset('admin/libs/sortable/sortable.min.js') }}?v={{ filemtime(public_path('admin/libs/sortable/sortable.min.js')) }}">
</script>
<script>
    document.addEventListener('livewire:init', function () {
    var sortable = new Sortable(document.getElementById('sortable-list'), {
        handle: '.sort-handle', // Sadece sort-handle class'ına sahip elementler sürüklenebilir
        animation: 150,
        onEnd: function (evt) {
            var items = document.querySelectorAll('#sortable-list tr');
            var orderData = [];
            items.forEach(function (item, index) {
                orderData.push({
                    order: index + 1,
                    value: item.getAttribute('data-id')
                });
            });
            Livewire.dispatch('updateOrder', { list: orderData });
        }
    });
});
</script>
@endpush