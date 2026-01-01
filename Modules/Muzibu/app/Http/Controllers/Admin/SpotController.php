<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class SpotController extends Controller
{
    /**
     * Spot Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.spot-index');
    }

    /**
     * Spot Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.spot-manage', [
            'spotId' => $id
        ]);
    }
}
