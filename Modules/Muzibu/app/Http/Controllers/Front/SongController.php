<?php

namespace Modules\Muzibu\app\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Song;
use App\Services\SeoMetaTagService;

class SongController extends Controller
{
    /**
     * Display songs list
     */
    public function index()
    {
        $songs = Song::with(['artist', 'album', 'coverMedia'])
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(40);

        // Set custom pagination view
        $songs->setPath(request()->url());

        return view('themes.muzibu.songs.index', compact('songs'));
    }

    /**
     * Song detail page
     */
    public function show($slug)
    {
        $song = Song::with(['artist', 'album.artist', 'coverMedia', 'album.coverMedia'])
            ->where(function($query) use ($slug) {
                $query->where('slug->tr', $slug)
                      ->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        // Aynı albümden diğer şarkılar (önerilen şarkılar)
        $relatedSongs = Song::with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('album_id', $song->album_id)
            ->where('song_id', '!=', $song->song_id)
            ->where('is_active', 1)
            ->orderBy('song_id')
            ->limit(10)
            ->get();

        // ⭐ SEO için model'i share et (HasSeo trait otomatik çalışır)
        view()->share('currentModel', $song);

        return view('themes.muzibu.songs.show', compact('song', 'relatedSongs'));
    }

    /**
     * SPA API - Song detail
     */
    public function apiShow($slug)
    {
        $song = Song::with(['artist', 'album.artist', 'coverMedia', 'album.coverMedia'])
            ->where(function($q) use ($slug) {
                $q->where('slug->tr', $slug)->orWhere('slug->en', $slug);
            })
            ->where('is_active', 1)
            ->firstOrFail();

        // Aynı albümden diğer şarkılar
        $relatedSongs = Song::with(['artist', 'coverMedia', 'album.coverMedia'])
            ->where('album_id', $song->album_id)
            ->where('song_id', '!=', $song->song_id)
            ->where('is_active', 1)
            ->orderBy('song_id')
            ->limit(10)
            ->get();

        $html = view('themes.muzibu.partials.song-detail', compact('song', 'relatedSongs'))->render();

        $titleJson = @json_decode($song->title);
        $title = $titleJson && isset($titleJson->tr) ? $titleJson->tr : $song->title;

        return response()->json([
            'html' => $html,
            'meta' => [
                'title' => $title . ' - Muzibu',
                'description' => 'Şarkıyı dinleyin'
            ]
        ]);
    }
}
