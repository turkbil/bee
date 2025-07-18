<?php
namespace Modules\ModuleManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Modules\ModuleManagement\App\Models\Module;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class ModuleManageComponent extends Component
{
    public $moduleId;
    public $domains = [];
    public $selectedDomains = [];
    public $isSaving = false;
    public $availableSettings = [];
    public $oldInputs = [];
    
    public $inputs = [
        'name' => '',
        'display_name' => '',
        'description' => '',
        'version' => '',
        'type' => 'content',
        'is_active' => true,
        'setting' => null,
    ];

    protected $messages = [
        'inputs.name.required' => 'Modül adı zorunludur.',
        'inputs.name.min' => 'Modül adı en az :min karakter olmalıdır.',
        'inputs.display_name.required' => 'Görünen ad zorunludur.',
        'inputs.display_name.min' => 'Görünen ad en az :min karakter olmalıdır.',
        'inputs.type.required' => 'Modül tipi seçilmelidir.',
        'inputs.type.in' => 'Geçerli bir modül tipi seçiniz.',
    ];

    public function mount($id = null)
    {
        // Domain listesini almak için
        $domainList = [];
        try {
            $domainList = DB::table('tenants')->get();
        } catch (\Exception $e) {
            // tenant tablosu olmayabilir
        }
        
        $this->domains = collect($domainList)->mapWithKeys(function ($tenant) {
            return [$tenant->id => [
                'id' => $tenant->id,
                'name' => $tenant->title ?? $tenant->id
            ]];
        })->toArray();
        
        try {
            $this->availableSettings = DB::table('settings_groups')
                ->where('parent_id', 2)
                ->select('id', 'name')
                ->get();
        } catch (\Exception $e) {
            $this->availableSettings = [];
        }
        
        if ($id) {
            $this->moduleId = $id;
            $module = Module::findOrFail($id);
            
            $this->inputs = [
                'name' => $module->name,
                'display_name' => $module->display_name,
                'description' => $module->description,
                'version' => $module->version,
                'type' => $module->type,
                'is_active' => $module->is_active,
                'setting' => $module->settings,
            ];
            
            // Orijinal değerleri sakla
            $this->oldInputs = $this->inputs;
            
            // Load the domains from the module_tenants table
            $moduleTenants = $module->tenants;
            
            // Başlangıçta tüm domainleri pasif olarak işaretle
            foreach ($this->domains as $domainId => $domain) {
                $this->selectedDomains[$domainId] = false;
            }
            
            // Modüle atanmış domainleri aktif olarak işaretle
            foreach ($moduleTenants as $tenant) {
                $this->selectedDomains[$tenant->id] = (bool)$tenant->pivot->is_active;
            }
        }
    }

    protected function rules()
    {
        return [
            'inputs.name' => 'required|min:3',
            'inputs.display_name' => 'required|min:3',
            'inputs.description' => 'nullable|string',
            'inputs.version' => 'nullable|string',
            'inputs.type' => 'required|in:content,management,system',
            'inputs.setting' => 'nullable|integer',
            'inputs.is_active' => 'boolean',
            'selectedDomains' => 'array',
            'selectedDomains.*' => 'boolean',
        ];
    }

    public function getAvailableModulesProperty()
    {
        $modulesList = \Module::all();
        $existingModules = Module::pluck('name')
            ->map(fn($name) => strtolower($name))
            ->toArray();

        $available = [];
        foreach ($modulesList as $module) {
            $moduleName = strtolower($module->getName());
            if (!in_array($moduleName, $existingModules)) {
                $available[$module->getName()] = $module->getName();
            }
        }

        return $available;
    }
    
    public function save($redirect = false)
    {
        $this->isSaving = true;
        $this->validate();
    
        $moduleData = [
            'name' => $this->inputs['name'],
            'display_name' => $this->inputs['display_name'],
            'description' => $this->inputs['description'],
            'version' => $this->inputs['version'],
            'type' => $this->inputs['type'],
            'settings' => $this->inputs['setting'],
            'is_active' => $this->inputs['is_active'],
        ];
        
        // Hiçbir değişiklik yapılmadıysa uyarı ver
        if ($this->moduleId && $this->oldInputs == $this->inputs && !count(array_filter($this->selectedDomains))) {
            $this->dispatch('toast', [
                'title' => 'Bilgi',
                'message' => 'Herhangi bir değişiklik yapılmadı.',
                'type' => 'info',
            ]);
            $this->isSaving = false;
            return;
        }
    
        if ($this->moduleId) {
            // Güncellemeden önce tenant ID'leri al
            $module = Module::findOrFail($this->moduleId);
            $oldTenantIds = $module->tenants()->pluck('tenant_id')->toArray();
            
            $oldData = $module->toArray();
            $module->update($moduleData);
            
            log_activity(
                $module,
                'güncellendi',
                array_diff_assoc($module->toArray(), $oldData)
            );
        } else {
            if (empty($this->inputs['name'])) {
                $this->dispatch('toast', [
                    'title' => 'Uyarı',
                    'message' => 'Lütfen bir modül seçiniz veya bilgileri doldurunuz.',
                    'type' => 'warning',
                ]);
                $this->isSaving = false;
                return;
            }
            
            $module = Module::create($moduleData);
            log_activity(
                $module,
                'oluşturuldu'
            );
            
            // Yeni modülü otomatik olarak tüm tenant'lara ata
            $this->autoAssignModuleToAllTenants($module);
        }
        
        // Tenant relationships güncelleme ve izin işlemleri
        $syncData = [];
        $addedTenants = [];
        $removedTenants = [];
        
        // Önceki tenant listesini al
        $previousTenants = isset($oldTenantIds) ? $oldTenantIds : [];
        
        // Hangi tenant'ların eklendiğini ve hangilerinin kaldırıldığını belirle
        foreach ($this->selectedDomains as $tenantId => $isActive) {
            if ($isActive) {
                $syncData[$tenantId] = ['is_active' => true];
                
                // Eğer önceden yoksa, eklenen olarak işaretle
                if (!in_array($tenantId, $previousTenants)) {
                    $addedTenants[] = $tenantId;
                }
            }
        }
        
        // Kaldırılan tenant'ları belirle (önceden var olan ama şimdi seçili olmayan)
        foreach ($previousTenants as $tenantId) {
            if (!isset($this->selectedDomains[$tenantId]) || !$this->selectedDomains[$tenantId]) {
                $removedTenants[] = $tenantId;
            }
        }
        
        // Modül-tenant ilişkilerini güncelle
        $module->tenants()->sync($syncData);
        
        // İzin işlemleri için servis
        $permissionService = app(\App\Services\ModuleTenantPermissionService::class);
        
        // Eklenen tenant'lar için izinleri oluştur
        foreach ($addedTenants as $tenantId) {
            $permissionService->handleModuleAddedToTenant($module->module_id, $tenantId);
        }
        
        // Silinen tenant'lar için izinleri ve kullanıcı modül izinlerini kaldır
        foreach ($removedTenants as $tenantId) {
            $permissionService->handleModuleRemovedFromTenant($module->module_id, $tenantId);
        }
    
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => 'Modül başarıyla kaydedildi.',
                'type' => 'success',
            ]);
            return redirect()->route('admin.modulemanagement.index');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Modül başarıyla kaydedildi.',
            'type' => 'success',
        ]);
    
        // Değişiklikleri orijinal değerler olarak güncelle
        $this->oldInputs = $this->inputs;
        
        $this->isSaving = false;
    }

    public function render()
    {
        $types = Module::select('type')->distinct()->whereNotNull('type')->pluck('type');
        
        return view('modulemanagement::livewire.module-manage-component', [
            'types' => $types
        ]);
    }
    
    /**
     * Yeni modülü otomatik olarak tüm tenant'lara ata ve permission'ları oluştur
     */
    private function autoAssignModuleToAllTenants(Module $module)
    {
        try {
            // Tüm tenant'ları al
            $tenants = DB::table('tenants')->get();
            
            // Permission service
            $permissionService = app(\App\Services\ModuleTenantPermissionService::class);
            
            foreach ($tenants as $tenant) {
                // Modülü tenant'a ata
                $module->tenants()->attach($tenant->id, ['is_active' => true]);
                
                // Permission'ları oluştur
                $permissionService->handleModuleAddedToTenant($module->module_id, $tenant->id);
            }
            
            // Başarı mesajı
            $this->dispatch('toast', [
                'title' => 'Bilgi',
                'message' => 'Yeni modül tüm tenant\'lara otomatik olarak atandı ve izinleri oluşturuldu.',
                'type' => 'info',
            ]);
            
        } catch (\Exception $e) {
            // Hata durumunda log'a yazdır
            \Log::error('Auto-assign module to tenants failed', [
                'module_id' => $module->module_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}