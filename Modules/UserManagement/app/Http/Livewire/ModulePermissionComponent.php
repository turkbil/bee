<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\ModuleManagement\App\Models\Module;
use Modules\UserManagement\App\Models\ModulePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Permission;

#[Layout('admin.layout')]
class ModulePermissionComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedModule = null;
    public $modulePermissions = [];
    public $isEditing = false;
    
    public function mount()
    {
        // Modül seçilmediyse, ilk modülü seç
        if (!$this->selectedModule) {
            $firstModule = Module::orderBy('display_name')->first();
            if ($firstModule) {
                $this->selectModule($firstModule->module_id);
            }
        }
    }
    
    public function selectModule($moduleId)
    {
        $this->selectedModule = $moduleId;
        $this->loadModulePermissions();
        $this->isEditing = false;
    }
    
    public function loadModulePermissions()
    {
        $module = Module::findOrFail($this->selectedModule);
        $permissionTypes = ModulePermission::getPermissionTypes();
        
        // Mevcut izinleri Spatie'den yükle
        $this->modulePermissions = [];
        
        foreach ($permissionTypes as $type => $label) {
            $permissionName = "{$module->name}.{$type}";
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'web')
                ->first();
            
            $this->modulePermissions[$type] = $permission ? true : false;
        }
    }
    
    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
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
    
    public function save()
    {
        DB::beginTransaction();
        
        try {
            $module = Module::findOrFail($this->selectedModule);
            
            foreach ($this->modulePermissions as $type => $isActive) {
                $permissionName = "{$module->name}.{$type}";
                
                if ($isActive) {
                    // İzin yoksa oluştur
                    Permission::firstOrCreate(
                        [
                            'name' => $permissionName,
                            'guard_name' => 'web',
                        ],
                        [
                            'description' => "{$module->display_name} - " . ModulePermission::getPermissionTypes()[$type]
                        ]
                    );
                } else {
                    // İzin varsa ve devre dışı yapılmışsa, sil
                    Permission::where('name', $permissionName)
                        ->where('guard_name', 'web')
                        ->delete();
                }
            }
            
            DB::commit();
            
            // Cache'i temizle
            $this->clearModulePermissionCache($module->name);
            
            $this->isEditing = false;
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Modül izinleri başarıyla kaydedildi.',
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
    
    protected function clearModulePermissionCache($moduleName)
    {
        // Helper fonksiyonu kullan (eğer yüklenmiş ise)
        if (function_exists('clear_module_permission_cache')) {
            clear_module_permission_cache($moduleName);
            return;
        }
        
        // Değilse manuel olarak temizle
        foreach (ModulePermission::getPermissionTypes() as $type => $label) {
            Cache::forget("module_{$moduleName}_permission_{$type}_active");
        }
        
        Cache::forget("module_{$moduleName}_permissions");
    }
    
    public function render()
    {
        $modules = Module::when($this->search, function ($query) {
                $query->where('display_name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('display_name')
            ->get();
            
        $selectedModuleData = null;
        if ($this->selectedModule) {
            $selectedModuleData = Module::find($this->selectedModule);
        }
        
        return view('usermanagement::livewire.module-permission-component', [
            'modules' => $modules,
            'selectedModuleData' => $selectedModuleData,
            'permissionLabels' => ModulePermission::getPermissionTypes()
        ]);
    }
}