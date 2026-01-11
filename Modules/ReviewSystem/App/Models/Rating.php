<?php

namespace Modules\ReviewSystem\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'ratable_id',
        'ratable_type',
        'rating_value',
    ];

    protected $casts = [
        'rating_value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ratable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModel($query, string $modelType)
    {
        return $query->where('ratable_type', $modelType);
    }
}
