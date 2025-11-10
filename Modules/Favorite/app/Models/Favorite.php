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
}
