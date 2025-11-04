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
                <div class="dropdown-menu" style="max-width: 600px; width: 600px; right: 0; left: auto;">
                    <div class="row g-0">
                        {{-- Sol Kolon --}}
                        <div class="col-6">
                            {{-- Ana AI İşlemleri --}}
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.main_operations') }}</span>
                            </h6>
                            
                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.prowess') }}">
                                {{ __('ai::admin.ai_prowess') }}
                            </a>
                            @endhasmoduleaccess

                            {{-- AI Yönetimi --}}
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.ai_management') }}</span>
                            </h6>
                            
                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}">
                                {{ __('ai::admin.ai_features') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.features.categories') }}">
                                {{ __('ai::admin.ai_feature_categories') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.features.dashboard') }}">
                                {{ __('ai::admin.dashboard') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'create')
                            <a class="dropdown-item" href="{{ route('admin.ai.features.manage') }}">
                                {{ __('ai::admin.new_feature') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.knowledge-base') }}">
                                <i class="ti ti-brain me-1"></i>
                                Bilgi Bankası
                            </a>
                            @endhasmoduleaccess

                            {{-- Profil ve Ayarlar --}}
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.profile_settings') }}</span>
                            </h6>
                            
                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.profile.show') }}">
                                {{ __('ai::admin.ai_profile') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.conversations.index') }}">
                                {{ __('ai::admin.conversations') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.conversations.archived') }}">
                                {{ __('ai::admin.archived_conversations') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'update')
                            <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                                {{ __('ai::admin.settings') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.providers') }}">
                                AI Providers
                            </a>
                            @endhasmoduleaccess

                            {{-- AI Workflow Engine --}}
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.workflow.workflow_engine') }}</span>
                            </h6>

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.workflow.flows.index') }}">
                                <i class="fa fa-code-branch me-1"></i>
                                {{ __('ai::admin.workflow.flows_title') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'create')
                            <a class="dropdown-item" href="{{ route('admin.ai.workflow.flows.manage') }}">
                                <i class="fa fa-plus me-1"></i>
                                {{ __('ai::admin.workflow.create_flow') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.workflow.directives.index') }}">
                                <i class="fa fa-cogs me-1"></i>
                                {{ __('ai::admin.workflow.directives_title') }}
                            </a>
                            @endhasmoduleaccess

                            {{-- Universal Input System V3 - Phase 3'te eklenecek
                            <h6 class=\"dropdown-menu-header card-header-light\">
                                <span class=\"dropdown-header\">Universal Input System</span>
                            </h6>
                            
                            @hasmoduleaccess('ai', 'view')
                            <a class=\"dropdown-item\" href=\"{{ route('admin.ai.universal.index') }}\">
                                {{ __('ai::admin.universal_input_system') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class=\"dropdown-item\" href=\"{{ route('admin.ai.integration.modules') }}\">
                                {{ __('ai::admin.module_integrations') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class=\"dropdown-item\" href=\"{{ route('admin.ai.templates.index') }}\">
                                {{ __('ai::admin.prompt_templates') }}
                            </a>
                            @endhasmoduleaccess
                            --}}
                        </div>

                        {{-- Sağ Kolon --}}
                        <div class="col-6">
                            {{-- Enterprise Credit Management --}}
                            @role('root')
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">Enterprise Credit System</span>
                            </h6>
                            
                            <a class="dropdown-item" href="{{ route('admin.ai.credit-rates.index') }}">
                                Model Credit Rates
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.credit-rates.calculator') }}">
                                Credit Calculator
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.credit-warnings.index') }}">
                                Credit Warnings
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.silent-fallback.index') }}">
                                Silent Fallback System
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.central-fallback.index') }}">
                                Central Fallback System
                            </a>

                            {{-- Legacy Credit Management --}}
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.token_management') }}</span>
                            </h6>

                            <a class="dropdown-item" href="{{ route('admin.ai.credits.index') }}">
                                {{ __('ai::admin.token_management') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.credits.packages') }}">
                                {{ __('ai::admin.token_packages') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.credits.purchases') }}">
                                {{ __('ai::admin.token_purchases') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.credits.usage-stats') }}">
                                {{ __('ai::admin.usage_statistics') }}
                            </a>

                            {{-- Bulk Operations - Phase 3'te eklenecek
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">{{ __('ai::admin.bulk_operations') }}</span>
                            </h6>
                            
                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.bulk.operations') }}">
                                {{ __('ai::admin.bulk_operations') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.analytics.usage') }}">
                                {{ __('ai::admin.usage_analytics') }}
                            </a>
                            @endhasmoduleaccess

                            @hasmoduleaccess('ai', 'view')
                            <a class="dropdown-item" href="{{ route('admin.ai.analytics.performance') }}">
                                {{ __('ai::admin.performance_analytics') }}
                            </a>
                            @endhasmoduleaccess
                            --}}
                            @endrole

                            {{-- Debug & Analytics --}}
                            @role('root')
                            <h6 class="dropdown-menu-header card-header-light">
                                <span class="dropdown-header">Debug & Analytics</span>
                            </h6>
                            
                            <a class="dropdown-item" href="{{ route('admin.ai.debug.dashboard') }}">
                                Priority Debug Dashboard
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.debug.performance') }}">
                                Performance Analytics
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.debug.heatmap') }}">
                                Prompt Usage Heatmap
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.debug.errors') }}">
                                Error Analysis
                            </a>

                            <a class="dropdown-item" href="{{ route('admin.ai.monitoring.index') }}">
                                AI Monitoring
                            </a>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
            @hasmoduleaccess('ai', 'view')
            <a href="{{ route('admin.ai.index') }}" class="dropdown-module-item btn btn-primary">
                {{ __('ai::admin.ai_assistant') }}
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
        {{ __('ai::admin.ai_assistant') }}
    </a>
    @endhasmoduleaccess
</div>
@endpush