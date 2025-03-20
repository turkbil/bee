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
                    wire:target="render, search, sortBy, delete, viewType, toggleActive"
                    class="position-absolute top-50 start-50 translate-middle text-center">
                    <div class="progress" style="height: 2px;">
                        <div class="progress-bar progress-bar-indeterminate"></div>
                    </div>
                </div>
            </div>

            <!-- Sağ Taraf (Görünüm Seçimi) -->
            <div class="col-md-3">
                <div class="d-flex align-items-center justify-content-end gap-3">
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

                    <a href="{{ route('admin.settingmanagement.manage', ['group_id' => $groupId]) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Yeni Ekle
                    </a>
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
                                wire:click="sortBy('label')">Başlık</button>
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
                <tbody id="sortable-list" style="min-height: 100px;">
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
                                class="btn btn-icon btn-sm {{ $setting->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
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
                                                    wire:click="$dispatch('showDeleteModal', {
                                                        module: 'settingmanagement',
                                                        id: {{ $setting->id }},
                                                        title: '{{ $setting->label }}'
                                                    })" class="dropdown-item link-danger">
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
                            <div class="empty" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
                                <p class="empty-title">Kayıt bulunamadı</p>
                                <p class="empty-subtitle text-muted">
                                    Arama kriterlerinize uygun kayıt bulunmamaktadır.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('admin.settingmanagement.manage', ['group_id' => $groupId]) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>
                                        Yeni Ayar Ekle
                                    </a>
                                </div>
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
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title d-flex align-items-center">
                            {{ $setting->label }}
                            <span class="ms-2 badge bg-blue-lt">{{ $setting->type }}</span>
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.settingmanagement.value', $setting->id) }}" class="btn btn-sm">
                                <i class="fas fa-edit me-1"></i> Değer Düzenle
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <small class="text-muted d-block mb-2">
                                <code>{{ $setting->key }}</code>
                            </small>
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
                            <div class="pretty p-default p-curve p-toggle p-smooth">
                                <input type="checkbox" class="form-check-input" @checked($setting->getValue())
                                disabled>
                                <div class="state p-success p-on">
                                    <label>Evet</label>
                                </div>
                                <div class="state p-danger p-off">
                                    <label>Hayır</label>
                                </div>
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
                <div class="empty" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
                    <p class="empty-title">Kayıt bulunamadı</p>
                    <p class="empty-subtitle text-muted">
                        Arama kriterlerinize uygun aktif kayıt bulunmamaktadır.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.settingmanagement.manage', ['group_id' => $groupId]) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Yeni Ayar Ekle
                        </a>
                    </div>
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
    
    .empty {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
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

<livewire:modals.delete-modal />
@endpush
