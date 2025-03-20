<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\DB;

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

    protected $listeners = ['modulesSaved' => '$refresh', 'itemDeleted' => '$refresh'];

    protected $rules = [
        'name'      => 'required|string|max:255',
        'fullname'  => 'nullable|string|max:255',
        'email'     => 'nullable|email|max:255',
        'phone'     => 'nullable|string|max:20',
        'is_active' => 'boolean',
        'newDomain' => 'nullable|string|max:255|unique:domains,domain',
    ];

    public function mount()
    {
        $this->is_active = true;
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
                    
                    // Güncelleme zamanı ekleyelim
                    $data['updated_at'] = now()->toDateTimeString();
                    
                    // Direkt veritabanı güncelleme
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update([
                            'title' => $this->name,
                            'is_active' => $this->is_active ? 1 : 0,
                            'data' => empty($data) ? null : json_encode($data),
                            'updated_at' => now()
                        ]);
                    
                    // Güncel tenant'ı yükle
                    $tenant = Tenant::find($tenant->id);
                    
                    // Log işlemi
                    activity()
                        ->performedOn($tenant)
                        ->withProperties([
                            'title' => $this->name,
                            'data' => $data
                        ])
                        ->log('tenant güncellendi');
                } else {
                    throw new \Exception("Tenant bulunamadı");
                }
            } else {
                // Yeni tenant oluştur
                $dbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
                
                // Veri hazırla
                $data = [];
                
                // Sadece dolu alanları ekle
                if (!empty($this->fullname)) $data['fullname'] = $this->fullname;
                if (!empty($this->email)) $data['email'] = $this->email;
                if (!empty($this->phone)) $data['phone'] = $this->phone;
                
                // Zaman bilgisi
                $data['created_at'] = now()->toDateTimeString();
                $data['updated_at'] = now()->toDateTimeString();
                
                // DB insertion
                $tenantId = DB::table('tenants')->insertGetId([
                    'title' => $this->name,
                    'tenancy_db_name' => $dbName,
                    'data' => empty($data) ? null : json_encode($data),
                    'is_active' => $this->is_active ? 1 : 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Güncel tenant'ı yükle
                $tenant = Tenant::find($tenantId);
                
                // Log işlemi
                activity()
                    ->performedOn($tenant)
                    ->withProperties([
                        'title' => $this->name,
                        'data' => $data
                    ])
                    ->log('tenant oluşturuldu');
                
                $wasRecentlyCreated = true;
            }

            $this->resetForm();

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
                activity()
                    ->performedOn($tenant)
                    ->withProperties([
                        'id' => $tenant->id,
                        'title' => $tenant->title
                    ])
                    ->log('tenant silindi');
                    
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
                
                // Log işlemi
                activity()
                    ->performedOn($tenant)
                    ->withProperties([
                        'old_status' => $tenant->is_active,
                        'new_status' => $newStatus
                    ])
                    ->log('tenant durumu değiştirildi');
                
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

                activity()
                    ->performedOn($domain)
                    ->withProperties([
                        'domain' => $domain->domain,
                        'tenant_id' => $tenant->id
                    ])
                    ->log('domain oluşturuldu');
                
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

                activity()
                    ->performedOn($domain)
                    ->withProperties([
                        'old' => $oldDomain,
                        'new' => $this->editingDomainValue
                    ])
                    ->log('domain güncellendi');

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
                activity()
                    ->performedOn($domain)
                    ->withProperties([
                        'domain' => $domain->domain,
                        'tenant_id' => $domain->tenant_id
                    ])
                    ->log('domain silindi');
                    
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
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenant-component', [
            'tenants' => $this->tenants
        ]);
    }
}