<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class SectorController extends Controller
{
    /**
     * Sector Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.sector-index');
    }

    /**
     * Sector Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.livewire.sector-manage-component', [
            'id' => $id
        ]);
    }
}
