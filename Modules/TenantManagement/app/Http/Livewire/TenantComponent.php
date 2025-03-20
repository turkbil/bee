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
                      ->orWhereJsonContains('data->name', $this->search)
                      ->orWhereJsonContains('data->email', $this->search);
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
        $tenant = Tenant::find($id);

        if ($tenant) {
            $this->editingTenant = $tenant;
            $this->tenantId = $tenant->id;
            
            // Data NULL olabilir, bu durumu ele alalım
            $data = $tenant->data ?? [];
            
            // Form alanlarını doldur
            $this->name = $data['name'] ?? ($tenant->title ?? '');
            $this->fullname = $data['fullname'] ?? '';
            $this->email = $data['email'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->is_active = $tenant->is_active ?? true;
        } else {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Tenant bulunamadı.',
                'type' => 'error'
            ]);
        }
    }

    public function saveTenant($action)
    {
        $this->validate();
        
        $wasRecentlyCreated = false;
        
        // Yeni tenant verileri
        $newData = [
            'name'     => $this->name,
            'fullname' => $this->fullname,
            'email'    => $this->email,
            'phone'    => $this->phone,
            'updated_at' => now()->toDateTimeString()
        ];
        
        if ($this->tenantId) {
            // Mevcut tenant'ı güncelle
            // Veritabanında direkt SQL sorgusu kullanarak data sütununu JSON olarak güncelleyelim
            $affected = DB::table('tenants')
                ->where('id', $this->tenantId)
                ->update([
                    'data' => json_encode($newData),
                    'is_active' => $this->is_active ? 1 : 0,
                    'updated_at' => now()
                ]);
                
            if ($affected) {
                $tenant = Tenant::find($this->tenantId);
                log_activity($tenant, 'güncellendi');
            }
        } else {
            // Yeni tenant oluştur
            $dbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
            
            // Oluşturma zamanını ekle
            $newData['created_at'] = now()->toDateTimeString();
            
            $tenant = new Tenant();
            $tenant->title = $this->name;
            $tenant->tenancy_db_name = $dbName;
            $tenant->data = $newData; // Cast edilen sütun
            $tenant->is_active = $this->is_active;
            $tenant->save();
            
            log_activity($tenant, 'oluşturuldu');
            
            $wasRecentlyCreated = true;
        }

        $this->resetForm();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $wasRecentlyCreated ? 'Tenant başarıyla oluşturuldu.' : 'Tenant başarıyla güncellendi.',
            'type' => 'success'
        ]);

        if ($action === 'close') {
            $this->dispatch('hideModal', ['id' => 'modal-tenant-edit']);
            $this->dispatch('hideModal', ['id' => 'modal-tenant-add']);
        }
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::find($id);

        if ($tenant) {
            log_activity($tenant, 'silindi');
            $tenant->delete();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Tenant başarıyla silindi.',
                'type' => 'success'
            ]);
            
            $this->dispatch('itemDeleted');
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

        $tenant = Tenant::find($this->tenantId);
        if ($tenant) {
            $domain = $tenant->domains()->create([
                'domain' => $this->newDomain,
            ]);

            log_activity($domain, 'oluşturuldu');
            
            $this->loadDomains($this->tenantId);
            $this->newDomain = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla eklendi.',
                'type' => 'success'
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
        $domain = Domain::find($domainId);

        if ($domain) {
            $oldDomain = $domain->domain;
            $domain->update(['domain' => $this->editingDomainValue]);

            log_activity($domain, 'güncellendi', [
                'old' => $oldDomain,
                'new' => $this->editingDomainValue
            ]);

            $this->loadDomains($this->tenantId);
            $this->editingDomainId    = null;
            $this->editingDomainValue = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla güncellendi.',
                'type' => 'success'
            ]);
        }
    }

    public function deleteDomain($domainId)
    {
        $domain = Domain::find($domainId);

        if ($domain) {
            log_activity($domain, 'silindi');
            $domain->delete();
            $this->loadDomains($this->tenantId);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla silindi.',
                'type' => 'success'
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