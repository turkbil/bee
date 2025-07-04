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
{{ __('ai::admin.token_packages_marketing') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.packages.admin')
{{ __('ai::admin.token_packages_admin') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.purchases')
{{ __('ai::admin.purchase_history') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.usage-stats')
{{ __('ai::admin.usage_statistics') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.index')
{{ __('ai::admin.token_management') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.statistics.overview')
{{ __('ai::admin.general_statistics') }}
@elseif(request()->route()->getName() === 'admin.ai.tokens.tenant-statistics')
{{ __('ai::admin.tenant_statistics') }}
@elseif(request()->route()->getName() === 'admin.ai.features.index')
{{ __('ai::admin.ai_features') }}
@elseif(request()->route()->getName() === 'admin.ai.features.show')
{{ __('ai::admin.ai_feature_detail') }}
@elseif(request()->route()->getName() === 'admin.ai.features.manage')
{{ isset($featureId) && $featureId ? __('ai::admin.edit_ai_feature') : __('ai::admin.new_ai_feature') }}
@elseif(request()->route()->getName() === 'admin.ai.settings')
{{ __('ai::admin.ai_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.settings.api')
{{ __('ai::admin.api_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.settings.limits')
{{ __('ai::admin.limit_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.settings.prompts')
{{ __('ai::admin.prompt_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.settings.prompts.manage')
{{ __('ai::admin.prompt_management') }}
@elseif(request()->route()->getName() === 'admin.ai.settings.general')
{{ __('ai::admin.general_settings') }}
@elseif(request()->route()->getName() === 'admin.ai.conversations.index')
{{ __('ai::admin.my_conversations') }}
@elseif(request()->route()->getName() === 'admin.ai.conversations.archived')
{{ __('ai::admin.archived_conversations') }}
@elseif(request()->route()->getName() === 'admin.ai.conversations.show')
{{ __('ai::admin.conversation_detail') }}
@elseif(request()->route()->getName() === 'admin.ai.examples')
{{ __('ai::admin.usage_examples') }}
@elseif(request()->route()->getName() === 'admin.ai.prowess')
{{ __('ai::admin.test_panel') }}
@else
{{ __('ai::admin.ai_assistant') }}
@endif
@endpush

{{-- AI Helper CSS --}}
@push('css')
<style>
    .nav-tabs .nav-link {
        border-radius: 0;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--tblr-body-color);
    }

    .nav-tabs .nav-link.active {
        background: none;
        border-bottom-color: var(--tblr-primary);
        color: var(--tblr-primary);
        font-weight: 600;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: var(--tblr-border-color);
    }

    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        color: var(--tblr-primary);
        transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
    }

    .badge {
        font-weight: 500;
    }

    .card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
    }

    .example-card,
    .prompt-card {
        border-left: 4px solid var(--tblr-border-color);
        transition: all 0.2s;
    }

    .example-card:hover,
    .prompt-card:hover {
        border-left-color: var(--tblr-primary);
        transform: translateY(-1px);
    }

    .border-success {
        border-color: var(--tblr-success) !important;
        border-left-color: var(--tblr-success) !important;
    }

    .text-success {
        color: var(--tblr-success) !important;
    }

    .small-stats .card {
        transition: transform 0.2s;
    }

    .small-stats .card:hover {
        transform: translateY(-2px);
    }

    .ai-feature-card {
        border: 1px solid var(--tblr-border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .ai-feature-card:hover {
        border-color: var(--tblr-primary);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .ai-chat-panel {
        height: calc(100vh - 200px);
        min-height: 500px;
    }

    .ai-message {
        margin-bottom: 1rem;
        padding: 0.75rem;
        border-radius: 0.5rem;
    }

    .ai-message.user {
        background-color: var(--tblr-primary);
        color: white;
        margin-left: 20%;
        text-align: right;
    }

    .ai-message.assistant {
        background-color: var(--tblr-light);
        margin-right: 20%;
    }

    .typing-indicator {
        display: flex;
        align-items: center;
        padding: 0.75rem;
    }

    .typing-indicator span {
        height: 8px;
        width: 8px;
        background-color: var(--tblr-primary);
        border-radius: 50%;
        display: inline-block;
        margin: 0 2px;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-indicator span:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0);
        }

        40% {
            transform: scale(1);
        }
    }

    .token-info-widget {
        background: linear-gradient(135deg, var(--tblr-primary) 0%, var(--tblr-blue) 100%);
        color: white;
        border-radius: 0.75rem;
        padding: 1rem;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        stroke: currentColor;
        stroke-linecap: round;
        transition: stroke-dasharray 0.35s;
        fill: transparent;
        stroke-width: 4;
    }
</style>
@endpush

{{-- Çok Dilli Tab Sistemi ve AI Helper JS --}}
@push('js')
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
    
    // Initialize Tab Manager
    TabManager.init(tabKey);
    
    // AI Feature Cards Animation
    $('.ai-feature-card').hover(
        function() { $(this).addClass('shadow-sm'); },
        function() { $(this).removeClass('shadow-sm'); }
    );
    
    // Toast mesajlarını dinle
    if (typeof Livewire !== 'undefined') {
        Livewire.on('toast', function(data) {
            if (data.type === 'success') {
                setTimeout(() => window.location.reload(), 1000);
            }
        });
    }
    
    // Auto-refresh token info every 30 seconds
    setInterval(function() {
        if ($('.token-info-widget').length || $('#header-token-balance').length) {
            // AJAX ile güncel token bilgilerini al
            updateTokenDisplays();
        }
    }, 30000);
});

// Real-time token güncelleme fonksiyonu
function updateTokenDisplays() {
    // AI Widget Helper kullanarak güncel token verilerini al
    $.ajax({
        url: '{{ route('admin.ai.token-stats') }}',
        method: 'GET',
        success: function(data) {
            // Header token balance güncelle
            if ($('#header-token-balance').length) {
                $('#header-token-balance').html(
                    new Intl.NumberFormat().format(data.remaining_tokens) + ' {{ __('ai::admin.tokens') }}'
                );
            }
            
            // Header monthly usage güncelle
            if ($('#header-monthly-usage').length) {
                $('#header-monthly-usage').html(
                    new Intl.NumberFormat().format(data.monthly_usage) + ' / ' + 
                    (data.monthly_limit > 0 ? new Intl.NumberFormat().format(data.monthly_limit) : '{{ __('ai::admin.unlimited') }}')
                );
            }
            
            // Progress bar güncelle
            if ($('#header-usage-progress').length && data.monthly_limit > 0) {
                const percentage = Math.min(100, (data.monthly_usage / data.monthly_limit) * 100);
                $('#header-usage-progress').css('width', percentage + '%');
            }
            
            // Sayfa token displaylerini güncelle
            if ($('#token-display').length) {
                $('#token-display').text(new Intl.NumberFormat().format(data.remaining_tokens));
            }
            if ($('#remaining-token-display').length) {
                $('#remaining-token-display').text(new Intl.NumberFormat().format(data.remaining_tokens));
            }
            if ($('#daily-usage-display').length) {
                $('#daily-usage-display').text(new Intl.NumberFormat().format(data.daily_usage));
            }
            if ($('#monthly-usage-display').length) {
                $('#monthly-usage-display').text(new Intl.NumberFormat().format(data.monthly_usage));
            }
            
            // Features sayfası token displaylerini güncelle
            if ($('#features-remaining-tokens').length) {
                $('#features-remaining-tokens').text(new Intl.NumberFormat().format(data.remaining_tokens));
                // Token renk durumu güncelle
                if (data.remaining_tokens > 0) {
                    $('#features-remaining-tokens').removeClass('text-danger').addClass('text-primary');
                } else {
                    $('#features-remaining-tokens').removeClass('text-primary').addClass('text-danger');
                }
            }
            if ($('#features-token-progress').length && data.total_tokens > 0) {
                const percentage = (data.remaining_tokens / data.total_tokens) * 100;
                $('#features-token-progress').css('width', percentage + '%');
            }
        },
        error: function() {
            console.warn('Token istatistikleri güncellenirken bir hata oluştu.');
        }
    });
});

// Bootstrap Tab Navigation
document.addEventListener('livewire:navigated', function () {
    // Tab geçişlerini yönet
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', function (event) {
            const targetTab = event.target.getAttribute('href');
            localStorage.setItem('ai-feature-active-tab', targetTab);
        });
    });
    
    // Son aktif tab'ı geri yükle
    const lastActiveTab = localStorage.getItem('ai-feature-active-tab');
    if (lastActiveTab) {
        const tabLink = document.querySelector(`[href="${lastActiveTab}"]`);
        if (tabLink) {
            const tab = new bootstrap.Tab(tabLink);
            tab.show();
        }
    }
});

// Progress Ring Animation
function animateProgressRing(element, percentage) {
    const circle = element.querySelector('.progress-ring-circle');
    const radius = circle.r.baseVal.value;
    const circumference = radius * 2 * Math.PI;
    
    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = circumference;
    
    const offset = circumference - percentage / 100 * circumference;
    circle.style.strokeDashoffset = offset;
}

// Initialize progress rings
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.progress-ring').forEach(ring => {
        const percentage = ring.dataset.percentage || 0;
        animateProgressRing(ring, percentage);
    });
});
</script>
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('ai::admin.menu')
        }}</a>
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
                    <a class="dropdown-item" href="{{ route('admin.ai.features.index') }}">
                        {{ __('ai::admin.ai_features') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'update')
                    <a class="dropdown-item" href="{{ route('admin.ai.settings') }}">
                        {{ __('ai::admin.ai_settings') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.examples') }}">
                        {{ __('ai::admin.usage_examples') }}
                    </a>
                    @endhasmoduleaccess

                    @hasmoduleaccess('ai', 'view')
                    <a class="dropdown-item" href="{{ route('admin.ai.prowess') }}">
                        {{ __('ai::admin.test_panel') }}
                    </a>
                    @endhasmoduleaccess

                    @if(auth()->check() && auth()->user()->hasRole('root'))
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">{{ __('ai::admin.token_management') }}</h6>

                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.index') }}">
                        {{ __('ai::admin.token_management') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.packages') }}">
                        {{ __('ai::admin.token_packages') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.purchases') }}">
                        {{ __('ai::admin.purchase_history') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.usage-stats') }}">
                        {{ __('ai::admin.usage_statistics') }}
                    </a>

                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.statistics.overview') }}">
                        {{ __('ai::admin.general_statistics') }}
                    </a>
                    @endif
                </div>
            </div>
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-primary"
                    data-bs-toggle="dropdown">
                    {{ __('ai::admin.token_operations') }}
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
                                <div class="fw-bold">{{ __('ai::admin.token_balance') }}</div>
                                <div class="text-muted small" id="header-token-balance">
                                    @php
                                    $tokenStats = ai_widget_token_data();
                                    @endphp
                                    {{ number_format($tokenStats['remaining'] ?? 0) }} {{ __('ai::admin.tokens') }}
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
                                <div class="fw-bold">{{ __('ai::admin.monthly_usage') }}</div>
                                <div class="text-muted small" id="header-monthly-usage">
                                    {{ number_format($tokenStats['monthly_usage'] ?? 0) }} / {{
                                    ($tokenStats['monthly_limit'] ?? 0) > 0 ?
                                    number_format($tokenStats['monthly_limit']) : __('ai::admin.unlimited') }}
                                </div>
                                @if(($tokenStats['monthly_limit'] ?? 0) > 0)
                                <div class="progress progress-sm mt-1">
                                    <div class="progress-bar" id="header-usage-progress"
                                        style="width: {{ min(100, ($tokenStats['monthly_usage'] ?? 0) / ($tokenStats['monthly_limit'] ?? 1) * 100) }}%">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    @if(Route::has('admin.ai.tokens.packages'))
                    <a class="dropdown-item" href="{{ route('admin.ai.tokens.packages') }}">
                        {{ __('ai::admin.buy_tokens') }}
                    </a>
                    @endif
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

{{-- AI Feature Page Specific Helper --}}
@if(isset($featureId))
@section('title', $featureId ? __('ai::admin.edit_ai_feature') : __('ai::admin.new_ai_feature'))

@push('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.ai.index') }}">{{ __('ai::admin.ai_module') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.ai.features.index') }}">{{ __('ai::admin.ai_features') }}</a>
        </li>
        <li class="breadcrumb-item active">
            {{ $featureId ? __('ai::admin.edit') : __('ai::admin.new_feature') }}
        </li>
    </ol>
</nav>
@endpush

@push('page-header')
<div class="row g-2 align-items-center">
    <div class="col">
        <div class="page-pretitle">{{ __('ai::admin.ai_module') }}</div>
        <h2 class="page-title">
            @if($featureId)
            {{ $inputs['name'] ?? __('ai::admin.ai_feature') }} - {{ __('ai::admin.edit') }}
            @else
            {{ __('ai::admin.create_new_ai_feature') }}
            @endif
        </h2>
        @if($featureId && isset($inputs['status']))
        <div class="page-subtitle text-muted">
            <span class="badge bg-{{ $inputs['badge_color'] ?? 'secondary' }}-lt">
                {{ ucfirst($inputs['status']) }}
            </span>
            @if(isset($feature) && $feature && $feature->is_system)
            <span class="badge bg-info-lt ms-2">{{ __('ai::admin.system_feature') }}</span>
            @endif
        </div>
        @endif
    </div>
    @if($featureId)
    <div class="col-auto">
        <div class="btn-list">
            <a href="{{ route('admin.ai.features.show', $featureId) }}" class="btn btn-outline-primary">
                <i class="fas fa-eye me-2"></i>{{ __('ai::admin.view') }}
            </a>
            <button type="button" class="btn btn-outline-info" wire:click="save(true)">
                <i class="fas fa-copy me-2"></i>{{ __('ai::admin.copy_and_edit') }}
            </button>
        </div>
    </div>
    @endif
</div>
@endpush
@endif