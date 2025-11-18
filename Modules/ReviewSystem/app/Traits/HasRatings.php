<?php

namespace Modules\ReviewSystem\App\Traits;

use Modules\ReviewSystem\App\Models\Rating;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRatings
{
    /**
     * Get all ratings for this model
     */
    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    /**
     * Get average rating
     * Fallback: 5.0 if no ratings exist
     */
    public function averageRating(): float
    {
        // Fallback DB'de (user_id=0, rating=5) olarak tutuluyor
        $avg = $this->ratings()->avg('rating_value');
        return $avg ? round($avg, 1) : 0.0;
    }

    /**
     * Get ratings count
     */
    public function ratingsCount(): int
    {
        // Fallback DB'de sayılıyor
        return $this->ratings()->count();
    }

    /**
     * Get user's rating for this model
     */
    public function userRating(?int $userId = null): ?int
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return null;
        }

        $rating = $this->ratings()->where('user_id', $userId)->first();
        return $rating ? $rating->rating_value : null;
    }

    /**
     * Check if user has rated this model
     */
    public function hasRatingByUser(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();

        if (!$userId) {
            return false;
        }

        return $this->ratings()->where('user_id', $userId)->exists();
    }

    /**
     * Get ratings distribution (1-5 stars)
     */
    public function ratingsDistribution(): array
    {
        $distribution = $this->ratings()
            ->selectRaw('rating_value, COUNT(*) as count')
            ->groupBy('rating_value')
            ->orderBy('rating_value', 'desc')
            ->pluck('count', 'rating_value')
            ->toArray();

        // Fill missing star ratings with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($distribution[$i])) {
                $distribution[$i] = 0;
            }
        }

        krsort($distribution);
        return $distribution;
    }
}
