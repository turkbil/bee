<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;
use App\Services\SeoMetaTagService;

class AlbumController extends Controller
{
    public function index()
    {
        // Only show albums with at least 1 active song (alfabetik sıralı)
        $albums = Album::with(['artist', 'coverMedia'])
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->paginate(40);

        // Set custom pagination view
        $albums->setPath(request()->url());

        return view('themes.muzibu.albums.index', compact('albums'));
    }

    public function show($slug)
    {
        $album = Album::with(['artist', 'coverMedia'])
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        $songs = Song::with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('album_id', $album->album_id)
            ->where('is_active', 1)
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->get();

        // ⭐ SEO için model'i share et (HasSeo trait otomatik çalışır)
        view()->share('currentModel', $album);

        // ⭐ Schema.org için item variable (HasUniversalSchemas trait)
        $item = $album;

        return response()
            ->view('themes.muzibu.albums.show', compact('album', 'songs', 'item'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function apiIndex()
    {
        // Only show albums with at least 1 active song
        $albums = Album::with(['artist', 'coverMedia'])
            ->where('is_active', 1)
            ->whereHas('songs', function($q) {
                $q->where('is_active', 1);
            })
            ->withCount(['songs' => function($q) {
                $q->where('is_active', 1);
            }])
            ->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')
            ->paginate(40);
        $html = view('themes.muzibu.partials.albums-grid', compact('albums'))->render();

        return response()->json(['html' => $html, 'meta' => ['title' => 'Albümler - Muzibu', 'description' => 'En yeni albümleri keşfedin']])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function apiShow($slug)
    {
        $album = Album::with(['artist', 'coverMedia'])->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();
        $songs = Song::with(['artist', 'coverMedia', 'album.coverMedia'])->where('album_id', $album->album_id)->where('is_active', 1)->orderByRaw('LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, "$.tr")))')->get();
        $html = view('themes.muzibu.partials.album-detail', compact('album', 'songs'))->render();
        $titleJson = @json_decode($album->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $album->title;

        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Albümü dinleyin']])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
