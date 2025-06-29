<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\LanguageManagement\app\Services\AdminLanguageService;
use Modules\LanguageManagement\app\Models\AdminLanguage;

#[Layout('admin.layout')]
class AdminLanguageComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    

    public $perPage = 12;

    public function mount()
    {
        // Central domain kontrolü middleware tarafından yapılıyor
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $language = AdminLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Dil bulunamadı.');
            return;
        }

        // TR ve EN silinemez
        if (in_array($language->code, ['tr', 'en'])) {
            session()->flash('error', 'Temel sistem dilleri silinemez.');
            return;
        }

        $systemLanguageService = app(AdminLanguageService::class);
        
        if ($systemLanguageService->deleteAdminLanguage($language->code)) {
            if (function_exists('log_activity')) {
                log_activity($language, 'silindi');
            }
            
            session()->flash('message', 'Sistem dili başarıyla silindi.');
        } else {
            session()->flash('error', 'Sistem dili silinirken hata oluştu.');
        }
    }

    public function toggleActive($id)
    {
        $language = AdminLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Dil bulunamadı.');
            return;
        }

        // TR ve EN deaktive edilemez
        if (in_array($language->code, ['tr', 'en']) && $language->is_active) {
            session()->flash('error', 'Temel sistem dilleri deaktive edilemez.');
            return;
        }

        $language->is_active = !$language->is_active;
        
        if ($language->save()) {
            $systemLanguageService = app(AdminLanguageService::class);
            $systemLanguageService->clearAdminLanguageCache();
            
            if (function_exists('log_activity')) {
                log_activity($language, 'güncellendi');
            }
            
            $status = $language->is_active ? 'aktif' : 'pasif';
            session()->flash('message', "Sistem dili {$status} yapıldı.");
        } else {
            session()->flash('error', 'Durum güncellenirken hata oluştu.');
        }
    }


    public function updateOrder($itemIds)
    {
        foreach ($itemIds as $index => $id) {
            AdminLanguage::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        if (function_exists('log_activity') && !empty($itemIds)) {
            $firstLanguage = AdminLanguage::find($itemIds[0]);
            if ($firstLanguage) {
                log_activity($firstLanguage, 'sıralama_güncellendi');
            }
        }

        session()->flash('message', 'Sıralama başarıyla güncellendi.');
    }

    public function render()
    {
        $languages = AdminLanguage::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('native_name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(); // paginate yerine get kullan çünkü sortable

        return view('languagemanagement::admin.livewire.system-language-component', [
            'languages' => $languages
        ]);
    }

}