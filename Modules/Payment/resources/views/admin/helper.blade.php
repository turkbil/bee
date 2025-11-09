{{-- Modules/Payment/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('payment::admin.payment_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('payment::admin.payments') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('payment::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('payment', 'view')
                    <a href="{{ route('admin.payment.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('payment::admin.payments') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('payment', 'view')
                    <a href="{{ route('admin.payment.category.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('payment::admin.categories') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('payment', 'create')
                    <a href="{{ route('admin.payment.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('payment::admin.new_payment') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
