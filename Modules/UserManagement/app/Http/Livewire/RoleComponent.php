<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\UserManagement\App\Models\Role;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class RoleComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $selectedItems = [];
    public $selectAll = false;
    public $roleIdToDelete = null;
    public $showDeleteModal = false;

    public function updatingSearch()
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

    public function confirmDelete($id)
    {
        try {
            $role = Role::findOrFail($id);
            if (!$role->isDeletable()) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Bu rol silinemez.',
                    'type' => 'error'
                ]);
                return;
            }

            $this->roleIdToDelete = $id;
            $this->showDeleteModal = true;
            $this->dispatch('showDeleteModal');
        } catch (\Exception $e) {
            Log::error('Rol silme onayı hatası: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Rol bulunamadı.',
                'type' => 'error'
            ]);
        }
    }
 
    public function cancelDelete()
    {
        $this->roleIdToDelete = null;
        $this->showDeleteModal = false;
        $this->dispatch('hideDeleteModal');
    }
 
    public function delete()
    {
        try {
            if (!$this->roleIdToDelete) {
                throw new \Exception('Silinecek rol ID\'si bulunamadı.');
            }
 
            $role = Role::findOrFail($this->roleIdToDelete);
            
            if (!$role->isDeletable()) {
                throw new \Exception('Bu rol silinemez.');
            }
 
            $role->delete();
            
            log_activity(
                $role,
                'silindi'
            );
 
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Rol başarıyla silindi.',
                'type' => 'success'
            ]);
 
            $this->roleIdToDelete = null;
            $this->showDeleteModal = false;
            $this->dispatch('hideDeleteModal');
        } catch (\Exception $e) {
            Log::error('Rol silme hatası: ' . $e->getMessage());
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Rol silinirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
 
    public function render()
    {
        $roles = Role::withCount(['users', 'permissions'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when(!auth()->user()->hasRole('root'), function($query) {
                // Root olmayan kullanıcılar root rolünü göremesin
                $query->where('name', '!=', 'root');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
     
        return view('usermanagement::livewire.role-component', [
            'roles' => $roles
        ]);
    }
 }