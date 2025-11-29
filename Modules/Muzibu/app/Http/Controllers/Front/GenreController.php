<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Genre;
use Modules\Muzibu\App\Models\Song;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::where('is_active', 1)
            ->withCount('songs')
            ->paginate(20);

        return view('themes.muzibu.genres.index', compact('genres'));
    }

    public function show($slug)
    {
        $genre = Genre::where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = Song::with(['album', 'artist'])
            ->where('genre_id', $genre->genre_id)
            ->where('is_active', 1)
            ->orderBy('play_count', 'desc')
            ->paginate(50);

        return view('themes.muzibu.genres.show', compact('genre', 'songs'));
    }
}
