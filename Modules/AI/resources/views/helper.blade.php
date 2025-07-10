{{-- Modules/AI/resources/views/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endsection

{{-- Başlık --}}
@section('title')
{{ __('ai::admin.ai_management') }}
@endsection

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('ai::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('ai::admin.ai_management') }}
                </button>
                <div class="dropdown-menu">
                    {{-- Ana AI İşlemleri --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('ai::admin.main_operations') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.prowess') }}">
                        <i class="icon-menu fas fa-star"></i>{{ __('ai::admin.ai_prowess') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- AI Yönetimi --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('ai::admin.ai_management') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}">
                        <i class="icon-menu fas fa-cogs"></i>{{ __('ai::admin.ai_features') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.features.categories') }}">
                        <i class="icon-menu fas fa-folder"></i>{{ __('ai::admin.ai_feature_categories') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.features.dashboard') }}">
                        <i class="icon-menu fas fa-chart-line"></i>{{ __('ai::admin.dashboard') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'create')
                    <a class="dropdown-item" href="{{ route('admin.ai.features.manage') }}">
                        <i class="icon-menu fas fa-plus"></i>{{ __('ai::admin.new_feature') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Profil ve Ayarlar --}}
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('ai::admin.profile_settings') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.profile.show') }}">
                        <i class="icon-menu fas fa-user-cog"></i>{{ __('ai::admin.ai_profile') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">
                        <i class="icon-menu fas fa-comments"></i>{{ __('ai::admin.conversations') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        <i class="icon-menu fas fa-cog"></i>{{ __('ai::admin.settings') }}
                    </a>
                    @endhasmoduleaccess

                    {{-- Token Yönetimi --}}
                    @role('root')
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('ai::admin.token_management') }}</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.index') }}">
                        <i class="icon-menu fas fa-coins"></i>{{ __('ai::admin.token_management') }}
                    </a>
                    @endrole
                </div>
            </div>
            @hasmoduleaccess('ai', 'view')
            <a href="{{ route('admin.ai.index') }}" class="dropdown-module-item btn btn-primary">
                <i class="icon-menu fas fa-robot"></i>{{ __('ai::admin.ai_assistant') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush

{{-- Sağ Taraf AI Linki --}}
@push('page-header-actions')
<div class="btn-list">
    @hasmoduleaccess('ai', 'view')
    <a href="{{ route('admin.ai.index') }}" class="btn btn-outline-primary">
        <i class="icon-menu fas fa-robot"></i>
        {{ __('ai::admin.ai_assistant') }}
    </a>
    @endhasmoduleaccess
</div>
@endpush