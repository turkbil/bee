<?php

namespace Modules\Favorite\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_id',
        'favoritable_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Polymorphic relation - Favorilenen içerik
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Favorileyen kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Belirli bir kullanıcının favorileri
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Belirli bir model type için favoriler
     */
    public function scopeForModel($query, string $modelType)
    {
        return $query->where('favoritable_type', $modelType);
    }

    /**
     * Check if user has favorited an item (TENANT-AWARE)
     *
     * @param int|null $userId
     * @param string $type (song, album, playlist)
     * @param int $itemId
     * @return bool
     */
    public static function check(?int $userId, string $type, int $itemId): bool
    {
        if (!$userId) {
            return false;
        }

        $modelMap = [
            'song' => \Modules\Muzibu\App\Models\Song::class,
            'album' => \Modules\Muzibu\App\Models\Album::class,
            'playlist' => \Modules\Muzibu\App\Models\Playlist::class,
        ];

        if (!isset($modelMap[$type])) {
            return false;
        }

        return self::where('user_id', $userId)
            ->where('favoritable_type', $modelMap[$type])
            ->where('favoritable_id', $itemId)
            ->exists();
    }
}
