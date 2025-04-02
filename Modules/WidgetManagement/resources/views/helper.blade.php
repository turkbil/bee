{{-- Modules/WidgetManagement/resources/views/helper.blade.php --}}

{{-- PreTitle --}}
@push('pretitle')
Widget Yönetimi
@endpush

{{-- Başlık --}}
@push('title')
Widget Yönetimi
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
                    Widget İşlemleri
                </button>
                
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        Widget Listesi
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('widgetmanagement', 'update')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        Yeni Widget Ekle
                    </a>
                    @endhasmoduleaccess

                    @if(auth()->user()->hasModulePermission('widgetmanagement', 'view') ||
                        auth()->user()->hasModulePermission('widgetmanagement', 'update'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Widget Bölümleri</span>
                    </h6>
                    @endif

                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.section') }}">
                        Widget Bölümleri
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>

            @hasmoduleaccess('widgetmanagement', 'update')
            <a href="{{ route('admin.widgetmanagement.manage') }}" class="btn btn-primary">
                Yeni Widget Ekle
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush