<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Sector;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class SectorManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $sectorId;

    public $multiLangInputs = [];

    public $inputs = [
        'is_active' => true,
        'radio_ids' => [],
        'playlist_ids' => [],
    ];

    public $radioSearch = '';
    public $playlistSearch = '';

    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    protected $sectorService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->sectorId) {
            return null;
        }

        return Sector::query()->find($this->sectorId);
    }

    #[Computed]
    public function activeRadios()
    {
        $query = \Modules\Muzibu\App\Models\Radio::where('is_active', true);

        // Search filtreleme
        if (!empty($this->radioSearch)) {
            $locale = app()->getLocale();
            $search = strtolower($this->radioSearch);

            $query->where(function($q) use ($search, $locale) {
                // JSON field için LOWER() ve LIKE kullan
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, ?))) LIKE ?", ["$.{$locale}", "%{$search}%"]);

                // Diğer dillerde de ara
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

    #[Computed]
    public function activePlaylists()
    {
        $query = \Modules\Muzibu\App\Models\Playlist::where('is_active', true);

        // Search filtreleme
        if (!empty($this->playlistSearch)) {
            $locale = app()->getLocale();
            $search = strtolower($this->playlistSearch);

            $query->where(function($q) use ($search, $locale) {
                // JSON field için LOWER() ve LIKE kullan
                $q->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, ?))) LIKE ?", ["$.{$locale}", "%{$search}%"]);

                // Diğer dillerde de ara
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
        $this->sectorService = app(\Modules\Muzibu\App\Services\SectorService::class);

        view()->share('pretitle', __('muzibu::admin.sector_management'));
        view()->share('title', __('muzibu::admin.sectors'));
    }

    public function updated($propertyName)
    {
        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    public function mount($id = null)
    {
        Log::info('🚀 SECTOR MOUNT BAŞLADI', ['id' => $id, 'type' => gettype($id)]);

        $this->boot();
        $this->initializeUniversalComponents();

        if ($id) {
            Log::info('📌 ID VAR - loadPageData çağrılacak', ['id' => $id]);
            $this->sectorId = $id;
            $this->loadPageData($id);

            Log::info('📊 LoadPageData SONRASI', [
                'sectorId' => $this->sectorId,
                'multiLangInputs_tr_title' => $this->multiLangInputs['tr']['title'] ?? 'EMPTY',
                'inputs_radio_ids_count' => count($this->inputs['radio_ids'] ?? []),
                'inputs_playlist_ids_count' => count($this->inputs['playlist_ids'] ?? [])
            ]);
        } else {
            Log::info('⚪ ID YOK - initializeEmptyInputs çağrılacak');
            $this->initializeEmptyInputs();
        }

        $this->dispatch('update-tab-completion', $this->getAllFormData());
    }

    protected function initializeUniversalComponents()
    {
        $languages = available_tenant_languages();
        $this->availableLanguages = array_column($languages, 'code');
        $this->languageNames = array_column($languages, 'native_name', 'code');
        $this->currentLanguage = get_tenant_default_locale();

        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('muzibu');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('sector');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 SectorManage - Dil değişti', [
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
                                Sector::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'sector_id',
                                $this->sectorId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ SectorManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ SectorManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        Log::info('🔍 loadPageData başladı', ['sector_id' => $id]);

        $formData = $this->sectorService->prepareSectorForForm($id, $this->currentLanguage);
        $sector = $formData['sector'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        Log::info('📊 Service sonucu', [
            'sector_found' => $sector ? 'YES' : 'NO',
            'sector_id' => $sector ? $sector->sector_id : 'null',
            'connection' => $sector ? $sector->getConnectionName() : 'null'
        ]);

        if ($sector) {
            $this->inputs = $sector->only(['is_active']);

            // İlişkileri yükle
            $this->inputs['radio_ids'] = $sector->radios()->pluck('muzibu_radios.radio_id')->toArray();
            $this->inputs['playlist_ids'] = $sector->playlists()->pluck('muzibu_playlists.playlist_id')->toArray();

            Log::info('✅ İlişkiler yüklendi', [
                'radios' => count($this->inputs['radio_ids']),
                'playlists' => count($this->inputs['playlist_ids'])
            ]);

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $sector->getTranslated('title', $lang, false) ?? '',
                    'slug' => $sector->getTranslated('slug', $lang, false) ?? '',
                    'description' => $sector->getTranslated('description', $lang, false) ?? '',
                ];
            }

            Log::info('✅ multiLangInputs yüklendi', [
                'tr_title' => $this->multiLangInputs['tr']['title'] ?? 'EMPTY',
                'tr_description' => strlen($this->multiLangInputs['tr']['description'] ?? '') . ' chars'
            ]);
        } else {
            Log::error('❌ Sector bulunamadı!', ['id' => $id]);
        }
    }

    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'slug' => '',
                'description' => '',
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
        return get_tenant_default_locale();
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
            Sector::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->sectorId
        );

        // Description alanını ekle
        $multiLangData['description'] = [];
        foreach ($this->availableLanguages as $lang) {
            $description = $this->multiLangInputs[$lang]['description'] ?? '';
            if (!empty($description)) {
                $multiLangData['description'][$lang] = $description;
            }
        }

        if (!empty($validatedContent['bio'])) {
            $multiLangData['bio'] = $validatedContent['bio'];
        }

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'sectorId' => $this->sectorId,
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
        $radioIds = $this->inputs['radio_ids'] ?? [];
        $playlistIds = $this->inputs['playlist_ids'] ?? [];

        // İlişkileri çıkar
        $safeInputs = collect($this->inputs)->except(['radio_ids', 'playlist_ids'])->all();

        $data = array_merge($safeInputs, $multiLangData);

        $isNewRecord = !$this->sectorId;

        if ($this->sectorId) {
            $sector = Sector::query()->findOrFail($this->sectorId);
            $currentData = collect($sector->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.sector_updated'),
                    'type' => 'success'
                ];
            } else {
                $sector->update($data);
                log_activity($sector, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.sector_updated'),
                    'type' => 'success'
                ];
            }

            // İlişkileri sync et
            $sector->radios()->sync($radioIds);
            $sector->playlists()->sync($playlistIds);
        } else {
            $sector = Sector::query()->create($data);
            $this->sectorId = $sector->sector_id;
            log_activity($sector, 'eklendi');

            // 🎨 MUZIBU: Media yoksa otomatik görsel üret (Universal Helper - Tercihen)
            if (!$sector->media_id) {
                muzibu_generate_ai_cover($sector, $sector->title, 'sektor');
            }

            // İlişkileri sync et
            $sector->radios()->sync($radioIds);
            $sector->playlists()->sync($playlistIds);

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.sector_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'sectorId' => $this->sectorId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->sectorId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.sector.index');
        }

        if ($isNewRecord && isset($sector)) {
            $this->dispatch('sector-saved', $sector->sector_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.sector.manage', ['id' => $sector->sector_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'sectorId' => $this->sectorId
        ]);

        if ($resetForm && !$this->sectorId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.sector-manage-component', [
            'jsVariables' => [
                'currentSectorId' => $this->sectorId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'sector';
    }

    public function getTargetFields(array $params): array
    {
        $sectorFields = [
            'title' => 'string',
            'bio' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $sectorFields[$params['target_field']] ?? 'html'];
        }

        return $sectorFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
