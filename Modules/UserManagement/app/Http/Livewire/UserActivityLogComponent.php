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
class UserActivityLogComponent extends Component
{
    use WithPagination, WithBulkActions;

    public $userId;
    public $userName;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 25;

    #[Url]
    public $sortField = 'created_at';

    #[Url]
    public $sortDirection = 'desc';

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
    
    public function mount($id)
    {
        $this->userId = $id;
        
        $user = User::find($id);
        if (!$user) {
            session()->flash('error', 'Kullanıcı bulunamadı');
            return redirect()->route('admin.usermanagement.index');
        }
        
        $this->userName = $user->name;
        
        // Root kullanıcısının loglarını sadece root görebilir
        if ($user->isRoot() && !Auth::user()->isRoot()) {
            session()->flash('error', 'Bu kullanıcının işlem kayıtlarını görüntüleme yetkiniz bulunmuyor');
            return redirect()->route('admin.usermanagement.index');
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
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
        $this->reset(['search', 'moduleFilter', 'dateFrom', 'dateTo', 'eventFilter']);
    }

    public function clearUserLogs()
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
            'title' => 'Kullanıcı Kayıtlarını Sil',
            'message' => $this->userName . ' kullanıcısına ait tüm aktivite kayıtlarını silmek istediğinize emin misiniz? Bu işlem geri alınamaz!',
            'method' => 'confirmClearUserLogs'
        ]);
    }

    public function confirmClearUserLogs()
    {
        try {
            DB::beginTransaction();
            
            $count = Activity::where('causer_type', User::class)
                ->where('causer_id', $this->userId)
                ->delete();
            
            DB::commit();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => $this->userName . ' kullanıcısına ait ' . $count . ' adet aktivite kaydı silindi.',
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
        $query = Activity::query()
            ->with(['causer', 'subject'])
            ->where('causer_type', User::class)
            ->where('causer_id', $this->userId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                        ->orWhereJsonContains('properties->baslik', $this->search)
                        ->orWhereJsonContains('properties->modul', $this->search);
                });
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

        $modules = Activity::where('causer_type', User::class)
            ->where('causer_id', $this->userId)
            ->distinct()
            ->pluck('log_name')
            ->filter()
            ->sort()
            ->values();

        $events = Activity::where('causer_type', User::class)
            ->where('causer_id', $this->userId)
            ->distinct()
            ->pluck('event')
            ->filter()
            ->sort()
            ->values();

        return view('usermanagement::livewire.user-activity-log-component', [
            'logs' => $logs,
            'modules' => $modules,
            'events' => $events,
            'isRoot' => Auth::user()->isRoot(),
        ]);
    }
}