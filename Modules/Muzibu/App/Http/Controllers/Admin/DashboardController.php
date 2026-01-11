<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Muzibu Dashboard
     */
    public function index()
    {
        return view('muzibu::admin.dashboard-index');
    }
}
