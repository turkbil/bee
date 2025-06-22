{{-- Modules/Studio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('studio::general.studio') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('studio::general.visual_editor') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('studio::general.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('studio::general.studio_operations') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.studio.index') }}">
                        {{ t('studio::general.studio_home') }}
                    </a>
                    
                    @if(Route::has('admin.page.index'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ t('studio::general.page_operations') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.index') }}">
                        {{ t('studio::general.all_pages') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.manage') }}">
                        {{ t('studio::general.add_new_page') }}
                    </a>
                    @endif
                </div>
            </div>
            @if(Route::has('admin.page.manage'))
            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
                {{ t('studio::general.new_page') }}
            </a>
            @endif
        </div>
    </div>
</div>
@endpush