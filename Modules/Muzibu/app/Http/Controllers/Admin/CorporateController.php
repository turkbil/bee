<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class CorporateController extends Controller
{
    /**
     * Kurumsal Hesap Listesi (Livewire Component)
     */
    public function index()
    {
        return view('muzibu::admin.corporate-index');
    }

    /**
     * Kurumsal Hesap Yönetim Sayfası (Livewire Component)
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.corporate-manage', [
            'corporateId' => $id
        ]);
    }

    /**
     * Kurumsal Abonelikler Sayfası
     */
    public function subscription()
    {
        return view('muzibu::admin.corporate-subscription');
    }

    /**
     * Kurumsal Kullanım Raporları Sayfası
     */
    public function usage()
    {
        return view('muzibu::admin.corporate-usage');
    }
}
