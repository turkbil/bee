<?php

namespace Modules\SettingManagement\App\Http\Livewire\Settings;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\SettingManagement\App\Models\Setting;
use Modules\SettingManagement\App\Models\SettingGroup;

class ItemList extends Component
{
    use WithPagination;
    
    public $groupId;
    #[Url]
    public $search = '';
    #[Url]
    public $perPage = 10;
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
        'perPage' => ['except' => 10],
    ];

    protected $listeners = ['updateOrder'];

    public function mount($group)
    {
        $this->groupId = $group;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
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

    public function updateOrder($list)
    {
        $oldOrders = Setting::where('group_id', $this->groupId)
            ->whereIn('id', collect($list)->pluck('value'))
            ->pluck('sort_order', 'id')
            ->toArray();
    
        foreach ($list as $item) {
            $setting = Setting::where('id', $item['value'])
                ->where('group_id', $this->groupId)
                ->first();
                
            if ($setting) {
                $oldOrder = $oldOrders[$setting->id];
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

    public function preview($id)
    {
        $this->previewData = Setting::find($id);
    }

    public function delete($id)
    {
        $setting = Setting::where('group_id', $this->groupId)->find($id);
    
        if ($setting) {
            $deletedSetting = clone $setting;
            $setting->delete();
    
            log_activity(
                $deletedSetting,
                'silindi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$setting->label}\" silindi.",
                'type' => 'success',
            ]);
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
            ->orderBy('sort_order', 'asc') // Önce sort_order'a göre sırala
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('settingmanagement::livewire.settings.item-list', [
            'settings' => $settings,
        ])->extends('admin.layout');
    }
}