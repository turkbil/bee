{{-- Modules/Portfolio/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('portfolio::admin.portfolios') }}
@endsection

@push('pretitle')
{{ __('portfolio::admin.portfolios') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('portfolio::admin.portfolio_management') }}
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
    
    // Çok Dilli Form Sistemi
    const LanguageSwitcher = {
        init: function() {
            this.bindLanguageSwitch();
        },
        
        bindLanguageSwitch: function() {
            $('.language-switch-btn').on('click', function(e) {
                e.preventDefault();
                const targetLanguage = $(this).data('language');
                
                // Dil butonlarını güncelle
                $('.language-switch-btn').removeClass('text-primary').addClass('text-muted');
                $('.language-switch-btn').css('border-bottom', '2px solid transparent');
                
                $(this).removeClass('text-muted').addClass('text-primary');
                $(this).css('border-bottom', '2px solid var(--primary-color)');
                
                // Form içeriklerini değiştir
                $('.language-content').hide();
                $(`.language-content[data-language="${targetLanguage}"]`).show();
                
                // currentLanguage değişkenini güncelle
                window.currentLanguage = targetLanguage;
            });
        }
    };
    
    // Initialize
    TabManager.init(tabKey);
    LanguageSwitcher.init();
});
</script>
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')
<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('portfolio::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            <div class="dropdown">
                <button type="button" class="dropdown-module-item dropdown-toggle btn btn-ghost-secondary"
                    data-bs-toggle="dropdown">
                    {{ __('portfolio::admin.portfolio_operations') }}
                </button>
                <div class="dropdown-menu">
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.index') }}">
                        <i class="icon-menu fas fa-briefcase"></i>{{ __('portfolio::admin.portfolios') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.manage') }}">
                        <i class="icon-menu fas fa-plus"></i>{{ __('portfolio::admin.add_new_portfolio') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @if(auth()->user()->hasModulePermission('portfolio', 'view'))
                    <h6 class="dropdown-menu-header card-header-light">
                        <span class="dropdown-header">{{ __('portfolio::admin.category_operations') }}</span>
                    </h6>
                    
                    @hasmoduleaccess('portfolio', 'view')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.index') }}">
                        <i class="icon-menu fas fa-tags"></i>{{ __('portfolio::admin.categories') }}
                    </a>
                    @endhasmoduleaccess
                    
                    @hasmoduleaccess('portfolio', 'create')
                    <a class="dropdown-item" href="{{ route('admin.portfolio.category.manage') }}">
                        <i class="icon-menu fas fa-tag"></i>{{ __('portfolio::admin.add_category') }}
                    </a>
                    @endhasmoduleaccess
                    @endif
                </div>
            </div>
            @hasmoduleaccess('portfolio', 'create')
            <a href="{{ route('admin.portfolio.manage') }}" class="btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('portfolio::admin.new_portfolio') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>
@endpush