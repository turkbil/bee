<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use App\Models\Tenant; 
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
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
       $this->modules = Module::orderBy('display_name')->get();

       if ($this->tenantId) {
           $tenant = Tenant::find($this->tenantId);
           if ($tenant) {
               // Tenant_modules tablosunun var olup olmadığını kontrol et
               if (Schema::hasTable('module_tenants')) {
                   $this->selectedModules = DB::table('module_tenants')
                       ->where('tenant_id', $this->tenantId)
                       ->pluck('module_id')
                       ->map(fn($id) => (string) $id)
                       ->toArray();
               } else {
                   $this->selectedModules = [];
               }
           }
       }
   }

   public function toggleSelectAll()
   {
       if (count($this->selectedModules) === $this->modules->count()) {
           $this->selectedModules = [];
       } else {
           $this->selectedModules = $this->modules
               ->pluck('module_id')
               ->map(fn($id) => (string) $id)
               ->toArray();
       }
   }

   public function save()
   {
       if (!$this->tenantId) return;
       
       $this->isSaving = true;

       try {
           DB::beginTransaction();
           
           $tenant = Tenant::find($this->tenantId);
           if ($tenant) {
               // Tenant_modules tablosunun var olup olmadığını kontrol et
               if (!Schema::hasTable('module_tenants')) {
                   $this->dispatch('toast', [
                       'title' => 'Hata!',
                       'message' => 'Tenant modülleri tablosu bulunamadı. Lütfen migrasyonları çalıştırın.',
                       'type' => 'error'
                   ]);
                   $this->isSaving = false;
                   DB::rollBack();
                   return;
               }
               
               // Önce mevcut modülleri al
               $oldModuleIds = DB::table('module_tenants')
                   ->where('tenant_id', $this->tenantId)
                   ->pluck('module_id')
                   ->toArray();
               
               // Mevcut ilişkileri temizle
               DB::table('module_tenants')
                   ->where('tenant_id', $this->tenantId)
                   ->delete();
               
               // Yeni ilişkileri ekle
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
               
               log_activity($tenant, 'modüller güncellendi', [
                   'old' => $oldModuleIds,
                   'new' => $this->selectedModules
               ]);
               
               // Tenant için cache'i temizle
               Cache::forget("modules_tenant_" . $tenant->id);
               
               DB::commit();
    
               $this->dispatch('toast', [
                   'title' => 'Başarılı!',
                   'message' => 'Modül atamaları güncellendi.',
                   'type' => 'success'
               ]);
    
               $this->dispatch('hideModal', ['id' => 'modal-module-management']);
               $this->dispatch('modulesSaved');
           }
       } catch (\Exception $e) {
           DB::rollBack();
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
       // Modülleri gruplandır
       $modulesByType = $this->modules ? $this->modules->groupBy('type') : collect();
       
       // Tip sıralaması
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