<?php
namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;
use App\Services\ModuleAccessService;

#[Layout('admin.layout')]
class ModuleComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 100;

    #[Url]
    public $sortField = 'module_id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $typeFilter = '';

    public $showDomains = false;

    protected function getListeners()
    {
        return [
            'moduleDeleted' => '$refresh',
            'refresh' => '$refresh',
            'itemDeleted' => '$refresh'
        ];
    }
    
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleDomains()
    {
        $this->showDomains = !$this->showDomains;
    }

    public function toggleActive($id)
    {
        $module = Module::find($id);
        if ($module) {
            $module->is_active = !$module->is_active;
            $module->save();
            
            log_activity(
                $module,
                $module->is_active ? 'aktif edildi' : 'pasif edildi'
            );

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $module->display_name . ($module->is_active ? ' aktif' : ' pasif') . ' edildi.',
                'type' => 'success',
            ]);
        }
    }

    public function toggleDomainStatus($moduleId, $domain)
    {
        $module = Module::find($moduleId);
        if ($module) {
            $tenant = $module->tenants()->where('id', $domain)->first();
            $isActive = false;
            
            if ($tenant) {
                // Toggle the status in the pivot table
                $isActive = !$tenant->pivot->is_active;
                $module->tenants()->updateExistingPivot($domain, [
                    'is_active' => $isActive
                ]);
            } else {
                // Create a new relationship if it doesn't exist
                $isActive = true;
                $module->tenants()->attach($domain, [
                    'is_active' => true
                ]);
            }
            
            log_activity(
                $module,
                'domain durumu güncellendi',
                [
                    'domain' => $domain,
                    'status' => $isActive
                ]
            );
            
            // İzin işlemleri için servis
            $permissionService = app(\App\Services\ModuleTenantPermissionService::class);
            
            if ($isActive) {
                $permissionService->handleModuleAddedToTenant($moduleId, $domain);
            } else {
                $permissionService->handleModuleRemovedFromTenant($moduleId, $domain);
            }
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "Domain durumu güncellendi.",
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        $query = Module::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('display_name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection);
    
        $modules = $query->paginate($this->perPage);
        
        // Domain listesini almak için
        $domains = [];
        try {
            $domains = DB::table('tenants')->get();
        } catch (\Exception $e) {
            // tenant tablosu olmayabilir, bu durumda sessiz geçiyoruz
        }
        
        $types = Module::select('type')->distinct()->whereNotNull('type')->pluck('type');
    
        return view('modulemanagement::livewire.module-component', [
            'modules' => $modules,
            'domains' => $domains,
            'types' => $types
        ]);
    }
}