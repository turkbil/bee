<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\SettingManagement\App\Models\SettingGroup;

class FormBuilderComponent extends Component
{
    public $groupId;
    public $group;
    
    public function mount($groupId = null)
    {
        $this->groupId = $groupId;
        
        if ($groupId) {
            $this->group = SettingGroup::findOrFail($groupId);
        }
    }
    
    public function saveLayout($formData)
    {
        $group = SettingGroup::findOrFail($this->groupId);
        $group->layout = $formData;
        $group->save();
        
        log_activity(
            $group,
            'form layout güncellendi'
        );
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Form yapısı başarıyla kaydedildi',
            'type' => 'success'
        ]);
    }
    
    public function render()
    {
        if (!$this->groupId) {
            // Index sayfası
            $groups = SettingGroup::whereNotNull('parent_id')->get();
            return view('settingmanagement::form-builder.index', [
                'groups' => $groups
            ])->layout('settingmanagement::layouts.form-builder');
        } else {
            // Edit sayfası
            return view('settingmanagement::form-builder.edit')->layout('settingmanagement::layouts.form-builder');
        }
    }
}