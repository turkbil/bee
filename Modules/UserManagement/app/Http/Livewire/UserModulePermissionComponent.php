<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Modules\ModuleManagement\App\Models\Module;
use Modules\UserManagement\App\Models\ModulePermission;
use Modules\UserManagement\App\Models\UserModulePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Permission;

#[Layout('admin.layout')]
class UserModulePermissionComponent extends Component
{
    use WithPagination;

    public $userId;
    public $search = '';
    public $selectedModule = null;
    public $userPermissions = [];
    
    public function mount($id)
    {
        $this->userId = $id;
        
        // Kullanıcı bulunamadıysa hata ver
        $user = User::find($this->userId);
        if (!$user) {
            session()->flash('error', 'Kullanıcı bulunamadı.');
            return redirect()->route('admin.usermanagement.index');
        }
        
        // İlk modülü seç
        $firstModule = Module::where('is_active', true)
            ->orderBy('display_name')
            ->first();
            
        if ($firstModule) {
            $this->selectModule($firstModule->name);
        }
    }
    
    public function selectModule($moduleName)
    {
        $this->selectedModule = $moduleName;
        $this->loadUserPermissions();
    }
    
    public function loadUserPermissions()
    {
        $permissionTypes = ModulePermission::getPermissionTypes();
        
        // Spatie'den bu modül için mevcut izinleri kontrol et
        $availablePermissions = Permission::where('name', 'like', $this->selectedModule . '.%')
            ->pluck('name')
            ->map(function($name) {
                $parts = explode('.', $name);
                return $parts[1] ?? null;
            })
            ->filter()
            ->unique()
            ->toArray();
        
        // Seçilen modüle ait kullanıcı izinlerini yükle
        $existingPermissions = UserModulePermission::where('user_id', $this->userId)
            ->where('module_name', $this->selectedModule)
            ->pluck('is_active', 'permission_type')
            ->toArray();
        
        // Kullanıcının doğrudan Spatie izinlerini de kontrol et
        $user = User::find($this->userId);
        $userPermissions = [];
        
        if ($user) {
            foreach ($permissionTypes as $type => $label) {
                $permissionName = "{$this->selectedModule}.{$type}";
                try {
                    if ($user->hasPermissionTo($permissionName)) {
                        $userPermissions[$type] = true;
                    }
                } catch (\Exception $e) {
                    // İzin bulunamadı
                }
            }
        }
        
        // İzin tiplerini düzenle
        $this->userPermissions = [];
        foreach ($permissionTypes as $type => $label) {
            // Kullanıcının doğrudan izni varsa veya özel modül izni varsa
            $hasDirectPermission = $userPermissions[$type] ?? false;
            $hasModulePermission = array_key_exists($type, $existingPermissions) ? $existingPermissions[$type] : false;
            
            // Sistemde bu izin tanımlı mı veya kullanıcı doğrudan bu izne sahip mi?
            if (in_array($type, $availablePermissions) || $hasDirectPermission) {
                $this->userPermissions[$type] = $hasDirectPermission || $hasModulePermission;
            }
        }
    }
    
    public function getPermissionIcon($type)
    {
        $icons = [
            'view' => 'eye',
            'create' => 'plus',
            'update' => 'edit',
            'delete' => 'trash'
        ];
        
        return $icons[$type] ?? 'key';
    }
    
    public function togglePermission($type)
    {
        // Mevcut durumu tersine çevir
        $this->userPermissions[$type] = !$this->userPermissions[$type];
        
        // Değişikliği veritabanına kaydet
        DB::beginTransaction();
        
        try {
            UserModulePermission::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'module_name' => $this->selectedModule,
                    'permission_type' => $type,
                ],
                [
                    'is_active' => $this->userPermissions[$type],
                ]
            );
            
            DB::commit();
            
            // Cache temizle
            Cache::forget("user_{$this->userId}_module_{$this->selectedModule}_permission_{$type}");
            Cache::forget("user_{$this->userId}_module_{$this->selectedModule}_permissions");
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => ($this->userPermissions[$type] ? 'Etkinleştirildi' : 'Devre dışı bırakıldı') . ': ' . ModulePermission::getPermissionTypes()[$type],
                'type' => 'success',
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            // Hata durumunda izin durumunu eski haline getir
            $this->userPermissions[$type] = !$this->userPermissions[$type];
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İzin durumu değiştirilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }
    
    public function render()
    {
        $modules = Module::where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where('display_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('display_name')
            ->get();
            
        $user = User::find($this->userId);
        
        $selectedModuleData = null;
        if ($this->selectedModule) {
            $selectedModuleData = Module::where('name', $this->selectedModule)->first();
        }
        
        return view('usermanagement::livewire.user-module-permission-component', [
            'modules' => $modules,
            'selectedModuleData' => $selectedModuleData,
            'user' => $user,
            'permissionLabels' => ModulePermission::getPermissionTypes()
        ]);
    }
}