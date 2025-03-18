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
    public $isSaving = false;
    public $availableSettings = [];
    
    public $inputs = [
        'name' => '',
        'display_name' => '',
        'description' => '',
        'version' => '',
        'type' => 'content',
        'group' => '',
        'settings' => null,
        'is_active' => true,
        'domains' => [],
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
                'name' => $tenant->id
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
            
            $this->inputs = $module->toArray();
            
            // domains array'ini düzenli şekilde işleme
            if (empty($this->inputs['domains']) || !is_array($this->inputs['domains'])) {
                $this->inputs['domains'] = [];
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
            'inputs.group' => 'nullable|string',
            'inputs.settings' => 'nullable|integer',
            'inputs.is_active' => 'boolean',
            'inputs.domains' => 'array',
            'inputs.domains.*' => 'boolean',
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

        if ($this->moduleId) {
            $module = Module::findOrFail($this->moduleId);
            $oldData = $module->toArray();
            $module->update($this->inputs);
            
            log_activity(
                $module,
                'güncellendi',
                array_diff_assoc($module->toArray(), $oldData)
            );
        } else {
            $module = Module::create($this->inputs);
            log_activity(
                $module,
                'oluşturuldu'
            );
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

        $this->isSaving = false;
    }

    public function render()
    {
        $existingGroups = Module::getGroups();
        
        return view('modulemanagement::livewire.module-manage-component', [
            'existingGroups' => $existingGroups
        ]);
    }
}