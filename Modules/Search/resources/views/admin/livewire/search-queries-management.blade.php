@php
    View::share('pretitle', 'Arama Sistemi');
@endphp

<div class="search-queries-management-wrapper">
    {{-- Stats Cards --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Sorgu</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($totalQueries) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">⭐ Popüler</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($totalPopular) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">👁️ Gizli</div>
                    </div>
                    <div class="h1 mb-0">{{ number_format($totalHidden) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Sayfa</div>
                    </div>
                    <div class="h1 mb-0">{{ $queries->lastPage() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                @include('search::admin.helper')

                <div class="card-header">
                    <h3 class="card-title">Arama Sorguları Yönetimi</h3>
                    <div class="ms-auto">
                        <a href="{{ route('admin.search') }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-chart-bar me-1"></i>
                            Analytics
                        </a>
                    </div>
                </div>

                <div class="card-body border-bottom">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   class="form-control"
                                   placeholder="Arama sorgusunda ara...">
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="filter" class="form-select">
                                <option value="all">Tümü</option>
                                <option value="popular">⭐ Sadece Popüler</option>
                                <option value="hidden">👁️ Sadece Gizli</option>
                                <option value="visible">✅ Sadece Görünür</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select wire:model.live="perPage" class="form-select">
                                <option value="25">25 / sayfa</option>
                                <option value="50">50 / sayfa</option>
                                <option value="100">100 / sayfa</option>
                                <option value="200">200 / sayfa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 40%">Arama Sorgusu</th>
                                <th class="text-center" style="width: 10%">Arama Sayısı</th>
                                <th class="text-center" style="width: 10%">Toplam Sonuç</th>
                                <th class="text-center" style="width: 15%">Son Arama</th>
                                <th class="text-center" style="width: 25%">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($queries as $query)
                                <tr>
                                    <td>
                                        @if($editingQueryId === $query->query)
                                            <div class="input-group input-group-sm">
                                                <input type="text"
                                                       wire:model="editingQueryText"
                                                       class="form-control"
                                                       wire:keydown.enter="saveEdit">
                                                <button wire:click="saveEdit" class="btn btn-success">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                                <button wire:click="cancelEdit" class="btn btn-secondary">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center gap-2">
                                                @if($query->is_popular)
                                                    <span class="badge bg-yellow-lt">⭐</span>
                                                @endif
                                                @if($query->is_hidden)
                                                    <span class="badge bg-secondary-lt">👁️</span>
                                                @endif
                                                <strong>{{ $query->query }}</strong>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-lt">{{ number_format($query->search_count) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info-lt">{{ number_format($query->total_results) }}</span>
                                    </td>
                                    <td class="text-center text-muted">
                                        <small>{{ \Carbon\Carbon::parse($query->last_searched)->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button
                                                wire:click="togglePopular('{{ $query->query }}')"
                                                class="btn {{ $query->is_popular ? 'btn-warning' : 'btn-outline-warning' }}"
                                                title="{{ $query->is_popular ? 'Popülerlikten çıkar' : 'Popüler yap' }}">
                                                <i class="ti ti-star"></i>
                                            </button>

                                            <button
                                                wire:click="toggleHidden('{{ $query->query }}')"
                                                class="btn {{ $query->is_hidden ? 'btn-secondary' : 'btn-outline-secondary' }}"
                                                title="{{ $query->is_hidden ? 'Göster' : 'Gizle' }}">
                                                <i class="ti ti-eye-{{ $query->is_hidden ? 'off' : 'check' }}"></i>
                                            </button>

                                            <button
                                                wire:click="startEdit('{{ $query->query }}')"
                                                class="btn btn-outline-primary"
                                                title="Düzenle">
                                                <i class="ti ti-edit"></i>
                                            </button>

                                            <button
                                                wire:click="deleteQuery('{{ $query->query }}')"
                                                wire:confirm="Bu arama sorgusunu silmek istediğinize emin misiniz?"
                                                class="btn btn-outline-danger"
                                                title="Sil">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <div class="empty">
                                            <p class="empty-title">Arama sorgusu bulunamadı</p>
                                            <p class="empty-subtitle text-muted">Filtreleri değiştirmeyi deneyin</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    {{ $queries->links() }}
                </div>
            </div>
        </div>

        {{-- Most Clicked Queries Sidebar --}}
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🔥 En Çok Tıklanan</h3>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($mostClickedQueries as $index => $clicked)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="badge bg-{{ $index < 3 ? 'red' : 'gray' }}-lt">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="col text-truncate">
                                    <strong>{{ $clicked->query }}</strong>
                                    <div class="text-muted">
                                        <small>{{ number_format($clicked->click_count) }} tıklama</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">💡 Bilgi</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <strong>⭐ Popüler:</strong> Footer'da gösterilir (max 10)
                        </li>
                        <li class="mb-2">
                            <strong>👁️ Gizle:</strong> Sitede hiç görünmez
                        </li>
                        <li class="mb-2">
                            <strong>✏️ Düzenle:</strong> Arama metnini düzenle
                        </li>
                        <li>
                            <strong>🗑️ Sil:</strong> Tüm kayıtları sil
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('success', (event) => {
            showToast(event.message, 'success');
        });

        Livewire.on('error', (event) => {
            showToast(event.message, 'danger');
        });
    });

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>
@endpush
