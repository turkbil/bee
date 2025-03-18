<?php

namespace Modules\SettingManagement\App\Http\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingGroup;
use Illuminate\Support\Str;

class Manage extends Component
{
    use WithFileUploads;

    public $settingId;
    public $tempFile;
    public $redirect = false;
    public $inputs = [
        'group_id' => '',
        'label' => '',
        'key' => '',
        'type' => 'text',
        'options' => null,
        'default_value' => null,
        'sort_order' => 0,
        'is_active' => true
    ];

    protected function rules()
    {
        return [
            'inputs.group_id' => 'required|exists:settings_groups,id',
            'inputs.label' => 'required|min:3|max:255',
            'inputs.key' => 'required|regex:/^[a-zA-Z0-9_]+$/|max:255|unique:settings,key,' . $this->settingId,
            'inputs.type' => 'required|in:text,textarea,number,select,checkbox,file,color,date,email,password,tel,url,time',
            'inputs.options' => 'nullable|required_if:inputs.type,select',
            'inputs.default_value' => 'nullable',
            'inputs.sort_order' => 'required|integer|min:0',
            'inputs.is_active' => 'boolean',
        ];
    }

    public function mount($id = null)
    {
        $this->settingId = $id;
        
        if ($id) {
            $setting = Setting::findOrFail($id);
            $this->inputs = $setting->only([
                'group_id', 'label', 'key', 'type', 'options', 
                'default_value', 'sort_order', 'is_active'
            ]);
        }
    }

    public function updatedInputsLabel()
    {
        if (empty($this->inputs['key']) && !empty($this->inputs['label'])) {
            $this->inputs['key'] = Str::snake($this->inputs['label']);
        }
    }

    public function updatedTempFile()
    {
        if ($this->tempFile) {
            $this->inputs['default_value'] = $this->tempFile->store('settings', 'public');
        }
    }

    public function save($redirect = false)
    {
        $this->redirect = $redirect;
        $this->validate();
        
        // Eğer select tipiyse, options string olarak geldiyse parse edelim
        if ($this->inputs['type'] === 'select' && is_string($this->inputs['options'])) {
            $options = [];
            $lines = explode("\n", $this->inputs['options']);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $options[trim($key)] = trim($value);
                } else {
                    $options[Str::slug($line)] = $line;
                }
            }
            $this->inputs['options'] = $options;
        }
    
        if ($this->settingId) {
            $setting = Setting::findOrFail($this->settingId);
            $oldData = $setting->toArray();
            $setting->update($this->inputs);
            
            log_activity(
                $setting,
                'güncellendi',
                array_diff_assoc($setting->toArray(), $oldData)
            );
            
            $message = 'Ayar güncellendi';
        } else {
            $setting = Setting::create($this->inputs);
            
            log_activity(
                $setting,
                'oluşturuldu'
            );
            
            $message = 'Ayar oluşturuldu';
        }
    
        if ($this->redirect) {
            return redirect()->route('admin.settingmanagement.items', $setting->group_id);
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success'
        ]);
    }

    public function render()
    {
        $groups = SettingGroup::all();
        
        return view('settingmanagement::livewire.settings.manage', [
            'groups' => $groups
        ])->extends('admin.layout');
    }
}