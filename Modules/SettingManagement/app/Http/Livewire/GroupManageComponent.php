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
        'parent_id' => null
    ];

    protected function rules()
    {
        return [
            'inputs.name' => 'required|min:3|max:255',
            'inputs.description' => 'nullable|string',
            'inputs.icon' => 'nullable|string|max:50',
            'inputs.parent_id' => 'nullable|exists:settings_groups,id',
            'inputs.is_active' => 'boolean',
        ];
    }

    public function mount($id = null)
    {
        $this->groupId = $id;
        $this->parentGroups = SettingGroup::whereNull('parent_id')->get();
            
        if ($id) {
            $group = SettingGroup::findOrFail($id);
            $this->inputs = $group->only(['name', 'description', 'icon', 'is_active', 'parent_id']);
        } else {
            // parent_id parametresi varsa inputs'a ekle
            if (request()->has('parent_id')) {
                $this->inputs['parent_id'] = request()->get('parent_id');
            }
        }
    }

    public function save($redirect = false)
    {
        $this->redirect = $redirect;
        $this->validate();
    
        if ($this->groupId) {
            $group = SettingGroup::findOrFail($this->groupId);
            $oldData = $group->toArray();
            $group->update($this->inputs);
            
            log_activity(
                $group,
                'güncellendi',
                array_diff_assoc($group->toArray(), $oldData)
            );
            
            $message = 'Grup güncellendi';
        } else {
            $inputs = array_merge($this->inputs, ['slug' => Str::slug($this->inputs['name'])]);
            $group = SettingGroup::create($inputs);
            
            log_activity(
                $group,
                'oluşturuldu'
            );
            
            $message = 'Grup oluşturuldu';
        }
    
        if ($this->redirect) {
            return redirect()->route('admin.settingmanagement.index');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
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