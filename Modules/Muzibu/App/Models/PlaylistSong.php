<?php

namespace Modules\Muzibu\App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * PlaylistSong Pivot Model
 *
 * Custom pivot model for Playlist-Song relationship.
 * Allows Observer usage for automatic cache count updates.
 *
 * @property int $playlist_id
 * @property int $song_id
 * @property int|null $position
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PlaylistSong extends Pivot
{
    /**
     * The table associated with the model.
     */
    protected $table = 'muzibu_playlist_song';

    /**
     * Indicates if the IDs are auto-incrementing.
     * False because this is a composite primary key (playlist_id, song_id)
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'playlist_id',
        'song_id',
        'position',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'playlist_id' => 'integer',
        'song_id' => 'integer',
        'position' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * Playlist ilişkisi
     */
    public function playlist()
    {
        return $this->belongsTo(Playlist::class, 'playlist_id', 'playlist_id');
    }

    /**
     * Song ilişkisi
     */
    public function song()
    {
        return $this->belongsTo(Song::class, 'song_id', 'song_id');
    }

    /**
     * Dinamik connection resolver
     * Muzibu modülü SADECE tenant 1001 için, ZORLA tenant connection kullan!
     */
    public function getConnectionName()
    {
        return 'tenant';
    }
}
