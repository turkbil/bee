<div>
    @php
        View::share(
            'pretitle',
            $blogId ? __('blog::admin.edit_blog_pretitle') : __('blog::admin.new_blog_pretitle'),
        );
    @endphp

    @include('blog::admin.helper')

    <form method="post" wire:submit.prevent="save">
        @include('admin.partials.error_message')
        <div class="card">

            <x-tab-system :tabs="$tabConfig" :tab-completion="$tabCompletionStatus" storage-key="blog_active_tab">
                {{-- Studio Edit Button --}}
                @if ($studioEnabled && $blogId)
                    <li class="nav-item ms-3">
                        <a href="{{ route('admin.studio.editor', ['module' => 'blog', 'id' => $blogId]) }}"
                            target="_blank" class="btn btn-outline-primary"
                            style="padding: 0.20rem 0.75rem; margin-top: 5px;">
                            <i
                                class="fa-solid fa-wand-magic-sparkles fa-lg me-1"></i>{{ __('blog::admin.studio.editor') }}
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
                                                placeholder="{{ __('blog::admin.title_field') }}">
                                            <label>
                                                {{ __('blog::admin.title_field') }}
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
                                                {{ __('admin.blog_url_slug') }}
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
                                            <select wire:model="inputs.blog_category_id"
                                                class="form-control @error('inputs.blog_category_id') is-invalid @enderror"
                                                id="category_select">
                                                <option value="">{{ __('blog::admin.select_category') }}</option>
                                                @foreach($this->activeCategories as $category)
                                                    <option value="{{ $category->category_id }}">
                                                        {{ $category->getTranslated('title', app()->getLocale()) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="category_select">
                                                {{ __('blog::admin.category') }}
                                            </label>
                                            @error('inputs.blog_category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <!-- Blog Özellikleri -->
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="datetime-local" wire:model="inputs.published_at"
                                                class="form-control @error('inputs.published_at') is-invalid @enderror"
                                                id="published_at">
                                            <label for="published_at">
                                                {{ __('blog::admin.published_at') }}
                                            </label>
                                            @error('inputs.published_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <div class="form-floating">
                                            <input type="text" wire:model="inputs.tags"
                                                class="form-control @error('inputs.tags') is-invalid @enderror"
                                                id="tags" placeholder="etiket1, etiket2">
                                            <label for="tags">
                                                {{ __('blog::admin.tags') }}
                                            </label>
                                            <div class="form-text">
                                                <small class="text-muted">
                                                    {{ __('blog::admin.tags_help') }}
                                                </small>
                                            </div>
                                            @error('inputs.tags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Öne Çıkan Checkbox -->
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" id="is_featured" name="is_featured" wire:model="inputs.is_featured" value="1" />
                                        <div class="state p-success p-on ms-2">
                                            <label>{{ __('blog::admin.featured') }}</label>
                                        </div>
                                        <div class="state p-off ms-2">
                                            <label>{{ __('blog::admin.not_featured') }}</label>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Excerpt Alanı -->
                                <div class="mb-4">
                                    <div class="form-floating">
                                        <textarea wire:model="multiLangInputs.{{ $lang }}.excerpt"
                                            class="form-control @error('multiLangInputs.' . $lang . '.excerpt') is-invalid @enderror"
                                            id="excerpt_{{ $lang }}"
                                            maxlength="500"
                                            rows="3"
                                            style="height: 100px;"
                                            placeholder="{{ __('blog::admin.excerpt_placeholder') }}"></textarea>
                                        <label for="excerpt_{{ $lang }}">
                                            {{ __('blog::admin.excerpt') }} ({{ $langName }})
                                        </label>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                {{ __('blog::admin.excerpt_help') }}
                                            </small>
                                        </div>
                                        @error('multiLangInputs.' . $lang . '.excerpt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- MEDYA YÖNETİMİ --}}
                        <div class="mb-4">
                            <livewire:mediamanagement::universal-media
                                wire:id="blog-media-component"
                                :model-id="$blogId"
                                model-type="blog"
                                model-class="Modules\Blog\App\Models\Blog"
                                :collections="['hero', 'gallery']"
                                :sortable="true"
                                :set-featured-from-gallery="true"
                                :key="'universal-media-' . ($blogId ?? 'new')"
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
                                    'label' => __('blog::admin.content'),
                                    'placeholder' => __('blog::admin.content_placeholder'),
                                ])
                            </div>
                        @endforeach

                        {{-- SEO Character Counter - manage.js'te tanımlı --}}

                        {{-- DIVIDER --}}
                        <hr class="my-5">

                        {{-- FAQ & HOWTO VISUAL EDITORS --}}
                        <div class="row mb-4">
                            {{-- FAQ VISUAL EDITOR --}}
                            <div class="col-12 col-xl-6" x-data="faqEditor(@entangle('inputs.faq_data').defer)">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">{{ __('blog::admin.faq') }}</label>
                                    <button type="button" @click="addFaq()" class="btn btn-primary">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                {{-- FAQ Sorular Container --}}
                                <div class="faq-container">
                                    <template x-for="(faq, index) in faqs" :key="index">
                                        <div class="faq-item card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="handle text-muted me-2" style="cursor: move;" title="Sürükle">
                                                        <i class="fa-solid fa-grip-vertical"></i>
                                                    </span>
                                                    <select x-model="faq.icon" class="form-select form-select-sm me-2" style="width: 70px;">
                                                        <option value="fa-question-circle">❓</option>
                                                        <option value="fa-info-circle">ℹ️</option>
                                                        <option value="fa-check-circle">✅</option>
                                                        <option value="fa-wrench">🔧</option>
                                                        <option value="fa-shield-alt">🛡️</option>
                                                        <option value="fa-dollar-sign">💰</option>
                                                        <option value="fa-truck">🚚</option>
                                                        <option value="fa-cog">⚙️</option>
                                                    </select>
                                                    <button type="button" @click="removeFaq(index)"
                                                            class="btn btn-sm btn-link text-danger ms-auto p-0"
                                                            title="Sil">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" x-model="faq.question"
                                                           class="form-control"
                                                           :id="'faq_question_' + index"
                                                           placeholder="Soru" />
                                                    <label :for="'faq_question_' + index">{{ __('blog::admin.question') }}</label>
                                                </div>
                                                <div class="form-floating">
                                                    <textarea x-model="faq.answer"
                                                              class="form-control"
                                                              :id="'faq_answer_' + index"
                                                              style="height: 100px"
                                                              placeholder="Cevap"></textarea>
                                                    <label :for="'faq_answer_' + index">{{ __('blog::admin.answer') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="faqs.length === 0" class="alert alert-info">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        {{ __('blog::admin.no_faq_yet') }}
                                    </div>
                                </div>
                            </div>

                            {{-- HOWTO VISUAL EDITOR --}}
                            <div class="col-12 col-xl-6" x-data="howtoEditor(@entangle('inputs.howto_data').defer)">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">{{ __('blog::admin.howto') }}</label>
                                    <button type="button" @click="addStep()" class="btn btn-primary">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                {{-- HowTo Steps Container --}}
                                <div class="howto-steps-container">
                                    <template x-for="(step, index) in howto.steps" :key="index">
                                        <div class="howto-item card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="handle text-muted me-2" style="cursor: move;" title="Sürükle">
                                                        <i class="fa-solid fa-grip-vertical"></i>
                                                    </span>
                                                    <span class="badge bg-secondary me-2" x-text="'Adım ' + (index + 1)"></span>
                                                    <select x-model="step.icon" class="form-select form-select-sm me-2" style="width: 70px;">
                                                        <option value="fa-cog">⚙️</option>
                                                        <option value="fa-check">✅</option>
                                                        <option value="fa-wrench">🔧</option>
                                                        <option value="fa-power-off">🔌</option>
                                                        <option value="fa-play">▶️</option>
                                                        <option value="fa-stop">⏹️</option>
                                                        <option value="fa-arrow-right">➡️</option>
                                                        <option value="fa-warning">⚠️</option>
                                                    </select>
                                                    <button type="button" @click="removeStep(index)"
                                                            class="btn btn-sm btn-link text-danger ms-auto p-0"
                                                            title="Sil">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input type="text" x-model="step.name"
                                                           class="form-control"
                                                           :id="'step_name_' + index"
                                                           placeholder="Başlık" />
                                                    <label :for="'step_name_' + index">{{ __('blog::admin.step_title') }}</label>
                                                </div>
                                                <div class="form-floating">
                                                    <textarea x-model="step.text"
                                                              class="form-control"
                                                              :id="'step_text_' + index"
                                                              style="height: 80px"
                                                              placeholder="Detay"></textarea>
                                                    <label :for="'step_text_' + index">{{ __('blog::admin.step_text') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <div x-show="howto.steps.length === 0" class="alert alert-info">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        {{ __('blog::admin.no_steps_yet') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DIVIDER --}}
                        <hr class="my-5">

                        <!-- Aktif/Pasif - sadece bir kere -->
                        <div class="mb-3 mt-4">
                            <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                <input type="checkbox" id="is_active" name="is_active" wire:model="inputs.is_active"
                                    value="1"
                                    {{ !isset($inputs['is_active']) || $inputs['is_active'] ? 'checked' : '' }} />

                                <div class="state p-success p-on ms-2">
                                    <label>{{ __('blog::admin.active') }}</label>
                                </div>
                                <div class="state p-danger p-off ms-2">
                                    <label>{{ __('blog::admin.inactive') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB - UNIVERSAL COMPONENT - NO FADE for instant switching -->
                    <div class="tab-pane" id="1" role="tabpanel">
                        <livewire:seomanagement::universal-seo-tab :model-id="$blogId" model-type="blog"
                            model-class="Modules\Blog\App\Models\Blog" />
                    </div>

                </div>
            </div>

            <x-form-footer route="admin.blog" :model-id="$blogId" />

        </div>
    </form>


    @push('scripts')
        {{-- 🎯 MODEL & MODULE SETUP --}}
        <script>
            window.currentModelId = {{ $blogId ?? 'null' }};
            window.currentModuleName = 'blog';
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

        {{-- Sortable.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        {{-- FAQ & HowTo Visual Editors --}}
        <script>
            // FAQ Visual Editor Component
            function faqEditor(wireModel) {
                return {
                    faqs: [],
                    sortable: null,

                    init() {
                        // Livewire'dan veriyi al (JSON string veya array)
                        this.$watch('$wire.inputs.faq_data', (value) => {
                            if (typeof value === 'string' && value) {
                                try {
                                    this.faqs = JSON.parse(value);
                                } catch (e) {
                                    this.faqs = [];
                                }
                            } else if (Array.isArray(value)) {
                                this.faqs = value;
                            } else {
                                this.faqs = [];
                            }
                        });

                        // İlk yükleme
                        const initialValue = this.$wire.inputs.faq_data;
                        if (typeof initialValue === 'string' && initialValue) {
                            try {
                                this.faqs = JSON.parse(initialValue);
                            } catch (e) {
                                this.faqs = [];
                            }
                        } else if (Array.isArray(initialValue)) {
                            this.faqs = initialValue;
                        }

                        // Sortable.js init
                        this.$nextTick(() => {
                            const container = this.$el.querySelector('.faq-container');
                            if (container) {
                                this.sortable = Sortable.create(container, {
                                    handle: '.handle',
                                    animation: 150,
                                    onEnd: (evt) => {
                                        // Array'i yeniden düzenle (sıralama için)
                                        const movedItem = this.faqs.splice(evt.oldIndex, 1)[0];
                                        this.faqs.splice(evt.newIndex, 0, movedItem);
                                        this.updateWire();
                                    }
                                });
                            }
                        });

                        // Değişiklikleri Livewire'a gönder
                        this.$watch('faqs', () => {
                            this.updateWire();
                        }, { deep: true });
                    },

                    addFaq() {
                        this.faqs.push({
                            question: '',
                            answer: '',
                            icon: 'fa-question-circle'
                        });
                    },

                    removeFaq(index) {
                        this.faqs.splice(index, 1);
                    },

                    updateWire() {
                        // JSON string olarak Livewire'a gönder
                        this.$wire.set('inputs.faq_data', JSON.stringify(this.faqs));
                    }
                }
            }

            // HowTo Visual Editor Component
            function howtoEditor(wireModel) {
                return {
                    howto: {
                        name: '',
                        description: '',
                        steps: []
                    },
                    sortable: null,

                    init() {
                        // Livewire'dan veriyi al
                        this.$watch('$wire.inputs.howto_data', (value) => {
                            if (typeof value === 'string' && value) {
                                try {
                                    this.howto = JSON.parse(value);
                                    if (!this.howto.steps) this.howto.steps = [];
                                } catch (e) {
                                    this.howto = { name: '', description: '', steps: [] };
                                }
                            } else if (typeof value === 'object' && value !== null) {
                                this.howto = value;
                                if (!this.howto.steps) this.howto.steps = [];
                            }
                        });

                        // İlk yükleme
                        const initialValue = this.$wire.inputs.howto_data;
                        if (typeof initialValue === 'string' && initialValue) {
                            try {
                                this.howto = JSON.parse(initialValue);
                                if (!this.howto.steps) this.howto.steps = [];
                            } catch (e) {
                                this.howto = { name: '', description: '', steps: [] };
                            }
                        } else if (typeof initialValue === 'object' && initialValue !== null) {
                            this.howto = initialValue;
                            if (!this.howto.steps) this.howto.steps = [];
                        }

                        // Sortable.js init
                        this.$nextTick(() => {
                            const container = this.$el.querySelector('.howto-steps-container');
                            if (container) {
                                this.sortable = Sortable.create(container, {
                                    handle: '.handle',
                                    animation: 150,
                                    onEnd: (evt) => {
                                        // Array'i yeniden düzenle (badge numaraları için)
                                        const movedItem = this.howto.steps.splice(evt.oldIndex, 1)[0];
                                        this.howto.steps.splice(evt.newIndex, 0, movedItem);
                                        this.updateWire();
                                    }
                                });
                            }
                        });

                        // Değişiklikleri Livewire'a gönder
                        this.$watch('howto', () => {
                            this.updateWire();
                        }, { deep: true });
                    },

                    addStep() {
                        this.howto.steps.push({
                            name: '',
                            text: '',
                            icon: 'fa-cog'
                        });
                    },

                    removeStep(index) {
                        this.howto.steps.splice(index, 1);
                    },

                    updateWire() {
                        // JSON string olarak Livewire'a gönder
                        this.$wire.set('inputs.howto_data', JSON.stringify(this.howto));
                    }
                }
            }
        </script>
    @endpush
</div>
