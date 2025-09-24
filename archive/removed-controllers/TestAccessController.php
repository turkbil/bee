<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModuleAccessService;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\Auth;

class TestAccessController extends Controller
{
    protected $moduleAccessService;
    
    public function __construct(ModuleAccessService $moduleAccessService)
    {
        $this->moduleAccessService = $moduleAccessService;
    }
    
    /**
     * Kullanıcının erişim haklarını test et
     */
    public function testAccess(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Kullanıcı bilgileri
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'roles' => $user->roles->pluck('name')->toArray(),
            'is_root' => $user->isRoot(),
            'is_admin' => $user->isAdmin(),
            'is_editor' => $user->isEditor(),
        ];
        
        // Tenant bilgileri
        $tenantData = null;
        if (is_tenant()) {
            $tenant = tenant();
            $tenantData = [
                'id' => $tenant->id,
                'name' => $tenant->title ?? 'Bilinmeyen',
                'domain' => tenant_domain(),
                'is_active' => $tenant->is_active,
            ];
        }
        
        // Tüm modüller
        $modules = Module::where('is_active', true)->get();
        
        $moduleData = [];
        foreach ($modules as $module) {
            $canView = $this->moduleAccessService->canAccess($module->name, 'view');
            $canCreate = $this->moduleAccessService->canAccess($module->name, 'create');
            $canUpdate = $this->moduleAccessService->canAccess($module->name, 'update');
            $canDelete = $this->moduleAccessService->canAccess($module->name, 'delete');
            
            $moduleData[] = [
                'id' => $module->module_id,
                'name' => $module->name,
                'display_name' => $module->display_name,
                'type' => $module->type,
                'permissions' => [
                    'view' => $canView,
                    'create' => $canCreate,
                    'update' => $canUpdate,
                    'delete' => $canDelete,
                ],
                'routes' => [
                    'index' => route('admin.' . $module->name . '.index', [], false),
                ],
            ];
        }
        
        // Erişilebilir modüller
        $accessibleModules = $this->moduleAccessService->getUserAccessibleModules($user);
        
        return view('test-access', compact(
            'userData', 
            'tenantData', 
            'moduleData', 
            'accessibleModules'
        ));
    }
}