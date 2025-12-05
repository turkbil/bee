@php
    View::share('pretitle', __('subscription::admin.plans'));
@endphp

<div wire:key="subscription-plan-component">
    @include('subscription::admin.helper')
    @include('admin.partials.error_message')

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
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="filterFeatured">
                        <option value="">{{ __('subscription::admin.all_featured') }}</option>
                        <option value="yes">{{ __('subscription::admin.featured_only') }}</option>
                        <option value="no">{{ __('subscription::admin.not_featured') }}</option>
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

    {{-- Plans List --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th class="w-1">
                            <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                        </th>
                        <th class="w-1"></th>
                        <th>{{ __('subscription::admin.plan_name') }}</th>
                        <th>Fiyat Döngüleri</th>
                        <th>KDV</th>
                        <th>Para Birimi</th>
                        <th>Fiyat Gösterim</th>
                        <th>{{ __('admin.status') }}</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody id="plan-sortable-list">
                    @forelse($plans as $plan)
                    <tr wire:key="plan-{{ $plan->subscription_plan_id }}" data-id="{{ $plan->subscription_plan_id }}">
                        <td>
                            <input type="checkbox" class="form-check-input" value="{{ $plan->subscription_plan_id }}" wire:model.live="selectedItems">
                        </td>
                        <td class="plan-drag-handle" style="cursor: grab;">
                            <i class="fas fa-grip-vertical text-muted"></i>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <div class="font-weight-medium">{{ $plan->title_text }}</div>
                                    <div class="text-muted small">{{ $plan->slug }}</div>
                                </div>
                                @if($plan->is_trial)
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-gift me-1"></i>Trial
                                </span>
                                @endif
                                @if($plan->is_featured)
                                <span class="badge bg-yellow ms-2">{{ __('subscription::admin.featured') }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $cycles = is_array($plan->billing_cycles) ? $plan->billing_cycles : [];
                                $cycleCount = count($cycles);
                            @endphp
                            @if($cycleCount > 0)
                                <span class="badge bg-blue">{{ $cycleCount }} döngü</span>
                                <div class="small text-muted mt-1">
                                    @foreach(array_slice($cycles, 0, 2) as $key => $cycle)
                                        {{ is_array($cycle['label'] ?? null) ? ($cycle['label']['tr'] ?? $key) : $key }}
                                        @if(!$loop->last), @endif
                                    @endforeach
                                    @if($cycleCount > 2)
                                        <span class="text-muted">+{{ $cycleCount - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>%{{ number_format($plan->tax_rate ?? 20, 2) }}</td>
                        <td>
                            @php
                                $currencySymbol = match($plan->currency) {
                                    'USD' => '$',
                                    'EUR' => '€',
                                    default => '₺'
                                };
                            @endphp
                            {{ $currencySymbol }} {{ $plan->currency }}
                        </td>
                        <td>
                            @php
                                $displayModeText = match($plan->price_display_mode ?? 'show') {
                                    'hide' => 'Gizli',
                                    'request' => 'Fiyat Sorunuz',
                                    default => 'Göster'
                                };
                                $displayModeBadge = match($plan->price_display_mode ?? 'show') {
                                    'hide' => 'secondary',
                                    'request' => 'warning',
                                    default => 'success'
                                };
                            @endphp
                            <span class="badge bg-{{ $displayModeBadge }}">{{ $displayModeText }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                {{ $plan->is_active ? __('admin.active') : __('admin.inactive') }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('admin.subscription.plans.manage', $plan->subscription_plan_id) }}"
                                   data-bs-toggle="tooltip" title="{{ __('admin.edit') }}">
                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                </a>
                                <div class="dropdown">
                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <button class="dropdown-item" wire:click="toggleStatus({{ $plan->subscription_plan_id }})">
                                            <i class="fas fa-toggle-on me-2"></i>{{ __('admin.toggle_status') }}
                                        </button>
                                        <button class="dropdown-item" wire:click="toggleFeatured({{ $plan->subscription_plan_id }})">
                                            <i class="fas fa-star me-2"></i>{{ __('subscription::admin.toggle_featured') }}
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <button class="dropdown-item text-danger" wire:click="delete({{ $plan->subscription_plan_id }})" wire:confirm="{{ __('admin.confirm_delete') }}">
                                            <i class="fas fa-trash me-2"></i>{{ __('admin.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-img">
                                    <i class="fas fa-list-alt fa-4x text-muted"></i>
                                </div>
                                <p class="empty-title mt-2">{{ __('subscription::admin.no_plans') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
document.addEventListener('livewire:navigated', initPlanSortable);
document.addEventListener('livewire:init', initPlanSortable);

function initPlanSortable() {
    const tbody = document.getElementById('plan-sortable-list');
    if (tbody && !tbody.sortableInstance) {
        tbody.sortableInstance = new Sortable(tbody, {
            handle: '.plan-drag-handle',
            animation: 150,
            onEnd: function(evt) {
                const items = Array.from(tbody.querySelectorAll('tr[data-id]')).map((el, index) => ({
                    id: el.getAttribute('data-id'),
                    order: index
                }));
                @this.call('updateOrder', items);
            }
        });
    }
}
</script>
@endpush
