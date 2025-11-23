{{-- Modules/Subscription/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('subscription::admin.subscription_management') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
           data-bs-toggle="dropdown">{{ __('subscription::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                <div class="dropdown">
                    <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                        data-bs-toggle="dropdown">
                        {{ __('subscription::admin.subscription_menu') }}
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="{{ route('admin.subscription.plans.index') }}">
                            <i class="icon-menu fas fa-list-alt"></i>{{ __('subscription::admin.plans') }}
                        </a>

                        <a class="dropdown-item" href="{{ route('admin.subscription.index') }}">
                            <i class="icon-menu fas fa-users"></i>{{ __('subscription::admin.subscriptions') }}
                        </a>

                        <a class="dropdown-item" href="{{ route('admin.coupon.index') }}">
                            <i class="icon-menu fas fa-ticket-alt"></i>{{ __('coupon::admin.coupons') }}
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.subscription.plans.manage') }}" class="dropdown-module-item btn btn-primary">
                    <i class="icon-menu fas fa-plus-circle"></i>{{ __('subscription::admin.new_plan') }}
                </a>
            </div>
        </div>
    </div>
@endpush
