<?php

namespace Modules\ReviewSystem\App\Services;

use Modules\ReviewSystem\App\Models\Review;
use Modules\ReviewSystem\App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewService
{
    public function addRating(string $modelClass, int $modelId, int $ratingValue, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return ['success' => false, 'message' => 'Kullanıcı girişi gerekli'];
        }

        if ($ratingValue < 1 || $ratingValue > 5) {
            return ['success' => false, 'message' => 'Puan 1-5 arası olmalı'];
        }

        $model = $modelClass::find($modelId);
        if (!$model) {
            return ['success' => false, 'message' => 'İçerik bulunamadı'];
        }

        Rating::updateOrCreate(
            [
                'user_id' => $userId,
                'ratable_type' => $modelClass,
                'ratable_id' => $modelId,
            ],
            ['rating_value' => $ratingValue]
        );

        $this->clearCache($modelClass, $modelId);

        return [
            'success' => true,
            'message' => 'Puanınız kaydedildi',
            'data' => [
                'average_rating' => $model->averageRating(),
                'ratings_count' => $model->ratingsCount(),
            ],
        ];
    }

    public function addReview(array $data): array
    {
        $userId = $data['user_id'] ?? Auth::id();

        if (!$userId) {
            return ['success' => false, 'message' => 'Kullanıcı girişi gerekli'];
        }

        $review = Review::create([
            'user_id' => $userId,
            'reviewable_type' => $data['model_class'],
            'reviewable_id' => $data['model_id'],
            'parent_id' => $data['parent_id'] ?? null,
            'author_name' => $data['author_name'] ?? Auth::user()->name,
            'review_body' => $data['review_body'],
            'rating_value' => $data['rating_value'] ?? null,
            'is_approved' => false,
        ]);

        $this->clearCache($data['model_class'], $data['model_id']);

        return [
            'success' => true,
            'message' => 'Yorumunuz onay bekliyor',
            'data' => $review,
        ];
    }

    public function approveReview(int $reviewId, ?int $approvedBy = null): bool
    {
        $review = Review::find($reviewId);
        if (!$review) {
            return false;
        }

        $review->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approvedBy ?? Auth::id(),
        ]);

        $this->clearCache($review->reviewable_type, $review->reviewable_id);

        return true;
    }

    public function getReviews(string $modelClass, int $modelId, bool $onlyApproved = true)
    {
        $query = Review::where('reviewable_type', $modelClass)
            ->where('reviewable_id', $modelId)
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->latest();

        if ($onlyApproved) {
            $query->where('is_approved', true);
        }

        return $query->get();
    }

    public function getSchemaMarkup(string $modelClass, int $modelId): ?array
    {
        $model = $modelClass::find($modelId);
        if (!$model) {
            return null;
        }

        $avgRating = $model->averageRating();
        $ratingsCount = $model->ratingsCount();

        if ($ratingsCount === 0) {
            return null;
        }

        return [
            '@type' => 'AggregateRating',
            'ratingValue' => round($avgRating, 1),
            'bestRating' => '5',
            'worstRating' => '1',
            'ratingCount' => $ratingsCount,
        ];
    }

    protected function clearCache(string $modelClass, int $modelId): void
    {
        Cache::forget("reviews_{$modelClass}_{$modelId}");
        Cache::forget("ratings_{$modelClass}_{$modelId}");
    }
}
