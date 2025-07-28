<div wire:key="page-manage-component" wire:id="page-manage-component">
    {{-- Helper dosyası --}}
    @include('page::admin.helper')
    @include('admin.partials.error_message')

    <form method="post" wire:submit.prevent="save">
        <div class="card">
            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="page_active_tab">

                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $pageId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'page', 'id' => $pageId]) }}"
                            target="_blank" class="btn btn-outline-primary" style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('page::admin.studio.editor') }}
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
                                                placeholder="{{ __('page::admin.title_field') }}">
                                            <label>
                                                {{ __('page::admin.title_field') }}
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
                                                placeholder="sayfa-url-slug">
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

                                <!-- İçerik editörü -->
                                @include('admin.components.content-editor', [
                                    'lang' => $lang,
                                    'langName' => $langName,
                                    'langData' => $langData,
                                    'fieldName' => 'body',
                                    'label' => __('page::admin.content'),
                                    'placeholder' => __('page::admin.content_placeholder')
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
                                    <label>{{ __('page::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('page::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="1" role="tabpanel">
                        <x-seomanagement::universal-seo-tab :model="$this->currentPage" :available-languages="$availableLanguages" :current-language="$currentLanguage" :seo-data-cache="$seoDataCache" />
                    </div>

                    <!-- Code Tab -->
                    <div class="tab-pane fade" id="2" role="tabpanel">
                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.css" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.css_code') }}"></textarea>
                            <label>{{ __('admin.css') }}</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea wire:model="inputs.js" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('admin.js_code') }}"></textarea>
                            <label>{{ __('admin.javascript') }}</label>
                        </div>
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.page" :model-id="$pageId" />

        </div>
    </form>
</div>

@push('scripts')
    {{-- Page JavaScript Variables --}}
    <script>
        window.currentPageId = {{ $pageId ?? 'null' }};
        window.currentLanguage = '{{ $currentLanguage }}';
        let currentLanguage = '{{ $currentLanguage }}';
    </script>
@endpush
