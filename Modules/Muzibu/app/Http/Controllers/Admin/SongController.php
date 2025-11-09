<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class SongController extends Controller
{
    /**
     * Song Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.song-index');
    }

    /**
     * Song Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.livewire.song-manage-component', [
            'id' => $id
        ]);
    }
}
