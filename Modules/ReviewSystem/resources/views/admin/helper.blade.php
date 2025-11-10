{{-- Modules/ReviewSystem/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    Yorum ve Puan Sistemi
@endsection

{{-- Başlık --}}
@section('title')
    Yorumlar
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">Menü</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                <a href="{{ route('admin.reviewsystem.add') }}" class="dropdown-module-item btn btn-success">
                    <i class="fas fa-plus"></i> Manuel Ekle
                </a>
                <a href="{{ route('admin.reviewsystem.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                    Tüm Yorumlar
                </a>
                <a href="{{ route('admin.reviewsystem.pending') }}" class="dropdown-module-item btn btn-warning">
                    Onay Bekleyenler
                </a>
                <a href="{{ route('admin.reviewsystem.statistics') }}" class="dropdown-module-item btn btn-ghost-primary">
                    İstatistikler
                </a>
            </div>
        </div>
    </div>

@endpush
