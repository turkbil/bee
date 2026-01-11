<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class GroupListComponent extends Component
{
    public $showAddForm = false;
    public $editingGroup = null;
    public $expandedGroups = [];
    public $search = '';
    public $formBuilderOpen = false;
    public $selectedGroup = null;
    
    public $inputs = [
        'name' => '',
        'description' => '',
        'icon' => '',
        'parent_id' => null,
        'is_active' => true
    ];

    protected $rules = [
        'inputs.name' => 'required|min:3|max:255',
        'inputs.description' => 'nullable|max:255',
        'inputs.icon' => 'nullable|max:50',
        'inputs.parent_id' => 'nullable|exists:settings_groups,id',
        'inputs.is_active' => 'boolean'
    ];

    public function toggleGroup($groupId)
    {
        if (in_array($groupId, $this->expandedGroups)) {
            $this->expandedGroups = array_diff($this->expandedGroups, [$groupId]);
        } else {
            $this->expandedGroups[] = $groupId;
        }
    }

    public function startEditing($group)
    {
        if ($this->editingGroup === $group['id']) {
            $this->editingGroup = null;
        } else {
            $this->editingGroup = $group['id'];
            $this->inputs = [
                'name' => $group['name'],
                'description' => $group['description'],
                'icon' => $group['icon'],
                'parent_id' => $group['parent_id'],
                'is_active' => $group['is_active']
            ];
        }
    }

    public function toggleActive($id)
    {
        $group = SettingGroup::findOrFail($id);
        $oldStatus = $group->is_active;
        $group->is_active = !$group->is_active;
        $group->save();
    
        log_activity(
            $group,
            $group->is_active ? __('settingmanagement.actions.group_activated') : __('settingmanagement.actions.group_deactivated')
        );
    
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => __('settingmanagement.messages.group_status_updated'),
            'type' => 'success'
        ]);
    }
    
    public function quickAdd()
    {
        $this->validate([
            'inputs.name' => 'required|min:3'
        ]);
    
        try {
            $group = SettingGroup::create([
                'name' => $this->inputs['name'],
                'slug' => Str::slug($this->inputs['name']),
                'parent_id' => $this->inputs['parent_id'],
                'is_active' => true
            ]);
    
            log_activity(
                $group,
                __('settingmanagement.actions.created')
            );
    
            $this->reset('inputs');
            $this->showAddForm = false;
    
            $this->dispatch('toast', [
                'title' => __('settingmanagement.messages.success'),
                'message' => __('settingmanagement.messages.group_created'),
                'type' => 'success'
            ]);
    
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('settingmanagement.messages.error'),
                'message' => __('settingmanagement.messages.group_create_error'),
                'type' => 'error'
            ]);
        }
    }
    
    public function saveInlineEdit()
    {
        $this->validate();
        $group = SettingGroup::findOrFail($this->editingGroup);
        $oldData = $group->toArray();
        $group->update($this->inputs);
    
        log_activity(
            $group,
            __('settingmanagement.actions.updated'),
            array_diff_assoc($group->toArray(), $oldData)
        );
    
        $this->editingGroup = null;
        $this->reset('inputs');
    
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => __('settingmanagement.messages.group_updated'),
            'type' => 'success'
        ]);
    }
    
    public function delete($id)
    {
        $group = SettingGroup::findOrFail($id);
    
        if ($group->children()->count() > 0) {
            $this->dispatch('toast', [
                'title' => __('settingmanagement.messages.error'),
                'message' => __('settingmanagement.messages.group_delete_error'),
                'type' => 'error'
            ]);
            return;
        }
    
        $deletedGroup = clone $group;
        $group->delete();
    
        log_activity(
            $deletedGroup,
            __('settingmanagement.actions.deleted')
        );
    
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => __('settingmanagement.messages.group_deleted'),
            'type' => 'success'
        ]);
    }
    
    public function openFormBuilder($groupId)
    {
        $this->selectedGroup = SettingGroup::findOrFail($groupId);
        $this->formBuilderOpen = true;
    }

    public function closeFormBuilder()
    {
        $this->formBuilderOpen = false;
        $this->selectedGroup = null;
    }

    #[On('saveFormLayout')]
    public function saveFormLayout($groupId, $formData)
    {
        $group = SettingGroup::findOrFail($groupId);
        $group->layout = json_decode($formData, true);
        $group->save();
        
        log_activity(
            $group,
            __('settingmanagement.actions.form_layout_updated')
        );
        
        $this->dispatch('toast', [
            'title' => __('settingmanagement.messages.success'),
            'message' => __('settingmanagement.messages.form_layout_saved'),
            'type' => 'success'
        ]);
        
        $this->closeFormBuilder();
    }
    
    #[On('refreshGroups')]
    public function render()
    {
        $groups = SettingGroup::with('children')
            ->whereNull('parent_id')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('children', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->get();

        return view('settingmanagement::livewire.group-list-component', [
            'groups' => $groups
        ]);
    }
}