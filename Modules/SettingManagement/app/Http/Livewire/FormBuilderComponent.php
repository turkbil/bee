<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
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
    
    #[On('save-form-layout')]
    public function saveLayout($data)
    {
        try {
            $groupId = $data['groupId'] ?? $this->groupId;
            $formData = $data['formData'] ?? null;
            
            if (!$groupId || !$formData) {
                throw new \Exception("Eksik parametreler: groupId veya formData");
            }
            
            $group = SettingGroup::findOrFail($groupId);
            
            // JSON string olarak gelmesi durumunda
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Geçersiz JSON formatı");
                }
            }
            
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
            
            return ['success' => true];
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Form yapısı kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
            
            return ['success' => false, 'error' => $e->getMessage()];
        }
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