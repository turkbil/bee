<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Song;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Playlist;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Sector;
use Modules\Muzibu\App\Models\Radio;
use Modules\ReviewSystem\App\Models\Rating;

class RatingController extends Controller
{
    protected $modelMap = [
        'songs' => Song::class,
        'albums' => Album::class,
        'playlists' => Playlist::class,
        'genres' => Genre::class,
        'sectors' => Sector::class,
        'radios' => Radio::class,
    ];

    public function rate(Request $request, string $type, int $id): JsonResponse
    {
        try {
            // Auth check
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Giriş yapmalısınız'], 401);
            }

            // Validate type
            if (!isset($this->modelMap[$type])) {
                return response()->json(['success' => false, 'message' => 'Geçersiz içerik türü'], 400);
            }

            // Validate request
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
            ]);

            $modelClass = $this->modelMap[$type];
            $model = $modelClass::find($id);

            if (!$model) {
                return response()->json(['success' => false, 'message' => 'İçerik bulunamadı'], 404);
            }

            $user = auth()->user();
            $ratingValue = $request->input('rating');

            // Save rating using ReviewSystem Rating model
            Rating::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'ratable_type' => $modelClass,
                    'ratable_id' => $id,
                ],
                [
                    'rating_value' => $ratingValue,
                ]
            );

            // Auto-favorite if rating is 4 or 5
            $autoFavorited = false;
            if ($ratingValue >= 4 && method_exists($model, 'isFavoritedBy')) {
                if (!$model->isFavoritedBy($user->id)) {
                    // Add to favorites using HasFavorites trait
                    $model->favorites()->create(['user_id' => $user->id]);
                    $autoFavorited = true;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Puanınız kaydedildi',
                'rating' => $ratingValue,
                'auto_favorited' => $autoFavorited,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            \Log::error('Rating error:', ['type' => $type, 'id' => $id, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Bir hata oluştu'], 500);
        }
    }
}
