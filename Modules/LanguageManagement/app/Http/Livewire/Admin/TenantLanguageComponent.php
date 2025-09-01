<?php

namespace Modules\LanguageManagement\app\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Modules\LanguageManagement\app\Services\TenantLanguageService;
use Modules\LanguageManagement\app\Models\TenantLanguage;
use App\Services\LanguageCleanupService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Collator;

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

        // Kullanılan (aktif) dil silinemez
        if ($language->is_active) {
            session()->flash('error', 'Aktif kullanımdaki dil silinemez. Önce pasif yapın.');
            return;
        }

        $siteLanguageService = app(TenantLanguageService::class);
        $languageCode = $language->code; // Silmeden önce kod'u sakla
        
        if ($siteLanguageService->deleteTenantLanguage($id)) {
            // JSON temizleme işlemini başlat
            try {
                $cleanupService = app(LanguageCleanupService::class);
                $cleanupResult = $cleanupService->cleanupLanguagesFromAllModules([$languageCode]);
                
                Log::info('Language deleted with JSON cleanup', [
                    'language_id' => $language->id,
                    'language_name' => $language->name,
                    'language_code' => $languageCode,
                    'cleanup_result' => $cleanupResult,
                    'user_id' => auth()->id(),
                    'tenant_id' => tenant('id')
                ]);
                
                session()->flash('message', 
                    "Site dili başarıyla silindi. {$cleanupResult['total_updated_rows']} kayıt güncellendi, " .
                    count($cleanupResult['processed_tables']) . " tablo temizlendi."
                );
                
            } catch (\Exception $e) {
                Log::error('JSON cleanup failed after language deletion', [
                    'language_code' => $languageCode,
                    'error' => $e->getMessage(),
                    'tenant_id' => tenant('id')
                ]);
                
                session()->flash('message', 'Site dili silindi ancak JSON temizlemede sorun yaşandı.');
            }
            
            if (function_exists('log_activity')) {
                log_activity($language, 'silindi');
            }
            
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

        $oldStatus = $language->is_active;
        $language->is_active = !$language->is_active;
        
        if ($language->save()) {
            $siteLanguageService = app(TenantLanguageService::class);
            $siteLanguageService->clearTenantLanguageCache();
            
            // Cache'leri temizle
            $this->clearAllCaches();
            
            $status = $language->is_active ? 'aktif' : 'pasif';
            
            // Eğer pasif yapıldıysa JSON temizleme işlemi başlat
            if ($oldStatus && !$language->is_active) {
                try {
                    $cleanupService = app(LanguageCleanupService::class);
                    $cleanupResult = $cleanupService->cleanupLanguagesFromAllModules([$language->code]);
                    
                    Log::info('Language deactivated with JSON cleanup', [
                        'language_id' => $language->id,
                        'language_name' => $language->name,
                        'language_code' => $language->code,
                        'cleanup_result' => $cleanupResult,
                        'user_id' => auth()->id(),
                        'tenant_id' => tenant('id')
                    ]);
                    
                    session()->flash('message', 
                        "Site dili {$status} yapıldı. {$cleanupResult['total_updated_rows']} kayıt güncellendi, " .
                        count($cleanupResult['processed_tables']) . " tablo temizlendi."
                    );
                    
                } catch (\Exception $e) {
                    Log::error('JSON cleanup failed after language deactivation', [
                        'language_code' => $language->code,
                        'error' => $e->getMessage(),
                        'tenant_id' => tenant('id')
                    ]);
                    
                    session()->flash('message', "Site dili {$status} yapıldı ancak JSON temizlemede sorun yaşandı.");
                }
            } else {
                session()->flash('message', "Site dili {$status} yapıldı.");
            }
            
            // Log kaydı
            Log::info('Language status changed', [
                'language_id' => $language->id,
                'language_name' => $language->name,
                'language_code' => $language->code,
                'old_status' => $oldStatus ? 'aktif' : 'pasif',
                'new_status' => $status,
                'user_id' => auth()->id(),
                'tenant_id' => tenant('id')
            ]);
            
            if (function_exists('log_activity')) {
                log_activity($language, $status . ' edildi');
            }
            
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

    public function updateOrder($list)
    {
        // Livewire.dispatch'den gelen veriyi işle
        if (isset($list['list']) && is_array($list['list'])) {
            $items = $list['list'];
        } else {
            $items = $list;
        }
        
        // Sıralama güncelleme
        foreach ($items as $item) {
            $id = isset($item['value']) ? $item['value'] : $item['id'];
            $order = isset($item['order']) ? $item['order'] : ($item['sort_order'] ?? 1);
            
            TenantLanguage::where('id', $id)->update(['sort_order' => $order]);
        }

        if (function_exists('log_activity') && !empty($items)) {
            $firstId = isset($items[0]['value']) ? $items[0]['value'] : $items[0]['id'];
            $firstLanguage = TenantLanguage::find($firstId);
            if ($firstLanguage) {
                log_activity($firstLanguage, 'sıralama_güncellendi');
            }
        }

        Log::info('Language order updated', [
            'items' => $items,
            'tenant_id' => tenant('id'),
            'user_id' => auth()->id()
        ]);

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
            
            // Cache'leri temizle
            $this->clearAllCaches();
            
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

    private function clearAllCaches()
    {
        try {
            // Cache'leri temizle
            Cache::flush();
            
            // Artisan komutlarıyla ekstra temizlik
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            Log::info('All caches cleared after language status change', [
                'tenant_id' => tenant('id'),
                'user_id' => auth()->id()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cache clearing failed', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id')
            ]);
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

        // 3 seviyeli kategorize (Türkçe locale ile sıralama)
        $collator = new Collator('tr_TR');
        
        $activeLanguages = $allLanguages->where('is_active', true)->where('is_visible', true);
        $inactiveLanguages = $allLanguages->where('is_active', false)->where('is_visible', true)
            ->sortBy('name', SORT_REGULAR, false)->sort(function($a, $b) use ($collator) {
                return $collator->compare($a->name, $b->name);
            });
        $hiddenLanguages = $allLanguages->where('is_visible', false)
            ->sortBy('name', SORT_REGULAR, false)->sort(function($a, $b) use ($collator) {
                return $collator->compare($a->name, $b->name);
            });

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