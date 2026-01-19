<?php

declare(strict_types=1);

namespace Modules\Muzibu\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\Muzibu\App\Models\{Playlist, Song};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaylistSongsManageComponent extends Component
{
    public int $playlistId;
    public string $search = '';

    public function mount(int $playlistId): void
    {
        $this->playlistId = $playlistId;

        // Playlist var mı kontrolü (ilk yüklemede)
        Playlist::findOrFail($playlistId);
    }

    /**
     * Playlist computed property - her request'te yüklenir
     */
    #[Computed]
    public function playlist(): Playlist
    {
        return Playlist::findOrFail($this->playlistId);
    }

    /**
     * Arama sonuçları - Sol liste (Tüm şarkılar)
     */
    #[Computed]
    public function availableSongs()
    {
        $query = Song::active()
            ->with(['album.artist', 'genre', 'coverMedia'])
            ->orderBy('title');

        // Arama filtresi (title, artist, album, genre, lyrics)
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                // Şarkı adı (JSON translatable)
                $q->where('title', 'like', $searchTerm)
                  // Şarkı sözleri (JSON translatable)
                  ->orWhere('lyrics', 'like', $searchTerm)
                  // Sanatçı adı
                  ->orWhereHas('album.artist', function ($artistQuery) use ($searchTerm) {
                      $artistQuery->where('title', 'like', $searchTerm);
                  })
                  // Albüm adı
                  ->orWhereHas('album', function ($albumQuery) use ($searchTerm) {
                      $albumQuery->where('title', 'like', $searchTerm);
                  })
                  // Tür (Genre) adı
                  ->orWhereHas('genre', function ($genreQuery) use ($searchTerm) {
                      $genreQuery->where('title', 'like', $searchTerm);
                  });
            });
        }

        return $query->limit(100)->get();
    }

    /**
     * Playlist şarkıları - Sağ liste (position order)
     */
    #[Computed]
    public function playlistSongs()
    {
        return $this->playlist
            ->songs()
            ->with(['album.artist', 'genre', 'coverMedia'])
            ->orderBy('muzibu_playlist_song.position')
            ->get();
    }

    /**
     * Seçili şarkı ID'leri (duplicate kontrolü için)
     */
    #[Computed]
    public function selectedSongIds()
    {
        return $this->playlistSongs->pluck('song_id')->toArray();
    }

    /**
     * Şarkı ekleme
     */
    public function addSong(int $songId): void
    {
        try {
            // 1. Song var mı ve aktif mi?
            $song = Song::active()->find($songId);
            if (!$song) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'Hata',
                    'message' => 'Şarkı bulunamadı veya aktif değil'
                ]);
                return;
            }

            // 2. Duplicate kontrolü
            $exists = $this->playlist->songs()->where('song_id', $songId)->exists();
            if ($exists) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'title' => 'Uyarı',
                    'message' => 'Bu şarkı zaten playlist\'te mevcut'
                ]);
                return;
            }

            // 3. Position hesapla (max + 1)
            $maxPosition = $this->playlist->songs()->max('muzibu_playlist_song.position') ?? -1;
            $newPosition = $maxPosition + 1;

            // 4. Pivot table'a ekle (cache count'ları da güncelle)
            $this->playlist->attachSongWithCache($songId, ['position' => $newPosition]);

            // 5. Log
            Log::info('Muzibu: Playlist\'e şarkı eklendi', [
                'playlist_id' => $this->playlistId,
                'song_id' => $songId,
                'position' => $newPosition,
                'user_id' => auth()->id()
            ]);

            // 6. Success feedback
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Şarkı playlist\'e eklendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şarkı ekleme hatası', [
                'playlist_id' => $this->playlistId,
                'song_id' => $songId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Şarkı eklenirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Şarkı çıkarma
     */
    public function removeSong(int $songId): void
    {
        try {
            // 1. Pivot'tan sil (cache count'ları da güncelle)
            $this->playlist->detachSongWithCache($songId);

            // 2. Sıralamayı yeniden düzenle (gap kalmasın)
            $this->reorderSequentially();

            // 3. Log
            Log::info('Muzibu: Playlist\'ten şarkı çıkarıldı', [
                'playlist_id' => $this->playlistId,
                'song_id' => $songId,
                'user_id' => auth()->id()
            ]);

            // 4. Success feedback
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Şarkı playlist\'ten çıkarıldı'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Şarkı çıkarma hatası', [
                'playlist_id' => $this->playlistId,
                'song_id' => $songId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Şarkı çıkarılırken bir hata oluştu'
            ]);
        }
    }

    /**
     * Sıralama güncelleme (Drag & Drop)
     */
    public function reorderSongs(array $newOrder): void
    {
        try {
            DB::transaction(function () use ($newOrder) {
                foreach ($newOrder as $songId => $position) {
                    DB::table('muzibu_playlist_song')
                        ->where('playlist_id', $this->playlistId)
                        ->where('song_id', $songId)
                        ->update(['position' => $position]);
                }
            });

            // Log
            Log::info('Muzibu: Playlist sıralama güncellendi', [
                'playlist_id' => $this->playlistId,
                'song_count' => count($newOrder),
                'user_id' => auth()->id()
            ]);

            // Success feedback
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'Başarılı',
                'message' => 'Sıralama güncellendi'
            ]);

        } catch (\Exception $e) {
            Log::error('Muzibu: Sıralama güncelleme hatası', [
                'playlist_id' => $this->playlistId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'Hata',
                'message' => 'Sıralama güncellenirken bir hata oluştu'
            ]);
        }
    }

    /**
     * Sıralamayı düzelt (gap kalmasın)
     */
    private function reorderSequentially(): void
    {
        $songs = $this->playlist->songs()->orderBy('muzibu_playlist_song.position')->get();

        DB::transaction(function () use ($songs) {
            foreach ($songs as $index => $song) {
                DB::table('muzibu_playlist_song')
                    ->where('playlist_id', $this->playlistId)
                    ->where('song_id', $song->song_id)
                    ->update(['position' => $index]);
            }
        });
    }

    public function render()
    {
        return view('muzibu::admin.livewire.playlist-songs-manage-component', [
            'playlist' => $this->playlist,
        ]);
    }
}
