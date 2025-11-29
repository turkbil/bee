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

        $type = $request->get('type', 'all');
        $userId = auth()->id();

        $query = Favorite::where('user_id', $userId)
            ->with('favoritable')
            ->latest();

        if ($type !== 'all') {
            $modelMap = [
                'songs' => Song::class,
                'albums' => Album::class,
                'playlists' => Playlist::class,
            ];
            if (isset($modelMap[$type])) {
                $query->where('favoritable_type', $modelMap[$type]);
            }
        }

        $favorites = $query->paginate(20);

        return view('themes.muzibu.favorites.index', compact('favorites', 'type'));
    }
}
