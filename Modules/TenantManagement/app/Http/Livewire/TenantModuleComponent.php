<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use App\Models\Tenant; 
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantModuleComponent extends Component
{
   public $selectedModules = [];
   public $tenantId;
   public $modules;
   public $isSaving = false;

   public function mount($tenantId = null)
   {
       $this->tenantId = $tenantId;
       $this->loadModules();
   }

   public function loadModules()
   {
       try {
           $this->modules = Module::orderBy('display_name')->get();

           if ($this->tenantId) {
               $assignedModules = DB::table('module_tenants')
                   ->where('tenant_id', $this->tenantId)
                   ->pluck('module_id')
                   ->map(function($id) { 
                       return (string)$id; // ID'leri string'e çevir
                   })
                   ->toArray();
               
               $this->selectedModules = $assignedModules;
           }
       } catch (\Exception $e) {
           Log::error('Modülleri yükleme hatası: ' . $e->getMessage());
           $this->modules = collect();
           $this->selectedModules = [];
       }
   }

   public function toggleSelectAll()
   {
       if (count($this->selectedModules) === $this->modules->count()) {
           $this->selectedModules = [];
       } else {
           $this->selectedModules = $this->modules
               ->pluck('module_id')
               ->map(function($id) { 
                   return (string)$id; 
               })
               ->toArray();
       }
   }

   public function save()
   {
       if (!$this->tenantId) {
           $this->dispatch('toast', [
               'title' => 'Hata!',
               'message' => 'Tenant ID bulunamadı. İşlem iptal edildi.',
               'type' => 'error'
           ]);
           return;
       }
       
       $this->isSaving = true;

       try {
           DB::transaction(function () {
               $tenant = Tenant::findOrFail($this->tenantId);
               
               if (!Schema::hasTable('module_tenants')) {
                   throw new \Exception("Tenant modülleri tablosu bulunamadı.");
               }
               
               DB::table('module_tenants')
                   ->where('tenant_id', $this->tenantId)
                   ->delete();
               
               $modulesToAdd = [];
               foreach ($this->selectedModules as $moduleId) {
                   $modulesToAdd[] = [
                       'tenant_id' => $this->tenantId,
                       'module_id' => $moduleId,
                       'is_active' => true,
                       'created_at' => now(),
                       'updated_at' => now()
                   ];
               }
               
               if (!empty($modulesToAdd)) {
                   DB::table('module_tenants')->insert($modulesToAdd);
               }
               
               activity()
                   ->performedOn($tenant)
                   ->causedBy(auth()->user())
                   ->log("modülleri güncellendi");
               
               Cache::forget("modules_tenant_" . $tenant->id);
           });
           
           $this->dispatch('toast', [
               'title' => 'Başarılı!',
               'message' => 'Modül atamaları güncellendi.',
               'type' => 'success'
           ]);

           $this->dispatch('hideModal', ['id' => 'modal-module-management']);
           $this->dispatch('modulesSaved');
       } catch (\Exception $e) {
           Log::error('Modül atama hatası: ' . $e->getMessage());
           
           $this->dispatch('toast', [
               'title' => 'Hata!',
               'message' => 'Modül atamaları güncellenirken bir hata oluştu: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
       
       $this->isSaving = false;
   }

   public function render()
   {
       $modulesByType = $this->modules ? $this->modules->groupBy('type') : collect();
       $typeOrder = ['content', 'management', 'system'];
       $orderedModuleGroups = collect();
       
       foreach ($typeOrder as $type) {
           if ($modulesByType->has($type)) {
               $orderedModuleGroups[$type] = $modulesByType[$type];
           }
       }
       
       return view('tenantmanagement::livewire.tenant-module-component', [
           'moduleGroups' => $orderedModuleGroups
       ]);
   }
}