<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Genre;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use App\Helpers\SlugHelper;
use Illuminate\Support\Facades\Queue;

#[Layout('admin.layout')]
class AlbumBulkUploadComponent extends Component
{
    use WithFileUploads;

    public $albumId;
    public $audioFiles = [];
    public $uploadedFiles = [];
    public $bulkGenreId = null;
    public $enableIndividualGenre = false;
    public $isUploading = false;
    public $processingFiles = [];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'fileProcessed' => 'handleFileProcessed',
    ];

    #[Computed]
    public function album()
    {
        if (!$this->albumId) {
            return null;
        }
        return Album::query()->find($this->albumId);
    }

    #[Computed]
    public function activeGenres()
    {
        return Genre::query()
            ->active()
            ->orderBy('title->tr', 'asc')
            ->get();
    }

    #[Computed]
    public function totalDuration()
    {
        return collect($this->uploadedFiles)->sum('duration');
    }

    public function boot()
    {
        view()->share('pretitle', __('muzibu::admin.bulk_upload.pretitle'));
        view()->share('title', __('muzibu::admin.bulk_upload.title'));
    }

    public function mount($id)
    {
        $this->albumId = $id;

        if (!$this->album) {
            session()->flash('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.album_not_found'),
                'type' => 'error'
            ]);
            return redirect()->route('admin.muzibu.album.index');
        }
    }

    /**
     * "Ayrı ayrı tür seç" checkbox değiştiğinde tetiklenir
     * Açıldığında tüm dosyalara toplu türü ata
     */
    public function updatedEnableIndividualGenre($value)
    {
        if ($value && $this->bulkGenreId) {
            // Checkbox açıldığında tüm dosyalara toplu türü ata
            foreach ($this->uploadedFiles as $index => $file) {
                if ($file['status'] === 'pending') {
                    $this->uploadedFiles[$index]['genre_id'] = $this->bulkGenreId;
                }
            }
        }
    }

    /**
     * Audio dosyaları yüklendiğinde tetiklenir
     */
    public function updatedAudioFiles()
    {
        $this->validate([
            'audioFiles.*' => 'file|mimes:mp3,wav,flac,m4a,ogg|max:102400', // 100MB
        ]);

        $this->isUploading = true;

        foreach ($this->audioFiles as $file) {
            $tempPath = $file->getRealPath();
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Metadata çıkar
            $metadata = $this->extractAudioMetadata($tempPath);

            // Dosya adından title oluştur (uzantısız)
            $titleFromFilename = pathinfo($originalName, PATHINFO_FILENAME);
            $title = $metadata['title'] ?? $titleFromFilename;

            // Unique ID oluştur
            $fileId = uniqid('file_');

            // Dosyayı geçici olarak kaydet
            $filename = uniqid('bulk_') . '.' . $extension;
            $path = $file->storeAs('muzibu/temp', $filename, 'public');

            $this->uploadedFiles[] = [
                'id' => $fileId,
                'original_name' => $originalName,
                'temp_path' => $path,
                'filename' => $filename,
                'title' => $title,
                'duration' => $metadata['duration'] ?? 0,
                'genre_id' => null, // Individual genre
                'status' => 'pending', // pending, processing, completed, failed
                'error' => null,
            ];

            Log::info('📁 Bulk upload - Dosya eklendi', [
                'file_id' => $fileId,
                'original_name' => $originalName,
                'title' => $title,
                'duration' => $metadata['duration'] ?? 0
            ]);
        }

        $this->isUploading = false;
        $this->audioFiles = []; // Input'u temizle

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => count($this->uploadedFiles) . ' ' . __('muzibu::admin.bulk_upload.files_added'),
            'type' => 'success'
        ]);
    }

    /**
     * Dosya title'ını güncelle
     */
    public function updateTitle($fileId, $newTitle)
    {
        foreach ($this->uploadedFiles as $index => $file) {
            if ($file['id'] === $fileId) {
                $this->uploadedFiles[$index]['title'] = $newTitle;
                break;
            }
        }
    }

    /**
     * Dosya için individual genre seç
     */
    public function updateFileGenre($fileId, $genreId)
    {
        foreach ($this->uploadedFiles as $index => $file) {
            if ($file['id'] === $fileId) {
                $this->uploadedFiles[$index]['genre_id'] = $genreId ?: null;
                break;
            }
        }
    }

    /**
     * Dosyayı listeden kaldır
     */
    public function removeFile($fileId)
    {
        foreach ($this->uploadedFiles as $index => $file) {
            if ($file['id'] === $fileId) {
                // Geçici dosyayı sil
                $tempPath = storage_path('app/public/' . $file['temp_path']);
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                unset($this->uploadedFiles[$index]);
                $this->uploadedFiles = array_values($this->uploadedFiles);

                Log::info('🗑️ Bulk upload - Dosya kaldırıldı', ['file_id' => $fileId]);
                break;
            }
        }
    }

    /**
     * Tüm dosyaları temizle
     */
    public function clearAll()
    {
        foreach ($this->uploadedFiles as $file) {
            $tempPath = storage_path('app/public/' . $file['temp_path']);
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
        }

        $this->uploadedFiles = [];
        $this->bulkGenreId = null;
        $this->enableIndividualGenre = false;

        Log::info('🗑️ Bulk upload - Tüm dosyalar temizlendi');
    }

    /**
     * Toplu yükleme işlemini başlat
     */
    public function startUpload()
    {
        if (empty($this->uploadedFiles)) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.bulk_upload.no_files'),
                'type' => 'error'
            ]);
            return;
        }

        // Genre kontrolü
        if (!$this->bulkGenreId && !$this->enableIndividualGenre) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.bulk_upload.select_genre'),
                'type' => 'error'
            ]);
            return;
        }

        $defaultLocale = get_tenant_default_locale();
        $successCount = 0;
        $errorCount = 0;

        foreach ($this->uploadedFiles as $index => $file) {
            try {
                // Genre belirle
                $genreId = $this->enableIndividualGenre && $file['genre_id']
                    ? $file['genre_id']
                    : $this->bulkGenreId;

                if (!$genreId) {
                    $this->uploadedFiles[$index]['status'] = 'failed';
                    $this->uploadedFiles[$index]['error'] = __('muzibu::admin.bulk_upload.genre_required');
                    $errorCount++;
                    continue;
                }

                $this->uploadedFiles[$index]['status'] = 'processing';

                // Dosyayı kalıcı konuma taşı
                $tempPath = storage_path('app/public/' . $file['temp_path']);
                $permanentFilename = uniqid('song_') . '.' . pathinfo($file['filename'], PATHINFO_EXTENSION);
                $permanentPath = storage_path('app/public/muzibu/songs/' . $permanentFilename);

                // Klasör yoksa oluştur
                $songsDir = storage_path('app/public/muzibu/songs');
                if (!file_exists($songsDir)) {
                    mkdir($songsDir, 0755, true);
                }

                rename($tempPath, $permanentPath);

                // Slug oluştur
                $slug = SlugHelper::generateFromTitle(
                    Song::class,
                    $file['title'],
                    $defaultLocale,
                    'slug',
                    'song_id',
                    null
                );

                // Song kaydı oluştur
                $song = Song::create([
                    'title' => [$defaultLocale => $file['title']],
                    'slug' => [$defaultLocale => $slug],
                    'album_id' => $this->albumId,
                    'genre_id' => $genreId,
                    'duration' => $file['duration'],
                    'file_path' => $permanentFilename,
                    'is_active' => true,
                ]);

                Log::info('✅ Bulk upload - Song oluşturuldu', [
                    'song_id' => $song->song_id,
                    'title' => $file['title']
                ]);

                // HLS conversion job'u kuyruğa ekle
                \Modules\Muzibu\App\Jobs\ProcessBulkSongHLSJob::dispatch($song->song_id);

                $this->uploadedFiles[$index]['status'] = 'completed';
                $this->uploadedFiles[$index]['song_id'] = $song->song_id;
                $successCount++;

            } catch (\Exception $e) {
                Log::error('❌ Bulk upload - Hata', [
                    'file' => $file['original_name'],
                    'error' => $e->getMessage()
                ]);

                $this->uploadedFiles[$index]['status'] = 'failed';
                $this->uploadedFiles[$index]['error'] = $e->getMessage();
                $errorCount++;
            }
        }

        // Sonuç bildirimi
        if ($successCount > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $successCount . ' ' . __('muzibu::admin.bulk_upload.songs_created'),
                'type' => 'success'
            ]);
        }

        if ($errorCount > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => $errorCount . ' ' . __('muzibu::admin.bulk_upload.songs_failed'),
                'type' => 'warning'
            ]);
        }

        Log::info('📊 Bulk upload tamamlandı', [
            'album_id' => $this->albumId,
            'success' => $successCount,
            'failed' => $errorCount
        ]);
    }

    /**
     * Audio dosyasından metadata çıkar
     */
    protected function extractAudioMetadata(string $filePath): array
    {
        $metadata = [];

        try {
            if (class_exists('\getID3')) {
                $getID3 = new \getID3();
                $fileInfo = $getID3->analyze($filePath);

                // Duration
                if (isset($fileInfo['playtime_seconds'])) {
                    $metadata['duration'] = (int) round($fileInfo['playtime_seconds']);
                }

                // Title
                if (isset($fileInfo['tags']['id3v2']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v2']['title'][0]);
                } elseif (isset($fileInfo['tags']['id3v1']['title'][0])) {
                    $metadata['title'] = trim($fileInfo['tags']['id3v1']['title'][0]);
                }
            }

            // FFprobe fallback
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

    /**
     * Süreyi formatla (saniye -> MM:SS)
     */
    public function formatDuration($seconds): string
    {
        if (!$seconds) return '00:00';
        return gmdate('i:s', $seconds);
    }

    public function render()
    {
        return view('muzibu::admin.livewire.album-bulk-upload-component');
    }
}
