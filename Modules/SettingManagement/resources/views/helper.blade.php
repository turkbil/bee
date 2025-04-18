{{-- Modules/SettingManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Ayarlar
@endpush

{{-- Başlık --}}
@push('title')
Ayar Yönetimi
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
                    Ayar İşlemleri
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('settingmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.index') }}">
                        Grup Listesi
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('settingmanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.group.manage') }}">
                        Yeni Grup Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasModulePermission('settingmanagement', 'view') || 
                        auth()->user()->hasModulePermission('settingmanagement', 'update'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Ayar İşlemleri</span>
                    </h6>
                    @endif
                    
                    @hasmoduleaccess('settingmanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.manage') }}">
                        Yeni Ayar Ekle
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasModulePermission('settingmanagement', 'view') || 
                        auth()->user()->hasModulePermission('settingmanagement', 'update'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Tenant İşlemleri</span>
                    </h6>
                    @endif
                    
                    @hasmoduleaccess('settingmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.tenant.settings') }}">
                        Tenant Ayarları
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('settingmanagement', 'update')
            <a href="{{ route('admin.settingmanagement.manage') }}" class="btn btn-primary">
                Yeni Ayar Ekle
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush