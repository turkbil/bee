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

#[Layout('admin.layout')]
class UserManageComponent extends Component
{
    use WithFileUploads;

    public $userId;
    public $inputs = [];
    public $allRoles;
    public $allPermissions;
    public $avatar;
    public $avatarUrl;
    
    // Modül bazlı izinler için
    public $modulePermissions = [];
    public $moduleDetails = [];
    public $availableModules = [];
    public $showModulePermissions = false;

    protected $rules = [
        'inputs.name' => 'required|min:3',
        'inputs.email' => 'required|email',
        'inputs.password' => 'nullable|min:6',
        'inputs.is_active' => 'boolean',
        'inputs.role_id' => 'nullable|exists:roles,name',
        'inputs.permissions' => 'nullable|array',
        'inputs.permissions.*' => 'exists:permissions,id',
    ];

    protected $listeners = ['refreshModulePermissions' => 'loadAvailableModules'];

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
           
           // Editör rolü seçiliyse modül izinlerini göster
           if ($this->inputs['role_id'] === 'editor') {
               $this->showModulePermissions = true;
           }
           
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
    
           if ($user->getFirstMedia('avatar')) {
               $this->avatar = null;
               $this->avatarUrl = $user->getFirstMediaUrl('avatar');
           }
           
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
            
            // Modül detaylarını başlangıçta kapalı hazırla
            if (!isset($this->moduleDetails[$module->name])) {
                $this->moduleDetails[$module->name] = false;
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

    public function toggleModuleDetails($moduleName)
    {
        if (!isset($this->moduleDetails[$moduleName])) {
            $this->moduleDetails[$moduleName] = true;
        } else {
            $this->moduleDetails[$moduleName] = !$this->moduleDetails[$moduleName];
        }
    }
    
    public function toggleModulePermission($moduleName, $type = null)
    {
        // Modülün etkinlik durumunu değiştir
        if ($type === null) {
            $this->modulePermissions[$moduleName]['enabled'] = !$this->modulePermissions[$moduleName]['enabled'];
            
            // Eğer modül devre dışı bırakıldıysa tüm alt izinleri kapat
            if (!$this->modulePermissions[$moduleName]['enabled']) {
                $this->modulePermissions[$moduleName]['view'] = false;
                $this->modulePermissions[$moduleName]['create'] = false;
                $this->modulePermissions[$moduleName]['update'] = false;
                $this->modulePermissions[$moduleName]['delete'] = false;
            } else {
                // Modül etkinleştirildiğinde en azından görüntüleme iznini aç
                $this->modulePermissions[$moduleName]['view'] = true;
            }
        } else {
            // Belirli bir iznin durumunu değiştir
            $this->modulePermissions[$moduleName][$type] = !$this->modulePermissions[$moduleName][$type];
            
            // Eğer herhangi bir izin aktifse, modülü etkinleştir
            if ($this->modulePermissions[$moduleName][$type]) {
                $this->modulePermissions[$moduleName]['enabled'] = true;
            } else {
                // Hiçbir izin kalmadıysa modülü devre dışı bırak
                $activePermissions = array_filter($this->modulePermissions[$moduleName], function($value, $key) {
                    return $key !== 'enabled' && $value === true;
                }, ARRAY_FILTER_USE_BOTH);
                
                if (empty($activePermissions)) {
                    $this->modulePermissions[$moduleName]['enabled'] = false;
                }
            }
        }
    }

    public function removeAvatar()
    {
        if ($this->avatar) {
            $this->avatar = null;
        } elseif ($this->userId) {
            $user = User::find($this->userId);
            if ($user && $user->getFirstMedia('avatar')) {
                $user->clearMediaCollection('avatar');
                $this->avatarUrl = null;
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Profil fotoğrafı kaldırıldı.',
                    'type' => 'success',
                ]);
            }
        }
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

            // Avatar işlemi
            if ($this->avatar) {
                $user->clearMediaCollection('avatar');
                $user->addMedia($this->avatar->getRealPath())
                    ->toMediaCollection('avatar');
            }

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
                $this->reset(['inputs', 'avatar', 'avatarUrl', 'modulePermissions']);
                $this->inputs = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'is_active' => true,
                    'role_id' => null,
                    'permissions' => []
                ];
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

    public function toggleModulePermissions($module)
    {
        $modulePermissions = $this->allPermissions
            ->filter(function($permission) use ($module) {
                return explode('.', $permission->name)[0] === $module;
            })
            ->pluck('id')
            ->toArray();

        // Eğer tüm izinler seçili ise, hepsini kaldır
        if (empty(array_diff($modulePermissions, $this->inputs['permissions']))) {
            $this->inputs['permissions'] = array_values(array_diff($this->inputs['permissions'], $modulePermissions));
        }
        // Değilse, eksik olanları ekle
        else {
            $this->inputs['permissions'] = array_values(array_unique(array_merge($this->inputs['permissions'], $modulePermissions)));
        }
    }

    public function updatedInputsRoleId($value)
    {
        // Rol değiştiğinde, eğer editor rolü seçildiyse modül izinlerini göster
        if ($value === 'editor') {
            $this->showModulePermissions = true;
        } else {
            $this->showModulePermissions = false;
        }
        
        // Editör rolü için modül izinlerini hazırla
        if ($value === 'editor') {
            $this->prepareModulePermissions();
        }
    }
    
    protected function prepareModulePermissions()
    {
        // Tüm modüller için boş izin şablonu oluştur (eğer yoksa)
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
            
            // Modül detaylarını başlangıçta aç
            $this->moduleDetails[$module->name] = true;
        }
    }

    public function render()
    {
        $groupedPermissions = $this->allPermissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        $permissionLabels = [
            'view' => 'Görüntüle',
            'create' => 'Oluştur',
            'update' => 'Düzenle',
            'delete' => 'Sil',
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