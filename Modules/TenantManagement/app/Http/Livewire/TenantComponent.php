<?php // Modules/TenantManagement/app/Http/Livewire/TenantComponent.php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\TenantManagement\App\Models\Domain;
use Modules\TenantManagement\App\Models\Tenant;

class TenantComponent extends Component
{
    public $tenants = [];
    public $domains = [];
    public $tenantId, $name, $fullname, $email, $phone, $is_active;
    public $newDomain, $editingDomainId, $editingDomainValue;

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
        $this->is_active = true; // Varsayılan olarak aktif
        $this->fetchTenants();   // Diğer işlemleri çağır
    }

    public function fetchTenants()
    {
        $this->tenants = Tenant::orderBy('created_at', 'desc')->get();
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

        $tenant = Tenant::updateOrCreate(
            ['id' => $this->tenantId],
            [
                'data'      => [
                    'name'     => $this->name,
                    'fullname' => $this->fullname,
                    'email'    => $this->email,
                    'phone'    => $this->phone,
                ],
                'is_active' => $this->is_active,
            ]
        );

        // İşlem türüne göre log kaydı
        $actionType = $tenant->wasRecentlyCreated ? 'eklendi' : 'güncellendi';
        log_activity('Tenant', $actionType, $tenant);

        $this->fetchTenants();
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::find($id);

        if ($tenant) {
            // Log kaydı
            log_activity('Tenant', 'silindi', $tenant);

            $tenant->delete();
            $this->fetchTenants();
        }
    }

    public function loadDomains($tenantId)
    {
        $this->tenantId = $tenantId;
        $this->domains  = Domain::where('tenant_id', $tenantId)->get()->toArray();
    }

    public function addDomain()
    {
        $this->validateOnly('newDomain');

        $domain = Domain::create([
            'domain'    => $this->newDomain,
            'tenant_id' => $this->tenantId,
        ]);

        // Log kaydı
        log_activity('Domain', 'eklendi', $domain, [
            'title' => $domain->domain,
        ]);

        $this->loadDomains($this->tenantId);
        $this->newDomain = '';
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
            $domain->update(['domain' => $this->editingDomainValue]);

            // Log kaydı
            log_activity('Domain', 'güncellendi', $domain, [
                'title' => $domain->domain,
            ]);

            $this->loadDomains($this->tenantId);
            $this->editingDomainId    = null;
            $this->editingDomainValue = '';
        }
    }

    public function deleteDomain($domainId)
    {
        $domain = Domain::find($domainId);

        if ($domain) {
            // Log kaydı
            log_activity('Domain', 'silindi', $domain, [
                'title' => $domain->domain,
            ]);

            $domain->delete();
            $this->loadDomains($this->tenantId);
        }
    }

    public function render()
    {
        return view('tenant::livewire.tenant-component');
    }
}
