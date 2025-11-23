@php
    View::share('pretitle', __('subscription::admin.subscriptions'));
@endphp

<div wire:key="subscription-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

    {{-- Stats --}}
    <div class="row mb-3">
        <div class="col-sm-6 col-lg-3">
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
                            <div class="text-muted">{{ __('subscription::admin.active') }}</div>
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
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['trial'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('subscription::admin.trial') }}</div>
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
                            <span class="bg-danger text-white avatar">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['expired'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('subscription::admin.expired') }}</div>
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
                            <span class="bg-secondary text-white avatar">
                                <i class="fas fa-ban"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">{{ $stats['cancelled'] ?? 0 }}</div>
                            <div class="text-muted">{{ __('subscription::admin.cancelled') }}</div>
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
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="{{ __('admin.search') }}..." wire:model.live.debounce.300ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterStatus">
                        <option value="">{{ __('admin.all_statuses') }}</option>
                        <option value="active">{{ __('admin.active') }}</option>
                        <option value="trial">{{ __('subscription::admin.trial') }}</option>
                        <option value="expired">{{ __('subscription::admin.expired') }}</option>
                        <option value="cancelled">{{ __('subscription::admin.cancelled') }}</option>
                        <option value="paused">{{ __('subscription::admin.paused') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterPlan">
                        <option value="">{{ __('subscription::admin.all_plans') }}</option>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->subscription_plan_id }}">{{ $plan->title_text }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterCycle">
                        <option value="">{{ __('subscription::admin.all_cycles') }}</option>
                        <option value="monthly">{{ __('subscription::admin.monthly') }}</option>
                        <option value="yearly">{{ __('subscription::admin.yearly') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscriptions List --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('subscription::admin.subscription_number') }}</th>
                        <th>{{ __('subscription::admin.customer') }}</th>
                        <th>{{ __('subscription::admin.plan') }}</th>
                        <th>{{ __('subscription::admin.billing_cycle') }}</th>
                        <th>{{ __('subscription::admin.period_end') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                    <tr wire:key="subscription-{{ $subscription->subscription_id }}">
                        <td>
                            <div class="font-weight-medium">{{ $subscription->subscription_number }}</div>
                        </td>
                        <td>
                            @if($subscription->customer)
                            <div>{{ $subscription->customer->name }}</div>
                            <div class="text-muted small">{{ $subscription->customer->email }}</div>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($subscription->plan)
                            {{ $subscription->plan->title_text }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ __('subscription::admin.' . $subscription->billing_cycle) }}</td>
                        <td>
                            @if($subscription->current_period_end)
                            {{ $subscription->current_period_end->format('d.m.Y') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $subscription->status_badge }}">
                                {{ __('subscription::admin.' . $subscription->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if($subscription->status === 'active' || $subscription->status === 'trial')
                                    <button class="dropdown-item text-danger" wire:click="cancel({{ $subscription->subscription_id }})" wire:confirm="{{ __('subscription::admin.confirm_cancel') }}">
                                        <i class="fas fa-ban me-2"></i>{{ __('subscription::admin.cancel') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-users fa-4x text-muted"></i>
                                </div>
                                <p class="empty-title mt-2">{{ __('subscription::admin.no_subscriptions') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subscriptions->hasPages())
        <div class="card-footer">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>
</div>
