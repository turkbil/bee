{{-- Modules/Page/resources/views/admin/helper.blade.php --}}
{{-- PreTitle --}}
@section('pretitle')
{{ __('page::admin.pages') }}
@endsection

@push('pretitle')
{{ __('page::admin.pages') }}
@endpush

{{-- Başlık --}}
@push('title')
{{ __('page::admin.page_management') }}
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
    
    // Çok Dilli Form Switcher
    const MultiLangFormSwitcher = {
        init: function() {
            this.bindLanguageToggle();
        },
        
        bindLanguageToggle: function() {
            $('.language-toggle').on('click', function(e) {
                e.preventDefault();
                const targetLang = $(this).data('language');
                const currentLang = $('.language-toggle.active').data('language');
                
                // Form değerlerini kaydet
                MultiLangFormSwitcher.saveFormData(currentLang);
                
                // Yeni dil verilerini yükle
                MultiLangFormSwitcher.loadFormData(targetLang);
                
                // Aktif dil toggle'ını değiştir
                $('.language-toggle').removeClass('active');
                $(this).addClass('active');
            });
        },
        
        saveFormData: function(lang) {
            const formData = {};
            $('[data-multilang]').each(function() {
                const fieldName = $(this).data('multilang');
                formData[fieldName] = $(this).val();
            });
            
            // Livewire ile veri gönder
            if (window.Livewire) {
                @this.updateLanguageData(lang, formData);
            }
        },
        
        loadFormData: function(lang) {
            // Livewire'dan veri al ve form alanlarını doldur
            if (window.Livewire) {
                @this.loadLanguageData(lang).then(data => {
                    $.each(data, function(fieldName, value) {
                        $(`[data-multilang="${fieldName}"]`).val(value);
                    });
                });
            }
        }
    };
    
    // Initialize
    TabManager.init(tabKey);
    MultiLangFormSwitcher.init();
});
</script>
@endpush

{{-- Modül Menüsü --}}
@push('module-menu')

<div class="dropdown d-grid d-md-flex module-menu">
    <a href="#" class="btn dropdown-toggle d-inline-block d-lg-none" data-bs-toggle="dropdown">{{ __('page::admin.menu') }}</a>
    <div class="dropdown-menu dropdown-module-menu">
        <div class="module-menu-revert">
            @hasmoduleaccess('page', 'view')
            <a href="{{ route('admin.page.index') }}" class="dropdown-module-item btn btn-ghost-secondary">
                <i class="icon-menu fas fa-file-alt"></i>{{ __('page::admin.pages') }}
            </a>
            @endhasmoduleaccess

            @hasmoduleaccess('page', 'create')
            <a href="{{ route('admin.page.manage') }}" class="dropdown-module-item btn btn-primary">
                <i class="icon-menu fas fa-plus"></i>{{ __('page::admin.new_page') }}
            </a>
            @endhasmoduleaccess
        </div>
    </div>
</div>

@endpush