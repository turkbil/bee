<?php
namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\TenantManagement\Entities\Tenant;

class TenantComponent extends Component
{
    public $tenants;               // Tenant listesi
    public $selectedTenant = null; // Düzenlenecek tenant
    public $name, $email, $phone, $is_active;

    public function mount()
    {
        $this->tenants = Tenant::all();
    }

    public function editTenant($id)
    {
        $tenant               = Tenant::findOrFail($id);
        $this->selectedTenant = $tenant->id;
        $this->name           = $tenant->name;
        $this->email          = $tenant->email;
        $this->phone          = $tenant->phone;
        $this->is_active      = $tenant->is_active;
    }

    public function updateTenant()
    {
        $this->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $tenant = Tenant::findOrFail($this->selectedTenant);
        $tenant->update([
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'is_active' => $this->is_active,
        ]);

        $this->tenants = Tenant::all();
        $this->resetForm();
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();

        $this->tenants = Tenant::all();
    }

    public function resetForm()
    {
        $this->selectedTenant = null;
        $this->name           = '';
        $this->email          = '';
        $this->phone          = '';
        $this->is_active      = '';
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenant-component');
    }
}
