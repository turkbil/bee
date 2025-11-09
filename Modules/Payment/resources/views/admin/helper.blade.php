{{-- Modules/Payment/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    Ödeme Yönetimi
@endsection

{{-- Başlık --}}
@section('title')
    Ödemeler
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">Menü</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('payment', 'view')
                    <a href="{{ route('admin.payment.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        <i class="fas fa-list me-1"></i> Ödemeler
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('payment', 'view')
                    <a href="{{ route('admin.payment.methods.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        <i class="fas fa-credit-card me-1"></i> Ödeme Yöntemleri
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('payment', 'create')
                    <a href="{{ route('admin.payment.methods.manage') }}" class="dropdown-module-item btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Yeni Yöntem
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
