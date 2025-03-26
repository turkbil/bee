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
    
    // Yetkiler için değişkenler
    public $moduleName; 
    public $modulePermType;
    public $previousRole = null;
    public $permissionTypes = ['view', 'create', 'update', 'delete']; // Dinamik olarak doldurulabilir
    
    // Detaylı yetkilendirme görünümü için
    public $showDetailedPermissions = false;
    public $modulePermissionCounts = [];

    protected $rules = [
        'inputs.name' => 'required|min:3',
        'inputs.email' => 'required|email',
        'inputs.password' => 'nullable|min:6',
        'inputs.is_active' => 'boolean',
        'inputs.role_id' => 'nullable|exists:roles,name',
        'inputs.permissions' => 'nullable|array',
        'inputs.permissions.*' => 'exists:permissions,id',
    ];

    // Listeners dizisi
    protected $listeners = ['refreshModules', 'clearAllModules', 'updateModules'];

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
       $this->loadPermissionTypes();
       
       // Kullanılabilir modülleri yükle
       $this->loadAvailableModules();
       
       if ($id) {
           $this->userId = $id;
           $user = User::with(['roles'])->findOrFail($id);
           
           $this->inputs['name'] = $user->name;
           $this->inputs['email'] = $user->email;
           $this->inputs['is_active'] = $user->is_active;
           $this->inputs['role_id'] = $user->roles->first() ? $user->roles->first()->name : null;
           $this->previousRole = $this->inputs['role_id']; // Önceki rolü sakla
           
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
               $this->calculateModulePermissionCounts();
           }
       }
    }
    
    /**
     * İzin tiplerini dinamik olarak yükle
     */
    protected function loadPermissionTypes()
    {
        // Bu fonksiyon daha sonra izin tiplerini dinamik olarak çekebilir
        // Şimdilik varsayılan izin tipleri kullanılıyor
        
        // İzin tipleri için etiketleri hazırla
        $this->permissionLabels = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Düzenleme',
            'delete' => 'Silme',
        ];
    }
    
    public function loadAvailableModules()
    {
        // Tenant için ilişkili modülleri al
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $tenantId = tenant()->id;
            
            // Tenant'a atanmış aktif modülleri al
            $this->availableModules = Module::with('tenants')
                ->where('modules.is_active', true)  // Tablo adını açıkça belirt
                ->whereHas('tenants', function ($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId)
                        ->where('module_tenants.is_active', true);  // Tablo adını açıkça belirt
                })
                ->orderBy('display_name')
                ->get();
        } else {
            // Central için tüm aktif modülleri al
            $this->availableModules = Module::where('is_active', true)
                ->orderBy('display_name')
                ->get();
        }
        
        // Her modül için varsayılan izinleri hazırla
        foreach ($this->availableModules as $module) {
            if (!isset($this->modulePermissions[$module->name])) {
                $permissions = ['enabled' => false];
                
                // Tüm izin tipleri için varsayılan değerleri ayarla
                foreach ($this->permissionTypes as $type) {
                    $permissions[$type] = false;
                }
                
                $this->modulePermissions[$module->name] = $permissions;
            }
        }
    }
    
    protected function loadUserModulePermissions($user)
    {
        // Tüm aktif modülleri al
        $modules = $this->availableModules;
    
        // Her modül için kullanıcının izinlerini kontrol et
        foreach ($modules as $module) {
            $modulePermissions = ['enabled' => false];
            
            // Tüm izin tipleri için varsayılan değerleri ayarla
            foreach ($this->permissionTypes as $type) {
                $modulePermissions[$type] = false;
            }
    
            // Kullanıcının modül bazlı izinlerini yükle
            $userModulePermissions = UserModulePermission::where('user_id', $user->id)
                ->where('module_name', $module->name)
                ->get();
    
            // Eğer izin yoksa, bu modül için izinleri varsayılan olarak bırak
            if ($userModulePermissions->isEmpty()) {
                $this->modulePermissions[$module->name] = $modulePermissions;
                continue;
            }
            
            // İzinleri kontrol et ve ayarla
            $hasAnyPermission = false;
            
            foreach ($userModulePermissions as $permission) {
                if ($permission->is_active) {
                    $modulePermissions[$permission->permission_type] = true;
                    $hasAnyPermission = true;
                }
            }
            
            // Herhangi bir izin aktifse, modülü de aktif olarak işaretle
            $modulePermissions['enabled'] = $hasAnyPermission;
            
            $this->modulePermissions[$module->name] = $modulePermissions;
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedInputsRoleId($value)
    {
        // Log role change
        Log::debug('Role changed from: ' . $this->previousRole . ' to: ' . $value);
        
        // Rol değişikliği yapıldığında tüm yetkileri sıfırla
        $this->clearAllModulePermissions();
        $this->inputs['permissions'] = [];
        
        // Normal kullanıcı (Üye) rolü seçildiğinde role_id'yi null olarak ayarla
        if ($value === 'user') {
            $this->inputs['role_id'] = null;
        }
        // Eğer rol editör ise modül izinleri bölümünü göster ve izinleri hazırla
        else if ($value === 'editor') {
            // Editör rolü seçildiğinde ilgili izinleri hazırla
            $this->prepareEditorPermissions();
            $this->calculateModulePermissionCounts();
        }
        
        $this->previousRole = $value; // Yeni rolü sakla
        $this->dispatch('roleChanged', $value);
    }

    /**
     * Editör rolü için gerekli izinleri hazırla
     */
    protected function prepareEditorPermissions()
    {
        // Editör rolü seçildiğinde varsayılan olarak tüm izinler kapalı olmalı
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            $newPermissions = ['enabled' => false];
            
            // Tüm izin tipleri için varsayılan değerleri ayarla
            foreach ($this->permissionTypes as $type) {
                $newPermissions[$type] = false;
            }
            
            $this->modulePermissions[$moduleName] = $newPermissions;
        }
    }
    
    /**
     * Her modül için izin sayılarını hesapla (ör. 3/4)
     */
    protected function calculateModulePermissionCounts()
    {
        $this->modulePermissionCounts = [];
        
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            // İzin tipi anahtarlarını al (enabled hariç)
            $permissionTypes = array_filter(array_keys($permissions), function($key) {
                return $key !== 'enabled';
            });
            
            // Toplam izin sayısı
            $totalPermissions = count($permissionTypes);
            
            // Seçili izin sayısı
            $selectedPermissions = 0;
            foreach ($permissionTypes as $type) {
                if ($permissions[$type]) {
                    $selectedPermissions++;
                }
            }
            
            // Modül için izin sayılarını kaydet
            $this->modulePermissionCounts[$moduleName] = [
                'selected' => $selectedPermissions,
                'total' => $totalPermissions
            ];
        }
    }

    public function clearAllModulePermissions()
    {
        Log::debug('Clearing all module permissions');
        
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            $newPermissions = ['enabled' => false];
            
            // Tüm izin tipleri için varsayılan değerleri ayarla
            foreach ($this->permissionTypes as $type) {
                $newPermissions[$type] = false;
            }
            
            $this->modulePermissions[$moduleName] = $newPermissions;
        }
        
        // İzin sayılarını güncelle
        $this->calculateModulePermissionCounts();
        
        $this->dispatch('modulePermissionsUpdated');
    }
    
    /**
     * Detaylı izinler panelini aç/kapat
     */
    public function toggleDetailedPermissions()
    {
        $this->showDetailedPermissions = !$this->showDetailedPermissions;
    }

    /**
     * Bilgileri kaydet ve gerekirse modül ismini kullan
     */
    public function setModuleName($name)
    {
        $this->moduleName = $name;
        Log::debug('Module name set to: ' . $name);
        $this->toggleModuleEnabled($name);
    }
    
    /**
     * İzin tipini ayarla ve gerekirse kullan
     */
    public function setModulePermType($module, $type)
    {
        $this->moduleName = $module;
        $this->modulePermType = $type;
        Log::debug('Module permission set', ['module' => $module, 'type' => $type]);
        $this->toggleModulePermission($module, $type);
    }

    /**
     * Modül etkinleştirme/devre dışı bırakma
     */
    public function toggleModuleEnabled($moduleName)
    {
        Log::debug('toggleModuleEnabled for: ' . $moduleName);
        
        try {
            if (!isset($this->modulePermissions[$moduleName])) {
                Log::error('modulePermissions dizisinde tanımlı olmayan modül', [
                    'moduleName' => $moduleName,
                    'availableModules' => array_keys($this->modulePermissions)
                ]);
                return;
            }
            
            // Mevcut durumu kontrol et
            $currentState = $this->modulePermissions[$moduleName]['enabled'];
            
            // Durumu değiştir
            if ($currentState) {
                // Eğer şu anda etkinse, tüm izinleri devre dışı bırak
                $this->modulePermissions[$moduleName]['enabled'] = false;
                
                foreach ($this->permissionTypes as $type) {
                    $this->modulePermissions[$moduleName][$type] = false;
                }
            } else {
                // Eğer şu anda etkin değilse, tüm izinleri etkinleştir
                $this->modulePermissions[$moduleName]['enabled'] = true;
                
                foreach ($this->permissionTypes as $type) {
                    $this->modulePermissions[$moduleName][$type] = true;
                }
            }
            
            // İzin sayılarını güncelle
            $this->calculateModulePermissionCounts();
            
            Log::debug('Module aktivasyonu', [
                'moduleName' => $moduleName,
                'permissions' => $this->modulePermissions[$moduleName]
            ]);
            
            // UI güncellemesi için event tetikle
            $this->dispatch('modulePermissionsUpdated');
            
        } catch (\Exception $e) {
            Log::error('toggleModuleEnabled error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function toggleModulePermission($moduleName, $permType)
    {
        try {
            Log::debug('toggleModulePermission called', [
                'module' => $moduleName,
                'type' => $permType
            ]);
            
            if (!isset($this->modulePermissions[$moduleName])) {
                Log::error('toggleModulePermission module not found', [
                    'moduleName' => $moduleName,
                    'availableModules' => array_keys($this->modulePermissions)
                ]);
                return;
            }
            
            // İlgili izni tersine çevir
            $currentValue = $this->modulePermissions[$moduleName][$permType];
            $this->modulePermissions[$moduleName][$permType] = !$currentValue;
            
            // Özel durum: Görüntüleme izni olmadan diğer izinler verilemez
            if ($permType === 'view' && !$this->modulePermissions[$moduleName]['view']) {
                // Görüntüleme izni kaldırıldığında, diğer tüm izinleri de kaldır
                foreach ($this->permissionTypes as $type) {
                    if ($type !== 'view') {
                        $this->modulePermissions[$moduleName][$type] = false;
                    }
                }
            }
            
            // Diğer izinler etkinleştirildiğinde görüntüleme izni de otomatik verilir
            if ($permType !== 'view' && $this->modulePermissions[$moduleName][$permType]) {
                $this->modulePermissions[$moduleName]['view'] = true;
            }
            
            // Herhangi bir izin aktif mi kontrol et
            $selectedPermissionsCount = 0;
            foreach ($this->permissionTypes as $type) {
                if ($this->modulePermissions[$moduleName][$type]) {
                    $selectedPermissionsCount++;
                }
            }
            
            // Modülün aktif/pasif durumunu güncelle - en az bir izin seçiliyse aktif kalsın
            $this->modulePermissions[$moduleName]['enabled'] = $selectedPermissionsCount > 0;
            
            // İzin sayılarını güncelle
            $this->calculateModulePermissionCounts();
            
            // UI güncellemesi için event tetikle
            $this->dispatch('modulePermissionsUpdated');
            
            Log::debug('Module permission toggled', [
                'module' => $moduleName,
                'permType' => $permType,
                'selectedPermissionsCount' => $selectedPermissionsCount,
                'moduleEnabled' => $this->modulePermissions[$moduleName]['enabled']
            ]);
            
        } catch (\Exception $e) {
            Log::error('toggleModulePermission error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
                
                $actionType = 'güncellendi';
                $logData = array_diff_assoc($data, $user->getOriginal());
            } else {
                $user = User::create($data);
                $actionType = 'oluşturuldu';
                $logData = $data;
            }
    
            // Avatar yükleme işlemi
            $this->handleImageUpload($user);
    
            if (isset($this->inputs['role_id']) && $this->inputs['role_id'] === 'user') {
                $this->inputs['role_id'] = null;
            }
            
            // Rol ve izin işlemleri
            $this->handleRoleAndPermissions($user);
    
            // Aktivite log kaydı
            if (function_exists('log_activity')) {
                log_activity($user, $actionType, $logData);
            }
    
            // Önbellekleri temizle
            Cache::forget("user_{$user->id}_accessible_modules");
            
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
                $this->reset(['inputs', 'temporaryImages', 'modulePermissions', 'modulePermissionCounts']);
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
            
            Log::error('Error saving user', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }
    
    protected function handleRoleAndPermissions($user)
    {
        try {
            Log::debug('Handling user roles and permissions', [
                'user_id' => $user->id,
                'selected_role' => $this->inputs['role_id'],
                'previous_role' => $this->previousRole
            ]);
            
            // Kullanıcının mevcut rollerini kontrol et
            $currentRoles = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->get();
                
            Log::debug('Current roles before deletion', [
                'user_id' => $user->id,
                'roles' => $currentRoles->toArray()
            ]);
            
            // 1. Önce tüm rolleri temizle 
            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->delete();
                
            Log::debug('Deleted all roles for user', [
                'user_id' => $user->id
            ]);
            
            // 2. Tüm direkt izinleri de temizle
            DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->delete();
                
            Log::debug('Deleted all direct permissions for user', [
                'user_id' => $user->id
            ]);
            
            // 3. Modül bazlı izinleri temizle
            $modulePermsDeleted = UserModulePermission::where('user_id', $user->id)->delete();
            
            Log::debug('Deleted module permissions for user', [
                'user_id' => $user->id,
                'count' => $modulePermsDeleted
            ]);
            
            // 4. Seçilen role göre işlem yap - 'user' rolü için hiçbir rol atanmamalı
            if (!empty($this->inputs['role_id']) && $this->inputs['role_id'] !== 'user') {
                $role = Role::where('name', $this->inputs['role_id'])->first();
                
                if ($role) {
                    // 4.1. Yeni rolü ekle (direkt DB query ile)
                    DB::table('model_has_roles')->insert([
                        'role_id' => $role->id,
                        'model_type' => User::class,
                        'model_id' => $user->id
                    ]);
                    
                    Log::debug('New role attached for user', [
                        'user_id' => $user->id,
                        'role' => $role->name,
                        'role_id' => $role->id
                    ]);
                    
                    // 4.2. Rol tipine göre izinleri ayarla
                    if ($role->name === 'editor') {
                        $this->saveModulePermissions($user);
                    } else {
                        Log::debug('Admin/Root role assigned, no additional permissions needed', [
                            'user_id' => $user->id
                        ]);
                    }
                }
            } else {
                // 'user' rolü veya rol seçilmediğinde hiçbir rol atanmaz
                Log::debug('User role or no role selected, no roles or permissions assigned', [
                    'user_id' => $user->id,
                    'role_id' => $this->inputs['role_id'] ?? 'null'
                ]);
            }
            
            // 5. Silme sonrası kontrol et
            $remainingRoles = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->get();
                
            Log::debug('Roles after operation', [
                'user_id' => $user->id,
                'roles_count' => $remainingRoles->count(),
                'roles' => $remainingRoles->toArray()
            ]);
            
            // 6. Önbellekleri temizle
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            
            foreach ($this->availableModules as $module) {
                foreach ($this->permissionTypes as $permissionType) {
                    Cache::forget("user_{$user->id}_module_{$module->name}_permission_{$permissionType}");
                }
                Cache::forget("user_{$user->id}_module_{$module->name}_permissions");
            }
            
            Cache::forget("user_{$user->id}_roles");
            Cache::forget("user_{$user->id}_permissions");
            
        } catch (\Exception $e) {
            Log::error('Error in handleRoleAndPermissions', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    protected function saveModulePermissions($user)
    {
        try {
            Log::debug('Saving module permissions for user', [
                'user_id' => $user->id,
                'permissions' => $this->modulePermissions
            ]);
            
            // Önce kullanıcının tüm modül izinlerini temizle
            $oldPermissions = UserModulePermission::where('user_id', $user->id)->get();
            UserModulePermission::where('user_id', $user->id)->delete();
            
            $createdPermissions = [];
            
            // Her modül için izinleri kontrol et ve kaydet
            foreach ($this->modulePermissions as $moduleName => $permissions) {
                // Modül etkinleştirilmişse veya herhangi bir izin aktifse
                if (isset($permissions['enabled']) && $permissions['enabled']) {
                    // CRUD izinleri
                    foreach ($this->permissionTypes as $permissionType) {
                        if (isset($permissions[$permissionType]) && $permissions[$permissionType]) {
                            Log::debug("Creating permission for module: $moduleName, type: $permissionType");
                            
                            $permissionRecord = UserModulePermission::create([
                                'user_id' => $user->id,
                                'module_name' => $moduleName,
                                'permission_type' => $permissionType,
                                'is_active' => true
                            ]);
                            
                            $createdPermissions[] = $permissionRecord->id;
                        }
                    }
                }
            }
            
            // Aktivite log kaydı
            if (function_exists('log_activity') && !empty($createdPermissions)) {
                log_activity(
                    $user,
                    'izinleri güncellendi',
                    [
                        'modül_izinleri' => count($createdPermissions) . ' adet izin güncellendi'
                    ]
                );
            }
            
            // Kullanıcı modül izinleri önbelleğini temizle
            $this->clearUserModulePermissionCache($user);
            
        } catch (\Exception $e) {
            Log::error('Error saving module permissions', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }
    
    protected function clearUserModulePermissionCache($user)
    {
        // Tüm modüller için önbelleği temizle
        foreach ($this->availableModules as $module) {
            foreach ($this->permissionTypes as $permissionType) {
                Cache::forget("user_{$user->id}_module_{$module->name}_permission_{$permissionType}");
            }
            Cache::forget("user_{$user->id}_module_{$module->name}_permissions");
        }
        
        // Genel önbellekler
        Cache::forget("user_{$user->id}_roles");
        Cache::forget("user_{$user->id}_permissions");
        Cache::forget("user_{$user->id}_accessible_modules");
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
        
        // Mevcut modül yetkilendirme durumunu kontrol et
        $hasModulePermissions = false;
        foreach ($this->modulePermissions as $moduleName => $permissions) {
            if ($permissions['enabled']) {
                $hasModulePermissions = true;
                break;
            }
        }

        return view('usermanagement::livewire.user-manage-component', [
            'groupedPermissions' => $groupedPermissions,
            'permissionLabels' => $permissionLabels,
            'moduleLabels' => $moduleLabels,
            'model' => $this->userId ? User::find($this->userId) : null,
            'hasModulePermissions' => $hasModulePermissions,
            'modulePermissionCounts' => $this->modulePermissionCounts
        ]);
    }
}