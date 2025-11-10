<?php

namespace Modules\ReviewSystem\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'reviewable_id',
        'reviewable_type',
        'parent_id',
        'author_name',
        'review_body',
        'rating_value',
        'is_approved',
        'approved_at',
        'approved_by',
        'helpful_count',
        'unhelpful_count',
    ];

    protected $casts = [
        'rating_value' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Review::class, 'parent_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
}
