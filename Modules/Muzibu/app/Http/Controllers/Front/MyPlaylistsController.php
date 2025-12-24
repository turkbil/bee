<?php

namespace Modules\Muzibu\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Models\Playlist;

class MyPlaylistsController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $playlists = Playlist::where('user_id', auth()->id())
            ->where('is_system', false)
            ->with('coverMedia') // ✅ Eager load cover media
            ->withCount('songs')
            ->latest()
            ->paginate(200);

        return view('themes.muzibu.playlists.my-playlists', compact('playlists'));
    }

    public function apiIndex()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $playlists = Playlist::where('user_id', auth()->id())->where('is_system', false)->with('coverMedia')->withCount('songs')->latest()->paginate(200);
        $html = view('themes.muzibu.partials.my-playlists-grid', compact('playlists'))->render();
        return response()->json(['html' => $html, 'meta' => ['title' => 'Çalma Listelerim - Muzibu', 'description' => 'Oluşturduğunuz çalma listeleri']]);
    }

    public function edit($slug)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // ✅ Slug ile ara (JSON field olduğu için LIKE kullan)
        $locale = app()->getLocale();
        $playlist = Playlist::with(['songs.album.artist', 'songs.album.coverMedia', 'coverMedia'])
            ->where('user_id', auth()->id())
            ->where('is_system', false)
            ->where('slug', 'LIKE', '%"' . $slug . '"%')
            ->firstOrFail();

        return view('themes.muzibu.playlists.edit', compact('playlist'));
    }
}
