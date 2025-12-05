<?php

namespace Modules\Muzibu\app\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\app\Models\Playlist;
use Modules\Muzibu\app\Models\Song;
use Modules\Muzibu\app\Models\Album;
use Illuminate\Support\Str;

class PlaylistManagementController extends Controller
{
    /**
     * Kullanıcının playlistlerini getir
     */
    public function getUserPlaylists()
    {
        $user = auth()->user();

        $playlists = Playlist::where('user_id', $user->id)
            ->withCount('songs')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($playlist) {
                return [
                    'id' => $playlist->id,
                    'title' => $playlist->title,
                    'song_count' => $playlist->songs_count,
                    'is_mine' => true
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $playlists
        ]);
    }

    /**
     * Playlist'e şarkı/albüm ekle
     */
    public function addToPlaylist(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:song,album',
            'item_id' => 'required|integer'
        ]);

        $playlist = Playlist::findOrFail($id);
        $user = auth()->user();

        // Yetki kontrolü - sadece kendi playlist'ine ekleyebilir
        if ($playlist->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu playlist size ait değil'
            ], 403);
        }

        if ($request->type === 'song') {
            $song = Song::findOrFail($request->item_id);

            // Zaten ekliyse ekleme
            if (!$playlist->songs()->where('song_id', $song->id)->exists()) {
                $playlist->songs()->attach($song->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Şarkı playliste eklendi'
            ]);
        } else {
            // Album - tüm şarkılarını ekle
            $album = Album::with('songs')->findOrFail($request->item_id);

            foreach ($album->songs as $song) {
                if (!$playlist->songs()->where('song_id', $song->id)->exists()) {
                    $playlist->songs()->attach($song->id);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Albüm şarkıları playliste eklendi'
            ]);
        }
    }

    /**
     * Playlist sil
     */
    public function deletePlaylist($id)
    {
        $playlist = Playlist::findOrFail($id);
        $user = auth()->user();

        // Yetki kontrolü - sadece kendi playlist'ini silebilir
        if ($playlist->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu playlist size ait değil'
            ], 403);
        }

        $playlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Playlist silindi'
        ]);
    }

    /**
     * Sistem playlist'ini kopyala (kullanıcının kendi playlist'i olarak)
     */
    public function copyPlaylist(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:255'
        ]);

        $sourcePlaylist = Playlist::with('songs')->findOrFail($id);
        $user = auth()->user();

        // Yeni playlist oluştur
        $newPlaylist = Playlist::create([
            'title' => $request->title ?? ($sourcePlaylist->title . ' (Kopyam)'),
            'slug' => Str::slug($request->title ?? ($sourcePlaylist->title . ' kopyam ' . time())),
            'user_id' => $user->id,
            'description' => $sourcePlaylist->description,
            'is_public' => false, // Kullanıcının kopyası private
        ]);

        // Tüm şarkıları kopyala
        foreach ($sourcePlaylist->songs as $song) {
            $newPlaylist->songs()->attach($song->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Playlist kopyalandı',
            'playlist' => [
                'id' => $newPlaylist->id,
                'title' => $newPlaylist->title,
                'song_count' => $sourcePlaylist->songs->count()
            ]
        ]);
    }
}
