<?php

namespace Modules\UserManagement\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Modules\UserManagement\App\Http\Livewire\Traits\WithBulkActions;

#[Layout('admin.layout')]
class ActivityLogComponent extends Component
{
    use WithPagination, WithBulkActions;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 25;

    #[Url]
    public $sortField = 'created_at';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $userFilter = '';

    #[Url]
    public $moduleFilter = '';

    #[Url]
    public $dateFrom = '';

    #[Url]
    public $dateTo = '';

    #[Url]
    public $eventFilter = '';

    protected $queryString = [
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 25],
        'userFilter' => ['except' => ''],
        'moduleFilter' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'eventFilter' => ['except' => ''],
    ];

    protected function getModelClass()
    {
        return Activity::class;
    }

    protected function getPrimaryKeyName() 
    {
        return 'id';
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUserFilter()
    {
        $this->resetPage();
    }

    public function updatedModuleFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedEventFilter()
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

    public function clearFilters()
    {
        $this->reset(['search', 'userFilter', 'moduleFilter', 'dateFrom', 'dateTo', 'eventFilter']);
    }

    public function clearLogs()
    {
        // Silme yetkisi kontrolü
        if (!Auth::user()->isRoot()) {
            $this->dispatch('toast', [
                'title' => 'Yetkisiz İşlem!',
                'message' => 'Bu işlem için yetkiniz bulunmuyor.',
                'type' => 'error',
            ]);
            return;
        }

        $this->dispatch('showConfirmModal', [
            'title' => 'Tüm Kayıtları Sil',
            'message' => 'Tüm aktivite kayıtlarını silmek istediğinize emin misiniz? Bu işlem geri alınamaz!',
            'method' => 'confirmClearLogs'
        ]);
    }

    public function clearUserLogs($userId)
    {
        // Silme yetkisi kontrolü
        if (!Auth::user()->isRoot()) {
            $this->dispatch('toast', [
                'title' => 'Yetkisiz İşlem!',
                'message' => 'Bu işlem için yetkiniz bulunmuyor.',
                'type' => 'error',
            ]);
            return;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Kullanıcı bulunamadı.',
                'type' => 'error',
            ]);
            return;
        }

        $this->dispatch('showConfirmModal', [
            'title' => 'Kullanıcı Kayıtlarını Sil',
            'message' => $user->name . ' kullanıcısına ait tüm aktivite kayıtlarını silmek istediğinize emin misiniz? Bu işlem geri alınamaz!',
            'method' => 'confirmClearUserLogs',
            'params' => ['userId' => $userId]
        ]);
    }

    public function confirmClearLogs()
    {
        try {
            DB::beginTransaction();
            
            Activity::query()->delete();
            
            DB::commit();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Tüm aktivite kayıtları silindi.',
                'type' => 'success',
            ]);
            
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function confirmClearUserLogs($params)
    {
        try {
            $userId = $params['userId'];
            
            DB::beginTransaction();
            
            $count = Activity::where('causer_type', User::class)
                ->where('causer_id', $userId)
                ->delete();
            
            DB::commit();
            
            $user = User::find($userId);
            $userName = $user ? $user->name : 'Belirtilen kullanıcı';
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $userName . ' kullanıcısına ait ' . $count . ' adet aktivite kaydı silindi.',
                'type' => 'success',
            ]);
            
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        $user = Auth::user();
        
        // Sadece root kullanıcısı tüm logları görebilir
        // Diğer kullanıcılar rootun loglarını göremez
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->when(!$user->isRoot(), function ($query) {
                $query->whereHas('causer', function ($q) {
                    $q->where('model_type', User::class)
                        ->whereDoesntHave('roles', function ($r) {
                            $r->where('name', 'root');
                        });
                })
                ->orWhereNull('causer_id');
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereJsonContains('properties->baslik', $this->search)
                        ->orWhereJsonContains('properties->modul', $this->search);
                });
            })
            ->when($this->userFilter, function ($query) {
                $query->where('causer_id', $this->userFilter)
                      ->where('causer_type', User::class);
            })
            ->when($this->moduleFilter, function ($query) {
                $query->whereJsonContains('properties->modul', $this->moduleFilter);
            })
            ->when($this->eventFilter, function ($query) {
                $query->where('event', $this->eventFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            });

        $logs = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $users = User::orderBy('name')
            ->when(!$user->isRoot(), function ($query) {
                $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'root');
                });
            })
            ->get();

        $modules = Activity::distinct()
            ->pluck('log_name')
            ->filter()
            ->sort()
            ->values();

        $events = Activity::distinct()
            ->pluck('event')
            ->filter()
            ->sort()
            ->values();

        return view('usermanagement::livewire.activity-log-component', [
            'logs' => $logs,
            'users' => $users,
            'modules' => $modules,
            'events' => $events,
            'isRoot' => $user->isRoot(),
        ]);
    }
}