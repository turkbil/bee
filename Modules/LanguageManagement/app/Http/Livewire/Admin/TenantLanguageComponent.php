<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\LanguageManagement\app\Services\TenantLanguageService;
use Modules\LanguageManagement\app\Models\TenantLanguage;

#[Layout('admin.layout')]
class TenantLanguageComponent extends Component
{
    #[Url]
    public $search = '';

    public function updatedSearch()
    {
        // Reset işlemi gerekmez, pagination kaldırıldı
    }

    public function delete($id)
    {
        $language = TenantLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Site dili bulunamadı.');
            return;
        }
        
        // Varsayılan dil silinemez
        if ($language->is_default) {
            session()->flash('error', 'Varsayılan site dili silinemez.');
            return;
        }

        $siteLanguageService = app(TenantLanguageService::class);
        
        if ($siteLanguageService->deleteTenantLanguage($id)) {
            if (function_exists('log_activity')) {
                log_activity($language, 'silindi');
            }
            
            session()->flash('message', 'Site dili başarıyla silindi.');
        } else {
            session()->flash('error', 'Site dili silinirken hata oluştu.');
        }
    }

    public function toggleActive($id)
    {
        $language = TenantLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Site dili bulunamadı.');
            return;
        }
        
        // Varsayılan dil pasif yapılamaz
        if ($language->is_default && $language->is_active) {
            session()->flash('error', 'Varsayılan site dili pasif yapılamaz.');
            return;
        }

        $language->is_active = !$language->is_active;
        
        if ($language->save()) {
            $siteLanguageService = app(TenantLanguageService::class);
            $siteLanguageService->clearTenantLanguageCache();
            
            if (function_exists('log_activity')) {
                log_activity($language, 'güncellendi');
            }
            
            $status = $language->is_active ? 'aktif' : 'pasif';
            session()->flash('message', "Site dili {$status} yapıldı.");
        } else {
            session()->flash('error', 'Durum güncellenirken hata oluştu.');
        }
    }


    public function setAsDefault($id)
    {
        $language = TenantLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Site dili bulunamadı.');
            return;
        }

        $siteLanguageService = app(TenantLanguageService::class);
        
        if ($siteLanguageService->setDefaultTenantLanguage($language->code)) {
            if (function_exists('log_activity')) {
                log_activity($language, 'varsayılan_yapıldı');
            }
            
            session()->flash('message', 'Varsayılan site dili güncellendi.');
        } else {
            session()->flash('error', 'Varsayılan dil güncellenirken hata oluştu.');
        }
    }

    public function updateOrder($itemIds)
    {
        foreach ($itemIds as $index => $id) {
            TenantLanguage::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        if (function_exists('log_activity') && !empty($itemIds)) {
            $firstLanguage = TenantLanguage::find($itemIds[0]);
            if ($firstLanguage) {
                log_activity($firstLanguage, 'sıralama_güncellendi');
            }
        }

        session()->flash('message', 'Sıralama başarıyla güncellendi.');
    }

    public function render()
    {
        $languages = TenantLanguage::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('native_name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('is_default', 'desc')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(); // paginate yerine get kullan çünkü sortable

        return view('languagemanagement::admin.livewire.site-language-component', [
            'languages' => $languages
        ]);
    }
}