@php
    View::share('pretitle', __('coupon::admin.coupons'));
@endphp

<div wire:key="coupon-component">
    @include('coupon::admin.helper')
    @include('admin.partials.error_message')

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-ticket-alt"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['total'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('coupon::admin.total_coupons') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['active'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('coupon::admin.active_coupons') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-chart-bar"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['total_usage'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('coupon::admin.total_usage') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="{{ __('admin.search') }}..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">{{ __('admin.all_statuses') }}</option>
                        <option value="active">{{ __('admin.active') }}</option>
                        <option value="inactive">{{ __('admin.inactive') }}</option>
                        <option value="expired">{{ __('coupon::admin.expired') }}</option>
                        <option value="limit_reached">{{ __('coupon::admin.limit_reached') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterType">
                        <option value="">{{ __('coupon::admin.all_types') }}</option>
                        <option value="percentage">{{ __('coupon::admin.percentage') }}</option>
                        <option value="fixed_amount">{{ __('coupon::admin.fixed_amount') }}</option>
                        <option value="free_shipping">{{ __('coupon::admin.free_shipping') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    @if(count($selectedItems) > 0)
    <div class="card mb-3 bg-primary-lt">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <span class="me-3">{{ count($selectedItems) }} {{ __('admin.items_selected') }}</span>
                <button class="btn btn-sm btn-success me-2" wire:click="bulkActivate">
                    <i class="fas fa-check me-1"></i>{{ __('admin.activate') }}
                </button>
                <button class="btn btn-sm btn-warning me-2" wire:click="bulkDeactivate">
                    <i class="fas fa-times me-1"></i>{{ __('admin.deactivate') }}
                </button>
                <button class="btn btn-sm btn-danger" wire:click="bulkDelete" wire:confirm="{{ __('admin.confirm_bulk_delete') }}">
                    <i class="fas fa-trash me-1"></i>{{ __('admin.delete') }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Coupons List --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th class="w-1">
                            <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                        </th>
                        <th>{{ __('coupon::admin.code') }}</th>
                        <th>{{ __('coupon::admin.discount') }}</th>
                        <th>{{ __('coupon::admin.usage') }}</th>
                        <th>{{ __('coupon::admin.valid_until') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr wire:key="coupon-{{ $coupon->coupon_id }}">
                        <td>
                            <input type="checkbox" class="form-check-input" value="{{ $coupon->coupon_id }}" wire:model.live="selectedItems">
                        </td>
                        <td>
                            <div class="font-weight-medium">{{ $coupon->code }}</div>
                            @if($coupon->title_text)
                            <div class="text-muted small">{{ $coupon->title_text }}</div>
                            @endif
                        </td>
                        <td>{{ $coupon->discount_display }}</td>
                        <td>{{ $coupon->usage_display }}</td>
                        <td>
                            @if($coupon->valid_until)
                            {{ $coupon->valid_until->format('d.m.Y') }}
                            @else
                            <span class="text-muted">∞</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $coupon->status_badge }}">
                                {{ __('coupon::admin.' . $coupon->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('admin.coupon.manage', $coupon->coupon_id) }}"
                                   data-bs-toggle="tooltip" title="{{ __('admin.edit') }}">
                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                </a>
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button class="dropdown-item" wire:click="toggleStatus({{ $coupon->coupon_id }})">
                                            <i class="fas fa-toggle-on me-2"></i>{{ __('admin.toggle_status') }}
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" wire:click="delete({{ $coupon->coupon_id }})" wire:confirm="{{ __('admin.confirm_delete') }}">
                                            <i class="fas fa-trash me-2"></i>{{ __('admin.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-ticket-alt fa-4x text-muted"></i>
                                </div>
                                <p class="empty-title mt-2">{{ __('coupon::admin.no_coupons') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="card-footer">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>
