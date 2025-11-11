<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class PlaylistController extends Controller
{
    /**
     * Playlist Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.playlist-index');
    }

    /**
     * Playlist Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.playlist-manage', [
            'playlistId' => $id
        ]);
    }

    /**
     * Playlist Şarkı Yönetim Sayfası (Dual-List)
     */
    public function manageSongs($id)
    {
        return view('muzibu::admin.playlist-songs-manage', [
            'playlistId' => $id
        ]);
    }
}
