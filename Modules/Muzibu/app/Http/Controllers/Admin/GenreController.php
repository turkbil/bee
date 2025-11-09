<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class GenreController extends Controller
{
    /**
     * Genre Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.genre-index');
    }

    /**
     * Genre Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.livewire.genre-manage-component', [
            'id' => $id
        ]);
    }
}
