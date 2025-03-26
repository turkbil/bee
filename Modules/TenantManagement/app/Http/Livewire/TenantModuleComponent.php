<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use App\Models\Tenant; 
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;

class TenantModuleComponent extends Component
{
   public $selectedModules = [];
   public $tenantId;
   public $modules;
   public $selectAll = false;

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
               $tenant = Tenant::find($this->tenantId);
               if ($tenant) {
                   $tenantModules = $tenant->modules()->get();
                   $this->selectedModules = $tenantModules->pluck('module_id')
                       ->map(fn($id) => (string) $id)
                       ->toArray();
               }
           }
       } catch (\Exception $e) {
           $this->modules = collect();
           $this->selectedModules = [];
       }
   }

   public function toggleSelectAll()
   {
       if (count($this->selectedModules) === $this->modules->count()) {
           $this->selectedModules = [];
           $this->selectAll = false;
       } else {
           $this->selectedModules = $this->modules
               ->pluck('module_id')
               ->map(fn($id) => (string) $id)
               ->toArray();
           $this->selectAll = true;
       }
   }

   public function save()
   {
       if (!$this->tenantId) return;
   
       try {
           $tenant = Tenant::find($this->tenantId);
           if ($tenant) {
               // Önceki modülleri kaydet
               $previousModules = $tenant->modules()->pluck('module_id')->toArray();
               
               // Seçilen modülleri hazırla
               $syncData = collect($this->selectedModules)
                   ->mapWithKeys(fn($id) => [$id => ['is_active' => true]])
                   ->toArray();
                   
               // Modülleri güncelle
               $tenant->modules()->sync($syncData);
               
               // Log işlemi
               activity()
                   ->performedOn($tenant)
                   ->withProperties([
                       'modules' => $this->selectedModules
                   ])
                   ->log('tenant modülleri güncellendi');
                   
               // Tenant izinlerini oluştur/kaldır
               $permissionService = app(\App\Services\ModuleTenantPermissionService::class);
               
               // Eklenen modüller için izinleri oluştur
               foreach ($this->selectedModules as $moduleId) {
                   if (!in_array($moduleId, $previousModules)) {
                       $permissionService->handleModuleAddedToTenant($moduleId, $this->tenantId);
                   }
               }
               
               // Silinen modüller için izinleri kaldır
               foreach ($previousModules as $moduleId) {
                   if (!in_array($moduleId, $this->selectedModules)) {
                       $permissionService->handleModuleRemovedFromTenant($moduleId, $this->tenantId);
                   }
               }
   
               $this->dispatch('toast', [
                   'title' => 'Başarılı!',
                   'message' => 'Modül atamaları güncellendi.',
                   'type' => 'success'
               ]);
   
               // Modalı kapat
               $this->dispatch('hideModal', ['id' => 'modal-module-management']);
               $this->dispatch('modulesSaved');
           }
       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Hata!',
               'message' => 'Modül atamaları güncellenirken bir hata oluştu: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }

   public function render()
   {
       return view('tenantmanagement::livewire.tenant-module-component', [
           'moduleGroups' => $this->modules ? $this->modules->groupBy('type') : collect()
       ]);
   }
}