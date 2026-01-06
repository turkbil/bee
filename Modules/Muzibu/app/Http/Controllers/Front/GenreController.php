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
        // Only show genres with at least 1 active playlist (alfabetik sıralı)
        $genres = Genre::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('playlists', function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            })
            ->withCount(['playlists' => function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
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

        // Only show playlists with active songs (son eklenenler önce)
        $playlists = $genre->playlists()
            ->with('coverMedia')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->withSum(['songs' => function($q) {
                $q->where('is_active', 1);
            }], 'duration')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('themes.muzibu.genres.show', compact('genre', 'playlists'));
    }

    public function apiIndex()
    {
        // Only show genres with at least 1 active playlist
        $genres = Genre::with('iconMedia')
            ->where('is_active', 1)
            ->whereHas('playlists', function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            })
            ->withCount(['playlists' => function($q) {
                $q->where('is_active', 1)
                  ->whereHas('songs', function($sq) {
                      $sq->where('is_active', 1);
                  });
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->paginate(200);
        $html = view('themes.muzibu.partials.genres-grid', compact('genres'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Müzik Türleri - Muzibu', 'description' => 'Tüm müzik türlerini keşfedin']]);
    }

    public function apiShow($slug)
    {
        $genre = Genre::with('iconMedia')->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();

        // Only show playlists with active songs (son eklenenler önce)
        $playlists = $genre->playlists()
            ->with('coverMedia')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->withSum(['songs' => function($q) {
                $q->where('is_active', 1);
            }], 'duration')
            ->orderBy('created_at', 'desc')
            ->get();

        $html = view('themes.muzibu.partials.genre-detail', compact('genre', 'playlists'))->render();
        $titleJson = @json_decode($genre->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $genre->title;
        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Tür detaylarını inceleyin']]);
    }
}
