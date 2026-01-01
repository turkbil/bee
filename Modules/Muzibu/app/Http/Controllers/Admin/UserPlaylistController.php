<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class UserPlaylistController extends Controller
{
    /**
     * Kullanıcı Listeleri Sayfası
     */
    public function index()
    {
        return view('muzibu::admin.user-playlist-index');
    }
}
