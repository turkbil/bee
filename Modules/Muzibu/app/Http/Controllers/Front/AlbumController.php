<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;

class AlbumController extends Controller
{
    public function index()
    {
        $albums = Album::with('artist')
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('themes.muzibu.albums.index', compact('albums'));
    }

    public function show($slug)
    {
        $album = Album::with('artist')
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = Song::with('artist')
            ->where('album_id', $album->album_id)
            ->where('is_active', 1)
            ->orderBy('track_number')
            ->get();

        return view('themes.muzibu.albums.show', compact('album', 'songs'));
    }
}
