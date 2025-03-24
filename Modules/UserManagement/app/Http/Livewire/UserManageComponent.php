<?php

namespace Modules\UserManagement\App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\App\Http\Livewire\Traits\WithImageUpload;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Modules\ModuleManagement\App\Models\Module;
use Modules\UserManagement\App\Models\UserModulePermission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class UserManageComponent extends Component
{
    use WithFileUploads, WithImageUpload;

    public $userId;
    public $inputs = [];
    public $allRoles;
    public $allPermissions;
    public $temporaryImages = [];
    
    // Modül izinleri için
    public $modulePermissions = [];
    public $availableModules = [];
    public $activeTab = 'profile';

    protected $rules = [
        'inputs.name' => 'required|min:3',
        'inputs.email' => 'required|email',
        'inputs.password' => 'nullable|min:6',
        'inputs.is_active' => 'boolean',
        'inputs.role_id' => 'nullable|exists:roles,name',
        'inputs.permissions' => 'nullable|array',
        'inputs.permissions.*' => 'exists:permissions,id',
    ];

    protected $listeners = [
        'refreshModulePermissions' => 'loadAvailableModules',
        'clearModulePermissions' => 'clearAllModulePermissions',
        'toggleModuleAll',
        'togglePermission' => 'toggleSinglePermission'
    ];

    public function mount($id = null)
    {
       $this->inputs = [
           'name' => '',
           'email' => '',
           'password' => '',
           'is_active' => true,
           'role_id' => null,
           'permissions' => []
       ];
    
       $this->allRoles = Role::all();
       // Root olmayan kullanıcılara root rolünü gösterme
       if (!auth()->user()->hasRole('root')) {
           $this->allRoles = $this->allRoles->filter(function($role) {
               return $role->name !== 'root';
           });
       }
       
       $this->allPermissions = Permission::all();
       
       // Kullanılabilir modülleri yükle
       $this->loadAvailableModules();
       
       if ($id) {
           $this->userId = $id;
           $user = User::with(['roles'])->findOrFail($id);
           
           $this->inputs['name'] = $user->name;
           $this->inputs['email'] = $user->email;
           $this->inputs['is_active'] = $user->is_active;
           $this->inputs['role_id'] = $user->roles->first() ? $user->roles->first()->name : null;
           
           $permissions = DB::table('model_has_permissions')
               ->where('model_id', $id)
               ->where('model_type', User::class)
               ->pluck('permission_id')
               ->map(function($id) {
                   return (string) $id;
               })
               ->toArray();
    
           $this->inputs['permissions'] = $permissions;
           $this->inputs['password'] = '';
    
           // Kullanıcının modül bazlı izinlerini yükle (eğer editor rolü varsa)
           if ($user->roles->first() && $user->roles->first()->name === 'editor') {
               $this->loadUserModulePermissions($user);
           }
       }
    }
    
    public function loadAvailableModules()
    {
        // Kullanılabilir modülleri yükle
        $this->availableModules = Module::where('is_active', true)
            ->orderBy('display_name')
            ->get();
            
        // Her modül için varsayılan izinleri hazırla
        foreach ($this->availableModules as $module) {
            if (!isset($this->modulePermissions[$module->name])) {
                $this->modulePermissions[$module->name] = [
                    'enabled' => false,
                    'view' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false
                ];
            }
        }
    }
    
    protected function loadUserModulePermissions($user)
    {
        // Tüm aktif modülleri al
        $modules = Module::where('is_active', true)->get();
    
        // Her modül için kullanıcının izinlerini kontrol et
        foreach ($modules as $module) {
            $modulePermissions = [
                'enabled' => false,
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => false
            ];
    
            // Kullanıcının modül bazlı izinlerini yükle
            $userModulePermissions = UserModulePermission::where('user_id', $user->id)
                ->where('module_name', $module->name)
                ->get();
    
            foreach ($userModulePermissions as $permission) {
                if ($permission->permission_type === 'view' && $permission->is_active) {
                    $modulePermissions['view'] = true;
                    $modulePermissions['enabled'] = true;
                } elseif ($permission->permission_type === 'create' && $permission->is_active) {
                    $modulePermissions['create'] = true;
                    $modulePermissions['enabled'] = true;
                } elseif ($permission->permission_type === 'update' && $permission->is_active) {
                    $modulePermissions['update'] = true;
                    $modulePermissions['enabled'] = true;
                } elseif ($permission->permission_type === 'delete' && $permission->is_active) {
                    $modulePermissions['delete'] = true;
                    $modulePermissions['enabled'] = true;
                }
            }
    
            $this->modulePermissions[$module->name] = $modulePermissions;
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedInputsRoleId($value)
    {
        // Eğer rol editör değilse, izinleri sıfırla
        if ($value !== 'editor') {
            $this->clearAllModulePermissions();
        }
        
        $this->dispatch('roleChanged', $value);
    }

    public function clearAllModulePermissions()
    {
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            $this->modulePermissions[$moduleName] = [
                'enabled' => false,
                'view' => false,
                'create' => false,
                'update' => false,
                'delete' => false
            ];
        }
        
        $this->dispatch('modulePermissionsUpdated');
    }

    /**
     * Modül izinlerinin tümünü aç/kapat
     */
    public function toggleModuleAll($params)
    {
        Log::debug('toggleModuleAll çağrıldı', [
            'params' => $params,
            'type' => gettype($params)
        ]);
        
        try {
            // Gelen parametre string ise (sadece modül adı)
            if (is_string($params)) {
                $moduleName = $params;
            }
            // Gelen parametre array ise (Livewire olayı)
            elseif (is_array($params) && isset($params['module'])) {
                $moduleName = $params['module'];
            } 
            // Farklı bir format gelirse 
            else {
                Log::error('toggleModuleAll için geçersiz parametre formatı', [
                    'params' => $params,
                    'type' => gettype($params)
                ]);
                return;
            }
            
            Log::debug('toggleModuleAll için modül adı', [
                'moduleName' => $moduleName
            ]);
            
            // ModulePermissions dizisinde bu modül tanımlı mı kontrol et
            if (!isset($this->modulePermissions[$moduleName])) {
                Log::error('modulePermissions dizisinde tanımlı olmayan modül', [
                    'moduleName' => $moduleName,
                    'availableModules' => array_keys($this->modulePermissions)
                ]);
                return;
            }
            
            // Mevcut durumun tersini al
            $isEnabled = !$this->modulePermissions[$moduleName]['enabled'];
            
            // Modül izinlerini güncelle
            $this->modulePermissions[$moduleName]['enabled'] = $isEnabled;
            $this->modulePermissions[$moduleName]['view'] = $isEnabled;
            $this->modulePermissions[$moduleName]['create'] = $isEnabled;
            $this->modulePermissions[$moduleName]['update'] = $isEnabled;
            $this->modulePermissions[$moduleName]['delete'] = $isEnabled;
            
            Log::debug('Modül izinleri güncellendi', [
                'moduleName' => $moduleName,
                'isEnabled' => $isEnabled
            ]);
            
            // Frontend'e bildir
            $this->dispatch('modulePermissionsUpdated');
            
        } catch (\Exception $e) {
            Log::error('toggleModuleAll hata', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    // Frontend'den gelen tekil izin toggle olaylarını yakala
    public function toggleSinglePermission($params)
    {
        try {
            if (is_string($params)) {
                Log::error('toggleSinglePermission string param', ['params' => $params]);
                return;
            }
            
            if (!isset($params['module']) || !isset($params['type'])) {
                Log::error('toggleSinglePermission eksik param', ['params' => $params]);
                return;
            }
            
            $moduleName = $params['module'];
            $permType = $params['type'];
            
            if (!isset($this->modulePermissions[$moduleName])) {
                Log::error('toggleSinglePermission modül bulunamadı', [
                    'moduleName' => $moduleName,
                    'availableModules' => array_keys($this->modulePermissions)
                ]);
                return;
            }
            
            $this->modulePermissions[$moduleName][$permType] = !$this->modulePermissions[$moduleName][$permType];
            
            // Eğer herhangi bir izin aktifse, modülü de aktif yap
            if ($this->modulePermissions[$moduleName][$permType]) {
                $this->modulePermissions[$moduleName]['enabled'] = true;
            } else {
                // Hiçbir izin kalmadıysa modülü pasif yap
                $hasAnyPermission = false;
                foreach (['view', 'create', 'update', 'delete'] as $type) {
                    if ($this->modulePermissions[$moduleName][$type]) {
                        $hasAnyPermission = true;
                        break;
                    }
                }
                
                if (!$hasAnyPermission) {
                    $this->modulePermissions[$moduleName]['enabled'] = false;
                }
            }
            
            $this->dispatch('modulePermissionsUpdated');
        } catch (\Exception $e) {
            Log::error('toggleSinglePermission hata', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function toggleActiveStatus()
    {
        $this->inputs['is_active'] = !$this->inputs['is_active']; 
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = collect($this->inputs)->except(['role_id', 'permissions'])->toArray();

            if (!empty($this->inputs['password'])) {
                $data['password'] = Hash::make($this->inputs['password']);
            } else {
                unset($data['password']);
            }

            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($data);
            } else {
                $user = User::create($data);
            }

            // Avatar yükleme işlemi
            $this->handleImageUpload($user);

            // Rol atama
            if (!empty($this->inputs['role_id'])) {
                $role = Role::where('name', $this->inputs['role_id'])->first();
                if ($role) {
                    $user->syncRoles([$role->name]);
                    
                    // Eğer rol editor ise, modül bazlı izinleri işle
                    if ($role->name === 'editor') {
                        $this->saveModulePermissions($user);
                    } else {
                        // Editor rolü değilse, modül izinlerini temizle
                        UserModulePermission::where('user_id', $user->id)->delete();
                    }
                }
            } else {
                $user->syncRoles([]);
                // Rol yoksa, modül izinlerini de temizle
                UserModulePermission::where('user_id', $user->id)->delete();
            }

            // İzin atama (sadece editor rolü değilse)
            if (empty($this->inputs['role_id']) || $this->inputs['role_id'] !== 'editor') {
                if (!empty($this->inputs['permissions'])) {
                    $permissions = Permission::whereIn('id', $this->inputs['permissions'])->get();
                    $user->syncPermissions($permissions);
                } else {
                    $user->syncPermissions([]);
                }
            }

            DB::commit();

            $message = $this->userId ? 'Kullanıcı başarıyla güncellendi.' : 'Kullanıcı başarıyla oluşturuldu.';

            if ($redirect) {
                session()->flash('toast', [
                    'title' => 'Başarılı!',
                    'message' => $message,
                    'type' => 'success',
                ]);
                return redirect()->route('admin.usermanagement.index');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);

            if ($resetForm && !$this->userId) {
                $this->reset(['inputs', 'temporaryImages', 'modulePermissions']);
                $this->inputs = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'is_active' => true,
                    'role_id' => null,
                    'permissions' => []
                ];
                $this->loadAvailableModules();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }
    
    protected function saveModulePermissions($user)
    {
        // Önce kullanıcının tüm modül izinlerini temizle
        UserModulePermission::where('user_id', $user->id)->delete();
        
        // Her modül için izinleri kontrol et ve kaydet
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            // Modül etkinleştirilmişse
            if (isset($permissions['enabled']) && $permissions['enabled']) {
                // CRUD izinleri
                foreach (['view', 'create', 'update', 'delete'] as $permissionType) {
                    if (isset($permissions[$permissionType]) && $permissions[$permissionType]) {
                        UserModulePermission::create([
                            'user_id' => $user->id,
                            'module_name' => $moduleName,
                            'permission_type' => $permissionType,
                            'is_active' => true
                        ]);
                    }
                }
            }
        }
        
        // Kullanıcı modül izinleri önbelleğini temizle
        $this->clearUserModulePermissionCache($user);
    }
    
    protected function clearUserModulePermissionCache($user)
    {
        // Tüm modüller için önbelleği temizle
        foreach ($this->availableModules as $module) {
            foreach (['view', 'create', 'update', 'delete'] as $permissionType) {
                Cache::forget("user_{$user->id}_module_{$module->name}_permission_{$permissionType}");
            }
            Cache::forget("user_{$user->id}_module_{$module->name}_permissions");
        }
    }

    public function toggleAllModulePermissions($module)
    {
        $modulePermissions = $this->allPermissions
            ->filter(function($permission) use ($module) {
                return explode('.', $permission->name)[0] === $module;
            })
            ->pluck('id')
            ->toArray();

        // Eğer tüm izinler seçili ise, hepsini kaldır
        if (count(array_intersect($modulePermissions, $this->inputs['permissions'])) === count($modulePermissions)) {
            $this->inputs['permissions'] = array_values(array_diff($this->inputs['permissions'], $modulePermissions));
        }
        // Değilse, eksik olanları ekle
        else {
            $this->inputs['permissions'] = array_values(array_unique(array_merge($this->inputs['permissions'], $modulePermissions)));
        }
    }

    public function render()
    {
        $groupedPermissions = $this->allPermissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        $permissionLabels = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Düzenleme',
            'delete' => 'Silme',
        ];

        // Modül etiketlerini dinamik olarak oluştur
        $moduleLabels = Module::pluck('display_name', 'name')->toArray();

        return view('usermanagement::livewire.user-manage-component', [
            'groupedPermissions' => $groupedPermissions,
            'permissionLabels' => $permissionLabels,
            'moduleLabels' => $moduleLabels,
            'model' => $this->userId ? User::find($this->userId) : null,
        ]);
    }
}