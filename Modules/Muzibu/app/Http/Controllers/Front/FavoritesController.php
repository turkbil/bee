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
            return redirect()->route('login');
        }

        $userId = auth()->id();

        // Count her tip için
        $modelMap = [
            'songs' => Song::class,
            'albums' => Album::class,
            'playlists' => Playlist::class,
            'genres' => \Modules\Muzibu\App\Models\Genre::class,
            'sectors' => \Modules\Muzibu\App\Models\Sector::class,
            'radios' => \Modules\Muzibu\App\Models\Radio::class,
            'blogs' => \Modules\Blog\App\Models\Blog::class,
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
            $modelMap = [
                'songs' => Song::class,
                'albums' => Album::class,
                'playlists' => Playlist::class,
                'genres' => \Modules\Muzibu\App\Models\Genre::class,
                'sectors' => \Modules\Muzibu\App\Models\Sector::class,
                'radios' => \Modules\Muzibu\App\Models\Radio::class,
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

        // 3. Model class mapping
        $modelMap = [
            'song' => Song::class,
            'playlist' => Playlist::class,
            'album' => Album::class,
            'genre' => \Modules\Muzibu\App\Models\Genre::class,
            'sector' => \Modules\Muzibu\App\Models\Sector::class,
            'radio' => \Modules\Muzibu\App\Models\Radio::class,
            'blog' => \Modules\Blog\App\Models\Blog::class,
        ];

        $modelClass = $modelMap[$type];

        // 4. Item var mı kontrol et
        $item = $modelClass::find($itemId);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => ucfirst($type) . ' bulunamadı',
                'error_code' => 'ITEM_NOT_FOUND'
            ], 404);
        }

        // 5. Zaten favoride mi kontrol et
        $existing = Favorite::where('user_id', $userId)
            ->where('favoritable_type', $modelClass)
            ->where('favoritable_id', $itemId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Zaten favorilerde!',
                'error_code' => 'ALREADY_IN_FAVORITES'
            ], 409);
        }

        // 6. Favoriye ekle
        Favorite::create([
            'user_id' => $userId,
            'favoritable_type' => $modelClass,
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
