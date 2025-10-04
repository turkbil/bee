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

    /**
     * Modül izinleri sayfası
     */
    public function modulePermissions()
    {
        return view('usermanagement::admin.module-permissions');
    }

    /**
     * Kullanıcı modül izinleri sayfası
     */
    public function userModulePermissions($id)
    {
        return view('usermanagement::admin.user-module-permissions', compact('id'));
    }

    /**
     * Aktivite log kayıtları
     */
    public function activityLogs()
    {
        return view('usermanagement::admin.activity-logs');
    }

    /**
     * Kullanıcı aktivite log kayıtları
     */
    public function userActivityLogs($id)
    {
        return view('usermanagement::admin.user-activity-logs', compact('id'));
    }

    /**
     * Roller listesi
     */
    public function roleIndex()
    {
        return view('usermanagement::admin.role.index');
    }

    /**
     * Rol yönetim/düzenleme
     */
    public function roleManage($id = null)
    {
        return view('usermanagement::admin.role.manage', compact('id'));
    }

    /**
     * İzinler listesi
     */
    public function permissionIndex()
    {
        return view('usermanagement::admin.permission.index');
    }

    /**
     * İzin yönetim/düzenleme
     */
    public function permissionManage($id = null)
    {
        return view('usermanagement::admin.permission.manage', compact('id'));
    }
}
