<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use App\Models\Domain; // Custom Domain model (extends vendor)
use Illuminate\Support\Facades\DB;
use Modules\ThemeManagement\App\Models\Theme;

#[Layout('admin.layout')]
class TenantComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $name, $fullname, $email, $phone, $is_active;
    public $newDomain, $editingDomainId, $editingDomainValue;
    public $selectedTenantForModules = null;
    public $tenantId = null;
    public $domains = [];
    public $editingTenant = null;
    public $refreshModuleKey = 0;
    public $theme_id;
    public $themes = [];
    public $tenant_ai_provider_id;
    public $tenant_ai_provider_model_id;
    public $availableAiProviders = [];
    public $availableProviderModels = [];

    // Theme Settings
    public $subheader_style = 'glass';
    public $availableSubheaderStyles = [
        'glass' => 'Glass (Şeffaf)',
        'minimal' => 'Minimal (Sade)',
        'hero' => 'Hero (Büyük)',
        'colored' => 'Colored (Renkli)'
    ];
    public $hasCustomSubheader = false;

    protected $listeners = ['modulesSaved' => '$refresh', 'itemDeleted' => '$refresh'];

    protected $rules = [
        'name'      => 'required|string|max:255',
        'fullname'  => 'nullable|string|max:255',
        'email'     => 'nullable|email|max:255',
        'phone'     => 'nullable|string|max:20',
        'is_active' => 'boolean',
        'newDomain' => 'nullable|string|max:255|unique:domains,domain',
        'theme_id'  => 'required|integer|min:0',
        'tenant_ai_provider_id' => 'nullable|integer|exists:ai_providers,id',
        'tenant_ai_provider_model_id' => 'nullable|integer|exists:ai_provider_models,id',
    ];

    public function mount()
    {
        $this->is_active = true;
        // Tema listesi ve başlangıç teması
        // t-* ile başlayan otomatik temaları listede gösterme
        $this->themes = [0 => 'Otomatik (t-{id})'] + Theme::where('is_active', true)
            ->where('name', 'not like', 't-%')
            ->pluck('title', 'theme_id')
            ->toArray();
        $this->theme_id = 0; // Varsayılan olarak "Otomatik" seçili

        // AI Provider listesi yükle
        $this->loadAiProviders();
    }
    
    /**
     * AI Provider listesini yükle
     */
    private function loadAiProviders()
    {
        try {
            $this->availableAiProviders = \Modules\AI\App\Models\AIProvider::getSelectOptions();
            // Varsayılan seçimi sadece yeni tenant için ayarla
            if (!$this->tenantId && count($this->availableAiProviders) > 0) {
                // is_default=1 olan provider'ı bul (OpenAI), yoksa ilk provider
                $defaultProvider = \Modules\AI\App\Models\AIProvider::where('is_default', 1)->first();
                $this->tenant_ai_provider_id = $defaultProvider?->id ?? $this->availableAiProviders[0]['value'] ?? null;
                // Provider'ı seçtikten sonra modellerini yükle
                if ($this->tenant_ai_provider_id) {
                    $this->updatedTenantAiProviderId($this->tenant_ai_provider_id);
                }
            }
        } catch (\Exception $e) {
            $this->availableAiProviders = [];
            $this->tenant_ai_provider_id = null;
        }
    }

    /**
     * Provider değiştiğinde modelleri yükle (YENİ SİSTEM)
     */
    public function updatedTenantAiProviderId($providerId)
    {
        $this->availableProviderModels = [];
        $this->tenant_ai_provider_model_id = null;
        
        if ($providerId) {
            try {
                $provider = \Modules\AI\App\Models\AIProvider::find($providerId);
                if ($provider) {
                    // Yeni sistem: ai_provider_models tablosundan modelleri al (sıralı)
                    $models = $provider->getAvailableModelsWithRates();
                    
                    $this->availableProviderModels = $models->map(function($model) {
                        return [
                            'id' => $model['id'],
                            'label' => $model['model_name'] . ' (' . $model['cost_info'] . ')',
                            'model_name' => $model['model_name'],
                            'is_default' => $model['is_default'],
                            'sort_order' => $model['sort_order']
                        ];
                    })->toArray();
                    
                    // Varsayılan modeli ayarla (en yüksek sort_order veya is_default)
                    $defaultModel = collect($this->availableProviderModels)->where('is_default', true)->first()
                                 ?? collect($this->availableProviderModels)->sortByDesc('sort_order')->first();
                    
                    if ($defaultModel) {
                        $this->tenant_ai_provider_model_id = $defaultModel['id'];
                    }
                }
            } catch (\Exception $e) {
                $this->availableProviderModels = [];
                $this->tenant_ai_provider_model_id = null;
                \Log::error('TenantComponent AI Provider Models Load Error: ' . $e->getMessage());
            }
        }
    }

    public function updatedTenantAiProviderModelId($modelId)
    {
        // Model değiştiğinde JavaScript'e bildir
        if ($modelId && $this->tenant_ai_provider_id) {
            $this->dispatch('modelSelectionChanged', [
                'modelId' => $modelId,
                'providerId' => $this->tenant_ai_provider_id
            ]);
        }
    }
    

    public function getTenantsProperty()
    {
        return Tenant::with('domains')
            ->orderBy($this->sortField, $this->sortDirection)
            ->when($this->search, function($query) {
                return $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhere('fullname', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate($this->perPage);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedPerPage()
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

    public function editTenant($id)
    {
        try {
            $tenantData = DB::table('tenants')->where('id', $id)->first();
            
            if (!$tenantData) {
                $this->dispatch('toast', [
                    'title' => 'Hata',
                    'message' => 'Tenant bulunamadı.',
                    'type' => 'error'
                ]);
                return;
            }
            
            $tenant = Tenant::find($id);
            $this->editingTenant = $tenant;
            $this->tenantId = $id;
            
            // Artık direkt kolonlardan alıyoruz
            $this->name = $tenantData->title ?? '';
            $this->fullname = $tenantData->fullname ?? '';
            $this->email = $tenantData->email ?? '';
            $this->phone = $tenantData->phone ?? '';
            $this->is_active = (bool)$tenantData->is_active;
            // Tema listesi ve mevcut tenant teması
            // t-* ile başlayan otomatik temaları listede gösterme (kendi teması hariç)
            $currentTheme = $tenantData->theme_id ? Theme::find($tenantData->theme_id) : null;
            $themesQuery = Theme::where('is_active', true)->where('name', 'not like', 't-%');

            // Eğer mevcut tema t-* formatındaysa, onu da listeye ekle
            if ($currentTheme && str_starts_with($currentTheme->name, 't-')) {
                $this->themes = [$currentTheme->theme_id => $currentTheme->title . ' (Özel)'] +
                    $themesQuery->pluck('title', 'theme_id')->toArray();
            } else {
                $this->themes = [0 => 'Otomatik (t-{id})'] + $themesQuery->pluck('title', 'theme_id')->toArray();
            }
            $this->theme_id = $tenantData->theme_id ?? 0;

            // Theme Settings (subheader_style vb.)
            $themeSettings = $tenantData->theme_settings ? json_decode($tenantData->theme_settings, true) : [];
            $this->subheader_style = $themeSettings['subheader_style'] ?? 'glass';

            // Seçilen temanın custom subheader'ı var mı kontrol et
            $this->checkCustomSubheader();

            // AI Provider listesi ve mevcut tenant AI provider'ı (YENİ SİSTEM)
            $this->loadAiProviders();
            $this->tenant_ai_provider_id = $tenantData->tenant_ai_provider_id;
            $this->tenant_ai_provider_model_id = $tenantData->tenant_ai_provider_model_id;
            
            // Provider seçiliyse modellerini yükle
            if ($this->tenant_ai_provider_id) {
                $this->updatedTenantAiProviderId($this->tenant_ai_provider_id);
                
                // Model ID'yi de doğru şekilde ayarla
                if ($this->tenant_ai_provider_model_id) {
                    // Seçilen modelin mevcut provider'a ait olup olmadığını kontrol et
                    $modelExists = collect($this->availableProviderModels)
                        ->pluck('id')
                        ->contains($this->tenant_ai_provider_model_id);
                    
                    if (!$modelExists) {
                        // Model mevcut provider'a ait değilse varsayılanı seç
                        $defaultModel = collect($this->availableProviderModels)->where('is_default', true)->first()
                                     ?? collect($this->availableProviderModels)->sortByDesc('sort_order')->first();
                        
                        if ($defaultModel) {
                            $this->tenant_ai_provider_model_id = $defaultModel['id'];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Tenant verileri yüklenirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function saveTenant($action)
    {
        $this->validate();
        
        $wasRecentlyCreated = false;
        
        try {
            if ($this->tenantId) {
                // Mevcut tenant'ı güncelle
                $tenant = Tenant::find($this->tenantId);

                if ($tenant) {
                    // Theme settings JSON oluştur
                    $themeSettings = [
                        'subheader_style' => $this->subheader_style,
                    ];

                    // Otomatik tema seçildiyse (theme_id = 0), yeni t-{id} teması oluştur
                    $finalThemeId = $this->theme_id;
                    if ($this->theme_id == 0) {
                        $autoTheme = $this->createAutoTheme($this->tenantId);
                        $finalThemeId = $autoTheme->theme_id;
                    }

                    // ✅ FİX: Eloquent kullan, double encoding önlenir
                    // Model'de 'theme_settings' => 'array' cast var
                    // Eloquent otomatik encode eder, json_encode() GEREKSIZ!
                    $tenant->update([
                        'title'      => $this->name,
                        'fullname'   => $this->fullname,
                        'email'      => $this->email,
                        'phone'      => $this->phone,
                        'is_active'  => $this->is_active ? 1 : 0,
                        'theme_id'     => $finalThemeId,
                        'theme_settings' => $themeSettings, // Array olarak gönder (cast encode eder)
                        'tenant_ai_provider_id' => $this->tenant_ai_provider_id,
                        'tenant_ai_provider_model_id' => $this->tenant_ai_provider_model_id,
                    ]);

                    // Log işlemi
                    if (function_exists('log_activity')) {
                        log_activity($tenant, 'güncellendi');
                    }
                } else {
                    throw new \Exception("Tenant bulunamadı");
                }
            } else {
                // Yeni tenant oluştur
                // Benzersiz veritabanı adı oluştur (rastgele suffix ekleyerek)
                // Türkçe karakterleri ASCII eşdeğerlerine çevir
                $turkishMap = [
                    'ı' => 'i', 'İ' => 'i', 'ş' => 's', 'Ş' => 's',
                    'ğ' => 'g', 'Ğ' => 'g', 'ü' => 'u', 'Ü' => 'u',
                    'ö' => 'o', 'Ö' => 'o', 'ç' => 'c', 'Ç' => 'c',
                ];
                $cleanName = strtr($this->name, $turkishMap);
                $baseDbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanName));
                $randomSuffix = '_' . substr(md5(mt_rand()), 0, 6);
                $dbName = $baseDbName . $randomSuffix;
                
                // Theme settings JSON oluştur
                $themeSettings = [
                    'subheader_style' => $this->subheader_style,
                ];

                // Boş olan en küçük ID'yi bul (1001 gibi özel ID'leri atlayarak 3'ten devam et)
                $existingIds = Tenant::pluck('id')->toArray();
                $nextId = 3; // 1 ve 2 zaten dolu, 3'ten başla
                while (in_array($nextId, $existingIds)) {
                    $nextId++;
                }

                // Otomatik tema seçildiyse (theme_id = 0), yeni t-{id} teması oluştur
                $finalThemeId = $this->theme_id;
                if ($this->theme_id == 0) {
                    $autoTheme = $this->createAutoTheme($nextId);
                    $finalThemeId = $autoTheme->theme_id;
                }

                // Tenant oluştur (YENİ SİSTEM - Direkt kolonlar)
                $tenant = Tenant::create([
                    'id'              => $nextId,
                    'title'           => $this->name,
                    'fullname'        => $this->fullname,
                    'email'           => $this->email,
                    'phone'           => $this->phone,
                    'tenancy_db_name' => $dbName,
                    'is_active'       => $this->is_active ? 1 : 0,
                    'theme_id'        => $finalThemeId,
                    'theme_settings'  => $themeSettings,
                    'tenant_ai_provider_id' => $this->tenant_ai_provider_id,
                    'tenant_ai_provider_model_id' => $this->tenant_ai_provider_model_id,
                ]);
                
                // Tenant dizinlerini hazırla
                $this->prepareTenantDirectories($tenant->id);
                
                // Log işlemi
                if (function_exists('log_activity')) {
                    log_activity($tenant, 'oluşturuldu');
                }
                
                $wasRecentlyCreated = true;
            }
    
            $this->resetForm();
    
            // Cache temizle
            \Illuminate\Support\Facades\Cache::flush();
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $wasRecentlyCreated ? 'Tenant başarıyla oluşturuldu.' : 'Tenant başarıyla güncellendi.',
                'type' => 'success'
            ]);
    
            // Modali kapat
            $this->dispatch('hideModal', ['id' => 'modal-tenant-edit']);
            $this->dispatch('hideModal', ['id' => 'modal-tenant-add']);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Tenant kaydedilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteTenant($id)
    {
        try {
            $tenant = Tenant::find($id);

            if ($tenant) {
                // Database adını kaydet (tenant silinmeden önce)
                $dbName = $tenant->tenancy_db_name;

                // Tenant dizinlerini temizle
                $this->cleanTenantDirectories($tenant->id);

                // ✅ KRİTİK FİX: Tenant database'ini DROP et
                // Sadece Laravel kaydı silinirse MySQL database ayakta kalır (orphan database)
                // Disk alanı tüketir, temizlik gerekir
                try {
                    if ($dbName) {
                        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
                        \Log::info("Tenant database dropped: {$dbName}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Tenant database DROP failed: {$dbName} - " . $e->getMessage());
                    // Database DROP hatası olsa bile devam et (Laravel kaydını sil)
                }

                // Log işlemi
                if (function_exists('log_activity')) {
                    log_activity($tenant, 'silindi');
                }

                $tenant->delete();

                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Tenant başarıyla silindi.',
                    'type' => 'success'
                ]);

                $this->dispatch('itemDeleted');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Tenant silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function toggleActive($id)
    {
        try {
            $tenant = Tenant::find($id);
            
            if ($tenant) {
                // Mevcut durumun tersini ayarla
                $newStatus = !$tenant->is_active;
                
                // Veritabanını güncelle
                DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->update([
                        'is_active' => $newStatus ? 1 : 0,
                        'updated_at' => now()
                    ]);
                
                // Tüm cache'leri temizle
                \Illuminate\Support\Facades\Cache::flush();
                \Illuminate\Support\Facades\Artisan::call('config:clear');
                \Illuminate\Support\Facades\Artisan::call('route:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');
                
                // Log işlemi
                if (function_exists('log_activity')) {
                    log_activity($tenant, $newStatus ? 'aktifleştirildi' : 'pasifleştirildi');
                }
                
                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Tenant durumu ' . ($newStatus ? 'aktif' : 'pasif') . ' olarak değiştirildi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Tenant durumu değiştirilirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function manageModules($id)
    {
       $this->tenantId = $id;
       $this->refreshModuleKey++;
    }
    
    public function loadDomains($tenantId)
    {
        $this->tenantId = $tenantId;
        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $this->domains = $tenant->domains()->get()->toArray();
        }
    }

    public function addDomain()
    {
        // www prefix'ini otomatik temizle
        $this->newDomain = $this->normalizeDomain($this->newDomain);

        $this->validateOnly('newDomain');

        try {
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                // Unique kontrol (www'suz halde)
                $exists = Domain::where('domain', $this->newDomain)->exists();
                if ($exists) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Bu domain zaten kayıtlı!',
                        'type' => 'error'
                    ]);
                    return;
                }

                // Domain oluştur
                $domain = $tenant->domains()->create([
                    'domain' => $this->newDomain,
                ]);

                // İlk domain ise otomatik primary yap
                $domainCount = Domain::where('tenant_id', $this->tenantId)->count();
                if ($domainCount === 1) {
                    $domain->setAsPrimary();
                }

                // ✅ Web server alias otomasyonu (nginx + apache + SSL)
                try {
                    // 1. Nginx ve Apache'ye ekle
                    \App\Jobs\RegisterDomainInWebServer::dispatchSync($this->newDomain, $this->tenantId);

                    // 2. SSL sertifikasını yenile (queue'da çalışsın)
                    \App\Jobs\RenewSSLCertificate::dispatch();

                    \Log::info("✅ Domain web server'a eklendi: {$this->newDomain}");
                } catch (\Exception $e) {
                    \Log::error("❌ Web server kaydı hatası: {$this->newDomain} - " . $e->getMessage());
                }

                if (function_exists('log_activity')) {
                    log_activity($domain, 'oluşturuldu');
                }

                $this->loadDomains($this->tenantId);
                $this->newDomain = '';

                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Domain başarıyla eklendi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Domain eklenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function startEditingDomain($domainId, $domainValue)
    {
        $this->editingDomainId    = $domainId;
        $this->editingDomainValue = $domainValue;
    }

    public function updateDomain($domainId)
    {
        try {
            // www prefix'ini otomatik temizle
            $this->editingDomainValue = $this->normalizeDomain($this->editingDomainValue);

            $domain = Domain::find($domainId);

            if ($domain) {
                // Unique kontrol (kendi dışında)
                $exists = Domain::where('domain', $this->editingDomainValue)
                    ->where('id', '!=', $domainId)
                    ->exists();

                if ($exists) {
                    $this->dispatch('toast', [
                        'title' => 'Hata!',
                        'message' => 'Bu domain zaten kayıtlı!',
                        'type' => 'error'
                    ]);
                    return;
                }

                $oldDomain = $domain->domain;
                $domain->update(['domain' => $this->editingDomainValue]);

                // ✅ Web server alias güncelleme: Eski sil + yeni ekle
                // DomainUpdated event ile otomatik yapılır
                // Manuel tetikleme gerekirse:
                \App\Jobs\UnregisterDomainAliasFromPlesk::dispatchSync($oldDomain, $domain->tenant_id);
                \App\Jobs\RegisterDomainInWebServer::dispatchSync($this->editingDomainValue, $domain->tenant_id);

                if (function_exists('log_activity')) {
                    log_activity($domain, 'güncellendi');
                }

                $this->loadDomains($this->tenantId);
                $this->editingDomainId    = null;
                $this->editingDomainValue = '';

                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Domain başarıyla güncellendi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Domain güncellenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteDomain($domainId)
    {
        try {
            $domain = Domain::find($domainId);

            if ($domain) {
                // ✅ Web server alias silme DeletingDomain event ile otomatik yapılır
                // Bkz: TenancyServiceProvider -> DeletingDomain event

                if (function_exists('log_activity')) {
                    log_activity($domain, 'silindi');
                }

                $domain->delete();
                $this->loadDomains($this->tenantId);

                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Domain başarıyla silindi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Domain silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function resetForm()
    {
        $this->tenantId = null;
        $this->name = '';
        $this->fullname = '';
        $this->email = '';
        $this->phone = '';
        $this->is_active = true;
        $this->editingTenant = null;
        $this->tenant_ai_provider_id = null;
        $this->tenant_ai_provider_model_id = null;
        $this->availableProviderModels = [];
        $this->subheader_style = 'glass';
        $this->hasCustomSubheader = false;

        // AI Provider listesi yeniden yükle ve varsayılanı ayarla
        $this->loadAiProviders();
    }

    /**
     * Tema değiştiğinde custom subheader kontrolü
     */
    public function updatedThemeId($themeId)
    {
        $this->checkCustomSubheader();
    }

    /**
     * Seçilen temanın custom subheader'ı var mı kontrol et
     */
    protected function checkCustomSubheader()
    {
        $theme = Theme::find($this->theme_id);
        $themeName = $theme ? $theme->name : 'simple';

        $this->hasCustomSubheader = view()->exists("themes.{$themeName}.layouts.partials.subheader");
    }

    /**
     * Tenant için gerekli dizinleri hazırla
     *
     * @param int $tenantId
     */
    protected function prepareTenantDirectories($tenantId)
    {
        // Önce tenant dizini varsa temizle
        $tenantPath = storage_path("tenant{$tenantId}");
        if (\Illuminate\Support\Facades\File::isDirectory($tenantPath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($tenantPath);
        }

        // Framework cache dizini
        $frameworkCachePath = storage_path("tenant{$tenantId}/framework/cache");
        \Illuminate\Support\Facades\File::ensureDirectoryExists($frameworkCachePath, 0775, true);

        // Framework diğer alt dizinleri
        $frameworkPaths = [
            storage_path("tenant{$tenantId}/framework/sessions"),
            storage_path("tenant{$tenantId}/framework/views"),
            storage_path("tenant{$tenantId}/framework/testing"),
        ];

        foreach ($frameworkPaths as $path) {
            \Illuminate\Support\Facades\File::ensureDirectoryExists($path, 0775, true);
        }

        // Diğer gerekli dizinler
        $paths = [
            storage_path("tenant{$tenantId}/app"),
            storage_path("tenant{$tenantId}/app/public"),
            storage_path("tenant{$tenantId}/logs"),
            storage_path("tenant{$tenantId}/sessions"),
        ];

        foreach ($paths as $path) {
            \Illuminate\Support\Facades\File::ensureDirectoryExists($path, 0775, true);
        }

        // Public storage dizini
        $publicStoragePath = public_path("storage/tenant{$tenantId}");
        if (\Illuminate\Support\Facades\File::isDirectory($publicStoragePath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($publicStoragePath);
        }
        \Illuminate\Support\Facades\File::ensureDirectoryExists($publicStoragePath, 0775, true);

        // ✅ KRİTİK FİX: Owner ve permission düzelt (root:root → tuufi.com_:psaserv)
        // File::ensureDirectoryExists() root ile çalıştığında root:root oluşturur
        // Nginx/Apache tuufi.com_:psaserv ile çalışır, erişemez → 403/500 hatası!
        try {
            // Storage dizini owner/permission düzelt
            exec("sudo chown -R tuufi.com_:psaserv " . escapeshellarg($tenantPath));
            exec("sudo find " . escapeshellarg($tenantPath) . " -type d -exec chmod 755 {} \\;");
            exec("sudo find " . escapeshellarg($tenantPath) . " -type f -exec chmod 644 {} \\;");

            // Public storage dizini owner/permission düzelt
            exec("sudo chown -R tuufi.com_:psaserv " . escapeshellarg($publicStoragePath));
            exec("sudo find " . escapeshellarg($publicStoragePath) . " -type d -exec chmod 755 {} \\;");
            exec("sudo find " . escapeshellarg($publicStoragePath) . " -type f -exec chmod 644 {} \\;");
        } catch (\Exception $e) {
            \Log::error("Tenant {$tenantId} permission fix failed: " . $e->getMessage());
        }
    }
    
    /**
     * Otomatik t-{id} teması oluştur
     *
     * @param int $tenantId
     * @return Theme
     */
    protected function createAutoTheme(int $tenantId): Theme
    {
        $themeName = "t-{$tenantId}";

        // Tema zaten varsa döndür
        $existingTheme = Theme::where('name', $themeName)->first();
        if ($existingTheme) {
            return $existingTheme;
        }

        // Yeni tema oluştur
        $theme = Theme::create([
            'name' => $themeName,
            'title' => "Tenant {$tenantId} Teması",
            'slug' => $themeName,
            'folder_name' => $themeName,
            'description' => "Tenant {$tenantId} için otomatik oluşturulan tema",
            'is_active' => true,
            'is_default' => false,
            'available_for_tenants' => [$tenantId],
        ]);

        // Tema klasör yapısını oluştur
        $this->createAutoThemeFiles($themeName, $tenantId);

        \Log::info("Auto theme created: {$themeName} for tenant {$tenantId}");

        return $theme;
    }

    /**
     * Otomatik tema dosyalarını oluştur
     *
     * @param string $themeName
     * @param int $tenantId
     */
    protected function createAutoThemeFiles(string $themeName, int $tenantId): void
    {
        $basePath = resource_path("views/themes/{$themeName}");
        $layoutsPath = "{$basePath}/layouts";
        $assetsPath = "{$basePath}/assets";

        // Klasörleri oluştur
        \Illuminate\Support\Facades\File::ensureDirectoryExists($layoutsPath, 0755, true);
        \Illuminate\Support\Facades\File::ensureDirectoryExists("{$assetsPath}/css", 0755, true);
        \Illuminate\Support\Facades\File::ensureDirectoryExists("{$assetsPath}/js", 0755, true);

        // config.json oluştur
        $configContent = json_encode([
            'name' => $themeName,
            'title' => "Tenant {$tenantId} Teması",
            'version' => '1.0.0',
            'description' => "Tenant {$tenantId} için otomatik oluşturulan tema",
            'extends' => 'simple',
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#f59e0b',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents("{$basePath}/config.json", $configContent);

        // layouts/app.blade.php - simple'dan extend eder
        $appContent = <<<'BLADE'
{{-- Auto-generated theme - extends simple --}}
@include('themes.simple.layouts.header')

{{-- Universal Notification System --}}
@include('themes.simple.layouts.notification')

<main class="flex-1 min-h-[60vh]">
    {{ $slot ?? '' }}

    @php
        ob_start();
    @endphp

    @yield('content')
    @yield('module_content')

    @php
        $content = ob_get_clean();
        echo app('widget.resolver')->resolveWidgetContent($content);
    @endphp
</main>

@include('themes.simple.layouts.footer')
BLADE;
        file_put_contents("{$layoutsPath}/app.blade.php", $appContent);

        // Permission düzelt
        try {
            exec("sudo chown -R tuufi.com_:psaserv " . escapeshellarg($basePath));
            exec("sudo find " . escapeshellarg($basePath) . " -type d -exec chmod 755 {} \\;");
            exec("sudo find " . escapeshellarg($basePath) . " -type f -exec chmod 644 {} \\;");
        } catch (\Exception $e) {
            \Log::error("Auto theme permission fix failed: {$themeName} - " . $e->getMessage());
        }
    }

    /**
     * Tenant dizinlerini temizle
     *
     * @param int $tenantId
     */
    protected function cleanTenantDirectories($tenantId)
    {
        // Tenant dizini varsa temizle
        $tenantPath = storage_path("tenant{$tenantId}");
        if (\Illuminate\Support\Facades\File::isDirectory($tenantPath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($tenantPath);
        }

        // Public storage dizini varsa temizle
        $publicStoragePath = public_path("storage/tenant{$tenantId}");
        if (\Illuminate\Support\Facades\File::isDirectory($publicStoragePath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($publicStoragePath);
        }
    }

    /**
     * Domain'i normalize et (www prefix'ini temizle)
     *
     * @param string $domain
     * @return string
     */
    protected function normalizeDomain(string $domain): string
    {
        // Trim ve lowercase
        $domain = strtolower(trim($domain));

        // www. prefix'ini kaldır
        if (str_starts_with($domain, 'www.')) {
            $domain = substr($domain, 4);
        }

        // Protocol prefix'lerini kaldır (http://, https://)
        $domain = preg_replace('#^https?://#', '', $domain);

        // Son slash'i kaldır
        $domain = rtrim($domain, '/');

        return $domain;
    }

    /**
     * Domain'i primary olarak işaretle
     *
     * @param int $domainId
     * @return void
     */
    public function setPrimaryDomain(int $domainId): void
    {
        try {
            $domain = Domain::find($domainId);

            if ($domain && $domain->tenant_id == $this->tenantId) {
                $domain->setAsPrimary();

                if (function_exists('log_activity')) {
                    log_activity($domain, 'primary domain olarak işaretlendi');
                }

                $this->loadDomains($this->tenantId);

                $this->dispatch('toast', [
                    'title' => 'Başarılı!',
                    'message' => 'Ana domain başarıyla değiştirildi.',
                    'type' => 'success'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Primary domain ayarlanırken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenant-component', [
            'tenants' => $this->tenants
        ]);
    }
}