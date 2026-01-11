<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class AlbumController extends Controller
{
    /**
     * Album Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.album-index');
    }

    /**
     * Album Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.album-manage', [
            'albumId' => $id
        ]);
    }
}
