<?php

namespace Modules\Favorite\App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Favorite\App\Models\Favorite;

trait HasFavorites
{
    /**
     * Polymorphic relation - Bu model'e eklenen favoriler
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Belirli bir kullanıcı bu içeriği favorilere eklemiş mi?
     */
    public function isFavoritedBy($userId): bool
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    /**
     * Toplam favori sayısı
     */
    public function favoritesCount(): int
    {
        return $this->favorites()->count();
    }

    /**
     * Favori ekle/çıkar (toggle)
     */
    public function toggleFavorite($userId): array
    {
        $existing = $this->favorites()->where('user_id', $userId)->first();

        if ($existing) {
            // Favorilerden çıkar
            $existing->delete();
            return [
                'action' => 'removed',
                'is_favorited' => false,
                'favorites_count' => $this->favorites()->count(),
            ];
        }

        // Favorilere ekle
        $this->favorites()->create([
            'user_id' => $userId,
        ]);

        return [
            'action' => 'added',
            'is_favorited' => true,
            'favorites_count' => $this->favorites()->count(),
        ];
    }

    /**
     * Scope: En çok favorilenenler
     */
    public function scopeMostFavorited($query, int $limit = 10)
    {
        return $query->withCount('favorites')
            ->orderBy('favorites_count', 'desc')
            ->limit($limit);
    }
}
