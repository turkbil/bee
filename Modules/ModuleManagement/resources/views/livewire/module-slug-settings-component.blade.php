<div wire:key="module-slug-manage-component" wire:id="module-slug-manage-component">
    {{-- Helper dosyası --}}
    @include('modulemanagement::helper')
    @include('admin.partials.error_message')

    <form method="post" wire:submit.prevent="save">
        <div class="card">
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="module_slug_active_tab">

                <x-manage.language.switcher :current-language="$currentLanguage" />

            </x-tab-system>
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <!-- URL Ayarları Tab -->
                    <div class="tab-pane fade show active" id="0" role="tabpanel">

                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangSlugs[$lang] ?? [];
                                $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)->get();
                                $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                @if(empty($defaultSlugs))
                                <div class="alert alert-warning">
                                    <div class="d-flex">
                                        <div>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">{{ __('modulemanagement::admin.configuration_not_found') }}</h4>
                                            {{ __('modulemanagement::admin.no_slug_configuration') }}
                                        </div>
                                    </div>
                                </div>
                                @else

                                <!-- Modül Başlık ve Açıklama -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <h3 class="card-title mb-2">
                                            <i class="fas fa-link me-2"></i>
                                            {{ $moduleDisplayName }} - {{ strtoupper($lang) }} {{ __('modulemanagement::admin.module_url_settings') }}
                                        </h3>
                                        <div class="text-muted">
                                            {{ __('modulemanagement::admin.customize_website_structure') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div wire:loading
                                            wire:target="updateSlug, resetSlug, resetAllSlugs, switchLanguage"
                                            class="text-center">
                                            <div class="small text-muted mb-2">{{ __('modulemanagement::admin.updating_status') }}</div>
                                            <div class="progress mb-1" style="width: 200px;">
                                                <div class="progress-bar progress-bar-indeterminate"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button wire:click="resetAllSlugs('{{ $lang }}')"
                                                class="btn btn-outline-danger"
                                                title="{{ strtoupper($lang) }} dilindeki tüm URL'leri sıfırla">
                                            <i class="fas fa-undo me-1"></i>
                                            {{ __('modulemanagement::admin.reset_all_button') }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Modül Adı Bölümü -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card border-primary">
                                            <div class="card-body p-3">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="mb-2">
                                                            <label class="form-label mb-1">
                                                                <strong><i class="fas fa-signature me-1"></i>{{ __('modulemanagement::admin.module_name') }}</strong>
                                                                <span class="text-muted ms-1">({{ strtoupper($lang) }})</span>
                                                            </label>
                                                            <div class="text-muted small">
                                                                {{ __('modulemanagement::admin.module_name_info') }}
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-tag text-primary"></i>
                                                            </span>
                                                            <input 
                                                                type="text" 
                                                                class="form-control"
                                                                value="{{ $multiLangNames[$lang] ?? '' }}"
                                                                wire:blur="updateModuleName($event.target.value, '{{ $lang }}')"
                                                                wire:keydown.enter="updateModuleName($event.target.value, '{{ $lang }}')"
                                                                placeholder="{{ \App\Services\ModuleSlugService::getDefaultModuleName($moduleName, $lang) }}"
                                                            >
                                                        </div>
                                                        
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                {{ __('modulemanagement::admin.module_name_used_in') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                
                                <h4 class="mb-3">
                                    <i class="fas fa-link me-2"></i>
                                    {{ __('modulemanagement::admin.url_settings') }}
                                </h4>

                                <div class="row g-3">
                                    @foreach($defaultSlugs as $key => $defaultValue)
                                    <div class="col-12">
                                        <div class="card border">
                                            <div class="card-body p-3">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <div class="mb-2">
                                                            <label class="form-label mb-1">
                                                                <strong>{{ ucfirst($key) }} {{ __('modulemanagement::admin.page_url') }}</strong>
                                                                <span class="text-muted ms-1">({{ strtoupper($lang) }})</span>
                                                            </label>
                                                            <div class="text-muted small">
                                                                @switch($key)
                                                                    @case('index')
                                                                        {{ __('modulemanagement::admin.list_page_url_info') }}
                                                                        @break
                                                                    @case('show')
                                                                        {{ __('modulemanagement::admin.detail_page_url_info') }}
                                                                        @break
                                                                    @case('category')
                                                                        {{ __('modulemanagement::admin.category_page_url_info') }}
                                                                        @break
                                                                    @default
                                                                        {{ ucfirst($key) }} {{ __('modulemanagement::admin.default_page_url_info') }}
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-globe text-muted"></i>
                                                            </span>
                                                            <span class="input-group-text text-muted">/</span>
                                                            <input 
                                                                type="text" 
                                                                class="form-control @error('multiLangSlugs.' . $lang . '.' . $key) is-invalid @enderror"
                                                                value="{{ $langData[$key] ?? $defaultValue }}"
                                                                wire:blur="updateSlug('{{ $key }}', $event.target.value, '{{ $lang }}')"
                                                                wire:keydown.enter="updateSlug('{{ $key }}', $event.target.value, '{{ $lang }}')"
                                                                placeholder="{{ $defaultValue }}"
                                                            >
                                                            @if($key === 'show' || $key === 'category')
                                                            <span class="input-group-text text-muted">/{slug}</span>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="mt-2">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-eye me-1"></i>
                                                                    {{ __('modulemanagement::admin.preview') }}: 
                                                                    <code class="text-primary">
                                                                        /{{ $langData[$key] ?? $defaultValue }}{{ in_array($key, ['show', 'category']) ? '/' . __('modulemanagement::admin.example_page') : '' }}
                                                                    </code>
                                                                </small>
                                                                
                                                                @if(($langData[$key] ?? $defaultValue) !== $defaultValue)
                                                                <button 
                                                                    wire:click="resetSlug('{{ $key }}', '{{ $lang }}')"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    title="{{ strtoupper($lang) }}: {{ __('modulemanagement::admin.reset_to_default') }}: {{ $defaultValue }}"
                                                                >
                                                                    <i class="fas fa-undo fa-xs"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                            </div>
                        @endforeach

                        @if(!empty($defaultSlugs))
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-info-circle me-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">{{ __('modulemanagement::admin.url_customization_info') }}</h4>
                                        <ul class="mb-0">
                                            <li>{{ __('modulemanagement::admin.url_changes_saved_instantly') }}</li>
                                            <li>{{ __('modulemanagement::admin.user_friendly_urls') }}</li>
                                            <li>{{ __('modulemanagement::admin.invalid_chars_cleaned') }}</li>
                                            <li>{{ __('modulemanagement::admin.empty_fields_use_default') }}</li>
                                            <li>{{ __('modulemanagement::admin.seo_friendly_urls') }}</li>
                                            <li><strong>{{ __('modulemanagement::admin.multilanguage_support') }}</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="alert alert-light">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-code me-2"></i>
                                    </div>
                                    <div>
                                        <h4 class="alert-title">{{ __('modulemanagement::admin.for_developers') }}</h4>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('modulemanagement::admin.template_usage_example') }}:</label>
                                            <div class="bg-dark text-light p-3 rounded">
                                                <code class="text-success">
                                                    &lt;!-- {{ __('modulemanagement::admin.list_page_link') }} --&gt;<br>
                                                    &lt;a href="&#123;&#123; href('{{ strtolower($moduleName) }}', 'index') &#125;&#125;"&gt;{{ ucfirst($moduleName) }} {{ __('modulemanagement::admin.list') }}&lt;/a&gt;<br><br>
                                                    &lt;!-- {{ __('modulemanagement::admin.detail_page_link') }} --&gt;<br>
                                                    &lt;a href="&#123;&#123; href('{{ strtolower($moduleName) }}', 'show', $item-&gt;slug) &#125;&#125;"&gt;{{ __('modulemanagement::admin.view_detail') }}&lt;/a&gt;
                                                </code>
                                            </div>
                                        </div>
                                        
                                        <div class="small text-muted">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            {{ __('modulemanagement::admin.href_function_info') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

            <x-form-footer route="admin.modulemanagement" :model-id="null" />

        </div>
    </form>
</div>

@push('scripts')
    {{-- Module Slug JavaScript Variables --}}
    <script>
        window.currentPageId = null;
        window.currentModuleName = '{{ $moduleName }}';
        window.currentLanguage = '{{ $currentLanguage }}';
        window.allLanguagesSeoData = {};
        
        document.addEventListener('DOMContentLoaded', function() {
            // Dinamik sayfa tespiti - Module slug manage için
            const currentPath = window.location.pathname;
            let pageName = 'Module Slug Manage';
            
            if (currentPath.includes('/modulemanagement/slug-manage')) {
                pageName = 'Module Slug Manage';
            }
            
            console.log(`🚀 ${pageName} sayfası başlatılıyor...`);
            
            // Initialize core systems - manage.js'ten alınan sistem
            setupLanguageSwitching();
            initializeTabSystem();
            setupSlugNormalization();
            
            console.log(`✅ ${pageName} sayfası hazır!`);
        });
        
        // ===== LANGUAGE SWITCHING SYSTEM =====
        function setupLanguageSwitching() {
            $(document).on('click', '.language-switch-btn', function() {
                const language = $(this).data('language');
                const nativeName = $(this).data('native-name');
                
                console.log('🌍 Dil değiştirildi:', language);
                
                // Update button states
                $('.language-switch-btn').removeClass('text-primary').addClass('text-muted')
                    .css('border-bottom', '2px solid transparent')
                    .prop('disabled', false);
                
                $(this).removeClass('text-muted').addClass('text-primary')
                    .css('border-bottom', '2px solid var(--primary-color)')
                    .prop('disabled', true);
                
                // Update language badge
                const languageBadge = document.getElementById('languageBadge');
                if (languageBadge && nativeName) {
                    const badgeContent = languageBadge.querySelector('.nav-link');
                    if (badgeContent) {
                        badgeContent.innerHTML = `<i class="fas fa-language me-2"></i>${nativeName}<i class="fas fa-chevron-down ms-2"></i>`;
                    }
                }
                
                // Switch language content
                $('.language-content').hide();
                $(`.language-content[data-language="${language}"]`).show();
                
                // Update global language variable
                window.currentLanguage = language;
                
                // Trigger Livewire language switch
                if (typeof Livewire !== 'undefined') {
                    Livewire.dispatch('switchLanguage', { language: language });
                }
            });
        }
        
        // ===== TAB SYSTEM =====
        function initializeTabSystem() {
            const storageKey = 'module_slug_active_tab';
            
            // Restore active tab
            const savedTab = localStorage.getItem(storageKey);
            if (savedTab) {
                const tabElement = document.querySelector(`[href="${savedTab}"]`);
                if (tabElement && typeof bootstrap !== 'undefined') {
                    const tab = new bootstrap.Tab(tabElement);
                    tab.show();
                }
            }
            
            // Bind tab events
            const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabLinks.forEach(link => {
                link.addEventListener('shown.bs.tab', (e) => {
                    localStorage.setItem(storageKey, e.target.getAttribute('href'));
                });
            });
        }
        
        // ===== SLUG NORMALIZATION =====
        function setupSlugNormalization() {
            // Input alanlarında sadece URL uyumlu karakterlere izin ver
            document.addEventListener('input', function(e) {
                if (e.target.type === 'text' && e.target.closest('.input-group')) {
                    let value = e.target.value;
                    // Türkçe karakterleri dönüştür ve sadece URL uyumlu karakterlere izin ver
                    value = value
                        .toLowerCase()
                        .replace(/[çÇ]/g, 'c')
                        .replace(/[ğĞ]/g, 'g')
                        .replace(/[ıİ]/g, 'i')
                        .replace(/[öÖ]/g, 'o')
                        .replace(/[şŞ]/g, 's')
                        .replace(/[üÜ]/g, 'u')
                        .replace(/[^a-z0-9\-_]/g, '');
                    
                    if (value !== e.target.value) {
                        e.target.value = value;
                    }
                }
            });
        }
        
        // Global functions - manage.js pattern
        window.setupLanguageSwitching = setupLanguageSwitching;
        window.initializeTabSystem = initializeTabSystem;
        window.setupSlugNormalization = setupSlugNormalization;
    </script>
@endpush