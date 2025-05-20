<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PermissionComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $editingPermissionId = null;
    public $editingPermissionName = '';
    public $permissionToDelete = null;

    public function getGroupedPermissionsProperty()
    {
        return Permission::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->get()
            ->sortBy('name')
            ->groupBy(function($permission) {
                return Str::before($permission->name, '.');
            });
    }

    public function startEditPermission($id, $name)
    {
        $this->editingPermissionId = $id;
        $this->editingPermissionName = $name;
    }

    public function saveEditPermission()
    {
        $permission = Permission::find($this->editingPermissionId);
        if ($permission) {
            $oldName = $permission->name;
            $permission->update(['name' => $this->editingPermissionName]);
            log_activity($permission, 'güncellendi', ['old' => $oldName, 'new' => $this->editingPermissionName]);
            
            $this->cancelEditPermission();
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Yetki başarıyla güncellendi.',
                'type' => 'success',
            ]);
        }
    }

    public function cancelEditPermission()
    {
        $this->editingPermissionId = null;
        $this->editingPermissionName = '';
    }

    public function confirmDeletePermission($id)
    {
        $this->permissionToDelete = $id;
        $this->dispatch('openDeleteModal');
    }

    public function deletePermission()
    {
        $permission = Permission::find($this->permissionToDelete);
        if ($permission) {
            log_activity($permission, 'silindi');
            $permission->delete();
            
            $this->permissionToDelete = null;
            $this->dispatch('closeDeleteModal');
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Yetki başarıyla silindi.',
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        return view('usermanagement::livewire.permission-component', [
            'groupedPermissions' => $this->groupedPermissions
        ]);
    }
}