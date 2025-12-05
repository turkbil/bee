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
        // Only show albums with at least 1 active song
        $albums = Album::with('artist')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(200);

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

        $songs = Song::with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('album_id', $album->album_id)
            ->where('is_active', 1)
            ->orderBy('song_id')
            ->get();

        return view('themes.muzibu.albums.show', compact('album', 'songs'));
    }

    public function apiIndex()
    {
        // Only show albums with at least 1 active song
        $albums = Album::with('artist')
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(200);
        $html = view('themes.muzibu.partials.albums-grid', compact('albums'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Albümler - Muzibu', 'description' => 'En yeni albümleri keşfedin']]);
    }

    public function apiShow($slug)
    {
        $album = Album::with('artist')->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();
        $songs = Song::with('artist')->where('album_id', $album->album_id)->where('is_active', 1)->orderBy('song_id')->get();
        $html = view('themes.muzibu.partials.album-detail', compact('album', 'songs'))->render();
        $titleJson = @json_decode($album->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $album->title;
        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Albümü dinleyin']]);
    }
}
