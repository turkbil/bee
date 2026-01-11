<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class ArtistController extends Controller
{
    /**
     * Artist Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.artist-index');
    }

    /**
     * Artist Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.artist-manage', [
            'artistId' => $id
        ]);
    }
}
