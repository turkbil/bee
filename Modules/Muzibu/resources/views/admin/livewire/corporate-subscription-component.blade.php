@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="corporate-subscription-component-wrapper">
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-building"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['total_accounts']) }}</div>
                            <div class="text-muted">Toplam Hesap</div>
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
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['active_accounts']) }}</div>
                            <div class="text-muted">Aktif</div>
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
                            <span class="bg-secondary text-white avatar">
                                <i class="fas fa-pause"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->stats['inactive_accounts']) }}</div>
                            <div class="text-muted">Pasif</div>
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
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Firma veya kullanıcı ara...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">Tüm Durumlar</option>
                        <option value="active">Aktif</option>
                        <option value="cancelled">Pasif</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kurumsal Hesaplar</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th>Firma</th>
                        <th>Kullanıcı</th>
                        <th class="text-center">Durum</th>
                        <th class="text-end">Oluşturulma</th>
                        <th class="text-end">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->subscriptions as $account)
                        <tr>
                            <td>
                                <strong>{{ $account->company_name }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm me-2 bg-secondary-lt">
                                        {{ substr($account->user?->name ?? '?', 0, 1) }}
                                    </span>
                                    {{ $account->user?->name ?? '-' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($account->is_active)
                                    <span class="badge bg-green">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Pasif</span>
                                @endif
                            </td>
                            <td class="text-end text-muted">
                                {{ $account->created_at?->format('d.m.Y') ?? '-' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.muzibu.corporate.manage', $account->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fas fa-building fa-2x mb-2"></i>
                                <p class="mb-0">Kurumsal hesap bulunamadı</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->subscriptions->links() }}
        </div>
    </div>
</div>
