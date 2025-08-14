<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
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
        
        // Varsayılan dil kontrolü (tenants tablosundan)
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        // Varsayılan dil silinemez
        if ($currentTenant && $language->code === $currentTenant->tenant_default_locale) {
            session()->flash('error', 'Varsayılan site dili silinemez.');
            return;
        }

        $siteLanguageService = app(TenantLanguageService::class);
        
        if ($siteLanguageService->deleteTenantLanguage($id)) {
            // Log kaydı
            Log::info('Language deleted', [
                'language_id' => $language->id,
                'language_name' => $language->name,
                'language_code' => $language->code,
                'user_id' => auth()->id(),
                'tenant_id' => tenant('id')
            ]);
            
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
        
        // Varsayılan dil kontrolü (tenants tablosundan)
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        // Varsayılan dil pasif yapılamaz
        if ($currentTenant && $language->code === $currentTenant->tenant_default_locale && $language->is_active) {
            session()->flash('error', 'Varsayılan site dili pasif yapılamaz.');
            return;
        }

        $language->is_active = !$language->is_active;
        
        if ($language->save()) {
            $siteLanguageService = app(TenantLanguageService::class);
            $siteLanguageService->clearTenantLanguageCache();
            
            $status = $language->is_active ? 'aktif' : 'pasif';
            
            // Log kaydı
            Log::info('Language status changed', [
                'language_id' => $language->id,
                'language_name' => $language->name,
                'language_code' => $language->code,
                'old_status' => !$language->is_active ? 'aktif' : 'pasif',
                'new_status' => $status,
                'user_id' => auth()->id(),
                'tenant_id' => tenant('id')
            ]);
            
            if (function_exists('log_activity')) {
                log_activity($language, $status . ' edildi');
            }
            
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

    public function toggleVisibility($id)
    {
        $language = TenantLanguage::find($id);
        
        if (!$language) {
            session()->flash('error', 'Site dili bulunamadı.');
            return;
        }
        
        // Aktif dil gizlenemez
        if ($language->is_active && $language->is_visible) {
            session()->flash('error', 'Aktif site dili gizlenemez. Önce pasif yapın.');
            return;
        }

        $language->is_visible = !$language->is_visible;
        
        if ($language->save()) {
            $siteLanguageService = app(TenantLanguageService::class);
            $siteLanguageService->clearTenantLanguageCache();
            
            $status = $language->is_visible ? 'görünür' : 'gizli';
            $action = $language->is_visible ? 'görünür yapıldı' : 'silindi';
            
            // Log kaydı
            Log::info('Language visibility changed', [
                'language_id' => $language->id,
                'language_name' => $language->name,
                'language_code' => $language->code,
                'old_status' => !$language->is_visible ? 'görünür' : 'gizli',
                'new_status' => $status,
                'user_id' => auth()->id(),
                'tenant_id' => tenant('id')
            ]);
            
            if (function_exists('log_activity')) {
                log_activity($language, $action);
            }
            
            session()->flash('message', "Site dili {$status} yapıldı.");
        } else {
            session()->flash('error', 'Görünürlük güncellenirken hata oluştu.');
        }
    }

    public function render()
    {
        // Tenant varsayılan dilini al
        $currentTenant = null;
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            $currentTenant = tenant();
        } else {
            // Central context'teyse domain'den çözümle
            $host = request()->getHost();
            $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
                ->where('domain', $host)
                ->first();
            $currentTenant = $domain?->tenant;
        }
        
        $defaultLanguageCode = $currentTenant ? $currentTenant->tenant_default_locale : 'tr';
        
        $allLanguages = TenantLanguage::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('native_name', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // 3 seviyeli kategorize
        $activeLanguages = $allLanguages->where('is_active', true)->where('is_visible', true);
        $inactiveLanguages = $allLanguages->where('is_active', false)->where('is_visible', true)->sortBy('name');
        $hiddenLanguages = $allLanguages->where('is_visible', false)->sortBy('name');

        // is_default kolonunu senkronize et
        app(TenantLanguageService::class)->syncDefaultLanguageColumn();

        return view('languagemanagement::admin.livewire.site-language-component', [
            'activeLanguages' => $activeLanguages,
            'inactiveLanguages' => $inactiveLanguages, 
            'hiddenLanguages' => $hiddenLanguages,
            'languages' => $allLanguages // geriye uyumluluk için
        ]);
    }
}