<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;

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

    protected $listeners = ['modulesSaved' => '$refresh'];

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
            $this->tenantId  = $tenant->id;
            $this->name      = $tenant->data['name'] ?? '';
            $this->fullname  = $tenant->data['fullname'] ?? '';
            $this->email     = $tenant->data['email'] ?? '';
            $this->phone     = $tenant->data['phone'] ?? '';
            $this->is_active = $tenant->is_active;
        }
    }

    public function saveTenant($action)
    {
        $this->validate();

        $oldData = null;
        if ($this->tenantId) {
            // Mevcut tenant'ı güncelle
            $oldData = Tenant::find($this->tenantId)?->toArray();
            
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                $tenant->data = [
                    'name'     => $this->name,
                    'fullname' => $this->fullname,
                    'email'    => $this->email,
                    'phone'    => $this->phone,
                ];
                $tenant->is_active = $this->is_active;
                $tenant->save();
                
                log_activity($tenant, 'güncellendi', array_diff_assoc($tenant->toArray(), $oldData));
                
                $wasRecentlyCreated = false;
            }
        } else {
            // Yeni tenant oluştur
            $dbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
            
            $tenant = Tenant::create([
                'title' => $this->name, // title alanını dolduruyoruz
                'tenancy_db_name' => $dbName,
                'data' => [
                    'name'     => $this->name,
                    'fullname' => $this->fullname,
                    'email'    => $this->email,
                    'phone'    => $this->phone,
                ],
                'is_active' => $this->is_active,
            ]);
            
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
        }
    }

    public function manageModules($id)
    {
       $this->tenantId = $id;
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
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenant-component', [
            'tenants' => $this->tenants
        ]);
    }
}