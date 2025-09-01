@php
    View::share('pretitle', $portfolioId ? 'Portföy Düzenleme' : 'Yeni Portföy Ekleme');
@endphp

<div wire:key="portfolio-manage-component" wire:id="portfolio-manage-component">
    {{-- Helper dosyası --}}
    @include('portfolio::admin.helper')
    @include('admin.partials.error_message')

    <form method="post" wire:submit.prevent="save">
        <div class="card">
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="portfolio_active_tab">

                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $portfolioId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'portfolio', 'id' => $portfolioId]) }}"
                            target="_blank" class="btn btn-outline-primary" style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('admin.studio.editor') }}
                        </a>
                    </li>
                @endif

                <x-manage.language.switcher :current-language="$currentLanguage" />

            </x-tab-system>
            <div class="card-body">
                <div class="tab-content" id="contentTabContent">
                    <!-- Temel Bilgiler Tab -->
                    <div class="tab-pane fade show active" id="0" role="tabpanel">
                        @foreach ($availableLanguages as $lang)
                            @php
                                $langData = $multiLangInputs[$lang] ?? [];
                                // Tenant languages'den dil ismini al
                                $tenantLanguages = \Modules\LanguageManagement\app\Models\TenantLanguage::where('is_active', true)->get();
                                $langName = $tenantLanguages->where('code', $lang)->first()?->native_name ?? strtoupper($lang);
                            @endphp

                            <div class="language-content" data-language="{{ $lang }}"
                                style="display: {{ $currentLanguage === $lang ? 'block' : 'none' }};">

                                <!-- Başlık ve Slug alanları -->
                                <div class="row mb-3">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" wire:model="multiLangInputs.{{ $lang }}.title"
                                                class="form-control @error('multiLangInputs.' . $lang . '.title') is-invalid @enderror"
                                                placeholder="{{ __('portfolio::admin.title_field') }}">
                                            <label>
                                                {{ __('portfolio::admin.title_field') }}
                                                @if ($lang === session('site_default_language', 'tr'))
                                                    <span class="required-star">★</span>
                                                @endif
                                            </label>
                                            @error('multiLangInputs.' . $lang . '.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                wire:model="multiLangInputs.{{ $lang }}.slug" maxlength="255"
                                                placeholder="portfolio-url-slug">
                                            <label>
                                                {{ __('admin.page_url_slug') }}
                                                <small class="text-muted ms-2">-
                                                    {{ __('admin.slug_auto_generated') }}</small>
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>{{ __('admin.slug_help') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Portfolio Özel Alanları - Sadece default dilde -->
                                @if ($lang === session('site_default_language', 'tr'))
                                    
                                    <!-- Kategori seçimi -->
                                    <div class="form-floating mb-3">
                                        <select wire:model.defer="inputs.portfolio_category_id"
                                            class="form-control @error('inputs.portfolio_category_id') is-invalid @enderror"
                                            data-choices 
                                            data-choices-search="{{ count($categories) > 6 ? 'true' : 'false' }}"
                                            data-choices-filter="true">
                                            <option value="">{{ __('portfolio::admin.select_category') }}</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->portfolio_category_id }}" {{ $category->portfolio_category_id == $inputs['portfolio_category_id'] ? 'selected' : '' }}>
                                                {{ $category->getTranslated('title', app()->getLocale()) }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <label>{{ __('portfolio::admin.category') }}</label>
                                        @error('inputs.portfolio_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Portfolio Detay Alanları -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" wire:model="inputs.client"
                                                    class="form-control"
                                                    placeholder="{{ __('portfolio::admin.client_name') }}">
                                                <label>{{ __('portfolio::admin.client_name') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" wire:model="inputs.date"
                                                    class="form-control"
                                                    placeholder="{{ __('portfolio::admin.project_date') }}">
                                                <label>{{ __('portfolio::admin.project_date') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="url" wire:model="inputs.url"
                                                    class="form-control"
                                                    placeholder="{{ __('portfolio::admin.project_url') }}">
                                                <label>{{ __('portfolio::admin.project_url') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fotoğraf Yükleme -->
                                    @include('portfolio::admin.partials.image-upload', [
                                        'imageKey' => 'image',
                                        'label' => __('admin.drag_drop_image')
                                    ])

                                @endif

                                <!-- İçerik editörü -->
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('portfolio::admin.content'),
                                    'placeholder' => __('portfolio::admin.content_placeholder')
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3">
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

                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        <x-seomanagement::universal-seo-tab :model="$this->currentPortfolio()" :available-languages="$availableLanguages" :current-language="$currentLanguage" :seo-data-cache="$seoDataCache" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.portfolio" :model-id="$portfolioId" />

        </div>
    </form>
</div>

@push('scripts')
    {{-- Portfolio JavaScript Variables --}}
    <script>
        window.currentPortfolioId = {{ $portfolioId ?? 'null' }};
        window.currentLanguage = '{{ $currentLanguage }}';
        let currentLanguage = '{{ $currentLanguage }}';
    </script>
@endpush