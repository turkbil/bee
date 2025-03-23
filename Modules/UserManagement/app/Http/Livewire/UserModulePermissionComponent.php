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

#[Layout('admin.layout')]
class UserModulePermissionComponent extends Component
{
    use WithPagination;

    public $userId;
    public $search = '';
    public $selectedModule = null;
    public $userPermissions = [];
    public $isEditing = false;
    
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
        $this->isEditing = false;
    }
    
    public function loadUserPermissions()
    {
        $permissionTypes = ModulePermission::getPermissionTypes();
        $user = User::find($this->userId);
        
        // Seçilen modüle ait kullanıcı izinlerini yükle
        $existingPermissions = UserModulePermission::where('user_id', $this->userId)
            ->where('module_name', $this->selectedModule)
            ->pluck('is_active', 'permission_type')
            ->toArray();
        
        // Sadece bu modül için etkin olan izin tiplerini al
        $activeModulePermissions = ModulePermission::whereHas('module', function ($query) {
                $query->where('name', $this->selectedModule);
            })
            ->where('is_active', true)
            ->pluck('permission_type')
            ->toArray();
        
        // İzin tiplerini düzenle
        $this->userPermissions = [];
        foreach ($permissionTypes as $type => $label) {
            // Sadece modülde etkin olan izin tiplerini göster
            if (in_array($type, $activeModulePermissions)) {
                $this->userPermissions[$type] = array_key_exists($type, $existingPermissions) 
                    ? $existingPermissions[$type] 
                    : false;
            }
        }
    }
    
    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
    }
    
    public function save()
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($this->userId);
            
            foreach ($this->userPermissions as $type => $isActive) {
                UserModulePermission::updateOrCreate(
                    [
                        'user_id' => $this->userId,
                        'module_name' => $this->selectedModule,
                        'permission_type' => $type,
                    ],
                    [
                        'is_active' => $isActive,
                    ]
                );
            }
            
            DB::commit();
            
            // Cache temizle
            $permissionTypes = array_keys(ModulePermission::getPermissionTypes());
            foreach ($permissionTypes as $type) {
                Cache::forget("user_{$this->userId}_module_{$this->selectedModule}_permission_{$type}");
            }
            Cache::forget("user_{$this->userId}_module_{$this->selectedModule}_permissions");
            
            $this->isEditing = false;
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kullanıcı modül izinleri başarıyla kaydedildi.',
                'type' => 'success',
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İzinler kaydedilirken bir hata oluştu: ' . $e->getMessage(),
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