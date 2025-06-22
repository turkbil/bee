{{-- Modules/AI/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ t('ai::general.artificial_intelligence') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ t('ai::general.ai_assistant') }}
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ t('common.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ t('ai::general.ai_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.index') }}">
                        {{ t('ai::general.ai_assistant') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">
                        {{ t('ai::general.my_conversations') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        {{ t('ai::general.ai_settings') }}
                    </a>
                    @endhasmoduleaccess
                </div>
            </div>
            @hasmoduleaccess('ai', 'view')
            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary">
                {{ t('ai::general.open_ai_assistant') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush