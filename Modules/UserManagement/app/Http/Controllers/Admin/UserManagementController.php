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

    /**
     * User yönetim/düzenleme sayfası
     */
    public function manage($id = null)
    {
        return view('usermanagement::admin.manage', compact('id'));
    }
}
