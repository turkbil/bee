<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class StatsController extends Controller
{
    /**
     * Dinleme İstatistikleri Sayfası
     */
    public function index()
    {
        return view('muzibu::admin.stats-index');
    }
}
