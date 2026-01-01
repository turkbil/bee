<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Radio;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class RadioManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $radioId;

    public $multiLangInputs = [];

    public $inputs = [
        'is_active' => true,
        'playlist_ids' => [],
    ];

    public $playlistSearch = '';

    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    protected $radioService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->radioId) {
            return null;
        }

        return Radio::query()->find($this->radioId);
    }

    #[Computed]
    public function activePlaylists()
    {
        $query = \Modules\Muzibu\App\Models\Playlist::where('is_active', true);

        // Search filtreleme
        if (!empty($this->playlistSearch)) {
            $locale = app()->getLocale();
            $search = strtolower($this->playlistSearch);

            $query->where(function($q) use ($search, $locale) {
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, ?))) LIKE ?", ["$.{$locale}", "%{$search}%"]);

                if ($locale !== 'tr') {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.tr'))) LIKE ?", ["%{$search}%"]);
                }
                if ($locale !== 'en') {
                    $q->orWhereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.en'))) LIKE ?", ["%{$search}%"]);
                }
            });
        }

        return $query->orderBy('title->tr')->get();
    }

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    public function boot()
    {
        $this->radioService = app(\Modules\Muzibu\App\Services\RadioService::class);

        view()->share('pretitle', __('muzibu::admin.radio_management'));
        view()->share('title', __('muzibu::admin.radios'));
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
            $this->radioId = $id;
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
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('radio');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 RadioManage - Dil değişti', [
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
                                Radio::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'radio_id',
                                $this->radioId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ RadioManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ RadioManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        $formData = $this->radioService->prepareRadioForForm($id, $this->currentLanguage);
        $radio = $formData['radio'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($radio) {
            $this->inputs = $radio->only(['is_active']);

            // İlişkileri yükle
            $this->inputs['playlist_ids'] = $radio->playlists()->pluck('muzibu_playlists.playlist_id')->toArray();

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $radio->getTranslated('title', $lang, false) ?? '',
                    'description' => $radio->getTranslated('description', $lang, false) ?? '',
                    'slug' => $radio->getTranslated('slug', $lang, false) ?? '',
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
            Radio::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->radioId
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
            'radioId' => $this->radioId,
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

        // İlişkiler için ayrı tut
        $playlistIds = $this->inputs['playlist_ids'] ?? [];

        // İlişkileri çıkar
        $safeInputs = collect($this->inputs)->except(['playlist_ids'])->all();

        $data = array_merge($safeInputs, $multiLangData);

        $isNewRecord = !$this->radioId;

        if ($this->radioId) {
            $radio = Radio::query()->findOrFail($this->radioId);
            $currentData = collect($radio->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.radio_updated'),
                    'type' => 'success'
                ];
            } else {
                $radio->update($data);
                log_activity($radio, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.radio_updated'),
                    'type' => 'success'
                ];
            }

            // İlişkileri sync et
            $radio->playlists()->sync($playlistIds);
        } else {
            $radio = Radio::query()->create($data);
            $this->radioId = $radio->radio_id;
            log_activity($radio, 'eklendi');

            // 🎨 MUZIBU: Hero yoksa otomatik görsel üret (Universal Helper - Tercihen)
            if (!$radio->hasMedia('hero')) {
                \muzibu_generate_ai_cover($radio, $radio->title, 'radio');
            }

            // İlişkileri sync et
            $radio->playlists()->sync($playlistIds);

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.radio_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'radioId' => $this->radioId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->radioId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.radio.index');
        }

        if ($isNewRecord && isset($radio)) {
            $this->dispatch('radio-saved', $radio->radio_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.radio.manage', ['id' => $radio->radio_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'radioId' => $this->radioId
        ]);

        if ($resetForm && !$this->radioId) {
            $this->reset();
            $this->currentLanguage = \get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.radio-manage-component', [
            'jsVariables' => [
                'currentRadioId' => $this->radioId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'radio';
    }

    public function getTargetFields(array $params): array
    {
        $radioFields = [
            'title' => 'string',
            'bio' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $radioFields[$params['target_field']] ?? 'html'];
        }

        return $radioFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
