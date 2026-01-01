@php
    View::share('pretitle', __('muzibu::admin.music_platform'));
@endphp

<div class="corporate-usage-component-wrapper">
    <!-- Period Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Dönem</label>
                    <select wire:model.live="period" class="form-select">
                        <option value="7">Son 7 Gün</option>
                        <option value="30">Son 30 Gün</option>
                        <option value="90">Son 90 Gün</option>
                        <option value="365">Son 1 Yıl</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-play"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->overallStats['total_plays']) }}</div>
                            <div class="text-muted">Toplam Dinleme</div>
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
                            <span class="bg-green text-white avatar">
                                <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->overallStats['unique_listeners']) }}</div>
                            <div class="text-muted">Benzersiz Dinleyici</div>
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
                            <span class="bg-yellow text-white avatar">
                                <i class="fas fa-building"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ number_format($this->overallStats['active_corporates']) }}</div>
                            <div class="text-muted">Aktif Kurumsal</div>
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
                            <span class="bg-purple text-white avatar">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium h3 mb-0">{{ $this->overallStats['total_hours'] }}</div>
                            <div class="text-muted">Toplam Saat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Corporate Accounts -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kurumsal Hesaplar</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead>
                            <tr>
                                <th>Firma</th>
                                <th class="text-center">Durum</th>
                                <th class="text-end">Oluşturulma</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->corporateAccounts as $account)
                                <tr>
                                    <td>
                                        <strong>{{ $account->company_name }}</strong>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <p class="mb-0">Kurumsal hesap bulunamadı</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $this->corporateAccounts->links() }}
                </div>
            </div>
        </div>

        <!-- Daily Usage -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Günlük Kullanım
                    </h3>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th class="text-end">Dinleme</th>
                                <th class="text-end">Dinleyici</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->dailyUsage as $stat)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($stat->date)->format('d.m') }}</td>
                                    <td class="text-end">{{ number_format($stat->plays) }}</td>
                                    <td class="text-end">{{ number_format($stat->listeners) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Veri yok</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
