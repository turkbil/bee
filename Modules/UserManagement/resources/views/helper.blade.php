{{-- PreTitle --}}
@push('pretitle')
Kullanıcılar
@endpush

{{-- Başlık --}}
@push('title')
Kullanıcı Listesi
@endpush

{{-- Modül Menüsü --}}
{{-- Prensip olarak buradaki menüye yeni seçenekler ekleyeceğiz --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">Menü</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    Kullanıcı Menüsü
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.index') }}">
                        Kullanıcılar
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.manage') }}">
                        Kullanıcı Ekle
                    </a>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Rol Listesi</span>
                    </h6>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.index') }}">
                        Roller
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.manage') }}">
                        Rol Ekle
                    </a>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Yetki Listesi</span>
                    </h6>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.index') }}">
                        Yetkiler
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.manage') }}">
                        Yetki Ekle
                    </a>
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Modül Yetkileri</span>
                    </h6>
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.module.permissions') }}">
                        Modül İzinleri
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.usermanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Kullanıcı
            </a>
        </div>
    </div>
</div>
@endpush