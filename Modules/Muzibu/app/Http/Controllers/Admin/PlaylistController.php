<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class PlaylistController extends Controller
{
    /**
     * Playlist Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.playlist-index');
    }

    /**
     * Playlist Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.playlist-manage', [
            'playlistId' => $id
        ]);
    }

    /**
     * Playlist Şarkı Yönetim Sayfası (jQuery AJAX)
     */
    public function manageSongs($id)
    {
        return view('muzibu::admin.playlist-songs-manage', [
            'playlistId' => (int)$id
        ]);
    }

    /**
     * Playlist bilgisi (AJAX)
     */
    public function getPlaylistInfo($id)
    {
        $playlist = \Modules\Muzibu\App\Models\Playlist::findOrFail($id);

        $title = $playlist->getTranslated('title', app()->getLocale());
        $safeTitle = is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? $title['en'] ?? reset($title) ?? 'Unknown') : 'Unknown');

        return response()->json([
            'id' => $playlist->playlist_id,
            'title' => $safeTitle
        ]);
    }

    /**
     * Kullanılabilir şarkıları getir (AJAX) - Playlist'te olmayanlar
     */
    public function getAvailableSongs($id)
    {
        $search = request('search', '');
        $offset = request('offset', 0);
        $limit = 50;

        $query = \Modules\Muzibu\App\Models\Song::active()
            ->with(['album.artist', 'genre'])
            ->whereNotIn('song_id', function($q) use ($id) {
                $q->select('song_id')
                  ->from('muzibu_playlist_song')
                  ->where('playlist_id', $id);
            })
            ->orderBy('title');

        if ($search) {
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                // Şarkı adı
                $q->where('title', 'like', $searchTerm)
                  // Şarkı sözleri
                  ->orWhere('lyrics', 'like', $searchTerm)
                  // Sanatçı
                  ->orWhereHas('album.artist', fn($artistQuery) =>
                      $artistQuery->where('title', 'like', $searchTerm)
                  )
                  // Albüm
                  ->orWhereHas('album', fn($albumQuery) =>
                      $albumQuery->where('title', 'like', $searchTerm)
                  )
                  // Genre/Tür
                  ->orWhereHas('genre', fn($genreQuery) =>
                      $genreQuery->where('title', 'like', $searchTerm)
                  );
            });
        }

        $songs = $query->offset($offset)->limit($limit)->get()->map(function($song) {
            $title = $song->getTranslated('title', app()->getLocale());
            $artistTitle = $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? 'Unknown';

            // Thumbnail URL - şimdilik devre dışı (Media model yükleme sorunu)
            $coverUrl = null;

            return [
                'id' => $song->song_id,
                'title' => is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? reset($title)) : 'Unknown'),
                'artist' => is_string($artistTitle) ? $artistTitle : (is_array($artistTitle) ? ($artistTitle[app()->getLocale()] ?? $artistTitle['tr'] ?? reset($artistTitle)) : 'Unknown'),
                'duration' => $song->duration ? gmdate('i:s', $song->duration) : null,
                'cover_url' => $coverUrl
            ];
        });

        return response()->json($songs);
    }

    /**
     * Playlist şarkılarını getir (AJAX)
     */
    public function getSelectedSongs($id)
    {
        $playlist = \Modules\Muzibu\App\Models\Playlist::findOrFail($id);

        $songs = $playlist->songs()
            ->with(['album.artist'])
            ->orderBy('muzibu_playlist_song.position')
            ->get()
            ->map(function($song) {
                $title = $song->getTranslated('title', app()->getLocale());
                $artistTitle = $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? 'Unknown';

                // Thumbnail URL - şimdilik devre dışı (Media model yükleme sorunu)
                $coverUrl = null;

                return [
                    'id' => $song->song_id,
                    'title' => is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? reset($title)) : 'Unknown'),
                    'artist' => is_string($artistTitle) ? $artistTitle : (is_array($artistTitle) ? ($artistTitle[app()->getLocale()] ?? $artistTitle['tr'] ?? reset($artistTitle)) : 'Unknown'),
                    'duration' => $song->duration ? gmdate('i:s', $song->duration) : null,
                    'cover_url' => $coverUrl
                ];
            });

        // Toplam süre hesapla
        $totalDuration = $playlist->songs()->sum('duration') ?? 0;

        return response()->json([
            'songs' => $songs,
            'total_duration' => $totalDuration
        ]);
    }

    /**
     * Tek şarkı ekle (AJAX)
     */
    public function addSongs($id)
    {
        $songId = request('song_id');

        if (!$songId) {
            return response()->json(['success' => false, 'message' => 'Şarkı ID gerekli'], 400);
        }

        $playlist = \Modules\Muzibu\App\Models\Playlist::findOrFail($id);
        $song = \Modules\Muzibu\App\Models\Song::active()->findOrFail($songId);

        // Duplicate kontrolü (pivot table column'unu belirt)
        $exists = $playlist->songs()->where('muzibu_playlist_song.song_id', $songId)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Bu şarkı zaten playlist\'te mevcut'], 400);
        }

        // Position hesapla
        $maxPosition = $playlist->songs()->max('muzibu_playlist_song.position') ?? -1;
        $newPosition = $maxPosition + 1;

        // Ekle
        $playlist->songs()->attach($songId, ['position' => $newPosition]);

        return response()->json([
            'success' => true,
            'message' => 'Şarkı playlist\'e eklendi'
        ]);
    }

    /**
     * Tek şarkı çıkar (AJAX)
     */
    public function removeSongs($id)
    {
        $songId = request('song_id');

        if (!$songId) {
            return response()->json(['success' => false, 'message' => 'Şarkı ID gerekli'], 400);
        }

        $playlist = \Modules\Muzibu\App\Models\Playlist::findOrFail($id);

        // Çıkar
        $playlist->songs()->detach($songId);

        // Sıralamayı düzelt (gap kalmasın)
        $songs = $playlist->songs()->orderBy('muzibu_playlist_song.position')->get();
        foreach ($songs as $index => $song) {
            \DB::table('muzibu_playlist_song')
                ->where('playlist_id', $id)
                ->where('song_id', $song->song_id)
                ->update(['position' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Şarkı playlist\'ten çıkarıldı'
        ]);
    }

    /**
     * Sıralama güncelle (AJAX)
     */
    public function reorderSongs($id)
    {
        $order = request('order', []);

        if (empty($order)) {
            return response()->json(['success' => false, 'message' => 'Sıralama bilgisi gerekli'], 400);
        }

        \DB::transaction(function () use ($id, $order) {
            foreach ($order as $songId => $position) {
                \DB::table('muzibu_playlist_song')
                    ->where('playlist_id', $id)
                    ->where('song_id', $songId)
                    ->update(['position' => $position]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sıralama güncellendi'
        ]);
    }
}
