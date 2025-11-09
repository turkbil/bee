<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class RadioController extends Controller
{
    /**
     * Radio Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.radio-index');
    }

    /**
     * Radio Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.livewire.radio-manage-component', [
            'id' => $id
        ]);
    }
}
