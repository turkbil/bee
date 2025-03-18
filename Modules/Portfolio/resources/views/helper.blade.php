{{-- Modules/Portfolio/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Portfolyolar
@endpush

{{-- Başlık --}}
@push('title')
Portfolyo Yönetimi
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    Portfolyo İşlemleri
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">
                        Portfolyolar
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.portfolio.manage') }}">
                        Yeni Portfolyo Ekle
                    </a>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Kategori İşlemleri</span>
                    </h6>
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.index') }}">
                        Kategoriler
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.manage') }}">
                        Kategori Ekle
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.portfolio.manage') }}" class="btn btn-primary">
                Yeni Portfolyo
            </a>
        </div>
    </div>
</div>
@endpush