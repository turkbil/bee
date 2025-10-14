<div>
    @php
        View::share(
            'pretitle',
            $portfolioId ? __('portfolio::admin.edit_portfolio_pretitle') : __('portfolio::admin.new_portfolio_pretitle'),
        );
    @endphp

    @include('portfolio::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="portfolio_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $portfolioId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'portfolio', 'id' => $portfolioId]) }}"
                            target="_blank" class="btn btn-outline-primary"
                            style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i
                                class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('portfolio::admin.studio.editor') }}
                        </a>
                    </li>
                @endif

                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>

            <div class="card-body">
                <div class="tab-content" id="contentTabContent">

                    <!-- TEMEL BİLGİLER TAB - NO FADE for instant switching -->
                    <div class="tab-pane show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating mb-3 mb-md-0">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('portfolio::admin.title_field') }}">
                                            <label>
                                                {{ __('portfolio::admin.title_field') }}
                                                @if ($lang === get_tenant_default_locale())
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug"
                                                id="slug_{{ $lang }}"
                                                maxlength="255"
                                                placeholder="sayfa-url-slug">
                                            <label for="slug_{{ $lang }}">
                                                {{ __('admin.portfolio_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    {{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kategori Seçimi (Sadece ilk dilde göster) -->
                                @if($lang === get_tenant_default_locale())
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <select wire:model="inputs.portfolio_category_id"
                                                class="form-control @error('inputs.portfolio_category_id') is-invalid @enderror"
                                                id="category_select">
                                                <option value="">{{ __('portfolio::admin.select_category') }}</option>
                                                @foreach($this->activeCategories as $category)
                                                    <option value="{{ $category->category_id }}">
                                                        {{ $category->getTranslated('title', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="category_select">
                                                {{ __('portfolio::admin.category') }}
                                            </label>
                                            @error('inputs.portfolio_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ --}}
                        <div class="mb-4">
                            <livewire:mediamanagement::universal-media
                                wire:id="portfolio-media-component"
                                :model-id="$portfolioId"
                                model-type="portfolio"
                                model-class="Modules\Portfolio\App\Models\Portfolio"
                                :collections="['featured_image', 'gallery']"
                                :sortable="true"
                                :set-featured-from-gallery="true"
                                :key="'universal-media-' . ($portfolioId ?? 'new')"
                            />
                        </div>

                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                {{-- İçerik editörü - AI button artık global component'te --}}
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('portfolio::admin.content'),
                                    'placeholder' => __('portfolio::admin.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3 mt-4">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('portfolio::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('portfolio::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$portfolioId" model-type="portfolio"
                            model-class="Modules\Portfolio\App\Models\Portfolio" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.portfolio" :model-id="$portfolioId" />

        </div>
    </form>

    {{-- AUTO SEO FILL TRIGGER - Premium tenants için otomatik SEO doldurma --}}
    @if($portfolioId && $portfolio)
        <x-auto-seo-trigger
            :model="$portfolio"
            model-type="portfolio"
            :locale="$currentLanguage"
        />
    @endif


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $portfolioId ?? 'null' }};
            window.currentModuleName = 'portfolio';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // 🔥 TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('restore-active-tab', () => {
                    console.log('🔄 Tab restore tetiklendi (validation error)');

                    // forceTabRestore fonksiyonu tab-system.blade.php'de tanımlı
                    if (typeof window.forceTabRestore === 'function') {
                        setTimeout(() => {
                            window.forceTabRestore();
                        }, 100);
                    } else {
                        console.warn('⚠️ forceTabRestore fonksiyonu bulunamadı');
                    }
                });

                // 🔄 BROWSER REDIRECT - Event işlendikten sonra yönlendir
                Livewire.on('browser', (event) => {
                    console.log('🔄 Browser event:', event);

                    if (event.action === 'redirect') {
                        const delay = event.delay || 0;
                        console.log(`🔄 Redirecting to ${event.url} after ${delay}ms`);

                        setTimeout(() => {
                            window.location.href = event.url;
                        }, delay);
                    }
                });
            });
        </script>

        {{-- 🌍 UNIVERSAL SYSTEMS --}}
        @include('languagemanagement::admin.components.universal-language-scripts', [
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => $availableLanguages,
        ])

        @include('seomanagement::admin.components.universal-seo-scripts', [
            'availableLanguages' => $availableLanguages,
        ])

        @include('ai::admin.components.universal-ai-content-scripts')

        {{-- 🎁 AUTO SEO FILL - Premium tenants için otomatik SEO doldurma --}}
        <script src="{{ asset('assets/js/auto-seo-fill.js') }}"></script>
    @endpush
</div>
