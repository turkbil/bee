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
        // Her içerik için varsayılan 1 adet 5 yıldız + kullanıcı oyları
        $userRatingsSum = $this->ratings()->sum('rating_value') ?? 0;
        $userRatingsCount = $this->ratings()->count();

        // Varsayılan 1 adet 5 yıldız ekliyoruz
        $totalSum = $userRatingsSum + 5;
        $totalCount = $userRatingsCount + 1;

        return round($totalSum / $totalCount, 1);
    }

    public function ratingsCount(): int
    {
        // Gerçek kullanıcı oyları + 1 varsayılan oy
        return $this->ratings()->count() + 1;
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

        $merged = array_merge($distribution, $results);

        // Varsayılan 1 adet 5 yıldız ekliyoruz
        $merged[5] = ($merged[5] ?? 0) + 1;

        return $merged;
    }
}
