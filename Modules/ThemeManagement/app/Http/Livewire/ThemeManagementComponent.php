<?php
namespace Modules\ThemeManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\ThemeManagement\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\ThemeManagement\App\Models\Theme;
use App\Models\Tenant;

#[Layout('admin.layout')]
class ThemeManagementComponent extends Component
{
    use WithPagination, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 12;

    #[Url]
    public $sortField = 'theme_id';

    #[Url]
    public $sortDirection = 'desc';

    public $showTenants = false;
    public $isCentral = false;
    public $tenants = [];
    public $currentTenantId = null;

    // Tema seçimi için (tenant'lar)
    public $selectedThemeId = null;
    public $selectedSubheaderStyle = 'glass';

    public function mount()
    {
        // Central domain kontrolü
        $centralDomains = config('tenancy.central_domains', []);
        $currentHost = request()->getHost();
        $this->isCentral = in_array($currentHost, $centralDomains);

        // Tenant listesi (Central için)
        if ($this->isCentral) {
            $this->tenants = Tenant::select('id', 'title')->orderBy('id')->get();
        }

        // Mevcut tenant ID
        if (function_exists('tenant') && tenant()) {
            $this->currentTenantId = tenant('id');
            $tenantData = tenant();
            $this->selectedThemeId = $tenantData->theme_id;
            $themeSettings = $tenantData->theme_settings ?? [];

            // theme_settings string olarak gelebilir
            if (is_string($themeSettings)) {
                $themeSettings = json_decode($themeSettings, true) ?? [];
            }

            $this->selectedSubheaderStyle = $themeSettings['subheader_style'] ?? '';
        }

        \Log::info('ThemeManagement mount', [
            'isCentral' => $this->isCentral,
            'currentTenantId' => $this->currentTenantId,
            'selectedThemeId' => $this->selectedThemeId,
            'selectedSubheaderStyle' => $this->selectedSubheaderStyle,
        ]);
    }

    public function toggleTenants()
    {
        $this->showTenants = !$this->showTenants;
    }

    protected function getModelClass()
    {
        return Theme::class;
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
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $theme = Theme::where('theme_id', $id)->first();
    
        if ($theme) {
            $theme->update(['is_active' => !$theme->is_active]);
            
            // Tüm cache'leri temizle
            app('App\Services\ThemeService')->clearThemeCache();
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            
            log_activity(
                $theme,
                $theme->is_active ? 'aktif edildi' : 'pasif edildi'
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$theme->title}\" " . ($theme->is_active ? 'aktif' : 'pasif') . " edildi.",
                'type' => $theme->is_active ? 'success' : 'warning',
            ]);
        }
    }
    
    public function setDefault($id)
    {
        // Önce tüm temaları varsayılan olmaktan çıkar
        Theme::where('is_default', true)->update(['is_default' => false]);

        // Seçilen temayı varsayılan yap
        $theme = Theme::findOrFail($id);
        $theme->update(['is_default' => true]);

        // Tüm cache'leri temizle
        app('App\Services\ThemeService')->clearThemeCache();
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        log_activity(
            $theme,
            'varsayılan tema olarak ayarlandı'
        );

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => "\"{$theme->title}\" varsayılan tema olarak ayarlandı.",
            'type' => 'success',
        ]);
    }

    /**
     * Tenant erişim durumunu değiştir (Central only)
     */
    public function toggleTenantAccess($themeId, $tenantId)
    {
        if (!$this->isCentral) {
            return;
        }

        $theme = Theme::find($themeId);
        if (!$theme) {
            return;
        }

        $available = $theme->available_for_tenants ?? [];

        // "all" varsa, tüm tenant'ları ekle ve seçileni çıkar
        if (empty($available) || in_array('all', $available)) {
            $available = $this->tenants->pluck('id')->toArray();
            $available = array_diff($available, [$tenantId]);
        } else {
            // Toggle: varsa çıkar, yoksa ekle
            if (in_array($tenantId, $available)) {
                $available = array_diff($available, [$tenantId]);
            } else {
                $available[] = $tenantId;
            }
        }

        // Tüm tenant'lar seçiliyse "all" yap
        if (count($available) >= $this->tenants->count()) {
            $available = null; // null = herkese açık
        } else {
            $available = array_values($available);
        }

        $theme->update(['available_for_tenants' => $available]);

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Erişim ayarları güncellendi.',
            'type' => 'success',
        ]);
    }

    /**
     * Tenant için tema seç ve otomatik kaydet
     */
    public function selectTheme($themeId)
    {
        if (!$this->currentTenantId) {
            return;
        }

        $theme = Theme::find($themeId);
        if (!$theme || !$theme->isAvailableForTenant($this->currentTenantId)) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu temaya erişim izniniz yok.',
                'type' => 'error',
            ]);
            return;
        }

        $this->selectedThemeId = $themeId;

        // Otomatik kaydet
        $this->saveThemeSettings();
    }

    /**
     * Tema ve subheader ayarlarını kaydet
     */
    public function saveThemeSettings()
    {
        \Log::info('saveThemeSettings called', [
            'currentTenantId' => $this->currentTenantId,
            'selectedThemeId' => $this->selectedThemeId,
            'selectedSubheaderStyle' => $this->selectedSubheaderStyle,
        ]);

        if (!$this->currentTenantId) {
            \Log::warning('No currentTenantId');
            return;
        }

        $tenant = Tenant::find($this->currentTenantId);
        if (!$tenant) {
            \Log::warning('Tenant not found: ' . $this->currentTenantId);
            return;
        }

        // Tema erişim kontrolü
        $theme = Theme::find($this->selectedThemeId);
        if (!$theme || !$theme->isAvailableForTenant($this->currentTenantId)) {
            \Log::warning('Theme access denied', [
                'themeId' => $this->selectedThemeId,
                'tenantId' => $this->currentTenantId,
            ]);
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Bu temaya erişim izniniz yok.',
                'type' => 'error',
            ]);
            return;
        }

        // Kaydet - Central DB'deki tenants tablosuna
        \DB::connection('mysql')->table('tenants')
            ->where('id', $this->currentTenantId)
            ->update([
                'theme_id' => $this->selectedThemeId,
                'theme_settings' => json_encode([
                    'subheader_style' => $this->selectedSubheaderStyle ?: null,
                ]),
                'updated_at' => now(),
            ]);

        // Universal cache temizleme - tema değişikliği tüm sayfaları etkiler
        // Bu yüzden global=true kullanıyoruz (sadece bu tenant için yeterli değil)
        clear_all_caches('theme_change', true);

        // ThemeService'e özel cache temizleme
        try {
            $themeService = app('App\Services\ThemeService');
            $themeService->clearThemeCache($this->currentTenantId);
        } catch (\Exception $e) {
            \Log::debug('ThemeService cache clear skipped: ' . $e->getMessage());
        }

        \Log::info('Theme changed and all caches cleared', [
            'tenant_id' => $this->currentTenantId,
            'theme_id' => $this->selectedThemeId
        ]);

        log_activity($tenant, 'tema ayarları güncellendi');

        // Toast JavaScript tarafından gösterilecek (çift mesaj olmasın)
    }

    public function render()
    {
        $query = Theme::where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('folder_name', 'like', '%' . $this->search . '%');
            });
    
        $themes = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    
        return view('thememanagement::livewire.theme-management-component', [
            'themes' => $themes,
        ]);
    }
}