<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\ModuleManagement\App\Models\Module;
use Modules\UserManagement\App\Models\ModulePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

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
        $permissionTypes = ModulePermission::getPermissionTypes();
        $module = Module::findOrFail($this->selectedModule);
        
        // Mevcut izinleri yükle
        $existingPermissions = ModulePermission::where('module_id', $this->selectedModule)
            ->pluck('is_active', 'permission_type')
            ->toArray();
        
        // İzin tiplerini düzenle
        $this->modulePermissions = [];
        foreach ($permissionTypes as $type => $label) {
            $this->modulePermissions[$type] = array_key_exists($type, $existingPermissions) 
                ? $existingPermissions[$type] 
                : false;
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
            foreach ($this->modulePermissions as $type => $isActive) {
                ModulePermission::updateOrCreate(
                    [
                        'module_id' => $this->selectedModule,
                        'permission_type' => $type,
                    ],
                    [
                        'is_active' => $isActive,
                    ]
                );
            }
            
            DB::commit();
            
            // Cache temizle
            Cache::forget("module_{$this->selectedModule}_permissions");
            
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