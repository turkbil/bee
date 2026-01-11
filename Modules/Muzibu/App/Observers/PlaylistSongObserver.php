<?php

namespace Modules\Muzibu\App\Observers;

use Modules\Muzibu\App\Models\PlaylistSong;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Song;

/**
 * PlaylistSongObserver
 *
 * Handles automatic cache count updates when songs are attached/detached from playlists.
 * Updates:
 * - Playlist: songs_count, total_duration
 */
class PlaylistSongObserver
{
    /**
     * Handle the PlaylistSong "created" event.
     * Fired when a song is attached to a playlist.
     */
    public function created(PlaylistSong $playlistSong): void
    {
        $playlist = Playlist::find($playlistSong->playlist_id);
        $song = Song::find($playlistSong->song_id);

        if (!$playlist || !$song) {
            return;
        }

        // Only count active songs
        if (!$song->is_active) {
            return;
        }

        // Increment playlist cache counts
        $playlist->incrementCachedCount('songs_count');
        $playlist->incrementCachedCount('total_duration', (int) $song->duration);

        \Log::info('PlaylistSong created - cache updated', [
            'playlist_id' => $playlist->playlist_id,
            'song_id' => $song->song_id,
            'duration' => $song->duration,
        ]);
    }

    /**
     * Handle the PlaylistSong "deleted" event.
     * Fired when a song is detached from a playlist.
     */
    public function deleted(PlaylistSong $playlistSong): void
    {
        $playlist = Playlist::find($playlistSong->playlist_id);
        $song = Song::find($playlistSong->song_id);

        if (!$playlist || !$song) {
            return;
        }

        // Only count active songs
        if (!$song->is_active) {
            return;
        }

        // Decrement playlist cache counts
        $playlist->decrementCachedCount('songs_count');
        $playlist->decrementCachedCount('total_duration', (int) $song->duration);

        \Log::info('PlaylistSong deleted - cache updated', [
            'playlist_id' => $playlist->playlist_id,
            'song_id' => $song->song_id,
            'duration' => $song->duration,
        ]);
    }
}
