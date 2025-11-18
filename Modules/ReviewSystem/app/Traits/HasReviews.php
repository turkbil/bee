<?php

namespace Modules\ReviewSystem\App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;

trait HasReviews
{
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratable');
    }

    public function averageRating(): float
    {
        // Fallback DB'de (user_id=0, rating=5) olarak tutuluyor
        $avg = $this->ratings()->avg('rating_value');
        return $avg ? (float) $avg : 0.0;
    }

    public function ratingsCount(): int
    {
        // Fallback DB'de sayılıyor
        return $this->ratings()->count();
    }

    public function reviewsCount(): int
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function getStarsDistribution(): array
    {
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        $results = $this->ratings()
            ->selectRaw('rating_value, COUNT(*) as count')
            ->groupBy('rating_value')
            ->pluck('count', 'rating_value')
            ->toArray();

        return array_merge($distribution, $results);
    }
}
