<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Artist;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class ArtistManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $artistId;

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

    protected $artistService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->artistId) {
            return null;
        }

        return Artist::query()->find($this->artistId);
    }

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    public function boot()
    {
        $this->artistService = app(\Modules\Muzibu\App\Services\ArtistService::class);

        view()->share('pretitle', __('muzibu::admin.artist_management'));
        view()->share('title', __('muzibu::admin.artists'));
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
            $this->artistId = $id;
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
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('artist');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 ArtistManage - Dil değişti', [
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
                                Artist::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'artist_id',
                                $this->artistId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ ArtistManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ ArtistManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        $formData = $this->artistService->prepareArtistForForm($id, $this->currentLanguage);
        $artist = $formData['artist'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($artist) {
            $this->inputs = $artist->only(['is_active']);

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $artist->getTranslated('title', $lang, false) ?? '',
                    'bio' => $artist->getTranslated('bio', $lang, false) ?? '',
                    'slug' => $artist->getTranslated('slug', $lang, false) ?? '',
                ];
            }
        }
    }

    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'bio' => '',
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
            $rules["multiLangInputs.{$lang}.bio"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
        'multiLangInputs.*.bio.string' => 'Biyografi metin formatında olmalıdır',
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
            $bio = $this->multiLangInputs[$lang]['bio'] ?? '';
            if (!empty(trim($bio))) {
                $result = \App\Services\SecurityValidationService::validateHtml($bio);
                if (!$result['valid']) {
                    $errors[] = "HTML ({$lang}): " . implode(', ', $result['errors']);
                } else {
                    $validated['bio'][$lang] = $result['clean_code'];
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
            Artist::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->artistId
        );

        if (!empty($validatedContent['bio'])) {
            $multiLangData['bio'] = $validatedContent['bio'];
        }

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'artistId' => $this->artistId,
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

        $isNewRecord = !$this->artistId;

        if ($this->artistId) {
            $artist = Artist::query()->findOrFail($this->artistId);
            $currentData = collect($artist->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.artist_updated'),
                    'type' => 'success'
                ];
            } else {
                $artist->update($data);
                log_activity($artist, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.artist_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $artist = Artist::query()->create($data);
            $this->artistId = $artist->artist_id;
            log_activity($artist, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.artist_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'artistId' => $this->artistId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->artistId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.artist.index');
        }

        if ($isNewRecord && isset($artist)) {
            $this->dispatch('artist-saved', $artist->artist_id);

            if ($resetForm) {
                // Kaydet ve Yeni Ekle: Formu resetle, aynı sayfada kal
                $this->artistId = null;
                $this->reset(['inputs', 'multiLangInputs']);
                $this->inputs = ['is_active' => true];
                $this->currentLanguage = \get_tenant_default_locale();
                $this->initializeEmptyInputs();

                Log::info('✅ Form resetlendi - Yeni kayıt için hazır', [
                    'previous_artist_id' => $artist->artist_id
                ]);
            } else {
                // Normal kaydet: Düzenleme sayfasına yönlendir
                session()->flash('toast', $toast);
                return redirect()->route('admin.muzibu.artist.manage', ['id' => $artist->artist_id]);
            }
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'artistId' => $this->artistId
        ]);
    }

    public function render()
    {
        return view('muzibu::admin.livewire.artist-manage-component', [
            'jsVariables' => [
                'currentArtistId' => $this->artistId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'artist';
    }

    public function getTargetFields(array $params): array
    {
        $artistFields = [
            'title' => 'string',
            'bio' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $artistFields[$params['target_field']] ?? 'html'];
        }

        return $artistFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
