<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Artist;
use Modules\Muzibu\App\Models\Album;
use Modules\Muzibu\App\Models\Song;
use App\Services\SeoMetaTagService;

class ArtistController extends Controller
{
    public function index()
    {
        // Only show artists with at least 1 active album
        $artists = Artist::with(['photoMedia'])
            ->where('is_active', 1)
            ->whereHas('albums', function($q) {
                $q->where('is_active', 1);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(40);

        // Set custom pagination view
        $artists->setPath(request()->url());

        return view('themes.muzibu.artists.index', compact('artists'));
    }

    public function show($slug)
    {
        $artist = Artist::with(['photoMedia'])
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        // Get all albums by this artist
        $albums = Album::with(['coverMedia'])
            ->where('artist_id', $artist->artist_id)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all songs by this artist
        $songs = Song::with(['coverMedia', 'album'])
            ->whereHas('album', function($q) use ($artist) {
                $q->where('artist_id', $artist->artist_id);
            })
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // ⭐ SEO için model'i share et (HasSeo trait otomatik çalışır)
        view()->share('currentModel', $artist);

        // ⭐ Schema.org için item variable (HasUniversalSchemas trait)
        $item = $artist;

        return response()
            ->view('themes.muzibu.artists.show', compact('artist', 'albums', 'songs', 'item'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function apiIndex()
    {
        // Only show artists with at least 1 active album or song
        $artists = Artist::with(['photoMedia'])
            ->where('is_active', 1)
            ->where(function($query) {
                $query->whereHas('albums', function($q) {
                    $q->where('is_active', 1);
                })->orWhereHas('songs', function($q) {
                    $q->where('is_active', 1);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(40);
        $html = view('themes.muzibu.partials.artists-grid', compact('artists'))->render();

        return response()->json(['html' => $html, 'meta' => ['title' => 'Sanatçılar - Muzibu', 'description' => 'En popüler sanatçıları keşfedin']])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function apiShow($slug)
    {
        $artist = Artist::with(['photoMedia'])->where(function($q) use ($slug) { $q->where('slug->tr', $slug)->orWhere('slug->en', $slug); })->where('is_active', 1)->firstOrFail();

        $albums = Album::with(['coverMedia'])->where('artist_id', $artist->artist_id)->where('is_active', 1)->orderBy('created_at', 'desc')->get();

        $songs = Song::with(['coverMedia', 'album'])->whereHas('album', function($q) use ($artist) { $q->where('artist_id', $artist->artist_id); })->where('is_active', 1)->orderBy('created_at', 'desc')->get();

        $html = view('themes.muzibu.partials.artist-detail', compact('artist', 'albums', 'songs'))->render();
        $titleJson = @json_decode($artist->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $artist->title;

        return response()->json(['html' => $html, 'meta' => ['title' => $title . ' - Muzibu', 'description' => 'Sanatçının şarkılarını dinleyin']])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
