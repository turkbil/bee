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
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.index') }}">
                        Kullanıcılar
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.manage') }}">
                        Kullanıcı Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Aktivite Kayıtları</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.activity.logs') }}">
                        İşlem Kayıtları
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root') || auth()->user()->hasRole('admin'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Rol Listesi</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.index') }}">
                        Roller
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.role.manage') }}">
                        Rol Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Yetki Listesi</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.index') }}">
                        Yetkiler
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('usermanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.permission.manage') }}">
                        Yetki Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Modül Yetkileri</span>
                    </h6>
                    
                    @hasmoduleaccess('usermanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.usermanagement.module.permissions') }}">
                        Modül İzinleri
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('usermanagement', 'create')
            <a href="{{ route('admin.usermanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Kullanıcı
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush