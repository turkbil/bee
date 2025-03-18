<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Modules\UserManagement\App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class RoleManageComponent extends Component
{
    public $roleId;
    public $role;
    public $permissionSearch = '';
    public $inputs = [
        'name' => '',
        'guard_name' => 'admin', 
        'permissions' => [],
        'is_protected' => false
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->roleId = $id;
            $this->role = Role::withCount(['users', 'permissions'])->findOrFail($id);
            
            // Düzenlenebilir mi kontrolü
            if (!$this->role->isEditable()) {
                session()->flash('toast', [
                    'title' => 'Hata!',
                    'message' => 'Bu rol düzenlenemez.',
                    'type' => 'error',
                ]);
                return redirect()->route('admin.role.index');
            }
            
            $this->inputs = $this->role->only(['name', 'guard_name', 'is_protected']);
            $this->inputs['permissions'] = $this->role->permissions->pluck('name')->toArray();
        }
    }

    protected function rules()
    {
        return [
            'inputs.name' => 'required|min:3|max:255|unique:roles,name,' . $this->roleId,
            'inputs.guard_name' => 'required|string',
            'inputs.permissions' => 'nullable|array',
            'inputs.is_protected' => 'boolean'
        ];
    }

    protected $messages = [
        'inputs.name.required' => 'Rol adı zorunludur.',
        'inputs.name.min' => 'Rol adı en az 3 karakter olmalıdır.',
        'inputs.name.max' => 'Rol adı en fazla 255 karakter olabilir.',
        'inputs.name.unique' => 'Bu rol adı zaten kullanılıyor.',
        'inputs.guard_name.required' => 'Guard name zorunludur.',
    ];

    /**
     * Yetki grubundaki tüm yetkileri seçer veya kaldırır
     */
    public function toggleGroupPermissions($group)
    {
        $permissions = $this->getGroupPermissions($group)->pluck('name')->toArray();
        $allSelected = $this->isGroupSelected($group);

        if ($allSelected) {
            // Grup yetkilerini kaldır
            $this->inputs['permissions'] = array_values(array_diff($this->inputs['permissions'], $permissions));
        } else {
            // Grup yetkilerini ekle
            $this->inputs['permissions'] = array_values(array_unique(array_merge($this->inputs['permissions'], $permissions)));
        }
    }

    /**
     * Yetki grubunun tüm yetkilerinin seçili olup olmadığını kontrol eder
     */
    public function isGroupSelected($group): bool
    {
        $permissions = $this->getGroupPermissions($group)->pluck('name')->toArray();
        return empty(array_diff($permissions, $this->inputs['permissions']));
    }

    /**
     * Bir gruptaki yetkileri döndürür
     */
    protected function getGroupPermissions($group)
    {
        return Permission::where('name', 'like', $group . '.%')
            ->where('guard_name', 'admin') 
            ->when($this->permissionSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->permissionSearch . '%');
            })
            ->get();
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->validate();
    
        // Temel rol adlarının kullanımını engelle
        if (in_array($this->inputs['name'], Role::BASE_ROLES)) {
            throw ValidationException::withMessages([
                'inputs.name' => 'Bu rol adı sistem tarafından korunmaktadır.'
            ]);
        }
    
        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            
            // Düzenlenebilir mi kontrolü
            if (!$role->isEditable()) {
                throw ValidationException::withMessages([
                    'inputs.name' => 'Bu rol düzenlenemez.'
                ]);
            }
            
            $oldData = $role->toArray();
            $role->update($this->inputs);
            
            log_activity(
                $role,
                'güncellendi',
                array_diff_assoc($role->toArray(), $oldData)
            );
            
            $message = 'Rol başarıyla güncellendi.';
        } else {
            $role = Role::create($this->inputs);
            
            log_activity(
                $role,
                'oluşturuldu'
            );
            
            $message = 'Rol başarıyla oluşturuldu.';
        }
    
        // Yetkileri senkronize et
        $role->syncPermissions($this->inputs['permissions']);
    
        // Yönlendirme veya toast mesajı
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
            return redirect()->route('admin.usermanagement.role.index');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    
        // Formu sıfırla
        if ($resetForm && !$this->roleId) {
            $this->reset('inputs');
            $this->inputs['guard_name'] = 'admin'; 
        }
    }

    public function render()
    {
        $groupedPermissions = Permission::where('guard_name', 'admin') 
            ->when($this->permissionSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->permissionSearch . '%');
            })
            ->get()
            ->groupBy(function($permission) {
                return Str::before($permission->name, '.');
            });

        return view('usermanagement::livewire.role-manage-component', [
            'groupedPermissions' => $groupedPermissions
        ]);
    }
}