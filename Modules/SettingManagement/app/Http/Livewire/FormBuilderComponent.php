<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\SettingManagement\App\Models\SettingGroup;

class FormBuilderComponent extends Component
{
    public $groupId;
    public $group;
    
    public function mount($groupId)
    {
        $this->groupId = $groupId;
        
        // Grup ID'si zorunlu ve grup bir alt grup olmalı
        $this->group = SettingGroup::where('id', $groupId)
            ->firstOrFail();
    }
    
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
            
            return ['success' => true, 'message' => 'Form yapısı başarıyla kaydedildi'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    public function render()
    {
        return view('settingmanagement::form-builder.edit')
            ->layout('settingmanagement::layouts.form-builder');
    }
}