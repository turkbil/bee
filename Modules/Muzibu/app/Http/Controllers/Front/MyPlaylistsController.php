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
            ->withCount('songs')
            ->latest()
            ->paginate(20);

        return view('themes.muzibu.playlists.my-playlists', compact('playlists'));
    }
}
