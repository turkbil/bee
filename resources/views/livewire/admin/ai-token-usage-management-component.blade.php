@include('ai::admin.helper')
<div class="card">
    <div class="card-body">
        <!-- Stats Row -->
        <div class="row row-deck row-cards mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.total_usage') }}</div>
                        </div>
                        <div class="h1 mb-0 text-blue">{{ number_format($stats['total_usage']) }}</div>
                        <div class="text-muted">{{ __('ai::admin.tokens') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.today_usage') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['today_usage']) }}</div>
                        <div class="text-muted">{{ __('ai::admin.tokens') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.monthly_usage') }}</div>
                        </div>
                        <div class="h1 mb-0 text-green">{{ number_format($stats['monthly_usage']) }}</div>
                        <div class="text-muted">{{ __('ai::admin.tokens') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">{{ __('ai::admin.active_users') }}</div>
                        </div>
                        <div class="h1 mb-0">{{ number_format($stats['unique_users']) }}</div>
                        <div class="text-muted">{{ $stats['unique_tenants'] }} {{ __('ai::admin.tenants') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Model Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">{{ __('ai::admin.model_usage_stats') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($modelStats as $modelStat)
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <div class="font-weight-medium">{{ $modelStat->model }}</div>
                            <div class="h3 mb-0 mt-2">{{ number_format($modelStat->total) }}</div>
                            <div class="text-muted small">{{ __('ai::admin.tokens') }} ({{ $modelStat->count }} {{ __('ai::admin.usages') }})</div>
                        </div>
                    </div>
                    @endforeach
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
                           placeholder="{{ __('ai::admin.search_usage_placeholder') }}" style="border-radius: 0.25rem !important;">
                </div>
            </div>
            
            <!-- Ortadaki Loading -->
            <div class="col position-relative">
                <div wire:loading 
                     wire:target="render, search, perPage, sortBy, gotoPage, previousPage, nextPage, model, dateFrom, dateTo, clearFilters" 
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
                    <button type="button" class="btn btn-sm btn-outline-primary {{ ($model || $dateFrom || $dateTo) ? '' : 'collapsed' }}" 
                            data-bs-toggle="collapse" data-bs-target="#filterCollapse" 
                            aria-expanded="{{ ($model || $dateFrom || $dateTo) ? 'true' : 'false' }}" 
                            aria-controls="filterCollapse" 
                            style="border-radius: 0.25rem !important; transition: border-radius 0.15s;">
                        <i class="fas fa-filter me-1"></i>
                        {{ __('ai::admin.filters') }}
                        @if($model || $dateFrom || $dateTo)
                        <span class="badge bg-primary ms-1">{{ collect([$model ? 1 : 0, $dateFrom ? 1 : 0, $dateTo ? 1 : 0])->sum() }}</span>
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
        <div class="collapse {{ ($model || $dateFrom || $dateTo) ? 'show' : '' }}" id="filterCollapse">
            <div class="card card-body mb-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('ai::admin.model') }}</label>
                        <select wire:model.live="model" class="form-select" style="border-radius: 0.25rem !important;">
                            <option value="">{{ __('ai::admin.all_models') }}</option>
                            @foreach($availableModels as $availableModel)
                            <option value="{{ $availableModel }}">{{ $availableModel }}</option>
                            @endforeach
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
                        <th style="width: 120px">
                            <button
                                class="table-sort {{ $sortField === 'model' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('model')">
                                {{ __('ai::admin.model') }}
                            </button>
                        </th>
                        <th style="width: 150px">
                            <button
                                class="table-sort {{ $sortField === 'tokens_used' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('tokens_used')">
                                {{ __('ai::admin.token_usage') }}
                            </button>
                        </th>
                        <th>
                            <button
                                class="table-sort {{ $sortField === 'purpose' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                wire:click="sortBy('purpose')">
                                {{ __('ai::admin.purpose') }}
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
                    @forelse($usages as $usage)
                    <tr class="hover-trigger" wire:key="row-{{ $usage->id }}">
                        <td class="sort-id small">
                            <div class="hover-toggle">
                                <span class="hover-hide">#{{ $usage->id }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex py-1 align-items-center">
                                <div class="flex-fill">
                                    <div class="font-weight-medium">{{ $usage->tenant->id ?? 'Central' }}</div>
                                    @if($usage->tenant)
                                    <div class="text-muted small">{{ $usage->tenant->tenancy_db_name ?? '' }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($usage->user)
                            <div>{{ $usage->user->name }}</div>
                            <div class="text-muted small">{{ $usage->user->email }}</div>
                            @else
                            <span class="text-muted">{{ __('ai::admin.system') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-azure">{{ $usage->model }}</span>
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ number_format($usage->tokens_used) }}</div>
                            <div class="text-muted small">
                                {{ __('ai::admin.prompt') }}: {{ number_format($usage->prompt_tokens) }} / {{ __('ai::admin.completion') }}: {{ number_format($usage->completion_tokens) }}
                            </div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $usage->purpose }}">
                                {{ $usage->purpose }}
                            </div>
                        </td>
                        <td class="text-center">
                            {{ $usage->created_at->format('d.m.Y H:i') }}
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
                        <td colspan="8" class="text-center py-4">
                            <div class="empty">
                                <p class="empty-title">{{ __('ai::admin.no_usage_found') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('ai::admin.no_usage_found_subtitle') }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $usages->links() }}
</div>