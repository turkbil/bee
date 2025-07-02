@include('ai::admin.helper')
<div class="card">
    <div class="card-body">
        <!-- Stats Row -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.total_purchases') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($totalStats['total_purchases']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.total_revenue') }}</div>
                        </div>
                        <div class="h1 mb-0 text-green">{{ number_format($totalStats['total_revenue'], 2) }} TRY</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.total_tokens') }}</div>
                        </div>
                        <div class="h1 mb-0 text-blue">{{ number_format($totalStats['total_tokens']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.today_revenue') }}</div>
                        </div>
                        <div class="h1 mb-0 text-success">{{ number_format($totalStats['today_revenue'], 2) }} TRY</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Bölümü -->
        <div class="row mb-3">
            <!-- Arama Kutusu -->
            <div class="col">
                <div class="input-icon">
                    <span class="input-icon-addon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" 
                           placeholder="{{ __('ai::admin.search_purchases_placeholder') }}" style="border-radius: 0.25rem !important;">
                </div>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading 
                     wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, status, dateFrom, dateTo, clearFilters" 
                     class="position-absolute top-50 start-50 translate-middle text-center" 
                     style="width: 100%; max-width: 250px;">
                    <div class="small text-muted mb-2">{{ __('ai::admin.loading') }}</div>
                    <div class="progress mb-1" style="border-radius: 0.25rem !important;">
                        <div class="progress-bar progress-bar-indeterminate" style="border-radius: 0.25rem !important;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Sağ Taraf (Filtre ve Sayfa Seçimi) -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <!-- Filtre Butonu -->
                    <button type="button" class="btn btn-sm btn-outline-primary {{ ($status || $dateFrom || $dateTo) ? '' : 'collapsed' }}" 
                            data-bs-toggle="collapse" data-bs-target="#filterCollapse" 
                            aria-expanded="{{ ($status || $dateFrom || $dateTo) ? 'true' : 'false' }}" 
                            aria-controls="filterCollapse" 
                            style="border-radius: 0.25rem !important; transition: border-radius 0.15s;">
                        <i class="fas fa-filter me-1"></i>
                        {{ __('ai::admin.filters') }}
                        @if($status || $dateFrom || $dateTo)
                        <span class="badge bg-primary ms-1">{{ collect([$status ? 1 : 0, $dateFrom ? 1 : 0, $dateTo ? 1 : 0])->sum() }}</span>
                        @endif
                    </button>
                    
                    <!-- Sayfa Adeti Seçimi -->
                    <div style="min-width: 70px">
                        <select wire:model.live="perPage" class="form-select" style="border-radius: 0.25rem !important;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collapse Filtre Alanı -->
        <div class="collapse {{ ($status || $dateFrom || $dateTo) ? 'show' : '' }}" id="filterCollapse">
            <div class="card card-body mb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('ai::admin.status') }}</label>
                        <select wire:model.live="status" class="form-select" style="border-radius: 0.25rem !important;">
                            <option value="">{{ __('ai::admin.all_statuses') }}</option>
                            <option value="pending">{{ __('ai::admin.pending') }}</option>
                            <option value="completed">{{ __('ai::admin.completed') }}</option>
                            <option value="failed">{{ __('ai::admin.failed') }}</option>
                            <option value="refunded">{{ __('ai::admin.refunded') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('ai::admin.date_from') }}</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control" style="border-radius: 0.25rem !important;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ __('ai::admin.date_to') }}</label>
                        <input type="date" wire:model.live="dateTo" class="form-control" style="border-radius: 0.25rem !important;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-outline-secondary w-100" wire:click="clearFilters" style="border-radius: 0.25rem !important;">
                            <i class="fas fa-times me-1"></i> {{ __('ai::admin.clear_filters') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tablo Bölümü -->
        <div id="table-default" class="table-responsive">
            <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                <thead>
                    <tr>
                        <th style="width: 50px">
                            <button
                                class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('id')">
                                ID
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'tenant_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('tenant_id')">
                                {{ __('ai::admin.tenant') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'user_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('user_id')">
                                {{ __('ai::admin.user') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'package_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('package_id')">
                                {{ __('ai::admin.package') }}
                            </button>
                        </th>
                        <th style="width: 120px">
                            <button
                                class="table-sort {{ $sortField === 'token_amount' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('token_amount')">
                                {{ __('ai::admin.tokens') }}
                            </button>
                        </th>
                        <th style="width: 120px">
                            <button
                                class="table-sort {{ $sortField === 'amount' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('amount')">
                                {{ __('ai::admin.amount') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 120px">
                            <button
                                class="table-sort {{ $sortField === 'status' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('status')">
                                {{ __('ai::admin.status') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 140px">
                            <button
                                class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('created_at')">
                                {{ __('ai::admin.date') }}
                            </button>
                        </th>
                        <th class="text-center" style="width: 80px">{{ __('ai::admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-tbody">
                    @forelse($purchases as $purchase)
                    <tr class="hover-trigger" wire:key="row-{{ $purchase->id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">#{{ $purchase->id }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex py-1 align-items-center">
                                <div class="flex-fill">
                                    <div class="font-weight-medium">{{ $purchase->tenant->id ?? 'Central' }}</div>
                                    @if($purchase->tenant)
                                    <div class="text-muted small">{{ $purchase->tenant->tenancy_db_name ?? '' }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($purchase->user)
                            <div>{{ $purchase->user->name }}</div>
                            <div class="text-muted small">{{ $purchase->user->email }}</div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($purchase->package)
                            <div>{{ $purchase->package->name }}</div>
                            @else
                            <span class="text-muted">{{ __('ai::admin.manual') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-blue">{{ number_format($purchase->token_amount) }}</span>
                        </td>
                        <td>
                            <div class="text-success font-weight-medium">
                                {{ number_format($purchase->amount, 2) }} {{ $purchase->currency }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($purchase->status == 'completed')
                                <span class="badge bg-success">{{ __('ai::admin.completed') }}</span>
                            @elseif($purchase->status == 'pending')
                                <span class="badge bg-warning">{{ __('ai::admin.pending') }}</span>
                            @elseif($purchase->status == 'failed')
                                <span class="badge bg-danger">{{ __('ai::admin.failed') }}</span>
                            @elseif($purchase->status == 'refunded')
                                <span class="badge bg-secondary">{{ __('ai::admin.refunded') }}</span>
                            @else
                                <span class="badge">{{ $purchase->status }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $purchase->created_at->format('d.m.Y H:i') }}
                        </td>
                        <td class="text-center align-middle">
                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <a href="javascript:void(0);" 
                                           data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.view_details') }}">
                                            <i class="fa-solid fa-eye link-secondary fa-lg"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('ai::admin.no_purchases_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('ai::admin.no_purchases_found_subtitle') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $purchases->links() }}
</div>