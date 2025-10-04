<?php

namespace Modules\UserManagement\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class UserManagementController extends Controller
{
    /**
     * User management ana sayfası
     */
    public function index()
    {
        return view('usermanagement::admin.index');
    }
}
