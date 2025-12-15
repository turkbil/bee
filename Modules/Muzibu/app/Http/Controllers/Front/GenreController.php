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
        // Only show genres with at least 1 active song
        $genres = Genre::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->paginate(200);

        return view('themes.muzibu.genres.index', compact('genres'));
    }

    public function show($slug)
    {
        $genre = Genre::with('iconMedia')
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = Song::with(['album', 'artist', 'coverMedia', 'album.coverMedia'])
            ->where('genre_id', $genre->genre_id)
            ->where('is_active', 1)
            ->orderBy('play_count', 'desc')
            ->paginate(200);

        return view('themes.muzibu.genres.show', compact('genre', 'songs'));
    }

    public function apiIndex()
    {
        // Only show genres with at least 1 active song
        $genres = Genre::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->paginate(200);
        $html = view('themes.muzibu.partials.genres-grid', compact('genres'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Müzik Türleri - Muzibu', 'description' => 'Tüm müzik türlerini keşfedin']]);
    }

    public function apiShow($slug)
    {
        $genre = Genre::with('iconMedia')->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();
        $songs = Song::with(['album', 'artist', 'coverMedia', 'album.coverMedia'])->where('genre_id', $genre->genre_id)->where('is_active', 1)->orderBy('play_count', 'desc')->paginate(200);
        $html = view('themes.muzibu.partials.genre-detail', compact('genre', 'songs'))->render();
        $titleJson = @json_decode($genre->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $genre->title;
        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Tür detaylarını inceleyin']]);
    }
}
