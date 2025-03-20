<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use App\Models\Tenant; 
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\Schema;

class TenantModuleComponent extends Component
{
   public $selectedModules = [];
   public $tenantId;
   public $modules;

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
               if (Schema::hasTable('tenant_modules')) {
                   $this->selectedModules = $tenant->modules()
                       ->pluck('modules.module_id')
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

       $tenant = Tenant::find($this->tenantId);
       if ($tenant) {
           // Tenant_modules tablosunun var olup olmadığını kontrol et
           if (!Schema::hasTable('tenant_modules')) {
               $this->dispatch('toast', [
                   'title' => 'Hata!',
                   'message' => 'Tenant modülleri tablosu bulunamadı. Lütfen migrasyonları çalıştırın.',
                   'type' => 'error'
               ]);
               return;
           }
           
           $oldModules = $tenant->modules()->pluck('module_id')->toArray();
           
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

           $this->dispatch('closeModal');
           $this->dispatch('modulesSaved');
       }
   }

   public function render()
   {
       return view('tenantmanagement::livewire.tenant-module-component', [
           'moduleGroups' => $this->modules ? $this->modules->groupBy('group') : collect()
       ]);
   }
}