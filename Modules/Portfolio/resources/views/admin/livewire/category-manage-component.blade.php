<div>
    @php
        View::share(
            'pretitle',
            $categoryId ? __('portfolio::admin.edit_category') : __('portfolio::admin.new_category'),
        );
    @endphp

    @include('portfolio::admin.helper-category')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="category_active_tab">
                <x-manage.language.switcher :current-language="$currentLanguage" />
            </x-tab-system>

            <div class="card-body">
                <div class="tab-content" id="contentTabContent">

                    <!-- TEMEL BİLGİLER TAB -->
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
                                                placeholder="{{ __('portfolio::admin.category_title') }}">
                                            <label>
                                                {{ __('portfolio::admin.category_title') }}
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
                                                wire:model="multiLangInputs.{{ $lang }}.slug" maxlength="255"
                                                placeholder="kategori-url-slug">
                                            <label>
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
                            </div>
                        @endforeach

                        {{-- ÜST KATEGORİ VE FOTOĞRAF --}}
                        <div class="row mb-4">
                            <!-- Üst Kategori -->
                            <div class="col-12 col-md-6 mb-3 mb-md-0">
                                <div class="form-floating">
                                    <select wire:model="inputs.parent_id"
                                        class="form-control @error('inputs.parent_id') is-invalid @enderror"
                                        id="parent_category_select">
                                        <option value="">{{ __('portfolio::admin.main_category') }}</option>
                                        @foreach($this->hierarchicalCategories as $cat)
                                            @if($categoryId != $cat['id'])
                                                <option value="{{ $cat['id'] }}">
                                                    {{ $cat['title'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="parent_category_select">
                                        {{ __('portfolio::admin.parent_category') }}
                                    </label>
                                    @error('inputs.parent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fotoğraf -->
                            <div class="col-12 col-md-6 category-media-wrapper">
                                <livewire:mediamanagement::universal-media
                                    wire:id="category-media-component"
                                    :model-id="$categoryId"
                                    model-type="portfolio_category"
                                    model-class="Modules\Portfolio\App\Models\PortfolioCategory"
                                    :collections="['hero']"
                                    :sortable="false"
                                    :key="'universal-media-' . ($categoryId ?? 'new')"
                                />
                            </div>
                        </div>

                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                $langName = $languageNames[$lang] ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="{{ $currentLanguage === $lang ? '' : 'display: none;' }}">

                                {{-- Açıklama editörü --}}
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'description',
                                    'label' => __('portfolio::admin.category_description'),
                                    'placeholder' => __('portfolio::admin.category_description_placeholder'),
                                ])
                            </div>
                        @endforeach

                        <!-- Aktif/Pasif -->
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

                    <!-- SEO TAB - UNIVERSAL COMPONENT -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$categoryId" model-type="portfolio_category"
                            model-class="Modules\Portfolio\App\Models\PortfolioCategory" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.portfolio.category.index" :model-id="$categoryId" />

        </div>
    </form>


    @push('scripts')
        {{-- MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $categoryId ?? 'null' }};
            window.currentModuleName = 'portfolio_category';
            window.currentLanguage = '{{ $jsVariables['currentLanguage'] ?? 'tr' }}';

            // TAB RESTORE - Validation hatası sonrası tab görünür kalsın
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('restore-active-tab', () => {
                    console.log('🔄 Tab restore tetiklendi (validation error)');

                    if (typeof window.forceTabRestore === 'function') {
                        setTimeout(() => {
                            window.forceTabRestore();
                        }, 100);
                    } else {
                        console.warn('⚠️ forceTabRestore fonksiyonu bulunamadı');
                    }
                });

                // BROWSER REDIRECT
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

        {{-- UNIVERSAL SYSTEMS --}}
        @include('languagemanagement::admin.components.universal-language-scripts', [
            'currentLanguage' => $currentLanguage,
            'availableLanguages' => $availableLanguages,
        ])

        @include('seomanagement::admin.components.universal-seo-scripts', [
            'availableLanguages' => $availableLanguages,
        ])
    @endpush
</div>
