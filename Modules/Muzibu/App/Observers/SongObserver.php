<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Sector;

/**
 * SongObserver
 *
 * Handles cache count updates when songs are created, updated, or deleted.
 * Updates:
 * - Album: songs_count, total_duration
 * - Genre: songs_count, total_duration
 * - Artist: songs_count, total_duration (via Album)
 */
class SongObserver
{
    /**
     * Handle the Song "created" event.
     */
    public function created(Song $song): void
    {
        if (!$song->is_active) {
            return; // Inactive songs don't count
        }

        $this->incrementCounts($song);

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($song, 'oluşturuldu');
        }
    }

    /**
     * Handle the Song "updated" event.
     */
    public function updated(Song $song): void
    {
        // Check if is_active changed
        $wasActive = $song->getOriginal('is_active');
        $isActive = $song->is_active;

        if ($wasActive && !$isActive) {
            // Song was deactivated
            $this->decrementCounts($song);
            $this->recalculatePlaylistAndSectorCounts($song); // Playlist ve Sector count'larını güncelle
        } elseif (!$wasActive && $isActive) {
            // Song was activated
            $this->incrementCounts($song);
            $this->recalculatePlaylistAndSectorCounts($song); // Playlist ve Sector count'larını güncelle
        } elseif ($isActive) {
            // Song is still active - check if duration changed
            $oldDuration = (int) $song->getOriginal('duration');
            $newDuration = (int) $song->duration;

            if ($oldDuration !== $newDuration) {
                $diff = $newDuration - $oldDuration;
                $this->updateDuration($song, $diff);
            }

            // Check if album_id changed
            $oldAlbumId = $song->getOriginal('album_id');
            $newAlbumId = $song->album_id;

            if ($oldAlbumId !== $newAlbumId) {
                // Moved to different album
                $this->handleAlbumChange($song, $oldAlbumId, $newAlbumId);
            }

            // Check if genre_id changed
            $oldGenreId = $song->getOriginal('genre_id');
            $newGenreId = $song->genre_id;

            if ($oldGenreId !== $newGenreId) {
                $this->handleGenreChange($song, $oldGenreId, $newGenreId);
            }
        }

        // 🎨 Title değiştiyse otomatik yeni görsel üret
        // ⚠️ FIX: Title translatable JSON olduğu için RAW değerleri karşılaştır
        $oldTitleRaw = $song->getOriginal('title'); // JSON string veya array
        $newTitleRaw = $song->getAttributes()['title'] ?? null; // Raw attribute

        // JSON string ise decode et, array ise olduğu gibi kullan
        $oldTitleArray = is_string($oldTitleRaw) ? json_decode($oldTitleRaw, true) : $oldTitleRaw;
        $newTitleArray = is_string($newTitleRaw) ? json_decode($newTitleRaw, true) : $newTitleRaw;

        // Gerçekten title içeriği değişmiş mi kontrol et
        $titleActuallyChanged = json_encode($oldTitleArray) !== json_encode($newTitleArray);

        if ($titleActuallyChanged && !empty($newTitleRaw)) {
            // Parsed title al (locale-aware)
            $newTitle = $song->getTranslated('title', 'tr') ?: $song->title;

            // color_hash'i yeni title'a göre güncelle
            $newColorHash = Song::generateColorHash($newTitle);
            if ($song->color_hash !== $newColorHash) {
                $song->withoutEvents(function () use ($song, $newColorHash) {
                    $song->update(['color_hash' => $newColorHash]);
                });
            }

            // 🎨 Yeni AI görsel üret - SADECE görsel yoksa
            // Zaten görseli varsa gereksiz API çağrısı yapma
            if (!$song->hasMedia('hero')) {
                \muzibu_generate_ai_cover($song, $newTitle, 'song');
            }
        }

        // Activity log - değişiklikleri kaydet
        if (function_exists('log_activity')) {
            $changes = $song->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $song->getOriginal('title');
                }

                log_activity($song, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }
    }

    /**
     * Handle the Song "deleted" event.
     */
    public function deleted(Song $song): void
    {
        if ($song->is_active) {
            $this->decrementCounts($song);
            $this->recalculatePlaylistAndSectorCounts($song); // Playlist ve Sector count'larını güncelle
        }

        // Activity log - silinen kaydın başlığını sakla
        if (function_exists('log_activity')) {
            log_activity($song, 'silindi', null, $song->title);
        }
    }

    /**
     * Handle the Song "restored" event (from soft delete).
     */
    public function restored(Song $song): void
    {
        if ($song->is_active) {
            $this->incrementCounts($song);
            $this->recalculatePlaylistAndSectorCounts($song); // Playlist ve Sector count'larını güncelle
        }

        // Activity log
        if (function_exists('log_activity')) {
            log_activity($song, 'geri yüklendi');
        }
    }

    /**
     * Handle the Song "force deleted" event.
     */
    public function forceDeleted(Song $song): void
    {
        if ($song->is_active) {
            $this->decrementCounts($song);
            $this->recalculatePlaylistAndSectorCounts($song); // Playlist ve Sector count'larını güncelle
        }

        // Activity log - kalıcı silme
        if (function_exists('log_activity')) {
            log_activity($song, 'kalıcı silindi', null, $song->title);
        }
    }

    /**
     * Increment counts for Album, Genre, Artist
     */
    protected function incrementCounts(Song $song): void
    {
        $duration = (int) $song->duration;

        // Album
        if ($song->album_id) {
            $album = Album::find($song->album_id);
            if ($album) {
                $album->incrementCachedCount('songs_count');
                $album->incrementCachedCount('total_duration', $duration);

                // Artist (via Album)
                if ($album->artist_id) {
                    $artist = Artist::find($album->artist_id);
                    if ($artist) {
                        $artist->incrementCachedCount('songs_count');
                        $artist->incrementCachedCount('total_duration', $duration);
                    }
                }
            }
        }

        // Genre
        if ($song->genre_id) {
            $genre = Genre::find($song->genre_id);
            if ($genre) {
                $genre->incrementCachedCount('songs_count');
                $genre->incrementCachedCount('total_duration', $duration);
            }
        }
    }

    /**
     * Decrement counts for Album, Genre, Artist
     */
    protected function decrementCounts(Song $song): void
    {
        $duration = (int) $song->duration;

        // Album
        if ($song->album_id) {
            $album = Album::find($song->album_id);
            if ($album) {
                $album->decrementCachedCount('songs_count');
                $album->decrementCachedCount('total_duration', $duration);

                // Artist (via Album)
                if ($album->artist_id) {
                    $artist = Artist::find($album->artist_id);
                    if ($artist) {
                        $artist->decrementCachedCount('songs_count');
                        $artist->decrementCachedCount('total_duration', $duration);
                    }
                }
            }
        }

        // Genre
        if ($song->genre_id) {
            $genre = Genre::find($song->genre_id);
            if ($genre) {
                $genre->decrementCachedCount('songs_count');
                $genre->decrementCachedCount('total_duration', $duration);
            }
        }
    }

    /**
     * Update duration for Album, Genre, Artist
     */
    protected function updateDuration(Song $song, int $diff): void
    {
        // Album
        if ($song->album_id) {
            $album = Album::find($song->album_id);
            if ($album) {
                if ($diff > 0) {
                    $album->incrementCachedCount('total_duration', $diff);
                } else {
                    $album->decrementCachedCount('total_duration', abs($diff));
                }

                // Artist (via Album)
                if ($album->artist_id) {
                    $artist = Artist::find($album->artist_id);
                    if ($artist) {
                        if ($diff > 0) {
                            $artist->incrementCachedCount('total_duration', $diff);
                        } else {
                            $artist->decrementCachedCount('total_duration', abs($diff));
                        }
                    }
                }
            }
        }

        // Genre
        if ($song->genre_id) {
            $genre = Genre::find($song->genre_id);
            if ($genre) {
                if ($diff > 0) {
                    $genre->incrementCachedCount('total_duration', $diff);
                } else {
                    $genre->decrementCachedCount('total_duration', abs($diff));
                }
            }
        }
    }

    /**
     * Handle album change - decrement old, increment new
     */
    protected function handleAlbumChange(Song $song, ?int $oldAlbumId, ?int $newAlbumId): void
    {
        $duration = (int) $song->duration;

        // Decrement old album
        if ($oldAlbumId) {
            $oldAlbum = Album::find($oldAlbumId);
            if ($oldAlbum) {
                $oldAlbum->decrementCachedCount('songs_count');
                $oldAlbum->decrementCachedCount('total_duration', $duration);

                // Old artist
                if ($oldAlbum->artist_id) {
                    $oldArtist = Artist::find($oldAlbum->artist_id);
                    if ($oldArtist) {
                        $oldArtist->decrementCachedCount('songs_count');
                        $oldArtist->decrementCachedCount('total_duration', $duration);
                    }
                }
            }
        }

        // Increment new album
        if ($newAlbumId) {
            $newAlbum = Album::find($newAlbumId);
            if ($newAlbum) {
                $newAlbum->incrementCachedCount('songs_count');
                $newAlbum->incrementCachedCount('total_duration', $duration);

                // New artist
                if ($newAlbum->artist_id) {
                    $newArtist = Artist::find($newAlbum->artist_id);
                    if ($newArtist) {
                        $newArtist->incrementCachedCount('songs_count');
                        $newArtist->incrementCachedCount('total_duration', $duration);
                    }
                }
            }
        }
    }

    /**
     * Handle genre change - decrement old, increment new
     */
    protected function handleGenreChange(Song $song, ?int $oldGenreId, ?int $newGenreId): void
    {
        $duration = (int) $song->duration;

        // Decrement old genre
        if ($oldGenreId) {
            $oldGenre = Genre::find($oldGenreId);
            if ($oldGenre) {
                $oldGenre->decrementCachedCount('songs_count');
                $oldGenre->decrementCachedCount('total_duration', $duration);
            }
        }

        // Increment new genre
        if ($newGenreId) {
            $newGenre = Genre::find($newGenreId);
            if ($newGenre) {
                $newGenre->incrementCachedCount('songs_count');
                $newGenre->incrementCachedCount('total_duration', $duration);
            }
        }
    }

    /**
     * Recalculate Playlist and Sector counts when song is_active changes
     *
     * Song is_active değiştiğinde veya silindiğinde, ilgili Playlist ve Sector count'larını
     * recalculate eder. PlaylistSongObserver sadece attach/detach'i yakalar, is_active değişikliğini
     * yakalayamaz, bu yüzden burada manuel recalculate ediyoruz.
     */
    protected function recalculatePlaylistAndSectorCounts(Song $song): void
    {
        // İlgili Playlist'leri bul ve recalculate et
        $playlistIds = \DB::table('muzibu_playlist_song')
            ->where('song_id', $song->song_id)
            ->pluck('playlist_id')
            ->unique()
            ->toArray();

        if (empty($playlistIds)) {
            return;
        }

        foreach ($playlistIds as $playlistId) {
            $playlist = Playlist::find($playlistId);
            if ($playlist) {
                $playlist->recalculateCachedCounts();
            }
        }

        // İlgili Sector'leri bul ve recalculate et
        // Sector, Playlist üzerinden bağlı (muzibu_playlistables tablosu)
        $sectorIds = \DB::table('muzibu_playlistables')
            ->whereIn('playlist_id', $playlistIds)
            ->where('playlistable_type', Sector::class)
            ->pluck('playlistable_id')
            ->unique()
            ->toArray();

        foreach ($sectorIds as $sectorId) {
            $sector = Sector::find($sectorId);
            if ($sector) {
                $sector->recalculateCachedCounts();
            }
        }
    }
}
