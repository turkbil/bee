<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingGroup;
use Modules\SettingManagement\App\Models\SettingValue;

#[Layout('admin.layout')]
class TenantSettingsComponent extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedGroup = null;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedSelectedGroup()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        // Sadece tenant ayarları olan grupları bul
        $tenantGroups = SettingGroup::whereHas('settings', function($query) {
            $query->where('is_active', true);
        })->get();
        
        $settings = Setting::where('is_active', true)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('label', 'like', '%' . $this->search . '%')
                      ->orWhere('key', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedGroup, function($query) {
                $query->where('group_id', $this->selectedGroup);
            })
            ->orderBy('group_id')
            ->orderBy('sort_order')
            ->get()
            ->map(function($setting) {
                $value = SettingValue::where('setting_id', $setting->id)->first();
                $setting->current_value = $value ? $value->value : $setting->default_value;
                $setting->is_custom = $value ? true : false;
                return $setting;
            });
        
        return view('settingmanagement::livewire.tenant-settings-component', [
            'settings' => $settings,
            'groups' => $tenantGroups
        ]);
    }
}