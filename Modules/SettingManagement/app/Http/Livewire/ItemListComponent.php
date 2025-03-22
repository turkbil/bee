<?php

namespace Modules\SettingManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingGroup;

#[Layout('admin.layout')]
class ItemListComponent extends Component
{
    use WithPagination;
    
    public $groupId;
    #[Url]
    public $search = '';
    #[Url]
    public $sortField = 'id';
    #[Url]
    public $sortDirection = 'desc';
    public $previewData = null;
    public $viewMode = 'table'; // table veya preview
    public $viewType = 'table';
    
    protected $queryString = [
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
    ];
    
    protected $listeners = [
        'deleteConfirmed' => 'performDelete'
    ];

    public $itemToDelete = null;

    #[On('updateOrder')]
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }
        
        $oldOrders = Setting::where('group_id', $this->groupId)
            ->whereIn('id', collect($list)->pluck('value'))
            ->pluck('sort_order', 'id')
            ->toArray();
    
        foreach ($list as $item) {
            if (!isset($item['value'], $item['order'])) {
                continue;
            }

            $setting = Setting::where('id', $item['value'])
                ->where('group_id', $this->groupId)
                ->first();
                
            if ($setting) {
                $oldOrder = $oldOrders[$setting->id] ?? 0;
                $setting->sort_order = $item['order'];
                $setting->save();
    
                if ($oldOrder != $item['order']) {
                    log_activity(
                        $setting,
                        'sıralama güncellendi',
                        ['old' => $oldOrder, 'new' => $item['order']]
                    );
                }
            }
        }
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sıralama güncellendi.',
            'type' => 'success',
        ]);
    }

    public function mount($group)
    {
        $this->groupId = $group;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $setting = Setting::where('group_id', $this->groupId)->find($id);
    
        if ($setting) {
            $setting->is_active = !$setting->is_active;
            $setting->save();
    
            log_activity(
                $setting,
                $setting->is_active ? 'aktif edildi' : 'pasif edildi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$setting->label}\" " . ($setting->is_active ? 'aktif' : 'pasif') . " yapıldı.",
                'type' => 'success',
            ]);
        }
    }

    public function delete($id)
    {
        $setting = Setting::find($id);
        
        if ($setting) {
            $this->itemToDelete = [
                'id' => $setting->id,
                'title' => $setting->label
            ];
            
            $this->dispatch('openDeleteModal');
        }
    }
    
    public function performDelete($id)
    {
        $setting = Setting::find($id);
    
        if ($setting) {
            $deletedSetting = clone $setting;
            $setting->delete();
    
            log_activity(
                $deletedSetting,
                'silindi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$deletedSetting->label}\" silindi.",
                'type' => 'success',
            ]);
            
            $this->itemToDelete = null;
        }
    }
    
    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'table' ? 'preview' : 'table';
    }

    public function render()
    {
        $settings = Setting::where('group_id', $this->groupId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('label', 'like', '%' . $this->search . '%')
                        ->orWhere('key', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('settingmanagement::livewire.item-list-component', [
            'settings' => $settings,
        ]);
    }
}