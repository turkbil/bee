<?php
namespace Modules\User\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class UserComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $perUser = 8;

    #[Url]
    public $sortField = 'id'; // Varsayılan sıralama alanı: id

    #[Url]
    public $sortDirection = 'desc'; // Varsayılan sıralama yönü: desc (son eklenen başa gelir)

    protected $listeners = ['itemDeleted' => 'refreshUsers'];

    public function updatedPerUser()
    {
        $this->resetPage(); // Sayfa adeti değiştiğinde sayfayı sıfırla
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Arama yapıldığında sayfayı sıfırla
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // Aynı alana tıklandığında sıralama yönünü tersine çevir
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Farklı bir alana tıklandığında sıralama alanını ve yönünü güncelle
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function refreshUsers()
    {
        // Silme işlemi sonrası kullanıcı listesini yenile
        $this->render();
    }

    public function render()
    {
        $tenant = tenancy()->tenant;
    
        $query = User::where('tenant_id', $tenant->id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
    
        // Sıralama işlemi
        $users = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perUser);
    
        return view('user::livewire.user-component', [
            'users' => $users,
        ])->layout('admin.layout');
    }
}