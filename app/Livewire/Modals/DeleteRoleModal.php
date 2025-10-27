<?php
namespace App\Livewire\Modals;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class DeleteRoleModal extends Component
{
    public $showModal = false;
    public $roleId;
    public $roleName;

    protected $listeners = ['showDeleteRoleModal'];

    public function showDeleteRoleModal($roleId, $roleName)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->showModal = true;
    }

    public function delete()
    {
        try {
            $role = Role::findOrFail($this->roleId);
            $roleName = $role->name;
            
            log_activity($role, 'silindi', [
                'name' => $roleName
            ]);
            
            $role->delete();

            $this->showModal = false;

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Rol başarıyla silindi.',
                'type' => 'success',
            ]);

            $this->dispatch('refreshRoles');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Silme işlemi sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.delete-role-modal');
    }
} 