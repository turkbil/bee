<?php

namespace Modules\UserManagement\App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\App\Http\Livewire\Traits\WithImageUpload;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class UserManageComponent extends Component
{
    use WithFileUploads;

    public $userId;
    public $inputs = [];
    public $allRoles;
    public $allPermissions;
    public $avatar;
    public $avatarUrl;

    protected $rules = [
        'inputs.name' => 'required|min:3',
        'inputs.email' => 'required|email',
        'inputs.password' => 'nullable|min:6',
        'inputs.is_active' => 'boolean',
        'inputs.role_id' => 'nullable|exists:roles,name',
        'inputs.permissions' => 'nullable|array',
        'inputs.permissions.*' => 'exists:permissions,id',
    ];

    public function mount($id = null)
    {
       $this->inputs = [
           'name' => '',
           'email' => '',
           'password' => '',
           'is_active' => true,
           'role_id' => null,
           'permissions' => []
       ];
    
       $this->allRoles = Role::all();
       $this->allPermissions = Permission::all();
       
       if ($id) {
           $this->userId = $id;
           $user = User::with(['roles'])->findOrFail($id);
           
           $this->inputs['name'] = $user->name;
           $this->inputs['email'] = $user->email;
           $this->inputs['is_active'] = $user->is_active;
           $this->inputs['role_id'] = $user->roles->first() ? $user->roles->first()->name : null;
           
           $permissions = DB::table('model_has_permissions')
               ->where('model_id', $id)
               ->where('model_type', User::class)
               ->pluck('permission_id')
               ->map(function($id) {
                   return (string) $id;
               })
               ->toArray();
    
           $this->inputs['permissions'] = $permissions;
           $this->inputs['password'] = '';
    
           if ($user->getFirstMedia('avatar')) {
               $this->avatar = null;
               $this->avatarUrl = $user->getFirstMediaUrl('avatar');
           }
       }
    }

    public function save($redirect = false, $resetForm = false)
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = collect($this->inputs)->except(['role_id', 'permissions'])->toArray();

            if (!empty($this->inputs['password'])) {
                $data['password'] = Hash::make($this->inputs['password']);
            } else {
                unset($data['password']);
            }

            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->update($data);
            } else {
                $user = User::create($data);
            }

            // Avatar işlemi
            if ($this->avatar) {
                $user->clearMediaCollection('avatar');
                $user->addMedia($this->avatar->getRealPath())
                    ->toMediaCollection('avatar');
            }

            // Rol atama
            if (!empty($this->inputs['role_id'])) {
                $role = Role::where('name', $this->inputs['role_id'])->first();
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            } else {
                $user->syncRoles([]);
            }

            // İzin atama
            if (!empty($this->inputs['permissions'])) {
                $permissions = Permission::whereIn('id', $this->inputs['permissions'])->get();
                $user->syncPermissions($permissions);
            } else {
                $user->syncPermissions([]);
            }

            DB::commit();

            $message = $this->userId ? 'Kullanıcı başarıyla güncellendi.' : 'Kullanıcı başarıyla oluşturuldu.';

            if ($redirect) {
                return redirect()->route('admin.user.index');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);

            if ($resetForm && !$this->userId) {
                $this->reset(['inputs', 'avatar', 'avatarUrl']);
                $this->inputs = [
                    'name' => '',
                    'email' => '',
                    'password' => '',
                    'is_active' => true,
                    'role_id' => null,
                    'permissions' => []
                ];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function toggleModulePermissions($module)
    {
        $modulePermissions = $this->allPermissions
            ->filter(function($permission) use ($module) {
                return explode('.', $permission->name)[0] === $module;
            })
            ->pluck('id')
            ->toArray();

        // Eğer tüm izinler seçili ise, hepsini kaldır
        if (empty(array_diff($modulePermissions, $this->inputs['permissions']))) {
            $this->inputs['permissions'] = array_values(array_diff($this->inputs['permissions'], $modulePermissions));
        }
        // Değilse, eksik olanları ekle
        else {
            $this->inputs['permissions'] = array_values(array_unique(array_merge($this->inputs['permissions'], $modulePermissions)));
        }
    }

    public function render()
    {
        $groupedPermissions = $this->allPermissions->groupBy(function($permission) {
            return explode('.', $permission->name)[0];
        });

        $permissionLabels = [
            'viewAny' => 'Listele',
            'view' => 'Görüntüle',
            'create' => 'Oluştur',
            'update' => 'Düzenle',
            'delete' => 'Sil',
            'publish' => 'Yayınla'
        ];

        $moduleLabels = [
            'page' => 'Sayfalar',
            'portfolio' => 'Portfolyo',
            'user' => 'Kullanıcılar',
            'role' => 'Roller',
            'permission' => 'İzinler'
        ];

        return view('usermanagement::livewire.user-manage-component', [
            'groupedPermissions' => $groupedPermissions,
            'permissionLabels' => $permissionLabels,
            'moduleLabels' => $moduleLabels,
            'model' => $this->userId ? User::find($this->userId) : null
        ]);
    }
}