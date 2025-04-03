{{-- Modules/WidgetManagement/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Widget Yönetimi
@endpush

{{-- Başlık --}}
@push('title')
Widget Listesi
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
                    Widget Menüsü
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.index') }}">
                        Widget Listesi
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasRole('root'))
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        Widget Ekle
                    </a>
                    @endif
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Bölüm Yönetimi</span>
                    </h6>
                    
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.section') }}">
                        Sayfa Bölümleri
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('widgetmanagement', 'view')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.section', ['module' => 'page']) }}">
                        Modül Bölümleri
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">İçerik Yönetimi</span>
                    </h6>
                    
                    @if(request()->routeIs('admin.widgetmanagement.items*'))
                    @hasmoduleaccess('widgetmanagement', 'update')
                    <a class="dropdown-item active" href="{{ url()->current() }}">
                        İçerik Düzenle
                    </a>
                    @endhasmoduleaccess
                    @endif
                    
                    @if(request()->routeIs('admin.widgetmanagement.settings*'))
                    @hasmoduleaccess('widgetmanagement', 'update')
                    <a class="dropdown-item active" href="{{ url()->current() }}">
                        Özelleştirme Ayarları
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @if(auth()->user()->hasRole('root'))
            <a href="{{ route('admin.widgetmanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Widget
            </a>
            @endif
        </div>
    </div>
</div>
@endpush