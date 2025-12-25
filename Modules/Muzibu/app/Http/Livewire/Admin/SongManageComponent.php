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
    ];

    /**
     * Audio dosyasını kaldır
     */
    public function removeAudio()
    {
        try {
            // Eğer dosya varsa, fiziksel dosyayı sil
            if (!empty($this->inputs['file_path'])) {
                $filePath = storage_path('app/public/muzibu/songs/' . $this->inputs['file_path']);
                if (file_exists($filePath)) {
                    unlink($filePath);
                    Log::info('✅ Audio dosyası silindi', [
                        'file' => $this->inputs['file_path']
                    ]);
                }
            }

            // Form verilerini temizle
            $this->inputs['file_path'] = null;
            $this->inputs['duration'] = 0;
            $this->audioFile = null;

            // Title'ı da temizle (şarkıdan otomatik dolmuşsa)
            $defaultLocale = \get_tenant_default_locale();
            $this->multiLangInputs[$defaultLocale]['title'] = null;

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Şarkı dosyası kaldırıldı',
                'type' => 'success'
            ]);

            Log::info('✅ Audio dosyası kaldırıldı');

        } catch (\Exception $e) {
            Log::error('❌ Audio kaldırma hatası', [
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Dosya kaldırılırken hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Audio dosya yükleme - otomatik duration hesaplama
     */
    public function updatedAudioFile()
    {
        try {
            // Audio yükleme başladı event'i
            Log::info('🎵 [SONG] Dispatching media-upload-started event');
            $this->dispatch('media-upload-started');

            $this->validate([
                'audioFile' => 'file|mimes:mp3,wav,flac,m4a,ogg|max:102400', // 100MB
            ]);
            // Eski dosyayı sil (varsa)
            if (!empty($this->inputs['file_path'])) {
                $oldFilePath = storage_path('app/public/muzibu/songs/' . $this->inputs['file_path']);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                    Log::info('🗑️ Eski audio dosyası silindi', ['file' => $this->inputs['file_path']]);
                }
            }

            // Dosyayı storage/muzibu/songs/ klasörüne kaydet
            $filename = uniqid('song_') . '.' . $this->audioFile->getClientOriginalExtension();
            $path = $this->audioFile->storeAs('muzibu/songs', $filename, 'public');

            // File path'i inputs'a ekle
            $this->inputs['file_path'] = $filename;

            // Duration ve Title hesapla (getID3 kütüphanesi ile)
            $fullPath = storage_path('app/public/' . $path);
            $metadata = $this->extractAudioMetadata($fullPath);

            $defaultLocale = \get_tenant_default_locale();

            // Duration'u kaydet
            if (isset($metadata['duration'])) {
                $this->inputs['duration'] = $metadata['duration'];
            } else {
                $this->inputs['duration'] = 0;
            }

            // Title'ı kaydet (SADECE ID3'te varsa - kullanıcının girdiğini korur)
            if (isset($metadata['title']) && !empty(trim($metadata['title']))) {
                $this->multiLangInputs[$defaultLocale]['title'] = $metadata['title'];
                Log::info('📝 ID3 tag\'inden title otomatik dolduruldu', [
                    'title' => $metadata['title'],
                    'locale' => $defaultLocale
                ]);
            } else {
                Log::info('⚠️ ID3 tag\'inde title bulunamadı, kullanıcının girdiği değer korundu', [
                    'current_title' => $this->multiLangInputs[$defaultLocale]['title'] ?? 'boş'
                ]);
            }

            Log::info('✅ Audio dosyası yüklendi ve metadata çıkarıldı', [
                'filename' => $filename,
                'duration' => $this->inputs['duration'],
                'formatted' => gmdate('i:s', $this->inputs['duration']),
                'title' => $metadata['title'] ?? 'yok'
            ]);

            // 🎵 HLS Conversion arka planda yapılacak (save sonrası job ile)
            Log::info('📌 HLS conversion job\'a alınacak (save sonrası)');

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Şarkı dosyası yüklendi! Süre: ' . gmdate('i:s', $this->inputs['duration']) . ' (HLS arka planda hazırlanacak)',
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
        } finally {
            // Her durumda (başarılı, hata, validation fail) kilidi aç
            Log::info('🎵 [SONG] Dispatching media-upload-completed event');
            $this->dispatch('media-upload-completed');
        }
    }

    /**
     * Audio dosyasından metadata çıkar (duration, title, artist, album vb.)
     *
     * @return array ['duration' => int, 'title' => string, 'artist' => string, 'album' => string]
     */
    protected function extractAudioMetadata(string $filePath): array
    {
        $metadata = [];

        try {
            // getID3 kütüphanesi ile metadata çıkar
            if (class_exists('\getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);

                // Duration
                if (isset($fileInfo['playtime_seconds'])) {
                    $metadata['duration'] = (int) round($fileInfo['playtime_seconds']);
                }

                // Title (ID3v2 öncelikli, sonra ID3v1)
                if (isset($fileInfo['tags']['id3v2']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v2']['title'][0]);
                } elseif (isset($fileInfo['tags']['id3v1']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v1']['title'][0]);
                }

                // Artist (gelecekte kullanılabilir)
                if (isset($fileInfo['tags']['id3v2']['artist'][0])) {
                    $metadata['artist'] = trim($fileInfo['tags']['id3v2']['artist'][0]);
                } elseif (isset($fileInfo['tags']['id3v1']['artist'][0])) {
                    $metadata['artist'] = trim($fileInfo['tags']['id3v1']['artist'][0]);
                }

                // Album (gelecekte kullanılabilir)
                if (isset($fileInfo['tags']['id3v2']['album'][0])) {
                    $metadata['album'] = trim($fileInfo['tags']['id3v2']['album'][0]);
                } elseif (isset($fileInfo['tags']['id3v1']['album'][0])) {
                    $metadata['album'] = trim($fileInfo['tags']['id3v1']['album'][0]);
                }
            }

            // getID3 yoksa veya duration bulunamadıysa FFmpeg/FFprobe dene
            if (empty($metadata['duration']) && function_exists('shell_exec')) {
                $ffprobeCmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath);
                $duration = shell_exec($ffprobeCmd);

                if ($duration && is_numeric(trim($duration))) {
                    $metadata['duration'] = (int) round(floatval(trim($duration)));
                }
            }

            return $metadata;

        } catch (\Exception $e) {
            Log::error('❌ Metadata çıkarma hatası', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);
            return [];
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
        $this->currentLanguage = \get_tenant_default_locale();

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
        return \get_tenant_default_locale();
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

            // Dosya değişti mi kontrol et
            $fileChanged = isset($data['file_path']) && $song->file_path !== $data['file_path'];

            if ($data == $currentData) {
                $toast = [
                    'title' => __('admin.success'),
                    'message' => __('muzibu::admin.song_updated'),
                    'type' => 'success'
                ];
            } else {
                $song->update($data);
                log_activity($song, 'güncellendi');

                // 🎵 Dosya değiştiyse HLS conversion job'u kuyruğa ekle
                if ($fileChanged && $song->file_path) {
                    \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);
                    Log::info('🔄 Dosya değişti, HLS conversion job\'a alındı', ['song_id' => $song->song_id]);
                }

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

            // 🎵 HLS conversion job'u kuyruğa ekle
            if ($song->file_path) {
                \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);
                Log::info('🎵 Yeni şarkı, HLS conversion job\'a alındı', ['song_id' => $song->song_id]);
            }

            // 🎨 MUZIBU: Media yoksa otomatik görsel üret (Universal Helper - Queue)
            if (!$song->media_id) {
                \muzibu_generate_ai_cover($song, $song->title, 'song');
            }

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

            if ($resetForm) {
                // Kaydet ve Yeni Ekle: Formu resetle, aynı sayfada kal
                $this->songId = null;
                $this->reset(['inputs', 'multiLangInputs']);
                $this->inputs = ['is_active' => true];
                $this->currentLanguage = \get_tenant_default_locale();
                $this->initializeEmptyInputs();

                Log::info('✅ Form resetlendi - Yeni kayıt için hazır', [
                    'previous_song_id' => $song->song_id
                ]);
            } else {
                // Normal kaydet: Düzenleme sayfasına yönlendir
                session()->flash('toast', $toast);
                return redirect()->route('admin.muzibu.song.manage', ['id' => $song->song_id]);
            }
        }

        Log::info('✅ Save method başarıyla tamamlandı', [
            'songId' => $this->songId
        ]);
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
