<?php

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Muzibu\App\Models\Song;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class SongBulkConvertComponent extends Component
{
    public $selectedSongs = [];
    public $selectAll = false;
    public $converting = false;
    public $conversionResults = [];
    public $searchTerm = '';

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    #[Computed]
    public function songsWithoutHls()
    {
        $query = Song::query()
            ->whereNull('hls_path')
            ->whereNotNull('file_path')
            ->where('is_active', true)
            ->with(['album', 'genre']);

        // Search filter
        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('title->tr', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('title->en', 'like', '%' . $this->searchTerm . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    #[Computed]
    public function totalSongsWithoutHls()
    {
        return Song::whereNull('hls_path')
            ->whereNotNull('file_path')
            ->where('is_active', true)
            ->count();
    }

    #[Computed]
    public function totalSongsWithHls()
    {
        return Song::whereNotNull('hls_path')
            ->whereNotNull('file_path')
            ->where('is_active', true)
            ->count();
    }

    public function boot()
    {
        view()->share('pretitle', __('muzibu::admin.bulk_convert.pretitle'));
        view()->share('title', __('muzibu::admin.bulk_convert.title'));
    }

    public function mount()
    {
        // Reset state
        $this->selectedSongs = [];
        $this->selectAll = false;
        $this->conversionResults = [];
    }

    /**
     * Toggle select all
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all songs
            $this->selectedSongs = $this->songsWithoutHls->pluck('song_id')->toArray();
        } else {
            // Deselect all
            $this->selectedSongs = [];
        }
    }

    /**
     * Toggle individual song selection
     */
    public function toggleSong($songId)
    {
        if (in_array($songId, $this->selectedSongs)) {
            $this->selectedSongs = array_diff($this->selectedSongs, [$songId]);
        } else {
            $this->selectedSongs[] = $songId;
        }

        // Update select all checkbox
        $this->selectAll = count($this->selectedSongs) === $this->songsWithoutHls->count();
    }

    /**
     * Start bulk HLS conversion
     */
    public function startConversion()
    {
        if (empty($this->selectedSongs)) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('muzibu::admin.bulk_convert.no_songs_selected'),
                'type' => 'error'
            ]);
            return;
        }

        $this->converting = true;
        $this->conversionResults = [];

        $successCount = 0;
        $errorCount = 0;

        foreach ($this->selectedSongs as $songId) {
            try {
                $song = Song::find($songId);

                if (!$song) {
                    $this->conversionResults[$songId] = [
                        'status' => 'error',
                        'message' => 'Şarkı bulunamadı'
                    ];
                    $errorCount++;
                    continue;
                }

                // Check if MP3 file exists
                $mp3Path = storage_path('../tenant' . tenant()->id . '/app/public/muzibu/songs/' . $song->file_path);
                if (!file_exists($mp3Path)) {
                    $this->conversionResults[$songId] = [
                        'status' => 'error',
                        'message' => 'MP3 dosyası bulunamadı'
                    ];
                    $errorCount++;
                    continue;
                }

                // Dispatch HLS conversion job
                \Modules\Muzibu\App\Jobs\ConvertToHLSJob::dispatch($song);

                $this->conversionResults[$songId] = [
                    'status' => 'success',
                    'message' => 'HLS conversion kuyruğa eklendi'
                ];
                $successCount++;

                Log::info('✅ Bulk HLS conversion - Job dispatched', [
                    'song_id' => $songId,
                    'title' => $song->getTranslated('title', 'tr')
                ]);

            } catch (\Exception $e) {
                $this->conversionResults[$songId] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
                $errorCount++;

                Log::error('❌ Bulk HLS conversion - Error', [
                    'song_id' => $songId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->converting = false;

        // Show result notification
        if ($successCount > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $successCount . ' şarkı HLS dönüşümü için kuyruğa eklendi',
                'type' => 'success'
            ]);
        }

        if ($errorCount > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => $errorCount . ' şarkı için hata oluştu',
                'type' => 'warning'
            ]);
        }

        // Clear selection
        $this->selectedSongs = [];
        $this->selectAll = false;

        // Refresh the list
        $this->dispatch('refreshComponent');

        Log::info('📊 Bulk HLS conversion completed', [
            'success' => $successCount,
            'failed' => $errorCount
        ]);
    }

    /**
     * Select songs by filter (e.g., all songs from a specific album or genre)
     */
    public function selectByAlbum($albumId)
    {
        $songs = Song::whereNull('hls_path')
            ->where('album_id', $albumId)
            ->whereNotNull('file_path')
            ->where('is_active', true)
            ->pluck('song_id')
            ->toArray();

        $this->selectedSongs = array_unique(array_merge($this->selectedSongs, $songs));
    }

    public function selectByGenre($genreId)
    {
        $songs = Song::whereNull('hls_path')
            ->where('genre_id', $genreId)
            ->whereNotNull('file_path')
            ->where('is_active', true)
            ->pluck('song_id')
            ->toArray();

        $this->selectedSongs = array_unique(array_merge($this->selectedSongs, $songs));
    }

    public function render()
    {
        return view('muzibu::admin.livewire.song-bulk-convert-component');
    }
}
