{{-- Modules/Announcement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
    {{ __('announcement::admin.announcement_management') }}
@endsection

{{-- Başlık --}}
@section('title')
    {{ __('announcement::admin.announcements') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

    <div class="dropdown d-grid d-md-flex module-menu">
        <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none"
            data-bs-toggle="dropdown">{{ __('announcement::admin.menu') }}</a>
        <div class="dropdown-menu dropdown-module-menu">
            <div class="module-menu-revert">
                @hasmoduleaccess('announcement', 'view')
                    <a href="{{ route('admin.announcement.index') }}" class="dropdown-module-item btn btn-ghost-primary">
                        {{ __('announcement::admin.announcements') }}
                    </a>
                @endhasmoduleaccess

                @hasmoduleaccess('announcement', 'create')
                    <a href="{{ route('admin.announcement.manage') }}" class="dropdown-module-item btn btn-primary">
                        {{ __('announcement::admin.new_announcement') }}
                    </a>
                @endhasmoduleaccess
            </div>
        </div>
    </div>

@endpush
