<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Modules\UserManagement\app\Http\Livewire\Traits\WithBulkActionsQueue;

#[Layout('admin.layout')]
class UserComponent extends Component
{
    use WithPagination, WithBulkActionsQueue;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 8;

    #[Url]
    public $sortField = 'id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $roleFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $subscriptionFilter = '';

    #[Url]
    public $viewType = 'grid';

    protected $queryString = [
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'subscriptionFilter' => ['except' => ''],
        'viewType' => ['except' => 'list'],
    ];

    protected function getListeners()
    {
        return array_merge([
            'refreshComponent' => '$refresh',
            'itemDeleted' => '$refresh',
            'bulkProgressUpdate' => 'refreshBulkProgress',
            'bulkJobCompleted' => 'onBulkJobCompleted',
            'closeBulkModal' => 'closeBulkModal'
        ]);
    }

    protected function getModelClass()
    {
        return User::class;
    }

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSubscriptionFilter()
    {
        $this->resetPage();
    }

    public function bulkToggleActive()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        $users = User::whereIn('id', $this->selectedItems)->get();

        foreach ($users as $user) {
            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'aktif' : 'pasif';
            log_activity($user, $status, [
                'status' => $status,
                'bulk_update' => true
            ]);
        }

        $this->selectedItems = [];

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Seçili kayıtların durumları güncellendi.',
            'type' => 'success',
        ]);
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

    public function toggleActive($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->is_active = !$user->is_active;
            $user->save();
            $status = $user->is_active ? 'aktif' : 'pasif';

            log_activity($user, $status, [
                'status' => $status
            ]);

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$user->name}\" {$status} yapıldı.",
                'type' => 'success',
            ]);
        }
    }

    public function toggleEmailVerification($id)
    {
        $user = User::find($id);

        if ($user) {
            // Root kullanıcıların email doğrulaması değiştirilemez
            if ($user->hasRole('root') && !auth()->user()->hasRole('root')) {
                $this->dispatch('toast', [
                    'title' => 'Hata!',
                    'message' => 'Root kullanıcıların email doğrulaması değiştirilemez.',
                    'type' => 'error',
                ]);
                return;
            }

            // Email doğrulama durumunu toggle yap
            if ($user->email_verified_at) {
                $user->email_verified_at = null;
                $action = 'Email doğrulaması kaldırıldı';
                $message = "\"{$user->name}\" kullanıcısının email doğrulaması kaldırıldı.";
            } else {
                $user->email_verified_at = now();
                $action = 'Email doğrulaması yapıldı';
                $message = "\"{$user->name}\" kullanıcısının email adresi doğrulandı.";
            }

            $user->save();

            log_activity($user, $action, [
                'email_verified_at' => $user->email_verified_at,
                'verified_by' => auth()->user()->name
            ]);

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $message,
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        $query = User::with('roles') // Rolleri eager loading ile yükle
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('users.name', 'like', '%' . $this->search . '%')
                            ->orWhere('users.email', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->roleFilter, function ($query) {
                    $query->whereHas('roles', function ($q) {
                        $q->where('id', $this->roleFilter);
                    });
                })
                ->when($this->statusFilter !== '', function ($query) {
                    $query->where('is_active', $this->statusFilter);
                })
                ->when($this->subscriptionFilter !== '', function ($query) {
                    if ($this->subscriptionFilter === 'active') {
                        // Aktif üyelik: süresi henüz dolmamış
                        $query->whereNotNull('subscription_expires_at')
                              ->where('subscription_expires_at', '>', now());
                    } elseif ($this->subscriptionFilter === 'expired') {
                        // Süresi dolmuş üyelik
                        $query->whereNotNull('subscription_expires_at')
                              ->where('subscription_expires_at', '<=', now());
                    } elseif ($this->subscriptionFilter === 'free') {
                        // Ücretsiz (hiç üyelik almamış)
                        $query->whereNull('subscription_expires_at');
                    }
                });

        $users = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $roles = Role::orderBy('name')->get();

        return view('usermanagement::livewire.user-component', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}