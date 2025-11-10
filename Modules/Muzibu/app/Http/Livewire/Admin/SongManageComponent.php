<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\AI\app\Traits\HasAIContentGeneration;
use Modules\AI\app\Contracts\AIContentGeneratable;
use Modules\Muzibu\App\Models\Song;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

class SongManageComponent extends Component implements AIContentGeneratable
{
    use WithFileUploads, HasAIContentGeneration;

    public $songId;

    public $multiLangInputs = [];

    public $inputs = [
        'is_active' => true,
    ];

    public $audioFile;

    public $currentLanguage;
    public $availableLanguages = [];
    public $languageNames = [];
    public $activeTab;
    public $tabConfig = [];
    public $tabCompletionStatus = [];

    protected $songService;

    #[Computed]
    public function currentPage()
    {
        if (!$this->songId) {
            return null;
        }

        return Song::query()->find($this->songId);
    }

    #[Computed]
    public function activeAlbums()
    {
        return \Modules\Muzibu\App\Models\Album::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    #[Computed]
    public function activeGenres()
    {
        return \Modules\Muzibu\App\Models\Genre::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'languageChanged' => 'handleLanguageChange',
        'translation-completed' => 'handleTranslationCompleted',
        'ai-content-generated' => 'handleAIContentGenerated',
    ];

    /**
     * Audio dosya yükleme - otomatik duration hesaplama
     */
    public function updatedAudioFile()
    {
        $this->validate([
            'audioFile' => 'file|mimes:mp3,wav,flac,m4a,ogg|max:102400', // 100MB
        ]);

        try {
            // Dosyayı storage/muzibu/songs/ klasörüne kaydet
            $filename = uniqid('song_') . '.' . $this->audioFile->getClientOriginalExtension();
            $path = $this->audioFile->storeAs('muzibu/songs', $filename, 'public');

            // File path'i inputs'a ekle
            $this->inputs['file_path'] = $filename;

            // Duration hesapla (getID3 kütüphanesi ile)
            $fullPath = storage_path('app/public/' . $path);
            $duration = $this->calculateAudioDuration($fullPath);

            if ($duration) {
                $this->inputs['duration'] = $duration;
                Log::info('✅ Audio dosyası yüklendi ve duration hesaplandı', [
                    'filename' => $filename,
                    'duration' => $duration,
                    'formatted' => gmdate('i:s', $duration)
                ]);
            } else {
                Log::warning('⚠️ Audio duration hesaplanamadı, varsayılan 0 kullanılacak', [
                    'filename' => $filename
                ]);
                $this->inputs['duration'] = 0;
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Şarkı dosyası yüklendi! Süre: ' . gmdate('i:s', $this->inputs['duration']),
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Audio upload hatası', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Dosya yüklenirken hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Audio dosyasının duration'ını hesapla (getID3 veya FFmpeg ile)
     */
    protected function calculateAudioDuration(string $filePath): ?int
    {
        try {
            // Önce getID3 kütüphanesi dene
            if (class_exists('\getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);

                if (isset($fileInfo['playtime_seconds'])) {
                    return (int) round($fileInfo['playtime_seconds']);
                }
            }

            // getID3 yoksa FFmpeg/FFprobe dene
            if (function_exists('shell_exec')) {
                $ffprobeCmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
                $duration = shell_exec($ffprobeCmd);

                if ($duration && is_numeric(trim($duration))) {
                    return (int) round(floatval(trim($duration)));
                }
            }

            Log::warning('⚠️ getID3 ve FFprobe bulunamadı, duration hesaplanamadı');
            return null;

        } catch (\Exception $e) {
            Log::error('❌ Duration hesaplama hatası', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);
            return null;
        }
    }

    public function boot()
    {
        $this->songService = app(\Modules\Muzibu\App\Services\SongService::class);

        view()->share('pretitle', __('muzibu::admin.song_management'));
        view()->share('title', __('muzibu::admin.songs'));
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
            $this->songId = $id;
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
        $this->currentLanguage = get_tenant_default_locale();

        $this->tabConfig = \App\Services\GlobalTabService::getAllTabs('muzibu');
        $this->activeTab = \App\Services\GlobalTabService::getDefaultTabKey('song');
    }

    public function handleLanguageChange($language)
    {
        if (in_array($language, $this->availableLanguages)) {
            $this->currentLanguage = $language;

            Log::info('🎯 SongManage - Dil değişti', [
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
                                Song::class,
                                $translatedText,
                                $lang,
                                'slug',
                                'song_id',
                                $this->songId
                            );
                        }
                    }
                }
            }

            $this->save();

            Log::info('✅ SongManage - Çeviri sonuçları alındı ve kaydedildi', [
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

                Log::info('✅ SongManage - AI içerik alındı ve kaydedildi', [
                    'field' => $targetField,
                    'language' => $language,
                    'content_length' => strlen($content)
                ]);
            }
        }
    }

    protected function loadPageData($id)
    {
        $formData = $this->songService->prepareSongForForm($id, $this->currentLanguage);
        $song = $formData['song'] ?? null;
        $this->tabCompletionStatus = $formData['tabCompletion'] ?? [];

        if ($song) {
            $this->inputs = $song->only(['is_active', 'is_featured', 'album_id', 'genre_id', 'duration', 'file_path']);

            foreach ($this->availableLanguages as $lang) {
                $this->multiLangInputs[$lang] = [
                    'title' => $song->getTranslated('title', $lang, false) ?? '',
                    'lyrics' => $song->getTranslated('lyrics', $lang, false) ?? '',
                    'slug' => $song->getTranslated('slug', $lang, false) ?? '',
                ];
            }
        }
    }

    protected function initializeEmptyInputs()
    {
        foreach ($this->availableLanguages as $lang) {
            $this->multiLangInputs[$lang] = [
                'title' => '',
                'lyrics' => '',
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
        return get_tenant_default_locale();
    }

    protected function rules()
    {
        $rules = [
            'inputs.is_active' => 'boolean',
            'inputs.is_featured' => 'boolean',
            'inputs.album_id' => 'nullable|exists:muzibu_albums,album_id',
            'inputs.genre_id' => 'required|exists:muzibu_genres,genre_id',
            'inputs.duration' => 'nullable|integer|min:0',
            'inputs.file_path' => 'nullable|string|max:255',
        ];

        $mainLanguage = $this->getMainLanguage();
        foreach ($this->availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === $mainLanguage ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.lyrics"] = 'nullable|string';
        }

        return $rules;
    }

    protected $messages = [
        'inputs.is_active.boolean' => 'Aktif durumu geçerli bir değer olmalıdır',
        'inputs.is_featured.boolean' => 'Öne çıkan durumu geçerli bir değer olmalıdır',
        'inputs.album_id.exists' => 'Seçilen albüm bulunamadı',
        'inputs.genre_id.required' => 'Tür seçimi zorunludur',
        'inputs.genre_id.exists' => 'Seçilen tür bulunamadı',
        'inputs.duration.integer' => 'Süre saniye cinsinden sayı olmalıdır',
        'inputs.duration.min' => 'Süre 0 veya daha büyük olmalıdır',
        'inputs.file_path.max' => 'Dosya yolu en fazla 255 karakter olabilir',
        'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
        'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
        'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
        'multiLangInputs.*.lyrics.string' => 'Şarkı sözü metin formatında olmalıdır',
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
            $lyrics = $this->multiLangInputs[$lang]['lyrics'] ?? '';
            if (!empty(trim($lyrics))) {
                $result = \App\Services\SecurityValidationService::validateHtml($lyrics);
                if (!$result['valid']) {
                    $errors[] = "HTML ({$lang}): " . implode(', ', $result['errors']);
                } else {
                    $validated['lyrics'][$lang] = $result['clean_code'];
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
            Song::class,
            $slugInputs,
            $titleInputs,
            'slug',
            $this->songId
        );

        if (!empty($validatedContent['lyrics'])) {
            $multiLangData['lyrics'] = $validatedContent['lyrics'];
        }

        return $multiLangData;
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->dispatch('sync-tinymce-content');

        Log::info('🚀 SAVE METHOD BAŞLADI', [
            'songId' => $this->songId,
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

        $isNewRecord = !$this->songId;

        if ($this->songId) {
            $song = Song::query()->findOrFail($this->songId);
            $currentData = collect($song->toArray())->only(array_keys($data))->all();

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.song_updated'),
                    'type' => 'success'
                ];
            } else {
                $song->update($data);
                log_activity($song, 'güncellendi');

                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.song_updated'),
                    'type' => 'success'
                ];
            }
        } else {
            $song = Song::query()->create($data);
            $this->songId = $song->song_id;
            log_activity($song, 'eklendi');

            $toast = [
                'title' => __('admin.success'),
                'message' => __('muzibu::admin.song_created'),
                'type' => 'success'
            ];
        }

        Log::info('🎯 Save method tamamlanıyor', [
            'songId' => $this->songId,
            'redirect' => $redirect,
            'isNewRecord' => $isNewRecord
        ]);

        $this->dispatch('toast', $toast);

        $this->dispatch('page-saved', $this->songId);

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.song.index');
        }

        if ($isNewRecord && isset($song)) {
            $this->dispatch('song-saved', $song->song_id);

            session()->flash('toast', $toast);
            return redirect()->route('admin.muzibu.song.manage', ['id' => $song->song_id]);
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'songId' => $this->songId
        ]);

        if ($resetForm && !$this->songId) {
            $this->reset();
            $this->currentLanguage = get_tenant_default_locale();
            $this->initializeEmptyInputs();
        }
    }

    public function render()
    {
        return view('muzibu::admin.livewire.song-manage-component', [
            'jsVariables' => [
                'currentSongId' => $this->songId ?? null,
                'currentLanguage' => $this->currentLanguage ?? 'tr'
            ]
        ]);
    }

    public function getEntityType(): string
    {
        return 'song';
    }

    public function getTargetFields(array $params): array
    {
        $songFields = [
            'title' => 'string',
            'lyrics' => 'html',
            'meta_title' => 'string',
            'meta_description' => 'text'
        ];

        if (isset($params['target_field'])) {
            return [$params['target_field'] => $songFields[$params['target_field']] ?? 'html'];
        }

        return $songFields;
    }

    public function getModuleInstructions(): string
    {
        return __('muzibu::admin.ai_content_instructions');
    }
}
