<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\app\Models\Song;
use Modules\Muzibu\app\Models\Album;
use Modules\Muzibu\app\Models\Playlist;
use Modules\ReviewSystem\app\Models\Review;

class RatingController extends Controller
{
    /**
     * Şarkıya puan ver
     * 4-5 yıldız verince otomatik favoriye ekle
     */
    public function rateSong(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $song = Song::findOrFail($id);
        $user = auth()->user();

        // Rating kaydet/güncelle
        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'reviewable_type' => Song::class,
                'reviewable_id' => $song->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        // 4-5 yıldız verdiyse otomatik favoriye ekle
        if ($request->rating >= 4) {
            if (!$user->hasFavorite('song', $song->id)) {
                $user->addFavorite('song', $song->id);
                $autoFavorited = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Puan kaydedildi',
            'rating' => $request->rating,
            'auto_favorited' => $autoFavorited ?? false
        ]);
    }

    /**
     * Albüme puan ver
     * 4-5 yıldız verince otomatik favoriye ekle
     */
    public function rateAlbum(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $album = Album::findOrFail($id);
        $user = auth()->user();

        // Rating kaydet/güncelle
        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'reviewable_type' => Album::class,
                'reviewable_id' => $album->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        // 4-5 yıldız verdiyse otomatik favoriye ekle
        if ($request->rating >= 4) {
            if (!$user->hasFavorite('album', $album->id)) {
                $user->addFavorite('album', $album->id);
                $autoFavorited = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Puan kaydedildi',
            'rating' => $request->rating,
            'auto_favorited' => $autoFavorited ?? false
        ]);
    }

    /**
     * Playlist'e puan ver
     * 4-5 yıldız verince otomatik favoriye ekle
     */
    public function ratePlaylist(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $playlist = Playlist::findOrFail($id);
        $user = auth()->user();

        // Rating kaydet/güncelle
        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'reviewable_type' => Playlist::class,
                'reviewable_id' => $playlist->id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        // 4-5 yıldız verdiyse otomatik favoriye ekle
        if ($request->rating >= 4) {
            if (!$user->hasFavorite('playlist', $playlist->id)) {
                $user->addFavorite('playlist', $playlist->id);
                $autoFavorited = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Puan kaydedildi',
            'rating' => $request->rating,
            'auto_favorited' => $autoFavorited ?? false
        ]);
    }
}
