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
                        Ayar Listesi
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.group.manage') }}">
                        Yeni Ayar Grubu Ekle
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.settingmanagement.tenant.settings') }}">
                        Tenant Ayarları
                    </a>
                    @endif
                </div>
            </div>
            @hasmoduleaccess('settingmanagement', 'view')
            <a href="{{ route('admin.settingmanagement.index') }}" class="btn btn-primary">
                Ayar Listesi
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush