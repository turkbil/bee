<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
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
    public $default_ai_provider_id;
    public $default_ai_model;
    public $availableAiProviders = [];
    public $availableModels = [];

    protected $listeners = ['modulesSaved' => '$refresh', 'itemDeleted' => '$refresh'];

    protected $rules = [
        'name'      => 'required|string|max:255',
        'fullname'  => 'nullable|string|max:255',
        'email'     => 'nullable|email|max:255',
        'phone'     => 'nullable|string|max:20',
        'is_active' => 'boolean',
        'newDomain' => 'nullable|string|max:255|unique:domains,domain',
        'theme_id'  => 'required|integer',
        'default_ai_provider_id' => 'nullable|integer|exists:ai_providers,id',
        'default_ai_model' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->is_active = true;
        // Tema listesi ve başlangıç teması
        $this->themes = Theme::where('is_active', true)->pluck('title','theme_id')->toArray();
        $this->theme_id = $this->themes ? array_key_first($this->themes) : 1;
        
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
                $this->default_ai_provider_id = $this->availableAiProviders[0]['value'] ?? null;
            }
        } catch (\Exception $e) {
            $this->availableAiProviders = [];
            $this->default_ai_provider_id = null;
        }
    }

    /**
     * Provider değiştiğinde modelleri yükle
     */
    public function updatedDefaultAiProviderId($providerId)
    {
        $this->availableModels = [];
        $this->default_ai_model = null;
        
        if ($providerId) {
            try {
                $provider = \Modules\AI\App\Models\AIProvider::find($providerId);
                if ($provider && $provider->available_models) {
                    // Model-based credit rates entegrasyonu ile modellerle birlikte maliyet bilgisi
                    $this->availableModels = collect($provider->available_models)->map(function($model, $key) use ($provider) {
                        $modelName = is_string($model) ? $model : $key;
                        $modelLabel = is_array($model) ? ($model['name'] ?? $key) : $model;
                        
                        // Model için credit rate'i kontrol et
                        $creditRate = $provider->getModelRate($modelName);
                        $costInfo = '';
                        
                        if ($creditRate) {
                            $inputCost = $creditRate->input_cost_per_1k ?? 0;
                            $outputCost = $creditRate->output_cost_per_1k ?? 0;
                            $costInfo = $inputCost > 0 || $outputCost > 0 
                                ? sprintf('%s/%s kredi/1K token', $inputCost, $outputCost)
                                : '';
                        } else {
                            // Fallback: available_models'dan maliyet bilgisi
                            if (is_array($model) && isset($model['input_cost'])) {
                                $costInfo = '$' . $model['input_cost'] . '/' . $model['output_cost'];
                            }
                        }
                        
                        return [
                            'value' => $modelName,
                            'label' => $modelLabel,
                            'cost' => $costInfo,
                            'credit_rate_id' => $creditRate ? $creditRate->id : null
                        ];
                    })->toArray();
                    
                    // Varsayılan modeli ayarla
                    $this->default_ai_model = $provider->default_model;
                }
            } catch (\Exception $e) {
                $this->availableModels = [];
                $this->default_ai_model = null;
            }
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
                      ->orWhere(function ($q) {
                          $q->whereRaw("JSON_EXTRACT(data, '$.fullname') LIKE ?", ['%' . $this->search . '%'])
                            ->orWhereRaw("JSON_EXTRACT(data, '$.email') LIKE ?", ['%' . $this->search . '%'])
                            ->orWhereRaw("JSON_EXTRACT(data, '$.phone') LIKE ?", ['%' . $this->search . '%']);
                      });
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
            
            $data = json_decode($tenantData->data, true) ?? [];
            
            $this->name = $tenantData->title ?? '';
            $this->fullname = $data['fullname'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->is_active = (bool)$tenantData->is_active;
            // Tema listesi ve mevcut tenant teması
            $this->themes = Theme::where('is_active', true)->pluck('title','theme_id')->toArray();
            $this->theme_id = $tenantData->theme_id ?? (array_key_first($this->themes) ?? 1);
            // AI Provider listesi ve mevcut tenant AI provider'ı
            $this->loadAiProviders();
            $this->default_ai_provider_id = $tenantData->default_ai_provider_id;
            $this->default_ai_model = $data['default_ai_model'] ?? null;
            
            // Provider seçiliyse modellerini yükle
            if ($this->default_ai_provider_id) {
                $this->updatedDefaultAiProviderId($this->default_ai_provider_id);
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
                    // Veri hazırla
                    $data = [];
                    
                    // Sadece dolu alanları ekle
                    if (!empty($this->fullname)) $data['fullname'] = $this->fullname;
                    if (!empty($this->email)) $data['email'] = $this->email;
                    if (!empty($this->phone)) $data['phone'] = $this->phone;
                    if (!empty($this->default_ai_model)) $data['default_ai_model'] = $this->default_ai_model;
                    
                    // Güncelleme zamanı ekleyelim
                    $data['updated_at'] = now()->toDateTimeString();
                    
                    // Direkt veritabanı güncelleme
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update([
                            'title'      => $this->name,
                            'is_active'  => $this->is_active ? 1 : 0,
                            'data'      => empty($data) ? null : json_encode($data),
                            'theme_id'     => $this->theme_id,
                            'default_ai_provider_id' => $this->default_ai_provider_id,
                            'updated_at' => now()
                        ]);
                    
                    // Güncel tenant'ı yükle
                    $tenant = Tenant::find($tenant->id);
                    
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
                $baseDbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
                $randomSuffix = '_' . substr(md5(mt_rand()), 0, 6);
                $dbName = $baseDbName . $randomSuffix;
                
                // Veri hazırla
                $data = [];
                
                // Sadece dolu alanları ekle
                if (!empty($this->fullname)) $data['fullname'] = $this->fullname;
                if (!empty($this->email)) $data['email'] = $this->email;
                if (!empty($this->phone)) $data['phone'] = $this->phone;
                if (!empty($this->default_ai_model)) $data['default_ai_model'] = $this->default_ai_model;
                
                // Zaman bilgisi
                $data['created_at'] = now()->toDateTimeString();
                $data['updated_at'] = now()->toDateTimeString();
                
                // Tenant oluştur
                $tenant = Tenant::create([
                    'title'           => $this->name,
                    'tenancy_db_name' => $dbName,
                    'is_active'       => $this->is_active ? 1 : 0,
                    'data'            => empty($data) ? null : $data,
                    'theme_id'        => $this->theme_id,
                    'default_ai_provider_id' => $this->default_ai_provider_id,
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
                // Tenant dizinlerini temizle
                $this->cleanTenantDirectories($tenant->id);
                
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
        $this->validateOnly('newDomain');

        try {
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                $domain = $tenant->domains()->create([
                    'domain' => $this->newDomain,
                ]);

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
            $domain = Domain::find($domainId);

            if ($domain) {
                $oldDomain = $domain->domain;
                $domain->update(['domain' => $this->editingDomainValue]);

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
        $this->default_ai_model = null;
        $this->availableModels = [];
        
        // AI Provider listesi yeniden yükle ve varsayılanı ayarla
        $this->loadAiProviders();
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

    public function render()
    {
        return view('tenantmanagement::livewire.tenant-component', [
            'tenants' => $this->tenants
        ]);
    }
}