<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Log;
use Modules\Favorite\App\Services\FavoriteService;

class ProcessPendingFavorite
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * Handle the event.
     * Login olduktan sonra pending favorite varsa otomatik olarak favoriye ekle
     */
    public function handle(Authenticated $event)
    {
        // Session'da pending favorite var mı kontrol et
        $pendingFavorite = session('pending_favorite');

        if (!$pendingFavorite || !isset($pendingFavorite['model_class'], $pendingFavorite['model_id'])) {
            return; // Pending favorite yok, devam et
        }

        try {
            // Favoriye ekle
            $result = $this->favoriteService->toggleFavorite(
                $pendingFavorite['model_class'],
                $pendingFavorite['model_id'],
                $event->user->id
            );

            if ($result['success']) {
                Log::info('✅ Pending favorite added after login', [
                    'user_id' => $event->user->id,
                    'model_class' => $pendingFavorite['model_class'],
                    'model_id' => $pendingFavorite['model_id'],
                    'favorited' => $result['data']['is_favorited'] ?? null
                ]);

                // Session'da başarı mesajı kaydet
                session()->flash('favorite_added', true);

                // Return URL varsa session'a kaydet (redirect için)
                if (isset($pendingFavorite['return_url'])) {
                    session(['intended_url' => $pendingFavorite['return_url']]);
                }
            }
        } catch (\Exception $e) {
            Log::error('❌ Pending favorite failed after login', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage()
            ]);
        } finally {
            // Pending favorite'yi temizle (sadece bir kez çalışsın)
            session()->forget('pending_favorite');
        }
    }
}
