@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="ai-cover-component-wrapper">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-images"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->stats['total'] }}</div>
                            <div class="text-muted">Toplam {{ ucfirst($contentType) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-green text-white avatar">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->stats['with_cover'] }}</div>
                            <div class="text-muted">Görseli Var</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-orange text-white avatar">
                                <i class="fas fa-exclamation"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->stats['without_cover'] }}</div>
                            <div class="text-muted">Görseli Yok</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">İçerik Tipi</label>
                    <select wire:model.live="contentType" class="form-select">
                        <option value="song">Şarkılar</option>
                        <option value="album">Albümler</option>
                        <option value="playlist">Playlistler</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Filtre</label>
                    <select wire:model.live="filter" class="form-select">
                        <option value="all">Tümü</option>
                        <option value="without_cover">Görseli Olmayanlar</option>
                        <option value="with_cover">Görseli Olanlar</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button wire:click="generateSelected" class="btn btn-primary w-100" {{ empty($selectedItems) || $processing ? 'disabled' : '' }}>
                        <i class="fas fa-magic me-2"></i>
                        Seçilileri Üret ({{ count($selectedItems) }})
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Items List -->
    <div class="card">
        <div class="card-header">
            <div class="form-check">
                <input type="checkbox" wire:model.live="selectAll" class="form-check-input" id="selectAll">
                <label class="form-check-label" for="selectAll">Tümünü Seç</label>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1"></th>
                        <th>Görsel</th>
                        <th>Başlık</th>
                        <th class="text-end">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->items as $item)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model.live="selectedItems" value="{{ $item->id }}" class="form-check-input">
                            </td>
                            <td>
                                @php
                                    $heroImage = $item->getFirstMediaUrl('hero');
                                @endphp
                                @if($heroImage)
                                    <img src="{{ $heroImage }}" alt="" class="avatar avatar-md">
                                @else
                                    <span class="avatar avatar-md bg-secondary-lt">
                                        <i class="fas fa-image"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->title ?? $item->name }}</strong>
                            </td>
                            <td class="text-end">
                                @if(!$heroImage)
                                    <button wire:click="generateSingle({{ $item->id }})" class="btn btn-sm btn-primary">
                                        <i class="fas fa-magic"></i> Üret
                                    </button>
                                @else
                                    <span class="badge bg-green">
                                        <i class="fas fa-check"></i> Var
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Kayıt bulunamadı</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->items->links() }}
        </div>
    </div>
</div>
