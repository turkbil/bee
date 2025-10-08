{{-- Modules/Blog/resources/views/admin/helper-category.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('blog::admin.category_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('blog::admin.categories') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('blog::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('blog', 'view')
                    <a href="{{ route('admin.blog.category.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('blog::admin.categories') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('blog', 'create')
                    <a href="{{ route('admin.blog.category.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('blog::admin.new_category') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
