{{-- Modules/AI/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('ai::admin.ai_assistant') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('common.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('ai::admin.ai_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.index') }}">
                        {{ __('ai::admin.ai_assistant') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">
                        {{ __('ai::admin.my_conversations') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        {{ __('ai::admin.ai_settings') }}
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('ai', 'view')
            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                {{ __('ai::admin.open_ai_assistant') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush