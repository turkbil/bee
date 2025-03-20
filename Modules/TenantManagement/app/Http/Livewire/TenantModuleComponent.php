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
       $this->modules = Module::orderBy('display_name')->get();

       if ($this->tenantId) {
           $tenant = Tenant::find($this->tenantId);
           if ($tenant) {
               // Bu sorguyu bir try-catch bloğu içine koyup hata ayıklama ekleyelim
               try {
                   $selectedIds = DB::table('module_tenants')
                       ->where('tenant_id', $this->tenantId)
                       ->pluck('module_id')
                       ->toArray();
                   
                   $this->selectedModules = collect($selectedIds)
                       ->map(fn($id) => (string) $id)
                       ->toArray();
               } catch (\Exception $e) {
                   // Hata oluşursa boş dizi kullan
                   $this->selectedModules = [];
               }
           }
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

       $tenant = Tenant::find($this->tenantId);
       if ($tenant) {
           // Burada da daha güvenli bir yaklaşım kullanalım
           try {
               $oldModules = DB::table('module_tenants')
                   ->where('tenant_id', $this->tenantId)
                   ->pluck('module_id')
                   ->toArray();
               
               $syncData = collect($this->selectedModules)
                   ->mapWithKeys(fn($id) => [$id => ['is_active' => true]])
                   ->toArray();
                   
               $tenant->modules()->sync($syncData);
               
               log_activity($tenant, 'modüller güncellendi', [
                   'old' => $oldModules,
                   'new' => $this->selectedModules
               ]);

               $this->dispatch('toast', [
                   'title' => 'Başarılı!',
                   'message' => 'Modül atamaları güncellendi.',
                   'type' => 'success'
               ]);

               $this->dispatch('hideModal', ['id' => 'modal-module-management']);
               $this->dispatch('modulesSaved');
           } catch (\Exception $e) {
               $this->dispatch('toast', [
                   'title' => 'Hata!',
                   'message' => 'Modül atamaları güncellenirken bir hata oluştu: ' . $e->getMessage(),
                   'type' => 'error'
               ]);
           }
       }
   }

   public function render()
   {
       return view('tenantmanagement::livewire.tenant-module-component', [
           'moduleGroups' => $this->modules ? $this->modules->groupBy('type') : collect()
       ]);
   }
}