<?php

namespace Modules\Muzibu\App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Modules\Muzibu\App\Models\Playlist;

/**
 * HasPlaylistDistribution Trait
 *
 * Playlist'lerin dağıtılabileceği entity'ler için (Sector, Radio, Corporate, Mood vb.)
 * Bu trait'i kullanan modeller otomatik olarak playlists() ilişkisine sahip olur.
 *
 * @example
 * class Sector extends Model {
 *     use HasPlaylistDistribution;
 *     // Artık $sector->playlists() kullanılabilir
 * }
 */
trait HasPlaylistDistribution
{
    /**
     * Bu entity'e dağıtılmış playlist'ler
     */
    public function playlists(): MorphToMany
    {
        return $this->morphToMany(
            Playlist::class,
            'playlistable',
            'muzibu_playlistables',
            'playlistable_id',
            'playlist_id'
        )->withPivot('position')->withTimestamps();
    }

    /**
     * Aktif playlist'ler
     */
    public function activePlaylists(): MorphToMany
    {
        return $this->playlists()->where('is_active', true);
    }

    /**
     * Public playlist'ler
     */
    public function publicPlaylists(): MorphToMany
    {
        return $this->playlists()->where('is_public', true)->where('is_active', true);
    }

    /**
     * Playlist sayısı
     */
    public function getPlaylistsCountAttribute(): int
    {
        return $this->playlists()->count();
    }

    /**
     * Playlist ekle
     */
    public function attachPlaylist(Playlist $playlist, int $position = 0): void
    {
        if (!$this->playlists()->where('playlist_id', $playlist->playlist_id)->exists()) {
            $this->playlists()->attach($playlist->playlist_id, ['position' => $position]);
        }
    }

    /**
     * Playlist çıkar
     */
    public function detachPlaylist(Playlist $playlist): void
    {
        $this->playlists()->detach($playlist->playlist_id);
    }

    /**
     * Playlist'leri sync et
     */
    public function syncPlaylists(array $playlistIds): void
    {
        $this->playlists()->sync($playlistIds);
    }
}
