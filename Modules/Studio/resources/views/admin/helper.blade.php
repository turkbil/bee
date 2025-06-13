{{-- Modules/Studio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
Studio
@endpush

{{-- Başlık --}}
@push('title')
Görsel Editör
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
                    Studio İşlemleri
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.studio.index') }}">
                        Studio Ana Sayfa
                    </a>
                    
                    @if(Route::has('admin.page.index'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Sayfa İşlemleri</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.index') }}">
                        Tüm Sayfalar
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.manage') }}">
                        Yeni Sayfa Ekle
                    </a>
                    @endif
                </div>
            </div>
            @if(Route::has('admin.page.manage'))
            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
                Yeni Sayfa
            </a>
            @endif
        </div>
    </div>
</div>
@endpush