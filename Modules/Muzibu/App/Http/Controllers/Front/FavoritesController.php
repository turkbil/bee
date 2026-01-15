<?php

namespace Modules\Muzibu\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Favorite\App\Models\Favorite;
use Modules\Muzibu\App\Models\{Song, Album, Playlist};

class FavoritesController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            // 🔥 FIX: AJAX isteklerinde redirect yerine 401 döndür (CORS sorunu!)
            if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response('Unauthorized', 401);
            }
            return redirect()->route('login');
        }

        $userId = auth()->id();

        // Count her tip için - morphMap alias'ları kullan (veritabanında bu şekilde kayıtlı)
        $modelMap = [
            'songs' => 'Song',
            'albums' => 'Album',
            'playlists' => 'Playlist',
            'genres' => 'Genre',
            'sectors' => 'Sector',
            'radios' => 'Radio',
            'blogs' => \Modules\Blog\App\Models\Blog::class, // Blog morphMap'te yok, tam path kullan
        ];

        $counts = [];
        foreach ($modelMap as $key => $class) {
            $counts[$key] = Favorite::where('user_id', $userId)
                ->where('favoritable_type', $class)
                ->count();
        }

        // Eğer hiç favori yoksa ve type belirtilmemişse, ilk dolu olan'a yönlendir
        $type = $request->get('type');
        if (!$type) {
            foreach ($counts as $key => $count) {
                if ($count > 0) {
                    // 🔥 FIX: AJAX isteklerinde redirect yerine type'ı set et (CORS sorunu!)
                    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        $type = $key;
                        break;
                    }
                    return redirect()->route('muzibu.favorites', ['type' => $key]);
                }
            }
            // Hiç favori yoksa songs'a yönlendir
            $type = 'songs';
        }

        $query = Favorite::where('user_id', $userId)
            ->with(['favoritable' => function($query) use ($type) {
                // Polymorphic eager loading optimizasyonu
                if ($type === 'songs') {
                    $query->with(['album.artist', 'coverMedia', 'album.coverMedia']);
                } elseif ($type === 'albums') {
                    $query->with(['artist', 'coverMedia']);
                } elseif ($type === 'playlists') {
                    $query->with('coverMedia')->withCount('songs');
                } elseif ($type === 'genres' || $type === 'sectors') {
                    $query->with('iconMedia');
                } elseif ($type === 'radios') {
                    $query->with('logoMedia');
                } elseif ($type === 'blogs') {
                    $query->with('media');
                }
            }])
            ->latest();

        if (isset($modelMap[$type])) {
            $query->where('favoritable_type', $modelMap[$type]);
        }

        $favorites = $query->paginate(40);

        // Set custom pagination view
        $favorites->setPath(request()->url());

        return response()
            ->view('themes.muzibu.favorites.index', compact('favorites', 'type', 'counts'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function apiIndex(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->get('type', 'all');
        $userId = auth()->id();

        $query = Favorite::where('user_id', $userId)
            ->with(['favoritable' => function($query) use ($type) {
                // Polymorphic eager loading optimizasyonu
                if ($type === 'songs') {
                    $query->with(['album.artist', 'coverMedia', 'album.coverMedia']);
                } elseif ($type === 'albums') {
                    $query->with(['artist', 'coverMedia']);
                } elseif ($type === 'playlists') {
                    $query->with('coverMedia')->withCount('songs');
                } elseif ($type === 'genres' || $type === 'sectors') {
                    $query->with('iconMedia');
                } elseif ($type === 'radios') {
                    $query->with('logoMedia');
                } elseif ($type === 'blogs') {
                    $query->with('media');
                }
            }])
            ->latest();

        if ($type !== 'all') {
            // morphMap alias'ları kullan
            $modelMap = [
                'songs' => 'Song',
                'albums' => 'Album',
                'playlists' => 'Playlist',
                'genres' => 'Genre',
                'sectors' => 'Sector',
                'radios' => 'Radio',
                'blogs' => \Modules\Blog\App\Models\Blog::class,
            ];
            if (isset($modelMap[$type])) {
                $query->where('favoritable_type', $modelMap[$type]);
            }
        }

        $favorites = $query->paginate(40);
        $html = view('themes.muzibu.partials.favorites-list', compact('favorites', 'type'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Favorilerim - Muzibu', 'description' => 'Favori içerikleriniz']])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * AI için favorilere ekleme
     * ACTION:ADD_TO_FAVORITES sistemi için
     */
    public function addToFavorites(Request $request)
    {
        // 1. Auth check
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen giriş yapın',
                'error_code' => 'AUTH_REQUIRED'
            ], 401);
        }

        // 2. Validate
        $validated = $request->validate([
            'type' => 'required|in:song,playlist,album,genre,sector,radio,blog',
            'id' => 'required|integer'
        ]);

        $userId = auth()->id();
        $type = $validated['type'];
        $itemId = $validated['id'];

        // 3. Model class mapping (tam class path - item bulmak için)
        $classMap = [
            'song' => Song::class,
            'playlist' => Playlist::class,
            'album' => Album::class,
            'genre' => \Modules\Muzibu\App\Models\Genre::class,
            'sector' => \Modules\Muzibu\App\Models\Sector::class,
            'radio' => \Modules\Muzibu\App\Models\Radio::class,
            'blog' => \Modules\Blog\App\Models\Blog::class,
        ];

        // morphMap alias'ları (veritabanına kaydetmek için)
        $morphMap = [
            'song' => 'Song',
            'playlist' => 'Playlist',
            'album' => 'Album',
            'genre' => 'Genre',
            'sector' => 'Sector',
            'radio' => 'Radio',
            'blog' => \Modules\Blog\App\Models\Blog::class,
        ];

        $modelClass = $classMap[$type];
        $morphAlias = $morphMap[$type];

        // 4. Item var mı kontrol et
        $item = $modelClass::find($itemId);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' bulunamadı',
                'error_code' => 'ITEM_NOT_FOUND'
            ], 404);
        }

        // 5. Zaten favoride mi kontrol et (morphAlias ile)
        $existing = Favorite::where('user_id', $userId)
            ->where('favoritable_type', $morphAlias)
            ->where('favoritable_id', $itemId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten favorilerde!',
                'error_code' => 'ALREADY_IN_FAVORITES'
            ], 409);
        }

        // 6. Favoriye ekle (morphAlias ile kaydet)
        Favorite::create([
            'user_id' => $userId,
            'favoritable_type' => $morphAlias,
            'favoritable_id' => $itemId,
        ]);

        // 7. Başarılı yanıt
        $typeTr = [
            'song' => 'Şarkı',
            'playlist' => 'Playlist',
            'album' => 'Albüm',
            'genre' => 'Tür',
            'sector' => 'Sektör',
            'radio' => 'Radyo',
            'blog' => 'Blog',
        ];

        return response()->json([
            'success' => true,
            'message' => $typeTr[$type] . ' favorilere eklendi! ❤️',
        ]);
    }
}
