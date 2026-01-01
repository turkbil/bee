<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Playlist;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class PlaylistManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $playlistId;

    public $multiLangInputs = [];

    public $inputs = [
        'is_active' => true,
        'is_system' => true,     // Varsayılan: Sistem Listesi
        'is_public' => true,     // Varsayılan: Herkese Açık
        'is_radio' => false,     // Varsayılan: Liste Modu
        'sector_ids' => [],
        'radio_ids' => [],
        'corporate_ids' => [],   // ✅ Kurumsal hesap dağıtımı
    ];

    public $sectorSearch = '';
    public $radioSearch = '';
    public $corporateSearch = '';

    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    protected $playlistService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->playlistId) {
            return null;
        }

        return Playlist::query()->find($this->playlistId);
    }

    #[Computed]
    public function activeSectors()
    {
        $query = \Modules\Muzibu\App\Models\Sector::where('is_active', true);

        // Search filter
        if (!empty($this->sectorSearch)) {
            $search = strtolower($this->sectorSearch);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('title->tr')->get();
    }

    #[Computed]
    public function activeRadios()
    {
        $query = \Modules\Muzibu\App\Models\Radio::where('is_active', true);

        // Search filter
        if (!empty($this->radioSearch)) {
            $search = strtolower($this->radioSearch);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.en"))) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('title->tr')->get();
    }

    #[Computed]
    public function activeCorporates()
    {
        $query = \Modules\Muzibu\App\Models\MuzibuCorporateAccount::where('is_active', true)
            ->whereNull('parent_id'); // Sadece ana firmalar (şubeler değil)

        // Search filter
        if (!empty($this->corporateSearch)) {
            $search = strtolower($this->corporateSearch);
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(company_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(corporate_code) LIKE ?', ["%{$search}%"]);
            });
        }

        return $query->orderBy('company_name')->get();
    }

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    public function boot()
    {
        $this->playlistService = app(\Modules\Muzibu\App\Services\PlaylistService::class);

        view()->share('pretitle', __('muzibu::admin.playlist_management'));
        view()->share('title', __('muzibu::admin.playlists'));
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
            $this->playlistId = $id;
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
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('playlist');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 PlaylistManage - Dil değişti', [
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
                                Playlist::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'playlist_id',
                                $this->playlistId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ PlaylistManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ PlaylistManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        $formData = $this->playlistService->preparePlaylistForForm($id, $this->currentLanguage);
        $playlist = $formData['playlist'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($playlist) {
            $this->inputs = $playlist->only(['is_active', 'is_system', 'is_public', 'is_radio']);

            // İlişkileri yükle (playlistables tablosundan)
            $this->inputs['sector_ids'] = $playlist->sectors()->pluck('playlistable_id')->toArray();
            $this->inputs['radio_ids'] = $playlist->radios()->pluck('playlistable_id')->toArray();
            $this->inputs['corporate_ids'] = $playlist->corporates()->pluck('playlistable_id')->toArray();

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $playlist->getTranslated('title', $lang, false) ?? '',
                    'description' => $playlist->getTranslated('description', $lang, false) ?? '',
                    'slug' => $playlist->getTranslated('slug', $lang, false) ?? '',
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
            Playlist::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->playlistId
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
            'playlistId' => $this->playlistId,
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
        $sectorIds = $this->inputs['sector_ids'] ?? [];
        $radioIds = $this->inputs['radio_ids'] ?? [];
        $corporateIds = $this->inputs['corporate_ids'] ?? [];

        // İlişkileri çıkar
        $safeInputs = collect($this->inputs)->except(['sector_ids', 'radio_ids', 'corporate_ids'])->all();

        $data = array_merge($safeInputs, $multiLangData);

        $isNewRecord = !$this->playlistId;

        if ($this->playlistId) {
            $playlist = Playlist::query()->findOrFail($this->playlistId);
            $currentData = collect($playlist->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.playlist_updated'),
                    'type' => 'success'
                ];
            } else {
                $playlist->update($data);
                log_activity($playlist, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.playlist_updated'),
                    'type' => 'success'
                ];
            }

            // İlişkileri sync et (playlistables tablosuna)
            $playlist->sectors()->sync($sectorIds);
            $playlist->radios()->sync($radioIds);
            $playlist->corporates()->sync($corporateIds);
        } else {
            $playlist = Playlist::query()->create($data);
            $this->playlistId = $playlist->playlist_id;
            log_activity($playlist, 'eklendi');

            // 🎨 MUZIBU: Hero yoksa otomatik görsel üret (Universal Helper - Tercihen)
            if (!$playlist->hasMedia('hero')) {
                \muzibu_generate_ai_cover($playlist, $playlist->title, 'playlist');
            }

            // İlişkileri sync et (playlistables tablosuna)
            $playlist->sectors()->sync($sectorIds);
            $playlist->radios()->sync($radioIds);
            $playlist->corporates()->sync($corporateIds);

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.playlist_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'playlistId' => $this->playlistId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->playlistId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.playlist.index');
        }

        if ($isNewRecord && isset($playlist)) {
            $this->dispatch('playlist-saved', $playlist->playlist_id);

            if ($resetForm) {
                // Kaydet ve Yeni Ekle: Formu resetle, aynı sayfada kal
                $this->playlistId = null;
                $this->reset(['inputs', 'multiLangInputs']);
                $this->inputs = ['is_active' => true];
                $this->currentLanguage = \get_tenant_default_locale();
                $this->initializeEmptyInputs();

                Log::info('✅ Form resetlendi - Yeni kayıt için hazır', [
                    'previous_playlist_id' => $playlist->playlist_id
                ]);
            } else {
                // Normal kaydet: Düzenleme sayfasına yönlendir
                session()->flash('toast', $toast);
                return redirect()->route('admin.muzibu.playlist.manage', ['id' => $playlist->playlist_id]);
            }
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'playlistId' => $this->playlistId
        ]);
    }

    public function render()
    {
        return view('muzibu::admin.livewire.playlist-manage-component', [
            'jsVariables' => [
                'currentPlaylistId' => $this->playlistId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'playlist';
    }

    public function getTargetFields(array $params): array
    {
        $playlistFields = [
            'title' => 'string',
            'bio' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $playlistFields[$params['target_field']] ?? 'html'];
        }

        return $playlistFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
