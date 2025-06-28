<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class GroupManageComponent extends Component
{
    protected static $recordEvents = ['created', 'updated', 'deleted'];

    public $groupId;
    public $parentGroups;
    public $redirect = false;
    public $inputs = [
        'name' => '',
        'description' => '',
        'icon' => '',
        'is_active' => true,
        'parent_id' => null,
        'prefix' => ''
    ];

    public $isSubGroup = false;

    protected function rules()
    {
        $rules = [
            'inputs.name' => 'required|min:3|max:255',
            'inputs.description' => 'nullable|string',
            'inputs.icon' => 'nullable|string|max:50',
            'inputs.parent_id' => 'nullable|exists:settings_groups,id',
            'inputs.is_active' => 'boolean',
        ];
        
        // Alt gruplar için prefix alanı ekle
        if ($this->isSubGroup) {
            $rules['inputs.prefix'] = 'nullable|string|max:50';
        }
        
        return $rules;
    }

    public function mount($id = null)
    {
        $this->groupId = $id;
        $this->parentGroups = SettingGroup::whereNull('parent_id')->get();
        
        // Varsayılan icon ayarla
        if (empty($this->inputs['icon'])) {
            $this->inputs['icon'] = 'fas fa-folder';
        }
            
        if ($id) {
            $group = SettingGroup::findOrFail($id);
            $this->inputs = $group->only(['name', 'description', 'icon', 'is_active', 'parent_id', 'prefix']);
            // Alt grup ise isSubGroup özelliğini true yap
            $this->isSubGroup = !is_null($group->parent_id);
        } else {
            // parent_id parametresi varsa inputs'a ekle
            if (request()->has('parent_id')) {
                $this->inputs['parent_id'] = request()->get('parent_id');
                // Eğer parent_id varsa alt grup olarak işaretle
                $this->isSubGroup = true;
            }
        }
    }
    
    public function updatedInputsParentId($value)
    {
        // Parent ID değiştiğinde alt grup olup olmadığını güncelle
        $this->isSubGroup = !is_null($value);
        
        // JavaScript'e olay gönder
        $this->dispatch('parentIdChanged', $value);
    }

    public function save($redirect = false)
    {
        $this->redirect = $redirect;
        $this->validate();
        
        // Eğer icon boş bırakıldıysa varsayılan icon ata
        if (empty($this->inputs['icon'])) {
            $this->inputs['icon'] = 'fas fa-folder';
        }
        
        // Prefix sadece alt gruplar için kullanılabilir
        if (!$this->isSubGroup) {
            $this->inputs['prefix'] = null;
        }
    
        if ($this->groupId) {
            $group = SettingGroup::findOrFail($this->groupId);
            $oldData = $group->toArray();
            $group->update($this->inputs);
            
            // JSON/array veri tiplerinde karşılaştırma hatasını önlemek için
            // sadece değişen temel alanları gönderelim
            $changes = [];
            $basicFields = ['name', 'description', 'icon', 'prefix', 'is_active', 'parent_id'];
            
            foreach ($basicFields as $field) {
                if (isset($oldData[$field]) && isset($group->$field) && $oldData[$field] !== $group->$field) {
                    $changes[$field] = $group->$field;
                }
            }
            
            log_activity(
                $group,
                __('settingmanagement.actions.updated'),
                $changes
            );
            
            $message = __('settingmanagement.messages.group_updated');
        } else {
            // Slug oluşturma işlemi artık Sluggable trait tarafından otomatik yapılıyor
            $group = SettingGroup::create($this->inputs);
            
            log_activity(
                $group,
                __('settingmanagement.actions.created')
            );
            
            $message = __('settingmanagement.messages.group_created');
        }
    
        if ($this->redirect) {
            return redirect()->route('admin.settingmanagement.index');
        }
    
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => $message,
            'type' => 'success'
        ]);

        $this->dispatch('refreshGroups');
    }

    public function render()
    {
        return view('settingmanagement::livewire.group-manage-component', [
            'groupId' => $this->groupId,
        ]);
    }
}