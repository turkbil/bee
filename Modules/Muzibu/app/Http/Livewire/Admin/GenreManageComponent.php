<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Genre;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class GenreManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $genreId;

    public $multiLangInputs = [];

    public $inputs = [
        'is_active' => true,
    ];

    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    protected $genreService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->genreId) {
            return null;
        }

        return Genre::query()->find($this->genreId);
    }

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    public function boot()
    {
        $this->genreService = app(\Modules\Muzibu\App\Services\GenreService::class);

        view()->share('pretitle', __('muzibu::admin.genre_management'));
        view()->share('title', __('muzibu::admin.genres'));
    }

    public function updated($propertyName)
    {
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        $this->boot();
        $this->initializeUniversalComponents();

        if ($id) {
            $this->genreId = $id;
            $this->loadPageData($id);
        } else {
            $this->initializeEmptyInputs();
        }

        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    protected function initializeUniversalComponents()
    {
        $languages = available_tenant_languages();
        $this->availableLanguages = array_column($languages, 'code');
        $this->languageNames = array_column($languages, 'native_name', 'code');
        $this->currentLanguage = \get_tenant_default_locale();

        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('muzibu');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('genre');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 GenreManage - Dil değişti', [
                'new_language' => $language
            ]);
        }
    }

    public function handleTranslationCompleted($result)
    {
        if ($result['success'] && isset($result['results'])) {
            foreach ($result['results'] as $translationResult) {
                if ($translationResult['success']) {
                    $lang = $translationResult['language'];
                    $field = $translationResult['field'];
                    $translatedText = $translationResult['translated_text'];

                    if (isset($this->multiLangInputs[$lang][$field])) {
                        $this->multiLangInputs[$lang][$field] = $translatedText;

                        if ($field === 'title') {
                            $this->multiLangInputs[$lang]['slug'] = SlugHelper::generateFromTitle(
                                Genre::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'genre_id',
                                $this->genreId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ GenreManage - Çeviri sonuçları alındı ve kaydedildi', [
                'translated_count' => $result['translated_count'] ?? 0
            ]);
        }
    }

    public function handleAIContentGenerated($result)
    {
        if ($result['success']) {
            $content = $result['content'];
            $targetField = $result['target_field'];
            $language = $result['language'];

            if (isset($this->multiLangInputs[$language][$targetField])) {
                $this->multiLangInputs[$language][$targetField] = $content;

                $this->save();

                Log::info('✅ GenreManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        $formData = $this->genreService->prepareGenreForForm($id, $this->currentLanguage);
        $genre = $formData['genre'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($genre) {
            $this->inputs = $genre->only(['is_active']);

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $genre->getTranslated('title', $lang, false) ?? '',
                    'description' => $genre->getTranslated('description', $lang, false) ?? '',
                    'slug' => $genre->getTranslated('slug', $lang, false) ?? '',
                ];
            }
        }
    }

    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'description' => '',
                'slug' => '',
            ];
        }
    }

    protected function getAllFormData(): array
    {
        return array_merge(
            $this->inputs,
            $this->multiLangInputs[$this->currentLanguage] ?? []
        );
    }

    protected function getMainLanguage()
    {
        return \get_tenant_default_locale();
    }

    protected function rules()
    {
        $rules = [
            'inputs.is_active' => 'boolean',
        ];

        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.description"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
        'multiLangInputs.*.description.string' => 'Açıklama metin formatında olmalıdır',
        'multiLangInputs.*.slug.string' => 'Slug metin formatında olmalıdır',
        'multiLangInputs.*.slug.max' => 'Slug en fazla 255 karakter olabilir',
    ];

    protected function getMessages()
    {
        $slugMessages = SlugHelper::getValidationMessages($this->availableLanguages, 'multiLangInputs');

        return array_merge($this->messages, $slugMessages);
    }

    protected function validateAndSanitizeContent(): array
    {
        $validated = [];
        $errors = [];

        foreach ($this->availableLanguages as $lang) {
            $description = $this->multiLangInputs[$lang]['description'] ?? '';
            if (!empty(trim($description))) {
                $result = \App\Services\SecurityValidationService::validateHtml($description);
                if (!$result['valid']) {
                    $errors[] = "HTML ({$lang}): " . implode(', ', $result['errors']);
                } else {
                    $validated['description'][$lang] = $result['clean_code'];
                }
            }
        }

        return [
            'valid' => empty($errors),
            'data' => $validated,
            'errors' => $errors
        ];
    }

    protected function prepareMultiLangData(array $validatedContent = []): array
    {
        $multiLangData = [];

        $multiLangData['title'] = [];
        foreach ($this->availableLanguages as $lang) {
            $title = $this->multiLangInputs[$lang]['title'] ?? '';
            if (!empty($title)) {
                $multiLangData['title'][$lang] = $title;
            }
        }

        $slugInputs = [];
        $titleInputs = [];
        foreach ($this->availableLanguages as $lang) {
            $slugInputs[$lang] = $this->multiLangInputs[$lang]['slug'] ?? '';
            $titleInputs[$lang] = $this->multiLangInputs[$lang]['title'] ?? '';
        }

        $multiLangData['slug'] = SlugHelper::processMultiLanguageSlugs(
            Genre::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->genreId
        );

        if (!empty($validatedContent['description'])) {
            $multiLangData['description'] = $validatedContent['description'];
        }

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'genreId' => $this->genreId,
            'redirect' => $redirect,
            'currentLanguage' => $this->currentLanguage
        ]);

        try {
            $this->validate($this->rules(), $this->getMessages());
            Log::info('✅ Validation başarılı');
        } catch (\Exception $e) {
            Log::error('❌ Validation HATASI', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Doğrulama Hatası',
                'message' => $e->getMessage(),
                'type' => 'error'
            ]);

            $this->dispatch('restore-active-tab');

            return;
        }

        $validation = $this->validateAndSanitizeContent();
        if (!$validation['valid']) {
            $this->dispatch('toast', [
                'title' => 'İçerik Doğrulama Hatası',
                'message' => implode("\n", $validation['errors']),
                'type' => 'error'
            ]);

            $this->dispatch('restore-active-tab');

            return;
        }

        $multiLangData = $this->prepareMultiLangData($validation['data']);

        $safeInputs = $this->inputs;

        $data = array_merge($safeInputs, $multiLangData);

        $isNewRecord = !$this->genreId;

        if ($this->genreId) {
            $genre = Genre::query()->findOrFail($this->genreId);
            $currentData = collect($genre->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.genre_updated'),
                    'type' => 'success'
                ];
            } else {
                $genre->update($data);
                log_activity($genre, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.genre_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $genre = Genre::query()->create($data);
            $this->genreId = $genre->genre_id;
            log_activity($genre, 'eklendi');

            // 🎨 MUZIBU: Media yoksa otomatik görsel üret (Universal Helper - Tercihen)
            if (!$genre->media_id) {
                \muzibu_generate_ai_cover($genre, $genre->title, 'genre');
            }

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.genre_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'genreId' => $this->genreId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->genreId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.genre.index');
        }

        if ($isNewRecord && isset($genre)) {
            $this->dispatch('genre-saved', $genre->genre_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.genre.manage', ['id' => $genre->genre_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'genreId' => $this->genreId
        ]);

        if ($resetForm && !$this->genreId) {
            $this->reset();
            $this->currentLanguage = \get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.genre-manage-component', [
            'jsVariables' => [
                'currentGenreId' => $this->genreId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'genre';
    }

    public function getTargetFields(array $params): array
    {
        $genreFields = [
            'title' => 'string',
            'bio' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $genreFields[$params['target_field']] ?? 'html'];
        }

        return $genreFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
