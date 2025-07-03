{{-- Modules/AI/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@push('pretitle')
{{ __('ai::admin.artificial_intelligence') }}
@endpush

{{-- Başlık --}}
@push('title')
@if(request()->route()->getName() === 'admin.ai.tokens.packages')
    {{ __('ai::admin.token_packages') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.packages.marketing')
    Token Paketleri - Pazarlama Sayfası
@elseif(request()->route()->getName() === 'admin.ai.tokens.packages.admin')
    Token Paketleri - Admin Yönetimi
@elseif(request()->route()->getName() === 'admin.ai.tokens.purchases')
    {{ __('ai::admin.purchase_history') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.usage-stats')
    {{ __('ai::admin.usage_statistics') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.index')
    {{ __('ai::admin.token_management') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.statistics.overview')
    Genel İstatistikler
@elseif(request()->route()->getName() === 'admin.ai.tokens.tenant-statistics')
    Kiracı İstatistikleri
@elseif(request()->route()->getName() === 'admin.ai.features')
    Gerçek Zamanlı Token Dashboard
@elseif(request()->route()->getName() === 'admin.ai.settings')
    {{ __('ai::admin.ai_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.conversations.index')
    {{ __('ai::admin.my_conversations') }}
@elseif(request()->route()->getName() === 'admin.ai.conversations.show')
    {{ __('ai::admin.conversation_detail') }}
@else
    {{ __('ai::admin.ai_assistant') }}
@endif
@endpush

{{-- Çok Dilli Tab Sistemi --}}
@push('admin-js')
<script>
$(document).ready(function() {
    // Aktif tab'ı kaydet
    const routeName = '{{ request()->route()->getName() }}';
    const tabKey = routeName.replace('.', '') + 'ActiveTab';
    
    // Tab Manager
    const TabManager = {
        init: function(storageKey) {
            this.storageKey = storageKey;
            this.restoreActiveTab();
            this.bindTabEvents();
        },
        
        restoreActiveTab: function() {
            const activeTab = localStorage.getItem(this.storageKey);
            if (activeTab) {
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-content .tab-pane').removeClass('active show');
                
                const targetTab = $(`[href="${activeTab}"]`);
                if (targetTab.length) {
                    targetTab.addClass('active');
                    $(activeTab).addClass('active show');
                }
            }
        },
        
        bindTabEvents: function() {
            const self = this;
            $('.nav-tabs .nav-link').on('click', function() {
                const href = $(this).attr('href');
                localStorage.setItem(self.storageKey, href);
            });
        }
    };
    
    // Initialize
    TabManager.init(tabKey);
});
</script>
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('ai::admin.menu') }}</a>
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
                    
                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.features') }}">
                        AI Özellikleri ve Test Sayfası
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        {{ __('ai::admin.ai_settings') }}
                    </a>
                    @endhasmoduleaccess
                    
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">Token Yönetimi</span>
                    </h6>
                    
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.packages') }}">
                        Token Paketleri
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.purchases') }}">
                        Satın Alma Geçmişim
                    </a>
                    
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.usage-stats') }}">
                        Kullanım İstatistiklerim
                    </a>
                    
                    @if(auth()->check() && auth()->user()->hasRole('root'))
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Root Yönetimi</h6>
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.index') }}">
                        Tüm Kiracı Yönetimi
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.statistics.overview') }}">
                        Genel İstatistikler
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.purchases') }}">
                        Tüm Satın Alımlar
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.usage-stats') }}">
                        Tüm Kullanım İstatistikleri
                    </a>
                    @endif
                </div>
            </div>
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-primary"
                    data-bs-toggle="dropdown">
                    Token İşlemleri
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm bg-primary text-white">
                                    ₺
                                </span>
                            </div>
                            <div>
                                <div class="fw-bold">Token Bakiyesi</div>
                                <div class="text-muted small">
                                    {{ \App\Helpers\TokenHelper::remainingFormatted() }} token
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm bg-info text-white">
                                    %
                                </span>
                            </div>
                            <div>
                                <div class="fw-bold">Bu Ay Kullanım</div>
                                <div class="text-muted small">
                                    {{ \App\Helpers\TokenHelper::monthlyUsageFormatted() }} / {{ \App\Helpers\TokenHelper::monthlyLimitFormatted() }}
                                </div>
                                @if(\App\Helpers\TokenHelper::monthlyLimit() > 0)
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar" style="width: {{ \App\Helpers\TokenHelper::usagePercentage() }}%"></div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.packages') }}">
                        Token Satın Al
                    </a>
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