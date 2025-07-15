{{-- Modules/Announcement/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('announcement::admin.announcements') }}
@endsection

{{-- Başlık --}}
@section('title')
{{ __('announcement::admin.announcement_management') }}
@endsection

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
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('announcement::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('announcement', 'view')
            <a href="{{ route('admin.announcement.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
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