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
                    
                    @hasmoduleaccess('widgetmanagement', 'create')
                    <a class="dropdown-item" href="{{ route('admin.widgetmanagement.manage') }}">
                        Widget Ekle
                    </a>
                    @endhasmoduleaccess
                    
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
            @hasmoduleaccess('widgetmanagement', 'create')
            <a href="{{ route('admin.widgetmanagement.manage') }}" class="dropdown-module-item btn btn-primary">
                Yeni Widget
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush

@push('styles')
<style>
    .sortable-ghost {
        opacity: 0.5;
        background: #c8e6c9;
    }
    
    .cursor-move {
        cursor: move;
    }
    
    .widget-select-card:hover {
        border-color: #206bc4;
        box-shadow: 0 0 0 3px rgba(32, 107, 196, 0.2);
        cursor: pointer;
    }
    
    .form-label.required:after {
        content: " *";
        color: red;
    }
    
    /* Sürükle-bırak dosya alanı stillemesi */
    .file-drop-area {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border: 2px dashed #ccc;
        border-radius: 6px;
        background-color: #f8f9fa;
        transition: 0.2s;
    }
    
    .file-drop-area:hover,
    .file-drop-area.is-active {
        background-color: #eef2f7;
        border-color: #adb5bd;
    }
</style>
@endpush