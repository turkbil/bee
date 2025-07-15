{{-- Modules/Studio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('studio::admin.studio') }}
@endsection

{{-- Başlık --}}
@section('title')
{{ __('studio::admin.visual_editor') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('studio::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('studio::admin.studio_operations') }}
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.studio.index') }}">
                        {{ __('studio::admin.studio_home') }}
                    </a>
                    
                    @if(Route::has('admin.page.index'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('studio::admin.page_operations') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.index') }}">
                        {{ __('studio::admin.all_pages') }}
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.page.manage') }}">
                        {{ __('studio::admin.add_new_page') }}
                    </a>
                    @endif
                </div>
            </div>
            @if(Route::has('admin.page.manage'))
            <a href="{{ route('admin.page.manage') }}" class="btn btn-primary">
                {{ __('studio::admin.new_page') }}
            </a>
            @endif
        </div>
    </div>
</div>
@endpush