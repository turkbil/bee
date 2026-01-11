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
     * Cache: Sadece arama sonuçları için 30sn (boş arama = cache yok)
     */
    public function getAvailableSongs($id)
    {
        $search = request('search', '');
        $offset = (int) request('offset', 0);
        $limit = 50;

        // Playlist şarkı ID'leri (kısa cache - 15sn)
        $playlistSongIds = \Cache::remember("playlist_songs_ids_{$id}", 15, function() use ($id) {
            return \DB::table('muzibu_playlist_song')
                ->where('playlist_id', $id)
                ->pluck('song_id')
                ->toArray();
        });

        // Query builder
        $buildQuery = function() use ($search, $offset, $limit, $playlistSongIds) {
            $query = \Modules\Muzibu\App\Models\Song::where('is_active', true)
                ->with(['album.artist', 'genre'])
                ->whereNotIn('song_id', $playlistSongIds)
                ->orderBy('title');

            if ($search) {
                $searchTerm = '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    // Şarkı adı - JSON field
                    $q->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', [$searchTerm])
                      // Şarkı sözleri (lyrics) - JSON field (sadece TR)
                      ->orWhereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(lyrics, "$.tr"))) LIKE ?', [$searchTerm])
                      // Sanatçı - JSON field
                      ->orWhereHas('album.artist', fn($artistQuery) =>
                          $artistQuery->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', [$searchTerm])
                      )
                      // Albüm - JSON field
                      ->orWhereHas('album', fn($albumQuery) =>
                          $albumQuery->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', [$searchTerm])
                      )
                      // Genre/Tür - JSON field
                      ->orWhereHas('genre', fn($genreQuery) =>
                          $genreQuery->whereRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr"))) LIKE ?', [$searchTerm])
                      );
                });
            }

            return $query->offset($offset)->limit($limit)->get();
        };

        // Boş arama = cache yok (yeni şarkılar hemen görünsün)
        // Arama varsa = 30sn cache (tekrar aramalar hızlı olsun)
        if (empty($search)) {
            $songs = $buildQuery();
        } else {
            $cacheKey = "playlist_search_{$id}_{$offset}_" . md5($search);
            $songs = \Cache::remember($cacheKey, 30, $buildQuery);
        }

        $result = $songs->map(function($song) {
            $title = $song->getTranslated('title', app()->getLocale());
            $artistTitle = $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? 'Unknown';

            // Thumbnail URL - şimdilik devre dışı (Media model yükleme sorunu)
            $coverUrl = null;

            // Ayrı URL'ler hesapla (fallback için)
            $hlsUrl = $song->hls_path ? asset('storage/' . $song->hls_path) : null;
            $fileUrl = $song->file_path ? asset('storage/muzibu/songs/' . $song->file_path) : null;

            // Albüm ve Genre bilgisi
            $albumTitle = $song->album?->getTranslated('title', app()->getLocale());
            $genreTitle = $song->genre?->getTranslated('title', app()->getLocale());

            return [
                'id' => $song->song_id,
                'title' => is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? reset($title)) : 'Unknown'),
                'artist' => is_string($artistTitle) ? $artistTitle : (is_array($artistTitle) ? ($artistTitle[app()->getLocale()] ?? $artistTitle['tr'] ?? reset($artistTitle)) : 'Unknown'),
                'album' => is_string($albumTitle) ? $albumTitle : (is_array($albumTitle) ? ($albumTitle[app()->getLocale()] ?? $albumTitle['tr'] ?? reset($albumTitle)) : null),
                'genre' => is_string($genreTitle) ? $genreTitle : (is_array($genreTitle) ? ($genreTitle[app()->getLocale()] ?? $genreTitle['tr'] ?? reset($genreTitle)) : null),
                'duration' => $song->duration ? gmdate('i:s', $song->duration) : null,
                'cover_url' => $coverUrl,
                'hls_path' => $song->hls_path,
                'file_path' => $song->file_path,
                'hls_url' => $hlsUrl,
                'file_url' => $fileUrl,
                'audio_url' => $hlsUrl ?? $fileUrl // Backward compatibility
            ];
        });

        return response()->json($result);
    }

    /**
     * Playlist şarkılarını getir (AJAX) - Infinite Scroll
     */
    public function getSelectedSongs($id)
    {
        $playlist = \Modules\Muzibu\App\Models\Playlist::findOrFail($id);

        $offset = request('offset', 0);
        $limit = 50;

        $songs = $playlist->songs()
            ->with(['album.artist', 'genre'])
            ->orderBy('muzibu_playlist_song.position')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function($song) {
                $title = $song->getTranslated('title', app()->getLocale());
                $artistTitle = $song->album?->artist?->getTranslated('title', app()->getLocale()) ?? 'Unknown';

                // Albüm ve Genre bilgisi
                $albumTitle = $song->album?->getTranslated('title', app()->getLocale());
                $genreTitle = $song->genre?->getTranslated('title', app()->getLocale());

                // Thumbnail URL - şimdilik devre dışı (Media model yükleme sorunu)
                $coverUrl = null;

                // Ayrı URL'ler hesapla (fallback için)
                $hlsUrl = $song->hls_path ? asset('storage/' . $song->hls_path) : null;
                $fileUrl = $song->file_path ? asset('storage/muzibu/songs/' . $song->file_path) : null;

                return [
                    'id' => $song->song_id,
                    'title' => is_string($title) ? $title : (is_array($title) ? ($title[app()->getLocale()] ?? $title['tr'] ?? reset($title)) : 'Unknown'),
                    'artist' => is_string($artistTitle) ? $artistTitle : (is_array($artistTitle) ? ($artistTitle[app()->getLocale()] ?? $artistTitle['tr'] ?? reset($artistTitle)) : 'Unknown'),
                    'album' => is_string($albumTitle) ? $albumTitle : (is_array($albumTitle) ? ($albumTitle[app()->getLocale()] ?? $albumTitle['tr'] ?? reset($albumTitle)) : null),
                    'genre' => is_string($genreTitle) ? $genreTitle : (is_array($genreTitle) ? ($genreTitle[app()->getLocale()] ?? $genreTitle['tr'] ?? reset($genreTitle)) : null),
                    'duration' => $song->duration ? gmdate('i:s', $song->duration) : null,
                    'cover_url' => $coverUrl,
                    'hls_path' => $song->hls_path,
                    'file_path' => $song->file_path,
                    'hls_url' => $hlsUrl,
                    'file_url' => $fileUrl,
                    'audio_url' => $hlsUrl ?? $fileUrl // Backward compatibility
                ];
            });

        // Toplam süre hesapla (tüm şarkılar için - cache'lenebilir)
        $totalDuration = $playlist->songs()->sum('duration') ?? 0;

        // Toplam şarkı sayısı (has more kontrolü için)
        $totalCount = $playlist->songs()->count();

        return response()->json([
            'songs' => $songs,
            'total_duration' => $totalDuration,
            'total_count' => $totalCount,
            'has_more' => ($offset + $limit) < $totalCount
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

        // Ekle (cache count'ları da güncelle)
        $playlist->attachSongWithCache($songId, ['position' => $newPosition]);

        // Search cache'ini temizle
        \Cache::forget("playlist_songs_ids_{$id}");

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

        // Çıkar (cache count'ları da güncelle)
        $playlist->detachSongWithCache($songId);

        // Search cache'ini temizle
        \Cache::forget("playlist_songs_ids_{$id}");

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
