<?php

namespace Modules\Favorite\App\Services;

use Modules\Favorite\App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FavoriteService
{
    /**
     * Favori ekle/çıkar (toggle)
     */
    public function toggleFavorite(string $modelClass, int $modelId, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return [
                'success' => false,
                'message' => 'Kullanıcı girişi gerekli',
            ];
        }

        // Model'i bul
        $model = $modelClass::find($modelId);

        if (!$model) {
            return [
                'success' => false,
                'message' => 'İçerik bulunamadı',
            ];
        }

        // Toggle işlemi
        $result = $model->toggleFavorite($userId);

        // Cache temizle
        $this->clearCache($userId, $modelClass);

        return [
            'success' => true,
            'message' => $result['action'] === 'added' ? 'Favorilere eklendi' : 'Favorilerden çıkarıldı',
            'data' => $result,
        ];
    }

    /**
     * Kullanıcının favorilerini getir
     */
    public function getUserFavorites(?int $userId = null, ?string $modelType = null, int $perPage = 15)
    {
        $userId = $userId ?? Auth::id();

        $query = Favorite::with('favoritable')
            ->where('user_id', $userId)
            ->latest();

        if ($modelType) {
            $query->where('favoritable_type', $modelType);
        }

        return $query->paginate($perPage);
    }

    /**
     * En çok favorilenen içerikleri getir
     */
    public function getMostFavorited(string $modelClass, int $limit = 10, int $days = 30)
    {
        $cacheKey = "most_favorited_{$modelClass}_{$limit}_{$days}";

        return Cache::remember($cacheKey, 3600, function () use ($modelClass, $limit, $days) {
            return $modelClass::query()
                ->withCount(['favorites' => function ($query) use ($days) {
                    $query->where('created_at', '>=', now()->subDays($days));
                }])
                ->orderBy('favorites_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Kullanıcı favorilerine eklemiş mi kontrol et
     */
    public function isFavorited(string $modelClass, int $modelId, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            return false;
        }

        return Favorite::where('user_id', $userId)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $modelId)
            ->exists();
    }

    /**
     * Favori sayısını getir
     */
    public function getFavoritesCount(string $modelClass, int $modelId): int
    {
        return Favorite::where('favoritable_type', $modelClass)
            ->where('favoritable_id', $modelId)
            ->count();
    }

    /**
     * Cache temizle
     */
    protected function clearCache(?int $userId = null, ?string $modelType = null): void
    {
        if ($userId) {
            Cache::forget("user_favorites_{$userId}");
        }

        if ($modelType) {
            Cache::forget("most_favorited_{$modelType}_10_30");
            Cache::forget("most_favorited_{$modelType}_10_7");
        }
    }
}
