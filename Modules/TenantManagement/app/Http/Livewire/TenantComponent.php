<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layout')]
class TenantComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';
    
    #[Url]
    public $perPage = 10;
    
    #[Url]
    public $sortField = 'created_at';
    
    #[Url]
    public $sortDirection = 'desc';

    public $name, $fullname, $email, $phone, $is_active;
    public $newDomain, $editingDomainId, $editingDomainValue;
    public $tenantId = null;
    public $domains = [];
    
    protected $listeners = [
        'modulesSaved' => '$refresh',
        'itemDeleted' => '$refresh',
        'refreshDomains' => 'loadDomains',
        'refreshList' => '$refresh',
    ];

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
        $this->resetForm();
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

    public function saveTenant()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                if ($this->tenantId) {
                    $tenant = Tenant::findOrFail($this->tenantId);
                    $tenant->data = [
                        'name'     => $this->name,
                        'fullname' => $this->fullname,
                        'email'    => $this->email,
                        'phone'    => $this->phone,
                    ];
                    $tenant->is_active = $this->is_active;
                    $tenant->save();
                    
                    activity()
                        ->performedOn($tenant)
                        ->causedBy(auth()->user())
                        ->log("güncellendi");
                    
                    $message = 'Tenant başarıyla güncellendi.';
                } else {
                    $dbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $this->name));
                    
                    $tenant = Tenant::create([
                        'title' => $this->name,
                        'tenancy_db_name' => $dbName,
                        'data' => [
                            'name'     => $this->name,
                            'fullname' => $this->fullname,
                            'email'    => $this->email,
                            'phone'    => $this->phone,
                        ],
                        'is_active' => $this->is_active,
                    ]);
                    
                    activity()
                        ->performedOn($tenant)
                        ->causedBy(auth()->user())
                        ->log("oluşturuldu");
                        
                    $message = 'Tenant başarıyla oluşturuldu.';
                }
            });

            $this->resetForm();
            $this->dispatch('hideModal', ['id' => 'modal-tenant-manage']);

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Tenant işlemi hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
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
        $this->validate([
            'newDomain' => 'required|string|max:255|unique:domains,domain'
        ]);

        try {
            DB::beginTransaction();
            
            $tenant = Tenant::find($this->tenantId);
            if (!$tenant) {
                throw new \Exception("Tenant bulunamadı (ID: {$this->tenantId})");
            }
            
            $domain = $tenant->domains()->create([
                'domain' => $this->newDomain,
            ]);

            activity()
                ->performedOn($domain)
                ->causedBy(auth()->user())
                ->log("oluşturuldu");
                
            DB::commit();
            $this->loadDomains($this->tenantId);
            $this->newDomain = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla eklendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Domain ekleme hatası: ' . $e->getMessage());
            
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
            DB::beginTransaction();
            
            $domain = Domain::find($domainId);

            if (!$domain) {
                throw new \Exception("Domain bulunamadı (ID: {$domainId})");
            }
            
            $domain->update(['domain' => $this->editingDomainValue]);

            activity()
                ->performedOn($domain)
                ->causedBy(auth()->user())
                ->log("güncellendi");

            DB::commit();
            $this->loadDomains($this->tenantId);
            $this->editingDomainId = null;
            $this->editingDomainValue = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Domain güncelleme hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Domain güncellenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function resetForm()
    {
        $this->reset(['tenantId', 'name', 'fullname', 'email', 'phone', 'newDomain', 'domains']);
        $this->is_active = true;
    }

    public function render()
    {
        $query = Tenant::with('domains')
            ->orderBy($this->sortField, $this->sortDirection)
            ->when($this->search, function($query) {
                return $query->where(function ($q) {
                    $q->where('id', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhereJsonContains('data->name', $this->search)
                      ->orWhereJsonContains('data->email', $this->search);
                });
            });
            
        $tenants = $query->paginate($this->perPage);
        
        return view('tenantmanagement::livewire.tenant-component', [
            'tenants' => $tenants
        ]);
    }
}