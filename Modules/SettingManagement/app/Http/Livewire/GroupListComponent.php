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
            $group->is_active ? 'aktif edildi' : 'pasif edildi'
        );
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Grup durumu güncellendi',
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
                'oluşturuldu'
            );
    
            $this->reset('inputs');
            $this->showAddForm = false;
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Grup başarıyla eklendi',
                'type' => 'success'
            ]);
    
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Grup eklenirken bir hata oluştu',
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
            'güncellendi',
            array_diff_assoc($group->toArray(), $oldData)
        );
    
        $this->editingGroup = null;
        $this->reset('inputs');
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Grup başarıyla güncellendi',
            'type' => 'success'
        ]);
    }
    
    public function delete($id)
    {
        $group = SettingGroup::findOrFail($id);
    
        if ($group->children()->count() > 0) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Alt grupları olan bir grubu silemezsiniz',
                'type' => 'error'
            ]);
            return;
        }
    
        $deletedGroup = clone $group;
        $group->delete();
    
        log_activity(
            $deletedGroup,
            'silindi'
        );
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Grup başarıyla silindi',
            'type' => 'success'
        ]);
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