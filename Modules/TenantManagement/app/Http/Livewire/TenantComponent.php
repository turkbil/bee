<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Log;

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
        'showDeleteModal' => 'triggerDeleteModal'
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

    public function triggerDeleteModal($data)
    {
        $this->dispatch('showDeleteModal', $data);
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
            $oldData = null;
            $wasRecentlyCreated = false;
            
            if ($this->tenantId) {
                // Mevcut tenant'ı güncelle
                $tenant = Tenant::find($this->tenantId);
                if (!$tenant) {
                    throw new \Exception("Tenant bulunamadı (ID: {$this->tenantId})");
                }
                
                $oldData = $tenant->toArray();
                
                $tenant->data = [
                    'name'     => $this->name,
                    'fullname' => $this->fullname,
                    'email'    => $this->email,
                    'phone'    => $this->phone,
                ];
                $tenant->is_active = $this->is_active;
                $tenant->save();
                
                // Page modülünden ilham alarak basitleştirilmiş log
                activity()
                    ->performedOn($tenant)
                    ->causedBy(auth()->user())
                    ->inLog(class_basename($tenant))
                    ->withProperties(['old' => $oldData, 'new' => $tenant->toArray()])
                    ->log("\"" . ($tenant->title ?? $tenant->data['name'] ?? 'Tenant') . "\" güncellendi");
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
                
                // Page modülünden ilham alarak basitleştirilmiş log
                activity()
                    ->performedOn($tenant)
                    ->causedBy(auth()->user())
                    ->inLog(class_basename($tenant))
                    ->withProperties(['new' => $tenant->toArray()])
                    ->log("\"" . ($tenant->title ?? $tenant->data['name'] ?? 'Tenant') . "\" oluşturuldu");
                
                $wasRecentlyCreated = true;
            }
            
            $this->resetForm();
            $this->dispatch('hideModal', ['id' => 'modal-tenant-manage']);

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $wasRecentlyCreated ? 'Tenant başarıyla oluşturuldu.' : 'Tenant başarıyla güncellendi.',
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
        $this->validateOnly('newDomain');

        try {
            $tenant = Tenant::find($this->tenantId);
            if (!$tenant) {
                throw new \Exception("Tenant bulunamadı (ID: {$this->tenantId})");
            }
            
            $domain = $tenant->domains()->create([
                'domain' => $this->newDomain,
            ]);

            // Page modülünden ilham alarak basitleştirilmiş log
            activity()
                ->performedOn($domain)
                ->causedBy(auth()->user())
                ->inLog(class_basename($domain))
                ->withProperties(['new' => $domain->toArray()])
                ->log("\"" . $domain->domain . "\" oluşturuldu");
                
            $this->loadDomains($this->tenantId);
            $this->newDomain = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla eklendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
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
            $domain = Domain::find($domainId);

            if (!$domain) {
                throw new \Exception("Domain bulunamadı (ID: {$domainId})");
            }
            
            $oldDomain = $domain->domain;
            $domain->update(['domain' => $this->editingDomainValue]);

            // Page modülünden ilham alarak basitleştirilmiş log
            activity()
                ->performedOn($domain)
                ->causedBy(auth()->user())
                ->inLog(class_basename($domain))
                ->withProperties(['old' => $oldDomain, 'new' => $this->editingDomainValue])
                ->log("\"" . $domain->domain . "\" güncellendi");

            $this->loadDomains($this->tenantId);
            $this->editingDomainId    = null;
            $this->editingDomainValue = '';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Domain başarıyla güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
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
        $this->tenantId = null;
        $this->name = '';
        $this->fullname = '';
        $this->email = '';
        $this->phone = '';
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