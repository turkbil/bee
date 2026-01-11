<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PermissionManageComponent extends Component
{
    public $permissionId;
    public $inputs = [
        'name' => '',
        'guard_name' => 'admin',
        'module_name' => '',
        'permission_types' => [],
    ];

    public $generatedPermissions = [];
    public $manualPermission = '';
    public $editingIndex = null;

    public function mount($id = null)
    {
        // Varsayılan olarak tüm CRUD işlemleri seçili olsun
        if (empty($this->inputs['permission_types'])) {
            $this->inputs['permission_types'] = ['view', 'create', 'update', 'delete'];
        }

        if ($id) {
            $this->permissionId = $id;
            $permission = Permission::findOrFail($id);
            $this->inputs = $permission->only(['name', 'guard_name']);
        }

        if (request()->has('module')) {
            $module = request()->query('module');
            $this->inputs['module_name'] = $module;

            $permissions = Permission::where('name', 'like', $module . '.%')->get();
            foreach ($permissions as $permission) {
                $type = Str::after($permission->name, '.');
                if (!in_array($type, $this->inputs['permission_types'])) {
                    $this->inputs['permission_types'][] = $type;
                }
                $this->generatedPermissions[] = $permission->name;
            }
        }
    }

    protected function rules()
    {
        return [
            'inputs.name' => 'required|min:3|max:255|unique:permissions,name,' . $this->permissionId,
            'inputs.guard_name' => 'required|string',
            'inputs.module_name' => 'required|string|min:3|max:255',
            'inputs.permission_types' => 'required|array|min:1',
            'manualPermission' => 'nullable|string|min:3|max:255',
        ];
    }

    protected $messages = [
        'inputs.name.required' => 'Yetki adı zorunludur.',
        'inputs.name.min' => 'Yetki adı en az 3 karakter olmalıdır.',
        'inputs.name.max' => 'Yetki adı en fazla 255 karakter olabilir.',
        'inputs.name.unique' => 'Bu yetki adı zaten kullanılıyor.',
        'inputs.guard_name.required' => 'Guard name zorunludur.',
        'inputs.module_name.min' => 'Modül adı en az 3 karakter olmalıdır.',
        'inputs.module_name.max' => 'Modül adı en fazla 255 karakter olabilir.',
        'inputs.module_name.required' => 'Modül adı zorunludur.',
        'inputs.permission_types.required' => 'En az bir yetki tipi seçmelisiniz.',
        'inputs.permission_types.min' => 'En az bir yetki tipi seçmelisiniz.',
        'manualPermission.min' => 'Manuel yetki adı en az 3 karakter olmalıdır.',
        'manualPermission.max' => 'Manuel yetki adı en fazla 255 karakter olabilir.',
    ];

    public function generatePermissions()
    {
        $this->validate([
            'inputs.module_name' => 'required|min:3|max:255',
            'inputs.permission_types' => 'required|array|min:1',
        ]);

        $newPermissions = [];
        foreach ($this->inputs['permission_types'] as $type) {
            $newPermissions[] = Str::slug($this->inputs['module_name']) . '.' . $type;
        }

        // Ana modül yetkisini de ekle
        $newPermissions[] = Str::slug($this->inputs['module_name']);

        $this->generatedPermissions = array_unique(array_merge($this->generatedPermissions, $newPermissions));
    }

    public function addManualPermission()
    {
        $this->validate([
            'manualPermission' => 'required|string|min:3|max:255',
        ]);

        $this->generatedPermissions[] = $this->manualPermission;
        $this->generatedPermissions = array_unique($this->generatedPermissions);
        $this->reset('manualPermission');
    }

    public function removePermission($permission)
    {
        $this->generatedPermissions = array_filter($this->generatedPermissions, fn ($p) => $p !== $permission);
        $this->generatedPermissions = array_values($this->generatedPermissions);
    }

    public function startEdit($index)
    {
        $this->editingIndex = $index;
    }

    public function saveEdit($index)
    {
        $this->editingIndex = null;
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Yetki başarıyla güncellendi.',
            'type' => 'success',
        ]);
    }

    public function cancelEdit()
    {
        $this->editingIndex = null;
    }

    public function deletePermission($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            log_activity($permission, 'silindi');
            $permission->delete();
        }
        
        $this->removePermission($permissionName);

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Yetki başarıyla silindi.',
            'type' => 'success',
        ]);
    }

    public function save($redirect = false, $resetForm = false)
    {
        if (empty($this->generatedPermissions)) {
            $this->validate();
        }
    
        foreach ($this->generatedPermissions as $permissionName) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $this->inputs['guard_name'],
            ]);
            log_activity($permission, 'oluşturuldu');
        }
    
        $message = 'Yetkiler başarıyla kaydedildi.';
    
        if ($redirect) {
            session()->flash('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
            return redirect()->route('admin.usermanagement.permission.index');
        }
    
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => $message,
            'type' => 'success',
        ]);
    
        if ($resetForm) {
            $this->reset(['inputs', 'generatedPermissions', 'manualPermission']);
            $this->inputs['guard_name'] = 'admin';
        }
    }
    
    public function render()
    {
        return view('usermanagement::livewire.permission-manage-component');
    }
}